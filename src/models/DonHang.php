<?php
// ==============================================================================
// MODEL ĐƠN HÀNG: models/DonHang.php
// Quản lý Giỏ Hàng (chỉ cho người mua), Quy trình 3 bước đơn hàng, Thương lượng giá 1 cột
// Tự động trừ kho khi đơn sang 'da_xac_nhan' và gửi thông báo chuông
// ==============================================================================

class DonHang {
    private $db;
    private $coCotGiaBanDau = null;

    public function __construct($ketnoi) {
        $this->db = $ketnoi;
    }

    /**
     * Tự động kiểm tra xem bảng don_hang đã có cột gia_ban_dau chưa
     * Nếu chưa có, tự động thực thi lệnh ALTER TABLE an toàn để cập nhật CSDL
     */
    public function coCotGiaBanDau() {
        if ($this->coCotGiaBanDau !== null) {
            return $this->coCotGiaBanDau;
        }

        try {
            $check = mysqli_query($this->db, "SHOW COLUMNS FROM don_hang LIKE 'gia_ban_dau'");
            if ($check && mysqli_num_rows($check) > 0) {
                $this->coCotGiaBanDau = true;
                return true;
            }

            // Thử tự động thêm cột vào CSDL nếu MySQL cho phép
            $themCot = @mysqli_query($this->db, "ALTER TABLE don_hang ADD COLUMN gia_ban_dau DECIMAL(12, 2) NULL DEFAULT NULL AFTER gia");
            if ($themCot) {
                $this->coCotGiaBanDau = true;
                return true;
            }
        } catch (\Throwable $e) {
            // Tránh văng lỗi nếu MySQL chặn hoặc không có quyền ALTER
        }

        $this->coCotGiaBanDau = false;
        return false;
    }

    // ========================================================
    // 1. QUẢN LÝ GIỎ HÀNG (LƯU TRONG $_SESSION['gio_hang'])
    // CHỈ VAI TRÒ 'nguoi_mua' MỚI ĐƯỢC PHÉP THAO TÁC
    // ========================================================

    public function themGioHang($maSanPham, $soLuong = 1, $giaDeNghi = null) {
        if (!isset($_SESSION['gio_hang'])) {
            $_SESSION['gio_hang'] = [];
        }

        $soLuong = max(0.1, (float)$soLuong);

        if (isset($_SESSION['gio_hang'][$maSanPham])) {
            $_SESSION['gio_hang'][$maSanPham]['so_luong'] += $soLuong;
            if ($giaDeNghi !== null && $giaDeNghi > 0) {
                $_SESSION['gio_hang'][$maSanPham]['gia_de_nghi'] = (float)$giaDeNghi;
            }
        } else {
            $_SESSION['gio_hang'][$maSanPham] = [
                'so_luong'     => $soLuong,
                'gia_de_nghi'  => ($giaDeNghi !== null && $giaDeNghi > 0) ? (float)$giaDeNghi : null
            ];
        }
        return $this->demMonGioHang();
    }

    public function capNhatGioHang($maSanPham, $soLuong, $giaDeNghi = null) {
        if (isset($_SESSION['gio_hang'][$maSanPham])) {
            $soLuong = (float)$soLuong;
            if ($soLuong <= 0) {
                unset($_SESSION['gio_hang'][$maSanPham]);
            } else {
                $_SESSION['gio_hang'][$maSanPham]['so_luong'] = $soLuong;
                if ($giaDeNghi !== null) {
                    $_SESSION['gio_hang'][$maSanPham]['gia_de_nghi'] = ($giaDeNghi > 0) ? (float)$giaDeNghi : null;
                }
            }
        }
        return true;
    }

    public function xoaKhoiGioHang($maSanPham) {
        if (isset($_SESSION['gio_hang'][$maSanPham])) {
            unset($_SESSION['gio_hang'][$maSanPham]);
        }
        return true;
    }

    public function demMonGioHang() {
        return isset($_SESSION['gio_hang']) ? count($_SESSION['gio_hang']) : 0;
    }

    public function layChiTietGioHang($modelSanPham) {
        if (empty($_SESSION['gio_hang'])) return [];

        $danhSachGio = [];
        foreach ($_SESSION['gio_hang'] as $maSP => $thongTin) {
            $sp = $modelSanPham->chiTiet($maSP);
            if ($sp && $sp['an'] == 0) {
                $sp['so_luong_dat'] = $thongTin['so_luong'];
                // Nếu sản phẩm có giá niêm yết thì ưu tiên, nếu không thì lấy giá người mua đề xuất
                $sp['gia_ap_dung']  = ($thongTin['gia_de_nghi'] !== null && $thongTin['gia_de_nghi'] > 0) 
                                      ? $thongTin['gia_de_nghi'] 
                                      : $sp['gia'];
                $danhSachGio[] = $sp;
            } else {
                unset($_SESSION['gio_hang'][$maSP]);
            }
        }
        return $danhSachGio;
    }

    public function xoaSachGioHang() {
        unset($_SESSION['gio_hang']);
    }

    // ========================================================
    // 2. TẠO ĐƠN HÀNG VÀ THEO DÕI VÒNG ĐỜI ĐƠN
    // ========================================================

    /**
     * Tạo đơn hàng mới từ giỏ hàng
     */
    public function taoDonHang($maNguoiMua, $maSanPham, $soLuong, $donVi, $gia, $donViGia, $ghiChu = '', $giaBanDau = null) {
        if ($this->coCotGiaBanDau()) {
            if ($giaBanDau === null || (float)$giaBanDau <= 0) {
                $spGoc = mysqli_fetch_assoc(mysqli_query($this->db, "SELECT gia FROM san_pham WHERE ma_san_pham = " . (int)$maSanPham));
                $giaBanDau = (!empty($spGoc['gia']) && (float)$spGoc['gia'] > 0) ? (float)$spGoc['gia'] : (float)$gia;
            }
            $cauLenh = "INSERT INTO don_hang (ma_nguoi_mua, ma_san_pham, so_luong, don_vi, gia, gia_ban_dau, don_vi_gia, trang_thai, ghi_chu) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, 'moi', ?)";
            $stmt = mysqli_prepare($this->db, $cauLenh);
            mysqli_stmt_bind_param($stmt, "iidsddss", $maNguoiMua, $maSanPham, $soLuong, $donVi, $gia, $giaBanDau, $donViGia, $ghiChu);
        } else {
            $cauLenh = "INSERT INTO don_hang (ma_nguoi_mua, ma_san_pham, so_luong, don_vi, gia, don_vi_gia, trang_thai, ghi_chu) 
                        VALUES (?, ?, ?, ?, ?, ?, 'moi', ?)";
            $stmt = mysqli_prepare($this->db, $cauLenh);
            mysqli_stmt_bind_param($stmt, "iidsdss", $maNguoiMua, $maSanPham, $soLuong, $donVi, $gia, $donViGia, $ghiChu);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            $maDonHang = mysqli_insert_id($this->db);

            // Gửi thông báo đến Nông Hộ sở hữu sản phẩm
            $sp = mysqli_fetch_assoc(mysqli_query($this->db, "SELECT ma_nong_ho, ten FROM san_pham WHERE ma_san_pham = $maSanPham"));
            if ($sp) {
                $noiDung = "Bạn có yêu cầu đặt mua nông sản [" . $sp['ten'] . "] mới. Vui lòng kiểm tra và phản hồi.";
                $this->guiThongBao($sp['ma_nong_ho'], $noiDung);
            }

            return $maDonHang;
        }
        return false;
    }

    /**
     * Lấy danh sách đơn hàng phân theo Tab & Hỗ trợ Bộ lọc nâng cao (Dùng chung cho cả Người Mua & Nông Hộ)
     * $vaiTro: 'nguoi_mua' hoặc 'nong_ho'
     * $tab: 'dang_xu_ly', 'da_xac_nhan', 'lich_su'
     * $boLoc: mảng chứa các tiêu chí lọc ['tu_khoa', 'quy_mo', 'xu_ly', 'thoi_gian', 'sap_xep']
     */
    public function layDanhSachTheoTab($maNguoiDung, $vaiTro, $tab = 'dang_xu_ly', $boLoc = []) {
        $cauLenh = "SELECT dh.*, sp.ten AS ten_san_pham, sp.anh, sp.ma_nong_ho,
                    sp.gia AS gia_goc_sp, sp.don_vi_gia AS don_vi_gia_sp,
                    sp.san_luong AS san_luong_kho, sp.don_vi_san_luong,
                    nm.ho_ten AS ten_nguoi_mua, nm.sdt AS sdt_nguoi_mua, nm.dia_chi AS dia_chi_nguoi_mua, nm.tinh AS tinh_nguoi_mua,
                    nh.ho_ten AS ten_nong_ho, nh.sdt AS sdt_nong_ho, nh.tinh AS tinh_nong_ho, nh.dia_chi AS dia_chi_nong_ho
                    FROM don_hang dh
                    JOIN san_pham sp ON dh.ma_san_pham = sp.ma_san_pham
                    JOIN nguoi_dung nm ON dh.ma_nguoi_mua = nm.ma_nguoi_dung
                    JOIN nguoi_dung nh ON sp.ma_nong_ho = nh.ma_nguoi_dung
                    WHERE ";

        if ($vaiTro === 'nguoi_mua') {
            $cauLenh .= "dh.ma_nguoi_mua = ? ";
        } else {
            $cauLenh .= "sp.ma_nong_ho = ? ";
        }

        $params = [$maNguoiDung];
        $types  = "i";

        // 1. Phân theo Tab chính
        switch ($tab) {
            case 'da_xac_nhan':
                $cauLenh .= "AND dh.trang_thai = 'da_xac_nhan' ";
                break;
            case 'lich_su':
                $cauLenh .= "AND dh.trang_thai IN ('hoan_thanh', 'da_huy') ";
                break;
            case 'dang_xu_ly':
            default:
                $cauLenh .= "AND dh.trang_thai IN ('moi', 'da_bao_gia') ";
                break;
        }

        // 2. Tìm kiếm từ khóa (Mã đơn, Tên nông sản, Tên/SĐT đối tác)
        if (!empty($boLoc['tu_khoa'])) {
            $tuKhoa = trim($boLoc['tu_khoa']);
            $maSo = preg_replace('/[^0-9]/', '', $tuKhoa);
            if ($maSo !== '' && is_numeric($maSo)) {
                $cauLenh .= "AND (dh.ma_don_hang = ? OR sp.ten LIKE ? OR nm.ho_ten LIKE ? OR nh.ho_ten LIKE ? OR nm.sdt LIKE ? OR nh.sdt LIKE ?) ";
                $like = "%$tuKhoa%";
                $params[] = (int)$maSo;
                $params[] = $like; $params[] = $like; $params[] = $like; $params[] = $like; $params[] = $like;
                $types   .= "isssss";
            } else {
                $cauLenh .= "AND (sp.ten LIKE ? OR nm.ho_ten LIKE ? OR nh.ho_ten LIKE ? OR nm.sdt LIKE ? OR nh.sdt LIKE ?) ";
                $like = "%$tuKhoa%";
                $params[] = $like; $params[] = $like; $params[] = $like; $params[] = $like; $params[] = $like;
                $types   .= "sssss";
            }
        }

        // 3. Lọc theo Quy mô đơn (Đơn sỉ từ 500 kg trở lên vs Đơn mua lẻ dưới 500 kg)
        if (!empty($boLoc['quy_mo'])) {
            if ($boLoc['quy_mo'] === 'si') {
                $cauLenh .= "AND (
                    (CASE 
                        WHEN dh.don_vi IN ('tấn', 'tan') THEN dh.so_luong * 1000
                        WHEN dh.don_vi IN ('tạ', 'ta') THEN dh.so_luong * 100
                        WHEN dh.don_vi IN ('yến', 'yen') THEN dh.so_luong * 10
                        ELSE dh.so_luong 
                    END) >= 500
                ) ";
            } elseif ($boLoc['quy_mo'] === 'thuong') {
                $cauLenh .= "AND (
                    (CASE 
                        WHEN dh.don_vi IN ('tấn', 'tan') THEN dh.so_luong * 1000
                        WHEN dh.don_vi IN ('tạ', 'ta') THEN dh.so_luong * 100
                        WHEN dh.don_vi IN ('yến', 'yen') THEN dh.so_luong * 10
                        ELSE dh.so_luong 
                    END) < 500
                ) ";
            }
        }

        // 4. Lọc theo Tình trạng xử lý
        if (!empty($boLoc['xu_ly'])) {
            if ($boLoc['xu_ly'] === 'can_xu_ly') {
                if ($vaiTro === 'nong_ho') {
                    $cauLenh .= "AND dh.trang_thai = 'moi' ";
                } else {
                    $cauLenh .= "AND dh.trang_thai = 'da_bao_gia' ";
                }
            } elseif ($boLoc['xu_ly'] === 'cho_doi_tac') {
                if ($vaiTro === 'nong_ho') {
                    $cauLenh .= "AND dh.trang_thai = 'da_bao_gia' ";
                } else {
                    $cauLenh .= "AND dh.trang_thai = 'moi' ";
                }
            }
        }

        // 5. Lọc theo Thời gian
        if (!empty($boLoc['thoi_gian'])) {
            if ($boLoc['thoi_gian'] === 'hom_nay') {
                $cauLenh .= "AND DATE(dh.ngay_tao) = CURDATE() ";
            } elseif ($boLoc['thoi_gian'] === '7_ngay') {
                $cauLenh .= "AND dh.ngay_tao >= DATE_SUB(NOW(), INTERVAL 7 DAY) ";
            } elseif ($boLoc['thoi_gian'] === 'thang_nay') {
                $cauLenh .= "AND MONTH(dh.ngay_tao) = MONTH(CURDATE()) AND YEAR(dh.ngay_tao) = YEAR(CURDATE()) ";
            }
        }

        // 6. Sắp xếp thứ tự
        $sapXep = $boLoc['sap_xep'] ?? 'moi_nhat';
        switch ($sapXep) {
            case 'cu_nhat':
                $cauLenh .= "ORDER BY dh.ma_don_hang ASC";
                break;
            case 'gia_cao':
                $cauLenh .= "ORDER BY (dh.gia * dh.so_luong) DESC, dh.ma_don_hang DESC";
                break;
            case 'so_luong_lon':
                $cauLenh .= "ORDER BY dh.so_luong DESC, dh.ma_don_hang DESC";
                break;
            case 'moi_nhat':
            default:
                $cauLenh .= "ORDER BY dh.ma_don_hang DESC";
                break;
        }

        $stmt = mysqli_prepare($this->db, $cauLenh);
        if (!empty($types)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $ketQua = mysqli_stmt_get_result($stmt);

        $danhSach = [];
        while ($dong = mysqli_fetch_assoc($ketQua)) {
            $danhSach[] = $dong;
        }
        return $danhSach;
    }

    /**
     * Đếm số lượng đơn hàng theo từng tab phục vụ hiển thị badge số lượng
     */
    public function demTheoCacTab($maNguoiDung, $vaiTro) {
        $dem = [
            'dang_xu_ly'  => 0,
            'da_xac_nhan' => 0,
            'lich_su'     => 0,
            'moi'         => 0
        ];
        
        $cauLenh = "SELECT dh.trang_thai, COUNT(*) AS tong
                    FROM don_hang dh
                    JOIN san_pham sp ON dh.ma_san_pham = sp.ma_san_pham
                    WHERE ";
        if ($vaiTro === 'nguoi_mua') {
            $cauLenh .= "dh.ma_nguoi_mua = ? ";
        } else {
            $cauLenh .= "sp.ma_nong_ho = ? ";
        }
        $cauLenh .= "GROUP BY dh.trang_thai";

        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "i", $maNguoiDung);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($res)) {
            $st = $row['trang_thai'];
            $sl = (int)$row['tong'];
            if ($st === 'moi') {
                $dem['dang_xu_ly'] += $sl;
                $dem['moi'] += $sl;
            } elseif ($st === 'da_bao_gia') {
                $dem['dang_xu_ly'] += $sl;
                if ($vaiTro === 'nguoi_mua') {
                    $dem['moi'] += $sl;
                }
            } elseif ($st === 'da_xac_nhan') {
                $dem['da_xac_nhan'] += $sl;
            } elseif (in_array($st, ['hoan_thanh', 'da_huy'])) {
                $dem['lich_su'] += $sl;
            }
        }
        return $dem;
    }

    /**
     * Chi tiết 1 đơn hàng (kèm giá gốc sản phẩm để đối chiếu giảm giá sỉ)
     */
    public function chiTiet($maDonHang) {
        $cauLenh = "SELECT dh.*, sp.ten AS ten_san_pham, sp.anh, sp.ma_nong_ho,
                    sp.gia AS gia_goc_sp, sp.don_vi_gia AS don_vi_gia_sp,
                    sp.san_luong AS san_luong_kho, sp.don_vi_san_luong,
                    nm.ho_ten AS ten_nguoi_mua, nm.sdt AS sdt_nguoi_mua, nm.dia_chi AS dia_chi_nguoi_mua, nm.tinh AS tinh_nguoi_mua,
                    nh.ho_ten AS ten_nong_ho, nh.sdt AS sdt_nong_ho, nh.tinh AS tinh_nong_ho, nh.dia_chi AS dia_chi_nong_ho
                    FROM don_hang dh
                    JOIN san_pham sp ON dh.ma_san_pham = sp.ma_san_pham
                    JOIN nguoi_dung nm ON dh.ma_nguoi_mua = nm.ma_nguoi_dung
                    JOIN nguoi_dung nh ON sp.ma_nong_ho = nh.ma_nguoi_dung
                    WHERE dh.ma_don_hang = ? LIMIT 1";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "i", $maDonHang);
        mysqli_stmt_execute($stmt);
        return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    }

    /**
     * Nông Hộ báo giá mới / chiết khấu sỉ (chuyển sang 'da_bao_gia')
     */
    public function nongHoBaoGia($maDonHang, $maNongHo, $giaMoi, $ghiChu = null, $giaBanDau = null) {
        $don = $this->chiTiet($maDonHang);
        if (!$don || $don['ma_nong_ho'] != $maNongHo || !in_array($don['trang_thai'], ['moi', 'da_bao_gia'])) {
            return false;
        }

        $coGiaBanDau = $this->coCotGiaBanDau();
        $giaBanDauCapNhat = !empty($don['gia_ban_dau']) && (float)$don['gia_ban_dau'] > 0 
            ? (float)$don['gia_ban_dau'] 
            : (($giaBanDau !== null && (float)$giaBanDau > 0) ? (float)$giaBanDau : (float)$don['gia']);

        if ($coGiaBanDau) {
            if ($ghiChu !== null && trim($ghiChu) !== '') {
                $cauLenh = "UPDATE don_hang SET gia = ?, gia_ban_dau = ?, ghi_chu = ?, trang_thai = 'da_bao_gia' WHERE ma_don_hang = ?";
                $stmt = mysqli_prepare($this->db, $cauLenh);
                mysqli_stmt_bind_param($stmt, "ddsi", $giaMoi, $giaBanDauCapNhat, $ghiChu, $maDonHang);
            } else {
                $cauLenh = "UPDATE don_hang SET gia = ?, gia_ban_dau = ?, trang_thai = 'da_bao_gia' WHERE ma_don_hang = ?";
                $stmt = mysqli_prepare($this->db, $cauLenh);
                mysqli_stmt_bind_param($stmt, "ddi", $giaMoi, $giaBanDauCapNhat, $maDonHang);
            }
        } else {
            if ($ghiChu !== null && trim($ghiChu) !== '') {
                $cauLenh = "UPDATE don_hang SET gia = ?, ghi_chu = ?, trang_thai = 'da_bao_gia' WHERE ma_don_hang = ?";
                $stmt = mysqli_prepare($this->db, $cauLenh);
                mysqli_stmt_bind_param($stmt, "dsi", $giaMoi, $ghiChu, $maDonHang);
            } else {
                $cauLenh = "UPDATE don_hang SET gia = ?, trang_thai = 'da_bao_gia' WHERE ma_don_hang = ?";
                $stmt = mysqli_prepare($this->db, $cauLenh);
                mysqli_stmt_bind_param($stmt, "di", $giaMoi, $maDonHang);
            }
        }
        
        if (mysqli_stmt_execute($stmt)) {
            $maHienThi = ma_hien_thi('don_hang', $maDonHang);
            $thongBao = "Nông hộ [" . $don['ten_nong_ho'] . "] đã gửi mức báo giá mới (" . dinh_dang_tien($giaMoi) . " đ/" . $don['don_vi_gia'] . ") cho đơn hàng #" . $maHienThi . ".";
            if (!empty($ghiChu)) {
                $thongBao .= " Kèm lưu ý: " . $ghiChu;
            }
            $this->guiThongBao($don['ma_nguoi_mua'], $thongBao);
            return true;
        }
        return false;
    }

    /**
     * Chấp nhận đơn / Xác nhận đồng thuận (Chuyển sang 'da_xac_nhan' và TỰ ĐỘNG TRỪ KHO)
     * Hỗ trợ Nông Hộ chỉnh giá chốt & giảm giá sỉ động trực tiếp khi chốt đơn ($giaChot, $ghiChu)
     */
    public function xacNhanDonHang($maDonHang, $maNguoiThaoTac, $vaiTro, $modelSanPham, $giaChot = null, $ghiChu = null, $giaBanDau = null) {
        $don = $this->chiTiet($maDonHang);
        if (!$don) return false;

        // Kiểm tra quyền: Nếu ở 'moi' thì Nông hộ xác nhận; Nếu ở 'da_bao_gia' thì Người mua chấp nhận giá
        if ($vaiTro === 'nong_ho' && $don['ma_nong_ho'] != $maNguoiThaoTac) return false;
        if ($vaiTro === 'nguoi_mua' && $don['ma_nguoi_mua'] != $maNguoiThaoTac) return false;

        $coGiaBanDau = $this->coCotGiaBanDau();
        $giaBanDauCapNhat = !empty($don['gia_ban_dau']) && (float)$don['gia_ban_dau'] > 0 
            ? (float)$don['gia_ban_dau'] 
            : (($giaBanDau !== null && (float)$giaBanDau > 0) ? (float)$giaBanDau : (float)$don['gia']);

        // Nếu Nông Hộ chủ động chỉnh giá chốt riêng cho đơn khi bấm Chốt Đơn
        if ($vaiTro === 'nong_ho' && $giaChot !== null && (float)$giaChot > 0) {
            $giaMoi = (float)$giaChot;
            if ($coGiaBanDau) {
                if ($ghiChu !== null && trim($ghiChu) !== '') {
                    $cauLenh = "UPDATE don_hang SET gia = ?, gia_ban_dau = ?, ghi_chu = ?, trang_thai = 'da_xac_nhan' WHERE ma_don_hang = ?";
                    $stmt = mysqli_prepare($this->db, $cauLenh);
                    mysqli_stmt_bind_param($stmt, "ddsi", $giaMoi, $giaBanDauCapNhat, $ghiChu, $maDonHang);
                } else {
                    $cauLenh = "UPDATE don_hang SET gia = ?, gia_ban_dau = ?, trang_thai = 'da_xac_nhan' WHERE ma_don_hang = ?";
                    $stmt = mysqli_prepare($this->db, $cauLenh);
                    mysqli_stmt_bind_param($stmt, "ddi", $giaMoi, $giaBanDauCapNhat, $maDonHang);
                }
            } else {
                if ($ghiChu !== null && trim($ghiChu) !== '') {
                    $cauLenh = "UPDATE don_hang SET gia = ?, ghi_chu = ?, trang_thai = 'da_xac_nhan' WHERE ma_don_hang = ?";
                    $stmt = mysqli_prepare($this->db, $cauLenh);
                    mysqli_stmt_bind_param($stmt, "dsi", $giaMoi, $ghiChu, $maDonHang);
                } else {
                    $cauLenh = "UPDATE don_hang SET gia = ?, trang_thai = 'da_xac_nhan' WHERE ma_don_hang = ?";
                    $stmt = mysqli_prepare($this->db, $cauLenh);
                    mysqli_stmt_bind_param($stmt, "di", $giaMoi, $maDonHang);
                }
            }
        } else {
            $cauLenh = "UPDATE don_hang SET trang_thai = 'da_xac_nhan' WHERE ma_don_hang = ?";
            $stmt = mysqli_prepare($this->db, $cauLenh);
            mysqli_stmt_bind_param($stmt, "i", $maDonHang);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            // TỰ ĐỘNG TRỪ SẢN LƯỢNG KHO TẠI BƯỚC NÀY (QUY ĐỔI CHUẨN ĐƠN VỊ KHO THEO PHƯƠNG ÁN 3)
            $sp = $modelSanPham->chiTiet($don['ma_san_pham']);
            if ($sp) {
                $soLuongTruKho = quy_doi_don_vi($don['so_luong'], $don['don_vi'], $sp['don_vi_san_luong']);
                $modelSanPham->truKho($don['ma_san_pham'], $soLuongTruKho);
            }

            // Gửi thông báo cho đối tác
            $nguoiNhan = ($vaiTro === 'nong_ho') ? $don['ma_nguoi_mua'] : $don['ma_nong_ho'];
            $thongDiep = "Đơn hàng nông sản [" . $don['ten_san_pham'] . "] đã được hai bên đồng thuận xác nhận chốt đơn.";
            if ($vaiTro === 'nong_ho' && $giaChot !== null && (float)$giaChot > 0 && (float)$giaChot != (float)$don['gia']) {
                $thongDiep = "Nông hộ [" . $don['ten_nong_ho'] . "] đã chốt đơn hàng [" . $don['ten_san_pham'] . "] với mức giá ưu đãi " . dinh_dang_tien($giaChot) . " đ/" . $don['don_vi_gia'] . ".";
            }
            $this->guiThongBao($nguoiNhan, $thongDiep);
            return true;
        }
        return false;
    }

    /**
     * Hoàn thành đơn hàng (Chỉ Nông hộ bấm khi giao hàng thành công)
     */
    public function hoanThanhDon($maDonHang, $maNongHo) {
        $don = $this->chiTiet($maDonHang);
        if (!$don || $don['ma_nong_ho'] != $maNongHo || $don['trang_thai'] !== 'da_xac_nhan') {
            return false;
        }

        $cauLenh = "UPDATE don_hang SET trang_thai = 'hoan_thanh' WHERE ma_don_hang = ?";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "i", $maDonHang);
        if (mysqli_stmt_execute($stmt)) {
            $this->guiThongBao($don['ma_nguoi_mua'], "Đơn hàng nông sản [" . $don['ten_san_pham'] . "] đã giao dịch hoàn thành thành công.");
            return true;
        }
        return false;
    }

    /**
     * Hủy đơn hàng
     */
    public function huyDonHang($maDonHang, $maNguoiThaoTac, $vaiTro) {
        $don = $this->chiTiet($maDonHang);
        if (!$don) return false;

        // Đơn đã chốt (da_xac_nhan) không cho phép bên nào tự ý hủy bỏ
        if ($don['trang_thai'] === 'da_xac_nhan') {
            return false;
        }

        // Đơn đã hoàn thành hoặc đã hủy cũng không được thao tác lại
        if (in_array($don['trang_thai'], ['hoan_thanh', 'da_huy'])) {
            return false;
        }

        $cauLenh = "UPDATE don_hang SET trang_thai = 'da_huy' WHERE ma_don_hang = ?";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "i", $maDonHang);
        if (mysqli_stmt_execute($stmt)) {
            $nguoiNhan = ($vaiTro === 'nong_ho') ? $don['ma_nguoi_mua'] : $don['ma_nong_ho'];
            $this->guiThongBao($nguoiNhan, "Đơn hàng nông sản [" . $don['ten_san_pham'] . "] đã bị hủy.");
            return true;
        }
        return false;
    }

    // ========================================================
    // 3. THÔNG BÁO HỆ THỐNG
    // ========================================================

    public function guiThongBao($maNguoiNhan, $noiDung) {
        $cauLenh = "INSERT INTO thong_bao (ma_nguoi_nhan, noi_dung, da_doc) VALUES (?, ?, 0)";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "is", $maNguoiNhan, $noiDung);
        return mysqli_stmt_execute($stmt);
    }

    public function layThongBao($maNguoiDung, $gioiHan = 10) {
        $cauLenh = "SELECT * FROM thong_bao WHERE ma_nguoi_nhan = ? ORDER BY ma_thong_bao DESC LIMIT ?";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "ii", $maNguoiDung, $gioiHan);
        mysqli_stmt_execute($stmt);
        $ketQua = mysqli_stmt_get_result($stmt);
        $danhSach = [];
        while ($dong = mysqli_fetch_assoc($ketQua)) {
            $danhSach[] = $dong;
        }
        return $danhSach;
    }

    public function demThongBaoChuaDoc($maNguoiDung) {
        $cauLenh = "SELECT COUNT(*) as dem FROM thong_bao WHERE ma_nguoi_nhan = ? AND da_doc = 0";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "i", $maNguoiDung);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        return $row['dem'] ?? 0;
    }

    public function danhDauDaDoc($maNguoiDung) {
        $cauLenh = "UPDATE thong_bao SET da_doc = 1 WHERE ma_nguoi_nhan = ?";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "i", $maNguoiDung);
        return mysqli_stmt_execute($stmt);
    }

    // ========================================================
    // 3. THỐNG KÊ DASHBOARD CHO NÔNG HỘ & NGƯỜI MUA
    // ========================================================

    /**
     * Thống kê chi tiết dành cho Nông Hộ
     * @param int $maNongHo
     * @param string $thoiGian: 'hom_nay', '7_ngay', 'thang_nay', 'nam_nay', 'tat_ca'
     */
    public function layThongKeNongHo($maNongHo, $thoiGian = 'thang_nay') {
        $dieuKienNgay = "";
        switch ($thoiGian) {
            case 'hom_nay':
                $dieuKienNgay = " AND DATE(dh.ngay_tao) = CURDATE() ";
                break;
            case '7_ngay':
                $dieuKienNgay = " AND dh.ngay_tao >= DATE_SUB(NOW(), INTERVAL 7 DAY) ";
                break;
            case 'nam_nay':
                $dieuKienNgay = " AND YEAR(dh.ngay_tao) = YEAR(CURDATE()) ";
                break;
            case 'tat_ca':
                $dieuKienNgay = "";
                break;
            case 'thang_nay':
            default:
                $dieuKienNgay = " AND MONTH(dh.ngay_tao) = MONTH(CURDATE()) AND YEAR(dh.ngay_tao) = YEAR(CURDATE()) ";
                $thoiGian = 'thang_nay';
                break;
        }

        // 1. Chỉ số tổng quan KPI
        $sqlKPI = "SELECT 
            COUNT(CASE WHEN dh.trang_thai IN ('da_xac_nhan', 'hoan_thanh') THEN 1 END) AS so_don_thanh_cong,
            COUNT(CASE WHEN dh.trang_thai IN ('moi', 'da_bao_gia') THEN 1 END) AS so_don_dang_xu_ly,
            COUNT(CASE WHEN dh.trang_thai = 'da_huy' THEN 1 END) AS so_don_da_huy,
            COUNT(*) AS tong_so_don,
            COALESCE(SUM(CASE WHEN dh.trang_thai IN ('da_xac_nhan', 'hoan_thanh') THEN (dh.gia * dh.so_luong) ELSE 0 END), 0) AS tong_doanh_thu,
            COALESCE(SUM(CASE WHEN dh.trang_thai IN ('da_xac_nhan', 'hoan_thanh') THEN (
                CASE 
                    WHEN dh.don_vi IN ('tấn', 'tan') THEN dh.so_luong * 1000
                    WHEN dh.don_vi IN ('tạ', 'ta') THEN dh.so_luong * 100
                    WHEN dh.don_vi IN ('yến', 'yen') THEN dh.so_luong * 10
                    ELSE dh.so_luong 
                END
            ) ELSE 0 END), 0) AS tong_san_luong_kg,
            COUNT(CASE WHEN dh.trang_thai IN ('da_xac_nhan', 'hoan_thanh') AND (
                CASE 
                    WHEN dh.don_vi IN ('tấn', 'tan') THEN dh.so_luong * 1000
                    WHEN dh.don_vi IN ('tạ', 'ta') THEN dh.so_luong * 100
                    WHEN dh.don_vi IN ('yến', 'yen') THEN dh.so_luong * 10
                    ELSE dh.so_luong 
                END
            ) >= 500 THEN 1 END) AS so_don_si,
            COUNT(CASE WHEN dh.trang_thai IN ('da_xac_nhan', 'hoan_thanh') AND (
                CASE 
                    WHEN dh.don_vi IN ('tấn', 'tan') THEN dh.so_luong * 1000
                    WHEN dh.don_vi IN ('tạ', 'ta') THEN dh.so_luong * 100
                    WHEN dh.don_vi IN ('yến', 'yen') THEN dh.so_luong * 10
                    ELSE dh.so_luong 
                END
            ) < 500 THEN 1 END) AS so_don_le
            FROM don_hang dh
            JOIN san_pham sp ON dh.ma_san_pham = sp.ma_san_pham
            WHERE sp.ma_nong_ho = ? $dieuKienNgay";

        $stmt = mysqli_prepare($this->db, $sqlKPI);
        mysqli_stmt_bind_param($stmt, "i", $maNongHo);
        mysqli_stmt_execute($stmt);
        $kpi = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?? [];

        // 2. Top nông sản bán chạy nhất
        $sqlTop = "SELECT sp.ma_san_pham, sp.ten, sp.anh, sp.don_vi_gia,
            COALESCE(SUM(dh.so_luong), 0) AS tong_so_luong,
            COALESCE(SUM(dh.gia * dh.so_luong), 0) AS tong_tien,
            COUNT(dh.ma_don_hang) AS so_don
            FROM don_hang dh
            JOIN san_pham sp ON dh.ma_san_pham = sp.ma_san_pham
            WHERE sp.ma_nong_ho = ? AND dh.trang_thai IN ('da_xac_nhan', 'hoan_thanh') $dieuKienNgay
            GROUP BY sp.ma_san_pham, sp.ten, sp.anh, sp.don_vi_gia
            ORDER BY tong_tien DESC
            LIMIT 5";
        $stmtTop = mysqli_prepare($this->db, $sqlTop);
        mysqli_stmt_bind_param($stmtTop, "i", $maNongHo);
        mysqli_stmt_execute($stmtTop);
        $resTop = mysqli_stmt_get_result($stmtTop);
        $topSanPham = [];
        while ($row = mysqli_fetch_assoc($resTop)) {
            $topSanPham[] = $row;
        }

        // 3. Sản lượng tồn kho hiện tại của vườn
        $sqlKho = "SELECT 
            COUNT(*) AS so_loai_sp,
            COALESCE(SUM(CASE 
                WHEN don_vi_san_luong IN ('tấn', 'tan') THEN san_luong * 1000
                WHEN don_vi_san_luong IN ('tạ', 'ta') THEN san_luong * 100
                WHEN don_vi_san_luong IN ('yến', 'yen') THEN san_luong * 10
                ELSE san_luong 
            END), 0) AS tong_kho_kg,
            COUNT(CASE WHEN tinh_trang = 'sap_thu_hoach' THEN 1 END) AS so_sp_sap_thu_hoach
            FROM san_pham WHERE ma_nong_ho = ? AND an = 0";
        $stmtKho = mysqli_prepare($this->db, $sqlKho);
        mysqli_stmt_bind_param($stmtKho, "i", $maNongHo);
        mysqli_stmt_execute($stmtKho);
        $khoVien = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtKho)) ?? [];

        // 4. Dữ liệu Biểu đồ Tăng trưởng Doanh thu
        $bieuDo = $this->taoDuLieuBieuDo($maNongHo, 'nong_ho', $thoiGian);

        return [
            'thoi_gian'       => $thoiGian,
            'kpi'             => $kpi,
            'top_san_pham'    => $topSanPham,
            'kho'             => $khoVien,
            'bieu_do'         => $bieuDo
        ];
    }

    /**
     * Thống kê chi tiết dành cho Người Mua / Thương Lái
     * @param int $maNguoiMua
     * @param string $thoiGian: 'hom_nay', '7_ngay', 'thang_nay', 'nam_nay', 'tat_ca'
     */
    public function layThongKeNguoiMua($maNguoiMua, $thoiGian = 'thang_nay') {
        $dieuKienNgay = "";
        switch ($thoiGian) {
            case 'hom_nay':
                $dieuKienNgay = " AND DATE(dh.ngay_tao) = CURDATE() ";
                break;
            case '7_ngay':
                $dieuKienNgay = " AND dh.ngay_tao >= DATE_SUB(NOW(), INTERVAL 7 DAY) ";
                break;
            case 'nam_nay':
                $dieuKienNgay = " AND YEAR(dh.ngay_tao) = YEAR(CURDATE()) ";
                break;
            case 'tat_ca':
                $dieuKienNgay = "";
                break;
            case 'thang_nay':
            default:
                $dieuKienNgay = " AND MONTH(dh.ngay_tao) = MONTH(CURDATE()) AND YEAR(dh.ngay_tao) = YEAR(CURDATE()) ";
                $thoiGian = 'thang_nay';
                break;
        }

        // 1. Chỉ số KPI
        $coGiaBanDau = $this->coCotGiaBanDau();
        $selectTietKiem = $coGiaBanDau
            ? "COALESCE(SUM(CASE WHEN dh.trang_thai IN ('da_xac_nhan', 'hoan_thanh') AND dh.gia_ban_dau > dh.gia THEN ((dh.gia_ban_dau - dh.gia) * dh.so_luong) ELSE 0 END), 0) AS tong_tiet_kiem,"
            : "COALESCE(SUM(CASE WHEN dh.trang_thai IN ('da_xac_nhan', 'hoan_thanh') AND sp.gia > dh.gia THEN ((sp.gia - dh.gia) * dh.so_luong) ELSE 0 END), 0) AS tong_tiet_kiem,";

        $fromJoin = $coGiaBanDau
            ? "FROM don_hang dh"
            : "FROM don_hang dh LEFT JOIN san_pham sp ON dh.ma_san_pham = sp.ma_san_pham";

        $sqlKPI = "SELECT 
            COUNT(CASE WHEN dh.trang_thai IN ('da_xac_nhan', 'hoan_thanh') THEN 1 END) AS so_don_thanh_cong,
            COUNT(CASE WHEN dh.trang_thai IN ('moi', 'da_bao_gia') THEN 1 END) AS so_don_dang_cho,
            COUNT(CASE WHEN dh.trang_thai = 'da_huy' THEN 1 END) AS so_don_da_huy,
            COUNT(*) AS tong_so_don,
            COALESCE(SUM(CASE WHEN dh.trang_thai IN ('da_xac_nhan', 'hoan_thanh') THEN (dh.gia * dh.so_luong) ELSE 0 END), 0) AS tong_chi_tieu,
            $selectTietKiem
            COALESCE(SUM(CASE WHEN dh.trang_thai IN ('da_xac_nhan', 'hoan_thanh') THEN (
                CASE 
                    WHEN dh.don_vi IN ('tấn', 'tan') THEN dh.so_luong * 1000
                    WHEN dh.don_vi IN ('tạ', 'ta') THEN dh.so_luong * 100
                    WHEN dh.don_vi IN ('yến', 'yen') THEN dh.so_luong * 10
                    ELSE dh.so_luong 
                END
            ) ELSE 0 END), 0) AS tong_khoi_luong_kg
            $fromJoin
            WHERE dh.ma_nguoi_mua = ? $dieuKienNgay";

        $stmt = mysqli_prepare($this->db, $sqlKPI);
        mysqli_stmt_bind_param($stmt, "i", $maNguoiMua);
        mysqli_stmt_execute($stmt);
        $kpi = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?? [];

        // 2. Top nông sản thu mua nhiều nhất
        $sqlTop = "SELECT sp.ma_san_pham, sp.ten, sp.anh, sp.don_vi_gia,
            COALESCE(SUM(dh.so_luong), 0) AS tong_so_luong,
            COALESCE(SUM(dh.gia * dh.so_luong), 0) AS tong_tien,
            COUNT(dh.ma_don_hang) AS so_don
            FROM don_hang dh
            JOIN san_pham sp ON dh.ma_san_pham = sp.ma_san_pham
            WHERE dh.ma_nguoi_mua = ? AND dh.trang_thai IN ('da_xac_nhan', 'hoan_thanh') $dieuKienNgay
            GROUP BY sp.ma_san_pham, sp.ten, sp.anh, sp.don_vi_gia
            ORDER BY tong_tien DESC
            LIMIT 5";
        $stmtTop = mysqli_prepare($this->db, $sqlTop);
        mysqli_stmt_bind_param($stmtTop, "i", $maNguoiMua);
        mysqli_stmt_execute($stmtTop);
        $resTop = mysqli_stmt_get_result($stmtTop);
        $topSanPham = [];
        while ($row = mysqli_fetch_assoc($resTop)) {
            $topSanPham[] = $row;
        }

        // 3. Top nhà vườn đối tác thân thiết
        $sqlDoiTac = "SELECT nh.ma_nguoi_dung, nh.ho_ten, nh.sdt, nh.tinh,
            COUNT(dh.ma_don_hang) AS so_don,
            COALESCE(SUM(dh.gia * dh.so_luong), 0) AS tong_tien
            FROM don_hang dh
            JOIN san_pham sp ON dh.ma_san_pham = sp.ma_san_pham
            JOIN nguoi_dung nh ON sp.ma_nong_ho = nh.ma_nguoi_dung
            WHERE dh.ma_nguoi_mua = ? AND dh.trang_thai IN ('da_xac_nhan', 'hoan_thanh') $dieuKienNgay
            GROUP BY nh.ma_nguoi_dung, nh.ho_ten, nh.sdt, nh.tinh
            ORDER BY tong_tien DESC
            LIMIT 5";
        $stmtDT = mysqli_prepare($this->db, $sqlDoiTac);
        mysqli_stmt_bind_param($stmtDT, "i", $maNguoiMua);
        mysqli_stmt_execute($stmtDT);
        $resDT = mysqli_stmt_get_result($stmtDT);
        $topDoiTac = [];
        while ($row = mysqli_fetch_assoc($resDT)) {
            $topDoiTac[] = $row;
        }

        // 4. Dữ liệu Biểu đồ Chi tiêu
        $bieuDo = $this->taoDuLieuBieuDo($maNguoiMua, 'nguoi_mua', $thoiGian);

        return [
            'thoi_gian'       => $thoiGian,
            'kpi'             => $kpi,
            'top_san_pham'    => $topSanPham,
            'top_doi_tac'     => $topDoiTac,
            'bieu_do'         => $bieuDo
        ];
    }

    /**
     * Tạo dữ liệu vẽ biểu đồ Chart.js theo thời gian
     */
    private function taoDuLieuBieuDo($maNguoiDung, $vaiTro, $thoiGian) {
        $labels = [];
        $values = [];
        $counts = [];

        if ($thoiGian === 'hom_nay') {
            // Theo 4 khung giờ trong ngày
            $khungGio = [
                '00-06h' => ['00:00:00', '05:59:59'],
                '06-12h' => ['06:00:00', '11:59:59'],
                '12-18h' => ['12:00:00', '17:59:59'],
                '18-24h' => ['18:00:00', '23:59:59']
            ];
            foreach ($khungGio as $nhan => $gio) {
                $labels[] = $nhan;
                $sql = "SELECT COALESCE(SUM(dh.gia * dh.so_luong), 0) AS tien, COUNT(*) AS dem
                        FROM don_hang dh
                        JOIN san_pham sp ON dh.ma_san_pham = sp.ma_san_pham
                        WHERE " . ($vaiTro === 'nong_ho' ? "sp.ma_nong_ho = ?" : "dh.ma_nguoi_mua = ?") . "
                        AND dh.trang_thai IN ('da_xac_nhan', 'hoan_thanh')
                        AND DATE(dh.ngay_tao) = CURDATE()
                        AND TIME(dh.ngay_tao) BETWEEN ? AND ?";
                $stmt = mysqli_prepare($this->db, $sql);
                mysqli_stmt_bind_param($stmt, "iss", $maNguoiDung, $gio[0], $gio[1]);
                mysqli_stmt_execute($stmt);
                $res = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                $values[] = (float)($res['tien'] ?? 0);
                $counts[] = (int)($res['dem'] ?? 0);
            }
        } elseif ($thoiGian === '7_ngay') {
            // Theo 7 ngày gần nhất
            for ($i = 6; $i >= 0; $i--) {
                $ngay = date('Y-m-d', strtotime("-$i days"));
                $labels[] = date('d/m', strtotime($ngay));
                $sql = "SELECT COALESCE(SUM(dh.gia * dh.so_luong), 0) AS tien, COUNT(*) AS dem
                        FROM don_hang dh
                        JOIN san_pham sp ON dh.ma_san_pham = sp.ma_san_pham
                        WHERE " . ($vaiTro === 'nong_ho' ? "sp.ma_nong_ho = ?" : "dh.ma_nguoi_mua = ?") . "
                        AND dh.trang_thai IN ('da_xac_nhan', 'hoan_thanh')
                        AND DATE(dh.ngay_tao) = ?";
                $stmt = mysqli_prepare($this->db, $sql);
                mysqli_stmt_bind_param($stmt, "is", $maNguoiDung, $ngay);
                mysqli_stmt_execute($stmt);
                $res = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                $values[] = (float)($res['tien'] ?? 0);
                $counts[] = (int)($res['dem'] ?? 0);
            }
        } elseif ($thoiGian === 'thang_nay') {
            // Theo 4 tuần trong tháng hiện tại
            $soNgayTrongThang = (int)date('t');
            $mocTuan = [
                'Tuần 1' => [1, min(7, $soNgayTrongThang)],
                'Tuần 2' => [8, min(14, $soNgayTrongThang)],
                'Tuần 3' => [15, min(21, $soNgayTrongThang)],
                'Tuần 4' => [22, $soNgayTrongThang],
            ];
            $namHienTai = date('Y');
            $thangHienTai = date('m');
            foreach ($mocTuan as $nhan => $ngayRange) {
                $labels[] = $nhan;
                $tuNgay = sprintf('%s-%s-%02d 00:00:00', $namHienTai, $thangHienTai, $ngayRange[0]);
                $denNgay = sprintf('%s-%s-%02d 23:59:59', $namHienTai, $thangHienTai, $ngayRange[1]);
                $sql = "SELECT COALESCE(SUM(dh.gia * dh.so_luong), 0) AS tien, COUNT(*) AS dem
                        FROM don_hang dh
                        JOIN san_pham sp ON dh.ma_san_pham = sp.ma_san_pham
                        WHERE " . ($vaiTro === 'nong_ho' ? "sp.ma_nong_ho = ?" : "dh.ma_nguoi_mua = ?") . "
                        AND dh.trang_thai IN ('da_xac_nhan', 'hoan_thanh')
                        AND dh.ngay_tao BETWEEN ? AND ?";
                $stmt = mysqli_prepare($this->db, $sql);
                mysqli_stmt_bind_param($stmt, "iss", $maNguoiDung, $tuNgay, $denNgay);
                mysqli_stmt_execute($stmt);
                $res = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                $values[] = (float)($res['tien'] ?? 0);
                $counts[] = (int)($res['dem'] ?? 0);
            }
        } else {
            // 'nam_nay' hoặc 'tat_ca': 12 Tháng trong năm
            $namXet = date('Y');
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = "T" . $m;
                $sql = "SELECT COALESCE(SUM(dh.gia * dh.so_luong), 0) AS tien, COUNT(*) AS dem
                        FROM don_hang dh
                        JOIN san_pham sp ON dh.ma_san_pham = sp.ma_san_pham
                        WHERE " . ($vaiTro === 'nong_ho' ? "sp.ma_nong_ho = ?" : "dh.ma_nguoi_mua = ?") . "
                        AND dh.trang_thai IN ('da_xac_nhan', 'hoan_thanh')
                        AND YEAR(dh.ngay_tao) = ? AND MONTH(dh.ngay_tao) = ?";
                $stmt = mysqli_prepare($this->db, $sql);
                mysqli_stmt_bind_param($stmt, "iii", $maNguoiDung, $namXet, $m);
                mysqli_stmt_execute($stmt);
                $res = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                $values[] = (float)($res['tien'] ?? 0);
                $counts[] = (int)($res['dem'] ?? 0);
            }
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'counts' => $counts
        ];
    }
}

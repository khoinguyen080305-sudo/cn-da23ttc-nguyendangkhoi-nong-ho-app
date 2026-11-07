<?php
// ==============================================================================
// MODEL NHU CẦU: models/NhuCau.php
// Quản lý tin thu mua thị trường và thuật toán Ghép Nối Thông Minh (Matching %)
// ==============================================================================

class NhuCau {
    private $db;

    public function __construct($ketnoi) {
        $this->db = $ketnoi;
    }

    /**
     * Người mua đăng tin nhu cầu thu mua (nêu rõ mặt hàng cụ thể)
     */
    public function them($maNguoiMua, $maDanhMuc, $tenNongSan, $soLuong, $donVi, $giaToiDa, $tinh, $hanCan) {
        $cauLenh = "INSERT INTO nhu_cau (ma_nguoi_mua, ma_danh_muc, ten_nong_san, so_luong, don_vi, gia_toi_da, tinh, han_can) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "iisdsdss", $maNguoiMua, $maDanhMuc, $tenNongSan, $soLuong, $donVi, $giaToiDa, $tinh, $hanCan);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Sửa tin nhu cầu
     */
    public function sua($maNhuCau, $maNguoiMua, $maDanhMuc, $tenNongSan, $soLuong, $donVi, $giaToiDa, $tinh, $hanCan) {
        $cauLenh = "UPDATE nhu_cau SET ma_danh_muc = ?, ten_nong_san = ?, so_luong = ?, don_vi = ?, gia_toi_da = ?, tinh = ?, han_can = ? 
                    WHERE ma_nhu_cau = ? AND ma_nguoi_mua = ?";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "isdsdssii", $maDanhMuc, $tenNongSan, $soLuong, $donVi, $giaToiDa, $tinh, $hanCan, $maNhuCau, $maNguoiMua);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Xóa tin nhu cầu
     */
    public function xoa($maNhuCau, $maNguoiMua) {
        $cauLenh = "DELETE FROM nhu_cau WHERE ma_nhu_cau = ? AND ma_nguoi_mua = ?";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "ii", $maNhuCau, $maNguoiMua);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Lấy các tin nhu cầu do 1 người mua đăng
     */
    public function layTheoNguoiMua($maNguoiMua) {
        $cauLenh = "SELECT nc.*, dm.ten_danh_muc 
                    FROM nhu_cau nc
                    JOIN danh_muc dm ON nc.ma_danh_muc = dm.ma_danh_muc
                    WHERE nc.ma_nguoi_mua = ?
                    ORDER BY nc.ma_nhu_cau DESC";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "i", $maNguoiMua);
        mysqli_stmt_execute($stmt);
        $ketQua = mysqli_stmt_get_result($stmt);
        $danhSach = [];
        while ($dong = mysqli_fetch_assoc($ketQua)) {
            $danhSach[] = $dong;
        }
        return $danhSach;
    }

    /**
     * Lấy toàn bộ nhu cầu thị trường còn hiệu lực (cho Nông Hộ xem)
     */
    public function layDanhSachThiTruong($maDanhMuc = 0, $tinh = '') {
        $cauLenh = "SELECT nc.*, dm.ten_danh_muc, nm.ho_ten AS ten_nguoi_mua, nm.sdt AS sdt_nguoi_mua, nm.dia_chi AS dia_chi_nguoi_mua, nm.tinh AS tinh_nguoi_mua
                    FROM nhu_cau nc
                    JOIN danh_muc dm ON nc.ma_danh_muc = dm.ma_danh_muc
                    JOIN nguoi_dung nm ON nc.ma_nguoi_mua = nm.ma_nguoi_dung
                    WHERE nc.han_can >= CURDATE()";
        
        $params = [];
        $types = "";

        if ($maDanhMuc > 0) {
            $cauLenh .= " AND nc.ma_danh_muc = ?";
            $params[] = $maDanhMuc;
            $types .= "i";
        }

        if (!empty($tinh)) {
            $cauLenh .= " AND nc.tinh = ?";
            $params[] = $tinh;
            $types .= "s";
        }

        $cauLenh .= " ORDER BY nc.ma_nhu_cau DESC";
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
     * THUẬT TOÁN GỢI Ý GHÉP NỐI THÔNG MINH (SMART MATCHING %)
     * 1. TIÊN QUYẾT: Phải khớp đúng loại nông sản cụ thể người mua yêu cầu (tránh nói chung chung).
     * 2. Điểm khớp sản phẩm: 40%.
     * 3. Khả năng cung ứng sản lượng kho: 30%.
     * 4. Tương thích giá bán: 15%.
     * 5. Cùng tỉnh thành: 10%.
     * 6. Sẵn hàng hoặc kịp mùa vụ: 5%.
     */
    public function tinhGhepNoiChoNongHo($maNongHo, $tinhNongHo) {
        // Lấy tất cả sản phẩm đang bán của nông hộ
        $cauLenhSP = "SELECT * FROM san_pham WHERE ma_nong_ho = ? AND an = 0";
        $stmtSP = mysqli_prepare($this->db, $cauLenhSP);
        mysqli_stmt_bind_param($stmtSP, "i", $maNongHo);
        mysqli_stmt_execute($stmtSP);
        $resSP = mysqli_stmt_get_result($stmtSP);

        $dsSanPham = [];
        while ($sp = mysqli_fetch_assoc($resSP)) {
            $dsSanPham[] = $sp;
        }

        if (empty($dsSanPham)) return [];

        // Lấy tất cả nhu cầu còn hạn
        $dsNhuCau = $this->layDanhSachThiTruong();
        $ketQuaGhepNoi = [];

        foreach ($dsNhuCau as $nc) {
            $diemCaoNhat = 0;
            $spPhuHopNhat = null;

            foreach ($dsSanPham as $sp) {
                // TIÊU CHÍ BẮT BUỘC 1: Cùng danh mục ngành hàng
                if ($sp['ma_danh_muc'] != $nc['ma_danh_muc']) {
                    continue;
                }

                // TIÊU CHÍ TIÊN QUYẾT 2: Phải đúng loại nông sản người mua yêu cầu (nêu rõ mặt hàng)
                $khopTen = true;
                if (!empty($nc['ten_nong_san'])) {
                    $khopTen = khop_ten_nong_san($nc['ten_nong_san'], $sp['ten']);
                }

                // Nếu nông hộ không trồng đúng mặt hàng này -> Bỏ qua ngay lập tức!
                if (!$khopTen) {
                    continue;
                }

                // ĐÃ KHỚP ĐÚNG LOẠI NÔNG SẢN -> TÍNH ĐIỂM CHI TIẾT
                $diem = 40; // 40 điểm nền tảng cho việc đúng loại sản phẩm

                // 1. Khả năng cung ứng sản lượng kho (30 điểm tối đa)
                $sanLuongKhoQuyDoi = quy_doi_don_vi($sp['san_luong'], $sp['don_vi_san_luong'], $nc['don_vi']);
                if ($sanLuongKhoQuyDoi >= $nc['so_luong']) {
                    $diem += 30; // Kho thừa hoặc đủ hàng
                } elseif ($nc['so_luong'] > 0) {
                    $diem += round(($sanLuongKhoQuyDoi / $nc['so_luong']) * 30);
                }

                // 2. Tương thích giá bán (15 điểm tối đa)
                if ($sp['gia'] === null || $sp['gia'] <= 0) {
                    $diem += 10; // Giá thương lượng khuyến khích kết nối
                } elseif ($nc['gia_toi_da'] !== null && $nc['gia_toi_da'] > 0) {
                    if ($sp['gia'] <= $nc['gia_toi_da']) {
                        $diem += 15;
                    }
                } else {
                    $diem += 10;
                }

                // 3. Lợi thế địa lý cùng tỉnh thành (10 điểm)
                if (!empty($tinhNongHo) && !empty($nc['tinh']) && mb_strtolower(trim($tinhNongHo)) === mb_strtolower(trim($nc['tinh']))) {
                    $diem += 10;
                }

                // 4. Tính sẵn sàng hoặc kịp ngày thu hoạch (5 điểm)
                if ($sp['tinh_trang'] === 'con_hang') {
                    $diem += 5;
                } elseif (!empty($sp['ngay_thu_hoach']) && !empty($nc['han_can'])) {
                    if (strtotime($sp['ngay_thu_hoach']) <= strtotime($nc['han_can'])) {
                        $diem += 5;
                    }
                }

                if ($diem > $diemCaoNhat) {
                    $diemCaoNhat = min(100, $diem);
                    $spPhuHopNhat = $sp;
                }
            }

            if ($diemCaoNhat > 0 && $spPhuHopNhat !== null) {
                $nc['ti_le_phu_hop']   = $diemCaoNhat;
                $nc['ty_le_matching']  = $diemCaoNhat;
                $nc['san_pham_khop']   = $spPhuHopNhat;
                
                // Giải thích lý do ghép nối trực quan, minh bạch cho nông hộ
                $lyDo = "Khớp đúng loại: " . $spPhuHopNhat['ten'];
                $slKhoQD = quy_doi_don_vi($spPhuHopNhat['san_luong'], $spPhuHopNhat['don_vi_san_luong'], $nc['don_vi']);
                if ($slKhoQD >= $nc['so_luong']) {
                    $lyDo .= " • Kho bạn có đủ hàng";
                } else {
                    $lyDo .= " • Kho bạn có " . dinh_dang_so_luong($slKhoQD) . " " . $nc['don_vi'];
                }
                if (!empty($tinhNongHo) && !empty($nc['tinh']) && mb_strtolower(trim($tinhNongHo)) === mb_strtolower(trim($nc['tinh']))) {
                    $lyDo .= " • Cùng tỉnh " . $nc['tinh'];
                }
                $nc['ly_do_phu_hop'] = $lyDo;

                $ketQuaGhepNoi[] = $nc;
            }
        }

        // Sắp xếp từ % phù hợp cao nhất xuống thấp nhất
        usort($ketQuaGhepNoi, function($a, $b) {
            $diemB = $b['ti_le_phu_hop'] ?? $b['ty_le_matching'] ?? 0;
            $diemA = $a['ti_le_phu_hop'] ?? $a['ty_le_matching'] ?? 0;
            return $diemB <=> $diemA;
        });

        return $ketQuaGhepNoi;
    }
}

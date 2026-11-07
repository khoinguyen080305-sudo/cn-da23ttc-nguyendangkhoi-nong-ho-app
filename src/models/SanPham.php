<?php
// ==============================================================================
// MODEL SẢN PHẨM: models/SanPham.php
// Quản lý nông sản, bộ lọc sàn, tách bạch sản lượng & đơn vị giá, dọn dẹp ảnh cũ
// ==============================================================================

class SanPham {
    private $db;

    public function __construct($ketnoi) {
        $this->db = $ketnoi;
        $this->tuDongCapNhatThuHoach();
    }

    /**
     * Tự động chuyển các sản phẩm 'sap_thu_hoach' thành 'con_hang' khi đã đến hoặc qua ngày thu hoạch
     */
    public function tuDongCapNhatThuHoach() {
        if ($this->db) {
            $cauLenh = "UPDATE san_pham SET tinh_trang = 'con_hang' 
                        WHERE tinh_trang = 'sap_thu_hoach' 
                        AND ngay_thu_hoach IS NOT NULL 
                        AND ngay_thu_hoach <= CURDATE()";
            mysqli_query($this->db, $cauLenh);
        }
    }

    /**
     * Lấy danh sách sản phẩm hiển thị trên Sàn Nông Sản (Kèm bộ lọc đa năng & Phân trang)
     */
    public function layDanhSachSan($tuKhoa = '', $maDanhMuc = 0, $tinh = '', $chungNhan = '', $maNongHo = 0, $limit = 0, $offset = 0, $tinhTrang = '', $sapXep = '') {
        $cauLenh = "SELECT sp.*, dm.ten_danh_muc, nd.ho_ten AS ten_nong_ho, nd.sdt, nd.tinh AS tinh_nong_ho, nd.dia_chi,
                    (SELECT COUNT(*) FROM don_hang dh WHERE dh.ma_san_pham = sp.ma_san_pham AND dh.trang_thai = 'hoan_thanh') AS don_thanh_cong
                    FROM san_pham sp
                    JOIN danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc
                    JOIN nguoi_dung nd ON sp.ma_nong_ho = nd.ma_nguoi_dung
                    WHERE sp.an = 0 AND nd.trang_thai = 'da_duyet'";
        
        $params = [];
        $types = "";

        if (!empty($tuKhoa)) {
            $cauLenh .= " AND (sp.ten LIKE ? OR nd.ho_ten LIKE ?)";
            $like = "%$tuKhoa%";
            $params[] = $like; $params[] = $like;
            $types .= "ss";
        }

        if ($maDanhMuc > 0) {
            $cauLenh .= " AND sp.ma_danh_muc = ?";
            $params[] = $maDanhMuc;
            $types .= "i";
        }

        if (!empty($tinh)) {
            $cauLenh .= " AND nd.tinh = ?";
            $params[] = $tinh;
            $types .= "s";
        }

        if (!empty($chungNhan)) {
            $cauLenh .= " AND FIND_IN_SET(?, sp.chung_nhan)";
            $params[] = $chungNhan;
            $types .= "s";
        }

        if ($maNongHo > 0) {
            $cauLenh .= " AND sp.ma_nong_ho = ?";
            $params[] = $maNongHo;
            $types .= "i";
        }

        if (!empty($tinhTrang)) {
            $cauLenh .= " AND sp.tinh_trang = ?";
            $params[] = $tinhTrang;
            $types .= "s";
        }

        // Tùy chọn sắp xếp giá hoặc mặc định mới nhất
        if ($sapXep === 'gia_tang') {
            // Giá thấp đến cao (ưu tiên mặt hàng có giá niêm yết trước, Báo giá xuống cuối)
            $cauLenh .= " ORDER BY CASE WHEN sp.gia IS NULL OR sp.gia <= 0 THEN 1 ELSE 0 END ASC, sp.gia ASC, sp.ma_san_pham DESC";
        } elseif ($sapXep === 'gia_giam') {
            // Giá cao đến thấp (ưu tiên mặt hàng có giá niêm yết trước, Báo giá xuống cuối)
            $cauLenh .= " ORDER BY CASE WHEN sp.gia IS NULL OR sp.gia <= 0 THEN 1 ELSE 0 END ASC, sp.gia DESC, sp.ma_san_pham DESC";
        } else {
            // Mặc định: Nông sản mới nhất lên đầu
            $cauLenh .= " ORDER BY sp.ma_san_pham DESC";
        }

        if ($limit > 0) {
            $cauLenh .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            $types .= "ii";
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
     * Đếm tổng số sản phẩm thỏa mãn bộ lọc (phục vụ phân trang)
     */
    public function demDanhSachSan($tuKhoa = '', $maDanhMuc = 0, $tinh = '', $chungNhan = '', $maNongHo = 0, $tinhTrang = '') {
        $cauLenh = "SELECT COUNT(*) AS tong_so
                    FROM san_pham sp
                    JOIN danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc
                    JOIN nguoi_dung nd ON sp.ma_nong_ho = nd.ma_nguoi_dung
                    WHERE sp.an = 0 AND nd.trang_thai = 'da_duyet'";
        
        $params = [];
        $types = "";

        if (!empty($tuKhoa)) {
            $cauLenh .= " AND (sp.ten LIKE ? OR nd.ho_ten LIKE ?)";
            $like = "%$tuKhoa%";
            $params[] = $like; $params[] = $like;
            $types .= "ss";
        }

        if ($maDanhMuc > 0) {
            $cauLenh .= " AND sp.ma_danh_muc = ?";
            $params[] = $maDanhMuc;
            $types .= "i";
        }

        if (!empty($tinh)) {
            $cauLenh .= " AND nd.tinh = ?";
            $params[] = $tinh;
            $types .= "s";
        }

        if (!empty($chungNhan)) {
            $cauLenh .= " AND FIND_IN_SET(?, sp.chung_nhan)";
            $params[] = $chungNhan;
            $types .= "s";
        }

        if ($maNongHo > 0) {
            $cauLenh .= " AND sp.ma_nong_ho = ?";
            $params[] = $maNongHo;
            $types .= "i";
        }

        if (!empty($tinhTrang)) {
            $cauLenh .= " AND sp.tinh_trang = ?";
            $params[] = $tinhTrang;
            $types .= "s";
        }

        $stmt = mysqli_prepare($this->db, $cauLenh);
        if (!empty($types)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $ketQua = mysqli_stmt_get_result($stmt);
        $dong = mysqli_fetch_assoc($ketQua);
        return (int)($dong['tong_so'] ?? 0);
    }

    /**
     * Lấy chi tiết 1 sản phẩm kèm thông tin nông hộ
     */
    public function chiTiet($maSanPham) {
        $cauLenh = "SELECT sp.*, dm.ten_danh_muc, nd.ho_ten AS ten_nong_ho, nd.sdt, nd.tinh AS tinh_nong_ho, nd.dia_chi,
                    (SELECT COUNT(*) FROM don_hang dh WHERE dh.ma_san_pham = sp.ma_san_pham AND dh.trang_thai = 'hoan_thanh') AS don_thanh_cong
                    FROM san_pham sp
                    JOIN danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc
                    JOIN nguoi_dung nd ON sp.ma_nong_ho = nd.ma_nguoi_dung
                    WHERE sp.ma_san_pham = ? LIMIT 1";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "i", $maSanPham);
        mysqli_stmt_execute($stmt);
        $ketQua = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($ketQua);
    }

    /**
     * Lấy danh sách sản phẩm thuộc quyền quản lý của 1 Nông Hộ
     */
    public function layTheoNongHo($maNongHo) {
        $cauLenh = "SELECT sp.*, dm.ten_danh_muc 
                    FROM san_pham sp
                    JOIN danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc
                    WHERE sp.ma_nong_ho = ?
                    ORDER BY sp.ma_san_pham DESC";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "i", $maNongHo);
        mysqli_stmt_execute($stmt);
        $ketQua = mysqli_stmt_get_result($stmt);

        $danhSach = [];
        while ($dong = mysqli_fetch_assoc($ketQua)) {
            $danhSach[] = $dong;
        }
        return $danhSach;
    }

    /**
     * Thêm nông sản mới (Hỗ trợ ảnh đại diện chính & danh sách tối đa 5 ảnh dạng JSON)
     */
    public function them($maNongHo, $maDanhMuc, $ten, $sanLuong, $donViSanLuong, $gia, $donViGia, $anh, $tinhTrang, $ngayThuHoach, $chungNhan, $danhSachAnh = null) {
        $cauLenh = "INSERT INTO san_pham (ma_nong_ho, ma_danh_muc, ten, san_luong, don_vi_san_luong, gia, don_vi_gia, anh, danh_sach_anh, tinh_trang, ngay_thu_hoach, chung_nhan, an) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "iisdsdssssss", $maNongHo, $maDanhMuc, $ten, $sanLuong, $donViSanLuong, $gia, $donViGia, $anh, $danhSachAnh, $tinhTrang, $ngayThuHoach, $chungNhan);
        
        if (mysqli_stmt_execute($stmt)) {
            return mysqli_insert_id($this->db);
        }
        return false;
    }

    /**
     * Sửa sản phẩm (Cập nhật ảnh đại diện chính & danh sách tối đa 5 ảnh)
     */
    public function sua($maSanPham, $maNongHo, $maDanhMuc, $ten, $sanLuong, $donViSanLuong, $gia, $donViGia, $anhMoi, $tinhTrang, $ngayThuHoach, $chungNhan, $danhSachAnhMoi = null) {
        // 1. Kiểm tra quyền sở hữu
        $spCu = $this->chiTiet($maSanPham);
        if (!$spCu || $spCu['ma_nong_ho'] != $maNongHo) {
            return false; // Không đúng chủ sở hữu
        }

        // 2. Cập nhật vào CSDL (Bao gồm cả danh_sach_anh)
        $cauLenh = "UPDATE san_pham SET 
                    ma_danh_muc = ?, ten = ?, san_luong = ?, don_vi_san_luong = ?, 
                    gia = ?, don_vi_gia = ?, anh = ?, danh_sach_anh = ?, tinh_trang = ?, 
                    ngay_thu_hoach = ?, chung_nhan = ?
                    WHERE ma_san_pham = ? AND ma_nong_ho = ?";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "isdsdssssssii", $maDanhMuc, $ten, $sanLuong, $donViSanLuong, $gia, $donViGia, $anhMoi, $danhSachAnhMoi, $tinhTrang, $ngayThuHoach, $chungNhan, $maSanPham, $maNongHo);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Ẩn hoặc Hiện sản phẩm (Xóa mềm để bảo toàn lịch sử đơn hàng cũ)
     */
    public function anHien($maSanPham, $maNongHo, $trangThaiAn) {
        $cauLenh = "UPDATE san_pham SET an = ? WHERE ma_san_pham = ? AND ma_nong_ho = ?";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "iii", $trangThaiAn, $maSanPham, $maNongHo);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Trừ sản lượng kho (Chỉ thực hiện khi đơn hàng chuyển sang 'da_xac_nhan')
     */
    public function truKho($maSanPham, $soLuongTru) {
        $cauLenh = "UPDATE san_pham SET san_luong = GREATEST(0, san_luong - ?) WHERE ma_san_pham = ?";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "di", $soLuongTru, $maSanPham);
        return mysqli_stmt_execute($stmt);
    }
}

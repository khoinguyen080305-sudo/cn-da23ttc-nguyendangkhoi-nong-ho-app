<?php
// ==============================================================================
// MODEL NGƯỜI DÙNG: models/NguoiDung.php
// Quản lý đăng ký, đăng nhập, đổi mật khẩu tạm, duyệt/khóa tài khoản, lọc tỉnh động
// Đặt tên hàm và biến thuần Việt, sử dụng prepared statement an toàn
// ==============================================================================

class NguoiDung {
    private $db;

    public function __construct($ketnoi) {
        $this->db = $ketnoi;
    }

    /**
     * Tìm người dùng theo số điện thoại (định danh tài khoản chính duy nhất)
     */
    public function timTheoSDT($sdt) {
        $sdt = trim($sdt ?? '');
        $cauLenh = "SELECT * FROM nguoi_dung WHERE sdt = ? LIMIT 1";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "s", $sdt);
        mysqli_stmt_execute($stmt);
        $ketQua = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($ketQua);
    }

    /**
     * Tương thích ngược: Alias cho timTheoSDT
     */
    public function timTheoTaiKhoan($sdt) {
        return $this->timTheoSDT($sdt);
    }

    /**
     * Tìm người dùng theo mã ID
     */
    public function timTheoMa($maNguoiDung) {
        $cauLenh = "SELECT * FROM nguoi_dung WHERE ma_nguoi_dung = ? LIMIT 1";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "i", $maNguoiDung);
        mysqli_stmt_execute($stmt);
        $ketQua = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($ketQua);
    }

    /**
     * Đăng ký tài khoản mới bằng Số điện thoại (sdt)
     * Loại bỏ hoàn toàn trường tên đăng nhập (tai_khoan)
     * Mặc định: người mua là 'da_duyet', nông hộ là 'cho_duyet'
     */
    public function dangKy($sdt, $matKhau, $hoTen, $vaiTro, $tinh = null, $diaChi = null) {
        $sdt = trim($sdt ?? '');
        $matKhauHash = password_hash($matKhau, PASSWORD_BCRYPT);
        $trangThai = ($vaiTro === 'nong_ho') ? 'cho_duyet' : 'da_duyet';

        $cauLenh = "INSERT INTO nguoi_dung (sdt, mat_khau, ho_ten, vai_tro, trang_thai, phai_doi_mk, tinh, dia_chi) 
                    VALUES (?, ?, ?, ?, ?, 0, ?, ?)";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "sssssss", $sdt, $matKhauHash, $hoTen, $vaiTro, $trangThai, $tinh, $diaChi);
        
        if (mysqli_stmt_execute($stmt)) {
            return mysqli_insert_id($this->db);
        }
        return false;
    }

    /**
     * Kiểm tra đăng nhập bằng Số điện thoại và Mật khẩu
     */
    public function dangNhap($sdt, $matKhau) {
        $nguoiDung = $this->timTheoSDT($sdt);
        if (!$nguoiDung) {
            return ['thanh_cong' => false, 'thong_bao' => 'Số điện thoại này chưa được đăng ký tài khoản.'];
        }

        if ($nguoiDung['trang_thai'] === 'khoa') {
            return ['thanh_cong' => false, 'thong_bao' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Admin.'];
        }

        if (!password_verify($matKhau, $nguoiDung['mat_khau'])) {
            return ['thanh_cong' => false, 'thong_bao' => 'Mật khẩu đăng nhập không chính xác.'];
        }

        return ['thanh_cong' => true, 'nguoi_dung' => $nguoiDung];
    }

    /**
     * Đổi mật khẩu mới (áp dụng cho cả đổi bình thường và khi buộc đổi MK tạm)
     */
    public function doiMatKhau($maNguoiDung, $matKhauMoi) {
        $matKhauHash = password_hash($matKhauMoi, PASSWORD_BCRYPT);
        // Khi đổi thành công thì gán phai_doi_mk = 0
        $cauLenh = "UPDATE nguoi_dung SET mat_khau = ?, phai_doi_mk = 0 WHERE ma_nguoi_dung = ?";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "si", $matKhauHash, $maNguoiDung);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Cập nhật thông tin hồ sơ (Họ tên, tỉnh thành, địa chỉ)
     * Tương thích linh hoạt khi truyền 4 hoặc 5 tham số
     */
    public function capNhatHoSo($maNguoiDung, $hoTen, $arg3 = null, $arg4 = null, $arg5 = null) {
        if ($arg5 !== null) {
            $tinh = $arg4;
            $diaChi = $arg5;
        } else {
            $tinh = $arg3;
            $diaChi = $arg4;
        }
        $cauLenh = "UPDATE nguoi_dung SET ho_ten = ?, tinh = ?, dia_chi = ? WHERE ma_nguoi_dung = ?";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "sssi", $hoTen, $tinh, $diaChi, $maNguoiDung);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Admin: Đổi số điện thoại cho tài khoản khi người dùng yêu cầu
     */
    public function capNhatSoDienThoai($maNguoiDung, $sdtMoi) {
        $sdtMoi = trim($sdtMoi ?? '');
        $tonTai = $this->timTheoSDT($sdtMoi);
        if ($tonTai && (int)$tonTai['ma_nguoi_dung'] !== (int)$maNguoiDung) {
            return ['thanh_cong' => false, 'thong_bao' => 'Số điện thoại này đã được đăng ký cho một tài khoản khác.'];
        }

        $cauLenh = "UPDATE nguoi_dung SET sdt = ? WHERE ma_nguoi_dung = ?";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "si", $sdtMoi, $maNguoiDung);
        if (mysqli_stmt_execute($stmt)) {
            return ['thanh_cong' => true, 'thong_bao' => 'Đã cập nhật số điện thoại thành công.'];
        }
        return ['thanh_cong' => false, 'thong_bao' => 'Lỗi khi cập nhật số điện thoại vào CSDL.'];
    }

    /**
     * Admin cấp mật khẩu tạm 1 chạm (Set phai_doi_mk = 1)
     */
    public function capMatKhauTam($maNguoiDung, $matKhauTam) {
        $matKhauHash = password_hash($matKhauTam, PASSWORD_BCRYPT);
        $cauLenh = "UPDATE nguoi_dung SET mat_khau = ?, phai_doi_mk = 1 WHERE ma_nguoi_dung = ?";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "si", $matKhauHash, $maNguoiDung);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Lấy danh sách tỉnh thành thực tế từ các nông hộ đã duyệt
     * Tối ưu lọc nhanh, không import 63 tỉnh thừa thãi
     */
    public function layDanhSachTinhThanh() {
        $cauLenh = "SELECT DISTINCT tinh FROM nguoi_dung 
                    WHERE vai_tro = 'nong_ho' AND trang_thai = 'da_duyet' AND tinh IS NOT NULL AND tinh != '' 
                    ORDER BY tinh ASC";
        $ketQua = mysqli_query($this->db, $cauLenh);
        $dsTinh = [];
        while ($dong = mysqli_fetch_assoc($ketQua)) {
            $dsTinh[] = $dong['tinh'];
        }
        return $dsTinh;
    }

    /**
     * Lấy danh sách nông hộ đã duyệt kèm số sản phẩm đang bán phục vụ bộ lọc
     */
    public function layDanhSachNongHo() {
        $cauLenh = "SELECT nd.ma_nguoi_dung, nd.ho_ten, nd.tinh, nd.dia_chi,
                    (SELECT COUNT(*) FROM san_pham sp WHERE sp.ma_nong_ho = nd.ma_nguoi_dung AND sp.an = 0) AS so_san_pham
                    FROM nguoi_dung nd
                    WHERE nd.vai_tro = 'nong_ho' AND nd.trang_thai = 'da_duyet'
                    ORDER BY nd.ho_ten ASC";
        $ketQua = mysqli_query($this->db, $cauLenh);
        $dsNongHo = [];
        while ($dong = mysqli_fetch_assoc($ketQua)) {
            $dsNongHo[] = $dong;
        }
        return $dsNongHo;
    }

    /**
     * Admin: Lấy danh sách người dùng kèm bộ lọc và đếm số đơn hoàn thành
     */
    public function layDanhSachAdmin($tuKhoa = '', $vaiTro = '', $trangThai = '') {
        $cauLenh = "SELECT u.*, 
                    (SELECT COUNT(*) FROM don_hang dh 
                     JOIN san_pham sp ON dh.ma_san_pham = sp.ma_san_pham 
                     WHERE sp.ma_nong_ho = u.ma_nguoi_dung AND dh.trang_thai = 'hoan_thanh') AS so_don_thanh_cong
                    FROM nguoi_dung u WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($tuKhoa)) {
            $cauLenh .= " AND (u.sdt LIKE ? OR u.ho_ten LIKE ? OR u.tinh LIKE ? OR u.dia_chi LIKE ?)";
            $like = "%$tuKhoa%";
            $params[] = $like; $params[] = $like; $params[] = $like; $params[] = $like;
            $types .= "ssss";
        }

        if (!empty($vaiTro)) {
            $cauLenh .= " AND u.vai_tro = ?";
            $params[] = $vaiTro;
            $types .= "s";
        }

        if (!empty($trangThai)) {
            $cauLenh .= " AND u.trang_thai = ?";
            $params[] = $trangThai;
            $types .= "s";
        }

        $cauLenh .= " ORDER BY u.ma_nguoi_dung DESC";
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
     * Admin: Đổi trạng thái tài khoản (da_duyet / khoa)
     */
    public function capNhatTrangThai($maNguoiDung, $trangThaiMoi) {
        $cauLenh = "UPDATE nguoi_dung SET trang_thai = ? WHERE ma_nguoi_dung = ?";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "si", $trangThaiMoi, $maNguoiDung);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Admin: Từ chối và xóa vĩnh viễn tài khoản nông hộ đang chờ duyệt khỏi CSDL
     */
    public function xoaNongHoChoDuyet($maNguoiDung) {
        $cauLenh = "DELETE FROM nguoi_dung WHERE ma_nguoi_dung = ? AND vai_tro = 'nong_ho' AND trang_thai = 'cho_duyet'";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "i", $maNguoiDung);
        return mysqli_stmt_execute($stmt);
    }
}

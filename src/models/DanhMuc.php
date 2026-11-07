<?php
// ==============================================================================
// MODEL DANH MỤC: models/DanhMuc.php
// Quản lý ngành hàng nông sản (Trái cây, Rau củ sạch, Lúa gạo...)
// ==============================================================================

class DanhMuc {
    private $db;

    public function __construct($ketnoi) {
        $this->db = $ketnoi;
    }

    /**
     * Lấy toàn bộ danh mục kèm số lượng sản phẩm đang có
     */
    public function layTatCa() {
        $cauLenh = "SELECT dm.*, 
                    (SELECT COUNT(*) FROM san_pham sp WHERE sp.ma_danh_muc = dm.ma_danh_muc AND sp.an = 0) AS so_san_pham
                    FROM danh_muc dm ORDER BY dm.ma_danh_muc ASC";
        $ketQua = mysqli_query($this->db, $cauLenh);
        $danhSach = [];
        while ($dong = mysqli_fetch_assoc($ketQua)) {
            $danhSach[] = $dong;
        }
        return $danhSach;
    }

    /**
     * Lấy danh sách danh mục thực tế đang có sản phẩm (dùng để lọc động tương tự tỉnh thành)
     * Giữ lại danh mục đang chọn nếu có để không bị mất ngữ cảnh tìm kiếm
     */
    public function layDanhSachCoSanPham($maDanhMucDangChon = 0) {
        $maDanhMucDangChon = (int)$maDanhMucDangChon;
        $dieuKienHaving = "HAVING so_san_pham > 0" . ($maDanhMucDangChon > 0 ? " OR dm.ma_danh_muc = {$maDanhMucDangChon}" : "");
        $cauLenh = "SELECT dm.*, 
                    (SELECT COUNT(*) FROM san_pham sp WHERE sp.ma_danh_muc = dm.ma_danh_muc AND sp.an = 0) AS so_san_pham
                    FROM danh_muc dm 
                    {$dieuKienHaving} 
                    ORDER BY so_san_pham DESC, dm.ten_danh_muc ASC";
        $ketQua = mysqli_query($this->db, $cauLenh);
        $danhSach = [];
        if ($ketQua) {
            while ($dong = mysqli_fetch_assoc($ketQua)) {
                $danhSach[] = $dong;
            }
        }

        // Nếu sàn mới tinh chưa có sản phẩm nào, fallback lấy tất cả danh mục
        if (empty($danhSach)) {
            return $this->layTatCa();
        }
        return $danhSach;
    }

    /**
     * Thêm mới danh mục hoặc lấy ID nếu đã tồn tại (không phân biệt hoa/thường)
     * Dùng khi nông dân tự thêm ngành nông sản mới từ form sản phẩm
     */
    public function layHoacThemMoi($tenDanhMuc) {
        $tenDanhMuc = trim($tenDanhMuc ?? '');
        if (empty($tenDanhMuc)) return 0;

        // Viết hoa chữ cái đầu cho đẹp và chuẩn mực
        $tenChuanHoa = mb_strtoupper(mb_substr($tenDanhMuc, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($tenDanhMuc, 1, null, 'UTF-8');

        // Kiểm tra xem đã có danh mục nào trùng tên chưa (so sánh không phân biệt hoa/thường)
        $cauLenhCheck = "SELECT ma_danh_muc FROM danh_muc WHERE LOWER(TRIM(ten_danh_muc)) = LOWER(?) LIMIT 1";
        $stmtCheck = mysqli_prepare($this->db, $cauLenhCheck);
        mysqli_stmt_bind_param($stmtCheck, "s", $tenChuanHoa);
        mysqli_stmt_execute($stmtCheck);
        $res = mysqli_stmt_get_result($stmtCheck);
        if ($dong = mysqli_fetch_assoc($res)) {
            return (int)$dong['ma_danh_muc'];
        }

        // Chưa có -> Thêm mới vào CSDL
        $cauLenhInsert = "INSERT INTO danh_muc (ten_danh_muc) VALUES (?)";
        $stmtInsert = mysqli_prepare($this->db, $cauLenhInsert);
        mysqli_stmt_bind_param($stmtInsert, "s", $tenChuanHoa);
        if (mysqli_stmt_execute($stmtInsert)) {
            return (int)mysqli_insert_id($this->db);
        }
        return 0;
    }

    /**
     * Tìm danh mục theo mã ID
     */
    public function timTheoMa($maDanhMuc) {
        $cauLenh = "SELECT * FROM danh_muc WHERE ma_danh_muc = ? LIMIT 1";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "i", $maDanhMuc);
        mysqli_stmt_execute($stmt);
        $ketQua = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($ketQua);
    }

    /**
     * Thêm mới danh mục
     */
    public function them($tenDanhMuc) {
        $tenDanhMuc = trim($tenDanhMuc);
        $cauLenh = "INSERT INTO danh_muc (ten_danh_muc) VALUES (?)";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "s", $tenDanhMuc);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Sửa tên danh mục
     */
    public function sua($maDanhMuc, $tenDanhMuc) {
        $tenDanhMuc = trim($tenDanhMuc);
        $cauLenh = "UPDATE danh_muc SET ten_danh_muc = ? WHERE ma_danh_muc = ?";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "si", $tenDanhMuc, $maDanhMuc);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Xóa danh mục (Kiểm tra nếu có sản phẩm thì không cho xóa để tránh lỗi)
     */
    public function xoa($maDanhMuc) {
        // Kiểm tra xem có sản phẩm thuộc danh mục này không
        $check = mysqli_prepare($this->db, "SELECT COUNT(*) as dem FROM san_pham WHERE ma_danh_muc = ?");
        mysqli_stmt_bind_param($check, "i", $maDanhMuc);
        mysqli_stmt_execute($check);
        $res = mysqli_stmt_get_result($check);
        $row = mysqli_fetch_assoc($res);
        if ($row['dem'] > 0) {
            return ['thanh_cong' => false, 'thong_bao' => 'Không thể xóa vì đang có sản phẩm thuộc danh mục này.'];
        }

        $cauLenh = "DELETE FROM danh_muc WHERE ma_danh_muc = ?";
        $stmt = mysqli_prepare($this->db, $cauLenh);
        mysqli_stmt_bind_param($stmt, "i", $maDanhMuc);
        if (mysqli_stmt_execute($stmt)) {
            return ['thanh_cong' => true, 'thong_bao' => 'Đã xóa danh mục thành công.'];
        }
        return ['thanh_cong' => false, 'thong_bao' => 'Lỗi khi xóa danh mục.'];
    }
}

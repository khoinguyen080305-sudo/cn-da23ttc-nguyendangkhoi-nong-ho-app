<?php
// ==============================================================================
// CONTROLLER ADMIN: controllers/AdminController.php
// Quản trị hệ thống tinh gọn: Duyệt nông hộ, Quản lý tài khoản, Cấp MK tạm 1 chạm, Danh mục
// TUYỆT ĐỐI KHÔNG DÙNG CARD THẺ THÔ KỆCH, TẬP TRUNG TÁC VỤ 1 CHẠM TRỰC QUAN
// ==============================================================================

class AdminController {
    private $modelNguoiDung;
    private $modelDanhMuc;
    private $modelSanPham;
    private $modelDonHang;

    public function __construct($models) {
        $this->modelNguoiDung = $models['nguoi_dung'];
        $this->modelDanhMuc   = $models['danh_muc'];
        $this->modelSanPham   = $models['san_pham'];
        $this->modelDonHang   = $models['don_hang'];
    }

    /**
     * Bảng điều khiển Quản trị Tinh gọn (Admin Command Center)
     */
    public function tongQuan() {
        yeu_cau_vai_tro('admin');

        $tuKhoa     = lam_sach($_GET['tu_khoa'] ?? '');
        $vaiTro     = lam_sach($_GET['vai_tro'] ?? '');
        $trangThai  = lam_sach($_GET['trang_thai'] ?? '');

        // Lấy danh sách người dùng cho bảng quản trị
        $dsNguoiDung = $this->modelNguoiDung->layDanhSachAdmin($tuKhoa, $vaiTro, $trangThai);
        
        // Lấy riêng danh sách nông hộ chờ duyệt (Ưu tiên hiển thị trên đầu)
        $dsChoDuyet  = $this->modelNguoiDung->layDanhSachAdmin('', 'nong_ho', 'cho_duyet');

        // Danh mục nông sản
        $dsDanhMuc   = $this->modelDanhMuc->layTatCa();

        require_once __DIR__ . '/../views/admin/tong_quan.php';
    }

    /**
     * Duyệt nông hộ (chuyển sang 'da_duyet')
     */
    public function duyetNongHo() {
        yeu_cau_vai_tro('admin');
        $id = (int)($_GET['id'] ?? 0);

        if ($this->modelNguoiDung->capNhatTrangThai($id, 'da_duyet')) {
            $this->modelDonHang->guiThongBao($id, 'Hồ sơ nông hộ của bạn đã được Ban Quản Trị phê duyệt. Bây giờ bạn có thể bắt đầu đăng bán nông sản.');
            dat_thong_bao('success', 'Đã duyệt', 'Đã phê duyệt hồ sơ nông hộ thành công.');
        } else {
            dat_thong_bao('error', 'Lỗi', 'Không thể duyệt hồ sơ.');
        }

        chuyen_huong('index.php?trang=admin_tong_quan');
    }

    /**
     * Từ chối hồ sơ nông hộ & Xóa vĩnh viễn tài khoản khỏi CSDL
     */
    public function tuChoiNongHo() {
        yeu_cau_vai_tro('admin');
        $id = (int)($_GET['id'] ?? 0);

        if ($this->modelNguoiDung->xoaNongHoChoDuyet($id)) {
            dat_thong_bao('info', 'Từ chối hồ sơ', 'Đã từ chối và xóa hoàn toàn hồ sơ nông hộ khỏi cơ sở dữ liệu.');
        } else {
            dat_thong_bao('error', 'Lỗi', 'Không thể xóa hồ sơ nông hộ này.');
        }

        chuyen_huong('index.php?trang=admin_tong_quan');
    }

    /**
     * Khóa hoặc Mở khóa tài khoản
     */
    public function doiTrangThaiTaiKhoan() {
        yeu_cau_vai_tro('admin');
        $id        = (int)($_GET['id'] ?? 0);
        $trangThai = lam_sach($_GET['trang_thai'] ?? 'khoa');

        if ($this->modelNguoiDung->capNhatTrangThai($id, $trangThai)) {
            $thongBao = ($trangThai === 'khoa') ? 'Đã tạm khóa tài khoản thành công.' : 'Đã mở khóa tài khoản thành công.';
            dat_thong_bao('info', 'Trạng thái tài khoản', $thongBao);
        }

        chuyen_huong('index.php?trang=admin_tong_quan');
    }

    /**
     * Cấp mật khẩu tạm 1 chạm (Xử lý khi người dùng quên mật khẩu và liên hệ Admin)
     */
    public function capMatKhauTam() {
        yeu_cau_vai_tro('admin');
        $id = (int)($_POST['ma_nguoi_dung'] ?? 0);

        $user = $this->modelNguoiDung->timTheoMa($id);
        if (!$user) {
            echo json_encode(['thanh_cong' => false, 'thong_bao' => 'Không tìm thấy người dùng.']);
            exit();
        }

        // Sinh mật khẩu ngẫu nhiên dễ đọc: Vd NH@8492
        $matKhauTam = 'NH@' . rand(1000, 9999);

        if ($this->modelNguoiDung->capMatKhauTam($id, $matKhauTam)) {
            $this->modelDonHang->guiThongBao($id, 'Mật khẩu của bạn vừa được Admin cấp lại tạm thời. Vui lòng đổi mật khẩu mới ngay khi đăng nhập.');
            
            echo json_encode([
                'thanh_cong'   => true,
                'tai_khoan'    => $user['sdt'],
                'sdt'          => $user['sdt'],
                'ho_ten'       => $user['ho_ten'],
                'mat_khau_tam' => $matKhauTam,
                'thong_bao'    => 'Đã cấp mật khẩu tạm thành công.'
            ]);
            exit();
        }

        echo json_encode(['thanh_cong' => false, 'thong_bao' => 'Lỗi khi cấp mật khẩu tạm.']);
        exit();
    }

    /**
     * Admin đổi số điện thoại cho tài khoản khi người dùng yêu cầu
     * @return void
     */
    public function doiSoDienThoai() {
        yeu_cau_vai_tro('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maNguoiDung = (int)($_POST['ma_nguoi_dung'] ?? 0);
            $sdtMoi      = lam_sach($_POST['sdt_moi'] ?? '');

            if (!kiem_tra_sdt_hop_le($sdtMoi)) {
                dat_thong_bao('error', 'Lỗi định dạng', 'Số điện thoại mới không hợp lệ. Vui lòng nhập số di động 10 chữ số Việt Nam.');
            } else {
                $kq = $this->modelNguoiDung->capNhatSoDienThoai($maNguoiDung, $sdtMoi);
                if ($kq['thanh_cong']) {
                    $this->modelDonHang->guiThongBao($maNguoiDung, 'Số điện thoại tài khoản của bạn đã được Admin cập nhật thành: ' . $sdtMoi);
                    dat_thong_bao('success', 'Thành công', 'Đã đổi số điện thoại tài khoản thành: ' . $sdtMoi);
                } else {
                    dat_thong_bao('error', 'Lỗi', $kq['thong_bao']);
                }
            }
        }
        chuyen_huong('index.php?trang=admin_tong_quan');
    }

    /**
     * Thêm danh mục nông sản
     */
    public function themDanhMuc() {
        yeu_cau_vai_tro('admin');
        $tenDanhMuc = lam_sach($_POST['ten_danh_muc'] ?? '');

        if (!empty($tenDanhMuc)) {
            if ($this->modelDanhMuc->them($tenDanhMuc)) {
                dat_thong_bao('success', 'Danh mục', 'Đã thêm danh mục nông sản mới thành công.');
            } else {
                dat_thong_bao('error', 'Lỗi', 'Tên danh mục có thể đã bị trùng lặp.');
            }
        }
        chuyen_huong('index.php?trang=admin_tong_quan');
    }

    /**
     * Xóa danh mục nông sản
     */
    public function xoaDanhMuc() {
        yeu_cau_vai_tro('admin');
        $id = (int)($_GET['id'] ?? 0);
        $ketQua = $this->modelDanhMuc->xoa($id);
        
        $loai = $ketQua['thanh_cong'] ? 'success' : 'error';
        dat_thong_bao($loai, 'Quản lý danh mục', $ketQua['thong_bao']);
        chuyen_huong('index.php?trang=admin_tong_quan');
    }
}

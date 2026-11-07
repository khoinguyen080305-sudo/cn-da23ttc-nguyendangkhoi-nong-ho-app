<?php
// ==============================================================================
// CONTROLLER ĐƠN HÀNG: controllers/DonHangController.php
// Quản lý quy trình 3 bước đơn hàng (Dùng chung cho cả Người Mua & Nông Hộ)
// Tự động trừ kho khi đơn sang 'da_xac_nhan', đổi trạng thái và gửi thông báo
// ==============================================================================

class DonHangController {
    private $modelNguoiDung;
    private $modelDonHang;
    private $modelSanPham;

    public function __construct($models) {
        $this->modelNguoiDung = $models['nguoi_dung'];
        $this->modelDonHang   = $models['don_hang'];
        $this->modelSanPham   = $models['san_pham'];
    }

    /**
     * Xem danh sách đơn hàng theo 3 Tab trạng thái
     */
    public function danhSach() {
        if (!da_dang_nhap()) {
            chuyen_huong('index.php?trang=dang_nhap');
        }

        $nguoiDung = nguoi_dung_hien_tai();
        $vaiTro    = $nguoiDung['vai_tro'];
        
        if ($vaiTro === 'admin') {
            dat_thong_bao('info', 'Thông Báo Quản Trị', 'Khu vực quản lý giao dịch đơn hàng chỉ dành riêng cho Nông Hộ và Người Mua.');
            chuyen_huong('index.php?trang=admin_tong_quan');
        }
        
        // Tab hiện tại: 'dang_xu_ly' (mặc định), 'da_xac_nhan', 'lich_su'
        $tab       = lam_sach($_GET['tab'] ?? 'dang_xu_ly');

        // Bộ lọc đơn hàng (Tìm kiếm, Quy mô sỉ, Tình trạng xử lý, Thời gian, Sắp xếp)
        $boLoc = [
            'tu_khoa'   => lam_sach($_GET['tu_khoa'] ?? ''),
            'quy_mo'    => lam_sach($_GET['quy_mo'] ?? ''),
            'xu_ly'     => lam_sach($_GET['xu_ly'] ?? ''),
            'thoi_gian' => lam_sach($_GET['thoi_gian'] ?? ''),
            'sap_xep'   => lam_sach($_GET['sap_xep'] ?? 'moi_nhat'),
        ];

        $dsDonHang = $this->modelDonHang->layDanhSachTheoTab($nguoiDung['ma_nguoi_dung'], $vaiTro, $tab, $boLoc);
        $demTabs   = $this->modelDonHang->demTheoCacTab($nguoiDung['ma_nguoi_dung'], $vaiTro);

        require_once __DIR__ . '/../views/don_hang/danh_sach.php';
    }

    /**
     * Nông Hộ báo giá lại & quyết định chiết khấu sỉ (chuyển sang 'da_bao_gia')
     */
    public function baoGiaLai() {
        yeu_cau_vai_tro('nong_ho');
        $nguoiDung  = nguoi_dung_hien_tai();
        $maDonHang  = (int)($_POST['ma_don_hang'] ?? 0);
        $giaMoi     = (float)($_POST['gia_moi'] ?? 0);
        $giaBanDau  = isset($_POST['gia_ban_dau']) && (float)$_POST['gia_ban_dau'] > 0 ? (float)$_POST['gia_ban_dau'] : null;
        $ghiChu     = lam_sach($_POST['ghi_chu'] ?? '');

        if ($maDonHang > 0 && $giaMoi > 0) {
            if ($this->modelDonHang->nongHoBaoGia($maDonHang, $nguoiDung['ma_nguoi_dung'], $giaMoi, $ghiChu, $giaBanDau)) {
                dat_thong_bao('success', 'Báo giá thành công', 'Đã gửi mức báo giá mới và chính sách ưu đãi sỉ đến người mua. Vui lòng chờ phản hồi.');
            } else {
                dat_thong_bao('error', 'Lỗi', 'Không thể gửi báo giá cho đơn hàng này.');
            }
        }
        chuyen_huong('index.php?trang=don_hang&tab=dang_xu_ly');
    }

    /**
     * Xác nhận đơn hàng (Đồng thuận chốt giá & TỰ ĐỘNG TRỪ SẢN LƯỢNG KHO)
     * - Nông hộ chốt đơn (có thể chỉnh giá chốt & giảm giá sỉ động riêng cho đơn tại đây)
     * - Người mua chấp nhận giá nông hộ báo lại (tại 'da_bao_gia')
     */
    public function xacNhan() {
        if (!da_dang_nhap()) chuyen_huong('index.php?trang=dang_nhap');

        $nguoiDung  = nguoi_dung_hien_tai();
        $maDonHang  = (int)($_POST['ma_don_hang'] ?? ($_GET['id'] ?? 0));
        $giaChot    = isset($_POST['gia_chot']) && (float)$_POST['gia_chot'] > 0 ? (float)$_POST['gia_chot'] : null;
        $giaBanDau  = isset($_POST['gia_ban_dau']) && (float)$_POST['gia_ban_dau'] > 0 ? (float)$_POST['gia_ban_dau'] : null;
        $ghiChu     = isset($_POST['ghi_chu']) ? lam_sach($_POST['ghi_chu']) : null;

        if ($this->modelDonHang->xacNhanDonHang($maDonHang, $nguoiDung['ma_nguoi_dung'], $nguoiDung['vai_tro'], $this->modelSanPham, $giaChot, $ghiChu, $giaBanDau)) {
            $thongBao = 'Đơn hàng đã được xác nhận đồng thuận và hệ thống đã tự động trừ sản lượng kho.';
            if ($giaChot !== null && $giaChot > 0) {
                $thongBao = 'Đã chốt đơn hàng thành công với mức giá ' . dinh_dang_tien($giaChot) . ' đ và tự động trừ sản lượng kho.';
            }
            dat_thong_bao('success', 'Chốt đơn thành công', $thongBao);
            chuyen_huong('index.php?trang=don_hang&tab=da_xac_nhan');
        } else {
            dat_thong_bao('error', 'Lỗi', 'Không thể xác nhận đơn hàng này.');
            chuyen_huong('index.php?trang=don_hang');
        }
    }

    /**
     * Nông Hộ hoàn thành đơn (khi giao hàng xong)
     */
    public function hoanThanh() {
        yeu_cau_vai_tro('nong_ho');
        $nguoiDung = nguoi_dung_hien_tai();
        $maDonHang = (int)($_GET['id'] ?? 0);

        if ($this->modelDonHang->hoanThanhDon($maDonHang, $nguoiDung['ma_nguoi_dung'])) {
            dat_thong_bao('success', 'Hoàn tất đơn hàng', 'Đơn hàng đã giao dịch thành công. Điểm số đơn hoàn thành của bạn đã được cộng.');
            chuyen_huong('index.php?trang=don_hang&tab=lich_su');
        } else {
            dat_thong_bao('error', 'Lỗi', 'Không thể hoàn thành đơn hàng này.');
            chuyen_huong('index.php?trang=don_hang');
        }
    }

    /**
     * Hủy đơn hàng
     */
    public function huyDon() {
        if (!da_dang_nhap()) chuyen_huong('index.php?trang=dang_nhap');

        $nguoiDung = nguoi_dung_hien_tai();
        $maDonHang = (int)($_GET['id'] ?? 0);
        $don       = $this->modelDonHang->chiTiet($maDonHang);

        if (!$don) {
            dat_thong_bao('error', 'Lỗi', 'Không tìm thấy đơn hàng cần hủy.');
            chuyen_huong('index.php?trang=don_hang');
        }

        // Đơn hàng đã được chốt (da_xac_nhan) thì cả 2 bên không được phép hủy bỏ
        if ($don['trang_thai'] === 'da_xac_nhan') {
            dat_thong_bao('warning', 'Không thể hủy đơn', 'Đơn hàng đã được nông hộ chốt và hai bên đang thực hiện giao dịch, không được phép hủy.');
            chuyen_huong('index.php?trang=don_hang&tab=da_xac_nhan');
        }

        if ($this->modelDonHang->huyDonHang($maDonHang, $nguoiDung['ma_nguoi_dung'], $nguoiDung['vai_tro'])) {
            dat_thong_bao('info', 'Hủy đơn hàng', 'Đơn hàng đã được hủy bỏ thành công.');
            chuyen_huong('index.php?trang=don_hang&tab=lich_su');
        } else {
            dat_thong_bao('error', 'Lỗi', 'Không thể hủy đơn hàng này.');
            chuyen_huong('index.php?trang=don_hang');
        }
    }
}

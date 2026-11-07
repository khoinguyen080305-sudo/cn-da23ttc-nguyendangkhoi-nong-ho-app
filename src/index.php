<?php
// ============================================
// ĐIỀU HƯỚNG TẬP TRUNG: index.php (ROUTER MVC)
// ============================================

require_once __DIR__ . '/config.php';

// Nạp vào các Models
require_once __DIR__ . '/models/NguoiDung.php';
require_once __DIR__ . '/models/DanhMuc.php';
require_once __DIR__ . '/models/SanPham.php';
require_once __DIR__ . '/models/DonHang.php';
require_once __DIR__ . '/models/NhuCau.php';

// Khởi tạo các Model
$models = [
    'nguoi_dung' => new NguoiDung($ketnoi),
    'danh_muc'   => new DanhMuc($ketnoi),
    'san_pham'   => new SanPham($ketnoi),
    'don_hang'   => new DonHang($ketnoi),
    'nhu_cau'    => new NhuCau($ketnoi),
];

// Nạp vào các Controllers
require_once __DIR__ . '/controllers/CongKhaiController.php';
require_once __DIR__ . '/controllers/NguoiMuaController.php';
require_once __DIR__ . '/controllers/NongHoController.php';
require_once __DIR__ . '/controllers/AdminController.php';
require_once __DIR__ . '/controllers/DonHangController.php';

// Khởi tạo các Controller
/** @var CongKhaiController $ctrlCongKhai */
$ctrlCongKhai = new CongKhaiController($models);

/** @var NguoiMuaController $ctrlNguoiMua */
$ctrlNguoiMua = new NguoiMuaController($models);

/** @var NongHoController $ctrlNongHo */
$ctrlNongHo   = new NongHoController($models);

/** @var AdminController $ctrlAdmin */
$ctrlAdmin    = new AdminController($models);

/** @var DonHangController $ctrlDonHang */
$ctrlDonHang  = new DonHangController($models);

// Lấy tham số trang điều hướng (Mặc định: 'trang_chu')
$trang = lam_sach($_GET['trang'] ?? 'trang_chu');

// KIỂM TRA BẢO MẬT: Bắt buộc đổi mật khẩu nếu đang dùng mật khẩu tạm do Admin cấp
$phaiDoiMk = !empty($_SESSION['nguoi_dung']['phai_doi_mk']) || !empty($_SESSION['phai_doi_mk']);
if (da_dang_nhap() && $phaiDoiMk) {
    if ($trang !== 'doi_mk_tam' && $trang !== 'dang_xuat') {
        dat_thong_bao('warning', 'Yêu cầu bảo mật', 'Bạn đang dùng mật khẩu tạm do Admin cấp. Vui lòng đổi mật khẩu mới để tiếp tục.');
        chuyen_huong('index.php?trang=doi_mk_tam');
    }
}

// BẢNG ĐIỀU HƯỚNG CÁC TRANG (ROUTING)
switch ($trang) {
    // ----------------------------------------------------
    // CÁC TRANG CÔNG KHAI & XÁC THỰC
    // ----------------------------------------------------
    case 'trang_chu':
        $ctrlCongKhai->trangChu();
        break;

    case 'chi_tiet':
        $ctrlCongKhai->chiTiet();
        break;

    case 'dang_ky':
        $ctrlCongKhai->dangKy();
        break;

    case 'dang_nhap':
        $ctrlCongKhai->dangNhap();
        break;

    case 'doi_mk_tam':
        $ctrlCongKhai->doiMatKhauTam();
        break;

    case 'dang_xuat':
        $ctrlCongKhai->dangXuat();
        break;

    // ----------------------------------------------------
    // VAI TRÒ NGƯỜI MUA: GIỎ HÀNG & NHU CẦU THU MUA
    // ----------------------------------------------------
    case 'them_gio_hang':
        $ctrlNguoiMua->themGioHang();
        break;

    case 'gio_hang':
        $ctrlNguoiMua->xemGioHang();
        break;

    case 'cap_nhat_gio_hang':
        $ctrlNguoiMua->capNhatGioHang();
        break;

    case 'xoa_gio_hang':
        $ctrlNguoiMua->xoaKhoiGioHang();
        break;

    case 'gui_don_hang':
        $ctrlNguoiMua->guiDonHang();
        break;

    case 'nhu_cau_cua_toi':
        $ctrlNguoiMua->nhuCauCuaToi();
        break;

    case 'xoa_nhu_cau':
        $ctrlNguoiMua->xoaNhuCau();
        break;

    case 'nguoi_mua_ho_so':
        $ctrlNguoiMua->hoSo();
        break;

    // ----------------------------------------------------
    // VAI TRÒ NÔNG HỘ: QUẢN LÝ SẢN PHẨM & GHÉP NỐI
    // ----------------------------------------------------
    case 'nong_ho_san_pham':
        $ctrlNongHo->danhSachSanPham();
        break;

    case 'nong_ho_form_san_pham':
        $ctrlNongHo->formSanPham();
        break;

    case 'nong_ho_an_hien':
        $ctrlNongHo->anHienSanPham();
        break;

    case 'nong_ho_nhu_cau':
        $ctrlNongHo->nhuCauThiTruong();
        break;

    case 'nong_ho_ho_so':
        $ctrlNongHo->hoSo();
        break;

    // ----------------------------------------------------
    // QUY TRÌNH ĐƠN HÀNG (DÙNG CHUNG NGƯỜI MUA & NÔNG HỘ)
    // ----------------------------------------------------
    case 'don_hang':
        $ctrlDonHang->danhSach();
        break;

    case 'don_hang_xac_nhan':
        $ctrlDonHang->xacNhan();
        break;

    case 'don_hang_bao_gia':
        $ctrlDonHang->baoGiaLai();
        break;

    case 'don_hang_hoan_thanh':
        $ctrlDonHang->hoanThanh();
        break;

    case 'don_hang_huy':
        $ctrlDonHang->huyDon();
        break;

    // ----------------------------------------------------
    // VAI TRÒ ADMIN: QUẢN TRỊ TINH GỌN
    // ----------------------------------------------------
    case 'admin_tong_quan':
        $ctrlAdmin->tongQuan();
        break;

    case 'admin_duyet_nong_ho':
        $ctrlAdmin->duyetNongHo();
        break;

    case 'admin_tu_choi_nong_ho':
        $ctrlAdmin->tuChoiNongHo();
        break;

    case 'admin_doi_trang_thai':
        $ctrlAdmin->doiTrangThaiTaiKhoan();
        break;

    case 'admin_doi_sdt':
        $ctrlAdmin->doiSoDienThoai();
        break;

    case 'admin_cap_mk_tam':
        $ctrlAdmin->capMatKhauTam();
        break;

    case 'admin_them_danh_muc':
        $ctrlAdmin->themDanhMuc();
        break;

    case 'admin_xoa_danh_muc':
        $ctrlAdmin->xoaDanhMuc();
        break;

    // MẶC ĐỊNH
    default:
        $ctrlCongKhai->trangChu();
        break;
}

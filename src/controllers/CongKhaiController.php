<?php
// ==============================================================================
// CONTROLLER CÔNG KHAI: controllers/CongKhaiController.php
// Quản lý Sàn nông sản, Xem chi tiết, Đăng ký, Đăng nhập, Đổi mật khẩu tạm, Đăng xuất
// ==============================================================================

class CongKhaiController {
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
     * Trang chủ Sàn Nông Sản
     */
    public function trangChu() {
        $tuKhoa     = lam_sach($_GET['tu_khoa'] ?? '');
        $maDanhMuc  = (int)($_GET['danh_muc'] ?? 0);
        $tinh       = lam_sach($_GET['tinh'] ?? '');
        $chungNhan  = lam_sach($_GET['chung_nhan'] ?? '');
        $maNongHo   = (int)($_GET['nong_ho'] ?? 0);
        $tinhTrang  = lam_sach($_GET['tinh_trang'] ?? ($_GET['trang_thai'] ?? ''));
        $sapXep     = lam_sach($_GET['sap_xep'] ?? '');

        // Cấu hình phân trang: 6 sản phẩm mỗi trang
        $soLuongMoiTrang = 6;
        $trangHienTai    = max(1, (int)($_GET['trang_sp'] ?? 1));
        
        // Đếm tổng số lượng và tính số trang
        $tongSoSanPham   = $this->modelSanPham->demDanhSachSan($tuKhoa, $maDanhMuc, $tinh, $chungNhan, $maNongHo, $tinhTrang);
        $tongSoTrang     = max(1, (int)ceil($tongSoSanPham / $soLuongMoiTrang));
        if ($trangHienTai > $tongSoTrang) {
            $trangHienTai = $tongSoTrang;
        }
        $viTriBatDau     = ($trangHienTai - 1) * $soLuongMoiTrang;

        // Lấy danh sách sản phẩm theo trang và các bộ lọc
        $dsSanPham   = $this->modelSanPham->layDanhSachSan($tuKhoa, $maDanhMuc, $tinh, $chungNhan, $maNongHo, $soLuongMoiTrang, $viTriBatDau, $tinhTrang, $sapXep);
        $dsDanhMuc   = $this->modelDanhMuc->layDanhSachCoSanPham($maDanhMuc); // Lọc động: Chỉ lấy danh mục có sản phẩm thực tế
        $dsTinhThanh = $this->modelNguoiDung->layDanhSachTinhThanh(); // Lọc động từ nông hộ thực tế
        $dsNongHo    = $this->modelNguoiDung->layDanhSachNongHo();

        require_once __DIR__ . '/../views/public/trang_chu.php';
    }

    /**
     * Xem chi tiết 1 sản phẩm
     */
    public function chiTiet() {
        $id = (int)($_GET['id'] ?? 0);
        $sanPham = $this->modelSanPham->chiTiet($id);

        if (!$sanPham || $sanPham['an'] == 1) {
            dat_thong_bao('error', 'Không tìm thấy', 'Nông sản này không tồn tại hoặc đã tạm dừng cung cấp.');
            chuyen_huong('index.php');
        }

        require_once __DIR__ . '/../views/public/chi_tiet.php';
    }

    /**
     * Đăng ký tài khoản bằng Số điện thoại (sdt)
     * Loại bỏ hoàn toàn trường tên đăng nhập (tai_khoan)
     */
    public function dangKy() {
        if (da_dang_nhap()) {
            chuyen_huong('index.php');
        }

        $loi = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sdt            = trim($_POST['sdt'] ?? '');
            $matKhau        = trim($_POST['mat_khau'] ?? '');
            $xacNhanMatKhau = trim($_POST['xac_nhan_mat_khau'] ?? '');
            $hoTen          = lam_sach($_POST['ho_ten'] ?? '');
            $vaiTro         = lam_sach($_POST['vai_tro'] ?? 'nguoi_mua');
            $tinh           = lam_sach($_POST['tinh'] ?? null);
            $diaChi         = lam_sach($_POST['dia_chi'] ?? null);

            // Kiểm tra tính hợp lệ của số điện thoại di động 10 chữ số Việt Nam
            if (empty($sdt)) {
                $loi = 'Vui lòng nhập số điện thoại của bạn.';
            } elseif (!kiem_tra_sdt_hop_le($sdt)) {
                $loi = 'Số điện thoại không đúng định dạng di động 10 chữ số của Việt Nam (vd: 0912345678).';
            } elseif (!kiem_tra_mat_khau_an_toan($matKhau)) {
                $loi = 'Mật khẩu phải từ 8 ký tự trở lên và có ít nhất 1 ký tự đặc biệt (@, #, $, %...).';
            } elseif ($matKhau !== $xacNhanMatKhau) {
                $loi = 'Mật khẩu xác nhận không trùng khớp với mật khẩu đã nhập!';
            } elseif (empty($hoTen)) {
                $loi = 'Vui lòng điền đầy đủ họ và tên hoặc tên nhà vườn / cơ sở.';
            } elseif (empty($tinh)) {
                $loi = 'Vui lòng cung cấp Tỉnh / Thành phố của bạn.';
            } elseif (empty($diaChi)) {
                $loi = ($vaiTro === 'nong_ho') 
                    ? 'Nông hộ bắt buộc phải khai báo Địa chỉ vườn / cơ sở sản xuất.' 
                    : 'Người mua vui lòng cung cấp địa chỉ nhận hàng chi tiết để thuận tiện giao dịch.';
            } else {
                // Kiểm tra tính duy nhất (UNIQUE) của số điện thoại
                $tonTai = $this->modelNguoiDung->timTheoSDT($sdt);
                if ($tonTai) {
                    $loi = 'Số điện thoại này đã được đăng ký tài khoản. Vui lòng đăng nhập hoặc liên hệ hỗ trợ.';
                } else {
                    $maMoi = $this->modelNguoiDung->dangKy($sdt, $matKhau, $hoTen, $vaiTro, $tinh, $diaChi);
                    if ($maMoi) {
                        if ($vaiTro === 'nong_ho') {
                            dat_thong_bao('info', 'Đăng ký thành công', 'Hồ sơ nông hộ của bạn đã được gửi. Vui lòng chờ Admin xét duyệt trước khi đăng bán sản phẩm.');
                        } else {
                            dat_thong_bao('success', 'Đăng ký thành công', 'Tài khoản người mua của bạn đã sẵn sàng. Hãy đăng nhập để bắt đầu mua sắm nông sản.');
                        }
                        chuyen_huong('index.php?trang=dang_nhap');
                    } else {
                        $loi = 'Lỗi hệ thống khi đăng ký. Vui lòng thử lại sau.';
                    }
                }
            }
        }

        require_once __DIR__ . '/../views/auth/dang_ky.php';
    }

    /**
     * Đăng nhập tài khoản bằng Số điện thoại + Mật khẩu
     */
    public function dangNhap() {
        if (da_dang_nhap()) {
            chuyen_huong('index.php');
        }

        $loi = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sdt     = trim($_POST['sdt'] ?? ($_POST['tai_khoan'] ?? ''));
            $matKhau = trim($_POST['mat_khau'] ?? '');

            if (empty($sdt) || empty($matKhau)) {
                $loi = 'Vui lòng nhập đầy đủ số điện thoại và mật khẩu.';
            } elseif (!kiem_tra_sdt_hop_le($sdt)) {
                $loi = 'Số điện thoại không đúng định dạng di động 10 chữ số của Việt Nam.';
            } else {
                $ketQua = $this->modelNguoiDung->dangNhap($sdt, $matKhau);
                if ($ketQua['thanh_cong']) {
                    $_SESSION['nguoi_dung'] = $ketQua['nguoi_dung'];
                    $_SESSION['phai_doi_mk'] = (int)($ketQua['nguoi_dung']['phai_doi_mk'] ?? 0);

                    // Nếu tài khoản đang có cờ buộc đổi mật khẩu tạm do Admin cấp
                    if ($ketQua['nguoi_dung']['phai_doi_mk'] == 1) {
                        dat_thong_bao('warning', 'Yêu cầu bảo mật', 'Bạn đang sử dụng mật khẩu tạm do Quản trị viên cấp. Vui lòng đổi mật khẩu mới để tiếp tục.');
                        chuyen_huong('index.php?trang=doi_mk_tam');
                    }

                    dat_thong_bao('success', 'Đăng nhập thành công', 'Chào mừng ' . $ketQua['nguoi_dung']['ho_ten'] . ' đã quay trở lại!');
                    
                    if ($ketQua['nguoi_dung']['vai_tro'] === 'admin') {
                        chuyen_huong('index.php?trang=admin_tong_quan');
                    } elseif ($ketQua['nguoi_dung']['vai_tro'] === 'nong_ho') {
                        chuyen_huong('index.php?trang=nong_ho_san_pham');
                    } else {
                        chuyen_huong('index.php');
                    }
                } else {
                    $loi = $ketQua['thong_bao'];
                }
            }
        }

        require_once __DIR__ . '/../views/auth/dang_nhap.php';
    }

    /**
     * Màn hình Bắt buộc đổi mật khẩu tạm do Admin cấp
     */
    public function doiMatKhauTam() {
        if (!da_dang_nhap()) {
            chuyen_huong('index.php?trang=dang_nhap');
        }

        $nguoiDung = nguoi_dung_hien_tai();
        $loi = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $matKhauMoi     = trim($_POST['mat_khau_moi'] ?? '');
            $xacNhanMatKhau = trim($_POST['xac_nhan_mat_khau'] ?? '');

            if (strlen($matKhauMoi) < 8 || !kiem_tra_mat_khau_an_toan($matKhauMoi)) {
                $loi = 'Mật khẩu mới phải từ 8 ký tự trở lên và chứa ít nhất 1 ký tự đặc biệt (@, #, $, %...).';
            } elseif ($matKhauMoi !== $xacNhanMatKhau) {
                $loi = 'Xác nhận mật khẩu mới không trùng khớp.';
            } else {
                if ($this->modelNguoiDung->doiMatKhau($nguoiDung['ma_nguoi_dung'], $matKhauMoi)) {
                    $_SESSION['nguoi_dung']['phai_doi_mk'] = 0;
                    $_SESSION['phai_doi_mk'] = 0;
                    dat_thong_bao('success', 'Đổi mật khẩu thành công', 'Mật khẩu mới của bạn đã được cập nhật an toàn.');
                    chuyen_huong('index.php');
                } else {
                    $loi = 'Có lỗi xảy ra khi đổi mật khẩu. Vui lòng thử lại.';
                }
            }
        }

        require_once __DIR__ . '/../views/auth/doi_mk_tam.php';
    }

    /**
     * Đăng xuất
     */
    public function dangXuat() {
        unset($_SESSION['nguoi_dung']);
        unset($_SESSION['phai_doi_mk']);
        dat_thong_bao('info', 'Đăng xuất', 'Bạn đã đăng xuất khỏi tài khoản thành công.');
        chuyen_huong('index.php');
    }
}

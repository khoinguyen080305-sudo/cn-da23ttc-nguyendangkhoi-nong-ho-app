<?php
// ==============================================================================
// CONTROLLER NGƯỜI MUA: controllers/NguoiMuaController.php
// CHỈ DÀNH CHO VAI TRÒ 'nguoi_mua': Giỏ hàng, Gửi đơn thương lượng, Nhu cầu thu mua
// ==============================================================================

class NguoiMuaController {
    private $modelNguoiDung;
    private $modelDanhMuc;
    private $modelSanPham;
    private $modelDonHang;
    private $modelNhuCau;

    public function __construct($models) {
        $this->modelNguoiDung = $models['nguoi_dung'];
        $this->modelDanhMuc   = $models['danh_muc'];
        $this->modelSanPham   = $models['san_pham'];
        $this->modelDonHang   = $models['don_hang'];
        $this->modelNhuCau    = $models['nhu_cau'];
    }

    /**
     * Thêm vào giỏ hàng (Hỗ trợ cả AJAX và POST form)
     */
    public function themGioHang() {
        $laAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
               || isset($_GET['ajax']) 
               || isset($_POST['ajax'])
               || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        if (!co_vai_tro('nguoi_mua')) {
            if ($laAjax) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'thanh_cong' => false, 
                    'thong_bao' => 'Tính năng giỏ hàng chỉ dành riêng cho Người Mua. Vui lòng đăng nhập tài khoản người mua.'
                ]);
                exit();
            }
            dat_thong_bao('warning', 'Yêu cầu đăng nhập', 'Vui lòng đăng nhập với vai trò Người Mua để sử dụng giỏ hàng.');
            chuyen_huong('index.php?trang=dang_nhap');
        }

        $maSanPham = (int)($_POST['ma_san_pham'] ?? 0);
        $soLuong   = (float)($_POST['so_luong'] ?? 1);
        $giaDeNghi = isset($_POST['gia_de_nghi']) && $_POST['gia_de_nghi'] !== '' ? (float)$_POST['gia_de_nghi'] : null;

        $sp = $this->modelSanPham->chiTiet($maSanPham);
        if (!$sp || $sp['an'] == 1) {
            if ($laAjax) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['thanh_cong' => false, 'thong_bao' => 'Nông sản không tồn tại hoặc đã tạm dừng bán.']);
                exit();
            }
            dat_thong_bao('error', 'Lỗi', 'Nông sản không tồn tại hoặc đã tạm dừng bán.');
            chuyen_huong('index.php');
        }

        // Kiểm tra tồn kho sẵn có
        $maxMua = quy_doi_don_vi($sp['san_luong'], $sp['don_vi_san_luong'], $sp['don_vi_gia']);
        if ($soLuong <= 0) {
            $thongBaoLoi = 'Số lượng đặt mua phải lớn hơn 0.';
            if ($laAjax) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['thanh_cong' => false, 'thong_bao' => $thongBaoLoi]);
                exit();
            }
            dat_thong_bao('error', 'Lỗi số lượng', $thongBaoLoi);
            chuyen_huong('index.php?trang=chi_tiet&id=' . $maSanPham);
        }
        if ($soLuong > $maxMua) {
            $thongBaoLoi = "Số lượng đặt mua (" . dinh_dang_so_luong($soLuong) . " " . $sp['don_vi_gia'] . ") vượt quá kho sẵn có (" . dinh_dang_so_luong($maxMua) . " " . $sp['don_vi_gia'] . ").";
            if ($laAjax) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['thanh_cong' => false, 'thong_bao' => $thongBaoLoi]);
                exit();
            }
            dat_thong_bao('error', 'Vượt quá tồn kho', $thongBaoLoi);
            chuyen_huong('index.php?trang=chi_tiet&id=' . $maSanPham);
        }

        // Nếu sản phẩm đã có trong giỏ, cập nhật lại số lượng hợp lệ thay vì cộng dồn vượt quá kho
        $soLuongHienTai = isset($_SESSION['gio_hang'][$maSanPham]['so_luong']) ? (float)$_SESSION['gio_hang'][$maSanPham]['so_luong'] : 0;
        if ($soLuongHienTai > 0 && ($soLuongHienTai + $soLuong) > $maxMua) {
            $this->modelDonHang->capNhatGioHang($maSanPham, $soLuong, $giaDeNghi);
            $soMon = $this->modelDonHang->demMonGioHang();
        } else {
            $soMon = $this->modelDonHang->themGioHang($maSanPham, $soLuong, $giaDeNghi);
        }

        if ($laAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'thanh_cong'   => true, 
                'thong_bao'    => 'Đã thêm ' . $sp['ten'] . ' vào giỏ hàng thành công.',
                'so_luong_gio' => $soMon
            ]);
            exit();
        }

        dat_thong_bao('success', 'Giỏ hàng', 'Đã thêm nông sản vào giỏ hàng thành công.');
        chuyen_huong('index.php?trang=gio_hang');
    }

    /**
     * Xem giỏ hàng
     */
    public function xemGioHang() {
        yeu_cau_vai_tro('nguoi_mua');

        $dsGioHang = $this->modelDonHang->layChiTietGioHang($this->modelSanPham);
        require_once __DIR__ . '/../views/nguoi_mua/gio_hang.php';
    }

    /**
     * Cập nhật số lượng / giá đề xuất trong giỏ
     */
    public function capNhatGioHang() {
        yeu_cau_vai_tro('nguoi_mua');

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $maSanPham = (int)($_POST['ma_san_pham'] ?? 0);
            $soLuong   = (float)($_POST['so_luong'] ?? 0);
            $giaDeNghi = isset($_POST['gia_de_nghi']) && $_POST['gia_de_nghi'] !== '' ? (float)$_POST['gia_de_nghi'] : null;

            // Kiểm tra tồn kho của nông sản
            $sp = $this->modelSanPham->chiTiet($maSanPham);
            if ($sp) {
                $maxMua = quy_doi_don_vi($sp['san_luong'], $sp['don_vi_san_luong'], $sp['don_vi_gia']);
                if ($soLuong > $maxMua) {
                    dat_thong_bao('error', 'Vượt quá tồn kho', "Nông sản \"{$sp['ten']}\" chỉ còn tối đa " . dinh_dang_so_luong($maxMua) . " {$sp['don_vi_gia']}. Vui lòng giảm số lượng.");
                    chuyen_huong('index.php?trang=gio_hang');
                }
            }

            $this->modelDonHang->capNhatGioHang($maSanPham, $soLuong, $giaDeNghi);
            dat_thong_bao('success', 'Giỏ hàng', 'Đã cập nhật số lượng nông sản thành công.');
        }

        chuyen_huong('index.php?trang=gio_hang');
    }

    /**
     * Xóa 1 món khỏi giỏ
     */
    public function xoaKhoiGioHang() {
        yeu_cau_vai_tro('nguoi_mua');

        $maSanPham = (int)($_GET['id'] ?? 0);
        $this->modelDonHang->xoaKhoiGioHang($maSanPham);
        dat_thong_bao('info', 'Giỏ hàng', 'Đã bỏ sản phẩm khỏi giỏ hàng.');
        chuyen_huong('index.php?trang=gio_hang');
    }

    /**
     * Gửi toàn bộ đơn đặt hàng từ giỏ hàng đến các Nông Hộ
     */
    public function guiDonHang() {
        yeu_cau_vai_tro('nguoi_mua');

        $nguoiDung = nguoi_dung_hien_tai();
        $dsGioHang = $this->modelDonHang->layChiTietGioHang($this->modelSanPham);

        if (empty($dsGioHang)) {
            dat_thong_bao('warning', 'Giỏ hàng trống', 'Giỏ hàng của bạn đang trống, vui lòng chọn nông sản để đặt mua.');
            chuyen_huong('index.php');
        }

        // Kiểm tra toàn bộ giỏ hàng: Chặn gửi đơn nếu có sản phẩm vượt quá kho sẵn có
        foreach ($dsGioHang as $sp) {
            $maxMua = quy_doi_don_vi($sp['san_luong'], $sp['don_vi_san_luong'], $sp['don_vi_gia']);
            if ($sp['so_luong_dat'] > $maxMua) {
                dat_thong_bao('error', 'Kho không đủ số lượng', "Nông sản \"{$sp['ten']}\" đặt " . dinh_dang_so_luong($sp['so_luong_dat']) . " {$sp['don_vi_gia']}, vượt quá kho sẵn có (" . dinh_dang_so_luong($maxMua) . " {$sp['don_vi_gia']}). Vui lòng giảm số lượng.");
                chuyen_huong('index.php?trang=gio_hang');
            }
        }

        $soLuongTao = 0;
        foreach ($dsGioHang as $sp) {
            $soLuong   = $sp['so_luong_dat'];
            $donVi     = $sp['don_vi_gia']; // Phương án 3: Người mua đặt hàng theo đơn vị tính giá
            $donViGia  = $sp['don_vi_gia'];
            $giaDeNghi = $sp['gia_ap_dung'];
            $ghiChu    = lam_sach($_POST['ghi_chu_' . $sp['ma_san_pham']] ?? 'Đặt từ sàn Nông Hộ');

            if ($giaDeNghi === null || $giaDeNghi <= 0) {
                // Nếu sản phẩm không có giá niêm yết mà người mua không nhập giá thì mặc định 0 hoặc bỏ qua
                $giaDeNghi = 0;
            }

            $maDon = $this->modelDonHang->taoDonHang(
                $nguoiDung['ma_nguoi_dung'], 
                $sp['ma_san_pham'], 
                $soLuong, 
                $donVi, 
                $giaDeNghi, 
                $donViGia, 
                $ghiChu
            );

            if ($maDon) $soLuongTao++;
        }

        // Xóa sạch giỏ hàng sau khi gửi
        $this->modelDonHang->xoaSachGioHang();

        dat_thong_bao('success', 'Gửi đơn thành công', "Đã gửi thành công $soLuongTao yêu cầu thu mua đến các hộ nông dân.");
        chuyen_huong('index.php?trang=don_hang');
    }

    /**
     * Quản lý các tin Nhu Cầu Thu Mua của tôi
     */
    public function nhuCauCuaToi() {
        yeu_cau_vai_tro('nguoi_mua');

        $nguoiDung  = nguoi_dung_hien_tai();
        $dsDanhMuc  = $this->modelDanhMuc->layTatCa();
        $dsNhuCau   = $this->modelNhuCau->layTheoNguoiMua($nguoiDung['ma_nguoi_dung']);

        // Xử lý thêm tin nhu cầu mới
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $maDanhMuc  = (int)($_POST['ma_danh_muc'] ?? 0);
            $tenNongSan = lam_sach($_POST['ten_nong_san'] ?? '');
            $soLuong    = (float)($_POST['so_luong'] ?? 0);
            $donVi      = lam_sach($_POST['don_vi'] ?? 'kg');
            $giaToiDa   = !empty($_POST['gia_toi_da']) ? (float)$_POST['gia_toi_da'] : null;
            $tinh       = lam_sach($_POST['tinh'] ?? '');
            $hanCan     = lam_sach($_POST['han_can'] ?? '');

            if ($maDanhMuc <= 0 || empty($tenNongSan) || $soLuong <= 0 || empty($tinh) || empty($hanCan)) {
                dat_thong_bao('error', 'Lỗi nhập liệu', 'Vui lòng chọn ngành nông sản, ghi rõ tên loại trái cây/nông sản cần mua, số lượng, tỉnh nhận hàng và thời hạn.');
            } else {
                if ($this->modelNhuCau->them($nguoiDung['ma_nguoi_dung'], $maDanhMuc, $tenNongSan, $soLuong, $donVi, $giaToiDa, $tinh, $hanCan)) {
                    dat_thong_bao('success', 'Thành công', 'Đã đăng tin nhu cầu thu mua lên thị trường.');
                    chuyen_huong('index.php?trang=nhu_cau_cua_toi');
                } else {
                    dat_thong_bao('error', 'Lỗi', 'Có lỗi khi lưu tin nhu cầu.');
                }
            }
        }

        require_once __DIR__ . '/../views/nguoi_mua/nhu_cau_cua_toi.php';
    }

    /**
     * Xóa tin nhu cầu
     */
    public function xoaNhuCau() {
        yeu_cau_vai_tro('nguoi_mua');
        $nguoiDung = nguoi_dung_hien_tai();
        $maNhuCau  = (int)($_GET['id'] ?? 0);

        if ($this->modelNhuCau->xoa($maNhuCau, $nguoiDung['ma_nguoi_dung'])) {
            dat_thong_bao('info', 'Đã xóa', 'Đã xóa tin nhu cầu thu mua.');
        }
        chuyen_huong('index.php?trang=nhu_cau_cua_toi');
    }

    /**
     * Cập nhật thông tin và địa chỉ nhận hàng của Người Mua
     */
    public function hoSo() {
        yeu_cau_vai_tro('nguoi_mua');
        $nguoiDung = nguoi_dung_hien_tai();
        $loi = '';

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $hoTen  = lam_sach($_POST['ho_ten'] ?? '');
            $tinh   = lam_sach($_POST['tinh'] ?? '');
            $diaChi = lam_sach($_POST['dia_chi'] ?? '');

            if (empty($hoTen) || empty($diaChi)) {
                $loi = 'Vui lòng điền đầy đủ họ tên/đơn vị thu mua và địa chỉ nhận hàng.';
            } else {
                if ($this->modelNguoiDung->capNhatHoSo($nguoiDung['ma_nguoi_dung'], $hoTen, $tinh, $diaChi)) {
                    // Cập nhật lại session người dùng
                    $_SESSION['nguoi_dung']['ho_ten']  = $hoTen;
                    $_SESSION['nguoi_dung']['tinh']    = $tinh;
                    $_SESSION['nguoi_dung']['dia_chi'] = $diaChi;

                    dat_thong_bao('success', 'Thành công', 'Thông tin và địa chỉ nhận hàng của bạn đã được cập nhật thành công.');
                    chuyen_huong('index.php?trang=nguoi_mua_ho_so&tab=thong_tin');
                } else {
                    $loi = 'Không thể cập nhật thông tin vào cơ sở dữ liệu. Vui lòng thử lại.';
                }
            }
        }

        $thoiGian   = lam_sach($_GET['thoi_gian'] ?? 'thang_nay');
        $tabHienTai = lam_sach($_GET['tab'] ?? 'thong_ke');
        $thongKe    = $this->modelDonHang->layThongKeNguoiMua($nguoiDung['ma_nguoi_dung'], $thoiGian);

        require_once __DIR__ . '/../views/nguoi_mua/ho_so.php';
    }
}

<?php
// ==============================================================================
// CONTROLLER NÔNG HỘ: controllers/NongHoController.php
// Quản lý nông sản vườn, Thêm/Sửa (tự động xóa ảnh cũ), Xem gợi ý ghép nối %
// ==============================================================================

class NongHoController {
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
     * Danh sách sản phẩm của Nông Hộ
     */
    public function danhSachSanPham() {
        yeu_cau_vai_tro('nong_ho');
        $nguoiDung  = nguoi_dung_hien_tai();
        $dsSanPham  = $this->modelSanPham->layTheoNongHo($nguoiDung['ma_nguoi_dung']);

        require_once __DIR__ . '/../views/nong_ho/san_pham.php';
    }

    /**
     * Form Thêm mới hoặc Chỉnh sửa nông sản
     * CÓ CƠ CHẾ: TỰ ĐỘNG XÓA FILE ẢNH CŨ KHỎI THƯ MỤC LƯU TRỮ VÀ CSDL KHI ĐỔI ẢNH MỚI
     */
    public function formSanPham() {
        yeu_cau_vai_tro('nong_ho');
        $nguoiDung = nguoi_dung_hien_tai();

        // Nông hộ chưa được duyệt thì không được thêm sản phẩm
        if ($nguoiDung['trang_thai'] === 'cho_duyet') {
            dat_thong_bao('warning', 'Hồ sơ chờ duyệt', 'Hồ sơ của bạn đang chờ Admin phê duyệt. Bạn chưa thể đăng bán nông sản vào lúc này.');
            chuyen_huong('index.php?trang=nong_ho_san_pham');
        }

        $id        = (int)($_GET['id'] ?? 0);
        $sanPham   = null;
        $dsDanhMuc = $this->modelDanhMuc->layTatCa();
        $loi       = '';

        if ($id > 0) {
            $sanPham = $this->modelSanPham->chiTiet($id);
            if (!$sanPham || $sanPham['ma_nong_ho'] != $nguoiDung['ma_nguoi_dung']) {
                dat_thong_bao('error', 'Lỗi', 'Không tìm thấy nông sản cần chỉnh sửa.');
                chuyen_huong('index.php?trang=nong_ho_san_pham');
            }
        }

        // Xử lý gửi form
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $maDanhMuc       = (int)($_POST['ma_danh_muc'] ?? 0);
            $tenDanhMucMoi   = lam_sach($_POST['ten_danh_muc_moi'] ?? '');

            // Nếu nông dân chọn "+ Thêm ngành nông sản mới..." hoặc có nhập tên ngành mới
            if ($maDanhMuc === -1 || (!empty($tenDanhMucMoi) && $maDanhMuc <= 0)) {
                if (!empty($tenDanhMucMoi)) {
                    $maDanhMuc = $this->modelDanhMuc->layHoacThemMoi($tenDanhMucMoi);
                }
            }

            $ten             = lam_sach($_POST['ten'] ?? '');
            $sanLuong        = (float)($_POST['san_luong'] ?? 0);
            $donViSanLuong   = lam_sach($_POST['don_vi_san_luong'] ?? 'kg');
            
            // Giá bán: Cho phép rỗng nếu tick "Liên hệ báo giá"
            $laLienHe        = isset($_POST['la_lien_he']) && $_POST['la_lien_he'] == '1';
            $gia             = $laLienHe ? null : (!empty($_POST['gia']) ? (float)$_POST['gia'] : null);
            $donViGia        = lam_sach($_POST['don_vi_gia'] ?? 'kg');

            $tinhTrang       = lam_sach($_POST['tinh_trang'] ?? 'con_hang');
            if (!array_key_exists($tinhTrang, TINH_TRANG_SP)) {
                $tinhTrang = 'con_hang';
            }
            $ngayThuHoach    = ($tinhTrang === 'sap_thu_hoach' && !empty($_POST['ngay_thu_hoach'])) ? $_POST['ngay_thu_hoach'] : null;
            
            // Chứng nhận (SET nhiều ô tick)
            $chungNhanArr    = $_POST['chung_nhan'] ?? [];
            $chungNhan       = !empty($chungNhanArr) ? implode(',', $chungNhanArr) : null;

            // Xử lý tải ảnh lên (Hỗ trợ upload tối đa 5 ảnh & tự động dọn dẹp ảnh cũ)
            $anhCuGiuLai = [];
            if ($id > 0 && isset($_POST['anh_cu_giu_lai']) && is_array($_POST['anh_cu_giu_lai'])) {
                foreach ($_POST['anh_cu_giu_lai'] as $ac) {
                    $tenAc = basename(lam_sach($ac));
                    if (!empty($tenAc) && !in_array($tenAc, $anhCuGiuLai)) {
                        $anhCuGiuLai[] = $tenAc;
                    }
                }
            }

            $anhMoiArr = [];
            $thuMucLuu = __DIR__ . '/../assets/images/products/';
            if (!is_dir($thuMucLuu)) {
                mkdir($thuMucLuu, 0777, true);
            }
            $cacDuoiChoPhep = ['jpg', 'jpeg', 'png', 'webp'];
            $maxSizeBytes = 5 * 1024 * 1024; // 5 MB

            if (isset($_FILES['anh']) && !empty($_FILES['anh']['name'])) {
                $filesData = [];
                if (is_array($_FILES['anh']['name'])) {
                    $soLuong = count($_FILES['anh']['name']);
                    for ($i = 0; $i < $soLuong; $i++) {
                        if (!empty($_FILES['anh']['name'][$i])) {
                            $filesData[] = [
                                'name'     => $_FILES['anh']['name'][$i],
                                'type'     => $_FILES['anh']['type'][$i] ?? '',
                                'tmp_name' => $_FILES['anh']['tmp_name'][$i] ?? '',
                                'error'    => $_FILES['anh']['error'][$i] ?? UPLOAD_ERR_NO_FILE,
                                'size'     => $_FILES['anh']['size'][$i] ?? 0,
                            ];
                        }
                    }
                } elseif (!empty($_FILES['anh']['name'])) {
                    $filesData[] = $_FILES['anh'];
                }

                // Kiểm tra giới hạn tổng số lượng ảnh: tối đa 5 ảnh
                if (count($anhCuGiuLai) + count($filesData) > 5) {
                    $loi = 'Mỗi sản phẩm chỉ được có tối đa 5 hình ảnh (Hiện có: ' . count($anhCuGiuLai) . ' ảnh cũ và ' . count($filesData) . ' ảnh mới chọn). Vui lòng chọn bớt ảnh.';
                } else {
                    foreach ($filesData as $idx => $tep) {
                        if ($tep['error'] === UPLOAD_ERR_OK) {
                            $duoiTep = strtolower(pathinfo($tep['name'], PATHINFO_EXTENSION));
                            if (!in_array($duoiTep, $cacDuoiChoPhep)) {
                                $loi = 'Tệp "' . htmlspecialchars($tep['name']) . '" không đúng định dạng. Chỉ hỗ trợ ảnh JPG, PNG hoặc WEBP.';
                                break;
                            }
                            if ($tep['size'] > $maxSizeBytes) {
                                $loi = 'Tệp "' . htmlspecialchars($tep['name']) . '" quá lớn (vượt quá dung lượng 5MB).';
                                break;
                            }

                            // Đặt tên tệp duy nhất, an toàn
                            $tenFileMoi = 'sp_' . $nguoiDung['ma_nguoi_dung'] . '_' . time() . '_' . ($idx + 1) . '_' . substr(md5(uniqid()), 0, 4) . '.' . $duoiTep;
                            $duongDanDich = $thuMucLuu . $tenFileMoi;

                            if (move_uploaded_file($tep['tmp_name'], $duongDanDich)) {
                                $anhMoiArr[] = $tenFileMoi;
                            } else {
                                $loi = 'Không thể lưu tệp ảnh "' . htmlspecialchars($tep['name']) . '" lên máy chủ. Vui lòng thử lại.';
                                break;
                            }
                        } elseif ($tep['error'] !== UPLOAD_ERR_NO_FILE) {
                            $loi = 'Tải ảnh "' . htmlspecialchars($tep['name']) . '" thất bại (Mã lỗi: ' . $tep['error'] . ').';
                            break;
                        }
                    }
                }
            }

            // Nếu gặp lỗi validation, xóa ngay các tệp vừa upload lên để tránh rác ổ cứng
            if (!empty($loi) && !empty($anhMoiArr)) {
                foreach ($anhMoiArr as $fXoa) {
                    xoa_file_anh_cu($fXoa);
                }
                $anhMoiArr = [];
            }

            if (empty($ten) || $maDanhMuc <= 0 || $sanLuong <= 0) {
                if ($maDanhMuc <= 0) {
                    $loi = 'Vui lòng chọn hoặc nhập tên ngành nông sản.';
                } else {
                    $loi = 'Vui lòng nhập đầy đủ tên nông sản, ngành nông sản và sản lượng sẵn có trong kho.';
                }
            }

            if (empty($loi)) {
                // Hợp nhất ảnh cũ giữ lại và ảnh mới tải lên, cắt đúng tối đa 5 ảnh
                $tongAnh = array_merge($anhCuGiuLai, $anhMoiArr);
                $tongAnh = array_slice($tongAnh, 0, 5);

                $anhChinh = !empty($tongAnh) ? $tongAnh[0] : null;
                $danhSachAnhJson = !empty($tongAnh) ? json_encode(array_values($tongAnh), JSON_UNESCAPED_UNICODE) : null;

                if ($id > 0) {
                    // Dọn dẹp ổ cứng: xóa các file ảnh cũ đã bị nông hộ bấm gỡ bỏ
                    $dsAnhCuGoc = lay_danh_sach_anh_sp($sanPham);
                    foreach ($dsAnhCuGoc as $fCu) {
                        if (!in_array($fCu, $tongAnh)) {
                            xoa_file_anh_cu($fCu);
                        }
                    }

                    if ($this->modelSanPham->sua($id, $nguoiDung['ma_nguoi_dung'], $maDanhMuc, $ten, $sanLuong, $donViSanLuong, $gia, $donViGia, $anhChinh, $tinhTrang, $ngayThuHoach, $chungNhan, $danhSachAnhJson)) {
                        dat_thong_bao('success', 'Thành công', 'Đã cập nhật thông tin nông sản thành công.');
                        chuyen_huong('index.php?trang=nong_ho_san_pham');
                    } else {
                        $loi = 'Lỗi cập nhật nông sản.';
                    }
                } else {
                    // Thêm mới
                    if ($this->modelSanPham->them($nguoiDung['ma_nguoi_dung'], $maDanhMuc, $ten, $sanLuong, $donViSanLuong, $gia, $donViGia, $anhChinh, $tinhTrang, $ngayThuHoach, $chungNhan, $danhSachAnhJson)) {
                        dat_thong_bao('success', 'Thành công', 'Đã thêm nông sản mới lên sàn thành công.');
                        chuyen_huong('index.php?trang=nong_ho_san_pham');
                    } else {
                        $loi = 'Lỗi lưu nông sản mới.';
                    }
                }
            }
        }

        require_once __DIR__ . '/../views/nong_ho/form_san_pham.php';
    }

    /**
     * Ẩn / Hiện sản phẩm
     */
    public function anHienSanPham() {
        yeu_cau_vai_tro('nong_ho');
        $nguoiDung   = nguoi_dung_hien_tai();
        $id          = (int)($_GET['id'] ?? 0);
        $trangThaiAn = (int)($_GET['an'] ?? 1);

        $this->modelSanPham->anHien($id, $nguoiDung['ma_nguoi_dung'], $trangThaiAn);
        $thongBao = ($trangThaiAn == 1) ? 'Đã ẩn nông sản khỏi sàn công khai.' : 'Đã hiện nông sản trở lại sàn.';
        dat_thong_bao('info', 'Trạng thái hiển thị', $thongBao);
        chuyen_huong('index.php?trang=nong_ho_san_pham');
    }

    /**
     * Nhu cầu thị trường & Gợi ý ghép nối % Matching
     */
    public function nhuCauThiTruong() {
        yeu_cau_vai_tro('nong_ho');
        $nguoiDung = nguoi_dung_hien_tai();

        // Tính tỷ lệ ghép nối Matching % cho nông hộ này
        $dsGhepNoi = $this->modelNhuCau->tinhGhepNoiChoNongHo($nguoiDung['ma_nguoi_dung'], $nguoiDung['tinh']);
        
        // Toàn bộ nhu cầu thị trường chung
        $dsThiTruong = $this->modelNhuCau->layDanhSachThiTruong();

        require_once __DIR__ . '/../views/nong_ho/nhu_cau_thi_truong.php';
    }

    /**
     * Cập nhật hồ sơ nhà vườn / nông hộ
     */
    public function hoSo() {
        yeu_cau_vai_tro('nong_ho');
        $nguoiDung = nguoi_dung_hien_tai();
        $loi = '';

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $hoTen  = lam_sach($_POST['ho_ten'] ?? '');
            $tinh   = lam_sach($_POST['tinh'] ?? '');
            $diaChi = lam_sach($_POST['dia_chi'] ?? '');

            if (empty($hoTen) || empty($tinh) || empty($diaChi)) {
                $loi = 'Vui lòng điền đầy đủ tên nhà vườn/hợp tác xã, tỉnh thành và địa chỉ vườn.';
            } else {
                if ($this->modelNguoiDung->capNhatHoSo($nguoiDung['ma_nguoi_dung'], $hoTen, $tinh, $diaChi)) {
                    // Cập nhật lại session người dùng
                    $_SESSION['nguoi_dung']['ho_ten']  = $hoTen;
                    $_SESSION['nguoi_dung']['tinh']    = $tinh;
                    $_SESSION['nguoi_dung']['dia_chi'] = $diaChi;

                    dat_thong_bao('success', 'Thành công', 'Hồ sơ nhà vườn của bạn đã được cập nhật thành công.');
                    chuyen_huong('index.php?trang=nong_ho_ho_so&tab=thong_tin');
                } else {
                    $loi = 'Không thể cập nhật hồ sơ vào cơ sở dữ liệu. Vui lòng thử lại.';
                }
            }
        }

        $thoiGian   = lam_sach($_GET['thoi_gian'] ?? 'thang_nay');
        $tabHienTai = lam_sach($_GET['tab'] ?? 'thong_ke');
        $thongKe    = $this->modelDonHang->layThongKeNongHo($nguoiDung['ma_nguoi_dung'], $thoiGian);

        require_once __DIR__ . '/../views/nong_ho/ho_so.php';
    }
}

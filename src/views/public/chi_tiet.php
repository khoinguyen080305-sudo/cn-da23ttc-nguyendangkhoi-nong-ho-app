<?php
// views/public/chi_tiet.php
// Khởi tạo giá trị mặc định để IDE không báo lỗi đỏ biến chưa định nghĩa
$sanPham = $sanPham ?? [];
if (empty($sanPham)) {
    chuyen_huong('index.php');
}

require_once __DIR__ . '/../layouts/header.php';
$nguoiDung = nguoi_dung_hien_tai();
$anhHienThi = duong_dan_anh_sp($sanPham['anh'] ?? '');
$dsAnhChiTiet = lay_danh_sach_anh_sp($sanPham);
$dsAnhUrls = array_map('duong_dan_anh_sp', $dsAnhChiTiet);
$coNhieuAnh = count($dsAnhUrls) > 1;
$maxMua = quy_doi_don_vi($sanPham['san_luong'], $sanPham['don_vi_san_luong'], $sanPham['don_vi_gia']);
$khacDonVi = (mb_strtolower(trim($sanPham['don_vi_san_luong']), 'UTF-8') !== mb_strtolower(trim($sanPham['don_vi_gia']), 'UTF-8'));
$giaGoc = ($sanPham['gia'] !== null && $sanPham['gia'] > 0) ? (float) $sanPham['gia'] : 0;

// Xử lý chuẩn hóa địa chỉ nông hộ tránh lặp từ (ví dụ: "Vĩnh Long, xã Bình Phú, Vĩnh Long")
$tinhNongHo = trim($sanPham['tinh_nong_ho'] ?? '');
$diaChi = trim($sanPham['dia_chi'] ?? '');
$diaChiHienThi = '';
if (!empty($diaChi) && !empty($tinhNongHo)) {
    if (mb_stripos($diaChi, $tinhNongHo, 0, 'UTF-8') !== false) {
        $diaChiHienThi = $diaChi;
    } else {
        $diaChiHienThi = $diaChi . ', ' . $tinhNongHo;
    }
} else {
    $diaChiHienThi = !empty($diaChi) ? $diaChi : $tinhNongHo;
}
?>

<div class="container py-3 py-lg-4" style="max-width: 1040px;">
    <!-- THANH ĐIỀU HƯỚNG QUAY LẠI & BREADCRUMB -->
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <a href="javascript:history.back()"
            class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 fw-bold shadow-sm text-secondary text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i>Quay lại
        </a>
    </div>

    <!-- THẺ CHÍNH CHI TIẾT SẢN PHẨM: ĐƠN GIẢN, TRỰC QUAN, DỄ HIỂU -->
    <div class="nh-detail-card-unified p-3 p-md-4">
        <div class="row g-4 align-items-start">

            <!-- CỘT TRÁI (5 CỘT): ẢNH NÔNG SẢN & CAM KẾT CHẤT LƯỢNG -->
            <div class="col-lg-5 col-md-5">
                <!-- KHUNG ẢNH NÔNG SẢN TOÀN DIỆN (HIỂN THỊ TRỌN VẸN 100% ẢNH, KHÔNG BỊ CẮT XÉN) -->
                <div class="nh-detail-img-box" id="nhDetailImgBox">
                    <!-- NỀN MỜ NGHỆ THUẬT LẤY TỪ CHÍNH ẢNH (TỰ ĐỘNG THÍCH ỨNG CẢ ẢNH DỌC LẪN ẢNH NGANG) -->
                    <div class="nh-detail-img-backdrop" id="imgDetailBackdrop" style="background-image: url('<?= $anhHienThi ?>');"></div>

                    <!-- ẢNH CHÍNH HIỂN THỊ TOÀN DIỆN TRỌN VẸN NÔNG SẢN -->
                    <img src="<?= $anhHienThi ?>" alt="<?= htmlspecialchars($sanPham['ten']) ?>"
                        id="imgDetailMain"
                        class="nh-detail-img-main" onerror="this.src='assets/images/nong_san_mac_dinh.svg'"
                        onclick="moXemAnhHienTai()">

                    <?php if ($coNhieuAnh): ?>
                        <!-- NÚT CHUYỂN ẢNH HAI BÊN (LUCIAN UX/UI FROSTED GLASS BUTTONS) -->
                        <button type="button" class="nh-gallery-nav-btn nh-gallery-prev"
                            onclick="chuyenAnhLienKe(-1)"
                            aria-label="Xem ảnh trước"
                            title="Ảnh trước (Phím mũi tên trái)">
                            <i class="bi bi-chevron-left"></i>
                        </button>

                        <button type="button" class="nh-gallery-nav-btn nh-gallery-next"
                            onclick="chuyenAnhLienKe(1)"
                            aria-label="Xem ảnh tiếp theo"
                            title="Ảnh tiếp theo (Phím mũi tên phải)">
                            <i class="bi bi-chevron-right"></i>
                        </button>

                        <!-- CHỈ SỐ ĐẾM ẢNH DẠNG VIÊN THUỐC KÍNH (LUCIAN UX/UI BADGE) -->
                        <div class="nh-gallery-counter-pill" id="galleryCounterPill" title="Ảnh hiện tại / Tổng số ảnh">
                            <span id="galleryCurrentIdx">1</span> / <span><?= count($dsAnhUrls) ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- NÚT XEM TOÀN MÀN HÌNH / PHÓNG TO GÓC DƯỚI PHẢI -->
                    <button type="button" class="nh-btn-zoom-img"
                        onclick="moXemAnhHienTai()"
                        title="Nhấp để xem ảnh toàn diện">
                        <i class="bi bi-arrows-fullscreen"></i>
                    </button>

                    <!-- TEM CHỨNG NHẬN GÓC TRÊN TRÁI -->
                    <?php $dsCnChiTiet = dinh_dang_chung_nhan($sanPham['chung_nhan'] ?? ''); ?>
                    <?php if (!empty($dsCnChiTiet)): ?>
                        <div class="nh-detail-certs-wrap">
                            <?php foreach ($dsCnChiTiet as $cn): ?>
                                <span class="nh-badge-glass-cert <?= $cn['badge_class'] ?>"
                                    title="Chứng nhận <?= htmlspecialchars($cn['ten']) ?>">
                                    <i class="bi <?= $cn['icon'] ?>"></i> <?= htmlspecialchars($cn['ten']) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- TRẠNG THÁI GÓC TRÊN PHẢI -->
                    <?php
                    $ttChiTiet = dinh_dang_tinh_trang_sp($sanPham['tinh_trang'] ?? 'con_hang', $sanPham['ngay_thu_hoach'] ?? null);
                    ?>
                    <span class="nh-detail-status-badge <?= htmlspecialchars($ttChiTiet['tinh_trang_hien_tai']) ?>">
                        <i
                            class="bi <?= $ttChiTiet['icon'] ?> me-1"></i><?= htmlspecialchars($ttChiTiet['nhan_kem_ngay']) ?>
                    </span>
                </div>

                <!-- DẢI ẢNH THU NHỎ (THUMBNAILS GALLERY CHO TỐI ĐA 5 ẢNH) -->
                <?php if ($coNhieuAnh): ?>
                <div class="nh-detail-thumbs-wrap mt-2.5 d-flex align-items-center gap-2 overflow-x-auto py-1" id="nhDetailThumbsWrap">
                    <?php foreach ($dsAnhUrls as $idx => $duongDanThumb): ?>
                        <button type="button" 
                            class="nh-detail-thumb-item <?= $idx === 0 ? 'active' : '' ?>"
                            onclick="chuyenAnhTheoChiSo(<?= $idx ?>)"
                            data-index="<?= $idx ?>"
                            title="Xem ảnh <?= $idx + 1 ?>">
                            <img src="<?= $duongDanThumb ?>" alt="Ảnh <?= $idx + 1 ?>" onerror="this.src='assets/images/nong_san_mac_dinh.svg'">
                        </button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- CỘT PHẢI (7 CỘT): TIÊU ĐỀ, BẢNG GIÁ, ĐẶT MUA & THÔNG TIN NHÀ VƯỜN -->
            <div class="col-lg-7 col-md-7 ps-lg-2">
                <!-- DANH MỤC & ĐỊA ĐIỂM XUẤT XỨ -->
                <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                    <span
                        class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-bold"
                        style="font-size: 0.8rem;">
                        <?= htmlspecialchars($sanPham['ten_danh_muc']) ?>
                    </span>
                    <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill fw-semibold"
                        style="font-size: 0.8rem;">
                        <i
                            class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($sanPham['tinh_nong_ho']) ?>
                    </span>
                </div>

                <!-- TÊN NÔNG SẢN TO RÕ -->
                <h1 class="fw-bold mb-3" style="font-size: 1.85rem; color: #064e3b; line-height: 1.25;">
                    <?= htmlspecialchars($sanPham['ten']) ?>
                </h1>

                <!-- BẢNG GIÁ & KHO SẠCH SẼ, NỔI BẬT ĐIỂM CHÍNH -->
                <div class="nh-detail-price-box mb-3">
                    <div class="row g-2 align-items-center">
                        <!-- GIÁ BÁN NIÊM YẾT -->
                        <div class="col-6 border-end-sm">
                            <span class="text-uppercase small fw-bold d-block mb-1"
                                style="font-size: 0.72rem; letter-spacing: 0.5px; color: #047857;">
                                Giá niêm yết tại vườn
                            </span>
                            <?php if ($sanPham['gia'] !== null && $sanPham['gia'] > 0): ?>
                                <div class="d-flex align-items-baseline gap-1 flex-wrap">
                                    <span class="nh-detail-price-val">
                                        <?= dinh_dang_tien($sanPham['gia']) ?>
                                    </span>
                                    <span class="text-secondary fw-bold small">/
                                        <?= htmlspecialchars($sanPham['don_vi_gia']) ?></span>
                                </div>
                            <?php else: ?>
                                <span
                                    class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 small fw-bold rounded-pill">
                                    Liên hệ báo giá
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- SẢN LƯỢNG KHO (TỰ ĐỘNG THÍCH ỨNG: < 1 TẤN HIỆN KG) -->
                        <div class="col-6 ps-2 ps-sm-3">
                            <span class="text-uppercase small fw-bold text-muted d-block mb-1"
                                style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                Sản lượng kho
                            </span>
                            <?php $khoTM = tach_kho_thong_minh($sanPham['san_luong'], $sanPham['don_vi_san_luong']); ?>
                            <div class="nh-detail-stock-val">
                                <?= $khoTM['so_luong'] ?> <span
                                    class="fs-6 fw-semibold text-muted"><?= htmlspecialchars($khoTM['don_vi']) ?></span>
                            </div>
                            <?php if ($khacDonVi && mb_strtolower(trim($khoTM['don_vi']), 'UTF-8') !== mb_strtolower(trim($sanPham['don_vi_gia']), 'UTF-8')): ?>
                                <span class="text-muted small fw-normal d-block" style="font-size: 0.8rem;">(~
                                    <?= dinh_dang_so_luong($maxMua) ?>
                                    <?= htmlspecialchars($sanPham['don_vi_gia']) ?>)</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (($sanPham['tinh_trang'] ?? '') === 'sap_thu_hoach' && !empty($sanPham['ngay_thu_hoach'])): ?>
                        <div class="mt-2.5 pt-2 border-top border-success-subtle d-flex align-items-center">
                            <span class="nh-harvest-notice">
                                <i class="bi bi-clock-history text-warning"></i>
                                <span>Dự kiến thu hoạch:
                                    <strong><?= date('d/m/Y', strtotime($sanPham['ngay_thu_hoach'])) ?></strong></span>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- KHU VỰC THAO TÁC THEO VAI TRÒ -->
                <div class="mb-3">
                    <?php if (co_vai_tro('nguoi_mua')): ?>
                        <!-- FORM ĐẶT MUA DÀNH CHO NGƯỜI MUA (CHUẨN THƯƠNG MẠI ĐIỆN TỬ, DỄ HIỂU NHẤT) -->
                        <form action="index.php?trang=them_gio_hang" method="POST" id="formDatHangChiTiet"
                            onsubmit="return kiemTraFormDatHang(event)">
                            <input type="hidden" name="ma_san_pham" value="<?= $sanPham['ma_san_pham'] ?>">
                            <input type="hidden" name="gia_de_nghi" id="inputGiaDeNghi"
                                value="<?= $giaGoc > 0 ? $giaGoc : '' ?>">

                            <!-- HÀNG 1: SỐ LƯỢNG MUA -->
                            <div class="row g-2 align-items-center mb-2.5">
                                <div class="col-sm-5 col-12">
                                    <label class="form-label small fw-bold text-dark mb-0">
                                        Số lượng mua:
                                    </label>
                                    <span class="text-muted d-block" style="font-size: 0.75rem;">
                                        Tối đa: <strong class="text-dark"><?= dinh_dang_so_luong($maxMua) ?></strong>
                                        <?= htmlspecialchars($sanPham['don_vi_gia']) ?>
                                    </span>
                                </div>
                                <div class="col-sm-7 col-12">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="nh-qty-control" id="khungSoLuongMua">
                                            <button type="button" class="nh-qty-btn" onclick="tangGiamSoLuong(-1)"
                                                aria-label="Giảm số lượng">
                                                <i class="bi bi-dash-lg"></i>
                                            </button>
                                            <input type="number" step="any" min="0.1" max="<?= $maxMua ?>" name="so_luong"
                                                id="inputSoLuongMua" class="nh-qty-input" value="1" required
                                                oninput="capNhatTongTienUocTinh()">
                                            <button type="button" class="nh-qty-btn" onclick="tangGiamSoLuong(1)"
                                                aria-label="Tăng số lượng">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        </div>
                                        <span
                                            class="text-muted fw-bold small"><?= htmlspecialchars($sanPham['don_vi_gia']) ?></span>
                                    </div>
                                    <div id="canhBaoVuotSoLuong" class="text-danger small mt-1 d-none"
                                        style="font-size: 0.8rem;">
                                        <i class="bi bi-exclamation-circle me-1"></i>Kho chỉ còn tối đa
                                        <?= dinh_dang_so_luong($maxMua) ?>         <?= htmlspecialchars($sanPham['don_vi_gia']) ?>
                                    </div>
                                </div>
                            </div>

                            <!-- HÀNG 2: GIÁ ĐỀ XUẤT THƯƠNG LƯỢNG -->
                            <div class="row g-2 align-items-center mb-2.5">
                                <div class="col-sm-5 col-12">
                                    <label class="form-label small fw-bold text-dark mb-0">
                                        Giá muốn mua:
                                    </label>
                                    <span class="text-muted d-block" style="font-size: 0.75rem;">
                                        (Có thể sửa để thương lượng)
                                    </span>
                                </div>
                                <div class="col-sm-7 col-12">
                                    <div class="nh-price-input-group" id="khungGiaDeNghi">
                                        <input type="text" id="inputGiaHienThi" class="nh-price-field"
                                            value="<?= $giaGoc > 0 ? number_format($giaGoc, 0, ',', '.') : '' ?>"
                                            placeholder="<?= $giaGoc > 0 ? number_format($giaGoc, 0, ',', '.') : 'Giá đề xuất' ?>"
                                            oninput="dinhDangNhapGia(this)">
                                        <span class="nh-price-addon">đ /
                                            <?= htmlspecialchars($sanPham['don_vi_gia']) ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- HÀNG 3: TỔNG TIỀN TẠM TÍNH -->
                            <div class="p-2.5 px-3 rounded-3 bg-light border mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fw-bold text-dark small">Tạm tính:</span>
                                    <span class="fs-4 fw-extrabold text-success" id="nhanTongTienUocTinh">
                                        <?= $giaGoc > 0 ? dinh_dang_tien($giaGoc) : '0 đ' ?>
                                    </span>
                                </div>
                            </div>

                            <!-- HÀNG 4: NÚT THÊM VÀO GIỎ HÀNG TO RÕ DUY NHẤT -->
                            <button type="submit" id="btnThemGioHang"
                                class="btn btn-clean btn-lg w-100 py-2.5 fw-bold fs-6 rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-cart-check-fill fs-5"></i> Thêm Vào Giỏ Hàng
                            </button>
                        </form>

                    <?php elseif (co_vai_tro('nong_ho')): ?>
                        <!-- VAI TRÒ NÔNG HỘ -->
                        <?php if ($sanPham['ma_nong_ho'] == $nguoiDung['ma_nguoi_dung']): ?>
                            <div class="nh-role-box-nongho">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div>
                                        <div class="fw-bold text-dark fs-6">Nông sản thuộc vườn của bạn</div>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2 pt-2 border-top border-success-subtle mt-2">
                                    <a href="index.php?trang=nong_ho_form_san_pham&id=<?= $sanPham['ma_san_pham'] ?>"
                                        class="btn btn-clean fw-bold rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center gap-1.5">
                                        <i class="bi bi-pencil-square"></i> Chỉnh Sửa
                                    </a>
                                    <a href="index.php?trang=nong_ho_san_pham"
                                        class="btn btn-outline-success fw-bold rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1.5">
                                        <i class="bi bi-grid-fill"></i> Kho Hàng Của Tôi
                                    </a>
                                    <a href="index.php?trang=don_hang"
                                        class="btn btn-light border fw-bold rounded-pill px-3 py-2 text-dark d-inline-flex align-items-center gap-1.5">
                                        <i class="bi bi-receipt"></i> Đơn Hàng Cần Xử Lý
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="nh-role-box-nongho bg-light border-secondary-subtle">
                                <div class="fw-bold text-dark fs-6 mb-1">Góc kết nối nhà vườn đồng nghiệp</div>
                                <div class="text-muted small mb-2">
                                    Bạn đang đăng nhập tài khoản <strong>Nông Hộ</strong>. Để đặt mua hàng, vui lòng sử dụng tài
                                    khoản <strong>Người Mua</strong>.
                                </div>
                                <a href="index.php?nong_ho=<?= $sanPham['ma_nong_ho'] ?>"
                                    class="btn btn-light border fw-bold rounded-pill px-3 py-1.5 small">
                                    <i class="bi bi-collection-fill me-1"></i> Xem Các Nông Sản Của Vườn Này
                                </a>
                            </div>
                        <?php endif; ?>

                    <?php elseif (co_vai_tro('admin')): ?>
                        <!-- VAI TRÒ ADMIN -->
                        <div class="nh-role-box-admin">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="fw-bold text-dark fs-6">
                                    <i class="bi bi-shield-shaded text-primary me-1"></i> Chế Độ Quản Trị Viên
                                </div>
                                <span
                                    class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill small fw-bold">
                                    Công khai trên sàn
                                </span>
                            </div>
                            <div class="small text-muted mb-2">
                                Nông hộ: <?= htmlspecialchars($sanPham['ten_nong_ho']) ?> (SĐT:
                                <?= htmlspecialchars($sanPham['sdt']) ?>)
                            </div>
                            <div class="d-flex flex-wrap gap-2 pt-1">
                                <a href="index.php?trang=admin_tong_quan&tu_khoa=<?= urlencode($sanPham['sdt']) ?>"
                                    class="btn btn-primary fw-bold rounded-pill px-3.5 py-1.5 small shadow-sm">
                                    <i class="bi bi-person-gear me-1"></i> Quản Lý Tài Khoản Nông Hộ Này
                                </a>
                                <a href="index.php?trang=admin_tong_quan"
                                    class="btn btn-outline-secondary fw-bold rounded-pill px-3 py-1.5 small">
                                    <i class="bi bi-speedometer2 me-1"></i> Về Quản trị
                                </a>
                            </div>
                        </div>

                    <?php else: ?>
                        <!-- KHÁCH CHƯA ĐĂNG NHẬP -->
                        <div class="nh-role-box-guest">
                            <div class="d-flex align-items-start gap-3 flex-wrap flex-sm-nowrap">
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-dark mb-1 fs-6">
                                        Bạn muốn thu mua nông sản này?
                                    </h6>
                                    <p class="text-muted small mb-3">
                                        Bạn cần phải đăng nhập bằng tài khoản <strong>Người Mua</strong> để thương lượng giá
                                        và đặt hàng trực tiếp từ nhà vườn.
                                    </p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="index.php?trang=dang_nhap"
                                            class="btn btn-clean px-4 py-2 fw-bold rounded-pill shadow-sm text-nowrap">
                                            <i class="bi bi-box-arrow-in-right me-1"></i> Đăng Nhập
                                        </a>
                                        <a href="index.php?trang=dang_ky"
                                            class="btn btn-outline-success px-3.5 py-2 fw-bold rounded-pill text-nowrap">
                                            Đăng Ký
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- THẺ NÔNG HỘ / NHÀ VƯỜN CUNG CẤP (XUẤT XỨ UY TÍN) -->
                <div class="nh-farmer-compact mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2.5 overflow-hidden">
                            <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 40px; height: 40px;">
                                <i class="bi bi-person-fill fs-5"></i>
                            </div>
                            <div class="overflow-hidden">
                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.98rem;">
                                    <?= htmlspecialchars($sanPham['ten_nong_ho']) ?>
                                </div>
                                <div class="text-muted small text-truncate" style="font-size: 0.8rem;"
                                    title="<?= htmlspecialchars($diaChiHienThi) ?>">
                                    <i class="bi bi-geo-alt-fill text-danger me-0.5"></i>
                                    <?= htmlspecialchars($diaChiHienThi) ?>
                                </div>
                            </div>
                        </div>
                        <span
                            class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill small fw-bold flex-shrink-0">
                            <i class="bi bi-shield-check"></i> Đã duyệt
                        </span>
                    </div>

                    <?php if (!empty($sanPham['don_thanh_cong']) && $sanPham['don_thanh_cong'] > 0): ?>
                        <div class="small text-muted py-1 border-top">
                            <i class="bi bi-patch-check-fill text-success me-1"></i>Đã bán thành công: <strong
                                class="text-dark"><?= (int) $sanPham['don_thanh_cong'] ?> đơn hàng</strong>
                        </div>
                    <?php endif; ?>

                    <!-- HOTLINE NHÀ VƯỜN -->
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                        <span class="small text-muted"><i class="bi bi-telephone-fill text-secondary me-1"></i>Hotline
                            nhà vườn:</span>
                        <?php if (da_dang_nhap()): ?>
                            <a href="#"
                                class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 fw-bold text-decoration-none">
                                <i class="bi bi-telephone-outbound-fill me-1"></i>Gọi ngay:
                                <?= htmlspecialchars($sanPham['sdt']) ?>
                            </a>
                        <?php else: ?>
                            <div class="small">
                                <span class="text-dark fw-bold"><?= che_so_dien_thoai($sanPham['sdt']) ?></span>
                                <a href="index.php?trang=dang_nhap" class="ms-1 text-success fw-bold text-decoration-none">
                                    (Đăng nhập)
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // TĂNG GIẢM SỐ LƯỢNG MUA 1 CHẠM
    function tangGiamSoLuong(delta) {
        const input = document.getElementById('inputSoLuongMua');
        if (!input) return;

        let hienTai = parseFloat(input.value) || 0;
        let moi = Math.round((hienTai + delta) * 100) / 100;

        let min = parseFloat(input.min) || 0.1;
        let max = parseFloat(input.max) || 99999999;

        if (moi < min) moi = min;
        if (moi > max) moi = max;

        input.value = moi;
        capNhatTongTienUocTinh();
    }

    // ĐỊNH DẠNG Ô NHẬP GIÁ TIỀN TỰ ĐỘNG THÊM DẤU CHẤM HÀNG NGHÌN (12000 -> 12.000)
    function dinhDangNhapGia(el) {
        let raw = el.value.replace(/\D/g, '');
        let num = parseInt(raw, 10);

        const hiddenInput = document.getElementById('inputGiaDeNghi');
        if (isNaN(num) || num <= 0) {
            if (hiddenInput) hiddenInput.value = '';
            el.value = '';
        } else {
            if (hiddenInput) hiddenInput.value = num;
            el.value = new Intl.NumberFormat('vi-VN').format(num);
        }
        capNhatTongTienUocTinh();
    }

    // CẬP NHẬT TỔNG TIỀN VÀ TRẠNG THÁI NÚT BẤM (GỌN GÀNG, KHÔNG RỐI MẮT)
    function capNhatTongTienUocTinh() {
        const elTongTien = document.getElementById('nhanTongTienUocTinh');
        const inputSl = document.getElementById('inputSoLuongMua');
        const inputGiaRaw = document.getElementById('inputGiaDeNghi');
        const khungSl = document.getElementById('khungSoLuongMua');
        const baoLoiSl = document.getElementById('canhBaoVuotSoLuong');
        const btnSubmit = document.getElementById('btnThemGioHang');

        if (!inputSl) return;

        const donViGia = <?= json_encode($sanPham['don_vi_gia'] ?? '') ?>;
        const max = parseFloat(inputSl.max) || 99999999;
        let sl = parseFloat(inputSl.value);
        let gia = inputGiaRaw ? parseFloat(inputGiaRaw.value) : 0;

        let vuotKho = (!isNaN(sl) && sl > max);
        let slHopLe = (!isNaN(sl) && sl > 0 && !vuotKho);
        let giaHopLe = (!isNaN(gia) && gia > 0);

        // 1. Cảnh báo số lượng: chỉ 1 dòng ngắn gọn duy nhất dưới ô nhập
        if (khungSl && baoLoiSl) {
            if (vuotKho) {
                khungSl.classList.add('is-invalid');
                baoLoiSl.classList.remove('d-none');
            } else {
                khungSl.classList.remove('is-invalid');
                baoLoiSl.classList.add('d-none');
            }
        }

        // 2. Tính tiền & hiển thị trạng thái nút bấm
        if (vuotKho) {
            if (elTongTien) elTongTien.innerHTML = '<span class="text-muted fs-6">--</span>';
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.classList.add('opacity-50');
                btnSubmit.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i> Kho chỉ còn tối đa ' + new Intl.NumberFormat('vi-VN').format(max) + ' ' + donViGia;
            }
        } else if (!slHopLe) {
            if (elTongTien) elTongTien.innerHTML = '<span class="text-muted fs-6">--</span>';
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.classList.add('opacity-50');
                btnSubmit.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i> Vui lòng nhập số lượng';
            }
        } else if (!giaHopLe) {
            if (elTongTien) elTongTien.innerHTML = '<span class="text-muted fs-6">Chưa có giá</span>';
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.classList.add('opacity-50');
                btnSubmit.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i> Vui lòng nhập giá mua';
            }
        } else {
            let tong = sl * gia;
            let formatted = new Intl.NumberFormat('vi-VN').format(Math.round(tong)) + ' đ';
            if (elTongTien) elTongTien.innerHTML = formatted;
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('opacity-50');
                btnSubmit.innerHTML = '<i class="bi bi-cart-check-fill fs-5"></i> Thêm Vào Giỏ Hàng';
            }
        }
    }

    // BẢO VỆ CHẶN SUBMIT NẾU SỐ LƯỢNG HOẶC GIÁ KHÔNG HỢP LỆ
    function kiemTraFormDatHang(event) {
        const inputSl = document.getElementById('inputSoLuongMua');
        const inputGia = document.getElementById('inputGiaDeNghi');
        if (!inputSl) return true;

        let sl = parseFloat(inputSl.value);
        let max = parseFloat(inputSl.max) || 99999999;
        let gia = inputGia ? parseFloat(inputGia.value) : 0;

        if (isNaN(sl) || sl <= 0 || sl > max) {
            event.preventDefault();
            return false;
        }
        if (isNaN(gia) || gia <= 0) {
            event.preventDefault();
            return false;
        }
        return true;
    }

    // ==============================================================================
    // LUCIAN UX/UI: QUẢN LÝ ALBUM ẢNH NÔNG SẢN (CHUYỂN ẢNH HAI BÊN, VUỐT CẢM ỨNG & PHÓNG TO)
    // ==============================================================================
    const dsAnhSanPham = <?= json_encode($dsAnhUrls, JSON_UNESCAPED_SLASHES) ?>;
    let chiSoAnhHienTai = 0;
    let duongDanAnhHienTai = dsAnhSanPham[0] || '<?= $anhHienThi ?>';
    const tenNongSanHienTai = '<?= htmlspecialchars(addslashes($sanPham['ten'])) ?>';

    function capNhatGiaoDienAnh(index, buoc = 0) {
        if (!dsAnhSanPham || dsAnhSanPham.length === 0) return;
        
        // Vòng lặp xoay vòng tuần hoàn (0 <-> length - 1)
        if (index < 0) {
            index = dsAnhSanPham.length - 1;
        } else if (index >= dsAnhSanPham.length) {
            index = 0;
        }
        chiSoAnhHienTai = index;
        duongDanAnhHienTai = dsAnhSanPham[chiSoAnhHienTai];

        const mainImg = document.getElementById('imgDetailMain');
        const backdrop = document.getElementById('imgDetailBackdrop');
        const counterIdx = document.getElementById('galleryCurrentIdx');
        const modalImg = document.getElementById('anhPhongToChiTiet');

        if (mainImg) {
            mainImg.classList.add('fade-switch');
            const imgLoader = new Image();
            imgLoader.src = duongDanAnhHienTai;
            const finishSwitch = () => {
                mainImg.src = duongDanAnhHienTai;
                mainImg.classList.remove('fade-switch');
            };
            imgLoader.onload = finishSwitch;
            imgLoader.onerror = finishSwitch;
            setTimeout(() => mainImg.classList.remove('fade-switch'), 220);
        }

        if (backdrop) {
            backdrop.style.backgroundImage = `url('${duongDanAnhHienTai}')`;
        }

        if (counterIdx) {
            counterIdx.textContent = (chiSoAnhHienTai + 1);
        }

        if (modalImg && modalImg.src) {
            modalImg.src = duongDanAnhHienTai;
        }

        // Cập nhật trạng thái thumbnail active & cuộn vào giữa
        const thumbButtons = document.querySelectorAll('.nh-detail-thumb-item');
        thumbButtons.forEach((btn, idx) => {
            if (idx === chiSoAnhHienTai) {
                btn.classList.add('active');
                btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            } else {
                btn.classList.remove('active');
            }
        });
    }

    function chuyenAnhLienKe(buoc) {
        capNhatGiaoDienAnh(chiSoAnhHienTai + buoc, buoc);
    }

    function chuyenAnhTheoChiSo(index) {
        capNhatGiaoDienAnh(index);
    }

    function chuyenAnhChiTiet(url, btnEl) {
        const idx = dsAnhSanPham.indexOf(url);
        if (idx !== -1) {
            capNhatGiaoDienAnh(idx);
        } else {
            duongDanAnhHienTai = url;
            const mainImg = document.getElementById('imgDetailMain');
            const backdrop = document.getElementById('imgDetailBackdrop');
            if (mainImg) mainImg.src = url;
            if (backdrop) backdrop.style.backgroundImage = `url('${url}')`;
            document.querySelectorAll('.nh-detail-thumb-item').forEach(btn => btn.classList.remove('active'));
            if (btnEl) btnEl.classList.add('active');
        }
    }

    function moXemAnhHienTai() {
        moXemAnhToanManHinh(duongDanAnhHienTai, tenNongSanHienTai);
    }

    // MỞ XEM ẢNH NÔNG SẢN KÍCH THƯỚC GỐC TOÀN MÀN HÌNH
    function moXemAnhToanManHinh(url, ten) {
        const modalEl = document.getElementById('modalXemAnhNongSan');
        const imgEl = document.getElementById('anhPhongToChiTiet');
        if (modalEl && imgEl) {
            imgEl.src = url;
            imgEl.alt = ten;
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }

    // Khởi chạy tính tiền & thiết lập điều hướng cảm ứng vuốt + bàn phím
    document.addEventListener('DOMContentLoaded', function () {
        capNhatTongTienUocTinh();

        // HỖ TRỢ VUỐT CẢM ỨNG (TOUCH SWIPE) TRÊN KHUNG ẢNH VÀ TRONG MODAL
        function bindSwipe(el) {
            if (!el || dsAnhSanPham.length <= 1) return;
            let startX = 0, startY = 0, endX = 0, endY = 0;
            el.addEventListener('touchstart', function (e) {
                if (e.touches.length === 1) {
                    startX = e.touches[0].clientX;
                    startY = e.touches[0].clientY;
                    endX = startX;
                    endY = startY;
                }
            }, { passive: true });

            el.addEventListener('touchmove', function (e) {
                if (e.touches.length === 1) {
                    endX = e.touches[0].clientX;
                    endY = e.touches[0].clientY;
                }
            }, { passive: true });

            el.addEventListener('touchend', function () {
                const diffX = endX - startX;
                const diffY = endY - startY;
                if (Math.abs(diffX) > 40 && Math.abs(diffX) > Math.abs(diffY)) {
                    if (diffX < 0) {
                        chuyenAnhLienKe(1); // Vuốt sang trái -> Ảnh tiếp theo
                    } else {
                        chuyenAnhLienKe(-1); // Vuốt sang phải -> Ảnh trước
                    }
                }
            }, { passive: true });
        }

        bindSwipe(document.getElementById('nhDetailImgBox'));
        bindSwipe(document.getElementById('modalXemAnhNongSan'));

        // BẮT SỰ KIỆN PHÍM MŨI TÊN BÀN PHÍM
        document.addEventListener('keydown', function (e) {
            const activeEl = document.activeElement;
            if (activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA')) {
                return;
            }
            if (dsAnhSanPham.length > 1) {
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    chuyenAnhLienKe(-1);
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    chuyenAnhLienKe(1);
                }
            }
        });
    });
</script>

<!-- MODAL XEM ẢNH NÔNG SẢN TOÀN DIỆN KÍCH THƯỚC GỐC -->
<div class="modal fade" id="modalXemAnhNongSan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="modal-body p-2 text-center position-relative">
                <button type="button" class="btn btn-dark rounded-circle position-absolute top-0 end-0 m-2 shadow"
                    data-bs-dismiss="modal" aria-label="Đóng" style="z-index: 12; width: 40px; height: 40px;">
                    <i class="bi bi-x-lg"></i>
                </button>

                <?php if ($coNhieuAnh): ?>
                    <!-- NÚT CHUYỂN ẢNH TRONG MODAL PHÓNG TO -->
                    <button type="button" class="nh-modal-nav-btn nh-modal-prev" onclick="chuyenAnhLienKe(-1)" aria-label="Xem ảnh trước">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button type="button" class="nh-modal-nav-btn nh-modal-next" onclick="chuyenAnhLienKe(1)" aria-label="Xem ảnh tiếp theo">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                <?php endif; ?>

                <img id="anhPhongToChiTiet" src="" alt="" class="img-fluid rounded-4 shadow-lg"
                    style="max-height: 85vh; object-fit: contain; background: rgba(15,23,42,0.92);">
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
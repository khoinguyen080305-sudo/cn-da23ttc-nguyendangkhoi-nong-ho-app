<?php
// views/don_hang/danh_sach.php
// Quản lý quy trình 3 bước Đơn Hàng - Tối ưu Trải nghiệm Giá, Chiết khấu sỉ, Nhấn mạnh Giá Chốt Cuối Cùng & Bộ lọc thông minh
/** @var array $dsDonHang Danh sách đơn hàng từ Controller */
/** @var array $demTabs Số lượng đếm theo các tab từ Controller */
/** @var array $boLoc Mảng tiêu chí lọc từ Controller */
$dsDonHang = $dsDonHang ?? [];
$demTabs = $demTabs ?? ['moi' => 0, 'dang_xu_ly' => 0, 'da_xac_nhan' => 0, 'lich_su' => 0];
$boLoc = $boLoc ?? ['tu_khoa' => '', 'quy_mo' => '', 'xu_ly' => '', 'thoi_gian' => '', 'sap_xep' => 'moi_nhat'];

require_once __DIR__ . '/../layouts/header.php';
$nguoiDung = nguoi_dung_hien_tai();
$vaiTro = $nguoiDung['vai_tro'];
$tabHienTai = $_GET['tab'] ?? 'dang_xu_ly';

// Kiểm tra xem có đang kích hoạt bộ lọc nào không
$dangLoc = !empty($boLoc['tu_khoa'])
    || !empty($boLoc['quy_mo'])
    || !empty($boLoc['xu_ly'])
    || !empty($boLoc['thoi_gian'])
    || (($boLoc['sap_xep'] ?? 'moi_nhat') !== 'moi_nhat');
?>

<div class="container-fluid px-2 px-sm-3 px-lg-4 py-2">
    <!-- TIÊU ĐỀ & VAI TRÒ -->
    <div class="d-flex align-items-center justify-content-between gap-2 mb-2.5">
        <h4 class="fw-bold mb-0" style="color: #064e3b;">
            <i class="bi bi-receipt text-success me-2"></i>Quản Lý Đơn Hàng
        </h4>
        <div class="badge bg-light text-dark border px-2.5 py-1.5 fs-6 rounded-pill">
            <span class="text-muted d-none d-sm-inline">Vai trò:</span>
            <strong class="text-success"><?= $vaiTro === 'nong_ho' ? 'Nông Hộ' : 'Người Mua' ?></strong>
        </div>
    </div>

    <!-- THANH 3 TAB TRẠNG THÁI DẠNG SEGMENTED CONTROL (1 HÀNG NGANG GỌN GÀNG, KHÔNG CHIẾM MÀN HÌNH) -->
    <div class="nh-order-tabs-box mb-3 bg-white p-1.5 rounded-pill border shadow-sm d-flex">
        <a href="index.php?trang=don_hang&tab=dang_xu_ly<?= !empty($boLoc['tu_khoa']) ? '&tu_khoa=' . urlencode($boLoc['tu_khoa']) : '' ?>"
            class="nh-order-tab-item flex-fill text-center py-2 px-1 text-decoration-none rounded-pill fw-bold <?= $tabHienTai === 'dang_xu_ly' ? 'active bg-success text-white shadow-sm' : 'text-secondary' ?>">
            <i class="bi bi-hourglass-split me-1"></i>
            <span>Chờ báo giá</span>
            <?php if (($demTabs['moi'] ?? 0) > 0): ?>
                <span class="badge bg-danger rounded-pill ms-1"><?= $demTabs['moi'] ?></span>
            <?php elseif (($demTabs['dang_xu_ly'] ?? 0) > 0): ?>
                <span
                    class="badge <?= $tabHienTai === 'dang_xu_ly' ? 'bg-white text-success' : 'bg-light text-dark border' ?> rounded-pill ms-1">
                    <?= $demTabs['dang_xu_ly'] ?>
                </span>
            <?php endif; ?>
        </a>

        <a href="index.php?trang=don_hang&tab=da_xac_nhan<?= !empty($boLoc['tu_khoa']) ? '&tu_khoa=' . urlencode($boLoc['tu_khoa']) : '' ?>"
            class="nh-order-tab-item flex-fill text-center py-2 px-1 text-decoration-none rounded-pill fw-bold <?= $tabHienTai === 'da_xac_nhan' ? 'active bg-primary text-white shadow-sm' : 'text-secondary' ?>">
            <i class="bi bi-check2-circle me-1"></i>
            <span>Đã chốt</span>
            <?php if (($demTabs['da_xac_nhan'] ?? 0) > 0): ?>
                <span
                    class="badge <?= $tabHienTai === 'da_xac_nhan' ? 'bg-white text-primary' : 'bg-light text-dark border' ?> rounded-pill ms-1">
                    <?= $demTabs['da_xac_nhan'] ?>
                </span>
            <?php endif; ?>
        </a>

        <a href="index.php?trang=don_hang&tab=lich_su<?= !empty($boLoc['tu_khoa']) ? '&tu_khoa=' . urlencode($boLoc['tu_khoa']) : '' ?>"
            class="nh-order-tab-item flex-fill text-center py-2 px-1 text-decoration-none rounded-pill fw-bold <?= $tabHienTai === 'lich_su' ? 'active bg-secondary text-white shadow-sm' : 'text-secondary' ?>">
            <i class="bi bi-clock-history me-1"></i>
            <span>Lịch sử</span>
            <?php if (($demTabs['lich_su'] ?? 0) > 0): ?>
                <span
                    class="badge <?= $tabHienTai === 'lich_su' ? 'bg-white text-dark' : 'bg-light text-muted border' ?> rounded-pill ms-1">
                    <?= $demTabs['lich_su'] ?>
                </span>
            <?php endif; ?>
        </a>
    </div>

    <!-- KHU VỰC BỘ LỌC ĐƠN HÀNG THÔNG MINH (TÌM KIẾM, ĐƠN SỈ SỐ LƯỢNG LỚN, CẦN XỬ LÝ, THỜI GIAN & SẮP XẾP) -->
    <div class="nh-order-filter-box mb-3">
        <form action="index.php" method="GET" class="row g-2 align-items-center">
            <input type="hidden" name="trang" value="don_hang">
            <input type="hidden" name="tab" value="<?= htmlspecialchars($tabHienTai) ?>">

            <!-- 1. Ô TÌM KIẾM TỨC THÌ (Mã đơn, Tên nông sản, Tên/SĐT đối tác) -->
            <div class="col-12 col-md-4 col-xl-3">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 rounded-start-pill text-muted px-2.5">
                        <i class="bi bi-search text-success"></i>
                    </span>
                    <input type="text" name="tu_khoa"
                        class="form-control nh-filter-input border-start-0 rounded-end-pill ps-0"
                        placeholder="Mã đơn, nông sản, đối tác..." value="<?= htmlspecialchars($boLoc['tu_khoa']) ?>">
                </div>
            </div>

            <!-- 2. LỌC THEO QUY MÔ ĐƠN & CHIẾT KHẤU SỈ -->
            <div class="col-6 col-sm-4 col-md-3 col-xl-3">
                <select name="quy_mo" class="form-select nh-filter-select" onchange="this.form.submit()">
                    <option value="">Tất cả quy mô</option>
                    <option value="si" <?= $boLoc['quy_mo'] === 'si' ? 'selected' : '' ?>>Đơn sỉ số lượng lớn (từ 500 kg
                        trở lên)</option>
                    <option value="thuong" <?= $boLoc['quy_mo'] === 'thuong' ? 'selected' : '' ?>>Đơn mua lẻ (dưới 500 kg)
                    </option>
                </select>
            </div>

            <!-- 3. LỌC THEO TÌNH TRẠNG CẦN XỬ LÝ -->
            <div class="col-6 col-sm-4 col-md-2 col-xl-2">
                <select name="xu_ly" class="form-select nh-filter-select" onchange="this.form.submit()">
                    <option value="">Tất cả việc cần làm</option>
                    <option value="can_xu_ly" <?= $boLoc['xu_ly'] === 'can_xu_ly' ? 'selected' : '' ?>>Cần tôi xử lý ngay
                    </option>
                    <option value="cho_doi_tac" <?= $boLoc['xu_ly'] === 'cho_doi_tac' ? 'selected' : '' ?>>Chờ đối tác phản
                        hồi</option>
                </select>
            </div>

            <!-- 4. LỌC THEO THỜI GIAN -->
            <div class="col-6 col-sm-4 col-md-2 col-xl-2">
                <select name="thoi_gian" class="form-select nh-filter-select" onchange="this.form.submit()">
                    <option value="">Tất cả thời gian</option>
                    <option value="hom_nay" <?= $boLoc['thoi_gian'] === 'hom_nay' ? 'selected' : '' ?>>Hôm nay</option>
                    <option value="7_ngay" <?= $boLoc['thoi_gian'] === '7_ngay' ? 'selected' : '' ?>>7 ngày qua</option>
                    <option value="thang_nay" <?= $boLoc['thoi_gian'] === 'thang_nay' ? 'selected' : '' ?>>Tháng này
                    </option>
                </select>
            </div>

            <!-- 5. SẮP XẾP THỨ TỰ -->
            <div class="col-6 col-sm-6 col-md-2 col-xl-2">
                <select name="sap_xep" class="form-select nh-filter-select" onchange="this.form.submit()">
                    <option value="moi_nhat" <?= ($boLoc['sap_xep'] ?? '') === 'moi_nhat' ? 'selected' : '' ?>>Mới nhất
                    </option>
                    <option value="cu_nhat" <?= ($boLoc['sap_xep'] ?? '') === 'cu_nhat' ? 'selected' : '' ?>>Cũ nhất
                    </option>
                    <option value="gia_cao" <?= ($boLoc['sap_xep'] ?? '') === 'gia_cao' ? 'selected' : '' ?>>Giá trị cao
                        nhất</option>
                    <option value="so_luong_lon" <?= ($boLoc['sap_xep'] ?? '') === 'so_luong_lon' ? 'selected' : '' ?>>Số
                        lượng nhiều nhất</option>
                </select>
            </div>

            <!-- NÚT ÁP DỤNG & ĐẶT LẠI -->
            <div class="col-12 col-sm-6 col-md-auto ms-auto d-flex gap-1.5 justify-content-end">
                <button type="submit" class="btn btn-sm btn-success rounded-pill fw-bold px-3 py-1.5">
                    Lọc
                </button>
                <?php if ($dangLoc): ?>
                    <a href="index.php?trang=don_hang&tab=<?= htmlspecialchars($tabHienTai) ?>"
                        class="btn btn-sm btn-outline-danger rounded-pill fw-bold px-3 py-1.5" title="Xóa toàn bộ bộ lọc">
                        Đặt lại
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <!-- DẢI TAG HIỂN THỊ CÁC TIÊU CHÍ ĐANG LỌC -->
        <?php if ($dangLoc): ?>
            <div class="mt-2.5 pt-2 border-top d-flex flex-wrap align-items-center gap-1.5">
                <span class="text-muted small fw-bold" style="font-size: 0.8rem;">
                    Tìm thấy <strong class="text-success"><?= count($dsDonHang) ?></strong> đơn:
                </span>
                <?php if (!empty($boLoc['tu_khoa'])): ?>
                    <span class="nh-filter-chip">
                        Từ khóa: "<?= htmlspecialchars($boLoc['tu_khoa']) ?>"
                        <a href="index.php?trang=don_hang&tab=<?= htmlspecialchars($tabHienTai) ?>&quy_mo=<?= urlencode($boLoc['quy_mo']) ?>&xu_ly=<?= urlencode($boLoc['xu_ly']) ?>&thoi_gian=<?= urlencode($boLoc['thoi_gian']) ?>&sap_xep=<?= urlencode($boLoc['sap_xep']) ?>"
                            class="text-danger text-decoration-none ms-1">×</a>
                    </span>
                <?php endif; ?>
                <?php if (!empty($boLoc['quy_mo'])): ?>
                    <span class="nh-filter-chip">
                        <?= $boLoc['quy_mo'] === 'si' ? 'Đơn sỉ (≥ 500 kg)' : 'Đơn mua lẻ (< 500 kg)' ?>
                        <a href="index.php?trang=don_hang&tab=<?= htmlspecialchars($tabHienTai) ?>&tu_khoa=<?= urlencode($boLoc['tu_khoa']) ?>&xu_ly=<?= urlencode($boLoc['xu_ly']) ?>&thoi_gian=<?= urlencode($boLoc['thoi_gian']) ?>&sap_xep=<?= urlencode($boLoc['sap_xep']) ?>"
                            class="text-danger text-decoration-none ms-1">×</a>
                    </span>
                <?php endif; ?>
                <?php if (!empty($boLoc['xu_ly'])): ?>
                    <span class="nh-filter-chip">
                        <?= $boLoc['xu_ly'] === 'can_xu_ly' ? 'Cần xử lý ngay' : 'Chờ đối tác' ?>
                        <a href="index.php?trang=don_hang&tab=<?= htmlspecialchars($tabHienTai) ?>&tu_khoa=<?= urlencode($boLoc['tu_khoa']) ?>&quy_mo=<?= urlencode($boLoc['quy_mo']) ?>&thoi_gian=<?= urlencode($boLoc['thoi_gian']) ?>&sap_xep=<?= urlencode($boLoc['sap_xep']) ?>"
                            class="text-danger text-decoration-none ms-1">×</a>
                    </span>
                <?php endif; ?>
                <?php if (!empty($boLoc['thoi_gian'])): ?>
                    <span class="nh-filter-chip">
                        Thời gian:
                        <?= $boLoc['thoi_gian'] === 'hom_nay' ? 'Hôm nay' : ($boLoc['thoi_gian'] === '7_ngay' ? '7 ngày qua' : 'Tháng này') ?>
                        <a href="index.php?trang=don_hang&tab=<?= htmlspecialchars($tabHienTai) ?>&tu_khoa=<?= urlencode($boLoc['tu_khoa']) ?>&quy_mo=<?= urlencode($boLoc['quy_mo']) ?>&xu_ly=<?= urlencode($boLoc['xu_ly']) ?>&sap_xep=<?= urlencode($boLoc['sap_xep']) ?>"
                            class="text-danger text-decoration-none ms-1">×</a>
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- DANH SÁCH ĐƠN HÀNG TRONG TAB HIỆN TẠI -->
    <?php if (empty($dsDonHang)): ?>
        <div class="bg-white rounded-4 border shadow-sm p-5 text-center my-3 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
            <h5 class="fw-bold text-dark">Chưa có đơn hàng nào phù hợp</h5>
            <p class="text-muted mb-0 small">
                <?= $dangLoc ? 'Không tìm thấy đơn hàng theo tiêu chí bộ lọc. Hãy thử đặt lại bộ lọc.' : 'Các giao dịch phát sinh sẽ hiển thị tại đây.' ?>
            </p>
            <?php if ($dangLoc): ?>
                <a href="index.php?trang=don_hang&tab=<?= htmlspecialchars($tabHienTai) ?>"
                    class="btn btn-outline-success btn-sm rounded-pill mt-3 fw-bold">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Xem tất cả đơn trong tab này
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>

        <!-- 1. GIAO DIỆN THẺ ĐƠN HÀNG DI ĐỘNG (CHUẨN UX MOBILE, NÚT GỌI ĐIỆN 1 CHẠM & KHUNG GIÁ CHỐT RÕ RÀNG) -->
        <div class="d-block d-md-none mb-4">
            <?php foreach ($dsDonHang as $dh):
                $tongTien = (float) $dh['gia'] * (float) $dh['so_luong'];
                $anhHienThi = duong_dan_anh_sp($dh['anh_san_pham'] ?? ($dh['anh'] ?? ''));
                $trangThaiInfo = TRANG_THAI_DON[$dh['trang_thai']] ?? ['nhan' => $dh['trang_thai'], 'mau' => 'secondary'];
                $laDonMoi = ($dh['trang_thai'] === 'moi');
                $sdtDoiTac = ($vaiTro === 'nong_ho') ? $dh['sdt_nguoi_mua'] : $dh['sdt_nong_ho'];
                $tenDoiTac = ($vaiTro === 'nong_ho') ? $dh['ten_nguoi_mua'] : $dh['ten_nong_ho'];

                // Nhận diện đơn sỉ số lượng lớn
                $laDonSi = ($dh['don_vi'] === 'tấn')
                    || ($dh['don_vi'] === 'tạ' && $dh['so_luong'] >= 5)
                    || ($dh['don_vi'] === 'kg' && $dh['so_luong'] >= 500)
                    || ($dh['so_luong'] >= 500);

                // Tính toán ưu đãi giảm giá sỉ (nếu có so với giá ban đầu hoặc giá niêm yết gốc)
                $giaGoc = 0;
                if (!empty($dh['gia_ban_dau']) && (float) $dh['gia_ban_dau'] > (float) $dh['gia']) {
                    $giaGoc = (float) $dh['gia_ban_dau'];
                } elseif (!empty($dh['gia_goc_sp']) && (float) $dh['gia_goc_sp'] > (float) $dh['gia']) {
                    $giaGoc = (float) $dh['gia_goc_sp'];
                }
                $coGiamGia = ($giaGoc > 0 && $giaGoc > (float) $dh['gia']);
                $mucGiamMoiDonVi = $coGiamGia ? ($giaGoc - (float) $dh['gia']) : 0;
                $tongTietKiem = $coGiamGia ? ($mucGiamMoiDonVi * (float) $dh['so_luong']) : 0;
                $phanTramGiam = $coGiamGia ? round(($mucGiamMoiDonVi / $giaGoc) * 100) : 0;

                $daChotGia = in_array($dh['trang_thai'], ['da_xac_nhan', 'hoan_thanh']);
                ?>
                <div
                    class="card mb-3 border shadow-sm rounded-3 overflow-hidden <?= $laDonMoi ? 'border-danger' : ($daChotGia ? 'border-success' : 'border-light-subtle') ?>">
                    <!-- Thanh tiêu đề thẻ: Mã đơn + Thời gian + Huy hiệu Đơn Sỉ + Badge trạng thái -->
                    <div
                        class="card-header bg-light bg-opacity-75 py-2 px-3 d-flex align-items-center justify-content-between border-bottom">
                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                            <span class="badge bg-white text-dark border fw-bold" style="font-size: 0.78rem;">
                                <?= ma_hien_thi('don_hang', $dh['ma_don_hang']) ?>
                            </span>
                            <?php if ($laDonSi): ?>
                                <span class="badge bg-light text-secondary border fw-normal" style="font-size: 0.75rem;">
                                    Sỉ (≥ 500 kg)
                                </span>
                            <?php endif; ?>
                            <small class="text-muted ms-1"
                                style="font-size: 0.75rem;"><?= dinh_dang_ngay($dh['ngay_tao']) ?></small>
                        </div>
                        <div>
                            <?php if ($laDonMoi): ?>
                                <span class="badge bg-danger text-white fw-bold px-2 py-0.5" style="font-size: 0.72rem;">
                                    <i class="bi bi-bell-fill me-0.5"></i>MỚI
                                </span>
                            <?php elseif ($dh['trang_thai'] === 'da_bao_gia' && $vaiTro === 'nguoi_mua'): ?>
                                <span class="badge bg-primary text-white fw-bold px-2 py-0.5" style="font-size: 0.72rem;">
                                    GIÁ MỚI
                                </span>
                            <?php else: ?>
                                <span class="badge bg-<?= $trangThaiInfo['mau'] ?> px-2 py-0.5" style="font-size: 0.72rem;">
                                    <?= $trangThaiInfo['nhan'] ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-body p-3">
                        <!-- Hàng 1: Ảnh nông sản + Tên + Sản lượng đặt mua -->
                        <div class="d-flex align-items-center gap-2.5 mb-2.5 pb-2 border-bottom">
                            <img src="<?= $anhHienThi ?>" alt="<?= htmlspecialchars($dh['ten_san_pham']) ?>"
                                class="rounded-3 border flex-shrink-0" style="width: 58px; height: 58px; object-fit: cover;"
                                onerror="this.src='assets/images/nong_san_mac_dinh.svg'">
                            <div class="flex-grow-1 min-w-0">
                                <h6 class="fw-bold mb-1 text-dark text-truncate" style="font-size: 0.98rem;">
                                    <?= htmlspecialchars($dh['ten_san_pham']) ?>
                                </h6>
                                <div class="small text-muted mb-0.5">
                                    Số lượng: <strong class="text-dark fs-6"><?= dinh_dang_so_luong($dh['so_luong']) ?>
                                        <?= htmlspecialchars($dh['don_vi']) ?></strong>
                                    <?php if ($laDonSi): ?>
                                        <span class="badge bg-light text-secondary border ms-1" style="font-size: 0.7rem;">Sỉ (≥ 500
                                            kg)</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- HÀNG 2: KHU VỰC THỂ HIỆN GIÁ & NHẤN MẠNH GIÁ CHỐT CUỐI CÙNG -->
                        <?php if ($daChotGia): ?>
                            <!-- KHUNG GIÁ CHỐT HỢP ĐỒNG CUỐI CÙNG (DÀNH CHO ĐƠN ĐÃ CHỐT HOẶC HOÀN THÀNH) -->
                            <div class="nh-final-deal-box mb-2.5">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="nh-final-deal-badge">Giá chốt</span>
                                    <?php if ($coGiamGia): ?>
                                        <span class="nh-discount-tag fw-bold">
                                            Giảm <?= $phanTramGiam ?>%
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex align-items-baseline justify-content-between">
                                    <div>
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">Đơn giá chốt:</small>
                                        <strong class="fs-6"><?= dinh_dang_tien($dh['gia']) ?></strong>
                                        <span class="text-muted fw-bold">/<?= htmlspecialchars($dh['don_vi_gia']) ?></span>
                                        <?php if ($coGiamGia): ?>
                                            <del class="text-muted ms-1" style="font-size: 0.78rem;">
                                                <?= dinh_dang_tien($giaGoc) ?>
                                            </del>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">Tổng tiền chốt cuối:</small>
                                        <div class="nh-final-deal-price">
                                            <?= dinh_dang_tien($tongTien) ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($coGiamGia && $tongTietKiem > 0): ?>
                                    <div class="mt-2 pt-1.5 border-top border-success border-opacity-25 text-success small d-flex flex-wrap justify-content-between align-items-center gap-1"
                                        style="font-size: 0.8rem;">
                                        <span>Nông hộ đã giảm: <strong>-<?= dinh_dang_tien($mucGiamMoiDonVi) ?>
                                                đ/<?= htmlspecialchars($dh['don_vi_gia']) ?></strong>
                                            (<strong>-<?= $phanTramGiam ?>%</strong>)</span>
                                        <span>Tiết kiệm: <strong class="fs-6"><?= dinh_dang_tien($tongTietKiem) ?> đ</strong></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <!-- KHUNG GIÁ ĐANG THƯƠNG LƯỢNG / BÁO LẠI -->
                            <div class="bg-light p-2.5 rounded-3 border mb-2.5">
                                <div class="d-flex justify-content-between align-items-baseline">
                                    <div>
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                                            <?= $dh['trang_thai'] === 'da_bao_gia' ? 'Giá Nông Hộ báo lại:' : 'Mức giá đề xuất:' ?>
                                        </small>
                                        <strong class="text-success fs-6"><?= dinh_dang_tien($dh['gia']) ?></strong>
                                        <span class="text-muted fw-bold">/<?= htmlspecialchars($dh['don_vi_gia']) ?></span>
                                        <?php if ($coGiamGia): ?>
                                            <del class="text-muted ms-1" style="font-size: 0.78rem;">
                                                <?= dinh_dang_tien($giaGoc) ?>
                                            </del>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">Thành tiền tạm tính:</small>
                                        <h5 class="fw-bold text-success mb-0" style="font-size: 1.25rem;">
                                            <?= dinh_dang_tien($tongTien) ?>
                                        </h5>
                                    </div>
                                </div>
                                <?php if ($coGiamGia && $tongTietKiem > 0): ?>
                                    <div class="mt-2 pt-1.5 border-top border-success-subtle small text-success d-flex flex-wrap justify-content-between align-items-center gap-1"
                                        style="font-size: 0.8rem;">
                                        <span>Nông hộ đã giảm: <strong>-<?= dinh_dang_tien($mucGiamMoiDonVi) ?>
                                                đ/<?= htmlspecialchars($dh['don_vi_gia']) ?></strong>
                                            (<strong>-<?= $phanTramGiam ?>%</strong>)</span>
                                        <span>Tiết kiệm: <strong class="fs-6"><?= dinh_dang_tien($tongTietKiem) ?> đ</strong></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Hàng 3: Đối tác giao dịch & Nút gọi điện 1 chạm -->
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="min-w-0" style="max-width: 65%;">
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Đối tác giao dịch:</small>
                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.9rem;">
                                    <?= htmlspecialchars($tenDoiTac) ?>
                                </div>
                            </div>
                            <!-- NÚT GỌI ĐIỆN 1 CHẠM TRỰC DIỆN -->
                            <a href="tel:<?= htmlspecialchars($sdtDoiTac) ?>"
                                class="btn btn-sm btn-outline-success py-1 px-2.5 rounded-pill fw-bold d-inline-flex align-items-center gap-1 shadow-sm"
                                style="font-size: 0.8rem;">
                                <i class="bi bi-telephone-fill"></i> Gọi <?= htmlspecialchars($sdtDoiTac) ?>
                            </a>
                        </div>

                        <!-- Ghi chú / Điều kiện ưu đãi sỉ nếu có -->
                        <?php if (!empty($dh['ghi_chu'])): ?>
                            <div class="p-2 rounded bg-light border mb-2.5 small text-muted">
                                <i class="bi bi-chat-left-dots text-primary me-1"></i>
                                <span class="fw-semibold text-dark"><?= htmlspecialchars($dh['ghi_chu']) ?></span>
                            </div>
                        <?php endif; ?>

                        <!-- Hàng 4: Các nút hành động to rõ, dễ bấm ngón tay -->
                        <div class="d-flex flex-wrap gap-1.5 justify-content-end pt-2 border-top">
                            <?php if ($tabHienTai === 'dang_xu_ly'): ?>
                                <?php if ($vaiTro === 'nong_ho' && $dh['trang_thai'] === 'moi'): ?>
                                    <button type="button" class="btn btn-sm btn-success px-2.5 py-1.5 fw-bold rounded-pill flex-grow-1"
                                        onclick="moModalChotDon(<?= $dh['ma_don_hang'] ?>, '<?= ma_hien_thi('don_hang', $dh['ma_don_hang']) ?>', '<?= htmlspecialchars(addslashes($dh['ten_san_pham'])) ?>', '<?= htmlspecialchars($dh['don_vi_gia']) ?>', <?= (float) $dh['gia'] ?>, <?= (float) $dh['so_luong'] ?>, '<?= htmlspecialchars($dh['don_vi']) ?>', <?= $giaGoc ?>, '<?= htmlspecialchars(addslashes($dh['ghi_chu'] ?? '')) ?>')">
                                        <i class="bi bi-check-lg me-1"></i>Chốt Đơn
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning text-dark px-2.5 py-1.5 fw-bold rounded-pill"
                                        onclick="moModalBaoGia(<?= $dh['ma_don_hang'] ?>, '<?= ma_hien_thi('don_hang', $dh['ma_don_hang']) ?>', '<?= htmlspecialchars(addslashes($dh['ten_san_pham'])) ?>', '<?= htmlspecialchars($dh['don_vi_gia']) ?>', <?= (float) $dh['gia'] ?>, <?= (float) $dh['so_luong'] ?>, '<?= htmlspecialchars($dh['don_vi']) ?>', <?= $giaGoc ?>, '<?= htmlspecialchars(addslashes($dh['ghi_chu'] ?? '')) ?>')">
                                        <i class="bi bi-cash-stack me-1"></i>Báo Giá Lại
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger px-2.5 py-1.5 rounded-pill"
                                        title="Từ chối/Hủy"
                                        onclick="xacNhanHuyDon(<?= $dh['ma_don_hang'] ?>, '<?= ma_hien_thi('don_hang', $dh['ma_don_hang']) ?>')">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                <?php elseif ($vaiTro === 'nguoi_mua' && $dh['trang_thai'] === 'da_bao_gia'): ?>
                                    <button type="button" class="btn btn-sm btn-success px-2.5 py-1.5 fw-bold rounded-pill flex-grow-1"
                                        onclick="xacNhanDonHang(<?= $dh['ma_don_hang'] ?>, '<?= ma_hien_thi('don_hang', $dh['ma_don_hang']) ?>')">
                                        <i class="bi bi-check-lg me-1"></i>Đồng Ý Giá Mới
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger px-2.5 py-1.5 rounded-pill"
                                        onclick="xacNhanHuyDon(<?= $dh['ma_don_hang'] ?>, '<?= ma_hien_thi('don_hang', $dh['ma_don_hang']) ?>')">
                                        <i class="bi bi-x-lg me-1"></i>Hủy
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted small py-1.5 me-auto" style="font-size: 0.78rem;">Đang chờ đối tác phản hồi...</span>
                                    <button type="button" class="btn btn-sm btn-outline-danger px-2.5 py-1.5 rounded-pill" title="Hủy đơn"
                                        onclick="xacNhanHuyDon(<?= $dh['ma_don_hang'] ?>, '<?= ma_hien_thi('don_hang', $dh['ma_don_hang']) ?>')">
                                        <i class="bi bi-x-lg me-1"></i>Hủy
                                    </button>
                                <?php endif; ?>

                            <?php elseif ($tabHienTai === 'da_xac_nhan'): ?>
                                <?php if ($vaiTro === 'nong_ho'): ?>
                                    <button type="button" class="btn btn-sm btn-clean px-3.5 py-2 fw-bold rounded-pill w-100 shadow-sm"
                                        onclick="xacNhanHoanThanh(<?= $dh['ma_don_hang'] ?>, '<?= ma_hien_thi('don_hang', $dh['ma_don_hang']) ?>')">
                                        <i class="bi bi-check2-all me-1"></i>Giao Hàng Xong
                                    </button>
                                <?php else: ?>
                                    <span class="text-success fw-bold small py-1.5 me-auto">
                                        <i class="bi bi-truck me-1"></i>Đơn đã chốt giá - Đang giao nhận ngoài vườn
                                    </span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted small py-1">Giao dịch đã kết thúc</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- 2. GIAO DIỆN BẢNG TO TRÊN MÁY TÍNH / MÀN HÌNH LỚN -->
        <div class="bg-white rounded-3 border shadow-sm p-3 d-none d-md-block mb-4">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead>
                        <tr class="table-light">
                            <th>Mã Đơn</th>
                            <th style="min-width: 220px;">Nông Sản</th>
                            <th>Số Lượng</th>
                            <th style="min-width: 190px;">Mức Giá Thỏa Thuận</th>
                            <th style="min-width: 170px;">Thành Tiền</th>
                            <th>Đối Tác</th>
                            <th>Trạng Thái</th>
                            <th class="text-end" style="min-width: 200px;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dsDonHang as $dh):
                            $tongTien = (float) $dh['gia'] * (float) $dh['so_luong'];
                            $anhHienThi = duong_dan_anh_sp($dh['anh_san_pham'] ?? ($dh['anh'] ?? ''));
                            $trangThaiInfo = TRANG_THAI_DON[$dh['trang_thai']] ?? ['nhan' => $dh['trang_thai'], 'mau' => 'secondary'];
                            $laDonMoi = ($dh['trang_thai'] === 'moi');

                            // Nhận diện đơn sỉ số lượng lớn
                            $laDonSi = ($dh['don_vi'] === 'tấn')
                                || ($dh['don_vi'] === 'tạ' && $dh['so_luong'] >= 5)
                                || ($dh['don_vi'] === 'kg' && $dh['so_luong'] >= 500)
                                || ($dh['so_luong'] >= 500);

                            // Tính toán ưu đãi giảm giá sỉ (nếu có so với giá ban đầu hoặc giá niêm yết gốc)
                            $giaGoc = 0;
                            if (!empty($dh['gia_ban_dau']) && (float) $dh['gia_ban_dau'] > (float) $dh['gia']) {
                                $giaGoc = (float) $dh['gia_ban_dau'];
                            } elseif (!empty($dh['gia_goc_sp']) && (float) $dh['gia_goc_sp'] > (float) $dh['gia']) {
                                $giaGoc = (float) $dh['gia_goc_sp'];
                            }
                            $coGiamGia = ($giaGoc > 0 && $giaGoc > (float) $dh['gia']);
                            $mucGiamMoiDonVi = $coGiamGia ? ($giaGoc - (float) $dh['gia']) : 0;
                            $tongTietKiem = $coGiamGia ? ($mucGiamMoiDonVi * (float) $dh['so_luong']) : 0;
                            $phanTramGiam = $coGiamGia ? round(($mucGiamMoiDonVi / $giaGoc) * 100) : 0;

                            $daChotGia = in_array($dh['trang_thai'], ['da_xac_nhan', 'hoan_thanh']);
                            ?>
                            <tr
                                class="<?= $laDonMoi ? 'table-warning border-start border-4 border-danger' : ($daChotGia ? 'border-start border-3 border-success' : '') ?>">
                                <td>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <span class="badge bg-light text-dark border fw-bold fs-6">
                                            <?= ma_hien_thi('don_hang', $dh['ma_don_hang']) ?>
                                        </span>
                                        <?php if ($laDonSi): ?>
                                            <span class="badge bg-light text-secondary border fw-normal" style="font-size: 0.72rem;"
                                                title="Đơn mua sỉ từ 500 kg trở lên">
                                                Sỉ (≥ 500 kg)
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($laDonMoi): ?>
                                            <span class="badge bg-danger text-white fw-bold shadow-sm px-2 py-0.5"
                                                style="font-size: 0.72rem;">
                                                <i class="bi bi-bell-fill me-0.5"></i>MỚI
                                            </span>
                                        <?php elseif ($dh['trang_thai'] === 'da_bao_gia' && $vaiTro === 'nguoi_mua'): ?>
                                            <span class="badge bg-primary text-white fw-bold shadow-sm px-2 py-0.5"
                                                style="font-size: 0.72rem;">
                                                GIÁ MỚI
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-muted small mt-1">
                                        <?= dinh_dang_ngay($dh['ngay_tao']) ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?= $anhHienThi ?>" alt="<?= htmlspecialchars($dh['ten_san_pham']) ?>"
                                            class="rounded border" style="width: 48px; height: 48px; object-fit: cover;"
                                            onerror="this.src='assets/images/nong_san_mac_dinh.svg'">
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 180px;">
                                                <?= htmlspecialchars($dh['ten_san_pham']) ?>
                                            </h6>
                                            <?php if (!empty($dh['ghi_chu'])): ?>
                                                <div class="text-muted text-truncate small" style="max-width: 180px;"
                                                    title="<?= htmlspecialchars($dh['ghi_chu']) ?>">
                                                    <i
                                                        class="bi bi-chat-left-dots text-primary me-1"></i><?= htmlspecialchars($dh['ghi_chu']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <strong class="text-dark fs-6"><?= dinh_dang_so_luong($dh['so_luong']) ?></strong>
                                    <span class="text-muted fw-bold"><?= htmlspecialchars($dh['don_vi']) ?></span>
                                    <?php if ($laDonSi): ?>
                                        <div class="small text-muted" style="font-size: 0.75rem;">Sỉ (≥ 500 kg)</div>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($daChotGia): ?>
                                        <div class="p-2 rounded-3 bg-success bg-opacity-10 border border-success-subtle">
                                            <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                                <span class="badge bg-success text-white py-0.5 px-1.5 fw-bold"
                                                    style="font-size: 0.7rem;">
                                                    GIÁ CHỐT
                                                </span>
                                                <?php if ($coGiamGia): ?>
                                                    <span class="nh-discount-tag fw-bold" style="font-size: 0.72rem;">
                                                        -<?= $phanTramGiam ?>%
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="fw-bold text-success fs-6">
                                                <?= dinh_dang_tien($dh['gia']) ?>
                                                <span
                                                    class="text-muted small fw-normal">/<?= htmlspecialchars($dh['don_vi_gia']) ?></span>
                                            </div>
                                            <?php if ($coGiamGia): ?>
                                                <div class="text-muted small" style="font-size: 0.75rem;">
                                                    Gốc: <del><?= dinh_dang_tien($giaGoc) ?></del>
                                                </div>
                                                <div class="text-success fw-semibold" style="font-size: 0.72rem;">
                                                    Giảm: -<?= dinh_dang_tien($mucGiamMoiDonVi) ?>
                                                    đ/<?= htmlspecialchars($dh['don_vi_gia']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <?php if ($dh['gia'] > 0): ?>
                                            <div>
                                                <strong class="text-success fs-6"><?= dinh_dang_tien($dh['gia']) ?></strong>
                                                <span class="text-muted fw-bold">/<?= htmlspecialchars($dh['don_vi_gia']) ?></span>
                                                <?php if ($coGiamGia): ?>
                                                    <div class="text-muted small" style="font-size: 0.75rem;">
                                                        Gốc: <del><?= dinh_dang_tien($giaGoc) ?></del>
                                                    </div>
                                                    <span class="nh-discount-tag fw-bold mt-1 d-inline-block" style="font-size: 0.72rem;">
                                                        -<?= $phanTramGiam ?>% (-<?= dinh_dang_tien($mucGiamMoiDonVi) ?> đ)
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="badge bg-warning bg-opacity-25 text-dark border border-warning fs-6">Chờ báo
                                                giá</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($daChotGia): ?>
                                        <h6 class="fw-bold text-success mb-0 fs-5">
                                            <?= dinh_dang_tien($tongTien) ?>
                                        </h6>
                                        <?php if ($coGiamGia && $tongTietKiem > 0): ?>
                                            <small class="text-success d-block fw-semibold mt-1" style="font-size: 0.75rem;">
                                                Tiết kiệm: <?= dinh_dang_tien($tongTietKiem) ?> đ (-<?= $phanTramGiam ?>%)
                                            </small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <strong class="text-dark fs-6"><?= dinh_dang_tien($tongTien) ?></strong>
                                        <?php if ($coGiamGia && $tongTietKiem > 0): ?>
                                            <small class="text-success d-block fw-semibold mt-1" style="font-size: 0.75rem;">
                                                Tiết kiệm: <?= dinh_dang_tien($tongTietKiem) ?> đ (-<?= $phanTramGiam ?>%)
                                            </small>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($vaiTro === 'nong_ho'): ?>
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 180px;">
                                            <?= htmlspecialchars($dh['ten_nguoi_mua']) ?>
                                        </div>
                                        <div class="mt-1">
                                            <a href="#" class="nh-phone-badge text-decoration-none">
                                                <i class="bi bi-telephone-fill"></i><?= htmlspecialchars($dh['sdt_nguoi_mua']) ?>
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 180px;">
                                            <?= htmlspecialchars($dh['ten_nong_ho']) ?>
                                        </div>
                                        <div class="mt-1">
                                            <a href="#" class="nh-phone-badge text-decoration-none">
                                                <i class="bi bi-telephone-fill"></i><?= htmlspecialchars($dh['sdt_nong_ho']) ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <span class="badge bg-<?= $trangThaiInfo['mau'] ?> px-2 py-1 fs-6">
                                        <?= $trangThaiInfo['nhan'] ?>
                                    </span>
                                </td>

                                <td class="text-end">
                                    <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                                        <?php if ($tabHienTai === 'dang_xu_ly'): ?>
                                            <?php if ($vaiTro === 'nong_ho' && $dh['trang_thai'] === 'moi'): ?>
                                                <button type="button" class="btn btn-sm btn-success px-3 py-1 fw-bold"
                                                    onclick="moModalChotDon(<?= $dh['ma_don_hang'] ?>, '<?= ma_hien_thi('don_hang', $dh['ma_don_hang']) ?>', '<?= htmlspecialchars(addslashes($dh['ten_san_pham'])) ?>', '<?= htmlspecialchars($dh['don_vi_gia']) ?>', <?= (float) $dh['gia'] ?>, <?= (float) $dh['so_luong'] ?>, '<?= htmlspecialchars($dh['don_vi']) ?>', <?= $giaGoc ?>, '<?= htmlspecialchars(addslashes($dh['ghi_chu'] ?? '')) ?>')">
                                                    <i class="bi bi-check-lg me-1"></i>Chốt Đơn
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning text-dark px-3 py-1 fw-bold"
                                                    onclick="moModalBaoGia(<?= $dh['ma_don_hang'] ?>, '<?= ma_hien_thi('don_hang', $dh['ma_don_hang']) ?>', '<?= htmlspecialchars(addslashes($dh['ten_san_pham'])) ?>', '<?= htmlspecialchars($dh['don_vi_gia']) ?>', <?= (float) $dh['gia'] ?>, <?= (float) $dh['so_luong'] ?>, '<?= htmlspecialchars($dh['don_vi']) ?>', <?= $giaGoc ?>, '<?= htmlspecialchars(addslashes($dh['ghi_chu'] ?? '')) ?>')">
                                                    <i class="bi bi-cash-stack me-1"></i>Báo Giá Lại / Giảm Sỉ
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1" title="Hủy đơn"
                                                    onclick="xacNhanHuyDon(<?= $dh['ma_don_hang'] ?>, '<?= ma_hien_thi('don_hang', $dh['ma_don_hang']) ?>')">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            <?php elseif ($vaiTro === 'nguoi_mua' && $dh['trang_thai'] === 'da_bao_gia'): ?>
                                                <button type="button" class="btn btn-sm btn-success px-3 py-1"
                                                    onclick="xacNhanDonHang(<?= $dh['ma_don_hang'] ?>, '<?= ma_hien_thi('don_hang', $dh['ma_don_hang']) ?>')">
                                                    <i class="bi bi-check-lg me-1"></i>Đồng Ý Giá Mới
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger px-3 py-1"
                                                    onclick="xacNhanHuyDon(<?= $dh['ma_don_hang'] ?>, '<?= ma_hien_thi('don_hang', $dh['ma_don_hang']) ?>')">
                                                    <i class="bi bi-x-lg"></i> Hủy
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted py-1 d-inline-block small me-2">Đang chờ phản hồi</span>
                                                <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1" title="Hủy đơn"
                                                    onclick="xacNhanHuyDon(<?= $dh['ma_don_hang'] ?>, '<?= ma_hien_thi('don_hang', $dh['ma_don_hang']) ?>')">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            <?php endif; ?>

                                        <?php elseif ($tabHienTai === 'da_xac_nhan'): ?>
                                            <?php if ($vaiTro === 'nong_ho'): ?>
                                                <button type="button"
                                                    class="btn btn-sm btn-clean px-3 py-1 fw-bold rounded-pill shadow-sm"
                                                    onclick="xacNhanHoanThanh(<?= $dh['ma_don_hang'] ?>, '<?= ma_hien_thi('don_hang', $dh['ma_don_hang']) ?>')">
                                                    <i class="bi bi-check2-all me-1"></i>Giao Hàng Xong
                                                </button>
                                            <?php else: ?>
                                                <span
                                                    class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-pill fw-bold">
                                                    <i class="bi bi-truck me-1"></i>Đang giao nhận ngoài vườn
                                                </span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted py-1 small">Giao dịch đã kết thúc</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- MODAL BÁO GIÁ LẠI (TỰ NHẬP TIỀN HOẶC % MUỐN GIẢM) -->
<div class="modal fade" id="modalBaoGiaNongHo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-3">
            <form action="index.php?trang=don_hang_bao_gia" method="POST">
                <input type="hidden" name="ma_don_hang" id="bgMaDonHang">
                <input type="hidden" name="gia_ban_dau" id="bgGiaBanDauInput">
                <div class="modal-header py-2.5 bg-light border-bottom">
                    <h6 class="modal-title fw-bold text-dark mb-0">Báo giá lại cho người mua</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <!-- Thông tin đơn hàng -->
                    <div class="p-2.5 bg-light rounded border mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">Đơn hàng: <strong id="bgMaHienThi"
                                    class="text-dark"></strong></span>
                            <span id="bgBadgeDonSi" class="badge bg-light text-secondary border d-none">Sỉ (≥ 500
                                kg)</span>
                        </div>
                        <div class="fw-bold text-dark mb-1" id="bgTenSP"></div>
                        <div class="row g-2 small text-muted">
                            <div class="col-6">
                                Đặt mua: <strong class="text-dark" id="bgSoLuongHienThi">0 kg</strong>
                            </div>
                            <div class="col-6">
                                Giá niêm yết: <strong class="text-dark" id="bgGiaGocHienThi">0 đ/kg</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Ô tự nhập mức giảm (tiền hoặc %) -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Mức giảm muốn bớt (tùy chọn)</label>
                        <div class="input-group">
                            <input type="number" step="any" min="0" id="bgGiaTriGiam" class="form-control"
                                placeholder="Nhập số tiền hoặc % muốn bớt" oninput="dongBoMucGiamSangGia('bg')">
                            <select id="bgLoaiGiam" class="form-select flex-grow-0" style="width: 120px;"
                                onchange="dongBoMucGiamSangGia('bg')">
                                <option value="vnd">đ/<span id="bgDonViGiam">kg</span></option>
                                <option value="percent">%</option>
                            </select>
                        </div>
                    </div>

                    <!-- Ô mức giá báo lại -->
                    <div class="mb-3">
                        <label for="bgGiaMoi" class="form-label small fw-bold text-dark mb-1">
                            Mức giá báo lại (đ/<span id="bgDonViGia">kg</span>) <span class="text-danger">*</span>
                        </label>
                        <input type="number" step="any" min="1" name="gia_moi" id="bgGiaMoi"
                            class="form-control form-control-lg fw-bold text-success" required
                            placeholder="Giá báo cho khách" oninput="dongBoGiaSangMucGiam('bg')">
                    </div>

                    <!-- Xem trước tính toán -->
                    <div
                        class="p-2.5 rounded bg-light border mb-3 small d-flex justify-content-between align-items-center">
                        <div>Thành tiền: <strong class="text-dark fs-6" id="previewTongTienMoi">0 đ</strong></div>
                        <div id="previewSoTienGiam" class="text-muted">Bớt: 0 đ</div>
                    </div>

                    <!-- Ghi chú -->
                    <div class="mb-1">
                        <label for="bgGhiChu" class="form-label small text-muted mb-1">Ghi chú (không bắt buộc)</label>
                        <input type="text" name="ghi_chu" id="bgGhiChu" class="form-control"
                            placeholder="Ví dụ: Giá sỉ lấy từ 1 tấn, bốc hàng sáng mai...">
                    </div>
                </div>

                <div class="modal-footer py-2 bg-light border-top">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">Gửi báo giá</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL CHỐT ĐƠN HÀNG (TỰ NHẬP TIỀN HOẶC % MUỐN GIẢM) -->
<div class="modal fade" id="modalChotDonNongHo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-3">
            <form action="index.php?trang=don_hang_xac_nhan" method="POST">
                <input type="hidden" name="ma_don_hang" id="cdMaDonHang">
                <input type="hidden" name="gia_ban_dau" id="cdGiaBanDauInput">
                <div class="modal-header py-2.5 bg-success text-white">
                    <h6 class="modal-title fw-bold text-white mb-0">Xác nhận chốt đơn</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <!-- Thông tin đơn hàng -->
                    <div class="p-2.5 bg-light rounded border mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">Đơn hàng: <strong id="cdMaHienThi"
                                    class="text-dark"></strong></span>
                            <span id="cdBadgeDonSi" class="badge bg-light text-secondary border d-none">Sỉ (≥ 500
                                kg)</span>
                        </div>
                        <div class="fw-bold text-dark mb-1" id="cdTenSP"></div>
                        <div class="row g-2 small text-muted">
                            <div class="col-6">
                                Đặt mua: <strong class="text-dark" id="cdSoLuongHienThi">0 kg</strong>
                            </div>
                            <div class="col-6">
                                Giá ban đầu: <strong class="text-dark" id="cdGiaBanDauHienThi">0 đ/kg</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Ô tự nhập mức giảm (tiền hoặc %) -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Mức giảm muốn bớt (tùy chọn)</label>
                        <div class="input-group">
                            <input type="number" step="any" min="0" id="cdGiaTriGiam" class="form-control"
                                placeholder="Nhập số tiền hoặc % muốn bớt" oninput="dongBoMucGiamSangGia('cd')">
                            <select id="cdLoaiGiam" class="form-select flex-grow-0" style="width: 120px;"
                                onchange="dongBoMucGiamSangGia('cd')">
                                <option value="vnd">đ/<span id="cdDonViGiam">kg</span></option>
                                <option value="percent">%</option>
                            </select>
                        </div>
                    </div>

                    <!-- Ô mức giá chốt cuối cùng -->
                    <div class="mb-3">
                        <label for="cdGiaChot" class="form-label small fw-bold text-dark mb-1">
                            Mức giá chốt đơn (đ/<span id="cdDonViGia">kg</span>) <span class="text-danger">*</span>
                        </label>
                        <input type="number" step="any" min="1" name="gia_chot" id="cdGiaChot"
                            class="form-control form-control-lg fw-bold text-success" required
                            placeholder="Giá chốt cho khách" oninput="dongBoGiaSangMucGiam('cd')">
                    </div>

                    <!-- Xem trước tính toán -->
                    <div
                        class="p-2.5 rounded bg-light border mb-3 small d-flex justify-content-between align-items-center">
                        <div>Thành tiền: <strong class="text-dark fs-6" id="cdPreviewTongTien">0 đ</strong></div>
                        <div id="cdPreviewSoTienGiam" class="text-muted">Bớt: 0 đ</div>
                    </div>

                    <!-- Ghi chú -->
                    <div class="mb-1">
                        <label for="cdGhiChu" class="form-label small text-muted mb-1">Ghi chú (không bắt buộc)</label>
                        <input type="text" name="ghi_chu" id="cdGhiChu" class="form-control"
                            placeholder="Ví dụ: Giá sỉ lấy từ 1 tấn, bốc hàng sáng mai...">
                    </div>
                </div>

                <div class="modal-footer py-2 bg-light border-top">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">Xác nhận chốt đơn</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Biến lưu thông tin đơn hiện tại
    let modalCurrentGiaGoc = 0;
    let modalCurrentSoLuong = 0;
    let modalCurrentDonViGia = 'kg';

    let cdCurrentGiaBanDau = 0;
    let cdCurrentSoLuong = 0;
    let cdCurrentDonViGia = 'kg';

    // Mở modal chốt đơn (Nông Hộ có thể tự nhập số tiền hoặc % muốn giảm)
    function moModalChotDon(maDonHang, maHienThi, tenSanPham, donViGia, giaHienTai, soLuong, donVi, giaGoc, ghiChuHienTai) {
        document.getElementById('cdMaDonHang').value = maDonHang;
        document.getElementById('cdMaHienThi').textContent = maHienThi;
        document.getElementById('cdTenSP').textContent = tenSanPham;
        document.getElementById('cdDonViGia').textContent = donViGia;
        document.getElementById('cdDonViGiam').textContent = donViGia;

        cdCurrentGiaBanDau = Number(giaHienTai) > 0 ? Number(giaHienTai) : (Number(giaGoc) > 0 ? Number(giaGoc) : 0);
        cdCurrentSoLuong = Number(soLuong) > 0 ? Number(soLuong) : 1;
        cdCurrentDonViGia = donViGia;

        document.getElementById('cdSoLuongHienThi').textContent = Number(soLuong).toLocaleString('vi-VN') + ' ' + donVi;
        document.getElementById('cdGiaBanDauHienThi').textContent = Number(cdCurrentGiaBanDau).toLocaleString('vi-VN') + ' đ/' + donViGia;

        const badgeDonSi = document.getElementById('cdBadgeDonSi');
        const laDonSi = (donVi === 'tấn') || (donVi === 'tạ' && soLuong >= 5) || (soLuong >= 500);
        if (laDonSi) {
            badgeDonSi.classList.remove('d-none');
        } else {
            badgeDonSi.classList.add('d-none');
        }

        document.getElementById('cdGiaTriGiam').value = '';
        document.getElementById('cdLoaiGiam').value = 'vnd';
        document.getElementById('cdGiaBanDauInput').value = cdCurrentGiaBanDau;
        document.getElementById('cdGiaChot').value = cdCurrentGiaBanDau;
        document.getElementById('cdGhiChu').value = ghiChuHienTai ? ghiChuHienTai : '';

        capNhatPreview('cd');

        const modal = new bootstrap.Modal(document.getElementById('modalChotDonNongHo'));
        modal.show();
    }

    // Mở modal báo giá lại
    function moModalBaoGia(maDonHang, maHienThi, tenSanPham, donViGia, giaHienTai, soLuong, donVi, giaGoc, ghiChuHienTai) {
        document.getElementById('bgMaDonHang').value = maDonHang;
        document.getElementById('bgMaHienThi').textContent = maHienThi;
        document.getElementById('bgTenSP').textContent = tenSanPham;
        document.getElementById('bgDonViGia').textContent = donViGia;
        document.getElementById('bgDonViGiam').textContent = donViGia;

        modalCurrentGiaGoc = Number(giaGoc) > 0 ? Number(giaGoc) : Number(giaHienTai);
        modalCurrentSoLuong = Number(soLuong) > 0 ? Number(soLuong) : 1;
        modalCurrentDonViGia = donViGia;

        document.getElementById('bgSoLuongHienThi').textContent = Number(soLuong).toLocaleString('vi-VN') + ' ' + donVi;
        document.getElementById('bgGiaGocHienThi').textContent = Number(modalCurrentGiaGoc).toLocaleString('vi-VN') + ' đ/' + donViGia;

        const badgeDonSi = document.getElementById('bgBadgeDonSi');
        const laDonSi = (donVi === 'tấn') || (donVi === 'tạ' && soLuong >= 5) || (soLuong >= 500);
        if (laDonSi) {
            badgeDonSi.classList.remove('d-none');
        } else {
            badgeDonSi.classList.add('d-none');
        }

        document.getElementById('bgGiaTriGiam').value = '';
        document.getElementById('bgLoaiGiam').value = 'vnd';
        document.getElementById('bgGiaBanDauInput').value = modalCurrentGiaGoc;
        document.getElementById('bgGiaMoi').value = giaHienTai > 0 ? giaHienTai : modalCurrentGiaGoc;
        document.getElementById('bgGhiChu').value = ghiChuHienTai ? ghiChuHienTai : '';

        capNhatPreview('bg');

        const modal = new bootstrap.Modal(document.getElementById('modalBaoGiaNongHo'));
        modal.show();
    }

    // Khi người dùng gõ vào ô "Mức giảm muốn bớt" (tiền hoặc %)
    function dongBoMucGiamSangGia(prefix) {
        const baseGia = (prefix === 'cd') ? cdCurrentGiaBanDau : modalCurrentGiaGoc;
        const inputGiam = document.getElementById(prefix + 'GiaTriGiam');
        const loaiGiam = document.getElementById(prefix + 'LoaiGiam').value;
        const inputGia = document.getElementById(prefix === 'cd' ? 'cdGiaChot' : 'bgGiaMoi');
        const valGiam = Number(inputGiam.value) || 0;

        let giaMoi = baseGia;
        if (valGiam > 0) {
            if (loaiGiam === 'vnd') {
                giaMoi = Math.max(0, baseGia - valGiam);
            } else if (loaiGiam === 'percent') {
                const phanTram = Math.min(100, Math.max(0, valGiam));
                giaMoi = Math.max(0, Math.round(baseGia * (1 - phanTram / 100)));
            }
        }
        inputGia.value = giaMoi;
        capNhatPreview(prefix);
    }

    // Khi người dùng gõ trực tiếp vào ô "Mức giá"
    function dongBoGiaSangMucGiam(prefix) {
        const baseGia = (prefix === 'cd') ? cdCurrentGiaBanDau : modalCurrentGiaGoc;
        const inputGiam = document.getElementById(prefix + 'GiaTriGiam');
        const loaiGiam = document.getElementById(prefix + 'LoaiGiam').value;
        const inputGia = document.getElementById(prefix === 'cd' ? 'cdGiaChot' : 'bgGiaMoi');
        const giaMoi = Number(inputGia.value) || 0;

        if (baseGia > 0 && giaMoi < baseGia) {
            const chenhLech = baseGia - giaMoi;
            if (loaiGiam === 'vnd') {
                inputGiam.value = chenhLech;
            } else if (loaiGiam === 'percent') {
                inputGiam.value = Math.round((chenhLech / baseGia) * 100);
            }
        } else {
            inputGiam.value = '';
        }
        capNhatPreview(prefix);
    }

    // Cập nhật khung xem trước tính toán gọn gàng
    function capNhatPreview(prefix) {
        const baseGia = (prefix === 'cd') ? cdCurrentGiaBanDau : modalCurrentGiaGoc;
        const soLuong = (prefix === 'cd') ? cdCurrentSoLuong : modalCurrentSoLuong;
        const donViGia = (prefix === 'cd') ? cdCurrentDonViGia : modalCurrentDonViGia;
        const inputGia = document.getElementById(prefix === 'cd' ? 'cdGiaChot' : 'bgGiaMoi');
        const boxTongTien = document.getElementById(prefix === 'cd' ? 'cdPreviewTongTien' : 'previewTongTienMoi');
        const boxSoTienGiam = document.getElementById(prefix === 'cd' ? 'cdPreviewSoTienGiam' : 'previewSoTienGiam');

        const giaMoi = Number(inputGia.value) || 0;
        const tongTien = giaMoi * soLuong;

        boxTongTien.textContent = tongTien.toLocaleString('vi-VN') + ' đ';

        if (baseGia > 0 && giaMoi > 0) {
            if (giaMoi < baseGia) {
                const bớtDonVi = baseGia - giaMoi;
                const tongTietKiem = bớtDonVi * soLuong;
                const phanTram = Math.round((bớtDonVi / baseGia) * 100);
                boxSoTienGiam.innerHTML = `<span class="text-success">Bớt ${bớtDonVi.toLocaleString('vi-VN')} đ/${donViGia} (-${phanTram}%) ➔ Tiết kiệm: <strong>${tongTietKiem.toLocaleString('vi-VN')} đ</strong></span>`;
            } else if (giaMoi > baseGia) {
                const tangGia = giaMoi - baseGia;
                boxSoTienGiam.innerHTML = `<span>Tăng ${tangGia.toLocaleString('vi-VN')} đ/${donViGia}</span>`;
            } else {
                boxSoTienGiam.innerHTML = `<span>Giữ nguyên giá</span>`;
            }
        } else {
            boxSoTienGiam.innerHTML = `<span>Bớt: 0 đ</span>`;
        }
    }

    // Xác nhận đơn hàng (Dành cho Người Mua chấp nhận báo giá mới)
    function xacNhanDonHang(maDonHang, maHienThi) {
        moModalXacNhan(
            'Xác Nhận Đơn Hàng',
            `Đồng ý nhận đơn <strong>${maHienThi}</strong> theo mức giá mới này?`,
            function () {
                window.location.href = 'index.php?trang=don_hang_xac_nhan&id=' + maDonHang;
            },
            'Đồng Ý Giá Mới',
            'btn-success'
        );
    }

    // Xác nhận hoàn thành đơn
    function xacNhanHoanThanh(maDonHang, maHienThi) {
        moModalXacNhan(
            'Hoàn Tất Giao Hàng',
            `Xác nhận đơn hàng <strong>${maHienThi}</strong> đã giao dịch thành công?`,
            function () {
                window.location.href = 'index.php?trang=don_hang_hoan_thanh&id=' + maDonHang;
            },
            'Xác Nhận Xong',
            'btn-success'
        );
    }

    // Xác nhận hủy đơn
    function xacNhanHuyDon(maDonHang, maHienThi) {
        moModalXacNhan(
            'Hủy Đơn Hàng',
            `Bạn có chắc chắn muốn hủy đơn hàng <strong>${maHienThi}</strong>?`,
            function () {
                window.location.href = 'index.php?trang=don_hang_huy&id=' + maDonHang;
            },
            'Hủy Đơn',
            'btn-danger'
        );
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
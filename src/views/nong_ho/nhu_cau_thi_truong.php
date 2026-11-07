<?php
// views/nong_ho/nhu_cau_thi_truong.php
// Xem nhu cầu thu mua thị trường & Gợi ý ghép nối thông minh % cho Nông Hộ - Tối ưu Mobile UX
$dsGhepNoi   = $dsGhepNoi ?? [];
$dsThiTruong = $dsThiTruong ?? [];

require_once __DIR__ . '/../layouts/header.php';
$nguoiDung = nguoi_dung_hien_tai();
$tabNhuCau = $_GET['tab_nc'] ?? 'ghep_noi';
?>

<div class="container-fluid px-2 px-sm-3 px-lg-4 py-2">
    <!-- TIÊU ĐỀ & VỊ TRÍ NÔNG HỘ -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-2.5">
        <div>
            <h4 class="fw-bold mb-1" style="color: #064e3b;">
                <i class="bi bi-lightning-charge-fill text-warning me-2"></i>Nhu Cầu Mua Và Ghép Nối
            </h4>
        </div>
        <div>
            <span class="badge bg-success bg-opacity-10 text-success border border-success p-2 fs-6 rounded-pill">
                <i class="bi bi-geo-alt-fill text-danger me-1"></i>Vị trí vườn: <strong><?= htmlspecialchars($nguoiDung['tinh'] ?? 'Chưa cập nhật') ?></strong>
            </span>
        </div>
    </div>

    <!-- 2 TAB CHUYỂN ĐỔI GỌN GÀNG: GHÉP NỐI THÔNG MINH (%) & TOÀN BỘ THỊ TRƯỜNG -->
    <div class="nh-order-tabs-box mb-3 bg-white p-1.5 rounded-pill border shadow-sm d-flex">
        <a href="index.php?trang=nong_ho_nhu_cau&tab_nc=ghep_noi" 
           class="nh-order-tab-item flex-fill text-center py-2 px-2 text-decoration-none rounded-pill fw-bold <?= $tabNhuCau === 'ghep_noi' ? 'active bg-success text-white shadow-sm' : 'text-secondary' ?>">
            <i class="bi bi-patch-check-fill me-1 text-warning"></i>
            <span>Khớp vườn của bạn (<?= count($dsGhepNoi) ?>)</span>
        </a>
        <a href="index.php?trang=nong_ho_nhu_cau&tab_nc=thi_truong" 
           class="nh-order-tab-item flex-fill text-center py-2 px-2 text-decoration-none rounded-pill fw-bold <?= $tabNhuCau === 'thi_truong' ? 'active bg-primary text-white shadow-sm' : 'text-secondary' ?>">
            <i class="bi bi-globe2 me-1"></i>
            <span>Tất cả nhu cầu (<?= count($dsThiTruong) ?>)</span>
        </a>
    </div>

    <!-- NỘI DUNG THEO TAB -->
    <?php if ($tabNhuCau === 'ghep_noi'): ?>
        <!-- TAB 1: GỢI Ý GHÉP NỐI THÔNG MINH (%) -->
        <div class="bg-white rounded-4 border shadow-sm p-3 p-md-4 mb-4">
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between border-bottom pb-2.5 mb-3 gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark px-2.5 py-1 fw-bold fs-6 rounded-pill">
                        <i class="bi bi-lightning-fill me-1"></i>TIN MUA KHỚP NHẤT
                    </span>
                </div>
            </div>

            <?php if (empty($dsGhepNoi)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-search fs-1 d-block mb-2 text-muted"></i>
                    <h6 class="fw-bold text-dark fs-5">Chưa có tin thu mua nào khớp với nông sản vườn của bạn</h6>
                    <p class="text-muted mb-3 small">Hệ thống sẽ tự động ghép nối ngay khi có người mua đăng tin cần loại nông sản bạn đang trồng.</p>
                    <a href="index.php?trang=nong_ho_nhu_cau&tab_nc=thi_truong" class="btn btn-outline-success fw-bold rounded-pill px-3 py-1.5 btn-sm">
                        <i class="bi bi-globe2 me-1"></i> Xem toàn bộ nhu cầu thị trường
                    </a>
                </div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                    <?php foreach ($dsGhepNoi as $gn): 
                        $phanTram = (int)($gn['ti_le_phu_hop'] ?? $gn['ty_le_matching'] ?? 0);
                        $spKhop   = $gn['san_pham_khop'] ?? null;
                        
                        if ($phanTram >= 80) {
                            $bannerClass = 'nh-match-banner-high';
                            $cardBorder  = 'border: 2px solid #10b981;';
                            $tieuDeKhop  = 'RẤT PHÙ HỢP (' . $phanTram . '%)';
                            $iconKhop    = 'bi-check-circle-fill';
                        } elseif ($phanTram >= 50) {
                            $bannerClass = 'nh-match-banner-medium';
                            $cardBorder  = 'border: 2px solid #f59e0b;';
                            $tieuDeKhop  = 'KHÁ PHÙ HỢP (' . $phanTram . '%)';
                            $iconKhop    = 'bi-lightning-charge-fill';
                        } else {
                            $bannerClass = 'nh-match-banner-low';
                            $cardBorder  = 'border: 1px solid #94a3b8;';
                            $tieuDeKhop  = 'TIỀM NĂNG (' . $phanTram . '%)';
                            $iconKhop    = 'bi-info-circle-fill';
                        }
                        $cungTinh = (!empty($nguoiDung['tinh']) && !empty($gn['tinh']) && mb_strtolower(trim($nguoiDung['tinh'])) === mb_strtolower(trim($gn['tinh'])));
                    ?>
                        <div class="col">
                            <div class="nh-matching-card shadow-sm h-100 d-flex flex-column rounded-3 overflow-hidden bg-white" style="<?= $cardBorder ?>">
                                <!-- BANNER TỶ LỆ GHÉP NỐI % TO RÕ RÀNG -->
                                <div class="<?= $bannerClass ?> py-2 px-3 d-flex align-items-center justify-content-between">
                                    <span class="fw-bold"><i class="bi <?= $iconKhop ?> me-1"></i><?= $tieuDeKhop ?></span>
                                    <span class="badge bg-white text-dark fw-bold"><?= ma_hien_thi('nhu_cau', $gn['ma_nhu_cau']) ?></span>
                                </div>

                                <div class="p-3 d-flex flex-column flex-grow-1">
                                    <!-- NÔNG SẢN CẦN MUA -->
                                    <h5 class="fw-bold text-dark mb-1" style="font-size: 1.15rem;">
                                        Cần mua: <span class="text-success"><?= htmlspecialchars($gn['ten_nong_san'] ?? $gn['ten_danh_muc']) ?></span>
                                    </h5>
                                    <?php if (!empty($gn['ten_nong_san'])): ?>
                                        <div class="small text-muted mb-2">
                                            Ngành hàng: <span class="fw-semibold text-dark"><?= htmlspecialchars($gn['ten_danh_muc']) ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- KHỚP VỚI VƯỜN BẠN -->
                                    <?php if (!empty($spKhop)): ?>
                                        <div class="p-2.5 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25 mb-3 small">
                                            <div class="fw-bold text-success mb-1 d-flex align-items-center gap-1">
                                                Khớp với vườn của bạn:
                                            </div>
                                            <div class="text-dark fw-bold fs-6"><?= htmlspecialchars($spKhop['ten']) ?></div>
                                            <div class="text-muted" style="font-size: 0.82rem;">
                                                Kho bạn có: <strong class="text-dark"><?= dinh_dang_kho_thong_minh($spKhop['san_luong'], $spKhop['don_vi_san_luong']) ?></strong>
                                                <?php 
                                                $slKhoQuyDoi = quy_doi_don_vi($spKhop['san_luong'], $spKhop['don_vi_san_luong'], $gn['don_vi']);
                                                if ($slKhoQuyDoi >= $gn['so_luong']): 
                                                ?>
                                                    <span class="badge bg-success text-white ms-1">Đủ hàng</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- CHỈ SỐ YÊU CẦU CỦA ĐƠN VỊ MUA -->
                                    <div class="text-dark mb-3 small">
                                        <div class="d-flex justify-content-between py-1.5 border-bottom">
                                            <span class="text-muted">Số lượng cần:</span>
                                            <strong class="text-dark fs-6"><?= dinh_dang_so_luong($gn['so_luong']) ?> <?= htmlspecialchars($gn['don_vi']) ?></strong>
                                        </div>
                                        <div class="d-flex justify-content-between py-1.5 border-bottom">
                                            <span class="text-muted">Giá mua tối đa:</span>
                                            <strong class="text-success fs-6"><?= dinh_dang_tien($gn['gia_toi_da']) ?></strong>
                                        </div>
                                        <div class="d-flex justify-content-between py-1.5 border-bottom">
                                            <span class="text-muted">Nơi giao hàng:</span>
                                            <span>
                                                <strong><?= htmlspecialchars($gn['tinh']) ?></strong>
                                                <?php if ($cungTinh): ?>
                                                    <span class="badge bg-success bg-opacity-25 text-success ms-1">Cùng tỉnh</span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between py-1.5">
                                            <span class="text-muted">Hạn gom hàng:</span>
                                            <strong><?= dinh_dang_ngay($gn['han_can']) ?></strong>
                                        </div>
                                    </div>

                                    <!-- THÔNG TIN NGƯỜI MUA & NÚT GỌI ĐIỆN 1 CHẠM -->
                                    <div class="mt-auto pt-3 border-top">
                                        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                            <div class="text-truncate">
                                                <small class="text-muted d-block" style="font-size: 0.72rem;">Đơn vị thu mua:</small>
                                                <strong class="text-dark text-truncate d-block" title="<?= htmlspecialchars($gn['ten_nguoi_mua']) ?>">
                                                    <?= htmlspecialchars($gn['ten_nguoi_mua']) ?>
                                                </strong>
                                                <?php if (!empty($gn['dia_chi_nguoi_mua'])): ?>
                                                    <small class="text-muted text-truncate d-block" style="font-size: 0.75rem;" title="<?= htmlspecialchars($gn['dia_chi_nguoi_mua']) ?>">
                                                        <i class="bi bi-geo-alt text-danger me-0.5"></i><?= htmlspecialchars($gn['dia_chi_nguoi_mua']) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- NÚT GỌI ĐIỆN CHỐT ĐƠN TO RÕ BẰNG NGÓN TAY -->
                                        <a href="#<?= htmlspecialchars($gn['sdt_nguoi_mua']) ?>" 
                                           class="btn btn-clean w-100 py-2.5 fw-bold rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-telephone-fill fs-5"></i>
                                            <span>Liên Hệ Ngay: <?= htmlspecialchars($gn['sdt_nguoi_mua']) ?></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <!-- TAB 2: TOÀN BỘ NHU CẦU THU MUA THỊ TRƯỜNG -->
        <div class="bg-white rounded-4 border shadow-sm p-3 p-md-4 mb-4">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-2.5 mb-3">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-globe2 text-primary me-1"></i> Tất Cả Nhu Cầu Thu Mua (<?= count($dsThiTruong) ?> nhu cầu)
                </h5>
            </div>

            <?php if (empty($dsThiTruong)): ?>
                <div class="text-center py-5 text-muted">
                    <p class="mb-0">Hiện tại thị trường chưa có tin nhu cầu nào.</p>
                </div>
            <?php else: ?>
                <!-- 1. GIAO DIỆN THẺ DI ĐỘNG (CHUẨN UX MOBILE, KHÔNG BỊ TRÀN BẢNG) -->
                <div class="d-block d-md-none">
                    <?php foreach ($dsThiTruong as $tt): ?>
                        <div class="card mb-3 border shadow-sm rounded-3">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-light text-dark border fw-bold">
                                        <?= ma_hien_thi('nhu_cau', $tt['ma_nhu_cau']) ?>
                                    </span>
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($tt['tinh']) ?>
                                    </span>
                                </div>

                                <h6 class="fw-bold text-dark mb-1 fs-6">
                                    Cần mua: <span class="text-success"><?= htmlspecialchars($tt['ten_nong_san'] ?? $tt['ten_danh_muc']) ?></span>
                                </h6>
                                <div class="small text-muted mb-2">
                                    Số lượng: <strong class="text-dark fs-6"><?= dinh_dang_so_luong($tt['so_luong']) ?> <?= htmlspecialchars($tt['don_vi']) ?></strong> | 
                                    Giá tối đa: <strong class="text-success"><?= dinh_dang_tien($tt['gia_toi_da']) ?></strong>
                                </div>

                                <div class="small text-muted mb-3">
                                    <div>Người mua: <strong class="text-dark"><?= htmlspecialchars($tt['ten_nguoi_mua']) ?></strong></div>
                                    <div>Hạn chót gom: <strong><?= dinh_dang_ngay($tt['han_can']) ?></strong></div>
                                </div>

                                <a href="#<?= htmlspecialchars($tt['sdt_nguoi_mua']) ?>" 
                                   class="btn btn-outline-success w-100 py-2 fw-bold rounded-pill d-flex align-items-center justify-content-center gap-1.5">
                                    <i class="bi bi-telephone-fill"></i> Liên hệ ngay: <?= htmlspecialchars($tt['sdt_nguoi_mua']) ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- 2. GIAO DIỆN BẢNG TRÊN MÁY TÍNH -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table align-middle table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Mã</th>
                                <th>Nông Sản &amp; Số Lượng Cần</th>
                                <th>Mức Giá Mong Muốn</th>
                                <th>Khu Vực Nhận</th>
                                <th>Hạn Gom</th>
                                <th>Đơn Vị Mua</th>
                                <th class="text-end">Liên Hệ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dsThiTruong as $tt): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark border fw-bold fs-6">
                                            <?= ma_hien_thi('nhu_cau', $tt['ma_nhu_cau']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-6">
                                            <?= htmlspecialchars($tt['ten_nong_san'] ?? $tt['ten_danh_muc']) ?>
                                        </div>
                                        <?php if (!empty($tt['ten_nong_san'])): ?>
                                            <div class="small text-muted">
                                                Ngành: <?= htmlspecialchars($tt['ten_danh_muc']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="text-success fw-bold">
                                            <?= dinh_dang_so_luong($tt['so_luong']) ?> <?= htmlspecialchars($tt['don_vi']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="text-dark fs-6">
                                            <?= dinh_dang_tien($tt['gia_toi_da']) ?>
                                        </strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border fs-6">
                                            <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($tt['tinh']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-muted fw-bold">
                                            <?= dinh_dang_ngay($tt['han_can']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 160px;">
                                            <?= htmlspecialchars($tt['ten_nguoi_mua']) ?>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <a href="tel:<?= htmlspecialchars($tt['sdt_nguoi_mua']) ?>" class="btn btn-sm btn-outline-success rounded-pill fw-bold px-3 py-1">
                                            <i class="bi bi-telephone-fill me-1"></i><?= htmlspecialchars($tt['sdt_nguoi_mua']) ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

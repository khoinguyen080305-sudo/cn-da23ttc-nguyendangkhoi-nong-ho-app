<?php
// views/layouts/header.php
$nguoiDung = nguoi_dung_hien_tai();
$soLuongGio = 0;
if (co_vai_tro('nguoi_mua')) {
    $soLuongGio = isset($_SESSION['gio_hang']) ? count($_SESSION['gio_hang']) : 0;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>THÔNG VỤ - Kết Nối Nông Hộ & Tiêu Thụ Nông Sản</title>
    <!-- Bootstrap 5.3 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Favicon chuẩn kích thước vuông cho Tab trình duyệt -->
    <link rel="icon" type="image/svg+xml" href="assets/images/logo_icon.svg?v=8">
    <link rel="shortcut icon" href="assets/images/logo_icon.svg?v=8">
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- CSS Tùy biến dự án -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(dirname(__DIR__, 2) . '/assets/css/style.css') ?>">
    <link rel="stylesheet" href="assets/css/lucian-theme-toggle.css?v=<?= file_exists(dirname(__DIR__, 2) . '/assets/css/lucian-theme-toggle.css') ? filemtime(dirname(__DIR__, 2) . '/assets/css/lucian-theme-toggle.css') : 1 ?>">
    <link rel="stylesheet" href="assets/css/lucian-mobile-nav.css?v=<?= file_exists(dirname(__DIR__, 2) . '/assets/css/lucian-mobile-nav.css') ? filemtime(dirname(__DIR__, 2) . '/assets/css/lucian-mobile-nav.css') : 1 ?>">
    <link rel="stylesheet" href="assets/css/lucian-liquid-button.css?v=<?= file_exists(dirname(__DIR__, 2) . '/assets/css/lucian-liquid-button.css') ? filemtime(dirname(__DIR__, 2) . '/assets/css/lucian-liquid-button.css') : 1 ?>">
    <link rel="stylesheet" href="assets/css/lucian-dashboard.css?v=<?= file_exists(dirname(__DIR__, 2) . '/assets/css/lucian-dashboard.css') ? filemtime(dirname(__DIR__, 2) . '/assets/css/lucian-dashboard.css') : 1 ?>">
    <!-- Kiểm tra Theme sớm chống chớp trắng màn hình (FOUC) -->
    <script>
        (function() {
            var savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
</head>
<body class="<?= da_dang_nhap() ? 'has-mobile-dock' : '' ?>">

<!-- KHUNG CHỨA THÔNG BÁO TOAST TOÀN HỆ THỐNG (TỰ ĐỘNG HIỂN THỊ SAU CÁC THAO TÁC) -->
<div id="nhToastContainer" class="nh-toast-container" aria-live="polite"></div>

<!-- HEADER SIÊU GỌN 56px (50px MOBILE) -->
<header class="nh-header d-flex align-items-center">
    <div class="container-fluid px-2.5 px-sm-3 px-lg-4 d-flex align-items-center justify-content-between">
        <!-- LOGO THÔNG VỤ -->
        <a href="index.php" class="d-flex align-items-center text-decoration-none me-auto">
            <img src="assets/images/logo.svg?v=<?= filemtime(dirname(__DIR__, 2) . '/assets/images/logo.svg') ?>" alt="THÔNG VỤ - Kết Nối Nông Hộ & Tiêu Thụ Nông Sản" class="nh-logo-img">
        </a>

        <!-- KHU VỰC ĐIỀU HƯỚNG & TÀI KHOẢN -->
        <div class="d-flex align-items-center gap-1.5 gap-sm-2">
            <!-- CHỈ HIỆN GIỎ HÀNG CHO VAI TRÒ 'nguoi_mua' -->
            <?php if (co_vai_tro('nguoi_mua')): ?>
                <a href="index.php?trang=gio_hang" class="btn-cart-header" title="Giỏ hàng thu mua của bạn">
                    <i class="bi bi-cart3"></i>
                    <span class="d-none d-sm-inline">Giỏ hàng</span>
                    <span class="cart-badge-count" id="headerCartCount" style="<?= $soLuongGio > 0 ? '' : 'display:none;' ?>"><?= $soLuongGio ?></span>
                </a>
            <?php endif; ?>

            <?php if (da_dang_nhap()): ?>
                <?php $soDonMoi = so_don_hang_moi_hien_tai(); ?>

                <!-- ĐÃ ĐĂNG NHẬP -->
                <div class="dropdown position-relative">
                    <button class="btn btn-light border d-flex align-items-center gap-1.5 gap-sm-2 rounded-pill px-2 py-1 px-sm-2.5 py-sm-1.5 fw-bold position-relative" type="button" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.85rem;">
                            <?= mb_strtoupper(mb_substr($nguoiDung['ho_ten'], 0, 1)) ?>
                        </div>
                        <span class="fw-bold text-truncate d-none d-sm-inline" style="max-width: 140px;"><?= htmlspecialchars($nguoiDung['ho_ten']) ?></span>
                        <i class="bi bi-chevron-down small text-muted"></i>
                        <?php if ($soDonMoi > 0 || (co_vai_tro('admin') && so_nong_ho_cho_duyet() > 0)): ?>
                            <span class="position-absolute top-0 start-100 translate-middle p-1.5 bg-danger border border-light rounded-circle" style="margin-left: -6px; margin-top: 4px;">
                                <span class="visually-hidden">Thông báo mới</span>
                            </span>
                        <?php endif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border mt-2 fs-6" style="border-radius: 12px; min-width: 210px;">
                        <li class="dropdown-header text-muted">
                            <div class="d-sm-none fw-bold text-dark text-truncate mb-1" style="font-size: 0.92rem; max-width: 190px;">
                                <?= htmlspecialchars($nguoiDung['ho_ten']) ?>
                            </div>
                            Vai trò: 
                            <strong class="text-success fs-6">
                                <?= $nguoiDung['vai_tro'] === 'admin' ? 'Quản trị viên' : ($nguoiDung['vai_tro'] === 'nong_ho' ? 'Nông Hộ' : 'Người Mua') ?>
                            </strong>
                        </li>
                        <li><hr class="dropdown-divider"></li>

                        <?php if ($nguoiDung['vai_tro'] === 'admin'): ?>
                            <?php $soChoDuyetHeader = so_nong_ho_cho_duyet(); ?>
                            <li class="d-none d-md-block">
                                <a class="dropdown-item py-2 fw-semibold d-flex align-items-center justify-content-between" href="index.php?trang=admin_tong_quan">
                                    <span><i class="bi bi-speedometer2 text-success me-2 fs-5"></i>Bảng Quản Trị</span>
                                    <?php if ($soChoDuyetHeader > 0): ?>
                                        <span class="badge bg-danger rounded-pill" title="<?= $soChoDuyetHeader ?> nông hộ chờ duyệt"><?= $soChoDuyetHeader ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php elseif ($nguoiDung['vai_tro'] === 'nong_ho'): ?>
                            <li class="d-none d-md-block"><a class="dropdown-item py-2 fw-semibold" href="index.php?trang=nong_ho_san_pham"><i class="bi bi-boxes text-success me-2 fs-5"></i>Nông Sản Của Tôi</a></li>
                            <li class="d-none d-md-block"><a class="dropdown-item py-2 fw-semibold" href="index.php?trang=nong_ho_nhu_cau"><i class="bi bi-lightning-charge text-warning me-2 fs-5"></i>Gợi Ý Ghép Nối %</a></li>
                            <li class="d-none d-md-block">
                                <a class="dropdown-item py-2 fw-semibold d-flex align-items-center justify-content-between" href="index.php?trang=don_hang">
                                    <span><i class="bi bi-receipt text-primary me-2 fs-5"></i>Đơn Đặt Hàng</span>
                                    <?php if ($soDonMoi > 0): ?>
                                        <span class="badge bg-danger rounded-pill"><?= $soDonMoi ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li class="d-none d-md-block"><a class="dropdown-item py-2 fw-semibold" href="index.php?trang=nong_ho_ho_so"><i class="bi bi-graph-up text-success me-2 fs-5"></i>Hồ Sơ &amp; Thống Kê</a></li>
                        <?php elseif ($nguoiDung['vai_tro'] === 'nguoi_mua'): ?>
                            <li class="d-none d-md-block">
                                <a class="dropdown-item py-2 fw-semibold d-flex align-items-center justify-content-between" href="index.php?trang=don_hang">
                                    <span><i class="bi bi-receipt text-primary me-2 fs-5"></i>Đơn Hàng Của Tôi</span>
                                    <?php if ($soDonMoi > 0): ?>
                                        <span class="badge bg-danger rounded-pill"><?= $soDonMoi ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li class="d-none d-md-block"><a class="dropdown-item py-2 fw-semibold" href="index.php?trang=nhu_cau_cua_toi"><i class="bi bi-megaphone text-warning me-2 fs-5"></i>Tin Nhu Cầu Thu Mua</a></li>
                            <li class="d-none d-md-block"><a class="dropdown-item py-2 fw-semibold" href="index.php?trang=nguoi_mua_ho_so"><i class="bi bi-graph-up text-primary me-2 fs-5"></i>Hồ Sơ &amp; Thống Kê</a></li>
                        <?php endif; ?>

                        <li class="d-none d-md-block"><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2 text-danger fw-bold" href="index.php?trang=dang_xuat"><i class="bi bi-box-arrow-right me-2 fs-5"></i>Đăng Xuất</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <!-- CHƯA ĐĂNG NHẬP (HIỆN CẢ 2 NÚT TRÊN CẢ MOBILE & DESKTOP) -->
                <a href="index.php?trang=dang_nhap" class="btn btn-clean-outline px-2.5 py-1 px-sm-3 py-sm-1.5 fw-bold text-nowrap nh-btn-header-auth">Đăng Nhập</a>
                <a href="index.php?trang=dang_ky" class="btn btn-clean px-2.5 py-1 px-sm-3.5 py-sm-1.5 fw-bold text-nowrap nh-btn-header-auth">Đăng Ký</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- BANNER THÔNG BÁO CHO NÔNG HỘ ĐANG CHỜ DUYỆT -->
<?php if (co_vai_tro('nong_ho') && ($nguoiDung['trang_thai'] ?? '') === 'cho_duyet'): ?>
    <div class="alert alert-warning border-0 rounded-0 mb-0 py-2 text-center small fw-semibold">
        <i class="bi bi-hourglass-split me-1 text-warning"></i>
        Hồ sơ của bạn đang chờ Ban Quản Trị phê duyệt danh tính. Trong thời gian này, bạn chưa thể đăng bán sản phẩm mới.
    </div>
<?php endif; ?>

<main class="py-3">

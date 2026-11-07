</main>

<!-- FOOTER TỐI GIẢN, TINH GỌN, SẠCH SẼ (CHỈ HIỆN TRÊN DESKTOP/TABLET) -->
<footer class="nh-footer-simple d-none d-md-block">
    <div class="container-fluid px-lg-4 py-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2.5">
            <!-- 1. LOGO & BẢN QUYỀN TINH GỌN -->
            <div class="d-flex align-items-center">
                <a href="index.php" class="text-decoration-none me-3">
                    <img src="assets/images/logo.svg?v=<?= filemtime(dirname(__DIR__, 2) . '/assets/images/logo.svg') ?>" alt="THÔNG VỤ" style="height: 25px;">
                </a>
                <span class="text-muted small border-start ps-3 d-none d-sm-inline">
                    © 2026 THÔNG VỤ - Kết Nối Nông Hộ & Tiêu Thụ Nông Sản Mùa Vụ
                </span>
            </div>


            <!-- 3. HOTLINE GỌN GÀNG DẠNG VIÊN THUỐC -->
            <div class="d-flex align-items-center gap-2">
                <a href="#" class="nh-simple-hotline text-decoration-none" title="Tổng đài hỗ trợ nông hộ &amp; thương lái">
                    <i class="bi bi-telephone-fill text-success"></i>
                    <span>Hotline: <strong class="text-dark">0900.000.001</strong></span>
                </a>
            </div>
        </div>
    </div>
</footer>


<?php
$trangHienTai = $_GET['trang'] ?? 'trang_chu';
$soDonMoiMobile = so_don_hang_moi_hien_tai();
$soLuongGioMobile = isset($_SESSION['gio_hang']) ? count($_SESSION['gio_hang']) : 0;
$soChoDuyetMobile = so_nong_ho_cho_duyet();
?>

<?php if (da_dang_nhap()): ?>
<!-- ==============================================================================
     LUCIAN UX/UI #041: NOTCHED BOTTOM TAB BAR (TRẢI NGHIỆM NATIVE APP CHO DI ĐỘNG)
     CHỈ HIỂN THỊ KHI ĐÃ ĐĂNG NHẬP ĐỂ ĐIỀU HƯỚNG CÁC TRANG NGHIỆP VỤ TƯƠNG ỨNG
     ============================================================================== -->
<nav class="ux u041 nh-mobile-dock d-md-none" id="nhMobileDock" aria-label="Thanh điều hướng ứng dụng di động">
    <span class="u041-shadow" aria-hidden="true"></span>
    <div class="u041-bar" aria-hidden="true"></div>
    <span class="u041-bub" aria-hidden="true"></span>

    <ul class="u041-tabs" role="tablist">
        <!-- NÚT TRANG CHỦ (DÙNG CHUNG CHO CẢ 3 VAI TRÒ) -->
        <?php $isActiveHome = ($trangHienTai === 'trang_chu' || empty($_GET['trang'])); ?>
        <li role="none">
            <a class="u041-tab" href="index.php" role="tab" aria-selected="<?= $isActiveHome ? 'true' : 'false' ?>" tabindex="<?= $isActiveHome ? '0' : '-1' ?>" title="Trang chủ">
                <span class="u041-ic" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M4 11l8-6.5 8 6.5V19a1 1 0 0 1-1 1h-4.5v-5h-5v5H5a1 1 0 0 1-1-1z"/></svg>
                </span>
                <span class="u041-lab">Trang chủ</span>
            </a>
        </li>

        <?php if (co_vai_tro('admin')): ?>
            <!-- 2. TÀI KHOẢN QUẢN TRỊ VIÊN (ADMIN) -->
            <?php $isActiveAdmin = ($trangHienTai === 'admin_tong_quan'); ?>
            <li role="none">
                <a class="u041-tab" href="index.php?trang=admin_tong_quan" role="tab" aria-selected="<?= $isActiveAdmin ? 'true' : 'false' ?>" tabindex="<?= $isActiveAdmin ? '0' : '-1' ?>" title="Quản trị tổng quan">
                    <span class="u041-ic" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="m16 8-4.5 4.5"/><path d="M8 12a4 4 0 0 1 8 0"/></svg>
                    </span>
                    <?php if ($soChoDuyetMobile > 0): ?>
                        <em class="u041-bd" aria-label="<?= $soChoDuyetMobile ?> hồ sơ chờ duyệt"><?= $soChoDuyetMobile ?></em>
                    <?php endif; ?>
                    <span class="u041-lab">Quản trị</span>
                </a>
            </li>
            <li role="none">
                <a class="u041-tab" href="index.php?trang=admin_tong_quan#khoiThanhVien" role="tab" aria-selected="false" tabindex="-1" title="Quản lý thành viên">
                    <span class="u041-ic" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </span>
                    <span class="u041-lab">Thành viên</span>
                </a>
            </li>
            <li role="none">
                <a class="u041-tab" href="index.php?trang=admin_tong_quan#khoiDanhMuc" role="tab" aria-selected="false" tabindex="-1" title="Quản lý danh mục">
                    <span class="u041-ic" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                    </span>
                    <span class="u041-lab">Danh mục</span>
                </a>
            </li>

        <?php elseif (co_vai_tro('nong_ho')): ?>
            <!-- 3. TÀI KHOẢN NÔNG HỘ / NHÀ VƯỜN -->
            <?php $isActiveSp = in_array($trangHienTai, ['nong_ho_san_pham', 'nong_ho_form_san_pham']); ?>
            <li role="none">
                <a class="u041-tab" href="index.php?trang=nong_ho_san_pham" role="tab" aria-selected="<?= $isActiveSp ? 'true' : 'false' ?>" tabindex="<?= $isActiveSp ? '0' : '-1' ?>" title="Nông sản của tôi">
                    <span class="u041-ic" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="m7.5 4.27 9 5.15M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5M12 22V12"/></svg>
                    </span>
                    <span class="u041-lab">Nông sản</span>
                </a>
            </li>
            <?php $isActiveGhepNoi = ($trangHienTai === 'nong_ho_nhu_cau'); ?>
            <li role="none">
                <a class="u041-tab" href="index.php?trang=nong_ho_nhu_cau" role="tab" aria-selected="<?= $isActiveGhepNoi ? 'true' : 'false' ?>" tabindex="<?= $isActiveGhepNoi ? '0' : '-1' ?>" title="Ghép nối nhu cầu thị trường">
                    <span class="u041-ic" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                    </span>
                    <span class="u041-lab">Ghép nối</span>
                </a>
            </li>
            <?php $isActiveDonHang = ($trangHienTai === 'don_hang'); ?>
            <li role="none">
                <a class="u041-tab" href="index.php?trang=don_hang" role="tab" aria-selected="<?= $isActiveDonHang ? 'true' : 'false' ?>" tabindex="<?= $isActiveDonHang ? '0' : '-1' ?>" title="Đơn hàng">
                    <span class="u041-ic" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </span>
                    <?php if ($soDonMoiMobile > 0): ?>
                        <em class="u041-bd" aria-label="<?= $soDonMoiMobile ?> đơn hàng mới"><?= $soDonMoiMobile > 99 ? '99+' : $soDonMoiMobile ?></em>
                    <?php endif; ?>
                    <span class="u041-lab">Đơn hàng</span>
                </a>
            </li>
            <?php $isActiveHoSo = ($trangHienTai === 'nong_ho_ho_so'); ?>
            <li role="none">
                <a class="u041-tab" href="index.php?trang=nong_ho_ho_so" role="tab" aria-selected="<?= $isActiveHoSo ? 'true' : 'false' ?>" tabindex="<?= $isActiveHoSo ? '0' : '-1' ?>" title="Hồ sơ & Thống kê">
                    <span class="u041-ic" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    </span>
                    <span class="u041-lab">Thống kê</span>
                </a>
            </li>

        <?php elseif (co_vai_tro('nguoi_mua')): ?>
            <!-- 4. TÀI KHOẢN NGƯỜI MUA / THƯƠNG LÁI -->
            <?php $isActiveNhuCau = ($trangHienTai === 'nhu_cau_cua_toi'); ?>
            <li role="none">
                <a class="u041-tab" href="index.php?trang=nhu_cau_cua_toi" role="tab" aria-selected="<?= $isActiveNhuCau ? 'true' : 'false' ?>" tabindex="<?= $isActiveNhuCau ? '0' : '-1' ?>" title="Đăng tin nhu cầu thu mua">
                    <span class="u041-ic" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
                    </span>
                    <span class="u041-lab">Nhu cầu</span>
                </a>
            </li>
            <?php $isActiveGioHang = ($trangHienTai === 'gio_hang'); ?>
            <li role="none">
                <a class="u041-tab" href="index.php?trang=gio_hang" role="tab" aria-selected="<?= $isActiveGioHang ? 'true' : 'false' ?>" tabindex="<?= $isActiveGioHang ? '0' : '-1' ?>" title="Giỏ hàng">
                    <span class="u041-ic" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                    </span>
                    <?php if ($soLuongGioMobile > 0): ?>
                        <em class="u041-bd" aria-label="<?= $soLuongGioMobile ?> sản phẩm trong giỏ"><?= $soLuongGioMobile ?></em>
                    <?php endif; ?>
                    <span class="u041-lab">Giỏ hàng</span>
                </a>
            </li>
            <?php $isActiveDonHang = ($trangHienTai === 'don_hang'); ?>
            <li role="none">
                <a class="u041-tab" href="index.php?trang=don_hang" role="tab" aria-selected="<?= $isActiveDonHang ? 'true' : 'false' ?>" tabindex="<?= $isActiveDonHang ? '0' : '-1' ?>" title="Đơn đặt hàng">
                    <span class="u041-ic" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </span>
                    <?php if ($soDonMoiMobile > 0): ?>
                        <em class="u041-bd" aria-label="<?= $soDonMoiMobile ?> đơn hàng mới"><?= $soDonMoiMobile > 99 ? '99+' : $soDonMoiMobile ?></em>
                    <?php endif; ?>
                    <span class="u041-lab">Đơn hàng</span>
                </a>
            </li>
            <?php $isActiveHoSo = ($trangHienTai === 'nguoi_mua_ho_so'); ?>
            <li role="none">
                <a class="u041-tab" href="index.php?trang=nguoi_mua_ho_so" role="tab" aria-selected="<?= $isActiveHoSo ? 'true' : 'false' ?>" tabindex="<?= $isActiveHoSo ? '0' : '-1' ?>" title="Hồ sơ & Thống kê">
                    <span class="u041-ic" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    </span>
                    <span class="u041-lab">Thống kê</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
<?php endif; ?>

<!-- BOOTSTRAP 5.3 BUNDLE JS & APP.JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js?v=<?= file_exists(dirname(__DIR__, 2) . '/assets/js/app.js') ? filemtime(dirname(__DIR__, 2) . '/assets/js/app.js') : 1 ?>"></script>
<script src="assets/js/theme-toggle.js"></script>
<script src="assets/js/lucian-mobile-nav.js?v=<?= file_exists(dirname(__DIR__, 2) . '/assets/js/lucian-mobile-nav.js') ? filemtime(dirname(__DIR__, 2) . '/assets/js/lucian-mobile-nav.js') : 1 ?>"></script>

<?php 
$thongBaoHeThong = lay_thong_bao();
if ($thongBaoHeThong): 
?>
<!-- TỰ ĐỘNG BẬT THÔNG BÁO SAU CÁC THAO TÁC (TẠO, XÓA, CẬP NHẬT) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof hienThiToast === 'function') {
        hienThiToast(
            <?= json_encode($thongBaoHeThong['loai'] ?? 'info') ?>,
            <?= json_encode($thongBaoHeThong['tieu_de'] ?? 'Thông báo') ?>,
            <?= json_encode($thongBaoHeThong['noi_dung'] ?? '') ?>
        );
    }
});
</script>
<noscript>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 10060;">
        <div class="alert alert-<?= $thongBaoHeThong['loai'] === 'error' ? 'danger' : ($thongBaoHeThong['loai'] === 'warning' ? 'warning' : 'success') ?> shadow">
            <strong><?= htmlspecialchars($thongBaoHeThong['tieu_de'] ?? 'Thông báo') ?>:</strong>
            <?= htmlspecialchars($thongBaoHeThong['noi_dung'] ?? '') ?>
        </div>
    </div>
</noscript>
<?php endif; ?>

</body>
</html>

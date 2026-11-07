<?php
// views/auth/doi_mk_tam.php
$loi = $loi ?? '';
require_once __DIR__ . '/../layouts/header.php';
$nguoiDung = nguoi_dung_hien_tai();
?>

<div class="nh-auth-wrapper">
    <div class="nh-auth-card">
        <!-- TIÊU ĐỀ -->
        <h2 class="nh-auth-title mb-2">Đổi Mật Khẩu</h2>

        <div class="alert py-2 px-3 rounded-4 small mb-2 text-center border-0"
            style="background: #eff6ff; color: #1d4ed8;">
            <i class="bi bi-shield-lock-fill me-1"></i>
            Xin chào <strong><?= htmlspecialchars($nguoiDung['ho_ten']) ?></strong>! Vui lòng thiết lập mật khẩu mới để
            bảo vệ an toàn cho tài khoản.
        </div>

        <?php if (!empty($loi)): ?>
            <div class="alert alert-danger py-2 px-3 rounded-pill fs-6 mb-2.5 text-center border-0"
                style="background: #fef2f2; color: #dc2626;">
                <i class="bi bi-exclamation-circle-fill me-1"></i> <?= htmlspecialchars($loi) ?>
            </div>
        <?php endif; ?>

        <form action="index.php?trang=doi_mk_tam" method="POST" id="formDoiMkTam">
            <div class="position-relative mb-2">
                <input type="password" class="form-control nh-auth-input pe-4" id="mat_khau" name="mat_khau_moi"
                    placeholder="Mật khẩu mới (tối thiểu 8 ký tự, có ký tự đặc biệt)" required
                    oninput="kiemTraDoManhMatKhau(this.value); kiemTraKhopMatKhau();">
                <button type="button"
                    class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted pe-3 text-decoration-none py-0"
                    onclick="toggleHienMatKhau('mat_khau', this)">
                    <i class="bi bi-eye"></i>
                </button>
            </div>

            <!-- THANH ĐO ĐỘ MẠNH 100% -->
            <div class="nh-password-meter-wrap mb-2">
                <div class="d-flex justify-content-between align-items-center small">
                    <span id="pwStrengthLabel" class="small">Độ mạnh: <strong class="text-muted">0%</strong></span>
                    <span class="text-muted" style="font-size: 0.75rem;">Yêu cầu: ≥ 8 ký tự &amp; ký tự đặc biệt</span>
                </div>
                <div class="nh-password-track">
                    <div id="pwStrengthBar" class="nh-password-bar"></div>
                </div>
                <div class="d-flex flex-wrap gap-1 mt-1">
                    <span id="ruleLen" class="nh-rule-badge"><i class="bi bi-check2"></i> 8+ ký tự</span>
                    <span id="ruleCase" class="nh-rule-badge"><i class="bi bi-check2"></i> Hoa &amp; Thường</span>
                    <span id="ruleNum" class="nh-rule-badge"><i class="bi bi-check2"></i> Số (0-9)</span>
                    <span id="ruleSpecial" class="nh-rule-badge"><i class="bi bi-check2"></i> Ký tự đặc biệt</span>
                </div>
            </div>

            <div class="position-relative mb-2">
                <input type="password" class="form-control nh-auth-input pe-4" id="xac_nhan_mat_khau"
                    name="xac_nhan_mat_khau" placeholder="Xác nhận lại mật khẩu mới" required
                    oninput="kiemTraKhopMatKhau();">
                <button type="button"
                    class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted pe-3 text-decoration-none py-0"
                    onclick="toggleHienMatKhau('xac_nhan_mat_khau', this)">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            <div id="confirmPasswordMatchMsg" class="px-3 mb-2"></div>

            <!-- NÚT LƯU MẬT KHẨU: LUCIAN UX/UI #001 LIQUID GLASS BUTTON -->
            <div class="ux u001" id="lucianChangePwBtnSlot" data-state="idle">
                <div class="u001-blobs" aria-hidden="true"><i></i><i></i><i></i></div>
                <div class="u001-mag">
                    <div class="u001-float">
                        <button class="u001-btn" type="submit">
                            <span class="u001-sheen" aria-hidden="true"></span>
                            <span class="u001-txt">Lưu Và Tiếp Tục</span>
                            <span class="u001-spin" aria-hidden="true"><svg viewBox="0 0 30 30">
                                    <circle cx="15" cy="15" r="10" />
                                </svg></span>
                            <span class="u001-done" aria-hidden="true"><b><svg viewBox="0 0 16 16">
                                        <path d="M3.5 8.4l3 3 6-6.4" />
                                    </svg></b> Đổi Mật Khẩu Thành Công</span>
                        </button>
                    </div>
                </div>
                <span class="u001-live" aria-live="polite"></span>
            </div>
        </form>
    </div>
</div>

<script
    src="assets/js/lucian-liquid-button.js?v=<?= file_exists(dirname(__DIR__, 2) . '/assets/js/lucian-liquid-button.js') ? filemtime(dirname(__DIR__, 2) . '/assets/js/lucian-liquid-button.js') : 1 ?>"></script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
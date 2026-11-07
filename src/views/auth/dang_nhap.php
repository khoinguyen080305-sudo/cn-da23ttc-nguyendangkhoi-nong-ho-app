<?php
// views/auth/dang_nhap.php
$loi = $loi ?? '';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="nh-auth-wrapper">
    <div class="nh-auth-card">
        <!-- NÚT VỀ TRANG CHỦ -->
        <div class="mb-2 text-start">
            <a href="index.php"
                class="text-decoration-none text-muted small fw-semibold d-inline-flex align-items-center">
                <i class="bi bi-arrow-left me-1"></i>Về Trang Chủ
            </a>
        </div>

        <!-- TIÊU ĐỀ & PHỤ ĐỀ SẠCH SẼ -->
        <div class="text-center mb-2.5">
            <h2 class="nh-auth-title">Đăng Nhập</h2>
        </div>

        <?php if (!empty($loi)): ?>
            <div class="alert alert-danger py-2 px-3 rounded-3 fs-6 mb-2.5 text-center border-0 d-flex align-items-center justify-content-center gap-1.5"
                style="background: #fef2f2; color: #dc2626;">
                <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                <span class="small fw-semibold"><?= htmlspecialchars($loi) ?></span>
            </div>
        <?php endif; ?>

        <form action="index.php?trang=dang_nhap" method="POST" id="formDangNhap">
            <!-- SỐ ĐIỆN THOẠI ĐĂNG NHẬP -->
            <div class="mb-2.5">
                <label class="nh-auth-label" for="sdt">
                    <span>Số điện thoại <span class="text-danger">*</span></span>
                </label>
                <input type="tel" class="form-control nh-auth-input" id="sdt" name="sdt"
                    placeholder="Ví dụ: 0912345678 (10 số)" maxlength="10" required autofocus
                    value="<?= htmlspecialchars($_POST['sdt'] ?? '') ?>"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            </div>

            <!-- MẬT KHẨU CÓ NÚT ẨN HIỆN TINH TẾ -->
            <div class="mb-2">
                <label class="nh-auth-label" for="mat_khau">
                    <span>Mật khẩu <span class="text-danger">*</span></span>
                </label>
                <div class="position-relative">
                    <input type="password" class="form-control nh-auth-input pe-4" id="mat_khau" name="mat_khau"
                        placeholder="Nhập mật khẩu của bạn" required>
                    <button type="button"
                        class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted pe-3 text-decoration-none py-0"
                        onclick="toggleHienMatKhau('mat_khau', this)" tabindex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <!-- HỖ TRỢ CẤP LẠI MẬT KHẨU (KHÔNG DÙNG LINK) -->
            <div class="d-flex align-items-center justify-content-between mb-2 small text-muted">
                <span>Quên mật khẩu?</span>
                <span class="nh-phone-badge" title="Hotline hỗ trợ cấp lại mật khẩu tạm">
                    <i class="bi bi-telephone-fill"></i>0900.000.001
                </span>
            </div>

            <!-- NÚT ĐĂNG NHẬP: LUCIAN UX/UI #001 LIQUID GLASS BUTTON -->
            <div class="ux u001" id="lucianLoginBtnSlot" data-state="idle">
                <div class="u001-blobs" aria-hidden="true"><i></i><i></i><i></i></div>
                <div class="u001-mag">
                    <div class="u001-float">
                        <button class="u001-btn" type="submit">
                            <span class="u001-sheen" aria-hidden="true"></span>
                            <span class="u001-txt">Đăng Nhập</span>
                            <span class="u001-spin" aria-hidden="true"><svg viewBox="0 0 30 30">
                                    <circle cx="15" cy="15" r="10" />
                                </svg></span>
                            <span class="u001-done" aria-hidden="true"><b><svg viewBox="0 0 16 16">
                                        <path d="M3.5 8.4l3 3 6-6.4" />
                                    </svg></b> Đăng Nhập Thành Công!</span>
                        </button>
                    </div>
                </div>
                <span class="u001-live" aria-live="polite"></span>
            </div>
        </form>

        <!-- CHUYỂN HƯỚNG ĐĂNG KÝ -->
        <div class="text-center small text-muted mt-2">
            Chưa có tài khoản?
            <a href="index.php?trang=dang_ky" class="nh-auth-link fw-bold ms-1">
                Đăng ký ngay
            </a>
        </div>
    </div>
</div>

<script
    src="assets/js/lucian-liquid-button.js?v=<?= file_exists(dirname(__DIR__, 2) . '/assets/js/lucian-liquid-button.js') ? filemtime(dirname(__DIR__, 2) . '/assets/js/lucian-liquid-button.js') : 1 ?>"></script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
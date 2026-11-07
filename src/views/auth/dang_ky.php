<?php
// views/auth/dang_ky.php
$loi = $loi ?? '';
require_once __DIR__ . '/../layouts/header.php';
$vaiTroChon = $_POST['vai_tro'] ?? 'nguoi_mua';
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
        <div class="text-center mb-2">
            <h2 class="nh-auth-title">Đăng Ký Tài Khoản</h2>
        </div>

        <?php if (!empty($loi)): ?>
            <div class="alert alert-danger py-2 px-3 rounded-3 fs-6 mb-2.5 text-center border-0 d-flex align-items-center justify-content-center gap-1.5"
                style="background: #fef2f2; color: #dc2626;">
                <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                <span class="small fw-semibold"><?= htmlspecialchars($loi) ?></span>
            </div>
        <?php endif; ?>

        <form action="index.php?trang=dang_ky" method="POST" id="formDangKy">
            <!-- 1. CHỌN VAI TRÒ DẠNG SEGMENTED CONTROL GỌN GÀNG -->
            <div class="nh-role-switch">
                <label class="nh-role-option <?= $vaiTroChon === 'nguoi_mua' ? 'active' : '' ?>"
                    id="opt_role_nguoi_mua">
                    <input type="radio" name="vai_tro" id="role_nguoi_mua" value="nguoi_mua"
                        <?= $vaiTroChon === 'nguoi_mua' ? 'checked' : '' ?> onchange="chuyenVaiTro('nguoi_mua')">
                    <span>Người Mua</span>
                </label>
                <label class="nh-role-option <?= $vaiTroChon === 'nong_ho' ? 'active' : '' ?>" id="opt_role_nong_ho">
                    <input type="radio" name="vai_tro" id="role_nong_ho" value="nong_ho" <?= $vaiTroChon === 'nong_ho' ? 'checked' : '' ?> onchange="chuyenVaiTro('nong_ho')">
                    <span>Nhà Vườn</span>
                </label>
            </div>

            <!-- 2. SỐ ĐIỆN THOẠI (TÀI KHOẢN ĐĂNG NHẬP) -->
            <div class="mb-2">
                <label class="nh-auth-label" for="sdt">
                    <span>Số điện thoại <span class="text-danger">*</span></span>
                    <span class="text-muted fw-normal" style="font-size: 0.72rem;">Dùng để đăng nhập</span>
                </label>
                <input type="tel" class="form-control nh-auth-input" id="sdt" name="sdt"
                    value="<?= htmlspecialchars($_POST['sdt'] ?? '') ?>" placeholder="Ví dụ: 0912345678 (10 số)"
                    maxlength="10" required autofocus oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            </div>

            <!-- 3. HỌ VÀ TÊN / TÊN NHÀ VƯỜN -->
            <div class="mb-2">
                <label class="nh-auth-label" for="ho_ten">
                    <span><span
                            id="lblHoTenText"><?= $vaiTroChon === 'nong_ho' ? 'Tên nhà vườn' : 'Họ và tên người mua' ?></span>
                        <span class="text-danger">*</span></span>
                </label>
                <input type="text" class="form-control nh-auth-input" id="ho_ten" name="ho_ten"
                    value="<?= htmlspecialchars($_POST['ho_ten'] ?? '') ?>"
                    placeholder="<?= $vaiTroChon === 'nong_ho' ? 'Ví dụ: Vườn Thanh Long Chú Ba' : 'Ví dụ: Nguyễn Đăng Khôi' ?>"
                    required>
            </div>

            <!-- 4. VỊ TRÍ TỈNH/TP & ĐỊA CHỈ CHI TIẾT -->
            <div class="row g-2 mb-2">
                <div class="col-5">
                    <label class="nh-auth-label" for="tinh">
                        <span>Tỉnh/ TP <span class="text-danger">*</span></span>
                    </label>
                    <input type="text" class="form-control nh-auth-input" id="tinh" name="tinh"
                        value="<?= htmlspecialchars($_POST['tinh'] ?? '') ?>" placeholder="Vĩnh Long..." required>
                </div>
                <div class="col-7">
                    <label class="nh-auth-label" for="dia_chi">
                        <span>Địa chỉ chi tiết <span class="text-danger">*</span></span>
                    </label>
                    <input type="text" class="form-control nh-auth-input" id="dia_chi" name="dia_chi"
                        value="<?= htmlspecialchars($_POST['dia_chi'] ?? '') ?>"
                        placeholder="<?= $vaiTroChon === 'nong_ho' ? 'vd: Xã Bình Phú, Càng Long' : 'vd: Số 12, Đường 30/4' ?>"
                        required>
                </div>
            </div>

            <!-- 5. MẬT KHẨU & XÁC NHẬN MẬT KHẨU (2 CỘT CÂN ĐỐI) -->
            <div class="row g-2 mb-1">
                <div class="col-6">
                    <label class="nh-auth-label" for="mat_khau">
                        <span>Mật khẩu <span class="text-danger">*</span></span>
                    </label>
                    <div class="position-relative">
                        <input type="password" class="form-control nh-auth-input pe-4" id="mat_khau" name="mat_khau"
                            placeholder="Mật khẩu (≥8 ký tự)" required
                            oninput="kiemTraDoManhMatKhau(this.value); kiemTraKhopMatKhau();">
                        <button type="button"
                            class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted pe-2 text-decoration-none py-0"
                            onclick="toggleHienMatKhau('mat_khau', this)" tabindex="-1">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="col-6">
                    <label class="nh-auth-label" for="xac_nhan_mat_khau">
                        <span>Nhập lại <span class="text-danger">*</span></span>
                    </label>
                    <div class="position-relative">
                        <input type="password" class="form-control nh-auth-input pe-4" id="xac_nhan_mat_khau"
                            name="xac_nhan_mat_khau" placeholder="Nhập lại mật khẩu" required
                            oninput="kiemTraKhopMatKhau();">
                        <button type="button"
                            class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted pe-2 text-decoration-none py-0"
                            onclick="toggleHienMatKhau('xac_nhan_mat_khau', this)" tabindex="-1">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- THÔNG BÁO KHỚP MẬT KHẨU -->
            <div id="confirmPasswordMatchMsg" class="mb-1" style="min-height: 14px;"></div>

            <!-- THANH ĐO ĐỘ MẠNH MẬT KHẨU TINH TẾ (CHỈ 1 DÒNG GỌN RÀNG) -->
            <div class="nh-password-meter-wrap mb-2">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span id="pwStrengthLabel" class="text-muted" style="font-size: 0.74rem;">Độ mạnh: <span
                            class="fw-semibold">0%</span></span>
                    <span class="text-muted" style="font-size: 0.7rem;">≥ 8 ký tự và có ký tự đặc biệt (@, #,
                        $...)</span>
                </div>
                <div class="nh-password-track">
                    <div id="pwStrengthBar" class="nh-password-bar"></div>
                </div>
                <!-- Ẩn các badge kỹ thuật phức tạp để giao diện nông dân sạch sẽ, không rối mắt -->
                <div class="d-none">
                    <span id="ruleLen"></span>
                    <span id="ruleCase"></span>
                    <span id="ruleNum"></span>
                    <span id="ruleSpecial"></span>
                </div>
            </div>

            <!-- NÚT HOÀN TẤT ĐĂNG KÝ: LUCIAN UX/UI #001 LIQUID GLASS BUTTON -->
            <div class="ux u001" id="lucianRegisterBtnSlot" data-state="idle">
                <div class="u001-blobs" aria-hidden="true"><i></i><i></i><i></i></div>
                <div class="u001-mag">
                    <div class="u001-float">
                        <button class="u001-btn" type="submit">
                            <span class="u001-sheen" aria-hidden="true"></span>
                            <span class="u001-txt">Hoàn Tất Đăng Ký</span>
                            <span class="u001-spin" aria-hidden="true"><svg viewBox="0 0 30 30">
                                    <circle cx="15" cy="15" r="10" />
                                </svg></span>
                            <span class="u001-done" aria-hidden="true"><b><svg viewBox="0 0 16 16">
                                        <path d="M3.5 8.4l3 3 6-6.4" />
                                    </svg></b> Đăng Ký Thành Công!</span>
                        </button>
                    </div>
                </div>
                <span class="u001-live" aria-live="polite"></span>
            </div>
        </form>

        <!-- CHUYỂN HƯỚNG ĐĂNG NHẬP -->
        <div class="text-center small text-muted mt-2">
            Đã có tài khoản?
            <a href="index.php?trang=dang_nhap" class="nh-auth-link fw-bold ms-1">
                Đăng nhập ngay
            </a>
        </div>
    </div>
</div>

<script
    src="assets/js/lucian-liquid-button.js?v=<?= file_exists(dirname(__DIR__, 2) . '/assets/js/lucian-liquid-button.js') ? filemtime(dirname(__DIR__, 2) . '/assets/js/lucian-liquid-button.js') : 1 ?>"></script>

<script>
    function chuyenVaiTro(vaiTro) {
        const lblHoTenText = document.getElementById('lblHoTenText');
        const inputHoTen = document.getElementById('ho_ten');
        const inputDiaChi = document.getElementById('dia_chi');
        const optNguoiMua = document.getElementById('opt_role_nguoi_mua');
        const optNongHo = document.getElementById('opt_role_nong_ho');

        if (vaiTro === 'nong_ho') {
            if (lblHoTenText) lblHoTenText.textContent = 'Tên nhà vườn';
            if (inputHoTen) inputHoTen.placeholder = 'Ví dụ: Vườn Thanh Long Chú Ba';
            if (inputDiaChi) inputDiaChi.placeholder = 'Ví dụ: Xã Bình Phú, Càng Long';
            if (optNongHo) optNongHo.classList.add('active');
            if (optNguoiMua) optNguoiMua.classList.remove('active');
        } else {
            if (lblHoTenText) lblHoTenText.textContent = 'Họ và tên người mua';
            if (inputHoTen) inputHoTen.placeholder = 'Ví dụ: Nguyễn Đăng Khôi';
            if (inputDiaChi) inputDiaChi.placeholder = 'Ví dụ: Số 12, Đường 30/4';
            if (optNguoiMua) optNguoiMua.classList.add('active');
            if (optNongHo) optNongHo.classList.remove('active');
        }
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
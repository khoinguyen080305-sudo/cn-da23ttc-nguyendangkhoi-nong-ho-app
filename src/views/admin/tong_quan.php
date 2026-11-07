<?php
// views/admin/tong_quan.php
// Bảng Điều Khiển Quản Trị Tinh Gọn (Admin Single-Pane Command Center)
// Khởi tạo giá trị mặc định để IDE không báo lỗi đỏ biến chưa định nghĩa
$dsNguoiDung = $dsNguoiDung ?? [];
$dsDanhMuc   = $dsDanhMuc ?? [];
$dsChoDuyet  = $dsChoDuyet ?? [];

require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid px-lg-4 py-2">
    <!-- TIÊU ĐỀ TRANG QUẢN TRỊ -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-1" style="color: #064e3b;">
                <i class="bi bi-shield-check text-success me-2"></i>Trung Tâm Quản Trị
            </h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 fs-6 rounded-pill">
                <i class="bi bi-person-badge-fill me-1"></i>Tài khoản Quản Trị
            </span>
        </div>
    </div>

    <!-- 1. KHỐI NÔNG HỘ CHỜ DUYỆT (ƯU TIÊN HIỂN THỊ NẾU CÓ) -->
    <?php if (!empty($dsChoDuyet)): ?>
        <div id="khoiChoDuyet" class="bg-white rounded-3 border border-warning shadow-sm p-3 p-md-4 mb-3" style="border-left: 5px solid #f59e0b !important; scroll-margin-top: 80px;">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pb-3 mb-3 border-bottom border-warning border-opacity-25">
                <div class="fw-bold text-dark fs-5 d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark px-2.5 py-1.5 rounded-pill fs-6">
                        <i class="bi bi-bell-fill me-1"></i><?= count($dsChoDuyet) ?> YÊU CẦU
                    </span>
                    <span>Hồ Sơ Nông Hộ Đang Chờ Phê Duyệt</span>
                </div>
                <div class="small text-muted">
                    <i class="bi bi-info-circle text-warning me-1"></i>Cần xác minh danh tính để nông hộ bắt đầu đăng bán sản phẩm
                </div>
            </div>

            <!-- GIAO DIỆN DESKTOP: BẢNG RỘNG DỄ NHÌN -->
            <div class="d-none d-md-block table-responsive">
                <table class="table align-middle table-hover mb-0 admin-table">
                    <thead>
                        <tr>
                            <th style="width: 26%;">Tên</th>
                            <th style="width: 17%;">Số Điện Thoại</th>
                            <th style="width: 12%;">Vai Trò</th>
                            <th style="width: 21%;">Địa Chỉ</th>
                            <th style="width: 11%;">Trạng Thái</th>
                            <th class="text-end" style="width: 13%; min-width: 200px;">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dsChoDuyet as $nh): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($nh['ho_ten']) ?></div>
                                    <div class="text-muted small">
                                        Đăng ký: <?= !empty($nh['ngay_tao']) ? dinh_dang_ngay($nh['ngay_tao']) : 'Gần đây' ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="nh-phone-badge" title="Số điện thoại tài khoản nông hộ">
                                        <i class="bi bi-telephone-fill"></i><?= htmlspecialchars($nh['sdt']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-pill bg-success-subtle text-success border border-success-subtle">Nông Hộ</span>
                                </td>
                                <td>
                                    <?php if (!empty($nh['tinh'])): ?>
                                        <div class="mb-1">
                                            <span class="badge bg-light text-dark border px-2 py-1">
                                                <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($nh['tinh']) ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($nh['dia_chi'])): ?>
                                        <div class="text-muted small text-truncate" style="max-width: 240px;" title="<?= htmlspecialchars($nh['dia_chi']) ?>">
                                            <i class="bi bi-pin-map text-secondary me-1"></i><?= htmlspecialchars($nh['dia_chi']) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle">Chờ duyệt</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1.5 justify-content-end">
                                        <button type="button" class="btn btn-sm btn-success px-2.5 py-1.5 fw-bold text-nowrap" 
                                                title="Phê duyệt cho phép nông hộ hoạt động"
                                                onclick="xacNhanDuyetNongHo(<?= $nh['ma_nguoi_dung'] ?>, '<?= htmlspecialchars(addslashes($nh['ho_ten'])) ?>')">
                                            <i class="bi bi-check-circle-fill me-1"></i>Phê Duyệt
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger px-2.5 py-1.5 fw-bold text-nowrap" 
                                                title="Từ chối và xóa hồ sơ này khỏi CSDL"
                                                onclick="xacNhanTuChoiNongHo(<?= $nh['ma_nguoi_dung'] ?>, '<?= htmlspecialchars(addslashes($nh['ho_ten'])) ?>')">
                                            <i class="bi bi-trash3-fill me-1"></i>Từ Chối &amp; Xóa
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- GIAO DIỆN MOBILE: DẠNG THẺ GỌN GÀNG, KHÔNG CUỘN NGANG, ĐỦ THAO TÁC 1 CHẠM -->
            <div class="d-block d-md-none d-flex flex-column gap-2.5">
                <?php foreach ($dsChoDuyet as $nh): ?>
                    <div class="admin-card-item">
                        <div class="admin-card-header">
                            <div>
                                <div class="admin-card-title"><?= htmlspecialchars($nh['ho_ten']) ?></div>
                                <div class="admin-card-sub">
                                    Đăng ký: <?= !empty($nh['ngay_tao']) ? dinh_dang_ngay($nh['ngay_tao']) : 'Gần đây' ?>
                                </div>
                            </div>
                            <span class="status-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle">Chờ duyệt</span>
                        </div>

                        <div class="admin-card-tags">
                            <span class="nh-phone-badge">
                                <i class="bi bi-telephone-fill"></i><?= htmlspecialchars($nh['sdt']) ?>
                            </span>
                            <span class="status-pill bg-success-subtle text-success border border-success-subtle">Nông Hộ</span>
                            <?php if (!empty($nh['tinh'])): ?>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($nh['tinh']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($nh['dia_chi'])): ?>
                            <div class="text-muted small mb-2 text-truncate" title="<?= htmlspecialchars($nh['dia_chi']) ?>">
                                <i class="bi bi-pin-map text-secondary me-1"></i><?= htmlspecialchars($nh['dia_chi']) ?>
                            </div>
                        <?php endif; ?>

                        <div class="admin-card-actions">
                            <button type="button" class="btn btn-sm btn-success" 
                                    onclick="xacNhanDuyetNongHo(<?= $nh['ma_nguoi_dung'] ?>, '<?= htmlspecialchars(addslashes($nh['ho_ten'])) ?>')">
                                <i class="bi bi-check-circle-fill"></i> Phê Duyệt
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                    onclick="xacNhanTuChoiNongHo(<?= $nh['ma_nguoi_dung'] ?>, '<?= htmlspecialchars(addslashes($nh['ho_ten'])) ?>')">
                                <i class="bi bi-trash3-fill"></i> Từ Chối
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- 2. BỘ LỌC TÌM KIẾM VÀ DANH SÁCH THÀNH VIÊN -->
    <div id="khoiThanhVien" class="bg-white rounded-3 border shadow-sm p-3 p-md-4 mb-3" style="scroll-margin-top: 80px;">
        <!-- TIÊU ĐỀ & THỐNG KÊ NHANH -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pb-3 mb-3 border-bottom">
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-people-fill text-success"></i>
                <span>Danh Sách Thành Viên</span>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fs-6 px-2.5 py-1">
                    <?= count($dsNguoiDung) ?> tài khoản
                </span>
            </h5>

            <?php
                $countNongHo = 0; 
                $countNguoiMua = 0;
                foreach ($dsNguoiDung as $u) {
                    if ($u['vai_tro'] === 'nong_ho') $countNongHo++;
                    elseif ($u['vai_tro'] === 'nguoi_mua') $countNguoiMua++;
                }
            ?>
            <div class="d-none d-md-flex align-items-center gap-2 small">
                <span class="badge bg-light text-dark border px-2.5 py-1.5"><?= $countNongHo ?> Nông Hộ</span>
                <span class="badge bg-light text-dark border px-2.5 py-1.5"><?= $countNguoiMua ?> Người Mua</span>
            </div>
        </div>

        <!-- BỘ LỌC DÀN HÀNG NGANG CÂN ĐỐI (100% WIDTH GRID) -->
        <form action="index.php" method="GET" class="bg-light p-2.5 p-md-3 rounded-3 border mb-3">
            <input type="hidden" name="trang" value="admin_tong_quan">
            <div class="row g-2 align-items-center">
                <!-- Ô TÌM KIẾM HỌ TÊN / SĐT / ĐỊA CHỈ -->
                <div class="col-12 col-md-5 col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="tu_khoa" class="form-control border-start-0 ps-0 bg-white" 
                               placeholder="Tìm họ tên, SĐT, tỉnh thành..." 
                               value="<?= htmlspecialchars($_GET['tu_khoa'] ?? '') ?>">
                    </div>
                </div>

                <!-- LỌC VAI TRÒ (KHÔNG DÙNG ICON) -->
                <div class="col-6 col-md-3 col-lg-3">
                    <select name="vai_tro" class="form-select bg-white fw-semibold" onchange="this.form.submit()">
                        <option value="">Tất cả vai trò</option>
                        <option value="nong_ho" <?= ($_GET['vai_tro'] ?? '') === 'nong_ho' ? 'selected' : '' ?>>Nông Hộ</option>
                        <option value="nguoi_mua" <?= ($_GET['vai_tro'] ?? '') === 'nguoi_mua' ? 'selected' : '' ?>>Người Mua</option>
                        <option value="admin" <?= ($_GET['vai_tro'] ?? '') === 'admin' ? 'selected' : '' ?>>Quản Trị</option>
                    </select>
                </div>

                <!-- LỌC TRẠNG THÁI (KHÔNG DÙNG CHẤM TRÒN) -->
                <div class="col-6 col-md-4 col-lg-3">
                    <select name="trang_thai" class="form-select bg-white fw-semibold" onchange="this.form.submit()">
                        <option value="">Tất cả trạng thái</option>
                        <option value="da_duyet" <?= ($_GET['trang_thai'] ?? '') === 'da_duyet' ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="cho_duyet" <?= ($_GET['trang_thai'] ?? '') === 'cho_duyet' ? 'selected' : '' ?>>Chờ duyệt</option>
                        <option value="khoa" <?= ($_GET['trang_thai'] ?? '') === 'khoa' ? 'selected' : '' ?>>Bị khóa</option>
                    </select>
                </div>

                <!-- NÚT LỌC VÀ ĐẶT LẠI -->
                <div class="col-12 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-clean fw-bold px-3 flex-fill text-nowrap">
                        <i class="bi bi-funnel-fill me-1"></i>Lọc
                    </button>
                    <?php if (!empty($_GET['tu_khoa']) || !empty($_GET['vai_tro']) || !empty($_GET['trang_thai'])): ?>
                        <a href="index.php?trang=admin_tong_quan" class="btn btn-outline-secondary fw-semibold px-2.5 text-nowrap" title="Bỏ lọc, xem tất cả">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <!-- GIAO DIỆN DESKTOP: BẢNG TÀI KHOẢN RỘNG RÃI -->
        <div class="d-none d-md-block table-responsive">
            <table class="table align-middle table-hover mb-0 admin-table">
                <thead>
                    <tr>
                        <th style="width: 26%;">Thành Viên</th>
                        <th style="width: 17%;">Số Điện Thoại</th>
                        <th style="width: 12%;">Vai Trò</th>
                        <th style="width: 21%;">Địa Chỉ</th>
                        <th style="width: 11%;">Trạng Thái</th>
                        <th class="text-end" style="width: 13%; min-width: 200px;">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($dsNguoiDung)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-person-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                    <div class="fw-bold fs-6">Không tìm thấy thành viên nào phù hợp</div>
                                    <div class="small mt-1">Vui lòng thử lại với từ khóa hoặc điều kiện lọc khác.</div>
                                    <a href="index.php?trang=admin_tong_quan" class="btn btn-sm btn-outline-secondary mt-3">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Xem Tất Cả Thành Viên
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($dsNguoiDung as $user): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark fs-6 text-truncate" title="<?= htmlspecialchars($user['ho_ten']) ?>">
                                        <?= htmlspecialchars($user['ho_ten']) ?>
                                    </div>
                                    <div class="text-muted small">
                                        Tham gia: <?= dinh_dang_ngay($user['ngay_tao']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column align-items-start gap-1">
                                        <span class="nh-phone-badge" title="Số điện thoại tài khoản">
                                            <i class="bi bi-telephone-fill"></i><?= htmlspecialchars($user['sdt']) ?>
                                        </span>
                                        <?php if (!empty($user['phai_doi_mk'])): ?>
                                            <span class="badge bg-warning text-dark border border-warning-subtle small" title="Đang dùng mật khẩu tạm, chờ đổi MK mới">
                                                <i class="bi bi-key-fill me-1"></i>Chờ đổi MK
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($user['vai_tro'] === 'admin'): ?>
                                        <span class="status-pill bg-dark-subtle text-dark border border-secondary-subtle">Quản Trị</span>
                                    <?php elseif ($user['vai_tro'] === 'nong_ho'): ?>
                                        <span class="status-pill bg-success-subtle text-success border border-success-subtle">Nông Hộ</span>
                                    <?php else: ?>
                                        <span class="status-pill bg-primary-subtle text-primary border border-primary-subtle">Người Mua</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($user['tinh'])): ?>
                                        <div class="mb-1">
                                            <span class="badge bg-light text-dark border px-2 py-1">
                                                <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($user['tinh']) ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($user['dia_chi'])): ?>
                                        <div class="text-muted small text-truncate" style="max-width: 240px;" title="<?= htmlspecialchars($user['dia_chi']) ?>">
                                            <i class="bi bi-pin-map text-secondary me-1"></i><?= htmlspecialchars($user['dia_chi']) ?>
                                        </div>
                                    <?php elseif (empty($user['tinh'])): ?>
                                        <span class="text-muted ps-2">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['trang_thai'] === 'da_duyet'): ?>
                                        <span class="status-pill bg-success-subtle text-success border border-success-subtle">Hoạt động</span>
                                    <?php elseif ($user['trang_thai'] === 'cho_duyet'): ?>
                                        <span class="status-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle">Chờ duyệt</span>
                                    <?php else: ?>
                                        <span class="status-pill bg-danger-subtle text-danger border border-danger-subtle">Bị khóa</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($user['vai_tro'] !== 'admin'): ?>
                                        <div class="d-inline-flex gap-1 justify-content-end align-items-center">
                                            <!-- ĐỔI SỐ ĐIỆN THOẠI TÀI KHOẢN -->
                                            <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-1 fw-semibold text-nowrap" 
                                                    title="Đổi số điện thoại đăng nhập khi người dùng yêu cầu"
                                                    onclick="moModalDoiSDT(<?= $user['ma_nguoi_dung'] ?>, '<?= htmlspecialchars(addslashes($user['ho_ten'])) ?>', '<?= htmlspecialchars($user['sdt']) ?>')">
                                                <i class="bi bi-telephone-outbound me-1"></i>Đổi SĐT
                                            </button>

                                            <!-- CẤP MẬT KHẨU TẠM 1 CHẠM -->
                                            <button type="button" class="btn btn-sm btn-warning text-dark px-2 py-1 fw-semibold text-nowrap shadow-none" 
                                                    title="Cấp lại mật khẩu tạm khi người dùng quên"
                                                    onclick="capMatKhauTamChoUser(<?= $user['ma_nguoi_dung'] ?>, '<?= htmlspecialchars(addslashes($user['ho_ten'])) ?>', '<?= htmlspecialchars($user['sdt']) ?>')">
                                                <i class="bi bi-key-fill me-1"></i>Cấp MK
                                            </button>

                                            <!-- KHÓA HOẶC MỞ KHÓA -->
                                            <?php if ($user['trang_thai'] === 'khoa'): ?>
                                                <button type="button" class="btn btn-sm btn-success px-2 py-1 fw-semibold text-nowrap" 
                                                        title="Mở khóa tài khoản"
                                                        onclick="doiTrangThaiUser(<?= $user['ma_nguoi_dung'] ?>, 'da_duyet', '<?= htmlspecialchars(addslashes($user['ho_ten'])) ?>')">
                                                    <i class="bi bi-unlock-fill me-1"></i>Mở
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1 fw-semibold text-nowrap" 
                                                        title="Khóa tài khoản"
                                                        onclick="doiTrangThaiUser(<?= $user['ma_nguoi_dung'] ?>, 'khoa', '<?= htmlspecialchars(addslashes($user['ho_ten'])) ?>')">
                                                    <i class="bi bi-lock-fill me-1"></i>Khóa
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 small">— Quản trị viên —</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- GIAO DIỆN MOBILE: DẠNG THẺ THÀNH VIÊN VỪA VẶN MÀN HÌNH, NÚT BẤM DÀN ĐỀU KHÔNG TRÀN -->
        <div class="d-block d-md-none d-flex flex-column gap-2.5">
            <?php if (empty($dsNguoiDung)): ?>
                <div class="text-center py-4 text-muted bg-light rounded-3 border">
                    <i class="bi bi-person-x fs-1 d-block mb-1 text-secondary opacity-50"></i>
                    <div>Không tìm thấy thành viên nào phù hợp</div>
                </div>
            <?php else: ?>
                <?php foreach ($dsNguoiDung as $user): ?>
                    <div class="admin-card-item">
                        <div class="admin-card-header">
                            <div>
                                <div class="admin-card-title"><?= htmlspecialchars($user['ho_ten']) ?></div>
                                <div class="admin-card-sub">
                                    Tham gia: <?= dinh_dang_ngay($user['ngay_tao']) ?>
                                </div>
                            </div>
                            <div class="d-flex flex-column align-items-end gap-1">
                                <?php if ($user['vai_tro'] === 'admin'): ?>
                                    <span class="status-pill bg-dark-subtle text-dark border border-secondary-subtle">Quản Trị</span>
                                <?php elseif ($user['vai_tro'] === 'nong_ho'): ?>
                                    <span class="status-pill bg-success-subtle text-success border border-success-subtle">Nông Hộ</span>
                                <?php else: ?>
                                    <span class="status-pill bg-primary-subtle text-primary border border-primary-subtle">Người Mua</span>
                                <?php endif; ?>

                                <?php if ($user['trang_thai'] === 'da_duyet'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.72rem;">Hoạt động</span>
                                <?php elseif ($user['trang_thai'] === 'cho_duyet'): ?>
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size: 0.72rem;">Chờ duyệt</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.72rem;">Bị khóa</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="admin-card-tags">
                            <span class="nh-phone-badge">
                                <i class="bi bi-telephone-fill"></i><?= htmlspecialchars($user['sdt']) ?>
                            </span>
                            <?php if (!empty($user['phai_doi_mk'])): ?>
                                <span class="badge bg-warning text-dark border border-warning-subtle">
                                    <i class="bi bi-key-fill me-1"></i>Chờ đổi MK
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($user['tinh'])): ?>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($user['tinh']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($user['dia_chi'])): ?>
                            <div class="text-muted small mb-2 text-truncate" title="<?= htmlspecialchars($user['dia_chi']) ?>">
                                <i class="bi bi-pin-map text-secondary me-1"></i><?= htmlspecialchars($user['dia_chi']) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($user['vai_tro'] !== 'admin'): ?>
                            <div class="admin-card-actions">
                                <button type="button" class="btn btn-outline-secondary" 
                                        onclick="moModalDoiSDT(<?= $user['ma_nguoi_dung'] ?>, '<?= htmlspecialchars(addslashes($user['ho_ten'])) ?>', '<?= htmlspecialchars($user['sdt']) ?>')">
                                    <i class="bi bi-telephone-outbound"></i> Đổi SĐT
                                </button>
                                <button type="button" class="btn btn-warning text-dark" 
                                        onclick="capMatKhauTamChoUser(<?= $user['ma_nguoi_dung'] ?>, '<?= htmlspecialchars(addslashes($user['ho_ten'])) ?>', '<?= htmlspecialchars($user['sdt']) ?>')">
                                    <i class="bi bi-key-fill"></i> Cấp MK
                                </button>
                                <?php if ($user['trang_thai'] === 'khoa'): ?>
                                    <button type="button" class="btn btn-success" 
                                            onclick="doiTrangThaiUser(<?= $user['ma_nguoi_dung'] ?>, 'da_duyet', '<?= htmlspecialchars(addslashes($user['ho_ten'])) ?>')">
                                        <i class="bi bi-unlock-fill"></i> Mở
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="doiTrangThaiUser(<?= $user['ma_nguoi_dung'] ?>, 'khoa', '<?= htmlspecialchars(addslashes($user['ho_ten'])) ?>')">
                                        <i class="bi bi-lock-fill"></i> Khóa
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-1.5 border-top small text-muted">— Quản trị viên hệ thống —</div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- 3. QUẢN LÝ DANH MỤC NÔNG SẢN -->
    <div id="khoiDanhMuc" class="bg-white rounded-3 border shadow-sm p-3 p-md-4 mb-3" style="scroll-margin-top: 80px;">
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 border-bottom pb-3 mb-3">
            <div>
                <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-tags-fill text-success"></i>
                    <span>Danh Mục</span>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill fs-6"><?= count($dsDanhMuc) ?> ngành</span>
                </h5>
            </div>

            <!-- FORM THÊM DANH MỤC VỪA VẶN MỌI MÀN HÌNH -->
            <form action="index.php?trang=admin_them_danh_muc" method="POST" class="d-flex align-items-center gap-2 w-100 w-sm-auto">
                <input type="text" name="ten_danh_muc" class="form-control fw-semibold flex-grow-1" placeholder="Tên danh mục mới..." required style="min-width: 0;">
                <button type="submit" class="btn btn-clean fw-bold text-nowrap px-3 py-2">
                    <i class="bi bi-plus-lg me-1"></i>Thêm
                </button>
            </form>
        </div>

        <div class="d-flex flex-wrap gap-2 pt-1">
            <?php foreach ($dsDanhMuc as $dm): ?>
                <div class="d-flex align-items-center gap-2 bg-light border rounded-pill px-3 py-1.5 fs-6 shadow-sm-hover" style="max-width: 100%;">
                    <span class="fw-bold text-dark text-truncate" style="max-width: 180px;"><?= htmlspecialchars($dm['ten_danh_muc']) ?></span>
                    <span class="badge bg-white text-muted border rounded-pill text-nowrap"><?= $dm['so_san_pham'] ?> sản phẩm</span>
                    
                    <button type="button" class="btn btn-link text-danger p-0 border-0 ms-1 opacity-75 flex-shrink-0" 
                            title="Xóa danh mục"
                            onclick="xacNhanXoaDanhMuc(<?= $dm['ma_danh_muc'] ?>, '<?= htmlspecialchars(addslashes($dm['ten_danh_muc'])) ?>', <?= $dm['so_san_pham'] ?>)">
                        <i class="bi bi-x-circle-fill fs-5"></i>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
/* ĐỒNG BỘ ĐỘ RỘNG & CANH LỀ BẢNG QUẢN TRỊ */
.admin-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
}
.admin-table thead th {
    background-color: #f8fafc;
    color: #475569;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px 14px;
    border-bottom: 2px solid #e2e8f0;
    border-top: none;
    white-space: nowrap;
}
.admin-table tbody td {
    padding: 13px 14px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}
.admin-table tbody tr:hover {
    background-color: #f8fafc;
}
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 9999px;
    font-size: 0.82rem;
    font-weight: 600;
    white-space: nowrap;
}
.shadow-sm-hover {
    transition: all 0.2s ease;
}
.shadow-sm-hover:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}
</style>

<!-- MODAL XÁC NHẬN HÀNH ĐỘNG IN-APP (TINH GỌN, ÍT CHỮ, THAY THẾ WINDOW.CONFIRM) -->
<div class="modal fade" id="modalXacNhanAdmin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 360px;">
        <div class="modal-content border-0 shadow-sm rounded-4">
            <div class="modal-body p-4 text-center">
                <h6 class="fw-bold text-dark mb-2 fs-5" id="xacNhanAdminTieuDe">Xác nhận</h6>
                <div class="text-muted mb-4 fs-6" id="xacNhanAdminNoiDung"></div>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light border px-3 py-2 fw-semibold rounded-pill flex-fill" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn px-3 py-2 fw-bold rounded-pill flex-fill" id="xacNhanAdminBtnOk">Đồng ý</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL THÔNG BÁO IN-APP (GỌN SẠCH, THAY THẾ TOAST & WINDOW.ALERT) -->
<div class="modal fade" id="modalThongBaoAdmin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 360px;">
        <div class="modal-content border-0 shadow-sm rounded-4">
            <div class="modal-body p-4 text-center">
                <h6 class="fw-bold text-dark mb-2 fs-5" id="thongBaoAdminTieuDe">Thông báo</h6>
                <div class="text-muted mb-4 fs-6" id="thongBaoAdminNoiDung"></div>
                <button type="button" class="btn btn-clean px-4 py-2 fw-bold rounded-pill w-100" data-bs-dismiss="modal">Đã hiểu</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL HIỂN THỊ MẬT KHẨU TẠM VỪA CẤP (GỌN SẠCH, ÍT CHỮ) -->
<div class="modal fade" id="modalHienThiMKTam" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content border-0 shadow-sm rounded-4">
            <div class="modal-header py-3 bg-light border-0">
                <h6 class="modal-title fw-bold text-dark mb-0 fs-5">Mật Khẩu Tạm Đã Cấp</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="text-muted mb-1 fs-6">Thành viên: <strong id="mkTamHoTen" class="text-dark"></strong></div>
                <div class="text-muted mb-3 fs-6">SĐT: <span class="fw-bold text-dark" id="mkTamTaiKhoan"></span></div>

                <div class="p-3 bg-light rounded-3 border mb-3">
                    <div class="text-muted small mb-1">Mật khẩu mới:</div>
                    <div class="fs-2 fw-bold text-danger user-select-all" id="mkTamGiaTri"></div>
                </div>

                <div class="text-muted small">
                    Hệ thống sẽ bắt buộc đổi mật khẩu mới khi đăng nhập.
                </div>
            </div>
            <div class="modal-footer py-3 bg-light border-0 justify-content-center">
                <button type="button" class="btn btn-clean px-4 py-2 fw-bold rounded-pill" data-bs-dismiss="modal">Đã Sao Chép &amp; Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL ĐỔI SỐ ĐIỆN THOẠI TÀI KHOẢN -->
<div class="modal fade" id="modalDoiSDT" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content border-0 shadow-sm rounded-4">
            <div class="modal-header py-3 bg-light border-0">
                <h6 class="modal-title fw-bold text-dark mb-0 fs-5">Đổi Số Điện Thoại</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?trang=admin_doi_sdt" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="ma_nguoi_dung" id="doiSdtMaUser">
                    <div class="text-muted mb-1 fs-6">Thành viên: <strong id="doiSdtHoTen" class="text-dark"></strong></div>
                    <div class="text-muted mb-3 fs-6">SĐT hiện tại: <span class="fw-bold text-dark" id="doiSdtCu"></span></div>

                    <div class="mb-3">
                        <label for="sdt_moi" class="form-label small fw-bold text-dark mb-1">
                            Số điện thoại mới (10 số) <span class="text-danger">*</span>
                        </label>
                        <input type="tel" 
                               class="form-control fw-bold rounded-pill px-3 py-2" 
                               id="sdt_moi" 
                               name="sdt_moi" 
                               placeholder="Ví dụ: 0912345678" 
                               maxlength="10" 
                               required
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                </div>
                <div class="modal-footer py-3 bg-light border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">Lưu Số Mới</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Biến lưu callback xác nhận hành động
let adminXacNhanCallback = null;

// Hàm mở modal xác nhận in-app tinh gọn, ít chữ
function moModalXacNhanAdmin(tieuDe, thongDiep, onXacNhan, nutNhan = 'Đồng ý', mauNut = 'btn-success') {
    adminXacNhanCallback = onXacNhan;
    document.getElementById('xacNhanAdminTieuDe').textContent = tieuDe;
    document.getElementById('xacNhanAdminNoiDung').innerHTML = thongDiep;
    
    const btnOk = document.getElementById('xacNhanAdminBtnOk');
    btnOk.className = `btn px-3 py-2 fw-bold rounded-pill flex-fill ${mauNut}`;
    btnOk.textContent = nutNhan;
    btnOk.onclick = function() {
        if (typeof adminXacNhanCallback === 'function') {
            adminXacNhanCallback();
        }
        const modalEl = document.getElementById('modalXacNhanAdmin');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    };

    const modal = new bootstrap.Modal(document.getElementById('modalXacNhanAdmin'));
    modal.show();
}

// Hàm mở modal thông báo in-app gọn sạch
function moThongBaoAdmin(tieuDe, thongDiep) {
    document.getElementById('thongBaoAdminTieuDe').textContent = tieuDe;
    document.getElementById('thongBaoAdminNoiDung').innerHTML = thongDiep;
    const modal = new bootstrap.Modal(document.getElementById('modalThongBaoAdmin'));
    modal.show();
}

// Mở modal đổi SĐT cho user
function moModalDoiSDT(id, hoTen, sdt) {
    document.getElementById('doiSdtMaUser').value = id;
    document.getElementById('doiSdtHoTen').textContent = hoTen;
    document.getElementById('doiSdtCu').textContent = sdt;
    document.getElementById('sdt_moi').value = '';
    const modal = new bootstrap.Modal(document.getElementById('modalDoiSDT'));
    modal.show();
}

// Phê duyệt nông hộ
function xacNhanDuyetNongHo(id, hoTen) {
    moModalXacNhanAdmin(
        'Phê duyệt nông hộ',
        `Duyệt hồ sơ nhà vườn <strong>${hoTen}</strong>?`,
        function() {
            window.location.href = 'index.php?trang=admin_duyet_nong_ho&id=' + id;
        },
        'Phê Duyệt',
        'btn-success'
    );
}

// Từ chối và xóa hồ sơ nông hộ
function xacNhanTuChoiNongHo(id, hoTen) {
    moModalXacNhanAdmin(
        'Từ chối hồ sơ',
        `Xóa nhà vườn <strong>${hoTen}</strong> khỏi hệ thống?`,
        function() {
            window.location.href = 'index.php?trang=admin_tu_choi_nong_ho&id=' + id;
        },
        'Xóa',
        'btn-danger'
    );
}

// Khóa hoặc mở khóa tài khoản
function doiTrangThaiUser(id, trangThai, hoTen) {
    const isKhoa = (trangThai === 'khoa');
    moModalXacNhanAdmin(
        isKhoa ? 'Khóa tài khoản' : 'Mở khóa tài khoản',
        isKhoa ? `Khóa tài khoản <strong>${hoTen}</strong>?` : `Mở khóa cho <strong>${hoTen}</strong>?`,
        function() {
            window.location.href = `index.php?trang=admin_doi_trang_thai&id=${id}&trang_thai=${trangThai}`;
        },
        isKhoa ? 'Khóa' : 'Mở khóa',
        isKhoa ? 'btn-danger' : 'btn-success'
    );
}

// Cấp mật khẩu tạm 1 chạm qua AJAX (không dùng toast)
function capMatKhauTamChoUser(id, hoTen, sdt) {
    moModalXacNhanAdmin(
        'Cấp mật khẩu tạm',
        `Tạo mật khẩu tạm cho <strong>${hoTen}</strong>?`,
        function() {
            const formData = new FormData();
            formData.append('ma_nguoi_dung', id);

            fetch('index.php?trang=admin_cap_mk_tam', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.thanh_cong) {
                    document.getElementById('mkTamHoTen').textContent = data.ho_ten;
                    document.getElementById('mkTamTaiKhoan').textContent = data.sdt || data.tai_khoan;
                    document.getElementById('mkTamGiaTri').textContent = data.mat_khau_tam;

                    const modal = new bootstrap.Modal(document.getElementById('modalHienThiMKTam'));
                    modal.show();
                } else {
                    moThongBaoAdmin('Lỗi', data.thong_bao || 'Không thể cấp mật khẩu tạm.');
                }
            })
            .catch(err => {
                console.error(err);
                moThongBaoAdmin('Lỗi', 'Không thể kết nối đến máy chủ.');
            });
        },
        'Cấp MK',
        'btn-warning text-dark'
    );
}

// Xóa danh mục nông sản
function xacNhanXoaDanhMuc(id, tenDanhMuc, soLuongSP) {
    if (soLuongSP > 0) {
        moThongBaoAdmin('Không thể xóa', `Ngành <strong>${tenDanhMuc}</strong> đang có ${soLuongSP} nông sản bày bán.`);
        return;
    }

    moModalXacNhanAdmin(
        'Xóa ngành hàng',
        `Xóa ngành <strong>${tenDanhMuc}</strong>?`,
        function() {
            window.location.href = 'index.php?trang=admin_xoa_danh_muc&id=' + id;
        },
        'Xóa',
        'btn-danger'
    );
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

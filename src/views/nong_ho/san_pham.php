<?php
// views/nong_ho/san_pham.php
// Quản lý danh sách nông sản của Nông Hộ - Tối ưu hiển thị đa thiết bị (Mobile, Tablet, Desktop)
$dsSanPham = $dsSanPham ?? [];

require_once __DIR__ . '/../layouts/header.php';
$nguoiDung = nguoi_dung_hien_tai();
$soDangBan = 0;
$soDangAn = 0;
foreach ($dsSanPham as $spItem) {
    if ($spItem['an'] == 1) {
        $soDangAn++;
    } else {
        $soDangBan++;
    }
}
?>

<div class="container-fluid px-2 px-sm-3 px-lg-4 py-3">
    <!-- 1. TIÊU ĐỀ & NÚT THÊM MỚI (ĐỒNG BỘ 1 HÀNG TRỰC QUAN TRÊN CẢ MOBILE & DESKTOP) -->
    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
        <div class="d-flex align-items-center gap-2 min-w-0">
            <h4 class="fw-bold mb-0 text-truncate" style="letter-spacing: -0.3px; font-size: calc(1.05rem + 0.35vw);">
                <i class="bi bi-boxes text-success me-1.5"></i>Nông Sản Của Bạn
            </h4>
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fw-semibold px-2 py-0.5 fs-7">
                <?= count($dsSanPham) ?>
            </span>
        </div>
        <div>
            <?php if (($nguoiDung['trang_thai'] ?? '') === 'cho_duyet'): ?>
                <button type="button" class="btn btn-secondary disabled rounded-pill px-3 py-1.5 fw-semibold small text-nowrap"
                    title="Đang chờ duyệt tài khoản">
                    <i class="bi bi-hourglass-split me-1"></i>Chờ duyệt
                </button>
            <?php else: ?>
                <a href="index.php?trang=nong_ho_form_san_pham"
                    class="btn btn-clean px-3 py-1.5 fw-bold rounded-pill shadow-sm d-inline-flex align-items-center gap-1.5 text-nowrap"
                    style="font-size: 0.88rem;">
                    <i class="bi bi-plus-lg fs-6"></i>
                    <span>Đăng Bán</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (empty($dsSanPham)): ?>
        <div class="bg-white rounded-4 border shadow-sm p-5 text-center my-4">
            <div class="rounded-circle bg-success-subtle d-inline-flex align-items-center justify-content-center mb-3"
                style="width: 80px; height: 80px;">
                <i class="bi bi-tree text-success fs-1"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">Vườn của bạn chưa có nông sản nào</h4>
            <p class="text-muted mb-4 fs-6" style="max-width: 460px; margin: 0 auto;">
                Hãy đăng bán các mặt hàng đang có trong vườn để các thương lái, vựa thu mua và nhà phân phối có thể liên hệ
                đặt hàng.
            </p>
        </div>
    <?php else: ?>

        <!-- 2. BỘ CÔNG CỤ ĐIỀU HƯỚNG: SEGMENTED TABS LỌC + TÌM KIẾM + CHUYỂN CHẾ ĐỘ XEM -->
        <div class="nh-sp-toolbar rounded-4 border shadow-sm p-2 p-sm-2.5 mb-3.5">
            <div class="row g-2 align-items-center">
                <!-- TABS LỌC TRẠNG THÁI (SEGMENTED CONTROL CHUẨN UX) -->
                <div class="col-12 col-md-auto">
                    <div class="nh-sp-filter-track" id="filterStatusGroup">
                        <button type="button" class="nh-filter-btn active"
                            data-filter="tat_ca" onclick="locTheoTrangThai('tat_ca')">
                            <span>Tất cả</span>
                            <span class="nh-filter-num"><?= count($dsSanPham) ?></span>
                        </button>
                        <button type="button" class="nh-filter-btn"
                            data-filter="dang_ban" onclick="locTheoTrangThai('dang_ban')">
                            <span>Đang bán</span>
                            <span class="nh-filter-num"><?= $soDangBan ?></span>
                        </button>
                        <button type="button" class="nh-filter-btn"
                            data-filter="dang_an" onclick="locTheoTrangThai('dang_an')">
                            <span>Đang ẩn</span>
                            <span class="nh-filter-num"><?= $soDangAn ?></span>
                        </button>
                    </div>
                </div>

                <!-- Ô TÌM KIẾM VÀ CHUYỂN CHẾ ĐỘ HIỂN THỊ (GRID / TABLE) -->
                <div class="col-12 col-md d-flex align-items-center justify-content-md-end gap-2">
                    <div class="input-group input-group-sm flex-grow-1 nh-search-input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="nhInputTimKiem"
                            class="form-control bg-light border-start-0 rounded-end-pill pe-3"
                            placeholder="Tìm tên, danh mục, mã SP..." oninput="xuLyTimKiem()">
                    </div>

                    <!-- CHUYỂN CHẾ ĐỘ XEM TRÊN TABLET & DESKTOP -->
                    <div class="d-none d-md-inline-flex btn-group btn-group-sm p-0.5 bg-light rounded-pill border"
                        role="group">
                        <button type="button" class="btn btn-sm rounded-pill fw-bold px-2.5 py-1 btn-success text-white"
                            id="btnCheDoGrid" onclick="chuyenCheDoXem('grid')" title="Hiển thị dạng thẻ hình ảnh">
                            <i class="bi bi-grid-fill me-1"></i>Thẻ
                        </button>
                        <button type="button" class="btn btn-sm rounded-pill fw-bold px-2.5 py-1 btn-light text-muted"
                            id="btnCheDoTable" onclick="chuyenCheDoXem('table')" title="Hiển thị dạng bảng chi tiết">
                            <i class="bi bi-table me-1"></i>Bảng
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. GIAO DIỆN CHÍNH: LƯỚI THẺ RESPONSIVE HIỂN THỊ ĐẸP TRÊN MỌI THIẾT BỊ -->
        <!-- Mobile (<576px): 1 cột | Tablet (576px-991px): 2 cột | Desktop (992px-1399px): 3 cột | Màn hình lớn (>=1400px): 4 cột -->
        <div id="nhSanPhamGrid" class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xxl-4 g-3 g-md-3.5 mb-4">
            <?php foreach ($dsSanPham as $sp):
                $anhHienThi = duong_dan_anh_sp($sp['anh'] ?? '');
                $khoTM = tach_kho_thong_minh($sp['san_luong'], $sp['don_vi_san_luong']);
                $dsCnItem = dinh_dang_chung_nhan($sp['chung_nhan'] ?? '');
                $laDangAn = ($sp['an'] == 1);
                $trangThaiFilter = $laDangAn ? 'dang_an' : 'dang_ban';
                $tuKhoaSearch = mb_strtolower($sp['ten'] . ' ' . $sp['ten_danh_muc'] . ' sp-' . $sp['ma_san_pham'] . ' ' . ($sp['chung_nhan'] ?? ''));
                ?>
                <div class="col nh-item-san-pham" data-trangthai="<?= $trangThaiFilter ?>"
                    data-search="<?= htmlspecialchars($tuKhoaSearch) ?>">
                    <div
                        class="card nh-sp-card h-100 rounded-4 overflow-hidden position-relative <?= $laDangAn ? 'nh-sp-card-hidden' : '' ?>">
                        <!-- BỐ CỤC THẺ: MOBILE DẠNG NGANG GỌN GÀNG, DESKTOP DẠNG DỌC CHUẨN GRID -->
                        <div class="nh-sp-card-main-wrap">
                            <!-- KHU VỰC ẢNH VÀ TEM TRẠNG THÁI NỔI BẬT -->
                            <div class="nh-sp-card-img-wrap position-relative">
                                <a href="index.php?trang=chi_tiet&id=<?= $sp['ma_san_pham'] ?>"
                                    class="d-block w-100 h-100 text-decoration-none">
                                    <img src="<?= $anhHienThi ?>" alt="<?= htmlspecialchars($sp['ten']) ?>" class="nh-sp-card-img"
                                        loading="lazy" onerror="this.src='assets/images/nong_san_mac_dinh.svg'">
                                </a>

                                <!-- TEM TRẠNG THÁI BÁN (GÓC TRÊN TRÁI) -->
                                <div class="nh-sp-badge-status-wrap">
                                    <?php if ($laDangAn): ?>
                                        <span class="badge bg-secondary bg-opacity-95 text-white rounded-pill px-2 py-1 shadow-sm fw-bold">
                                            Đang ẩn
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success bg-opacity-95 text-white rounded-pill px-2 py-1 shadow-sm fw-bold">
                                            Đang bán
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- TEM TÌNH TRẠNG KHO / MÙA VỤ (GÓC TRÊN PHẢI) -->
                                <div class="nh-sp-badge-crop-wrap">
                                    <?php $ttNongHo = dinh_dang_tinh_trang_sp($sp['tinh_trang'] ?? null); ?>
                                    <span class="badge bg-white text-<?= $ttNongHo['mau'] ?> border border-<?= $ttNongHo['mau'] ?>-subtle rounded-pill px-2 py-1 shadow-sm fw-bold">
                                        <i class="bi <?= $ttNongHo['icon'] ?> me-1"></i><?= htmlspecialchars($ttNongHo['nhan']) ?>
                                    </span>
                                </div>
                            </div>

                            <!-- PHẦN NỘI DUNG CHI TIẾT -->
                            <div class="nh-sp-card-body d-flex flex-column flex-grow-1">
                                <!-- DANH MỤC & CHỨNG NHẬN -->
                                <div class="d-flex align-items-center gap-1.5 flex-wrap mb-1.5 nh-sp-meta-tags">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 fw-bold nh-sp-tag-cat">
                                        <?= htmlspecialchars($sp['ten_danh_muc']) ?>
                                    </span>
                                    <?php if (!empty($dsCnItem)): ?>
                                        <?php foreach ($dsCnItem as $cn): ?>
                                            <span class="badge <?= $cn['badge_class'] ?> rounded-pill px-2 py-0.5 fw-bold nh-sp-tag-cert">
                                                <i class="bi <?= $cn['icon'] ?> me-0.5"></i> <?= htmlspecialchars($cn['ten']) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>

                                <!-- TÊN NÔNG SẢN -->
                                <h6 class="fw-bold text-dark nh-sp-card-title mb-1.5" title="<?= htmlspecialchars($sp['ten']) ?>">
                                    <a href="index.php?trang=chi_tiet&id=<?= $sp['ma_san_pham'] ?>"
                                        class="text-dark text-decoration-none">
                                        <?= htmlspecialchars($sp['ten']) ?>
                                    </a>
                                </h6>

                                <!-- HỘP THÔNG SỐ: KHO SẴN CÓ VÀ GIÁ BÁN NIÊM YẾT -->
                                <div class="nh-sp-stats-box rounded-3 mb-2 mt-auto">
                                    <div class="row g-2 align-items-center text-center">
                                        <div class="col-6 border-end nh-sp-stat-item">
                                            <span class="text-muted d-block nh-sp-stat-label">Kho sẵn có</span>
                                            <div class="text-dark fw-bold text-truncate nh-sp-stat-val">
                                                <span class="nh-stat-num text-dark"><?= $khoTM['so_luong'] ?></span>
                                                <span class="nh-stat-unit text-muted"><?= htmlspecialchars($khoTM['don_vi']) ?></span>
                                            </div>
                                        </div>
                                        <div class="col-6 nh-sp-stat-item">
                                            <span class="text-muted d-block nh-sp-stat-label">Giá niêm yết</span>
                                            <div class="text-truncate nh-sp-stat-val">
                                                <?php if ($sp['gia'] !== null && $sp['gia'] > 0): ?>
                                                    <span class="nh-stat-num text-success"><?= dinh_dang_tien($sp['gia']) ?></span>
                                                    <span class="nh-stat-unit text-muted">/<?= htmlspecialchars($sp['don_vi_gia']) ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning bg-opacity-25 text-dark border border-warning px-2 py-0.5 rounded-pill fw-bold small">Báo giá</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CÁC NÚT THAO TÁC (CHỈNH SỬA, ẨN/HIỆN, XEM TRANG) -->
                        <div class="nh-sp-card-actions d-flex align-items-center gap-2 pt-2 border-top">
                            <!-- NÚT SỬA NÔNG SẢN -->
                            <a href="index.php?trang=nong_ho_form_san_pham&id=<?= $sp['ma_san_pham'] ?>"
                                class="btn btn-clean btn-sm flex-grow-1 py-1.5 px-2.5 fw-bold rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-1.5"
                                title="Chỉnh sửa nông sản">
                                <i class="bi bi-pencil-square"></i>
                                <span>Sửa</span>
                            </a>

                            <!-- NÚT TẠM ẨN / HIỆN LẠI -->
                            <?php if ($laDangAn): ?>
                                <button type="button"
                                    class="btn btn-outline-success btn-sm px-3 py-1.5 fw-bold rounded-pill d-inline-flex align-items-center justify-content-center gap-1 text-nowrap"
                                    title="Hiển thị nông sản trở lại sàn"
                                    onclick="xacNhanAnHien(<?= $sp['ma_san_pham'] ?>, 0, '<?= htmlspecialchars(addslashes($sp['ten'])) ?>')">
                                    <i class="bi bi-eye-fill"></i>
                                    <span>Hiện</span>
                                </button>
                            <?php else: ?>
                                <button type="button"
                                    class="btn btn-outline-secondary btn-sm px-3 py-1.5 fw-bold rounded-pill d-inline-flex align-items-center justify-content-center gap-1 text-nowrap"
                                    title="Tạm ẩn khỏi sàn công khai"
                                    onclick="xacNhanAnHien(<?= $sp['ma_san_pham'] ?>, 1, '<?= htmlspecialchars(addslashes($sp['ten'])) ?>')">
                                    <i class="bi bi-eye-slash-fill text-warning"></i>
                                    <span>Ẩn</span>
                                </button>
                            <?php endif; ?>

                            <!-- XEM TRÊN SÀN CÔNG KHAI -->
                            <a href="index.php?trang=chi_tiet&id=<?= $sp['ma_san_pham'] ?>"
                                class="btn btn-light border btn-sm p-1.5 rounded-circle text-muted d-inline-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 34px; height: 34px;" title="Xem bài đăng trên sàn">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- 4. GIAO DIỆN BẢNG DÀNH CHO MÀN HÌNH LỚN / DESKTOP (TÙY CHỌN CHUYỂN ĐỔI) -->
        <div id="nhSanPhamTable" class="bg-white rounded-4 border shadow-sm p-3 mb-4 d-none">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-3" style="width: 90px;">Mã SP</th>
                            <th style="min-width: 250px;">Nông Sản</th>
                            <th>Ngành Hàng</th>
                            <th>Kho Sẵn Có</th>
                            <th>Giá Niêm Yết</th>
                            <th>Tình Trạng</th>
                            <th>Trạng Thái</th>
                            <th class="text-end pe-3" style="min-width: 190px;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dsSanPham as $sp):
                            $anhHienThi = duong_dan_anh_sp($sp['anh'] ?? '');
                            $khoTM = tach_kho_thong_minh($sp['san_luong'], $sp['don_vi_san_luong']);
                            $dsCnItem = dinh_dang_chung_nhan($sp['chung_nhan'] ?? '');
                            $laDangAn = ($sp['an'] == 1);
                            $trangThaiFilter = $laDangAn ? 'dang_an' : 'dang_ban';
                            $tuKhoaSearch = mb_strtolower($sp['ten'] . ' ' . $sp['ten_danh_muc'] . ' sp-' . $sp['ma_san_pham'] . ' ' . ($sp['chung_nhan'] ?? ''));
                            ?>
                            <tr class="nh-item-san-pham" data-trangthai="<?= $trangThaiFilter ?>"
                                data-search="<?= htmlspecialchars($tuKhoaSearch) ?>">
                                <td class="ps-3">
                                    <span class="badge bg-light text-dark border fw-bold">
                                        <?= ma_hien_thi('san_pham', $sp['ma_san_pham']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= $anhHienThi ?>" alt="<?= htmlspecialchars($sp['ten']) ?>"
                                            class="rounded-3 border flex-shrink-0"
                                            style="width: 52px; height: 52px; object-fit: cover;"
                                            onerror="this.src='assets/images/nong_san_mac_dinh.svg'">
                                        <div class="min-w-0">
                                            <h6 class="fw-bold text-dark mb-1 text-truncate" style="max-width: 240px;">
                                                <a href="index.php?trang=chi_tiet&id=<?= $sp['ma_san_pham'] ?>"
                                                    class="text-dark text-decoration-none">
                                                    <?= htmlspecialchars($sp['ten']) ?>
                                                </a>
                                            </h6>
                                            <?php if (!empty($dsCnItem)): ?>
                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                    <?php foreach ($dsCnItem as $cn): ?>
                                                        <span class="badge <?= $cn['badge_class'] ?> rounded-pill px-2 py-0.5 fw-bold"
                                                            style="font-size: 0.68rem;">
                                                            <i class="bi <?= $cn['icon'] ?> me-0.5"></i>
                                                            <?= htmlspecialchars($cn['ten']) ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= htmlspecialchars($sp['ten_danh_muc']) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-dark fs-6"><?= $khoTM['so_luong'] ?></strong>
                                    <span class="text-muted fw-bold small"><?= htmlspecialchars($khoTM['don_vi']) ?></span>
                                </td>
                                <td>
                                    <?php if ($sp['gia'] !== null && $sp['gia'] > 0): ?>
                                        <strong class="text-success fs-6"><?= dinh_dang_tien($sp['gia']) ?></strong>
                                        <span class="text-muted small fw-bold">/<?= htmlspecialchars($sp['don_vi_gia']) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-25 text-dark border border-warning small">Báo
                                            giá</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php $ttTable = dinh_dang_tinh_trang_sp($sp['tinh_trang'], $sp['ngay_thu_hoach'] ?? null); ?>
                                    <span
                                        class="badge bg-<?= $ttTable['mau'] ?> bg-opacity-10 text-<?= $ttTable['mau'] ?> border border-<?= $ttTable['mau'] ?>">
                                        <i class="bi <?= $ttTable['icon'] ?> me-1"></i><?= htmlspecialchars($ttTable['nhan']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($laDangAn): ?>
                                        <span class="badge bg-secondary text-white">Đang ẩn</span>
                                    <?php else: ?>
                                        <span class="badge bg-success text-white">Đang bán</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-inline-flex gap-1.5 align-items-center">
                                        <a href="index.php?trang=nong_ho_form_san_pham&id=<?= $sp['ma_san_pham'] ?>"
                                            class="btn btn-clean btn-sm px-2.5 py-1 rounded-pill" title="Sửa nông sản">
                                            <i class="bi bi-pencil-square me-0.5"></i> Sửa
                                        </a>

                                        <?php if ($laDangAn): ?>
                                            <button type="button" class="btn btn-outline-success btn-sm px-2.5 py-1 rounded-pill"
                                                title="Bật hiển thị lại trên sàn"
                                                onclick="xacNhanAnHien(<?= $sp['ma_san_pham'] ?>, 0, '<?= htmlspecialchars(addslashes($sp['ten'])) ?>')">
                                                <i class="bi bi-eye-fill me-0.5"></i> Hiện
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-outline-secondary btn-sm px-2.5 py-1 rounded-pill"
                                                title="Tạm ẩn khỏi sàn"
                                                onclick="xacNhanAnHien(<?= $sp['ma_san_pham'] ?>, 1, '<?= htmlspecialchars(addslashes($sp['ten'])) ?>')">
                                                <i class="bi bi-eye-slash-fill me-0.5 text-warning"></i> Ẩn
                                            </button>
                                        <?php endif; ?>

                                        <a href="index.php?trang=chi_tiet&id=<?= $sp['ma_san_pham'] ?>"
                                            class="btn btn-light border btn-sm p-1.5 rounded-circle text-muted"
                                            title="Xem trên sàn">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. KHUNG BÁO KHÔNG CÓ KẾT QUẢ KHI TÌM KIẾM HOẶC LỌC -->
        <div id="nhKhongTimThay" class="bg-white rounded-4 border shadow-sm p-5 text-center my-4 d-none">
            <i class="bi bi-search text-muted fs-1 mb-2 d-block"></i>
            <h5 class="fw-bold text-dark">Không tìm thấy nông sản phù hợp</h5>
            <p class="text-muted small mb-3">Vui lòng thử từ khóa khác hoặc xóa bộ lọc để xem lại tất cả mặt hàng.</p>
            <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3 py-1.5 fw-bold"
                onclick="datLaiBoLoc()">
                <i class="bi bi-arrow-counterclockwise me-1"></i>Xem Tất Cả Nông Sản
            </button>
        </div>

    <?php endif; ?>
</div>

<script>
    // Biến trạng thái bộ lọc client-side
    let boLocHienTai = 'tat_ca';

    // Chuyển đổi giữa chế độ Thẻ (Grid) và Bảng (Table) trên màn hình lớn
    function chuyenCheDoXem(cheDo) {
        const gridView = document.getElementById('nhSanPhamGrid');
        const tableView = document.getElementById('nhSanPhamTable');
        const btnGrid = document.getElementById('btnCheDoGrid');
        const btnTable = document.getElementById('btnCheDoTable');

        if (!gridView || !tableView) return;

        if (cheDo === 'table') {
            gridView.classList.add('d-none');
            tableView.classList.remove('d-none');
            if (btnGrid && btnTable) {
                btnGrid.className = 'btn btn-sm rounded-pill fw-bold px-2.5 py-1 btn-light text-muted';
                btnTable.className = 'btn btn-sm rounded-pill fw-bold px-2.5 py-1 btn-success text-white';
            }
            localStorage.setItem('nh_nong_ho_view_sp', 'table');
        } else {
            tableView.classList.add('d-none');
            gridView.classList.remove('d-none');
            if (btnGrid && btnTable) {
                btnGrid.className = 'btn btn-sm rounded-pill fw-bold px-2.5 py-1 btn-success text-white';
                btnTable.className = 'btn btn-sm rounded-pill fw-bold px-2.5 py-1 btn-light text-muted';
            }
            localStorage.setItem('nh_nong_ho_view_sp', 'grid');
        }
    }

    // Khôi phục tùy chọn chế độ xem đã lưu
    document.addEventListener('DOMContentLoaded', function () {
        const cheDoLuu = localStorage.getItem('nh_nong_ho_view_sp');
        if (cheDoLuu === 'table' && window.innerWidth >= 768) {
            chuyenCheDoXem('table');
        }
    });

    // Lọc nhanh theo trạng thái: Tất cả / Đang bán / Đang ẩn
    function locTheoTrangThai(trangThai) {
        boLocHienTai = trangThai;

        // Cập nhật giao diện nút tab lọc
        const buttons = document.querySelectorAll('#filterStatusGroup .nh-filter-btn');
        buttons.forEach(btn => {
            if (btn.getAttribute('data-filter') === trangThai) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        apDungBoLocVaTimKiem();
    }

    // Tìm kiếm tức thì theo từ khóa
    function xuLyTimKiem() {
        apDungBoLocVaTimKiem();
    }

    // Áp dụng đồng thời bộ lọc trạng thái và từ khóa tìm kiếm
    function apDungBoLocVaTimKiem() {
        const inputSearch = document.getElementById('nhInputTimKiem');
        const tuKhoa = inputSearch ? inputSearch.value.trim().toLowerCase() : '';
        const items = document.querySelectorAll('.nh-item-san-pham');
        const khongTimThay = document.getElementById('nhKhongTimThay');

        let soHienThiGrid = 0;

        items.forEach(item => {
            const itemTrangThai = item.getAttribute('data-trangthai') || '';
            const itemSearch = item.getAttribute('data-search') || '';

            // Kiểm tra khớp trạng thái
            const khopTrangThai = (boLocHienTai === 'tat_ca' || boLocHienTai === itemTrangThai);
            // Kiểm tra khớp từ khóa
            const khopTuKhoa = (tuKhoa === '' || itemSearch.includes(tuKhoa));

            if (khopTrangThai && khopTuKhoa) {
                item.classList.remove('d-none');
                soHienThiGrid++;
            } else {
                item.classList.add('d-none');
            }
        });

        if (khongTimThay) {
            if (soHienThiGrid === 0 && items.length > 0) {
                khongTimThay.classList.remove('d-none');
            } else {
                khongTimThay.classList.add('d-none');
            }
        }
    }

    // Đặt lại bộ lọc và tìm kiếm
    function datLaiBoLoc() {
        const inputSearch = document.getElementById('nhInputTimKiem');
        if (inputSearch) inputSearch.value = '';
        locTheoTrangThai('tat_ca');
    }

    // Xác nhận ẩn / hiện nông sản bằng in-app modal
    function xacNhanAnHien(maSanPham, trangThaiAn, tenSanPham) {
        const hanhDong = trangThaiAn === 1 ? 'tạm ẩn khỏi sàn công khai' : 'cho phép hiển thị trở lại sàn';
        const nutNhan = trangThaiAn === 1 ? 'Ẩn Nông Sản' : 'Hiện Trở Lại';
        const mauNut = trangThaiAn === 1 ? 'btn-warning' : 'btn-success';

        moModalXacNhan(
            'Thay Đổi Hiển Thị',
            `Bạn có chắc chắn muốn <strong>${hanhDong}</strong> đối với nông sản <strong>${tenSanPham}</strong>?`,
            function () {
                window.location.href = `index.php?trang=nong_ho_an_hien&id=${maSanPham}&an=${trangThaiAn}`;
            },
            nutNhan,
            mauNut
        );
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
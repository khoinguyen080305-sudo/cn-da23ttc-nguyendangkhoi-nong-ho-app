<?php
// views/nong_ho/ho_so.php
// Hồ Sơ & Báo Cáo Thống Kê Hoạt Động Dành Cho Nhà Vườn / Nông Hộ

$loi = $loi ?? '';
$tabHienTai = $tabHienTai ?? ($_GET['tab'] ?? 'thong_ke');
$thoiGian = $thoiGian ?? ($_GET['thoi_gian'] ?? 'thang_nay');

require_once __DIR__ . '/../layouts/header.php';
$nguoiDung = nguoi_dung_hien_tai();

// Đảm bảo dữ liệu thống kê an toàn
$kpi = $thongKe['kpi'] ?? [];
$topSanPham = $thongKe['top_san_pham'] ?? [];
$kho = $thongKe['kho'] ?? [];
$bieuDo = $thongKe['bieu_do'] ?? ['labels' => [], 'values' => [], 'counts' => []];

// Chuẩn hóa hiển thị khối lượng đã xuất bán
$sanLuongKg = (float) ($kpi['tong_san_luong_kg'] ?? 0);
if ($sanLuongKg >= 1000) {
    $sanLuongXuatChuoi = dinh_dang_so_luong(round($sanLuongKg / 1000, 2)) . ' tấn';
} else {
    $sanLuongXuatChuoi = dinh_dang_so_luong($sanLuongKg) . ' kg';
}

// Chuẩn hóa hiển thị tồn kho hiện tại
$khoKg = (float) ($kho['tong_kho_kg'] ?? 0);
if ($khoKg >= 1000) {
    $khoChuoi = dinh_dang_so_luong(round($khoKg / 1000, 2)) . ' tấn';
} else {
    $khoChuoi = dinh_dang_so_luong($khoKg) . ' kg';
}
?>

<!-- Lucian UX/UI Prompt #039, #047, #098 · MIT License (c) 2026 Seokwoooo · https://lucianlabs.dev/uxui-prompt/ -->
<div class="container py-4">
    <!-- TIÊU ĐỀ & CHUYỂN TAB CHÍNH (LUCIAN #039 SLIDING PILL TABS) -->
    <div class="row justify-content-center mb-4">
        <div class="col-12 col-xl-10">
            <div
                class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 pb-3 border-bottom">
                <div>
                    <h4 class="fw-bold mb-1" style="letter-spacing: -0.3px;">
                        <i
                            class="bi bi-house-door-fill text-success me-2"></i><?= htmlspecialchars($nguoiDung['ho_ten']) ?>
                    </h4>
                    <div class="text-muted small d-flex flex-wrap align-items-center gap-2">
                        <span><i class="bi bi-telephone text-success"></i>
                            <?= htmlspecialchars($nguoiDung['sdt']) ?></span>
                        <span>•</span>
                        <span><i class="bi bi-geo-alt text-danger"></i>
                            <?= htmlspecialchars($nguoiDung['tinh'] ?: 'Chưa cập nhật tỉnh') ?></span>
                        <span>•</span>
                        <?php if ($nguoiDung['trang_thai'] === 'da_duyet'): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle fw-medium">Đã
                                duyệt</span>
                        <?php else: ?>
                            <span
                                class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-medium">Chờ
                                duyệt</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- LUCIAN #039 SLIDING PILL TAB SWITCHER -->
                <nav class="ux u039-nav theme-farmer" id="nhFarmerProfileNav" role="tablist"
                    aria-label="Điều hướng hồ sơ nông hộ">
                    <span class="u039-pill" aria-hidden="true"></span>
                    <a href="index.php?trang=nong_ho_ho_so&tab=thong_ke&thoi_gian=<?= urlencode($thoiGian) ?>"
                        class="u039-tab <?= $tabHienTai === 'thong_ke' ? 'active' : '' ?>" role="tab"
                        aria-selected="<?= $tabHienTai === 'thong_ke' ? 'true' : 'false' ?>">
                        <i class="bi bi-graph-up-arrow"></i> <span class="d-none d-sm-inline">Báo Cáo & Thống
                            Kê</span><span class="d-sm-none">Báo Cáo</span>
                    </a>
                    <a href="index.php?trang=nong_ho_ho_so&tab=thong_tin"
                        class="u039-tab <?= $tabHienTai === 'thong_tin' ? 'active' : '' ?>" role="tab"
                        aria-selected="<?= $tabHienTai === 'thong_tin' ? 'true' : 'false' ?>">
                        <i class="bi bi-house-gear-fill"></i> <span class="d-none d-sm-inline">Thông Tin Nhà
                            Vườn</span><span class="d-sm-none">Hồ Sơ Vườn</span>
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <?php if ($tabHienTai === 'thong_ke'): ?>
        <!-- ============================================================== -->
        <!-- TAB 1: BÁO CÁO & THỐNG KÊ DOANH THU, ĐƠN HÀNG, SẢN PHẨM BÁN CHẠY -->
        <!-- ============================================================== -->
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <!-- THANH LỌC THỜI GIAN (LUCIAN #047 PERIOD SEGMENTED CONTROL) -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <div class="fw-bold fs-6 text-dark d-flex align-items-center gap-2">
                            <span>Báo cáo kinh doanh</span>
                        </div>
                        <div class="text-muted small">Dữ liệu ghi nhận từ các đơn hàng xuất bán thành công
                        </div>
                    </div>

                    <div class="u047-seg-wrap">
                        <div class="ux u047-seg theme-farmer" id="nhFarmerTimeSeg" role="radiogroup"
                            aria-label="Mốc thời gian">
                            <span class="u047-knob" aria-hidden="true"></span>
                            <a href="index.php?trang=nong_ho_ho_so&tab=thong_ke&thoi_gian=hom_nay"
                                class="u047-opt <?= $thoiGian === 'hom_nay' ? 'active' : '' ?>" role="radio"
                                aria-checked="<?= $thoiGian === 'hom_nay' ? 'true' : 'false' ?>">Hôm nay</a>
                            <a href="index.php?trang=nong_ho_ho_so&tab=thong_ke&thoi_gian=7_ngay"
                                class="u047-opt <?= $thoiGian === '7_ngay' ? 'active' : '' ?>" role="radio"
                                aria-checked="<?= $thoiGian === '7_ngay' ? 'true' : 'false' ?>">7 ngày</a>
                            <a href="index.php?trang=nong_ho_ho_so&tab=thong_ke&thoi_gian=thang_nay"
                                class="u047-opt <?= $thoiGian === 'thang_nay' ? 'active' : '' ?>" role="radio"
                                aria-checked="<?= $thoiGian === 'thang_nay' ? 'true' : 'false' ?>">Tháng này</a>
                            <a href="index.php?trang=nong_ho_ho_so&tab=thong_ke&thoi_gian=nam_nay"
                                class="u047-opt <?= $thoiGian === 'nam_nay' ? 'active' : '' ?>" role="radio"
                                aria-checked="<?= $thoiGian === 'nam_nay' ? 'true' : 'false' ?>">Năm nay</a>
                            <a href="index.php?trang=nong_ho_ho_so&tab=thong_ke&thoi_gian=tat_ca"
                                class="u047-opt <?= $thoiGian === 'tat_ca' ? 'active' : '' ?>" role="radio"
                                aria-checked="<?= $thoiGian === 'tat_ca' ? 'true' : 'false' ?>">Tất cả</a>
                        </div>
                    </div>
                </div>

                <!-- 4 THẺ CHỈ SỐ KPI CHỌN LỌC RÕ RÀNG (LUCIAN #098 METRIC CARDS) -->
                <div class="row g-3 mb-4">
                    <!-- KPI 1: DOANH THU BÁN HÀNG -->
                    <div class="col-6 col-lg-3">
                        <div class="ux u098-card">
                            <div class="u098-header">
                                <span class="u098-lbl">Doanh thu</span>
                            </div>
                            <div class="u098-val-row">
                                <span class="u098-num"
                                    style="color: #059669;"><?= dinh_dang_tien($kpi['tong_doanh_thu'] ?? 0) ?></span>
                            </div>
                            <div class="u098-sub">
                                Từ <strong><?= number_format($kpi['so_don_thanh_cong'] ?? 0) ?></strong> đơn chốt thành công
                            </div>
                        </div>
                    </div>

                    <!-- KPI 2: ĐƠN HÀNG THÀNH CÔNG -->
                    <div class="col-6 col-lg-3">
                        <div class="ux u098-card">
                            <div class="u098-header">
                                <span class="u098-lbl">Đơn thành công</span>
                            </div>
                            <div class="u098-val-row">
                                <span class="u098-num"
                                    style="color: #0284c7;"><?= number_format($kpi['so_don_thanh_cong'] ?? 0) ?></span>
                                <span class="u098-unit">đơn</span>
                            </div>
                            <div class="u098-sub">
                                <span class="badge bg-success-subtle text-success fw-bold"
                                    style="font-size: 0.72rem;"><?= (int) ($kpi['so_don_si'] ?? 0) ?> sỉ (≥500kg)</span>
                                <span class="text-muted ms-1">• <?= (int) ($kpi['so_don_le'] ?? 0) ?> lẻ</span>
                            </div>
                        </div>
                    </div>

                    <!-- KPI 3: SẢN LƯỢNG ĐÃ XUẤT BÁN -->
                    <div class="col-6 col-lg-3">
                        <div class="ux u098-card">
                            <div class="u098-header">
                                <span class="u098-lbl">Sản lượng đã bán</span>
                            </div>
                            <div class="u098-val-row">
                                <span class="u098-num" style="color: #d97706;"><?= $sanLuongXuatChuoi ?></span>
                            </div>
                            <div class="u098-sub">
                                Tổng số lượng nông sản đã xuất bán thành công
                            </div>
                        </div>
                    </div>

                    <!-- KPI 4: NÔNG SẢN TRONG VƯỜN HIỆN CÓ -->
                    <div class="col-6 col-lg-3">
                        <div class="ux u098-card">
                            <div class="u098-header">
                                <span class="u098-lbl">Nông sản</span>
                            </div>
                            <div class="u098-val-row">
                                <span class="u098-num"
                                    style="color: #6366f1;"><?= number_format($kho['so_loai_sp'] ?? 0) ?></span>
                                <span class="u098-unit">loại</span>
                            </div>
                            <div class="u098-sub">
                                Tồn kho sẵn sàng: <strong><?= $khoChuoi ?></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KHU VỰC BIỂU ĐỒ & TOP NÔNG SẢN BÁN CHẠY (LUCIAN #095 SURFACE & #039 LIST) -->
                <div class="row g-3">
                    <!-- CỘT TRÁI: BIỂU ĐỒ DOANH THU THEO THỜI GIAN -->
                    <div class="col-12 col-lg-8">
                        <div class="u095-surface">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">
                                        <i class="bi bi-bar-chart-line-fill text-success me-1.5"></i> Biểu Đồ Doanh Thu
                                    </h6>
                                </div>
                                <span class="badge bg-light text-muted border px-2.5 py-1.5">
                                    Đơn vị: VNĐ
                                </span>
                            </div>

                            <div style="position: relative; height: 310px; width: 100%;">
                                <canvas id="bieuDoDoanhThu"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- CỘT PHẢI: TOP NÔNG SẢN BÁN CHẠY NHẤT -->
                    <div class="col-12 col-lg-4">
                        <div class="u095-surface d-flex flex-column">
                            <div class="mb-3">
                                <h6 class="fw-bold mb-0 text-dark">
                                    <i class="bi bi-trophy-fill text-warning me-1.5"></i> Top Nông Sản Bán Chạy
                                </h6>
                                <div class="text-muted small" style="font-size: 0.78rem;">
                                    Xếp hạng theo tổng doanh thu bán được
                                </div>
                            </div>

                            <div class="flex-grow-1">
                                <?php if (empty($topSanPham)): ?>
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-2 text-secondary opacity-50 d-block mb-1"></i>
                                        <span class="small">Chưa có giao dịch hoàn thành trong mốc thời gian này</span>
                                    </div>
                                <?php else: ?>
                                    <div class="u039-list">
                                        <?php foreach ($topSanPham as $index => $sp): ?>
                                            <div class="u039-row">
                                                <div
                                                    class="u039-rank <?= $index === 0 ? 'rank-1' : ($index === 1 ? 'rank-2' : ($index === 2 ? 'rank-3' : 'rank-other')) ?>">
                                                    <?= $index + 1 ?>
                                                </div>
                                                <div class="u039-thumb">
                                                    <?php if (!empty($sp['anh'])): ?>
                                                        <img src="<?= htmlspecialchars($sp['anh']) ?>"
                                                            alt="<?= htmlspecialchars($sp['ten']) ?>">
                                                    <?php else: ?>
                                                        <div
                                                            class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                                            <i class="bi bi-image"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex-grow-1 min-w-0">
                                                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.88rem;"
                                                        title="<?= htmlspecialchars($sp['ten']) ?>">
                                                        <?= htmlspecialchars($sp['ten']) ?>
                                                    </div>
                                                    <div class="text-muted small" style="font-size: 0.76rem;">
                                                        Đã xuất: <strong><?= dinh_dang_so_luong($sp['tong_so_luong']) ?></strong>
                                                        <?= htmlspecialchars($sp['don_vi_gia']) ?>
                                                    </div>
                                                </div>
                                                <div class="text-end flex-shrink-0">
                                                    <div class="fw-bold text-success" style="font-size: 0.88rem;">
                                                        <?= dinh_dang_tien($sp['tong_tien']) ?>
                                                    </div>
                                                    <div class="text-muted" style="font-size: 0.72rem;">
                                                        <?= (int) $sp['so_don'] ?> đơn
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- TÓM TẮT SẢN PHẨM SẮP THU HOẠCH -->
                            <?php if (!empty($kho['so_sp_sap_thu_hoach']) && $kho['so_sp_sap_thu_hoach'] > 0): ?>
                                <div
                                    class="mt-3 p-2.5 rounded-3 bg-light border text-muted small d-flex align-items-center gap-2">
                                    <i class="bi bi-calendar2-check text-warning fs-5"></i>
                                    <div>Có <strong><?= (int) $kho['so_sp_sap_thu_hoach'] ?></strong> loại nông sản
                                        đang
                                        ở tình trạng <em>Sắp thu hoạch</em>.</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TÍCH HỢP CHART.JS VẼ BIỂU ĐỒ DOANH THU -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
        <script>
            (function () {
                var isDark = document.documentElement.getAttribute('data-theme') === 'dark' || localStorage.getItem('theme') === 'dark';
                var gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)';
                var textColor = isDark ? '#94a3b8' : '#64748b';

                var labels = <?= json_encode($bieuDo['labels'] ?? []) ?>;
                var values = <?= json_encode($bieuDo['values'] ?? []) ?>;
                var counts = <?= json_encode($bieuDo['counts'] ?? []) ?>;

                var ctx = document.getElementById('bieuDoDoanhThu');
                if (!ctx) return;

                var gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 280);
                gradient.addColorStop(0, 'rgba(5, 150, 105, 0.35)');
                gradient.addColorStop(1, 'rgba(5, 150, 105, 0.01)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Doanh thu',
                            data: values,
                            borderColor: '#059669',
                            backgroundColor: gradient,
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.3,
                            pointBackgroundColor: '#059669',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: isDark ? '#1e293b' : '#0f172a',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function (context) {
                                        var val = context.parsed.y || 0;
                                        var idx = context.dataIndex;
                                        var soDon = counts[idx] || 0;
                                        return ' Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(val) + ' đ (' + soDon + ' đơn)';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: textColor,
                                    font: {
                                        size: 11.5,
                                        weight: '500'
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: gridColor
                                },
                                ticks: {
                                    color: textColor,
                                    font: {
                                        size: 11
                                    },
                                    callback: function (value) {
                                        if (value >= 1000000) {
                                            return (value / 1000000).toFixed(1) + ' tr';
                                        } else if (value >= 1000) {
                                            return (value / 1000).toFixed(0) + ' k';
                                        }
                                        return value;
                                    }
                                }
                            }
                        }
                    }
                });
            })();
        </script>

    <?php else: ?>
        <!-- ============================================================== -->
        <!-- TAB 2: THÔNG TIN CÁ NHÂN & CẬP NHẬT NHÀ VƯỜN                    -->
        <!-- ============================================================== -->
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8 col-xl-7">
                <div class="u095-surface p-3 p-md-4">
                    <!-- TIÊU ĐỀ HỒ SƠ -->
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-2 shadow-sm"
                            style="width: 56px; height: 56px; background: linear-gradient(135deg, #059669, #047857) !important;">
                            <i class="bi bi-person-vcard fs-2"></i>
                        </div>
                        <h4 class="fw-bold mb-1" style="letter-spacing: -0.3px;">Thông Tin Nhà Vườn</h4>
                    </div>

                    <?php if ($nguoiDung['trang_thai'] === 'da_duyet'): ?>
                        <div class="d-flex align-items-center gap-3 p-3 mb-4 rounded-4"
                            style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border: 1.5px solid #a7f3d0;">
                            <div>
                                <div class="fw-bold text-success" style="font-size: 0.95rem;">Tài khoản đã được phê duyệt danh
                                    tính</div>
                                <div class="text-muted small" style="font-size: 0.8rem;">Vườn của bạn đã được xác minh, sản phẩm
                                    được tiếp cận đến các thương lái.</div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="d-flex align-items-center gap-3 p-3 mb-4 rounded-4"
                            style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1.5px solid #fde68a;">
                            <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 38px; height: 38px;">
                                <i class="bi bi-hourglass-split fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-warning-emphasis" style="font-size: 0.95rem;">Đang chờ Ban Quản Trị phê
                                    duyệt</div>
                                <div class="text-muted small" style="font-size: 0.8rem;">Vui lòng điền đúng số điện thoại và địa
                                    chỉ thực tế tại vườn để hồ sơ được duyệt nhanh.</div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($loi)): ?>
                        <div class="alert alert-danger py-2.5 px-3 rounded-3 fs-6 mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-circle-fill fs-5"></i>
                            <div><?= htmlspecialchars($loi) ?></div>
                        </div>
                    <?php endif; ?>

                    <form action="index.php?trang=nong_ho_ho_so" method="POST">
                        <div class="row g-3">
                            <!-- SỐ ĐIỆN THOẠI TÀI KHOẢN -->
                            <div class="col-sm-6">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    <i class="bi bi-telephone-fill text-success me-1"></i>Số điện thoại (Tài khoản)
                                </label>
                                <div class="input-group">
                                    <span
                                        class="input-group-text bg-light border-end-0 rounded-start-pill text-muted small fw-bold">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>
                                    <input type="text"
                                        class="form-control bg-light text-muted fw-bold border-start-0 rounded-end-pill"
                                        value="<?= htmlspecialchars($nguoiDung['sdt']) ?>" readonly
                                        title="Số điện thoại là tài khoản đăng nhập (được khóa cố định). Liên hệ Quản trị viên nếu cần đổi số.">
                                </div>
                                <div class="form-text text-muted" style="font-size: 0.72rem;">
                                    🔒 Khóa bảo mật đăng nhập. Liên hệ quản trị nếu cần đổi SĐT.
                                </div>
                            </div>

                            <!-- TÊN NHÀ VƯỜN / HỢP TÁC XÃ -->
                            <div class="col-sm-6">
                                <label for="ho_ten" class="form-label small fw-bold text-dark mb-1">
                                    <i class="bi bi-house-door-fill text-success me-1"></i>Tên nhà vườn<span
                                        class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control fw-bold rounded-pill px-3 py-2" id="ho_ten"
                                    name="ho_ten" value="<?= htmlspecialchars($_POST['ho_ten'] ?? $nguoiDung['ho_ten']) ?>"
                                    placeholder="Ví dụ: Nông Hộ Thanh Huy - Vườn Sầu Riêng" required>
                            </div>

                            <!-- TỈNH / THÀNH ĐẶT VƯỜN -->
                            <div class="col-sm-5">
                                <label for="tinh" class="form-label small fw-bold text-dark mb-1">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>Tỉnh/ Thành phố<span
                                        class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control fw-bold rounded-pill px-3 py-2" id="tinh" name="tinh"
                                    value="<?= htmlspecialchars($_POST['tinh'] ?? $nguoiDung['tinh']) ?>"
                                    placeholder="Ví dụ: Tiền Giang, Bến Tre..." required>
                            </div>

                            <!-- ĐỊA CHỈ CHI TIẾT VƯỜN -->
                            <div class="col-sm-7">
                                <label for="dia_chi" class="form-label small fw-bold text-dark mb-1">
                                    <i class="bi bi-pin-map-fill text-primary me-1"></i>Địa chỉ chi tiết vườn<span
                                        class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control fw-bold rounded-pill px-3 py-2" id="dia_chi"
                                    name="dia_chi"
                                    value="<?= htmlspecialchars($_POST['dia_chi'] ?? $nguoiDung['dia_chi']) ?>"
                                    placeholder="Ấp, Thôn, Xã, Huyện..." required>
                            </div>
                        </div>

                        <!-- NÚT LƯU THAY ĐỔI -->
                        <div class="mt-4 pt-2">
                            <button type="submit"
                                class="btn btn-clean btn-lg w-100 py-2.5 fw-bold fs-6 rounded-pill shadow">
                                <i class="bi bi-floppy2-fill me-1.5"></i> Lưu Cập Nhật Thông Tin Vườn
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- ĐIỀU KHIỂN HIỆU ỨNG TRƯỢT LUCIAN #039 & #047 -->
<script>
    (function () {
        function setupPill(containerId, pillSelector, itemSelector) {
            var container = document.getElementById(containerId);
            if (!container) return;
            var pill = container.querySelector(pillSelector);
            var activeItem = container.querySelector(itemSelector + '.active');
            if (!pill || !activeItem) return;

            function updatePosition() {
                var cRect = container.getBoundingClientRect();
                var aRect = activeItem.getBoundingClientRect();
                if (cRect.width === 0 || aRect.width === 0) return;
                var left = aRect.left - cRect.left;
                var width = aRect.width;
                pill.style.setProperty('--x', left + 'px');
                pill.style.setProperty('--w', width + 'px');
                pill.style.opacity = '1';
                container.setAttribute('data-ready', 'true');
            }

            updatePosition();
            requestAnimationFrame(updatePosition);
            window.addEventListener('load', updatePosition);
            window.addEventListener('resize', updatePosition);

            // Hỗ trợ điều hướng phím mũi tên theo chuẩn Lucian UX
            var items = Array.prototype.slice.call(container.querySelectorAll(itemSelector));
            items.forEach(function (item, idx) {
                item.addEventListener('keydown', function (e) {
                    if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                        e.preventDefault();
                        var next = items[(idx + 1) % items.length];
                        if (next) next.focus();
                    } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                        e.preventDefault();
                        var prev = items[(idx - 1 + items.length) % items.length];
                        if (prev) prev.focus();
                    }
                });
            });
        }

        setupPill('nhFarmerProfileNav', '.u039-pill', '.u039-tab');
        setupPill('nhFarmerTimeSeg', '.u047-knob', '.u047-opt');
    })();
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
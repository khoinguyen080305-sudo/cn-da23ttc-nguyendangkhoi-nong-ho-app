<?php
// views/nguoi_mua/ho_so.php
// Hồ Sơ & Báo Cáo Hoạt Động Thu Mua Dành Cho Người Mua / Thương Lái

$loi = $loi ?? '';
$tabHienTai = $tabHienTai ?? ($_GET['tab'] ?? 'thong_ke');
$thoiGian = $thoiGian ?? ($_GET['thoi_gian'] ?? 'thang_nay');

require_once __DIR__ . '/../layouts/header.php';
$nguoiDung = nguoi_dung_hien_tai();

// Đảm bảo dữ liệu thống kê an toàn
$kpi = $thongKe['kpi'] ?? [];
$topSanPham = $thongKe['top_san_pham'] ?? [];
$topDoiTac = $thongKe['top_doi_tac'] ?? [];
$bieuDo = $thongKe['bieu_do'] ?? ['labels' => [], 'values' => [], 'counts' => []];

// Chuẩn hóa hiển thị khối lượng nông sản đã gom
$khoiLuongKg = (float) ($kpi['tong_khoi_luong_kg'] ?? 0);
if ($khoiLuongKg >= 1000) {
    $khoiLuongChuoi = dinh_dang_so_luong(round($khoiLuongKg / 1000, 2)) . ' tấn';
} else {
    $khoiLuongChuoi = dinh_dang_so_luong($khoiLuongKg) . ' kg';
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
                            class="bi bi-person-badge-fill text-primary me-2"></i><?= htmlspecialchars($nguoiDung['ho_ten']) ?>
                    </h4>
                    <div class="text-muted small d-flex flex-wrap align-items-center gap-2">
                        <span><i class="bi bi-telephone text-primary"></i>
                            <?= htmlspecialchars($nguoiDung['sdt']) ?></span>
                        <span>•</span>
                        <span><i class="bi bi-geo-alt text-danger"></i>
                            <?= htmlspecialchars($nguoiDung['tinh'] ?: 'Chưa cập nhật tỉnh') ?></span>
                        <span>•</span>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-medium">Người
                            Mua</span>
                    </div>
                </div>

                <!-- LUCIAN #039 SLIDING PILL TAB SWITCHER -->
                <nav class="ux u039-nav theme-buyer" id="nhBuyerProfileNav" role="tablist"
                    aria-label="Điều hướng hồ sơ thương lái">
                    <span class="u039-pill" aria-hidden="true"></span>
                    <a href="index.php?trang=nguoi_mua_ho_so&tab=thong_ke&thoi_gian=<?= urlencode($thoiGian) ?>"
                        class="u039-tab <?= $tabHienTai === 'thong_ke' ? 'active' : '' ?>" role="tab"
                        aria-selected="<?= $tabHienTai === 'thong_ke' ? 'true' : 'false' ?>">
                        <i class="bi bi-graph-up-arrow"></i> <span class="d-none d-sm-inline">Báo Cáo Thu
                            Mua</span><span class="d-sm-none">Báo Cáo</span>
                    </a>
                    <a href="index.php?trang=nguoi_mua_ho_so&tab=thong_tin"
                        class="u039-tab <?= $tabHienTai === 'thong_tin' ? 'active' : '' ?>" role="tab"
                        aria-selected="<?= $tabHienTai === 'thong_tin' ? 'true' : 'false' ?>">
                        <i class="bi bi-gear-fill"></i> <span class="d-none d-sm-inline">Thông Tin Và Địa
                            Chỉ</span><span class="d-sm-none">Hồ Sơ & Địa Chỉ</span>
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <?php if ($tabHienTai === 'thong_ke'): ?>
        <!-- ============================================================== -->
        <!-- TAB 1: BÁO CÁO CHI TIÊU, TIẾT KIỆM SỈ, TOP HÀNG GOM & NHÀ VƯỜN -->
        <!-- ============================================================== -->
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <!-- THANH LỌC THỜI GIAN (LUCIAN #047 PERIOD SEGMENTED CONTROL) -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <div class="fw-bold fs-6 text-dark d-flex align-items-center gap-2">
                            <span>Báo cáo hoạt động thu mua</span>
                        </div>
                        <div class="text-muted small">Dữ liệu ghi nhận từ các đơn hàng chốt hợp đồng và thành công với nông
                            hộ</div>
                    </div>

                    <div class="u047-seg-wrap">
                        <div class="ux u047-seg theme-buyer" id="nhBuyerTimeSeg" role="radiogroup"
                            aria-label="Mốc thời gian">
                            <span class="u047-knob" aria-hidden="true"></span>
                            <a href="index.php?trang=nguoi_mua_ho_so&tab=thong_ke&thoi_gian=hom_nay"
                                class="u047-opt <?= $thoiGian === 'hom_nay' ? 'active' : '' ?>" role="radio"
                                aria-checked="<?= $thoiGian === 'hom_nay' ? 'true' : 'false' ?>">Hôm nay</a>
                            <a href="index.php?trang=nguoi_mua_ho_so&tab=thong_ke&thoi_gian=7_ngay"
                                class="u047-opt <?= $thoiGian === '7_ngay' ? 'active' : '' ?>" role="radio"
                                aria-checked="<?= $thoiGian === '7_ngay' ? 'true' : 'false' ?>">7 ngày</a>
                            <a href="index.php?trang=nguoi_mua_ho_so&tab=thong_ke&thoi_gian=thang_nay"
                                class="u047-opt <?= $thoiGian === 'thang_nay' ? 'active' : '' ?>" role="radio"
                                aria-checked="<?= $thoiGian === 'thang_nay' ? 'true' : 'false' ?>">Tháng này</a>
                            <a href="index.php?trang=nguoi_mua_ho_so&tab=thong_ke&thoi_gian=nam_nay"
                                class="u047-opt <?= $thoiGian === 'nam_nay' ? 'active' : '' ?>" role="radio"
                                aria-checked="<?= $thoiGian === 'nam_nay' ? 'true' : 'false' ?>">Năm nay</a>
                            <a href="index.php?trang=nguoi_mua_ho_so&tab=thong_ke&thoi_gian=tat_ca"
                                class="u047-opt <?= $thoiGian === 'tat_ca' ? 'active' : '' ?>" role="radio"
                                aria-checked="<?= $thoiGian === 'tat_ca' ? 'true' : 'false' ?>">Tất cả</a>
                        </div>
                    </div>
                </div>

                <!-- 4 THẺ CHỈ SỐ KPI CHỌN LỌC RÕ RÀNG (LUCIAN #098 METRIC CARDS) -->
                <div class="row g-3 mb-4">
                    <!-- KPI 1: TỔNG CHI TIÊU THU MUA -->
                    <div class="col-6 col-lg-3">
                        <div class="ux u098-card">
                            <div class="u098-header">
                                <span class="u098-lbl">Tổng chi tiêu</span>
                            </div>
                            <div class="u098-val-row">
                                <span class="u098-num"
                                    style="color: #0284c7;"><?= dinh_dang_tien($kpi['tong_chi_tieu'] ?? 0) ?></span>
                            </div>
                            <div class="u098-sub">
                                Đã giải ngân cho <strong><?= number_format($kpi['so_don_thanh_cong'] ?? 0) ?></strong> đơn
                                chốt
                            </div>
                        </div>
                    </div>

                    <!-- KPI 2: TIỀN TIẾT KIỆM ĐƯỢC NHỜ MUA SỈ / ĐÀM PHÁN -->
                    <div class="col-6 col-lg-3">
                        <div class="ux u098-card">
                            <div class="u098-header">
                                <span class="u098-lbl">Tiết kiệm sỉ</span>
                            </div>
                            <div class="u098-val-row">
                                <span
                                    class="u098-num text-success"><?= dinh_dang_tien($kpi['tong_tiet_kiem'] ?? 0) ?></span>
                            </div>
                            <div class="u098-sub">
                                <span class="text-muted ms-1">Giảm so với giá niêm yết</span>
                            </div>
                        </div>
                    </div>

                    <!-- KPI 3: ĐƠN THU MUA THÀNH CÔNG -->
                    <div class="col-6 col-lg-3">
                        <div class="ux u098-card">
                            <div class="u098-header">
                                <span class="u098-lbl">Đơn thành công</span>
                            </div>
                            <div class="u098-val-row">
                                <span class="u098-num"
                                    style="color: #6366f1;"><?= number_format($kpi['so_don_thanh_cong'] ?? 0) ?></span>
                                <span class="u098-unit">đơn</span>
                            </div>
                            <div class="u098-sub">
                                Đang trao đổi: <strong><?= (int) ($kpi['so_don_dang_cho'] ?? 0) ?></strong> đơn
                            </div>
                        </div>
                    </div>

                    <!-- KPI 4: TỔNG KHỐI LƯỢNG HÀNG ĐÃ GOM -->
                    <div class="col-6 col-lg-3">
                        <div class="ux u098-card">
                            <div class="u098-header">
                                <span class="u098-lbl">Hàng đã gom</span>
                            </div>
                            <div class="u098-val-row">
                                <span class="u098-num" style="color: #d97706;"><?= $khoiLuongChuoi ?></span>
                            </div>
                            <div class="u098-sub">
                                Tổng lượng mua
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KHU VỰC BIỂU ĐỒ & DANH SÁCH TOP (LUCIAN #095 SURFACE & #039 LIST) -->
                <div class="row g-3">
                    <!-- CỘT TRÁI: BIỂU ĐỒ CHI TIÊU THEO THỜI GIAN -->
                    <div class="col-12 col-lg-7">
                        <div class="u095-surface">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">
                                        <i class="bi bi-graph-up text-primary me-1.5"></i>Biểu Đồ Chi Tiêu
                                    </h6>
                                </div>
                                <span class="badge bg-light text-muted border px-2.5 py-1.5">
                                    Đơn vị: VNĐ
                                </span>
                            </div>

                            <div style="position: relative; height: 320px; width: 100%;">
                                <canvas id="bieuDoChiTieu"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- CỘT PHẢI: TOP NÔNG SẢN ĐÃ GOM & ĐỐI TÁC THÂN THIẾT -->
                    <div class="col-12 col-lg-5">
                        <div class="d-flex flex-column gap-3">
                            <!-- TOP 1: NÔNG SẢN THU MUA NHIỀU NHẤT -->
                            <div class="u095-surface">
                                <div class="mb-3">
                                    <h6 class="fw-bold mb-0 text-dark">
                                        <i class="bi bi-trophy-fill text-warning me-1.5"></i> Nông Sản Mua Nhiều Nhất
                                    </h6>
                                </div>

                                <?php if (empty($topSanPham)): ?>
                                    <div class="text-center py-3 text-muted">
                                        <span class="small">Chưa có giao dịch thu mua trong mốc thời gian này</span>
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
                                                        Đã gom: <strong><?= dinh_dang_so_luong($sp['tong_so_luong']) ?></strong>
                                                        <?= htmlspecialchars($sp['don_vi_gia']) ?>
                                                    </div>
                                                </div>
                                                <div class="text-end flex-shrink-0">
                                                    <div class="fw-bold text-primary" style="font-size: 0.88rem;">
                                                        <?= dinh_dang_tien($sp['tong_tien']) ?> đ
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

                            <!-- TOP 2: NHÀ VƯỜN ĐỐI TÁC THÂN THIẾT -->
                            <div class="u095-surface">
                                <div class="mb-3">
                                    <h6 class="fw-bold mb-0 text-dark">
                                        <i class="bi bi-people-fill text-success me-1.5"></i> Nhà Vườn Hợp Tác Thân Thiết
                                    </h6>
                                </div>

                                <?php if (empty($topDoiTac)): ?>
                                    <div class="text-center py-3 text-muted">
                                        <span class="small">Chưa có thông tin đối tác trong mốc thời gian này</span>
                                    </div>
                                <?php else: ?>
                                    <div class="u039-list">
                                        <?php foreach ($topDoiTac as $index => $dt): ?>
                                            <div class="u039-row">
                                                <div class="u039-avatar-initial">
                                                    <?= mb_strtoupper(mb_substr($dt['ho_ten'], 0, 1)) ?>
                                                </div>
                                                <div class="flex-grow-1 min-w-0">
                                                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.88rem;"
                                                        title="<?= htmlspecialchars($dt['ho_ten']) ?>">
                                                        <?= htmlspecialchars($dt['ho_ten']) ?>
                                                    </div>
                                                    <div class="text-muted small" style="font-size: 0.76rem;">
                                                        <i class="bi bi-geo-alt text-danger"></i>
                                                        <?= htmlspecialchars($dt['tinh']) ?> • <strong><?= (int) $dt['so_don'] ?>
                                                            đơn</strong>
                                                    </div>
                                                </div>
                                                <div class="text-end flex-shrink-0 d-flex flex-column align-items-end">
                                                    <div class="fw-bold text-success" style="font-size: 0.88rem;">
                                                        <?= dinh_dang_tien($dt['tong_tien']) ?>
                                                    </div>
                                                    <a href="#" class="u039-call-btn mt-1">
                                                        <i
                                                            class="bi bi-telephone-fill"></i><?= !empty($dt['sdt']) ? htmlspecialchars($dt['sdt']) : 'Chưa có số' ?>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TÍCH HỢP CHART.JS & LUCIAN SPRING PILL CONTROLLER -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
        <script>
            // Định vị trượt mượt mà cho Lucian #039 Tabs & #047 Segmented Control
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
                        var left = aRect.left - cRect.left;
                        var width = aRect.width;
                        pill.style.setProperty('--x', left + 'px');
                        pill.style.setProperty('--w', width + 'px');
                    }

                    updatePosition();
                    window.addEventListener('resize', updatePosition);
                }

                setupPill('nhBuyerProfileNav', '.u039-pill', '.u039-tab');
                setupPill('nhBuyerTimeSeg', '.u047-knob', '.u047-opt');

                // Khởi tạo Chart.js biểu đồ chi tiêu
                var isDark = document.documentElement.getAttribute('data-theme') === 'dark' || localStorage.getItem('theme') === 'dark';
                var gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)';
                var textColor = isDark ? '#94a3b8' : '#64748b';

                var labels = <?= json_encode($bieuDo['labels'] ?? []) ?>;
                var values = <?= json_encode($bieuDo['values'] ?? []) ?>;
                var counts = <?= json_encode($bieuDo['counts'] ?? []) ?>;

                var ctx = document.getElementById('bieuDoChiTieu');
                if (!ctx) return;

                var gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 280);
                gradient.addColorStop(0, 'rgba(2, 132, 199, 0.35)');
                gradient.addColorStop(1, 'rgba(2, 132, 199, 0.01)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Chi tiêu',
                            data: values,
                            borderColor: '#0284c7',
                            backgroundColor: gradient,
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.3,
                            pointBackgroundColor: '#0284c7',
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
                                        return ' Chi tiêu: ' + new Intl.NumberFormat('vi-VN').format(val) + ' đ (' + soDon + ' đơn)';
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
        <!-- TAB 2: THÔNG TIN CÁ NHÂN & ĐỊA CHỈ NHẬN HÀNG                   -->
        <!-- ============================================================== -->
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8 col-xl-7">
                <div class="u095-surface p-3 p-md-4">
                    <!-- TIÊU ĐỀ HỒ SƠ -->
                    <div class="text-center mb-4">
                        <div class="rounded-circle text-white d-inline-flex align-items-center justify-content-center mb-2 shadow-sm"
                            style="width: 56px; height: 56px; background: linear-gradient(135deg, #0284c7, #0369a1) !important;">
                            <i class="bi bi-geo-alt-fill fs-2"></i>
                        </div>
                        <h4 class="fw-bold mb-1" style="letter-spacing: -0.3px;">Thông Tin & Địa Chỉ Nhận Hàng</h4>
                        <div class="text-muted small">Cung cấp địa chỉ chi tiết để nông hộ liên hệ và vận chuyển nông sản
                            đúng hẹn</div>
                    </div>

                    <div class="d-flex align-items-center gap-3 p-3 mb-4 rounded-4"
                        style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 1.5px solid #bae6fd;">
                        <div>
                            <div class="fw-bold text-primary" style="font-size: 0.95rem;">Tài khoản Thương Lái / Đơn Vị Thu
                                Mua</div>
                            <div class="text-muted small" style="font-size: 0.8rem;">Đảm bảo số điện thoại và địa chỉ chính
                                xác để nông hộ liên hệ và giao hàng đúng hẹn.</div>
                        </div>
                    </div>

                    <?php if (!empty($loi)): ?>
                        <div class="alert alert-danger py-2.5 px-3 rounded-3 fs-6 mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-circle-fill fs-5"></i>
                            <div><?= htmlspecialchars($loi) ?></div>
                        </div>
                    <?php endif; ?>

                    <form action="index.php?trang=nguoi_mua_ho_so" method="POST">
                        <div class="row g-3">
                            <!-- SỐ ĐIỆN THOẠI TÀI KHOẢN -->
                            <div class="col-sm-6">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    <i class="bi bi-telephone-fill text-primary me-1"></i>Số điện thoại (Tài khoản)
                                </label>
                                <div class="input-group">
                                    <span
                                        class="input-group-text bg-light border-end-0 rounded-start-pill text-muted small fw-bold">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>
                                    <input type="text"
                                        class="form-control bg-light text-muted fw-bold border-start-0 rounded-end-pill"
                                        value="<?= htmlspecialchars($nguoiDung['sdt']) ?>" readonly
                                        title="Số điện thoại dùng để đăng nhập (được khóa cố định). Liên hệ Quản trị viên nếu cần đổi số.">
                                </div>
                                <div class="form-text text-muted" style="font-size: 0.72rem;">
                                    🔒 Khóa bảo mật đăng nhập. Liên hệ Admin nếu cần đổi SĐT.
                                </div>
                            </div>

                            <!-- HỌ TÊN HOẶC ĐƠN VỊ THU MUA -->
                            <div class="col-sm-6">
                                <label for="ho_ten" class="form-label small fw-bold text-dark mb-1">
                                    <i class="bi bi-person-badge text-primary me-1"></i>Họ và tên / Đơn vị thu mua <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control fw-bold rounded-pill px-3 py-2" id="ho_ten"
                                    name="ho_ten" value="<?= htmlspecialchars($_POST['ho_ten'] ?? $nguoiDung['ho_ten']) ?>"
                                    placeholder="Ví dụ: Cửa Hàng Trái Cây Thúy Vy" required>
                            </div>

                            <!-- TỈNH / THÀNH PHỐ -->
                            <div class="col-sm-5">
                                <label for="tinh" class="form-label small fw-bold text-dark mb-1">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>Tỉnh / Thành phố <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control fw-bold rounded-pill px-3 py-2" id="tinh" name="tinh"
                                    value="<?= htmlspecialchars($_POST['tinh'] ?? $nguoiDung['tinh']) ?>"
                                    placeholder="Ví dụ: TP. Hồ Chí Minh, Bình Dương..." required>
                            </div>

                            <!-- ĐỊA CHỈ NHẬN HÀNG CHI TIẾT -->
                            <div class="col-sm-7">
                                <label for="dia_chi" class="form-label small fw-bold text-dark mb-1">
                                    <i class="bi bi-pin-map-fill text-primary me-1"></i>Địa chỉ nhận hàng chi tiết <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control fw-bold rounded-pill px-3 py-2" id="dia_chi"
                                    name="dia_chi"
                                    value="<?= htmlspecialchars($_POST['dia_chi'] ?? $nguoiDung['dia_chi']) ?>"
                                    placeholder="Số nhà, đường, phường/xã, quận/huyện..." required>
                            </div>
                        </div>

                        <!-- NÚT LƯU THAY ĐỔI -->
                        <div class="mt-4 pt-2">
                            <button type="submit"
                                class="btn btn-clean btn-lg w-100 py-2.5 fw-bold fs-6 rounded-pill shadow">
                                <i class="bi bi-floppy2-fill me-1.5"></i> Lưu Cập Nhật Thông Tin &amp; Địa Chỉ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- ĐIỀU KHIỂN HIỆU ỨNG TRƯỢT LUCIAN #039 & #047 (DÀNH CHO NGƯỜI MUA) -->
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

        setupPill('nhBuyerProfileNav', '.u039-pill', '.u039-tab');
        setupPill('nhBuyerTimeSeg', '.u047-knob', '.u047-opt');
    })();
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
<?php
// views/nguoi_mua/gio_hang.php
// Quản lý giỏ hàng thu mua - Tối ưu Mobile Cards & Bảng Desktop
$dsGioHang = $dsGioHang ?? [];

require_once __DIR__ . '/../layouts/header.php';
$tongTienTamTinh = 0;
$coMonVuotKho = false;
foreach ($dsGioHang as $item) {
    $m = quy_doi_don_vi($item['san_luong'], $item['don_vi_san_luong'], $item['don_vi_gia']);
    if ((float)$item['so_luong_dat'] > $m) {
        $coMonVuotKho = true;
    }
    $tongTienTamTinh += ((float)($item['gia_ap_dung'] ?? 0) * (float)$item['so_luong_dat']);
}
?>

<div class="container-fluid px-2 px-sm-3 px-lg-4 py-2">
    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
        <h4 class="fw-bold mb-0" style="color: #064e3b;">
            <i class="bi bi-cart3 text-success me-2"></i>Giỏ Hàng Thu Mua (<?= count($dsGioHang) ?> món)
        </h4>
        <a href="index.php" class="btn btn-outline-success btn-sm fw-bold rounded-pill">
            <i class="bi bi-arrow-left me-1"></i>Tiếp Tục Chọn
        </a>
    </div>

    <?php if (empty($dsGioHang)): ?>
        <div class="bg-white rounded-4 p-5 text-center border shadow-sm my-3">
            <i class="bi bi-cart-x text-muted" style="font-size: 3.5rem;"></i>
            <h4 class="fw-bold text-dark mt-2">Giỏ hàng của bạn đang trống</h4>
            <p class="text-muted mb-4 fs-6">Chưa có nông sản nào được thêm vào giỏ. Khám phá các mặt hàng chất lượng từ bà con ngay!</p>
            <a href="index.php" class="btn btn-clean btn-lg px-4 py-2.5 fw-bold rounded-pill shadow">
                <i class="bi bi-shop me-1"></i> Khám Phá Sàn Nông Sản
            </a>
        </div>
    <?php else: ?>
        <form action="index.php?trang=gui_don_hang" method="POST" id="formGuiDonHang">
            <div class="row g-3">
                <!-- CỘT TRÁI: DANH SÁCH SẢN PHẨM TRONG GIỎ (CARD TRÊN MOBILE, TABLE TRÊN DESKTOP) -->
                <div class="col-lg-8">
                    <!-- 1. GIAO DIỆN THẺ DI ĐỘNG (CHUẨN UX MOBILE, NÚT TĂNG GIẢM TO RÕ) -->
                    <div class="d-block d-lg-none">
                        <?php foreach ($dsGioHang as $sp): 
                            $giaHienTai = $sp['gia_ap_dung'] ?? 0;
                            $soLuong    = (float)$sp['so_luong_dat'];
                            $thanhTien  = $giaHienTai * $soLuong;
                            $maxMua     = quy_doi_don_vi($sp['san_luong'], $sp['don_vi_san_luong'], $sp['don_vi_gia']);
                            $anhHienThi = duong_dan_anh_sp($sp['anh'] ?? '');
                            $isVuotKho  = ($soLuong > $maxMua);
                        ?>
                            <div class="card mb-3 border shadow-sm rounded-3 overflow-hidden <?= $isVuotKho ? 'border-danger' : 'border-light-subtle' ?>">
                                <div class="card-body p-3">
                                    <!-- Nông sản & Nhà vườn -->
                                    <div class="d-flex align-items-center gap-2.5 mb-2 pb-2 border-bottom">
                                        <img src="<?= $anhHienThi ?>" alt="<?= htmlspecialchars($sp['ten']) ?>" 
                                             class="rounded-3 border flex-shrink-0" style="width: 58px; height: 58px; object-fit: cover;" 
                                             onerror="this.src='assets/images/nong_san_mac_dinh.svg'">
                                        <div class="flex-grow-1 min-w-0">
                                            <h6 class="fw-bold mb-1 text-dark text-truncate" style="font-size: 0.98rem;">
                                                <?= htmlspecialchars($sp['ten']) ?>
                                            </h6>
                                            <div class="small text-muted text-truncate mb-0.5">
                                                <i class="bi bi-house-door-fill text-success"></i> <?= htmlspecialchars($sp['ten_nong_ho']) ?> (<?= htmlspecialchars($sp['tinh_nong_ho'] ?? 'Toàn quốc') ?>)
                                            </div>
                                            <div class="small text-muted" style="font-size: 0.78rem;">
                                                Kho còn: <strong class="text-dark"><?= dinh_dang_kho_thong_minh($sp['san_luong'], $sp['don_vi_san_luong']) ?></strong>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-outline-danger btn-sm p-1.5 rounded-pill flex-shrink-0" 
                                                title="Bỏ khỏi giỏ" onclick="xacNhanXoaMon(<?= $sp['ma_san_pham'] ?>, '<?= htmlspecialchars(addslashes($sp['ten'])) ?>')">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </div>

                                    <!-- Số lượng & Giá đề xuất -->
                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <label class="form-label small text-muted fw-bold mb-1">Số lượng mua:</label>
                                            <div class="input-group input-group-sm">
                                                <input type="number" step="any" min="0.01" max="<?= (float)$maxMua ?>" 
                                                       data-max="<?= (float)$maxMua ?>" data-id="<?= $sp['ma_san_pham'] ?>"
                                                       id="sl_m_<?= $sp['ma_san_pham'] ?>"
                                                       value="<?= (float)$soLuong ?>" 
                                                       class="form-control fw-bold text-center nh-input-sl <?= $isVuotKho ? 'is-invalid' : '' ?>"
                                                       oninput="dongBoSoLuong(<?= $sp['ma_san_pham'] ?>, this.value, 'mobile')"
                                                       onchange="xuLyDoiSoLuong(<?= $sp['ma_san_pham'] ?>, 'mobile')">
                                                <span class="input-group-text bg-success bg-opacity-10 text-success fw-bold"><?= htmlspecialchars($sp['don_vi_gia']) ?></span>
                                            </div>
                                            <div id="canhBaoVuotKho_m_<?= $sp['ma_san_pham'] ?>" class="text-danger small mt-1 <?= $isVuotKho ? '' : 'd-none' ?>" style="font-size: 0.75rem;">
                                                Kho tối đa <?= dinh_dang_so_luong($maxMua) ?>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small text-muted fw-bold mb-1">Giá đề xuất (đ):</label>
                                            <div class="input-group input-group-sm">
                                                <input type="number" step="any" min="0" 
                                                       id="gia_m_<?= $sp['ma_san_pham'] ?>"
                                                       value="<?= $giaHienTai > 0 ? (float)$giaHienTai : '' ?>" 
                                                       placeholder="Giá đề xuất" 
                                                       class="form-control fw-bold text-success"
                                                       oninput="dongBoGia(<?= $sp['ma_san_pham'] ?>, this.value, 'mobile')"
                                                       onchange="capNhatMon(<?= $sp['ma_san_pham'] ?>, 'mobile')">
                                                <span class="input-group-text bg-light fw-bold text-muted small">/<?= htmlspecialchars($sp['don_vi_gia']) ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Ghi chú đóng gói / giao hàng -->
                                    <div class="mb-2">
                                        <input type="text" name="ghi_chu_<?= $sp['ma_san_pham'] ?>" class="form-control form-control-sm" 
                                               placeholder="Ghi chú đóng gói, nơi giao (tùy chọn)...">
                                    </div>

                                    <!-- Thành tiền tạm tính -->
                                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                        <span class="small text-muted fw-bold">Tạm tính:</span>
                                        <span class="fw-bold text-success fs-5"><?= dinh_dang_tien($thanhTien) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- 2. GIAO DIỆN BẢNG TRÊN MÁY TÍNH / MÀN HÌNH LỚN -->
                    <div class="bg-white rounded-4 border shadow-sm p-3 d-none d-lg-block">
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="min-width: 240px;">Nông Sản &amp; Nhà Vườn</th>
                                        <th style="width: 160px;">Số Lượng</th>
                                        <th style="min-width: 180px;">Giá Đề Xuất (đ)</th>
                                        <th style="min-width: 140px;">Tạm Tính</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dsGioHang as $sp): 
                                        $giaHienTai = $sp['gia_ap_dung'] ?? 0;
                                        $soLuong    = (float)$sp['so_luong_dat'];
                                        $thanhTien  = $giaHienTai * $soLuong;
                                        $maxMua     = quy_doi_don_vi($sp['san_luong'], $sp['don_vi_san_luong'], $sp['don_vi_gia']);
                                        $anhHienThi = duong_dan_anh_sp($sp['anh'] ?? '');
                                        $isVuotKho  = ($soLuong > $maxMua);
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="<?= $anhHienThi ?>" alt="<?= htmlspecialchars($sp['ten']) ?>" 
                                                         class="rounded border" style="width: 54px; height: 54px; object-fit: cover;" onerror="this.src='assets/images/nong_san_mac_dinh.svg'">
                                                    <div>
                                                        <h6 class="fw-bold text-dark mb-1 text-truncate" style="max-width: 200px;">
                                                            <?= htmlspecialchars($sp['ten']) ?>
                                                        </h6>
                                                        <div class="text-muted small">
                                                            <i class="bi bi-house-door-fill text-success"></i> <strong><?= htmlspecialchars($sp['ten_nong_ho']) ?></strong> (<?= htmlspecialchars($sp['tinh_nong_ho'] ?? 'Toàn quốc') ?>)
                                                        </div>
                                                        <div class="text-muted small">
                                                            Kho còn: <strong><?= dinh_dang_kho_thong_minh($sp['san_luong'], $sp['don_vi_san_luong']) ?></strong>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td style="min-width: 170px;">
                                                <div class="input-group">
                                                    <input type="number" step="any" min="0.01" max="<?= (float)$maxMua ?>" 
                                                           data-max="<?= (float)$maxMua ?>"
                                                           data-id="<?= $sp['ma_san_pham'] ?>"
                                                           id="sl_<?= $sp['ma_san_pham'] ?>"
                                                           value="<?= (float)$soLuong ?>" 
                                                           class="form-control fw-bold text-center nh-input-sl <?= $isVuotKho ? 'is-invalid' : '' ?>"
                                                           oninput="dongBoSoLuong(<?= $sp['ma_san_pham'] ?>, this.value, 'desktop')"
                                                           onchange="xuLyDoiSoLuong(<?= $sp['ma_san_pham'] ?>, 'desktop')">
                                                    <span class="input-group-text bg-success bg-opacity-10 text-success fw-bold"><?= htmlspecialchars($sp['don_vi_gia']) ?></span>
                                                </div>
                                                <div id="canhBaoVuotKho_<?= $sp['ma_san_pham'] ?>" class="text-danger small mt-1 <?= $isVuotKho ? '' : 'd-none' ?>" style="font-size: 0.78rem;">
                                                    Kho chỉ còn tối đa <?= dinh_dang_so_luong($maxMua) ?> <?= htmlspecialchars($sp['don_vi_gia']) ?>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="input-group mb-1">
                                                    <input type="number" step="any" min="0" 
                                                           id="gia_<?= $sp['ma_san_pham'] ?>"
                                                           value="<?= $giaHienTai > 0 ? (float)$giaHienTai : '' ?>" 
                                                           placeholder="Nhập giá" 
                                                           class="form-control fw-bold text-success"
                                                           oninput="dongBoGia(<?= $sp['ma_san_pham'] ?>, this.value, 'desktop'); capNhatTienPreview(this, 'preview_gia_<?= $sp['ma_san_pham'] ?>')"
                                                           onchange="capNhatMon(<?= $sp['ma_san_pham'] ?>, 'desktop')">
                                                    <span class="input-group-text bg-light fw-bold text-muted">/<?= htmlspecialchars($sp['don_vi_gia']) ?></span>
                                                </div>
                                                <div id="preview_gia_<?= $sp['ma_san_pham'] ?>">
                                                    <span class="currency-preview-badge"><?= dinh_dang_tien($giaHienTai) ?></span>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="fw-bold text-success fs-5">
                                                    <?= dinh_dang_tien($thanhTien) ?>
                                                </div>
                                            </td>

                                            <td class="text-end">
                                                <button type="button" class="btn btn-outline-danger p-2 rounded-circle" 
                                                        title="Bỏ khỏi giỏ"
                                                        onclick="xacNhanXoaMon(<?= $sp['ma_san_pham'] ?>, '<?= htmlspecialchars(addslashes($sp['ten'])) ?>')">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="pt-0 pb-3 border-bottom">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light fw-bold text-muted"><i class="bi bi-chat-left-dots me-1"></i> Ghi chú:</span>
                                                    <input type="text" name="ghi_chu_<?= $sp['ma_san_pham'] ?>" 
                                                           class="form-control" 
                                                           placeholder="Ví dụ: Giao vào sáng sớm, đóng thùng xốp giúp tôi...">
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- CỘT PHẢI: TỔNG KẾT ĐƠN HÀNG & NÚT GỬI ĐƠN -->
                <div class="col-lg-4">
                    <div class="bg-white rounded-4 border shadow-sm p-3 p-md-4 sticky-top" style="top: 80px;">
                        <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                            <i class="bi bi-receipt-cutoff text-success me-1"></i> Tóm Tắt Đơn Thu Mua
                        </h5>

                        <div class="d-flex justify-content-between text-muted fs-6 mb-2">
                            <span>Số loại nông sản:</span>
                            <strong class="text-dark fs-5"><?= count($dsGioHang) ?> loại</strong>
                        </div>

                        <div class="d-flex justify-content-between align-items-baseline mb-3 pt-3 border-top">
                            <span class="fw-bold text-dark fs-5">Tạm tính:</span>
                            <span class="fs-2 fw-bold text-success">
                                <?= dinh_dang_tien($tongTienTamTinh) ?>
                            </span>
                        </div>

                        <?php $ndHienTai = nguoi_dung_hien_tai(); ?>
                        <div class="p-3 mb-3 rounded-3 bg-light border small">
                            <div class="d-flex align-items-center justify-content-between mb-1.5">
                                <span class="fw-bold text-dark"><i class="bi bi-geo-alt-fill text-danger me-1"></i>Địa chỉ nhận hàng:</span>
                                <a href="index.php?trang=nguoi_mua_ho_so" class="text-success text-decoration-none fw-bold small">Thay đổi</a>
                            </div>
                            <div class="fw-bold text-dark mb-0.5"><?= htmlspecialchars($ndHienTai['ho_ten'] ?? '') ?> - <span class="text-muted"><?= htmlspecialchars($ndHienTai['sdt'] ?? '') ?></span></div>
                            <div class="text-muted" style="line-height: 1.4;">
                                <?= htmlspecialchars($ndHienTai['dia_chi'] ?? 'Chưa cập nhật địa chỉ') ?><?= !empty($ndHienTai['tinh']) ? ', ' . htmlspecialchars($ndHienTai['tinh']) : '' ?>
                            </div>
                        </div>

                        <div class="p-3 mb-3 rounded-3 bg-light border small text-muted">
                            <i class="bi bi-info-circle text-primary me-1"></i>
                            Đơn hàng sẽ được chuyển đến từng nông hộ tương ứng để hai bên thỏa thuận giá và chốt đơn.
                        </div>

                        <div id="thongBaoLoiGioHang" class="alert alert-danger py-2 px-3 small mb-3 <?= $coMonVuotKho ? '' : 'd-none' ?>">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Có sản phẩm vượt quá sản lượng kho. Vui lòng điều chỉnh lại trước khi gửi đơn.
                        </div>

                        <button type="button" id="btnGuiDonHang" 
                                class="btn btn-clean btn-lg w-100 py-2.5 fw-bold fs-6 rounded-pill shadow <?= $coMonVuotKho ? 'disabled opacity-50' : '' ?>" 
                                onclick="xacNhanGuiDonHang()">
                            <i class="bi bi-send-check-fill me-2 fs-5"></i> Gửi Đơn Thu Mua
                        </button>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<!-- FORM ẨN DÙNG ĐỂ CẬP NHẬT GIỎ HÀNG -->
<form id="formCapNhatGioHang" action="index.php?trang=cap_nhat_gio_hang" method="POST" style="display:none;">
    <input type="hidden" name="ma_san_pham" id="formCapNhatMaSP">
    <input type="hidden" name="so_luong" id="formCapNhatSoLuong">
    <input type="hidden" name="gia_de_nghi" id="formCapNhatGia">
</form>

<script>
// Đồng bộ giá trị giữa mobile và desktop inputs
function dongBoSoLuong(maSanPham, giaTri, nguon) {
    if (nguon === 'mobile') {
        const desktopInput = document.getElementById('sl_' + maSanPham);
        if (desktopInput) desktopInput.value = giaTri;
    } else if (nguon === 'desktop') {
        const mobileInput = document.getElementById('sl_m_' + maSanPham);
        if (mobileInput) mobileInput.value = giaTri;
    } else {
        const desktopInput = document.getElementById('sl_' + maSanPham);
        const mobileInput = document.getElementById('sl_m_' + maSanPham);
        if (desktopInput) desktopInput.value = giaTri;
        if (mobileInput) mobileInput.value = giaTri;
    }
    kiemTraSoLuongItem(maSanPham);
}

function dongBoGia(maSanPham, giaTri, nguon) {
    if (nguon === 'mobile') {
        const desktopInput = document.getElementById('gia_' + maSanPham);
        if (desktopInput) desktopInput.value = giaTri;
    } else if (nguon === 'desktop') {
        const mobileInput = document.getElementById('gia_m_' + maSanPham);
        if (mobileInput) mobileInput.value = giaTri;
    }
}

// Cập nhật preview tiền VND khi gõ
function capNhatTienPreview(input, previewId) {
    const previewEl = document.getElementById(previewId);
    if (!previewEl) return;
    const val = parseFloat(input.value) || 0;
    if (val > 0) {
        previewEl.innerHTML = `<span class="currency-preview-badge">${new Intl.NumberFormat('vi-VN').format(val)} đ</span>`;
    } else {
        previewEl.innerHTML = `<span class="currency-preview-badge text-muted">0 đ</span>`;
    }
}

// Kiểm tra số lượng 1 món real-time khi gõ
function kiemTraSoLuongItem(maSanPham) {
    const input = document.getElementById('sl_' + maSanPham);
    const inputM = document.getElementById('sl_m_' + maSanPham);
    const canhBao = document.getElementById('canhBaoVuotKho_' + maSanPham);
    const canhBaoM = document.getElementById('canhBaoVuotKho_m_' + maSanPham);
    
    const targetInput = input || inputM;
    if (!targetInput) return false;
    
    const val = parseFloat(targetInput.value) || 0;
    const max = parseFloat(targetInput.getAttribute('data-max')) || 0;
    const vuotKho = (val > max || val <= 0);
    
    if (input) {
        if (vuotKho) input.classList.add('is-invalid');
        else input.classList.remove('is-invalid');
    }
    if (inputM) {
        if (vuotKho) inputM.classList.add('is-invalid');
        else inputM.classList.remove('is-invalid');
    }
    
    if (canhBao) {
        if (vuotKho && val > max) canhBao.classList.remove('d-none');
        else canhBao.classList.add('d-none');
    }
    if (canhBaoM) {
        if (vuotKho && val > max) canhBaoM.classList.remove('d-none');
        else canhBaoM.classList.add('d-none');
    }
    
    kiemTraTongTheGioHang();
    return val <= max && val > 0;
}

// Kiểm tra toàn bộ giỏ hàng và khóa/mở nút gửi đơn
function kiemTraTongTheGioHang() {
    const checkedIds = new Set();
    const slInputs = document.querySelectorAll('.nh-input-sl');
    let hopLe = true;
    
    slInputs.forEach(input => {
        const id = input.getAttribute('data-id');
        if (id && !checkedIds.has(id)) {
            checkedIds.add(id);
            const val = parseFloat(input.value) || 0;
            const max = parseFloat(input.getAttribute('data-max')) || 0;
            if (val <= 0 || val > max) {
                hopLe = false;
            }
        }
    });
    
    const btnGui = document.getElementById('btnGuiDonHang');
    const alertBox = document.getElementById('thongBaoLoiGioHang');
    
    if (btnGui) {
        if (!hopLe) {
            btnGui.classList.add('disabled', 'opacity-50');
            btnGui.setAttribute('aria-disabled', 'true');
        } else {
            btnGui.classList.remove('disabled', 'opacity-50');
            btnGui.removeAttribute('aria-disabled');
        }
    }
    
    if (alertBox) {
        if (!hopLe) {
            alertBox.classList.remove('d-none');
        } else {
            alertBox.classList.add('d-none');
        }
    }
    
    return hopLe;
}

// Xử lý khi thay đổi số lượng: chỉ submit cập nhật nếu số lượng hợp lệ
function xuLyDoiSoLuong(maSanPham, nguon) {
    const hopLe = kiemTraSoLuongItem(maSanPham);
    if (hopLe) {
        capNhatMon(maSanPham, nguon);
    }
}

// Cập nhật số lượng hoặc giá đề xuất trong giỏ
function capNhatMon(maSanPham, nguon) {
    let inputSl = (nguon === 'mobile')
        ? (document.getElementById('sl_m_' + maSanPham) || document.getElementById('sl_' + maSanPham))
        : (document.getElementById('sl_' + maSanPham) || document.getElementById('sl_m_' + maSanPham));
        
    let inputGia = (nguon === 'mobile')
        ? (document.getElementById('gia_m_' + maSanPham) || document.getElementById('gia_' + maSanPham))
        : (document.getElementById('gia_' + maSanPham) || document.getElementById('gia_m_' + maSanPham));
        
    if (!inputSl) return;

    const val = parseFloat(inputSl.value) || 0;
    const max = parseFloat(inputSl.getAttribute('data-max')) || 0;
    if (val > max || val <= 0) {
        kiemTraSoLuongItem(maSanPham);
        return;
    }
    
    document.getElementById('formCapNhatMaSP').value   = maSanPham;
    document.getElementById('formCapNhatSoLuong').value = inputSl.value;
    document.getElementById('formCapNhatGia').value     = inputGia ? inputGia.value : '';
    
    document.getElementById('formCapNhatGioHang').submit();
}

// Xác nhận xóa món bằng Modal giao diện
function xacNhanXoaMon(maSanPham, tenSanPham) {
    moModalXacNhan(
        'Bỏ Nông Sản Khỏi Giỏ',
        `Bạn có chắc chắn muốn bỏ nông sản <strong>${tenSanPham}</strong> khỏi giỏ hàng thu mua?`,
        function() {
            window.location.href = 'index.php?trang=xoa_gio_hang&id=' + maSanPham;
        },
        'Bỏ Khỏi Giỏ',
        'btn-danger'
    );
}

// Xác nhận gửi đơn thu mua bằng Modal giao diện
function xacNhanGuiDonHang() {
    if (!kiemTraTongTheGioHang()) {
        const alertBox = document.getElementById('thongBaoLoiGioHang');
        if (alertBox) {
            alertBox.classList.remove('d-none');
            alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return;
    }

    moModalXacNhan(
        'Xác Nhận Gửi Đơn Thu Mua',
        'Hệ thống sẽ gửi yêu cầu đặt hàng cùng mức giá đề xuất của bạn đến các nông hộ liên quan. Bạn có muốn tiếp tục?',
        function() {
            document.getElementById('formGuiDonHang').submit();
        },
        'Gửi Đơn Ngay',
        'btn-success'
    );
}

document.addEventListener('DOMContentLoaded', function() {
    kiemTraTongTheGioHang();
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

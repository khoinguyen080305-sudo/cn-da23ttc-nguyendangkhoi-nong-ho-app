<?php
// views/nguoi_mua/nhu_cau_cua_toi.php
// Quản lý tin nhu cầu thu mua của Người Mua - Tối ưu Mobile Cards & Bảng Desktop
$dsDanhMuc = $dsDanhMuc ?? [];
$dsNhuCau  = $dsNhuCau ?? [];

require_once __DIR__ . '/../layouts/header.php';
$nguoiDung = nguoi_dung_hien_tai();
?>

<div class="container-fluid px-2 px-sm-3 px-lg-4 py-2">
    <!-- TIÊU ĐỀ -->
    <div class="mb-3">
        <h4 class="fw-bold mb-1" style="color: #064e3b;">
            <i class="bi bi-megaphone-fill text-warning me-2"></i>Nhu Cầu Thu Mua Của Bạn
        </h4>
        <div class="text-muted small">Đăng tin để nông hộ có nông sản phù hợp chủ động liên hệ chào giá cho bạn.</div>
    </div>

    <div class="row g-3 g-lg-4">
        <!-- CỘT TRÁI: FORM ĐĂNG TIN NHU CẦU THU MUA -->
        <div class="col-lg-5 col-xl-4">
            <div class="bg-white p-3 p-md-4 rounded-4 border shadow-sm">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-megaphone-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.1rem;">Đăng Nhu Cầu Mới</h5>
                    </div>
                </div>

                <form action="index.php?trang=nhu_cau_cua_toi" method="POST">
                    <!-- DANH MỤC NÔNG SẢN -->
                    <div class="mb-2.5">
                        <label for="ma_danh_muc" class="form-label small fw-bold">Ngành hàng nông sản <span class="text-danger">*</span></label>
                        <select name="ma_danh_muc" id="ma_danh_muc" class="form-select fw-semibold rounded-pill" required>
                            <option value="">-- Chọn ngành hàng --</option>
                            <?php foreach ($dsDanhMuc as $dm): ?>
                                <option value="<?= $dm['ma_danh_muc'] ?>">
                                    <?= htmlspecialchars($dm['ten_danh_muc']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- TÊN NÔNG SẢN CỤ THỂ -->
                    <div class="mb-2.5">
                        <label for="ten_nong_san" class="form-label small fw-bold">Tên nông sản cụ thể <span class="text-danger">*</span></label>
                        <input type="text" name="ten_nong_san" id="ten_nong_san" class="form-control fw-semibold rounded-pill" 
                               placeholder="Ví dụ: Bưởi da xanh..." required>
                    </div>

                    <!-- SỐ LƯỢNG & ĐƠN VỊ TÍNH -->
                    <div class="row g-2 mb-2.5">
                        <div class="col-7">
                            <label for="so_luong" class="form-label small fw-bold">Số lượng cần <span class="text-danger">*</span></label>
                            <input type="number" step="any" min="0.01" name="so_luong" id="so_luong" class="form-control fw-bold rounded-pill" placeholder="Ví dụ: 500" required>
                        </div>
                        <div class="col-5">
                            <label for="don_vi" class="form-label small fw-bold">Đơn vị <span class="text-danger">*</span></label>
                            <select name="don_vi" id="don_vi" class="form-select fw-bold rounded-pill" required>
                                <?php foreach (DON_VI as $maDv => $tenDv): ?>
                                    <option value="<?= $maDv ?>"><?= $tenDv ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- MỨC GIÁ MUA TỐI ĐA -->
                    <div class="mb-2.5">
                        <label for="gia_toi_da" class="form-label small fw-bold">Giá mua tối đa (đ/ đơn vị)</label>
                        <input type="number" step="any" min="0" name="gia_toi_da" id="gia_toi_da" 
                               class="form-control fw-bold text-success rounded-pill" 
                               placeholder="Bỏ trống nếu chờ báo giá"
                               oninput="capNhatTienPreview(this, 'previewGiaToiDa')">
                        <div id="previewGiaToiDa" class="mt-1">
                            <span class="currency-preview-badge small">Liên hệ báo giá</span>
                        </div>
                    </div>

                    <!-- TỈNH THÀNH TIẾP NHẬN HÀNG -->
                    <div class="mb-2.5">
                        <label for="tinh" class="form-label small fw-bold">Tỉnh/ Nơi nhận hàng <span class="text-danger">*</span></label>
                        <input type="text" name="tinh" id="tinh" class="form-control fw-bold rounded-pill" 
                               value="<?= htmlspecialchars($_POST['tinh'] ?? ($nguoiDung['tinh'] ?? '')) ?>"
                               placeholder="Ví dụ: TP. Hồ Chí Minh, Cần Thơ..." required>
                    </div>

                    <!-- THỜI HẠN CẦN NÔNG SẢN -->
                    <div class="mb-3">
                        <label for="han_can" class="form-label small fw-bold">Hạn chót gom hàng <span class="text-danger">*</span></label>
                        <input type="date" name="han_can" id="han_can" class="form-control fw-bold rounded-pill" min="<?= date('Y-m-d') ?>" required>
                    </div>

                    <button type="submit" class="btn btn-clean btn-lg w-100 py-2.5 fw-bold fs-6 rounded-pill shadow">
                        <i class="bi bi-send-plus-fill me-1"></i> Đăng Tin Thu Mua
                    </button>
                </form>
            </div>
        </div>

        <!-- CỘT PHẢI: DANH SÁCH CÁC TIN NHU CẦU ĐÃ ĐĂNG -->
        <div class="col-lg-7 col-xl-8">
            <div class="bg-white p-3 p-md-4 rounded-4 border shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-list-check text-success me-1"></i> Tin Nhu Cầu Đã Đăng (<?= count($dsNhuCau) ?>)
                    </h5>
                </div>

                <?php if (empty($dsNhuCau)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                        <h6 class="fw-bold text-dark fs-5">Bạn chưa đăng tin nhu cầu nào</h6>
                        <p class="mb-0 small">Điền thông tin bên cạnh để thông báo cho nông hộ về sản lượng bạn cần mua.</p>
                    </div>
                <?php else: ?>
                    <!-- 1. GIAO DIỆN THẺ DI ĐỘNG -->
                    <div class="d-block d-md-none">
                        <?php foreach ($dsNhuCau as $nc): ?>
                            <div class="card mb-3 border shadow-sm rounded-3">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-light text-dark border fw-bold">
                                            <?= ma_hien_thi('nhu_cau', $nc['ma_nhu_cau']) ?>
                                        </span>
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($nc['tinh']) ?>
                                        </span>
                                    </div>

                                    <h6 class="fw-bold text-dark mb-1 fs-6">
                                        <?= htmlspecialchars($nc['ten_nong_san'] ?? $nc['ten_danh_muc']) ?>
                                    </h6>
                                    <div class="small text-muted mb-2">
                                        Số lượng: <strong class="text-dark fs-6"><?= dinh_dang_so_luong($nc['so_luong']) ?> <?= htmlspecialchars($nc['don_vi']) ?></strong> | 
                                        Giá tối đa: <strong class="text-success"><?= dinh_dang_tien($nc['gia_toi_da']) ?></strong>
                                    </div>
                                    <div class="small text-muted mb-3">
                                        Hạn chót gom hàng: <strong><?= dinh_dang_ngay($nc['han_can']) ?></strong>
                                    </div>

                                    <button type="button" class="btn btn-outline-danger btn-sm w-100 py-1.5 fw-bold rounded-pill" 
                                            onclick="xacNhanXoaNhuCau(<?= $nc['ma_nhu_cau'] ?>, '<?= ma_hien_thi('nhu_cau', $nc['ma_nhu_cau']) ?>')">
                                        <i class="bi bi-trash3-fill me-1"></i>Xóa Tin Nhu Cầu Này
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- 2. GIAO DIỆN BẢNG TRÊN MÁY TÍNH -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table align-middle table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Mã Tin</th>
                                    <th>Nông Sản &amp; Số Lượng</th>
                                    <th>Giá Tối Đa</th>
                                    <th>Khu Vực Nhận</th>
                                    <th>Hạn Chót</th>
                                    <th class="text-end">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dsNhuCau as $nc): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-light text-dark border fw-bold fs-6">
                                                <?= ma_hien_thi('nhu_cau', $nc['ma_nhu_cau']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark fs-6">
                                                <?= htmlspecialchars($nc['ten_nong_san'] ?? $nc['ten_danh_muc']) ?>
                                            </div>
                                            <?php if (!empty($nc['ten_nong_san'])): ?>
                                                <div class="small text-muted mb-1">
                                                    Ngành: <?= htmlspecialchars($nc['ten_danh_muc']) ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="text-success fw-bold">
                                                <?= dinh_dang_so_luong($nc['so_luong']) ?> <?= htmlspecialchars($nc['don_vi']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <strong class="text-dark fs-6">
                                                <?= dinh_dang_tien($nc['gia_toi_da']) ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border fs-6">
                                                <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($nc['tinh']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-muted fw-bold">
                                                <?= dinh_dang_ngay($nc['han_can']) ?>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-outline-danger btn-sm px-3 py-1 fw-bold rounded-pill" 
                                                    title="Xóa tin này"
                                                    onclick="xacNhanXoaNhuCau(<?= $nc['ma_nhu_cau'] ?>, '<?= ma_hien_thi('nhu_cau', $nc['ma_nhu_cau']) ?>')">
                                                <i class="bi bi-trash3-fill me-1"></i>Xóa
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Xác nhận xóa tin nhu cầu bằng in-app modal
function xacNhanXoaNhuCau(maNhuCau, maHienThi) {
    moModalXacNhan(
        'Xóa Tin Nhu Cầu Thu Mua',
        `Bạn có chắc chắn muốn xóa tin nhu cầu thu mua <strong>${maHienThi}</strong>?`,
        function() {
            window.location.href = 'index.php?trang=xoa_nhu_cau&id=' + maNhuCau;
        },
        'Xóa Tin',
        'btn-danger'
    );
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

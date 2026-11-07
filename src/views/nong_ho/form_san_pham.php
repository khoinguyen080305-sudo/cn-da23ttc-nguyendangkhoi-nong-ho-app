<?php
// views/nong_ho/form_san_pham.php
// Form Thêm mới / Cập nhật Nông Sản của Nông Hộ
// Khởi tạo giá trị mặc định để IDE không báo lỗi đỏ biến chưa định nghĩa
/** @var array $sanPham */
$sanPham   = (isset($sanPham) && is_array($sanPham)) ? $sanPham : [];
$dsDanhMuc = $dsDanhMuc ?? [];
$loi       = $loi ?? '';

require_once __DIR__ . '/../layouts/header.php';

$laChinhSua = !empty($sanPham['ma_san_pham']);
$tieuDeTrang = $laChinhSua ? 'Chỉnh Sửa Nông Sản' : 'Đăng Bán Nông Sản Mới';

$chungNhanDaChon = [];
if ($laChinhSua && !empty($sanPham['chung_nhan'])) {
    $chungNhanDaChon = explode(',', $sanPham['chung_nhan']);
}
?>

<div class="container py-3">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="bg-white p-3.5 p-md-4 rounded-3 border shadow-sm">
                <!-- TIÊU ĐỀ & ĐIỀU HƯỚNG QUAY LẠI -->
                <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                    <div>
                        <h4 class="fw-bold mb-1" style="color: #064e3b;">
                            <i class="bi bi-pencil-square text-success me-2"></i><?= $tieuDeTrang ?>
                        </h4>
                    </div>
                    <a href="index.php?trang=nong_ho_san_pham" class="btn btn-outline-secondary btn-sm fw-bold rounded-pill">
                        <i class="bi bi-arrow-left me-1"></i>Quay lại
                    </a>
                </div>

                <?php if (!empty($loi)): ?>
                    <div class="alert alert-danger py-2 fs-6 mb-3">
                        <i class="bi bi-exclamation-circle-fill me-1"></i> <?= htmlspecialchars($loi) ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?trang=nong_ho_form_san_pham<?= $laChinhSua ? '&id=' . ($sanPham['ma_san_pham'] ?? 0) : '' ?>" 
                      method="POST" enctype="multipart/form-data">
                    
                    <!-- 1. NGÀNH NÔNG SẢN (ĐƯA LÊN TRƯỚC ĐỂ RÀO ĐƠN VỊ VÀ GỢI Ý TÊN) -->
                    <div class="mb-3">
                        <label for="ma_danh_muc" class="form-label fw-bold text-dark fs-6">
                            <i class="bi bi-tag-fill text-success me-1"></i>Ngành nông sản <span class="text-danger">*</span>
                        </label>
                        <select name="ma_danh_muc" id="ma_danh_muc" class="form-select form-select-lg fw-bold" required onchange="xuLyDoiDanhMuc(this)">
                            <option value="">-- Chọn ngành nông sản --</option>
                            <?php 
                                $idDangChon = $_POST['ma_danh_muc'] ?? ($sanPham['ma_danh_muc'] ?? 0);
                                $tenDanhMucDangChon = '';
                            ?>
                            <?php foreach ($dsDanhMuc as $dm): ?>
                                <?php 
                                    $daChon = ($idDangChon == $dm['ma_danh_muc']);
                                    if ($daChon) $tenDanhMucDangChon = $dm['ten_danh_muc'];
                                ?>
                                <option value="<?= $dm['ma_danh_muc'] ?>" 
                                        data-ten="<?= htmlspecialchars($dm['ten_danh_muc']) ?>"
                                        <?= $daChon ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dm['ten_danh_muc']) ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="-1" class="text-success fw-bold" <?= (isset($_POST['ma_danh_muc']) && $_POST['ma_danh_muc'] == '-1') ? 'selected' : '' ?>>
                                + Thêm ngành nông sản mới...
                            </option>
                        </select>

                        <!-- Ô NHẬP TÊN NGÀNH MỚI KHI NÔNG DÂN CHỌN "+ Thêm ngành nông sản mới..." -->
                        <div id="khoiThemDanhMucMoi" class="mt-2 <?= (isset($_POST['ma_danh_muc']) && $_POST['ma_danh_muc'] == '-1') ? '' : 'd-none' ?>">
                            <div class="input-group">
                                <span class="input-group-text bg-success bg-opacity-10 text-success fw-bold">
                                    <i class="bi bi-plus-circle me-1"></i>Tên ngành mới
                                </span>
                                <input type="text" name="ten_danh_muc_moi" id="ten_danh_muc_moi" class="form-control fw-bold" 
                                       placeholder="Ví dụ: Dừa sáp & Cây có trái, Nấm sạch, Hoa kiểng..." 
                                       value="<?= htmlspecialchars($_POST['ten_danh_muc_moi'] ?? '') ?>"
                                       oninput="xuLyNhapDanhMucMoi(this.value)">
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">
                                <i class="bi bi-info-circle me-1 text-primary"></i>Hệ thống sẽ tự động thêm ngành này vào bộ lọc ngoài sàn khi có nông sản bày bán.
                            </small>
                        </div>
                    </div>

                    <!-- 2. TÊN NÔNG SẢN -->
                    <div class="mb-3">
                        <label for="ten" class="form-label fw-bold text-dark fs-6">
                            <i class="bi bi-basket2-fill text-success me-1"></i>Tên nông sản <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-lg fw-bold" id="ten" name="ten" 
                               value="<?= htmlspecialchars($_POST['ten'] ?? ($sanPham['ten'] ?? '')) ?>" 
                               placeholder="Ví dụ: Sầu riêng Ri6, Xoài cát..." required>
                        <small id="tenGoiY" class="text-muted d-block mt-1" style="font-size: 0.82rem;"></small>
                    </div>

                    <!-- 3. SẢN LƯỢNG KHO & ĐƠN VỊ TÍNH (RÀO THEO NGÀNH) -->
                    <div class="p-3 mb-3 rounded-2 bg-light border">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-bold text-dark fs-6">
                                <i class="bi bi-boxes text-success me-1"></i> Nhập sản lượng hiện có
                            </div>
                            <span class="badge bg-white text-muted border small fw-normal" id="badgeNhomDonViKho">
                                Đơn vị đề xuất theo ngành
                            </span>
                        </div>
                        <div class="row g-3">
                            <div class="col-sm-7">
                                <label for="san_luong" class="form-label">Sản lượng hiện có <span class="text-danger">*</span></label>
                                <input type="number" step="any" min="0.01" name="san_luong" id="san_luong" 
                                       class="form-control fw-bold" 
                                       value="<?= htmlspecialchars($_POST['san_luong'] ?? ($sanPham['san_luong'] ?? '')) ?>" 
                                       placeholder="Ví dụ: 450" required>
                            </div>
                            <div class="col-sm-5">
                                <label for="don_vi_san_luong" class="form-label">Đơn vị sản lượng <span class="text-danger">*</span></label>
                                <select name="don_vi_san_luong" id="don_vi_san_luong" class="form-select fw-bold" required>
                                    <?php 
                                        $dvKho = $_POST['don_vi_san_luong'] ?? ($sanPham['don_vi_san_luong'] ?? 'kg');
                                    ?>
                                    <?php foreach (DON_VI as $maDv => $tenDv): ?>
                                        <option value="<?= $maDv ?>" <?= $dvKho === $maDv ? 'selected' : '' ?>><?= $tenDv ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 4. GIÁ BÁN NIÊM YẾT (RÀO THEO NGÀNH) -->
                    <div class="p-3 mb-3 rounded-2" style="background: #f0fdf4; border: 2px solid #a7f3d0;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-bold text-success fs-6">
                                <i class="bi bi-cash-stack me-1"></i> Giá bán niêm yết
                            </div>
                            <span class="badge bg-white text-success border border-success-subtle small fw-normal" id="badgeNhomDonViGia">
                                Đơn vị tính giá theo ngành
                            </span>
                        </div>

                        <!-- Ô TICK LIÊN HỆ BÁO GIÁ -->
                        <div class="form-check form-check-inline mb-2">
                            <?php 
                                $laLienHe = false;
                                if ($laChinhSua && (!isset($sanPham['gia']) || $sanPham['gia'] === null || $sanPham['gia'] <= 0)) {
                                    $laLienHe = true;
                                }
                                if (isset($_POST['la_lien_he']) && $_POST['la_lien_he'] == '1') {
                                    $laLienHe = true;
                                }
                            ?>
                            <input class="form-check-input" type="checkbox" name="la_lien_he" id="checkLienHe" value="1" 
                                   <?= $laLienHe ? 'checked' : '' ?> onchange="chuyenCheDoGia(this.checked)">
                            <label class="form-check-label fw-bold text-dark fs-6" for="checkLienHe">
                                Liên hệ báo giá (không niêm yết giá cố định)
                            </label>
                        </div>

                        <div class="row g-3 align-items-center">
                            <div class="col-sm-7">
                                <label for="inputGia" class="form-label">Giá tiền (đ)</label>
                                <input type="number" step="any" min="0" name="gia" id="inputGia" 
                                       class="form-control form-control-lg fw-bold text-success" 
                                       value="<?= htmlspecialchars($_POST['gia'] ?? ($sanPham['gia'] ?? '')) ?>" 
                                       placeholder="Ví dụ: 35000" 
                                       <?= $laLienHe ? 'disabled' : '' ?>
                                       oninput="capNhatTienPreview(this, 'previewGiaBan')">
                                <!-- HIỂN THỊ PHÂN CHIA HÀNG NGHÌN TIẾNG VIỆT TO RÕ ĐỂ KHÔNG NHẦM SỐ 0 -->
                                <div id="previewGiaBan" class="mt-2">
                                    <?php 
                                        $giaHienTai = $_POST['gia'] ?? ($sanPham['gia'] ?? null);
                                    ?>
                                    <span class="currency-preview-badge fs-6"><?= dinh_dang_tien($giaHienTai) ?></span>
                                </div>
                            </div>

                            <div class="col-sm-5">
                                <label for="don_vi_gia" class="form-label">Đơn vị tính giá <span class="text-danger">*</span></label>
                                <select name="don_vi_gia" id="don_vi_gia" class="form-select form-select-lg fw-bold" required>
                                    <?php 
                                        $dvGia = $_POST['don_vi_gia'] ?? ($sanPham['don_vi_gia'] ?? 'kg');
                                    ?>
                                    <?php foreach (DON_VI as $maDv => $tenDv): ?>
                                        <option value="<?= $maDv ?>" <?= $dvGia === $maDv ? 'selected' : '' ?>>Mỗi <?= $maDv ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 4. TÌNH TRẠNG & NGÀY THU HOẠCH -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="tinh_trang" class="form-label">Tình trạng <span class="text-danger">*</span></label>
                            <?php 
                                $ttHienTai = $_POST['tinh_trang'] ?? ($sanPham['tinh_trang'] ?? 'con_hang');
                            ?>
                            <select name="tinh_trang" id="tinh_trang" class="form-select fw-bold" onchange="chuyenTinhTrang(this.value)">
                                <option value="con_hang" <?= $ttHienTai === 'con_hang' ? 'selected' : '' ?>>Còn hàng</option>
                                <option value="sap_thu_hoach" <?= $ttHienTai === 'sap_thu_hoach' ? 'selected' : '' ?>>Sắp thu hoạch</option>
                                <option value="het_hang" <?= $ttHienTai === 'het_hang' ? 'selected' : '' ?>>Tạm hết hàng</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="khoiNgayThuHoach" style="<?= $ttHienTai === 'sap_thu_hoach' ? '' : 'display: none;' ?>">
                            <label for="ngay_thu_hoach" class="form-label">Ngày dự kiến thu hoạch</label>
                            <input type="date" name="ngay_thu_hoach" id="ngay_thu_hoach" class="form-control fw-bold" 
                                   value="<?= htmlspecialchars($_POST['ngay_thu_hoach'] ?? ($sanPham['ngay_thu_hoach'] ?? '')) ?>" 
                                   <?= $ttHienTai === 'sap_thu_hoach' ? '' : 'disabled' ?>>
                            <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">
                                <i class="bi bi-info-circle me-1 text-primary"></i>Hệ thống sẽ tự động đếm ngược số ngày và chuyển sang <strong>"Còn hàng"</strong> khi đến ngày thu hoạch. Nông hộ có thể vào cập nhật lại ngày bất kỳ lúc nào.
                            </small>
                        </div>
                    </div>

                    <!-- 5. CHỨNG NHẬN CHẤT LƯỢNG TO RÕ -->
                    <div class="mb-3">
                        <label class="form-label d-block fw-bold text-dark">Chứng nhận (Chọn nếu có):</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php 
                                $iconMap = [
                                    'huu_co'    => 'bi-flower1',
                                    'vietgap'   => 'bi-patch-check-fill',
                                    'ocop'      => 'bi-award-fill',
                                    'globalgap' => 'bi-globe-americas'
                                ];
                            ?>
                            <?php foreach (CHUNG_NHAN as $maCn => $tenCn): ?>
                                <?php 
                                    $daCheck = in_array($maCn, $chungNhanDaChon);
                                    $iconCn = $iconMap[$maCn] ?? 'bi-patch-check';
                                ?>
                                <input type="checkbox" class="btn-check" name="chung_nhan[]" id="cn_<?= $maCn ?>" value="<?= $maCn ?>" <?= $daCheck ? 'checked' : '' ?>>
                                <label class="btn btn-outline-success px-3 py-2 rounded-pill fw-bold" for="cn_<?= $maCn ?>">
                                    <i class="bi <?= $iconCn ?> me-1.5"></i><?= $tenCn ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- 6. HÌNH ẢNH NÔNG SẢN (HỖ TRỢ TỐI ĐA 5 ẢNH & TỰ ĐỘNG NÉN CANVAS WEBP/JPEG <= 400x400px) -->
                    <div class="mb-4 p-3 bg-light rounded-4 border">
                        <label class="form-label fw-bold text-dark d-flex align-items-center justify-content-between mb-2 flex-wrap gap-1">
                            <span><i class="bi bi-images text-success me-1.5"></i>Hình ảnh sản phẩm (Tối đa 5 ảnh)</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold" style="font-size: 0.72rem;">
                                <i class="bi bi-lightning-charge-fill text-warning me-0.5"></i>Tự động nén &le; 400x400px (WebP)
                            </span>
                        </label>
                        
                        <?php 
                            $dsAnhHienTai = $laChinhSua ? lay_danh_sach_anh_sp($sanPham) : [];
                            // Lọc bỏ ảnh mặc định hệ thống
                            $dsAnhHienTai = array_filter($dsAnhHienTai, function($a) {
                                return !empty($a) && (duong_dan_anh_sp($a) !== 'assets/images/nong_san_mac_dinh.svg');
                            });
                            $coAnhThat = !empty($dsAnhHienTai);
                        ?>

                        <?php if ($laChinhSua && $coAnhThat): ?>
                            <!-- Thẻ hiển thị các ảnh thực tế hiện tại của nông sản -->
                            <div class="mb-3" id="khoiAnhCuWrap">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold small text-dark"><i class="bi bi-check-circle-fill text-success me-1"></i>Ảnh thực tế đang sử dụng (<span id="demAnhCuHienTai"><?= count($dsAnhHienTai) ?></span>/5 ảnh):</span>
                                    <span class="text-muted small" style="font-size: 0.75rem;">Nhấp nút xóa nếu muốn bỏ bớt ảnh</span>
                                </div>
                                <div class="row g-2" id="gridAnhCu">
                                    <?php foreach ($dsAnhHienTai as $idxCu => $anhCu): ?>
                                        <?php $urlCu = duong_dan_anh_sp($anhCu); ?>
                                        <div class="col-6 col-sm-4 col-md-3 item-anh-cu" id="itemAnhCu_<?= $idxCu ?>">
                                            <div class="card h-100 border rounded-3 position-relative overflow-hidden shadow-sm">
                                                <img src="<?= $urlCu ?>" class="card-img-top" style="height: 90px; object-fit: cover;" alt="Ảnh nông sản" onerror="this.src='assets/images/nong_san_mac_dinh.svg'">
                                                <input type="hidden" name="anh_cu_giu_lai[]" value="<?= htmlspecialchars($anhCu) ?>" class="input-anh-cu-val">
                                                <div class="card-body p-1.5 d-flex align-items-center justify-content-between bg-white">
                                                    <span class="badge bg-light text-dark border px-1.5 py-0.5" style="font-size: 0.68rem;">
                                                        <?= $idxCu === 0 ? 'Ảnh chính' : 'Ảnh ' . ($idxCu + 1) ?>
                                                    </span>
                                                    <button type="button" class="btn btn-outline-danger btn-sm py-0 px-1.5 rounded-pill" style="font-size: 0.72rem;" onclick="xoaAnhCu('itemAnhCu_<?= $idxCu ?>')" title="Gỡ bỏ ảnh này">
                                                        <i class="bi bi-trash3-fill"></i> Xóa
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php elseif ($laChinhSua): ?>
                            <!-- Thông báo ảnh minh họa mặc định -->
                            <div class="alert alert-info py-2 px-3 small mb-3 border-0 bg-info bg-opacity-10 text-primary rounded-3">
                                <i class="bi bi-info-circle-fill me-1.5"></i>Nông sản này hiện đang hiển thị bằng <strong>ảnh minh họa mặc định</strong>. Bạn hãy chọn ảnh thực tế ở ô bên dưới để tải lên (tối đa 5 ảnh).
                            </div>
                        <?php endif; ?>

                        <!-- KHU VỰC KÉO THẢ VÀ CHỌN TỆP CHUẨN LUCIAN #032 (TỰ ĐỘNG NÉN CANVAS) -->
                        <div class="ux u032-wrapper" id="u032UploadWrapper">
                            <button class="u032-zone" type="button" id="u032DropZone" aria-label="Bấm hoặc kéo thả ảnh vào đây để chọn tệp">
                                <svg class="u032-dash" aria-hidden="true"><rect width="100%" height="100%" rx="22"/></svg>
                                <span class="u032-orb" aria-hidden="true">
                                    <svg viewBox="0 0 28 28">
                                        <path d="M8.5 20.5H7a5 5 0 0 1-.6-10 7 7 0 0 1 13.4-1.6A5.5 5.5 0 0 1 21 20.5h-1.5M14 13v10M10.5 16.5L14 13l3.5 3.5"/>
                                    </svg>
                                </span>
                                <b class="u032-title" id="u032TitleText">Kéo thả ảnh vào đây hoặc <em>chọn từ điện thoại/máy tính</em></b>
                                <small class="u032-desc" id="u032DescText">Tự động nén chuẩn WebP &le; 400x400px siêu nhẹ &bull; Chọn tối đa 5 ảnh &bull; Ảnh chụp điện thoại dung lượng lớn đều được tối ưu tức thì</small>
                            </button>

                            <!-- INPUT FILE NATIVE MULTIPLE ẨN ĐỂ FORM PHP NHẬN $_FILES['anh'] -->
                            <input class="u032-file" type="file" id="anh" name="anh[]" multiple accept="image/*" tabindex="-1" aria-hidden="true">

                            <!-- BỘ ĐẾM SỐ LƯỢNG ẢNH & NÚT HỦY -->
                            <div class="d-flex align-items-center justify-content-between mt-2 px-1">
                                <span class="small text-muted" id="nhacNhoSoLuongAnh">
                                    Có thể thêm tối đa: <strong class="text-success" id="soLuongCoTheThem">5</strong> ảnh
                                </span>
                                <button type="button" class="btn btn-link btn-sm text-danger p-0 text-decoration-none d-none" id="btnXoaTatCaAnhMoi" onclick="xoaTatCaAnhMoi()">
                                    <i class="bi bi-x-circle me-1"></i>Hủy tất cả ảnh mới
                                </button>
                            </div>

                            <!-- DANH SÁCH THẺ XEM TRƯỚC CÁC ẢNH MỚI VỪA CHỌN (KÈM THÔNG TIN ĐÃ NÉN) -->
                            <ul class="u032-list mt-2" id="u032FileList" aria-live="polite"></ul>
                        </div>
                    </div>

                    <!-- NÚT LƯU FORM ĐỒNG BỘ -->
                    <button type="submit" class="btn btn-clean btn-lg w-100 py-2.5 fw-bold fs-6 rounded-pill shadow">
                        <i class="bi bi-check2-circle me-1.5 fs-5"></i>
                        <?= $laChinhSua ? 'Lưu Thay Đổi Nông Sản' : 'Đăng Bán Nông Sản Lên Sàn' ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// ==============================================================================
// CẤU HÌNH VÀ DỮ LIỆU ĐƠN VỊ TÍNH THEO NGÀNH NÔNG SẢN
// ==============================================================================
const TAT_CA_DON_VI = <?= json_encode(DON_VI, JSON_UNESCAPED_UNICODE) ?>;
const CAU_HINH_NHOM = <?= json_encode(DANH_MUC_DON_VI, JSON_UNESCAPED_UNICODE) ?>;
let donViKhoHienTai = <?= json_encode($_POST['don_vi_san_luong'] ?? ($sanPham['don_vi_san_luong'] ?? '')) ?>;
let donViGiaHienTai = <?= json_encode($_POST['don_vi_gia'] ?? ($sanPham['don_vi_gia'] ?? '')) ?>;

function boDauTiengVietJS(str) {
    if (!str) return '';
    str = str.toLowerCase();
    str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
    str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
    str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
    str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
    str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
    str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
    str = str.replace(/đ/g, "d");
    return str.trim();
}

function timNhomTheoTen(tenDanhMuc) {
    if (!tenDanhMuc) return null;
    const tenKD = boDauTiengVietJS(tenDanhMuc);
    for (const key in CAU_HINH_NHOM) {
        const nhom = CAU_HINH_NHOM[key];
        for (const tk of nhom.tu_khoa) {
            const tkKD = boDauTiengVietJS(tk);
            if (tenKD.includes(tkKD)) {
                return nhom;
            }
        }
    }
    return null;
}

function renderOptions(selectEl, dsKeys, selectedKey, laGia = false) {
    if (!selectEl) return;
    const giaTriCu = selectEl.value || selectedKey;
    selectEl.innerHTML = '';

    let daCoSelected = false;

    // Chỉ hiển thị các đơn vị phổ biến thuộc ngành nông sản được chọn
    dsKeys.forEach(k => {
        if (TAT_CA_DON_VI[k]) {
            const opt = document.createElement('option');
            opt.value = k;
            opt.textContent = laGia ? ('Mỗi ' + k) : TAT_CA_DON_VI[k];
            if (k === giaTriCu) {
                opt.selected = true;
                daCoSelected = true;
            }
            selectEl.appendChild(opt);
        }
    });

    // Nếu đang chỉnh sửa sản phẩm cũ mà đơn vị cũ khác nhóm gợi ý, giữ lại để không mất dữ liệu của nông hộ
    if (!daCoSelected && giaTriCu && TAT_CA_DON_VI[giaTriCu]) {
        const opt = document.createElement('option');
        opt.value = giaTriCu;
        opt.textContent = laGia ? ('Mỗi ' + giaTriCu) : TAT_CA_DON_VI[giaTriCu];
        opt.selected = true;
        selectEl.appendChild(opt);
        daCoSelected = true;
    }

    // Nếu chưa có mục nào được chọn, chọn ngay đơn vị đầu tiên
    if (!daCoSelected && selectEl.firstElementChild) {
        selectEl.firstElementChild.selected = true;
    }
}

function capNhatDonViTheoDanhMuc(tenDanhMuc, laThayDoiTuNguoiDung = false) {
    const nhom = timNhomTheoTen(tenDanhMuc);
    const selectKho = document.getElementById('don_vi_san_luong');
    const selectGia = document.getElementById('don_vi_gia');
    const inputTen = document.getElementById('ten');
    const badgeKho = document.getElementById('badgeNhomDonViKho');
    const badgeGia = document.getElementById('badgeNhomDonViGia');
    const hintTen = document.getElementById('tenGoiY');

    let dsKho = [];
    let dsGia = [];

    if (nhom) {
        dsKho = nhom.don_vi_kho;
        dsGia = nhom.don_vi_gia;
        if (inputTen) {
            inputTen.placeholder = nhom.placeholder;
        }
        if (hintTen) {
            hintTen.innerHTML = '<i class="bi bi-lightbulb-fill text-warning me-1"></i>Gợi ý: ' + nhom.placeholder;
        }
        if (badgeKho) badgeKho.textContent = 'Gợi ý cho: ' + nhom.ten;
        if (badgeGia) badgeGia.textContent = 'Gợi ý cho: ' + nhom.ten;

        // Nếu người dùng chủ động đổi ngành, cập nhật lại đơn vị mặc định
        if (laThayDoiTuNguoiDung) {
            donViKhoHienTai = nhom.don_vi_kho_mac_dinh;
            donViGiaHienTai = nhom.don_vi_gia_mac_dinh;
        }
    } else {
        // Danh mục mới hoặc chưa chọn: hiển thị toàn bộ đơn vị
        dsKho = Object.keys(TAT_CA_DON_VI);
        dsGia = Object.keys(TAT_CA_DON_VI);
        if (inputTen) {
            inputTen.placeholder = 'Ví dụ: Sầu riêng Ri6, Xoài cát, Dưa hấu...';
        }
        if (hintTen) hintTen.innerHTML = '';
        if (badgeKho) badgeKho.textContent = 'Tất cả đơn vị';
        if (badgeGia) badgeGia.textContent = 'Tất cả đơn vị';
    }

    renderOptions(selectKho, dsKho, donViKhoHienTai, false);
    renderOptions(selectGia, dsGia, donViGiaHienTai, true);
}

// Xử lý khi đổi lựa chọn trên dropdown ngành nông sản
function xuLyDoiDanhMuc(selectEl) {
    const khoiMoi = document.getElementById('khoiThemDanhMucMoi');
    const inputMoi = document.getElementById('ten_danh_muc_moi');

    if (selectEl.value === '-1') {
        khoiMoi.classList.remove('d-none');
        if (inputMoi) {
            inputMoi.focus();
            capNhatDonViTheoDanhMuc(inputMoi.value, true);
        }
    } else {
        khoiMoi.classList.add('d-none');
        const opt = selectEl.options[selectEl.selectedIndex];
        const ten = opt ? (opt.getAttribute('data-ten') || opt.textContent) : '';
        capNhatDonViTheoDanhMuc(ten, true);
    }
}

// Xử lý khi nông dân gõ tên ngành mới
function xuLyNhapDanhMucMoi(val) {
    capNhatDonViTheoDanhMuc(val, false);
}

// Khởi chạy khi tài liệu tải xong
document.addEventListener('DOMContentLoaded', function() {
    const selectDm = document.getElementById('ma_danh_muc');
    if (selectDm) {
        if (selectDm.value === '-1') {
            const inputMoi = document.getElementById('ten_danh_muc_moi');
            capNhatDonViTheoDanhMuc(inputMoi ? inputMoi.value : '', false);
        } else if (selectDm.value) {
            const opt = selectDm.options[selectDm.selectedIndex];
            const ten = opt ? (opt.getAttribute('data-ten') || opt.textContent) : '';
            capNhatDonViTheoDanhMuc(ten, false);
        } else {
            capNhatDonViTheoDanhMuc('', false);
        }
    }
});

// ==============================================================================
// XỬ LÝ QUẢN LÝ ẢNH CŨ VÀ TẢI NHIỀU ẢNH NÔNG SẢN (TỐI ĐA 5 ẢNH) CHUẨN LUCIAN UX/UI #032
// TÍCH HỢP TỰ ĐỘNG NÉN CANVAS (CANVAS IMAGE COMPRESSION) <= 400x400px (WEBP/JPEG)
// ==============================================================================
let danhSachTepMoi = []; // Mảng chứa các đối tượng File mới đã nén tối ưu

function demSoAnhCu() {
    return document.querySelectorAll('.item-anh-cu').length;
}

function capNhatSoLuongCoTheThem() {
    const soCu = demSoAnhCu();
    const tongHienTai = soCu + danhSachTepMoi.length;
    const conLai = Math.max(0, 5 - tongHienTai);
    
    const spanDem = document.getElementById('soLuongCoTheThem');
    if (spanDem) spanDem.textContent = conLai;

    const spanDemCu = document.getElementById('demAnhCuHienTai');
    if (spanDemCu) spanDemCu.textContent = soCu;

    const btnHuyTatCa = document.getElementById('btnXoaTatCaAnhMoi');
    if (btnHuyTatCa) {
        if (danhSachTepMoi.length > 0) {
            btnHuyTatCa.classList.remove('d-none');
        } else {
            btnHuyTatCa.classList.add('d-none');
        }
    }
}

// Xóa 1 ảnh cũ đang có của sản phẩm
function xoaAnhCu(itemId) {
    const el = document.getElementById(itemId);
    if (!el) return;
    
    // Hiệu ứng mờ dần rồi xóa khỏi DOM
    el.style.transition = 'all 0.25s ease';
    el.style.opacity = '0';
    el.style.transform = 'scale(0.85)';
    setTimeout(() => {
        el.remove();
        capNhatSoLuongCoTheThem();
        
        // Nếu không còn ảnh cũ nào thì ẩn khối ảnh cũ
        const grid = document.getElementById('gridAnhCu');
        if (grid && grid.children.length === 0) {
            const wrap = document.getElementById('khoiAnhCuWrap');
            if (wrap) wrap.remove();
        }

        if (typeof hienThiToast === 'function') {
            hienThiToast('info', 'Đã gỡ ảnh', 'Ảnh cũ đã được gỡ. Nhớ bấm "Lưu thay đổi" để hoàn tất.');
        }
    }, 250);
}

// Hủy toàn bộ ảnh mới vừa chọn
function xoaTatCaAnhMoi() {
    danhSachTepMoi = [];
    const fileInput = document.getElementById('anh');
    if (fileInput) fileInput.value = '';
    const fileList = document.getElementById('u032FileList');
    if (fileList) fileList.innerHTML = '';
    capNhatSoLuongCoTheThem();
}

/**
 * Thuật toán nén ảnh tự động bằng HTML5 Canvas:
 * - Kích thước tối đa: 400x400px (tự động giữ đúng tỉ lệ khung hình gốc)
 * - Định dạng: Ưu tiên WebP thế hệ mới (chất lượng 85%), fallback sang JPEG nếu máy không hỗ trợ
 * - Giảm dung lượng từ 5MB-10MB (ảnh chụp điện thoại) xuống chỉ còn 20KB-50KB!
 */
function nenAnhBangCanvas(fileGoc, maxW = 400, maxH = 400, quality = 0.85) {
    return new Promise((resolve) => {
        // Nếu không phải ảnh thì giữ nguyên
        if (!fileGoc.type.startsWith('image/')) {
            resolve(fileGoc);
            return;
        }

        const img = new Image();
        const urlTemp = URL.createObjectURL(fileGoc);

        img.onload = function() {
            URL.revokeObjectURL(urlTemp);

            let w = img.width;
            let h = img.height;

            // Tính toán kích thước mới: tối đa 400x400px mà vẫn giữ nguyên tỉ lệ khung hình
            if (w > maxW || h > maxH) {
                if (w / h > maxW / maxH) {
                    h = Math.round((h * maxW) / w);
                    w = maxW;
                } else {
                    w = Math.round((w * maxH) / h);
                    h = maxH;
                }
            }

            const canvas = document.createElement('canvas');
            canvas.width = w;
            canvas.height = h;
            const ctx = canvas.getContext('2d');

            // Bật khử răng cưa và làm mịn ảnh chất lượng cao
            ctx.imageSmoothingEnabled = true;
            ctx.imageSmoothingQuality = 'high';

            // Kiểm tra hỗ trợ định dạng WebP trên Canvas
            let hoTroWebp = false;
            try {
                hoTroWebp = canvas.toDataURL('image/webp').indexOf('data:image/webp') === 0;
            } catch (e) {
                hoTroWebp = false;
            }

            const mimeXuat = hoTroWebp ? 'image/webp' : 'image/jpeg';
            const duoiXuat = hoTroWebp ? 'webp' : 'jpg';

            // Nếu xuất JPEG thì tô nền trắng để tránh ảnh PNG trong suốt bị nền đen
            if (!hoTroWebp) {
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, w, h);
            }

            // Vẽ ảnh đã thu nhỏ lên Canvas
            ctx.drawImage(img, 0, 0, w, h);

            // Xuất ra Blob nhị phân nén
            canvas.toBlob(function(blob) {
                if (!blob) {
                    resolve(fileGoc);
                    return;
                }

                const tenKhongDuoi = fileGoc.name.replace(/\.[^/.]+$/, '');
                const tenMoi = `${tenKhongDuoi}.${duoiXuat}`;

                const fileNen = new File([blob], tenMoi, {
                    type: mimeXuat,
                    lastModified: Date.now()
                });

                // Đính kèm thông số nén để hiển thị thông tin trực quan cho người dùng
                fileNen.dungLuongGoc = fileGoc.size;
                fileNen.kichThuocMoi = `${w}x${h}px`;
                fileNen.dinhDangMoi = duoiXuat.toUpperCase();

                resolve(fileNen);
            }, mimeXuat, quality);
        };

        img.onerror = function() {
            URL.revokeObjectURL(urlTemp);
            resolve(fileGoc); // Fallback nếu không nén được
        };

        img.src = urlTemp;
    });
}

(function khoiTaoLucianMultiUpload() {
    const wrapper = document.getElementById('u032UploadWrapper');
    const dropZone = document.getElementById('u032DropZone');
    const fileInput = document.getElementById('anh');
    const fileList = document.getElementById('u032FileList');
    const titleText = document.getElementById('u032TitleText');
    const descText = document.getElementById('u032DescText');

    if (!wrapper || !dropZone || !fileInput || !fileList) return;

    function dinhDangDungLuong(bytes) {
        if (!bytes) return '0 KB';
        if (bytes >= 1048576) {
            return (bytes / 1048576).toFixed(1) + ' MB';
        }
        return Math.max(1, Math.round(bytes / 1024)) + ' KB';
    }

    function dongBoVaoInputFile() {
        try {
            const dt = new DataTransfer();
            danhSachTepMoi.forEach(f => dt.items.add(f));
            fileInput.files = dt.files;
        } catch (e) {
            // Fallback nếu trình duyệt không hỗ trợ DataTransfer
        }
    }

    function veLaiDanhSachAnhMoi() {
        fileList.innerHTML = '';
        const soCu = demSoAnhCu();

        danhSachTepMoi.forEach((file, index) => {
            const urlPreview = URL.createObjectURL(file);
            const laAnhChinh = (soCu === 0 && index === 0);

            // Hiển thị kích thước nén và so sánh với dung lượng gốc
            let thongTinNen = `<span class="text-success fw-bold"><i class="bi bi-file-earmark-check-fill me-0.5"></i>${dinhDangDungLuong(file.size)} (${file.dinhDangMoi || 'WebP'} &bull; ${file.kichThuocMoi || '400px'})</span>`;
            if (file.dungLuongGoc && file.dungLuongGoc > file.size) {
                thongTinNen += ` <span class="text-muted ms-1" style="font-size: 0.72rem;"><del>${dinhDangDungLuong(file.dungLuongGoc)}</del></span>`;
            }

            const li = document.createElement('li');
            li.className = 'u032-row done';
            li.innerHTML = `
                <img src="${urlPreview}" alt="Xem trước" class="u032-thumb">
                <div class="u032-meta">
                    <b title="${file.name}">${file.name}</b>
                    <small class="d-flex align-items-center flex-wrap gap-1 mt-0.5">
                        ${thongTinNen}
                        <span class="badge ${laAnhChinh ? 'bg-success text-white' : 'bg-light text-dark border'} px-1.5 py-0.5 ms-1" style="font-size: 0.68rem;">
                            ${laAnhChinh ? 'Ảnh chính' : 'Ảnh mới ' + (index + 1)}
                        </span>
                    </small>
                </div>
                <button type="button" class="u032-remove-btn" title="Hủy ảnh này">
                    <i class="bi bi-trash3-fill"></i>
                </button>
                <span class="u032-bar" style="--p: 1;"></span>
            `;

            // Bắt sự kiện xóa từng ảnh
            li.querySelector('.u032-remove-btn').addEventListener('click', function (e) {
                e.stopPropagation();
                danhSachTepMoi.splice(index, 1);
                dongBoVaoInputFile();
                veLaiDanhSachAnhMoi();
                capNhatSoLuongCoTheThem();
            });

            fileList.appendChild(li);
        });

        capNhatSoLuongCoTheThem();
    }

    async function xuLyCacTepDuocChon(danhSachTep) {
        if (!danhSachTep || !danhSachTep.length) return;

        const soCu = demSoAnhCu();
        let choTrong = 5 - soCu - danhSachTepMoi.length;

        if (choTrong <= 0) {
            const thongBao = `Sản phẩm đã đạt giới hạn tối đa 5 ảnh (${soCu} ảnh cũ + ${danhSachTepMoi.length} ảnh mới). Vui lòng gỡ bớt ảnh trước khi thêm tiếp.`;
            if (typeof hienThiToast === 'function') {
                hienThiToast('warning', 'Đã đủ số lượng ảnh', thongBao);
            } else {
                alert(thongBao);
            }
            return;
        }

        const cacTepArr = Array.from(danhSachTep);
        let daBoQuaDoVuot = false;
        let daBoQuaDoLoi = false;
        let soLuongVuaNen = 0;

        // Hiển thị trạng thái đang nén ảnh trên Dropzone
        if (titleText) {
            titleText.innerHTML = `<span class="spinner-border spinner-border-sm text-success me-1.5" role="status"></span> Đang tự động nén tối ưu ảnh về chuẩn WebP &le; 400x400px...`;
        }

        for (const file of cacTepArr) {
            if (choTrong <= 0) {
                daBoQuaDoVuot = true;
                break;
            }

            // Kiểm tra định dạng tệp có phải hình ảnh không
            if (!file.type.startsWith('image/')) {
                daBoQuaDoLoi = true;
                continue;
            }

            // Kiểm tra tránh chọn trùng tên và cùng kích thước
            const daCo = danhSachTepMoi.some(f => f.name === file.name && f.size === file.size);
            if (!daCo) {
                try {
                    // NÉN ẢNH CANVAS TỰ ĐỘNG VỀ CHUẨN WEBP/JPEG <= 400x400px
                    const fileNen = await nenAnhBangCanvas(file, 400, 400, 0.85);
                    danhSachTepMoi.push(fileNen);
                    choTrong--;
                    soLuongVuaNen++;
                } catch (err) {
                    console.error('Lỗi khi nén ảnh:', err);
                    danhSachTepMoi.push(file);
                    choTrong--;
                }
            }
        }

        // Khôi phục tiêu đề gốc của Dropzone
        if (titleText) {
            titleText.innerHTML = `Kéo thả ảnh vào đây hoặc <em>chọn từ điện thoại/máy tính</em>`;
        }

        dongBoVaoInputFile();
        veLaiDanhSachAnhMoi();

        if (soLuongVuaNen > 0 && typeof hienThiToast === 'function') {
            hienThiToast('success', 'Nén ảnh siêu tốc', `Đã nén tối ưu ${soLuongVuaNen} ảnh về chuẩn WebP (&le; 400x400px) siêu nhẹ, tiết kiệm 95% bộ nhớ!`);
        }

        if (daBoQuaDoVuot && typeof hienThiToast === 'function') {
            hienThiToast('info', 'Giới hạn tối đa 5 ảnh', 'Chỉ lấy đủ số lượng ảnh còn trống (tối đa 5 ảnh).');
        } else if (daBoQuaDoLoi && typeof hienThiToast === 'function') {
            hienThiToast('warning', 'Tệp không hợp lệ', 'Một số tệp bị bỏ qua do không phải định dạng hình ảnh.');
        }
    }

    // Mở hộp thoại chọn tệp khi bấm vào khu vực dropzone
    dropZone.addEventListener('click', function () {
        fileInput.click();
    });

    // Bắt sự kiện chọn tệp qua nút browse
    fileInput.addEventListener('change', function () {
        if (fileInput.files && fileInput.files.length > 0) {
            xuLyCacTepDuocChon(fileInput.files);
        }
    });

    // Xử lý kéo thả (Drag and Drop)
    let dragDepth = 0;

    dropZone.addEventListener('dragenter', function (e) {
        e.preventDefault();
        e.stopPropagation();
        dragDepth++;
        wrapper.classList.add('is-over');
    });

    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        e.stopPropagation();
    });

    dropZone.addEventListener('dragleave', function (e) {
        e.preventDefault();
        e.stopPropagation();
        dragDepth--;
        if (dragDepth <= 0) {
            dragDepth = 0;
            wrapper.classList.remove('is-over');
        }
    });

    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        dragDepth = 0;
        wrapper.classList.remove('is-over');

        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            xuLyCacTepDuocChon(e.dataTransfer.files);
        }
    });

    // Khởi tạo ban đầu
    capNhatSoLuongCoTheThem();
})();

// Chuyển đổi trạng thái ô nhập giá khi tick "Liên hệ báo giá"
function chuyenCheDoGia(laLienHe) {
    const inputGia = document.getElementById('inputGia');
    const previewEl = document.getElementById('previewGiaBan');

    if (laLienHe) {
        inputGia.disabled = true;
        inputGia.value = '';
        previewEl.innerHTML = '<span class="currency-preview-badge bg-warning text-dark fs-6">Liên hệ báo giá</span>';
    } else {
        inputGia.disabled = false;
        capNhatTienPreview(inputGia, 'previewGiaBan');
    }
}

// Bật tắt ngày thu hoạch dự kiến
function chuyenTinhTrang(giaTri) {
    const khoi = document.getElementById('khoiNgayThuHoach');
    const inputNgay = document.getElementById('ngay_thu_hoach');
    if (giaTri === 'sap_thu_hoach') {
        khoi.style.display = 'block';
        if (inputNgay) {
            inputNgay.disabled = false;
        }
    } else {
        khoi.style.display = 'none';
        if (inputNgay) {
            inputNgay.disabled = true;
        }
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

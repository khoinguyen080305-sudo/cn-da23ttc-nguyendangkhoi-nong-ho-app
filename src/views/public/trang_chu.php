<?php
// views/public/trang_chu.php
$dsDanhMuc = $dsDanhMuc ?? [];
$dsTinhThanh = $dsTinhThanh ?? [];
$dsNongHo = $dsNongHo ?? [];
$dsSanPham = $dsSanPham ?? [];
$tongSoSanPham = $tongSoSanPham ?? count($dsSanPham);
$tongSoTrang = $tongSoTrang ?? 1;
$trangHienTai = $trangHienTai ?? 1;
$tinhDangChon = $_GET['tinh'] ?? '';
$danhMucDangChon = (int) ($_GET['danh_muc'] ?? 0);
$nongHoDangChon = (int) ($_GET['nong_ho'] ?? 0);
$tinhTrangDangChon = trim($_GET['tinh_trang'] ?? ($_GET['trang_thai'] ?? ''));
$tuKhoa = trim($_GET['tu_khoa'] ?? '');
$sapXepDangChon = trim($_GET['sap_xep'] ?? '');

$tenNongHoDangChon = '';
$tinhNongHoDangChon = '';
if ($nongHoDangChon > 0) {
    foreach ($dsNongHo as $nh) {
        if ($nh['ma_nguoi_dung'] == $nongHoDangChon) {
            $tenNongHoDangChon = $nh['ho_ten'];
            $tinhNongHoDangChon = $nh['tinh'] ?? '';
            break;
        }
    }
}

require_once __DIR__ . '/../layouts/header.php';
$nguoiDung = nguoi_dung_hien_tai();
$coBoLoc = !empty($tuKhoa) || !empty($tinhDangChon) || $danhMucDangChon > 0 || $nongHoDangChon > 0 || !empty($tinhTrangDangChon) || !empty($sapXepDangChon);

// Đếm số lượng tiêu chí lọc đang được chọn
$soLuongLocNangCao = ($nongHoDangChon > 0 ? 1 : 0) + (!empty($tinhDangChon) ? 1 : 0) + (!empty($tinhTrangDangChon) ? 1 : 0);
$coLocNangCao = $soLuongLocNangCao > 0;
?>

<div class="container-fluid px-2 px-sm-3 px-lg-4">
    <!-- 1. BANNER -->
    <div class="nh-app-hero mb-3 mb-md-4">
        <div class="nh-hero-card">
            <div class="nh-hero-inner">
                <!-- CỘT NỘI DUNG CHỮ BÊN TRÁI -->
                <div class="nh-hero-text-block">
                    <!-- Hàng trên: Tag Nông Sản -->
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2 w-100 flex-wrap">
                        <div class="nh-hero-tag-wrap m-0">
                            <span class="nh-hero-tag">
                                <i class="bi bi-patch-check-fill"></i> Nông Sản Việt Nam
                            </span>
                            <span class="nh-hero-sunrays">
                                <span></span><span></span><span></span>
                            </span>
                        </div>

                        <!-- Lucian UX/UI Prompt #021 · Day–night sky switch (Gọn 62x28px) -->
                        <div class="nh-theme-toggle-slot">
                            <div class="ux u021" id="lucianThemeToggle">
                                <div class="u021-compact">
                                    <button class="u021-sw" type="button" role="switch" aria-checked="false"
                                        aria-label="Bật tắt giao diện sáng tối" title="Chuyển chế độ Sáng / Tối">
                                        <span class="u021-halo" aria-hidden="true"><i></i><i></i><i></i></span>
                                        <span class="u021-stars" aria-hidden="true">
                                            <i style="left:8px;top:5px"></i>
                                            <i
                                                style="left:16px;top:15px;animation-delay:-.6s;width:1.5px;height:1.5px"></i>
                                            <i style="left:22px;top:7px;animation-delay:-1.2s"></i>
                                            <i
                                                style="left:12px;top:18px;animation-delay:-1.8s;width:1.5px;height:1.5px"></i>
                                            <i
                                                style="left:28px;top:17px;animation-delay:-.9s;width:1.2px;height:1.2px"></i>
                                            <i
                                                style="left:5px;top:12px;animation-delay:-1.5s;width:1.2px;height:1.2px"></i>
                                        </span>
                                        <span class="u021-clouds" aria-hidden="true"><i></i><i></i></span>
                                        <span class="u021-knob" aria-hidden="true"><span
                                                class="u021-moon"><i></i><i></i><i></i></span></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tiêu đề 2 dòng: Trắng + Vàng Amber -->
                    <h1 class="nh-hero-title">
                        Kết Nối Thị Trường Và<br>
                        <span class="text-gold">Tiêu Thụ Nông Sản</span>
                    </h1>

                    <!-- Phụ đề -->
                    <p class="nh-hero-subtitle">
                        Đưa nông sản từ vườn đến mọi nơi
                    </p>

                    <!-- ============================================================
                         KHU VỰC NÚT THAO TÁC TRONG BANNER
                         - Khách chưa đăng nhập: 2 nút (Đăng ký + Hướng dẫn)
                         - Đã đăng nhập: 1 nút (Hướng dẫn sử dụng) bên trái chia ra cho 2 vai trò nông hộ và người mua
                         ============================================================ -->
                    <?php if (!co_vai_tro('admin')): ?>
                        <?php if (!da_dang_nhap()): ?>
                            <!-- TRƯỜNG HỢP 1: KHÁCH CHƯA ĐĂNG NHẬP -->
                            <div class="nh-hero-actions-unified two-btns">
                                <a href="index.php?trang=dang_ky" class="nh-btn-hero-primary"
                                    title="Đăng ký tài khoản miễn phí">
                                    <i class="bi bi-person-plus-fill"></i>
                                    <span>Đăng Ký Miễn Phí</span>
                                </a>
                                <button type="button" class="nh-btn-hero-secondary" onclick="moModalHuongDan()"
                                    title="Xem hướng dẫn sử dụng nhanh">
                                    <i class="bi bi-book-half"></i>
                                    <span>Hướng Dẫn Sử Dụng</span>
                                </button>
                            </div>
                        <?php else: ?>
                            <!-- TRƯỜNG HỢP 2: ĐÃ ĐĂNG NHẬP NÔNG HỘ HOẶC NGƯỜI MUA -->
                            <div class="nh-hero-actions-unified one-btn">
                                <button type="button" class="nh-btn-hero-secondary" onclick="moModalHuongDan()"
                                    title="Xem cẩm nang hướng dẫn sử dụng">
                                    <i class="bi bi-book-half"></i>
                                    <span>Hướng Dẫn Sử Dụng</span>
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="nh-hero-visual-spacer d-none d-md-block"></div>
            </div>
        </div>
    </div>

    <!-- 2. BỘ LỌC TÌM KIẾM & DANH MỤC-->
    <div class="nh-filter-card mb-3" id="khu_vuc_nong_san">
        <form action="index.php" method="GET" id="formBoLoc">
            <?php if ($danhMucDangChon > 0): ?>
                <input type="hidden" name="danh_muc" value="<?= $danhMucDangChon ?>">
            <?php endif; ?>
            <?php if (!empty($sapXepDangChon)): ?>
                <input type="hidden" name="sap_xep" value="<?= htmlspecialchars($sapXepDangChon) ?>">
            <?php endif; ?>

            <!-- HÀNG 1: Ô TÌM KIẾM GỘP CHUNG NÚT TÌM KIẾM + NÚT LỌC THÊM -->
            <div class="nh-search-bar-row">
                <div class="nh-search-box-integrated flex-grow-1">
                    <i class="bi bi-search nh-search-icon"></i>
                    <input type="text" name="tu_khoa" class="form-control nh-search-input" placeholder="Tìm nông sản..."
                        value="<?= htmlspecialchars($tuKhoa) ?>" autocomplete="off">
                    <?php if (!empty($tuKhoa)): ?>
                        <a href="index.php?<?= http_build_query(array_diff_key($_GET, ['tu_khoa' => ''])) ?>"
                            class="nh-search-clear" title="Xóa từ khóa">&times;</a>
                    <?php endif; ?>
                    <!-- Nút Tìm kiếm -->
                    <button class="nh-btn-search-submit" type="submit" title="Tìm kiếm">
                        <i class="bi bi-search"></i>
                        <span class="d-none d-sm-inline">Tìm kiếm</span>
                    </button>
                </div>

                <!-- NÚT BẬT / TẮT BỘ LỌC -->
                <button class="nh-btn-filter-toggle <?= $coLocNangCao ? 'active' : '' ?>" type="button"
                    data-bs-toggle="collapse" data-bs-target="#boLocNangCao"
                    aria-expanded="<?= $coLocNangCao ? 'true' : 'false' ?>" aria-controls="boLocNangCao"
                    title="<?= $coLocNangCao ? 'Đang bật bộ lọc nâng cao' : 'Mở rộng thêm tiêu chí lọc' ?>">
                    <i class="bi bi-sliders"></i>
                    <span class="d-none d-sm-inline">Lọc thêm</span>
                    <span class="d-inline d-sm-none">Lọc</span>
                    <?php if ($soLuongLocNangCao > 0): ?>
                        <span class="badge bg-success rounded-pill px-1.5 py-0.5"
                            style="font-size: 0.72rem;"><?= $soLuongLocNangCao ?></span>
                    <?php endif; ?>
                    <i class="bi bi-chevron-down nh-chevron-arrow"></i>
                </button>
            </div>

            <!-- HÀNG 2: 3 CỘT LỌC NÂNG CAO
                 Mặc định ẩn hoàn toàn (không có class 'show') trừ khi người dùng đã bấm mở hoặc đang có lựa chọn lọc -->
            <div class="collapse <?= $coLocNangCao ? 'show' : '' ?>" id="boLocNangCao">
                <div class="row g-2 g-md-3 nh-filter-dropdowns-row">
                    <div class="col-12 col-sm-4">
                        <label class="nh-filter-col-label">Nhà vườn</label>
                        <select name="nong_ho" class="form-select nh-filter-select" onchange="this.form.submit()">
                            <option value="0">Tất cả nhà vườn</option>
                            <?php foreach ($dsNongHo as $nh): ?>
                                <option value="<?= $nh['ma_nguoi_dung'] ?>" <?= $nongHoDangChon == $nh['ma_nguoi_dung'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($nh['ho_ten']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-sm-4">
                        <label class="nh-filter-col-label">Tỉnh thành</label>
                        <select name="tinh" class="form-select nh-filter-select" onchange="this.form.submit()">
                            <option value="">Tất cả tỉnh thành</option>
                            <?php foreach ($dsTinhThanh as $t): ?>
                                <option value="<?= htmlspecialchars($t) ?>" <?= $tinhDangChon === $t ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-sm-4">
                        <label class="nh-filter-col-label">Mùa vụ</label>
                        <select name="tinh_trang" class="form-select nh-filter-select" onchange="this.form.submit()">
                            <option value="">Tất cả tình trạng</option>
                            <option value="con_hang" <?= $tinhTrangDangChon === 'con_hang' ? 'selected' : '' ?>>Còn hàng
                            </option>
                            <option value="sap_thu_hoach" <?= $tinhTrangDangChon === 'sap_thu_hoach' ? 'selected' : '' ?>>
                                Sắp thu hoạch</option>
                            <option value="het_hang" <?= $tinhTrangDangChon === 'het_hang' ? 'selected' : '' ?>>Tạm hết
                                hàng</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>

        <!-- HÀNG 3: BỘ CHỌN DANH MỤC VÀ SẮP XẾP -->
        <?php
        // 1. Lọc danh sách danh mục có sản phẩm hoặc đang được chọn
        $dsDanhMucHopLe = [];
        foreach ($dsDanhMuc as $dm) {
            if (($dm['so_san_pham'] ?? 0) > 0 || $danhMucDangChon == $dm['ma_danh_muc']) {
                $dsDanhMucHopLe[] = $dm;
            }
        }

        // 2. Sắp xếp danh mục theo thứ tự số lượng sản phẩm giảm dần
        usort($dsDanhMucHopLe, function ($a, $b) {
            $soA = (int) ($a['so_san_pham'] ?? 0);
            $soB = (int) ($b['so_san_pham'] ?? 0);
            if ($soA === $soB) {
                return strcmp($a['ten_danh_muc'] ?? '', $b['ten_danh_muc'] ?? '');
            }
            return $soB <=> $soA;
        });

        // 3. Lấy thông tin danh mục đang được chọn nếu có
        $tenDanhMucDangChon = '';
        $soSanPhamDanhMucDangChon = 0;
        if ($danhMucDangChon > 0) {
            foreach ($dsDanhMucHopLe as $dm) {
                if ($dm['ma_danh_muc'] == $danhMucDangChon) {
                    $tenDanhMucDangChon = $dm['ten_danh_muc'];
                    $soSanPhamDanhMucDangChon = $dm['so_san_pham'] ?? 0;
                    break;
                }
            }
        }
        // 4. Lấy thông tin sắp xếp giá
        $nhanSapXep = 'Sắp xếp giá';
        $iconSapXep = 'bi-arrow-down-up';
        $laSapXepActive = false;

        if ($sapXepDangChon === 'gia_tang') {
            $nhanSapXep = 'Giá: Thấp → Cao';
            $iconSapXep = 'bi-sort-numeric-down';
            $laSapXepActive = true;
        } elseif ($sapXepDangChon === 'gia_giam') {
            $nhanSapXep = 'Giá: Cao → Thấp';
            $iconSapXep = 'bi-sort-numeric-down-alt';
            $laSapXepActive = true;
        }
        ?>

        <div class="nh-cat-selector-bar" id="khuVucDanhMuc">
            <!-- 1. NÚT MENU "SẮP XẾP GIÁ" KIỂU B (XỔ RA DROPDOWN: THẤP ĐẾN CAO / CAO ĐẾN THẤP) -->
            <div class="nh-cat-dropdown-wrapper" id="sortDropdownWrapper">
                <button type="button" class="nh-cat-dropdown-btn <?= $laSapXepActive ? 'active' : '' ?>"
                    id="btnSortDropdown" onclick="toggleSortDropdown(event)" aria-expanded="false" aria-haspopup="true"
                    title="Bấm để chọn cách sắp xếp giá nông sản">
                    <i class="bi <?= $iconSapXep ?>"></i>
                    <span><?= htmlspecialchars($nhanSapXep) ?></span>
                    <i class="bi bi-chevron-down nh-cat-arrow"></i>
                </button>

                <!-- MENU DROPDOWN SẮP XẾP GIÁ -->
                <div class="nh-cat-dropdown-menu" id="sortDropdownMenu" role="menu">
                    <div class="nh-cat-dropdown-header">
                        <span class="nh-cat-dropdown-title">Sắp Xếp Giá Nông Sản</span>
                    </div>
                    <div class="nh-cat-dropdown-list">
                        <!-- Tùy chọn 1: Mới nhất (Mặc định) -->
                        <?php $dangChonMoiNhat = empty($sapXepDangChon) || $sapXepDangChon === 'moi_nhat'; ?>
                        <a href="index.php?<?= http_build_query(array_merge($_GET, ['sap_xep' => '', 'trang_sp' => 1])) ?>"
                            class="nh-cat-item <?= $dangChonMoiNhat ? 'active' : '' ?>" role="menuitem">
                            <div class="nh-cat-item-left">
                                <span class="nh-cat-item-icon"><i class="bi bi-clock-history"></i></span>
                                <span class="nh-cat-item-name">Mới nhất (Mặc định)</span>
                            </div>
                            <?php if ($dangChonMoiNhat): ?>
                                <i class="bi bi-check2 nh-cat-item-check"></i>
                            <?php endif; ?>
                        </a>

                        <!-- Tùy chọn 2: Giá từ Thấp đến Cao -->
                        <?php $dangChonGiaTang = ($sapXepDangChon === 'gia_tang'); ?>
                        <a href="index.php?<?= http_build_query(array_merge($_GET, ['sap_xep' => 'gia_tang', 'trang_sp' => 1])) ?>"
                            class="nh-cat-item <?= $dangChonGiaTang ? 'active' : '' ?>" role="menuitem">
                            <div class="nh-cat-item-left">
                                <span class="nh-cat-item-icon"><i class="bi bi-sort-numeric-down"></i></span>
                                <span class="nh-cat-item-name">Giá: Thấp đến Cao</span>
                            </div>
                            <?php if ($dangChonGiaTang): ?>
                                <i class="bi bi-check2 nh-cat-item-check"></i>
                            <?php endif; ?>
                        </a>

                        <!-- Tùy chọn 3: Giá từ Cao đến Thấp -->
                        <?php $dangChonGiaGiam = ($sapXepDangChon === 'gia_giam'); ?>
                        <a href="index.php?<?= http_build_query(array_merge($_GET, ['sap_xep' => 'gia_giam', 'trang_sp' => 1])) ?>"
                            class="nh-cat-item <?= $dangChonGiaGiam ? 'active' : '' ?>" role="menuitem">
                            <div class="nh-cat-item-left">
                                <span class="nh-cat-item-icon"><i class="bi bi-sort-numeric-down-alt"></i></span>
                                <span class="nh-cat-item-name">Giá: Cao đến Thấp</span>
                            </div>
                            <?php if ($dangChonGiaGiam): ?>
                                <i class="bi bi-check2 nh-cat-item-check"></i>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. NÚT MENU "DANH MỤC" KẾ BÊN (XỔ RA DROPDOWN SẮP XẾP SẢN PHẨM NHIỀU NHẤT TRƯỚC) -->
            <div class="nh-cat-dropdown-wrapper" id="catDropdownWrapper">
                <button type="button" class="nh-cat-dropdown-btn <?= $danhMucDangChon > 0 ? 'active' : '' ?>"
                    id="btnCatDropdown" onclick="toggleCatDropdown(event)" aria-expanded="false" aria-haspopup="true"
                    title="Bấm để chọn ngành hàng nông sản">
                    <i class="bi bi-tags-fill"></i>
                    <span><?= !empty($tenDanhMucDangChon) ? htmlspecialchars($tenDanhMucDangChon) . ' (' . $soSanPhamDanhMucDangChon . ')' : 'Danh mục' ?></span>
                    <i class="bi bi-chevron-down nh-cat-arrow"></i>
                </button>

                <!-- MENU DROPDOWN XỔ XUỐNG -->
                <div class="nh-cat-dropdown-menu" id="catDropdownMenu" role="menu">
                    <div class="nh-cat-dropdown-header">
                        <span class="nh-cat-dropdown-title">Ngành Hàng Nông Sản</span>
                    </div>
                    <div class="nh-cat-dropdown-list">
                        <!-- Tùy chọn xem tất cả bên trong dropdown -->
                        <a href="index.php?<?= http_build_query(array_merge($_GET, ['danh_muc' => 0, 'trang_sp' => 1])) ?>"
                            class="nh-cat-item <?= $danhMucDangChon === 0 ? 'active' : '' ?>" role="menuitem">
                            <div class="nh-cat-item-left">
                                <span class="nh-cat-item-icon"><i class="bi bi-grid-fill"></i></span>
                                <span class="nh-cat-item-name">Tất cả ngành hàng</span>
                            </div>
                            <?php if ($danhMucDangChon === 0): ?>
                                <i class="bi bi-check2 nh-cat-item-check"></i>
                            <?php endif; ?>
                        </a>

                        <!-- Danh sách danh mục được sắp xếp số sản phẩm giảm dần -->
                        <?php foreach ($dsDanhMucHopLe as $dm): ?>
                            <?php $dangChon = ($danhMucDangChon == $dm['ma_danh_muc']); ?>
                            <a href="index.php?<?= http_build_query(array_merge($_GET, ['danh_muc' => $dm['ma_danh_muc'], 'trang_sp' => 1])) ?>"
                                class="nh-cat-item <?= $dangChon ? 'active' : '' ?>" role="menuitem">
                                <div class="nh-cat-item-left">
                                    <span class="nh-cat-item-icon"><i class="bi bi-flower2"></i></span>
                                    <span class="nh-cat-item-name"><?= htmlspecialchars($dm['ten_danh_muc']) ?></span>
                                </div>
                                <div class="nh-cat-item-right">
                                    <span class="nh-cat-item-count"><?= $dm['so_san_pham'] ?> mặt hàng</span>
                                    <?php if ($dangChon): ?>
                                        <i class="bi bi-check2 nh-cat-item-check"></i>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- HÀNG 4: TAGS ĐANG LỌC NẾU CÓ -->
        <?php if ($coBoLoc): ?>
            <div class="d-flex align-items-center flex-wrap gap-2 mt-3 pt-2.5 border-top">
                <span class="small text-muted fw-bold me-1 text-nowrap">Đang lọc:</span>

                <?php if (!empty($tuKhoa)): ?>
                    <span class="badge bg-light text-dark border rounded-pill fw-semibold py-1.5 px-3">
                        Từ khóa: <strong>"<?= htmlspecialchars($tuKhoa) ?>"</strong>
                        <a href="index.php?<?= http_build_query(array_diff_key($_GET, ['tu_khoa' => ''])) ?>"
                            class="text-danger text-decoration-none ms-1">&times;</a>
                    </span>
                <?php endif; ?>

                <?php if ($nongHoDangChon > 0 && !empty($tenNongHoDangChon)): ?>
                    <span class="badge bg-light text-dark border rounded-pill fw-semibold py-1.5 px-3">
                        Vườn: <strong><?= htmlspecialchars($tenNongHoDangChon) ?></strong>
                        <a href="index.php?<?= http_build_query(array_diff_key($_GET, ['nong_ho' => ''])) ?>"
                            class="text-danger text-decoration-none ms-1">&times;</a>
                    </span>
                <?php endif; ?>

                <?php if (!empty($tinhDangChon)): ?>
                    <span class="badge bg-light text-dark border rounded-pill fw-semibold py-1.5 px-3">
                        Tỉnh: <strong><?= htmlspecialchars($tinhDangChon) ?></strong>
                        <a href="index.php?<?= http_build_query(array_diff_key($_GET, ['tinh' => ''])) ?>"
                            class="text-danger text-decoration-none ms-1">&times;</a>
                    </span>
                <?php endif; ?>

                <?php if (!empty($tinhTrangDangChon)): ?>
                    <?php $infoTt = dinh_dang_tinh_trang_sp($tinhTrangDangChon); ?>
                    <span class="badge bg-light text-dark border rounded-pill fw-semibold py-1.5 px-3">
                        Tình trạng: <strong><?= htmlspecialchars($infoTt['nhan']) ?></strong>
                        <a href="index.php?<?= http_build_query(array_diff_key($_GET, ['tinh_trang' => '', 'trang_thai' => ''])) ?>"
                            class="text-danger text-decoration-none ms-1">&times;</a>
                    </span>
                <?php endif; ?>

                <?php if (!empty($sapXepDangChon)): ?>
                    <span class="badge bg-light text-dark border rounded-pill fw-semibold py-1.5 px-3">
                        Xếp giá: <strong><?= $sapXepDangChon === 'gia_tang' ? 'Thấp → Cao' : 'Cao → Thấp' ?></strong>
                        <a href="index.php?<?= http_build_query(array_diff_key($_GET, ['sap_xep' => ''])) ?>"
                            class="text-danger text-decoration-none ms-1">&times;</a>
                    </span>
                <?php endif; ?>

                <a href="index.php" class="small text-danger fw-bold ms-auto text-decoration-none">
                    Xóa tất cả
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- 3. TIÊU ĐỀ KHU VỰC SẢN PHẨM -->
    <div class="d-flex justify-content-between align-items-center mb-2 px-1">
        <div class="d-flex align-items-center gap-2">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-boxes text-success me-1"></i>
                <?php if ($nongHoDangChon > 0 && !empty($tenNongHoDangChon)): ?>
                    Nông Sản Vườn: <span class="text-success"><?= htmlspecialchars($tenNongHoDangChon) ?></span>
                <?php else: ?>
                    Nông Sản Mở Bán
                <?php endif; ?>
            </h5>
            <span
                class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-0.5 fw-bold small">
                <?= $tongSoSanPham ?> mặt hàng
            </span>
        </div>
    </div>

    <!-- 4. LƯỚI NÔNG SẢN: 2 CỘT TRÊN MOBILE, 3 CỘT TABLET, 4 CỘT DESKTOP -->
    <?php if (empty($dsSanPham)): ?>
        <div class="bg-white rounded-4 p-5 text-center border my-3 shadow-sm">
            <i class="bi bi-inbox text-muted" style="font-size: 3.0rem;"></i>
            <h4 class="fw-bold text-dark mt-2">Chưa tìm thấy nông sản phù hợp</h4>
            <p class="text-muted mb-4 fs-6">Hiện chưa có mặt hàng nào phù hợp với kết quả lọc tìm kiếm của bạn.</p>
            <a href="index.php" class="btn btn-clean px-4 py-2 rounded-pill fw-bold">Xem toàn bộ nông sản</a>
        </div>
    <?php else: ?>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-2 g-sm-3 mb-4">
            <?php foreach ($dsSanPham as $sp): ?>
                <div class="col">
                    <div class="nh-card-product h-100 d-flex flex-column">
                        <!-- KHUNG ẢNH NÔNG SẢN -->
                        <div class="nh-card-thumb-wrap">
                            <?php $anhHienThi = duong_dan_anh_sp($sp['anh'] ?? ''); ?>
                            <a href="index.php?trang=chi_tiet&id=<?= $sp['ma_san_pham'] ?>" class="d-block w-100 h-100"
                                title="<?= htmlspecialchars($sp['ten']) ?>">
                                <img src="<?= $anhHienThi ?>" alt="<?= htmlspecialchars($sp['ten']) ?>" class="nh-card-thumb"
                                    loading="lazy" onerror="this.src='assets/images/nong_san_mac_dinh.svg'">
                            </a>

                            <!-- TEM TRẠNG THÁI MÙA VỤ GÓC TRÊN TRÁI -->
                            <?php
                            $ttSpInfo = dinh_dang_tinh_trang_sp($sp['tinh_trang'] ?? 'con_hang', $sp['ngay_thu_hoach'] ?? null);
                            ?>
                            <span class="nh-badge-glass-status <?= htmlspecialchars($ttSpInfo['tinh_trang_hien_tai']) ?>">
                                <i class="bi <?= $ttSpInfo['icon'] ?> me-1"></i><?= htmlspecialchars($ttSpInfo['nhan']) ?>
                            </span>

                            <!-- SẢN LƯỢNG KHO GÓC DƯỚI PHẢI -->
                            <span class="nh-badge-glass-stock">
                                Kho: <?= dinh_dang_kho_thong_minh($sp['san_luong'], $sp['don_vi_san_luong']) ?>
                            </span>
                        </div>

                        <!-- THÔNG TIN NÔNG SẢN -->
                        <div class="nh-card-body p-2 p-sm-3 d-flex flex-column flex-grow-1">
                            <!-- Danh mục & Tỉnh thành -->
                            <div class="d-flex align-items-center justify-content-between mb-1 gap-1">
                                <span
                                    class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fw-bold text-truncate nh-card-category">
                                    <?= htmlspecialchars($sp['ten_danh_muc']) ?>
                                </span>
                                <div class="text-muted d-flex align-items-center gap-1 text-truncate nh-card-location"
                                    style="max-width: 55%;" title="<?= htmlspecialchars($sp['tinh_nong_ho'] ?? 'Toàn quốc') ?>">
                                    <i class="bi bi-geo-alt-fill text-danger flex-shrink-0"></i>
                                    <b><span
                                            class="text-truncate"><?= htmlspecialchars($sp['tinh_nong_ho'] ?? 'Toàn quốc') ?></span></b>
                                </div>
                            </div>

                            <!-- Tên Nông Sản -->
                            <h5 class="nh-card-title mb-1" title="<?= htmlspecialchars($sp['ten']) ?>">
                                <a href="index.php?trang=chi_tiet&id=<?= $sp['ma_san_pham'] ?>"
                                    class="text-decoration-none nh-product-title-link">
                                    <?= htmlspecialchars($sp['ten']) ?>
                                </a>
                            </h5>

                            <!-- Tên Nông Hộ và Chứng nhận (Nếu có) -->
                            <div class="d-flex align-items-center justify-content-between text-muted mb-2 gap-1 nh-card-farm">
                                <div class="text-truncate" title="<?= htmlspecialchars($sp['ten_nong_ho']) ?>">
                                    <b><i class="bi bi-shop text-success me-1"></i>
                                        <span><?= htmlspecialchars($sp['ten_nong_ho']) ?></span></b>
                                </div>
                                <?php if (!empty($sp['chung_nhan'])): ?>
                                    <?php $dsCnCard = dinh_dang_chung_nhan($sp['chung_nhan']); ?>
                                    <?php if (!empty($dsCnCard[0])): ?>
                                        <span
                                            class="badge <?= $dsCnCard[0]['badge_class'] ?> rounded-pill fw-bold flex-shrink-0 nh-card-cert">
                                            <?= htmlspecialchars($dsCnCard[0]['ten']) ?>
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <!-- GIÁ BÁN VÀ NÚT MUA -->
                            <div class="mt-auto pt-2 border-top d-flex align-items-center justify-content-between gap-1">
                                <div class="d-flex align-items-baseline gap-1 text-truncate">
                                    <?php if ($sp['gia'] !== null && $sp['gia'] > 0): ?>
                                        <span class="nh-price-val"><?= dinh_dang_tien($sp['gia']) ?></span>
                                        <span class="nh-price-unit">/<?= htmlspecialchars($sp['don_vi_gia']) ?></span>
                                    <?php else: ?>
                                        <span
                                            class="badge bg-warning bg-opacity-25 text-dark border border-warning px-2.5 py-1 fw-bold rounded-pill nh-card-quote">
                                            Báo giá
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <a href="index.php?trang=chi_tiet&id=<?= $sp['ma_san_pham'] ?>" class="nh-btn-card-buy"
                                    title="Xem chi tiết &amp; Đặt mua <?= htmlspecialchars($sp['ten']) ?>">
                                    <i class="bi bi-cart-plus-fill"></i>
                                    <span>Mua</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- PHÂN TRANG -->
        <?php if ($tongSoTrang > 1): ?>
            <nav class="mt-3 mb-4" aria-label="Phân trang danh sách nông sản">
                <ul class="nh-pagination">
                    <li class="nh-page-item <?= $trangHienTai <= 1 ? 'disabled' : '' ?>">
                        <a class="nh-page-link"
                            href="index.php?<?= http_build_query(array_merge($_GET, ['trang_sp' => max(1, $trangHienTai - 1)])) ?>"
                            title="Trang trước">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    <?php for ($p = 1; $p <= $tongSoTrang; $p++): ?>
                        <li class="nh-page-item <?= $p === $trangHienTai ? 'active' : '' ?>">
                            <a class="nh-page-link"
                                href="index.php?<?= http_build_query(array_merge($_GET, ['trang_sp' => $p])) ?>">
                                <?= $p ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    <li class="nh-page-item <?= $trangHienTai >= $tongSoTrang ? 'disabled' : '' ?>">
                        <a class="nh-page-link"
                            href="index.php?<?= http_build_query(array_merge($_GET, ['trang_sp' => min($tongSoTrang, $trangHienTai + 1)])) ?>"
                            title="Trang sau">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
                <div class="text-center text-muted small mt-2 fw-semibold">
                    Trang <?= $trangHienTai ?> / <?= $tongSoTrang ?>
                </div>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- ==========================================================================
     MODAL HƯỚNG DẪN SỬ DỤNG
     - Đăng nhập Nông hộ: Chỉ hiện cẩm nang Nông hộ (ẩn Người mua & bộ chuyển tab)
     - Đăng nhập Người mua: Chỉ hiện cẩm nang Người mua (ẩn Nông hộ & bộ chuyển tab)
     - Chưa đăng nhập (khách): Hiện cả 2 cẩm nang kèm bộ chuyển tab linh hoạt
     ========================================================================== -->
<?php
$laNongHo = co_vai_tro('nong_ho');
$laNguoiMua = co_vai_tro('nguoi_mua');

if ($laNongHo) {
    $hienThiNongHo = true;
    $hienThiNguoiMua = false;
    $hienBoChuyenTab = false;
} elseif ($laNguoiMua) {
    $hienThiNongHo = false;
    $hienThiNguoiMua = true;
    $hienBoChuyenTab = false;
} else {
    // Khách chưa đăng nhập (hoặc vai trò khác như admin xem thử)
    $hienThiNongHo = true;
    $hienThiNguoiMua = true;
    $hienBoChuyenTab = true;
}
?>
<div class="nh-guide-backdrop" id="modalHuongDan" onclick="dongModalHuongDanKhiClickNgoai(event)" role="dialog"
    aria-modal="true" aria-labelledby="guideTitle">
    <div class="nh-guide-card">
        <!-- NÚT ĐÓNG Ở GÓC TRÊN BÊN PHẢI (CHUẨN CẢM ỨNG >= 36px) -->
        <button type="button" class="nh-guide-close-btn" onclick="dongModalHuongDan()" aria-label="Đóng hướng dẫn">
            <i class="bi bi-x-lg"></i>
        </button>

        <!-- PHẦN ĐẦU: TIÊU ĐỀ & GIỚI THIỆU -->
        <div class="nh-guide-header">
            <div class="nh-guide-badge">
                <?php if ($laNongHo): ?>
                    <span>Dành Cho Nông Hộ</span>
                <?php elseif ($laNguoiMua): ?>
                    <span>Dành Cho Người Mua</span>
                <?php else: ?>
                    <span>Cẩm Nang Bỏ Túi</span>
                <?php endif; ?>
            </div>
            <h3 class="nh-guide-title" id="guideTitle">
                <?php if ($laNongHo): ?>
                    Hướng Dẫn Dành Cho Nông Hộ
                <?php elseif ($laNguoiMua): ?>
                    Hướng Dẫn Dành Cho Người Mua
                <?php else: ?>
                    Hướng Dẫn Sử Dụng
                <?php endif; ?>
            </h3>
        </div>

        <?php if ($hienBoChuyenTab): ?>
            <!-- BỘ CHUYỂN TAB VAI TRÒ (CHỈ HIỂN THỊ KHI CHƯA ĐĂNG NHẬP ĐỂ KHÁCH TỰ DO CHUYỂN ĐỔI) -->
            <div class="nh-guide-role-switch">
                <button type="button" class="nh-guide-tab-btn active" id="tabNongHo" onclick="chuyenTabHuongDan('nong_ho')">
                    <span>Dành Cho Nông Hộ</span>
                </button>
                <button type="button" class="nh-guide-tab-btn" id="tabNguoiMua" onclick="chuyenTabHuongDan('nguoi_mua')">
                    <span>Dành Cho Người Mua</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if ($hienThiNongHo): ?>
            <!-- NỘI DUNG 1: CÁC BƯỚC DÀNH CHO NÔNG HỘ -->
            <div class="nh-guide-content active" id="contentHuongDanNongHo">
                <div class="nh-guide-step-list">
                    <!-- Bước 1 -->
                    <div class="nh-guide-step-item">
                        <div class="nh-guide-step-num">1</div>
                        <div class="nh-guide-step-body">
                            <div class="nh-guide-step-title">
                                <?= $laNongHo ? 'Tài Khoản Và Thông Tin Vườn' : 'Tạo Tài Khoản Nông Hộ' ?>
                            </div>
                            <div class="nh-guide-step-text">
                                <?= $laNongHo
                                    ? 'Cập nhật đầy đủ thông tin nhà vườn, số điện thoại liên hệ và địa chỉ để tạo niềm tin với khách mua.'
                                    : 'Đăng ký tài khoản với vai trò <strong>Nông Hộ</strong>, cập nhật tên vườn và địa chỉ canh tác của bạn.' ?>
                            </div>
                        </div>
                    </div>

                    <!-- Bước 2 -->
                    <div class="nh-guide-step-item">
                        <div class="nh-guide-step-num">2</div>
                        <div class="nh-guide-step-body">
                            <div class="nh-guide-step-title">Đăng Bán Nông Sản</div>
                            <div class="nh-guide-step-text">Chụp ảnh nông sản tươi ngon, nhập số lượng sẵn có và giá bán hợp
                                lý để dễ tiếp cận khách mua.</div>
                        </div>
                    </div>

                    <!-- Bước 3 -->
                    <div class="nh-guide-step-item">
                        <div class="nh-guide-step-num">3</div>
                        <div class="nh-guide-step-body">
                            <div class="nh-guide-step-title">Xác Nhận Đơn Và Giao Hàng</div>
                            <div class="nh-guide-step-text">Theo dõi thông báo đơn đặt hàng, chuẩn bị nông sản chất lượng và
                                gửi đến tay người mua an toàn.</div>
                        </div>
                    </div>
                </div>

                <!-- Nút hành động nhanh cho Nông Hộ -->
                <div class="nh-guide-actions">
                    <?php if (!da_dang_nhap()): ?>
                        <a href="index.php?trang=dang_ky" class="nh-guide-cta-btn">
                            <span>Đăng Ký Nông Hộ Ngay</span>
                            <i class="bi bi-arrow-right-short"></i>
                        </a>
                    <?php elseif ($laNongHo): ?>
                        <a href="index.php?trang=nong_ho_form_san_pham" class="nh-guide-cta-btn">
                            <span>Đăng Bán Nông Sản Mới</span>
                            <i class="bi bi-arrow-right-short"></i>
                        </a>
                    <?php else: ?>
                        <button type="button" class="nh-guide-cta-btn secondary" onclick="dongModalHuongDan()">
                            <span>Đã hiểu, đóng lại</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($hienThiNguoiMua): ?>
            <!-- NỘI DUNG 2: CÁC BƯỚC DÀNH CHO NGƯỜI MUA -->
            <div class="nh-guide-content <?= $laNguoiMua ? 'active' : '' ?>" id="contentHuongDanNguoiMua">
                <div class="nh-guide-step-list">
                    <!-- Bước 1 -->
                    <div class="nh-guide-step-item">
                        <div class="nh-guide-step-num buyer">1</div>
                        <div class="nh-guide-step-body">
                            <div class="nh-guide-step-title">Tìm Nông Sản Tươi Ngon</div>
                            <div class="nh-guide-step-text">Duyệt theo danh mục trái cây, rau củ hoặc lọc nhà vườn gần bạn
                                để tìm nông sản ngon với giá gốc.</div>
                        </div>
                    </div>

                    <!-- Bước 2 -->
                    <div class="nh-guide-step-item">
                        <div class="nh-guide-step-num buyer">2</div>
                        <div class="nh-guide-step-body">
                            <div class="nh-guide-step-title">Đặt Mua Và Thỏa Thuận</div>
                            <div class="nh-guide-step-text">Chọn số lượng mong muốn, đặt mua trực tiếp hoặc gửi tin nhắn
                                trao đổi số lượng lớn với nhà vườn.</div>
                        </div>
                    </div>

                    <!-- Bước 3 -->
                    <div class="nh-guide-step-item">
                        <div class="nh-guide-step-num buyer">3</div>
                        <div class="nh-guide-step-body">
                            <div class="nh-guide-step-title">Nhận Hàng Và Đánh Giá</div>
                            <div class="nh-guide-step-text">Kiểm tra độ tươi ngon khi nhận hàng, đánh giá chất lượng để ủng
                                hộ bà con nông dân uy tín.</div>
                        </div>
                    </div>
                </div>

                <!-- Nút hành động nhanh cho Người Mua -->
                <div class="nh-guide-actions">
                    <?php if (!da_dang_nhap()): ?>
                        <a href="index.php?trang=dang_ky" class="nh-guide-cta-btn">
                            <span>Đăng Ký Mua Nông Sản</span>
                            <i class="bi bi-arrow-right-short"></i>
                        </a>
                    <?php else: ?>
                        <button type="button" class="nh-guide-cta-btn buyer-cta"
                            onclick="dongModalHuongDan(); cuonDenKhuVucNongSan();">
                            <span>Khám Phá Nông Sản Ngay</span>
                            <i class="bi bi-arrow-down-short"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ==========================================================================
     JAVASCRIPT ĐIỀU KHIỂN MODAL HƯỚNG DẪN SỬ DỤNG
     ========================================================================== -->
<script>
    /**
     * 1. Mở modal hướng dẫn sử dụng và khóa cuộn trang
     */
    function moModalHuongDan() {
        var modal = document.getElementById('modalHuongDan');
        if (modal) {
            modal.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        }
    }

    /**
     * 2. Đóng modal hướng dẫn sử dụng và khôi phục cuộn trang
     */
    function dongModalHuongDan() {
        var modal = document.getElementById('modalHuongDan');
        if (modal) {
            modal.classList.remove('is-open');
            document.body.style.overflow = '';
        }
    }

    /**
     * 3. Đóng modal khi người dùng bấm vào vùng nền mờ bên ngoài
     */
    function dongModalHuongDanKhiClickNgoai(event) {
        if (event.target && event.target.id === 'modalHuongDan') {
            dongModalHuongDan();
        }
    }

    /**
     * 4. Đóng modal khi bấm phím ESC trên bàn phím
     */
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' || event.key === 'Esc' || event.keyCode === 27) {
            dongModalHuongDan();
        }
    });

    /**
     * 5. Chuyển đổi qua lại giữa tab Nông Hộ và Người Mua
     */
    function chuyenTabHuongDan(vaiTro) {
        var tabNongHo = document.getElementById('tabNongHo');
        var tabNguoiMua = document.getElementById('tabNguoiMua');
        var contentNongHo = document.getElementById('contentHuongDanNongHo');
        var contentNguoiMua = document.getElementById('contentHuongDanNguoiMua');

        if (!tabNongHo || !tabNguoiMua || !contentNongHo || !contentNguoiMua) return;

        if (vaiTro === 'nong_ho') {
            tabNongHo.classList.add('active');
            tabNguoiMua.classList.remove('active');
            contentNongHo.classList.add('active');
            contentNguoiMua.classList.remove('active');
        } else {
            tabNguoiMua.classList.add('active');
            tabNongHo.classList.remove('active');
            contentNguoiMua.classList.add('active');
            contentNongHo.classList.remove('active');
        }
    }

    /**
     * 6. Cuộn mượt đến danh sách sản phẩm nông sản
     */
    function cuonDenKhuVucNongSan() {
        var khuVuc = document.getElementById('khu_vuc_nong_san');
        if (khuVuc) {
            khuVuc.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    /**
     * Tự động căn chỉnh vị trí Dropdown Popover (Danh mục & Sắp xếp giá)
     */
    function fitDropdownToScreen(menu) {
        if (!menu) return;
        var parentWrapper = menu.parentElement;
        if (!parentWrapper) return;

        // Reset vị trí inline
        menu.style.left = '0px';
        menu.style.right = 'auto';

        var winWidth = window.innerWidth || document.documentElement.clientWidth;
        var pad = 12; // Khoảng đệm tối thiểu 12px so với mép màn hình
        var maxAllowedW = winWidth - (pad * 2);

        menu.style.maxWidth = maxAllowedW + 'px';

        var wrapperRect = parentWrapper.getBoundingClientRect();
        var menuWidth = menu.offsetWidth;

        // Tọa độ X trên màn hình mong muốn (mặc định theo lề trái của nút bấm)
        var targetX = wrapperRect.left;
        var maxTargetX = winWidth - pad - menuWidth;

        if (targetX > maxTargetX) {
            targetX = maxTargetX;
        }
        if (targetX < pad) {
            targetX = pad;
        }

        // Chuyển đổi tọa độ X trên màn hình thành CSS left tương đối so với parentWrapper
        var relativeLeft = targetX - wrapperRect.left;
        menu.style.left = relativeLeft + 'px';
        menu.style.right = 'auto';
    }

    /**
     * 7. Bật / Tắt Menu Danh Mục Nông Sản (Lucian UX/UI Custom Listbox Popover)
     */
    function toggleCatDropdown(event) {
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }
        // Đóng menu sắp xếp giá nếu đang mở
        var sortMenu = document.getElementById('sortDropdownMenu');
        var sortBtn = document.getElementById('btnSortDropdown');
        if (sortMenu && sortMenu.classList.contains('is-open')) {
            sortMenu.classList.remove('is-open');
            if (sortBtn) sortBtn.setAttribute('aria-expanded', 'false');
        }

        var menu = document.getElementById('catDropdownMenu');
        var btn = document.getElementById('btnCatDropdown');
        if (!menu || !btn) return;

        var isOpen = menu.classList.toggle('is-open');
        btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

        if (isOpen) {
            fitDropdownToScreen(menu);
        }
    }

    /**
     * 8. Bật / Tắt Menu Sắp Xếp Giá Nông Sản (Lucian UX/UI Custom Listbox Popover)
     */
    function toggleSortDropdown(event) {
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }
        // Đóng menu danh mục nếu đang mở
        var catMenu = document.getElementById('catDropdownMenu');
        var catBtn = document.getElementById('btnCatDropdown');
        if (catMenu && catMenu.classList.contains('is-open')) {
            catMenu.classList.remove('is-open');
            if (catBtn) catBtn.setAttribute('aria-expanded', 'false');
        }

        var menu = document.getElementById('sortDropdownMenu');
        var btn = document.getElementById('btnSortDropdown');
        if (!menu || !btn) return;

        var isOpen = menu.classList.toggle('is-open');
        btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

        if (isOpen) {
            fitDropdownToScreen(menu);
        }
    }

    // Tự động căn lại vị trí khi xoay ngang/dọc điện thoại hoặc đổi kích thước
    window.addEventListener('resize', function () {
        var openMenu = document.querySelector('.nh-cat-dropdown-menu.is-open');
        if (openMenu) {
            fitDropdownToScreen(openMenu);
        }
    });

    // Đóng cả 2 menu khi click ra bên ngoài
    document.addEventListener('click', function (event) {
        var catWrapper = document.getElementById('catDropdownWrapper');
        var catMenu = document.getElementById('catDropdownMenu');
        var catBtn = document.getElementById('btnCatDropdown');
        if (catWrapper && catMenu && !catWrapper.contains(event.target)) {
            catMenu.classList.remove('is-open');
            if (catBtn) catBtn.setAttribute('aria-expanded', 'false');
        }

        var sortWrapper = document.getElementById('sortDropdownWrapper');
        var sortMenu = document.getElementById('sortDropdownMenu');
        var sortBtn = document.getElementById('btnSortDropdown');
        if (sortWrapper && sortMenu && !sortWrapper.contains(event.target)) {
            sortMenu.classList.remove('is-open');
            if (sortBtn) sortBtn.setAttribute('aria-expanded', 'false');
        }
    });

    // Đóng cả 2 menu khi nhấn ESC
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' || event.key === 'Esc' || event.keyCode === 27) {
            var catMenu = document.getElementById('catDropdownMenu');
            var catBtn = document.getElementById('btnCatDropdown');
            if (catMenu && catMenu.classList.contains('is-open')) {
                catMenu.classList.remove('is-open');
                if (catBtn) catBtn.setAttribute('aria-expanded', 'false');
            }

            var sortMenu = document.getElementById('sortDropdownMenu');
            var sortBtn = document.getElementById('btnSortDropdown');
            if (sortMenu && sortMenu.classList.contains('is-open')) {
                sortMenu.classList.remove('is-open');
                if (sortBtn) sortBtn.setAttribute('aria-expanded', 'false');
            }
        }
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
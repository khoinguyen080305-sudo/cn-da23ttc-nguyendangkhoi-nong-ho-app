<?php
// Thiết lập múi giờ Việt Nam cho toàn bộ ứng dụng
date_default_timezone_set('Asia/Ho_Chi_Minh');

// 1. KẾT NỐI DATABASE MYSQL
$may_chu = "localhost";
$tai_khoan = "root";
$mat_khau = "";
$csdl = "nong_ho_db";

$ketnoi = mysqli_connect($may_chu, $tai_khoan, $mat_khau, $csdl);

if (!$ketnoi) {
    error_log("Lỗi kết nối CSDL: " . mysqli_connect_error());
    die("Lỗi: Không thể kết nối cơ sở dữ liệu. Vui lòng kiểm tra lại MySQL trong XAMPP.");
}

// Bắt buộc mã hóa utf8mb4 để hỗ trợ tiếng Việt có dấu đầy đủ
mysqli_set_charset($ketnoi, "utf8mb4");

// Đồng bộ múi giờ MySQL sang múi giờ Việt Nam (+07:00)
mysqli_query($ketnoi, "SET time_zone = '+07:00'");


// 2. KHỞI TẠO SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// 3. CẤU HÌNH CHỨNG NHẬN DỰ ÁN
const CHUNG_NHAN = [
    'vietgap' => 'VietGAP',
    'globalgap' => 'GlobalGAP',
    'huu_co' => 'Hữu cơ',
    'ocop' => 'OCOP'
];

const DON_VI = [
    // 1. Nhóm Khối lượng
    'kg' => 'Kilôgam (kg)',
    'tấn' => 'Tấn',
    'nửa ký' => 'Nửa ký (500g)',

    // 2. Nhóm Đếm & Đóng gói thực tế
    'trái' => 'Trái/ Quả',
    'chục' => 'Chục (12 trái)',
    'bó' => 'Bó',
    'thùng' => 'Thùng',
    'bao' => 'Bao',
    'buồng' => 'Buồng',
    'sọt' => 'Sọt',
    'khay' => 'Khay/ Hộp'
];

/**
 * Ma trận phân nhóm đơn vị tính phổ biến theo từng ngành nông sản
 * Dùng để rào trước đơn vị trong form đăng bán sản phẩm
 */
const DANH_MUC_DON_VI = [
    'dua' => [
        'ten' => 'Dừa & Cây ăn trái đếm quả',
        'tu_khoa' => ['dừa', 'dua'],
        'placeholder' => 'Ví dụ: Dừa xiêm xanh, Dừa sáp Cầu Kè, Dừa dứa...',
        'don_vi_kho' => ['trái', 'chục', 'buồng', 'tấn'],
        'don_vi_gia' => ['trái', 'chục', 'buồng'],
        'don_vi_kho_mac_dinh' => 'trái',
        'don_vi_gia_mac_dinh' => 'trái'
    ],
    'trai_cay' => [
        'ten' => 'Trái cây',
        'tu_khoa' => ['trái cây', 'trai cay', 'hoa quả', 'hoa qua', 'trái', 'trai', 'quả', 'qua'],
        'placeholder' => 'Ví dụ: Sầu riêng Ri6, Xoài cát Hòa Lộc, Bưởi Da Xanh...',
        'don_vi_kho' => ['tấn', 'kg', 'thùng', 'sọt', 'trái'],
        'don_vi_gia' => ['kg', 'trái', 'thùng', 'khay', 'tấn'],
        'don_vi_kho_mac_dinh' => 'tấn',
        'don_vi_gia_mac_dinh' => 'kg'
    ],
    'rau_cu' => [
        'ten' => 'Rau củ sạch',
        'tu_khoa' => ['rau', 'củ', 'cu', 'nấm', 'nam'],
        'placeholder' => 'Ví dụ: Xà lách xoong, Cà chua bi, Bắp cải, Dưa leo...',
        'don_vi_kho' => ['kg', 'tấn', 'bó', 'thùng', 'bao'],
        'don_vi_gia' => ['kg', 'nửa ký', 'bó', 'khay', 'thùng'],
        'don_vi_kho_mac_dinh' => 'kg',
        'don_vi_gia_mac_dinh' => 'kg'
    ],
    'lua_gao' => [
        'ten' => 'Lúa gạo & Nông sản khô',
        'tu_khoa' => ['lúa', 'lua', 'gạo', 'gao', 'đậu', 'dau', 'bắp', 'bap', 'hạt', 'hat', 'ngũ cốc', 'ngu coc'],
        'placeholder' => 'Ví dụ: Gạo ST25, Gạo Nàng Thơm, Đậu xanh lòng...',
        'don_vi_kho' => ['tấn', 'bao', 'kg'],
        'don_vi_gia' => ['kg', 'bao', 'tấn'],
        'don_vi_kho_mac_dinh' => 'tấn',
        'don_vi_gia_mac_dinh' => 'kg'
    ]
];

const TRANG_THAI_DON = [
    'moi' => ['nhan' => 'Chờ xác nhận', 'mau' => 'warning'],
    'da_bao_gia' => ['nhan' => 'Nông hộ báo giá mới', 'mau' => 'info'],
    'da_xac_nhan' => ['nhan' => 'Đã xác nhận', 'mau' => 'primary'],
    'hoan_thanh' => ['nhan' => 'Giao dịch thành công', 'mau' => 'success'],
    'da_huy' => ['nhan' => 'Đã hủy', 'mau' => 'secondary']
];

const TINH_TRANG_SP = [
    'con_hang' => ['nhan' => 'Còn hàng', 'icon' => 'bi-check-circle-fill', 'mau' => 'success', 'mo_ta' => 'Sẵn sàng giao / có sẵn trong kho'],
    'sap_thu_hoach' => ['nhan' => 'Sắp thu hoạch', 'icon' => 'bi-clock-history', 'mau' => 'warning', 'mo_ta' => 'Sắp tới kỳ thu hoạch'],
    'dang_trong' => ['nhan' => 'Đang canh tác', 'icon' => 'bi-tree-fill', 'mau' => 'info', 'mo_ta' => 'Đang trong vụ mùa chăm sóc'],
    'het_hang' => ['nhan' => 'Tạm hết hàng', 'icon' => 'bi-x-circle', 'mau' => 'secondary', 'mo_ta' => 'Tạm hết hàng hoặc qua mùa']
];


// ==============================
// 4. CÁC HÀM TIỆN ÍCH DÙNG CHUNG
// ==============================

/**
 * Hàm định dạng tiền tệ Việt Nam Đồng (VND)
 * Phân chia hàng nghìn bằng dấu chấm (.) giúp nông hộ nhìn rõ số tiền, không bị nhầm số 0.
 * Giá được nhập thoải mái, nếu rỗng hoặc null sẽ hiện 'Liên hệ báo giá'.
 */
function dinh_dang_tien($so_tien)
{
    if ($so_tien === null || $so_tien === '' || $so_tien < 0) {
        return 'Liên hệ báo giá';
    }
    return number_format((float) $so_tien, 0, ',', '.') . ' đ';
}

/**
 * Hàm định dạng số lượng kho/ nông sản gọn gàng
 * Tự động loại bỏ .0 thừa (vd: 5.0 -> 5 tấn, 300.0 -> 300 kg, 2.5 -> 2.5 tấn)
 */
function dinh_dang_so_luong($so)
{
    if ($so === null || $so === '')
        return '0';
    $val = (float) $so;
    if (floor($val) == $val) {
        return number_format($val, 0, '.', ',');
    }
    return rtrim(rtrim(number_format($val, 2, '.', ','), '0'), '.');
}

/**
 * Định dạng hiển thị sản lượng kho thông minh tự động thích ứng đơn vị
 * - Khi kho có đơn vị Tấn nhưng sản lượng < 1 tấn
 *   Tự động hiển thị thành "... kg"
 */
function dinh_dang_kho_thong_minh($soLuong, $donVi)
{
    if ($soLuong === null || $soLuong === '')
        return '0 ' . $donVi;
    $so = (float) $soLuong;
    if ($so <= 0)
        return '0 ' . $donVi;

    $dv = mb_strtolower(trim($donVi), 'UTF-8');

    // 1. Nhóm Tấn -> Kg
    if ($dv === 'tấn' || $dv === 'tan') {
        if ($so < 1) {
            // Khi dưới 1 tấn: tự động đổi sang kg hiển thị
            $soKg = round($so * 1000, 2);
            return dinh_dang_so_luong($soKg) . ' kg';
        }
        return dinh_dang_so_luong($so) . ' tấn';
    }

    return dinh_dang_so_luong($so) . ' ' . $donVi;
}

/**
 * Tách thông tin kho thông minh thành mảng ['so_luong', 'don_vi', 'chuoi']
 */
function tach_kho_thong_minh($soLuong, $donVi)
{
    if ($soLuong === null || $soLuong === '') {
        return ['so_luong' => '0', 'don_vi' => $donVi, 'chuoi' => '0 ' . $donVi];
    }
    $so = (float) $soLuong;
    $dv = mb_strtolower(trim($donVi), 'UTF-8');

    if ($dv === 'tấn' || $dv === 'tan') {
        if ($so > 0 && $so < 1) {
            $soKg = round($so * 1000, 2);
            return ['so_luong' => dinh_dang_so_luong($soKg), 'don_vi' => 'kg', 'chuoi' => dinh_dang_so_luong($soKg) . ' kg'];
        }
        return ['so_luong' => dinh_dang_so_luong($so), 'don_vi' => 'tấn', 'chuoi' => dinh_dang_so_luong($so) . ' tấn'];
    }

    return ['so_luong' => dinh_dang_so_luong($so), 'don_vi' => $donVi, 'chuoi' => dinh_dang_so_luong($so) . ' ' . $donVi];
}

/**
 * Trả về hệ số quy đổi đơn vị khối lượng sang kg (chuẩn đo lường nông sản Việt Nam)
 */
function he_so_sang_kg($donVi)
{
    if (!$donVi)
        return null;
    $dv = mb_strtolower(trim($donVi), 'UTF-8');
    switch ($dv) {
        case 'tan':
        case 'tấn':
            return 1000;
        case 'kg':
        case 'kilogram':
        case 'ký':
            return 1;
        case 'nửa ký':
        case 'nua ky':
        case 'nửa_ký':
        case 'nua_ky':
            return 0.5;
        default:
            return null; // Không phải đơn vị khối lượng (vd: trái, bó...)
    }
}

/**
 * Quy đổi số lượng từ đơn vị nguồn sang đơn vị đích
 * Hỗ trợ tự động: Tấn <-> Kg <-> Nửa ký, và Chục (12 trái) <-> Trái
 * Nếu cùng đơn vị hoặc không quy đổi được thì giữ nguyên số lượng
 */
function quy_doi_don_vi($soLuong, $donViNguon, $donViDich)
{
    $soLuong = (float) $soLuong;
    $src = mb_strtolower(trim($donViNguon), 'UTF-8');
    $dst = mb_strtolower(trim($donViDich), 'UTF-8');

    if ($src === $dst) {
        return $soLuong;
    }

    // Nhóm khối lượng: Tấn <-> Kg <-> Nửa ký
    $heSoNguon = he_so_sang_kg($src);
    $heSoDich = he_so_sang_kg($dst);

    if ($heSoNguon !== null && $heSoDich !== null && $heSoDich > 0) {
        return ($soLuong * $heSoNguon) / $heSoDich;
    }

    // Nhóm đếm: Chục <-> Trái/Quả (1 chục dừa/trái = 12 trái ở miền Tây)
    $laSrcChuc = ($src === 'chục' || $src === 'chuc');
    $laDstChuc = ($dst === 'chục' || $dst === 'chuc');
    $laSrcTrai = ($src === 'trái' || $src === 'trai' || $src === 'quả' || $src === 'qua');
    $laDstTrai = ($dst === 'trái' || $dst === 'trai' || $dst === 'quả' || $dst === 'qua');

    if ($laSrcChuc && $laDstTrai) {
        return $soLuong * 12;
    }
    if ($laSrcTrai && $laDstChuc) {
        return $soLuong / 12;
    }

    return $soLuong;
}

/**
 * Tìm cấu hình gợi ý đơn vị tính theo tên ngành nông sản
 */
function lay_cau_hinh_don_vi_theo_nganh($tenDanhMuc)
{
    if (empty($tenDanhMuc))
        return null;
    $tenLower = bo_dau_tieng_viet($tenDanhMuc);

    foreach (DANH_MUC_DON_VI as $nhom => $cauHinh) {
        foreach ($cauHinh['tu_khoa'] as $tk) {
            $tkKhongDau = bo_dau_tieng_viet($tk);
            if (mb_strpos($tenLower, $tkKhongDau) !== false) {
                return $cauHinh;
            }
        }
    }
    return null;
}

/**
 * Xóa dấu tiếng Việt phục vụ so sánh chuỗi tương đối
 */
function bo_dau_tieng_viet($str)
{
    if (empty($str))
        return '';
    $str = mb_strtolower(trim($str), 'UTF-8');
    $unicode = [
        'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
        'd' => 'đ',
        'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'i' => 'í|ì|ỉ|ĩ|ị',
        'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
    ];
    foreach ($unicode as $nonAccent => $accent) {
        $str = preg_replace("/($accent)/iu", $nonAccent, $str);
    }
    return $str;
}

/**
 * So khớp tên loại nông sản giữa Nhu Cầu của Người Mua và Sản Phẩm của Nông Hộ
 */
function khop_ten_nong_san($tenNhuCau, $tenSanPham)
{
    if (empty($tenNhuCau) || empty($tenSanPham))
        return false;
    $ncClean = bo_dau_tieng_viet($tenNhuCau);
    $spClean = bo_dau_tieng_viet($tenSanPham);

    // 1. Khớp chuỗi trực tiếp (vd: "buoi da xanh" nằm trong "buoi da xanh ruot do")
    if (strpos($spClean, $ncClean) !== false || strpos($ncClean, $spClean) !== false) {
        return true;
    }

    // 2. Tách từ khóa kiểm tra trùng lặp (vd: "buoi", "sau rieng", "ca chua"...)
    $tuKhoaNC = array_filter(explode(' ', $ncClean), function ($w) {
        return mb_strlen($w) >= 2 && !in_array($w, ['trai', 'cay', 'rau', 'cu', 'sach', 'tuoi', 'loai', 'can', 'mua']);
    });

    foreach ($tuKhoaNC as $tk) {
        if (strpos($spClean, $tk) !== false) {
            return true;
        }
    }
    return false;
}

/**
 * Hàm định dạng ngày tháng tiếng Việt (d/m/Y)
 */
function dinh_dang_ngay($ngay_chuoi)
{
    if (empty($ngay_chuoi))
        return 'Chưa xác định';
    $thoi_gian = strtotime($ngay_chuoi);
    return date('d/m/Y', $thoi_gian);
}

/**
 * Thiết lập thông báo giao diện (Toast Notification nội bộ)
 * Loại: 'success', 'error', 'warning', 'info'
 */
function dat_thong_bao($loai, $tieu_de, $noi_dung)
{
    $_SESSION['thong_bao_toast'] = [
        'loai' => $loai,
        'tieu_de' => $tieu_de,
        'noi_dung' => $noi_dung
    ];
}

/**
 * Lấy thông báo giao diện và tự động xóa sau khi lấy
 */
function lay_thong_bao()
{
    if (isset($_SESSION['thong_bao_toast'])) {
        $thong_bao = $_SESSION['thong_bao_toast'];
        unset($_SESSION['thong_bao_toast']);
        return $thong_bao;
    }
    return null;
}

/**
 * Chuyển hướng trang an toàn
 */
function chuyen_huong($duong_dan)
{
    header("Location: " . $duong_dan);
    exit();
}

/**
 * Làm sạch dữ liệu đầu vào chống XSS
 */
function lam_sach($du_lieu)
{
    return htmlspecialchars(trim($du_lieu ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Kiểm tra định dạng số điện thoại di động Việt Nam chuẩn 10 chữ số
 * Đầu số hợp lệ: 03, 05, 07, 08, 09 theo chuẩn các nhà mạng Việt Nam
 */
function kiem_tra_sdt_hop_le($sdt)
{
    $sdt = trim($sdt ?? '');
    return preg_match('/^(0[3|5|7|8|9])[0-9]{8}$/', $sdt) === 1;
}

/**
 * Kiểm tra mật khẩu an toàn theo chuẩn:
 * Tối thiểu 8 ký tự, có ký tự đặc biệt (@, #, $, %...)
 */
function kiem_tra_mat_khau_an_toan($matKhau)
{
    if (mb_strlen($matKhau ?? '') < 8)
        return false;
    return preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $matKhau) === 1;
}

/**
 * Che số điện thoại bảo vệ sự riêng tư đối với khách vãng lai
 * Ví dụ: 0912345678 -> 0912.xxx.xxx
 */
function che_so_dien_thoai($sdt)
{
    if (empty($sdt))
        return '';
    $sdtClean = preg_replace('/[^0-9]/', '', $sdt);
    if (strlen($sdtClean) >= 4) {
        return substr($sdtClean, 0, 4) . '.xxx.xxx';
    }
    return '09xx.xxx.xxx';
}

/**
 * Kiểm tra xem người dùng đã đăng nhập hay chưa
 */
function da_dang_nhap()
{
    return isset($_SESSION['nguoi_dung']);
}

/**
 * Lấy thông tin người dùng đang đăng nhập
 */
function nguoi_dung_hien_tai()
{
    return $_SESSION['nguoi_dung'] ?? null;
}

/**
 * Kiểm tra vai trò của người dùng
 */
function co_vai_tro($vai_tro)
{
    return isset($_SESSION['nguoi_dung']) && $_SESSION['nguoi_dung']['vai_tro'] === $vai_tro;
}

/**
 * Kiểm tra phân quyền truy cập, nếu không hợp lệ thì chuyển hướng về trang chủ
 */
function yeu_cau_vai_tro($vai_tro_cho_phep)
{
    if (!da_dang_nhap()) {
        dat_thong_bao('warning', 'Yêu cầu đăng nhập', 'Vui lòng đăng nhập tài khoản để tiếp tục.');
        chuyen_huong('index.php?trang=dang_nhap');
    }

    // Nếu người dùng đang bị cờ phải đổi mật khẩu tạm thì chỉ cho vào trang đổi mật khẩu
    $trangHienTai = $_GET['trang'] ?? '';
    $phaiDoiMk = !empty($_SESSION['nguoi_dung']['phai_doi_mk']) || !empty($_SESSION['phai_doi_mk']);
    if ($phaiDoiMk && $trangHienTai !== 'doi_mk_tam' && $trangHienTai !== 'dang_xuat') {
        dat_thong_bao('warning', 'Mật khẩu tạm', 'Bạn đang sử dụng mật khẩu tạm do Admin cấp. Vui lòng đổi mật khẩu mới để tiếp tục.');
        chuyen_huong('index.php?trang=doi_mk_tam');
    }

    $ds_vai_tro = is_array($vai_tro_cho_phep) ? $vai_tro_cho_phep : [$vai_tro_cho_phep];
    if (!in_array($_SESSION['nguoi_dung']['vai_tro'], $ds_vai_tro)) {
        dat_thong_bao('error', 'Không có quyền truy cập', 'Tài khoản của bạn không có quyền truy cập khu vực này.');
        chuyen_huong('index.php');
    }
}

/**
 * Tạo mã hiển thị thân thiện, không làm lộ ID nhạy cảm của CSDL
 */
function ma_hien_thi($loai, $id)
{
    switch ($loai) {
        case 'don_hang':
            return '#DH' . str_pad($id, 4, '0', STR_PAD_LEFT);
        case 'san_pham':
            return '#SP' . str_pad($id, 4, '0', STR_PAD_LEFT);
        case 'nhu_cau':
            return '#NC' . str_pad($id, 4, '0', STR_PAD_LEFT);
        default:
            return '#' . $id;
    }
}

/**
 * Lấy đường dẫn ảnh nông sản hiển thị chuẩn xác
 * Ưu tiên tìm trong assets/images/products/, sau đó tìm trong assets/images/
 * Nếu không tồn tại tệp vật lý hoặc rỗng, tự động trả về ảnh mặc định nông sản
 */
function duong_dan_anh_sp($ten_anh)
{
    if (empty($ten_anh)) {
        return 'assets/images/nong_san_mac_dinh.svg';
    }
    // 1. Kiểm tra trong thư mục con assets/images/products/
    if (file_exists(__DIR__ . '/assets/images/products/' . $ten_anh)) {
        return 'assets/images/products/' . $ten_anh;
    }
    // 2. Kiểm tra trực tiếp trong assets/images/
    if (file_exists(__DIR__ . '/assets/images/' . $ten_anh)) {
        return 'assets/images/' . $ten_anh;
    }
    // 3. Fallback về ảnh mặc định nếu tệp không tồn tại
    return 'assets/images/nong_san_mac_dinh.svg';
}

/**
 * Xóa tệp ảnh vật lý khỏi thư mục nếu tồn tại (Tránh lưu rác máy chủ khi đổi ảnh mới)
 */
function xoa_file_anh_cu($ten_file)
{
    if (empty($ten_file))
        return;

    // Bảo vệ các tệp tĩnh hệ thống, không bao giờ xóa
    $cacTepHeThong = ['logo.svg', 'nong_san_mac_dinh.svg', 'default.svg'];
    if (in_array(basename($ten_file), $cacTepHeThong))
        return;

    $duongDanProducts = __DIR__ . '/assets/images/products/' . basename($ten_file);
    if (file_exists($duongDanProducts) && is_file($duongDanProducts)) {
        @unlink($duongDanProducts);
    }

    $duongDanRoot = __DIR__ . '/assets/images/' . basename($ten_file);
    if (file_exists($duongDanRoot) && is_file($duongDanRoot)) {
        @unlink($duongDanRoot);
    }
}

/**
 * Lấy danh sách ảnh nông sản (Tối đa 5 ảnh)
 * Trả về mảng danh sách tên tệp: ['sp_1.jpg', 'sp_2.jpg']
 * Đảm bảo tương thích ngược 100% với các sản phẩm cũ chỉ có 1 ảnh ở cột 'anh'
 */
function lay_danh_sach_anh_sp($sanPham)
{
    if (empty($sanPham)) {
        return [];
    }
    $danhSach = [];
    if (!empty($sanPham['danh_sach_anh'])) {
        $giaiMa = json_decode($sanPham['danh_sach_anh'], true);
        if (is_array($giaiMa)) {
            $danhSach = $giaiMa;
        }
    }
    // Fallback: Nếu danh_sach_anh rỗng nhưng cột anh có giá trị thì dùng cột anh
    if (empty($danhSach) && !empty($sanPham['anh'])) {
        $danhSach = [$sanPham['anh']];
    }
    return array_values(array_filter($danhSach));
}

/**
 * Định dạng danh sách chứng nhận sang tiếng Việt có dấu chuẩn đẹp kèm icon
 * Nhận vào chuỗi thô từ CSDL (vd: 'vietgap,huu_co')
 */
function dinh_dang_chung_nhan($chungNhanRaw)
{
    if (empty($chungNhanRaw))
        return [];
    $cacMa = is_array($chungNhanRaw) ? $chungNhanRaw : explode(',', $chungNhanRaw);
    $ketQua = [];
    $map = [
        'huu_co' => ['ten' => 'Hữu cơ', 'icon' => 'bi-flower1', 'nhan' => '🌿 Hữu cơ', 'badge_class' => 'nh-badge-huuco'],
        'vietgap' => ['ten' => 'VietGAP', 'icon' => 'bi-patch-check-fill', 'nhan' => '⭐ VietGAP', 'badge_class' => 'nh-badge-vietgap'],
        'globalgap' => ['ten' => 'GlobalGAP', 'icon' => 'bi-globe-americas', 'nhan' => '🌐 GlobalGAP', 'badge_class' => 'nh-badge-globalgap'],
        'ocop' => ['ten' => 'OCOP', 'icon' => 'bi-award-fill', 'nhan' => '🏅 OCOP', 'badge_class' => 'nh-badge-ocop'],
    ];

    foreach ($cacMa as $ma) {
        $k = strtolower(trim($ma));
        if (isset($map[$k])) {
            $ketQua[] = array_merge(['ma' => $k], $map[$k]);
        } elseif (!empty($k)) {
            $tenIn = ucfirst(str_replace('_', ' ', $k));
            $ketQua[] = [
                'ma' => $k,
                'ten' => $tenIn,
                'icon' => 'bi-check-circle-fill',
                'nhan' => '✓ ' . $tenIn,
                'badge_class' => 'nh-badge-chung'
            ];
        }
    }
    return $ketQua;
}

/**
 * Định dạng tình trạng mùa vụ của sản phẩm sang tiếng Việt kèm biểu tượng, màu sắc
 * - Với 'sap_thu_hoach': Tự động tính đếm ngược số ngày còn lại đến ngày thu hoạch.
 * - Khi đến ngày thu hoạch (hoặc đã qua): Tự động chuyển trạng thái thành 'con_hang'.
 */
function dinh_dang_tinh_trang_sp($tinhTrang, $ngayThuHoach = null)
{
    // 1. Xử lý logic đếm ngược & tự động chuyển thành 'Còn hàng' khi tới ngày thu hoạch
    if ($tinhTrang === 'sap_thu_hoach' && !empty($ngayThuHoach)) {
        try {
            $homNay = new DateTime(date('Y-m-d'));
            $ngayThu = new DateTime(date('Y-m-d', strtotime($ngayThuHoach)));

            // ĐÃ ĐẾN HOẶC VƯỢT QUÁ NGÀY THU HOẠCH -> TỰ ĐỘNG CHUYỂN SANG "CÒN HÀNG"
            if ($homNay >= $ngayThu) {
                $info = TINH_TRANG_SP['con_hang'];
                $info['tinh_trang_hien_tai'] = 'con_hang';
                $info['nhan'] = 'Còn hàng';
                $info['so_ngay_con_lai'] = 0;
                $info['ngay_dinh_dang'] = date('d/m/Y', strtotime($ngayThuHoach));
                $info['nhan_kem_ngay'] = 'Đã thu hoạch (' . $info['ngay_dinh_dang'] . ')';
                return $info;
            }

            // CHƯA ĐẾN NGÀY THU HOẠCH -> ĐẾM NGƯỢC SỐ NGÀY CÒN LẠI
            $diff = $homNay->diff($ngayThu);
            $soNgayCon = (int) $diff->days;

            $info = TINH_TRANG_SP['sap_thu_hoach'];
            $info['tinh_trang_hien_tai'] = 'sap_thu_hoach';
            $info['so_ngay_con_lai'] = $soNgayCon;
            $info['ngay_dinh_dang'] = date('d/m/Y', strtotime($ngayThuHoach));

            if ($soNgayCon === 1) {
                $info['nhan'] = 'Sắp thu hoạch (còn 1 ngày)';
                $info['nhan_kem_ngay'] = 'Thu hoạch vào ngày mai (' . $info['ngay_dinh_dang'] . ')';
            } else {
                $info['nhan'] = 'Sắp thu hoạch (còn ' . $soNgayCon . ' ngày)';
                $info['nhan_kem_ngay'] = 'Thu hoạch sau ' . $soNgayCon . ' ngày (' . $info['ngay_dinh_dang'] . ')';
            }
            return $info;
        } catch (Exception $e) {
            // Trường hợp lỗi parse ngày
        }
    }

    if (isset(TINH_TRANG_SP[$tinhTrang])) {
        $info = TINH_TRANG_SP[$tinhTrang];
        $info['tinh_trang_hien_tai'] = $tinhTrang;
        $info['so_ngay_con_lai'] = null;
        $info['ngay_dinh_dang'] = !empty($ngayThuHoach) ? date('d/m/Y', strtotime($ngayThuHoach)) : null;
        $info['nhan_kem_ngay'] = $info['nhan'];
        return $info;
    }

    return [
        'nhan' => $tinhTrang ?: 'Chưa cập nhật',
        'nhan_kem_ngay' => $tinhTrang ?: 'Chưa cập nhật',
        'icon' => 'bi-info-circle',
        'mau' => 'secondary',
        'mo_ta' => '',
        'ngay_dinh_dang' => null,
        'so_ngay_con_lai' => null,
        'tinh_trang_hien_tai' => $tinhTrang ?: 'con_hang'
    ];
}

/**
 * Đếm số lượng đơn hàng mới cần xử lý của tài khoản hiện tại (hiển thị badge thông báo đỏ)
 * - Nông Hộ: Các đơn hàng ở trạng thái 'moi' do người mua vừa gửi tới.
 * - Người Mua: Các đơn hàng ở trạng thái 'da_bao_gia' do nông hộ vừa báo giá lại, hoặc đơn 'moi' vừa tạo.
 */
function so_don_hang_moi_hien_tai()
{
    global $ketnoi;
    if (!da_dang_nhap() || !$ketnoi)
        return 0;
    $nd = nguoi_dung_hien_tai();
    $vaiTro = $nd['vai_tro'] ?? '';
    $ma = (int) ($nd['ma_nguoi_dung'] ?? 0);

    if ($vaiTro === 'nong_ho') {
        $stmt = mysqli_prepare($ketnoi, "SELECT COUNT(*) as t FROM don_hang dh 
                                         JOIN san_pham sp ON dh.ma_san_pham = sp.ma_san_pham 
                                         WHERE sp.ma_nong_ho = ? AND dh.trang_thai = 'moi'");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $ma);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($res);
            return (int) ($row['t'] ?? 0);
        }
    } elseif ($vaiTro === 'nguoi_mua') {
        $stmt = mysqli_prepare($ketnoi, "SELECT COUNT(*) as t FROM don_hang 
                                         WHERE ma_nguoi_mua = ? AND trang_thai IN ('da_bao_gia', 'moi')");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $ma);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($res);
            return (int) ($row['t'] ?? 0);
        }
    }
    return 0;
}

/**
 * Đếm số lượng nông hộ đang chờ duyệt
 */
function so_nong_ho_cho_duyet()
{
    global $ketnoi;
    if (!da_dang_nhap() || !co_vai_tro('admin') || !$ketnoi)
        return 0;
    $res = mysqli_query($ketnoi, "SELECT COUNT(*) as t FROM nguoi_dung WHERE vai_tro = 'nong_ho' AND trang_thai = 'cho_duyet'");
    if ($res) {
        $row = mysqli_fetch_assoc($res);
        return (int) ($row['t'] ?? 0);
    }
    return 0;
}
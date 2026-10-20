# ĐỒ ÁN CHUYÊN NGÀNH CÔNG NGHỆ THÔNG TIN

## THÔNG TIN ĐỒ ÁN

- **Tên đề tài:** XÂY DỰNG ỨNG DỤNG HỖ TRỢ NÔNG HỘ TIẾP CẬN THỊ TRƯỜNG VÀ TIÊU THỤ SẢN PHẨM
- **Sinh viên thực hiện:** Nguyễn Đăng Khôi
- **Mssv:** 110123117
- **Lớp:** DA23TTC
- **Mục tiêu cốt lõi:** Cho phép nông hộ cập nhật tình trạng sản phẩm (đang trồng, sắp thu hoạch, còn hàng, hết hàng) và ngày dự kiến thu hoạch để người mua tìm nguồn hàng sớm. Xây dựng chức năng cơ hội thị trường: người mua đăng nhu cầu cần mua, hệ thống gợi ý nông hộ phù hợp theo quy tắc chấm điểm. Xây dựng công cụ số giúp nông hộ tiếp cận thị trường, giới thiệu sản phẩm và kết nối với người mua/ đối tác. Hỗ trợ quảng bá và tiêu thụ sản phẩm nông nghiệp hiệu quả hơn thông qua nền tảng trực tuyến. Xây dựng hệ thống quản lý hồ sơ nông hộ, sản phẩm, sản lượng, giá bán và đơn hàng có cơ sở dữ liệu và chức năng hoạt động thực tế.

## 1. GIỚI THIỆU TỔNG QUAN VÀ ĐIỂM NỔI BẬT

Đồ án giải quyết vấn đề trăn trở lớn nhất của người nông dân Việt Nam: "Được mùa nhưng sản lượng bị ép giá, thiếu kênh tiêu thụ sản phẩm, quảng bá và khó tiếp cận thị trường". Hệ thống được thiết kế thực tiễn, tinh gọn, không nhồi nhét công nghệ phức tạp mà tập trung sâu vào nghiệp vụ thực tế:

- Bộ Lọc Đa Năng: Tích hợp tìm kiếm từ khóa, lọc theo tỉnh thành hoạt động, lọc theo tình trạng mùa vụ và nút tìm kiếm, tối giản thao tác cho người dùng.
- Theo Dõi Mùa Vụ và Hiển Thị Ngày Thu Hoạch Dự Kiến: Quản lý 4 trạng thái mùa vụ (dang_trong, sap_thu_hoach, con_hang, het_hang). Đặc biệt, với nông sản "Sắp thu hoạch", hệ thống hiển thị trực quan ngày dự kiến thu hoạch (d/m/Y) trên cả thẻ nông sản và trang chi tiết giúp người mua chủ động đặt trước kỳ thu hoạch.
- Tách Bạch Đơn Vị Sản Lượng Kho và Đơn Vị Tính Giá: Kho hàng lưu trữ khối lượng thực tế (3.5 Tấn, 5.000 Trái, 800 Bó), trong khi giá bán niêm yết có thể linh hoạt theo kg, trái, bó kèm cơ chế quy đổi đơn vị đo lường chuẩn.
- Bộ Lọc Tỉnh Thành Động: Dropdown lọc tỉnh chỉ hiển thị những tỉnh hiện có nông hộ đang bán hàng hoạt động, loại bỏ hoàn toàn tình trạng tìm kiếm trả về kết quả rỗng.
- Thuật Toán Ghép Nối Cung - Cầu Đa Tiêu Chí (Smart Matching %): Tự động so khớp 5 tiêu chí (Tên loại nông sản cụ thể + Ngành hàng, Khả năng cung ứng kho có quy đổi đơn vị, Mức giá, Lợi thế địa lý cùng tỉnh thành, và Tính sẵn sàng/kịp mùa vụ thu hoạch) để gợi ý cho nông hộ các cơ hội thu mua tiềm năng nhất.
- Quy Trình Thương Lượng Đơn Hàng 3 Bước Thực Tế: Người mua đề xuất giá Nông hộ báo giá lại hoặc Chấp nhận Khi xác nhận mới tự động trừ tồn kho Hoàn thành để tính điểm uy tín cho nông hộ trên sản phẩm đó.
- Quy Trình Quên Mật Khẩu An Toàn và Cấp Mật Khẩu Tạm: Không phụ thuộc SMTP máy chủ gửi mail dễ lỗi. Admin cấp mật khẩu tạm ngẫu nhiên, hệ thống kích hoạt gán cờ cho trạng thái đổi mật khẩu bằng 1 ép buộc người dùng phải đổi mật khẩu mới ngay khi đăng nhập.
- Quản Lý Tệp Ảnh: Khi nông hộ cập nhật ảnh sản phẩm mới hoặc xóa sản phẩm, hệ thống tự động xóa tệp ảnh cũ vật lý khỏi thư mục lưu trữ, chống phình to dung lượng máy chủ.

## 2. PHÂN QUYỀN VAI TRÒ

Hệ thống phân định rành mạch 3 vai trò thành viên cùng 1 trạng thái khách vãng lai:
Chi tiết quyền hạn:

1. Khách vãng lai: Xem nông sản, tra cứu theo danh mục và tỉnh thành. Số điện thoại của nhà vườn bị che một phần (0912.xxx.xxx) để bảo mật thông tin.
2. Người mua: Vai trò duy nhất có biểu tượng Giỏ hàng trên Header và nút Thêm vào giỏ; có thể đề xuất giá mong muốn cho từng nông hộ, gửi đơn hàng và đăng tin nhu cầu thu mua.
3. Nông hộ: Đăng ký tài khoản cần Admin xét duyệt. Sau khi duyệt có toàn quyền đăng bán nông sản, tiếp nhận đơn đặt, báo giá lại, xác nhận giao dịch và sử dụng bảng Smart Matching để tìm người mua.
4. Quản trị viên: Quản lý duyệt/từ chối nông hộ, khóa tài khoản vi phạm, cấp mật khẩu tạm cho người dùng quên mật khẩu, quản lý danh mục sản phẩm.

## 3. KIẾN TRÚC KỸ THUẬT & CẤU TRÚC MÃ NGUỒN

Dự án áp dụng mô hình MVC (Model - View - Controller)

### Sơ đồ thư mục dự án:

nongho-app/
├── index.php # Bộ định tuyến trung tâm (Router MVC tập trung ?trang=...)
├── config.php # Kết nối MySQL, Session, Hằng số & Hàm tiện ích chung (tiếng Việt)
├── phan_tich_du_an.md # Tài liệu phân tích yêu cầu & thiết kế nghiệp vụ
├── thiet_ke_giao_dien_ux_ui.md # Tài liệu thiết kế Design System & Giao diện UX/UI
├── bang_test_case.md # Bảng test case toàn diện 10 modules kiểm thử đồ án
├── README.md # Tài liệu đồ án, sơ đồ ERD, hướng dẫn vận hành & nhật ký cập nhật
│
├── database/
│ └── nong_ho_db.sql # Kịch bản tạo CSDL & dữ liệu mẫu hoàn chỉnh 6 bảng
│
├── models/
│ ├── NguoiDung.php # Xử lý tài khoản, phân quyền, đổi MK tạm, lọc tỉnh động
│ ├── DanhMuc.php # Quản lý danh mục ngành hàng nông sản
│ ├── SanPham.php # CRUD nông sản, trừ kho, phân tách đơn vị sản lượng & giá, dọn rác ảnh
│ ├── DonHang.php # Giỏ hàng Session, quy trình đơn hàng 3 bước, trừ kho quy đổi, thông báo
│ └── NhuCau.php # Đăng tin nhu cầu, thuật toán Smart Matching % đa tiêu chí
│
├── controllers/
│ ├── CongKhaiController.php # Sàn nông sản, chi tiết, đăng ký, đăng nhập, đổi MK tạm
│ ├── NguoiMuaController.php # Giỏ hàng, tạo đơn, quản lý nhu cầu, hồ sơ người mua
│ ├── NongHoController.php # Quản lý nông sản, xem nhu cầu thị trường & Matching %
│ ├── DonHangController.php # Xác nhận đơn, báo giá lại, hủy đơn, hoàn thành đơn
│ └── AdminController.php # Phê duyệt nông hộ, cấp MK tạm, quản lý danh mục
│
├── views/
│ ├── layouts/
│ │ ├── header.php # Thanh điều hướng có logo SVG, đếm giỏ hàng, menu phân quyền
│ │ └── footer.php # Chân trang tinh gọn 1 dòng, hotline pill & container Toast nội bộ
│ ├── public/
│ │ ├── trang_chu.php # Sàn nông sản, bộ lọc hợp nhất .nh-unified-bar & chip danh mục đếm số lượng
│ │ └── chi_tiet.php # Chi tiết sản phẩm, chứng nhận, nhãn ngày thu hoạch & thông tin nhà vườn
│ ├── auth/
│ │ ├── dang_nhap.php # Form đăng nhập & hotline hỗ trợ quên mật khẩu
│ │ ├── dang_ky.php # Đăng ký tài khoản (hỗ trợ auto lowercase UX & đo độ mạnh mật khẩu)
│ │ └── doi_mk_tam.php # Màn hình cưỡng chế đổi mật khẩu mới khi dùng MK tạm
│ ├── nguoi_mua/
│ │ ├── gio_hang.php # Giỏ hàng, chỉnh sửa số lượng & mức giá đề xuất
│ │ ├── nhu_cau_cua_toi.php # Quản lý các tin nhu cầu cần mua
│ │ └── ho_so.php # Cập nhật hồ sơ cá nhân người mua
│ ├── nong_ho/
│ │ ├── san_pham.php # Danh sách nông sản của nông hộ (ẩn/hiện, sửa, xóa)
│ │ ├── form_san_pham.php # Form thêm/sửa sản phẩm (chọn đơn vị sản lượng & giá tách biệt)
│ │ ├── nhu_cau_thi_truong.php # Giao diện ghép nối Smart Matching %
│ │ └── ho_so.php # Cập nhật thông tin vườn, số điện thoại, tỉnh thành
│ ├── don_hang/
│ │ └── danh_sach.php # Bảng theo dõi 3 tab: Đang xử lý, Đã xác nhận, Lịch sử
│ └── admin/
│ └── tong_quan.php # Bảng điều khiển quản trị tinh gọn (Duyệt nông hộ, Quản lý tài khoản & Cấp MK, Danh mục)
│
└── assets/
├── css/
│ └── style.css # Design tokens, màu xanh nông nghiệp, lưới compact, toast & modal
├── js/
│ └── app.js # Xử lý tương tác trang, tăng giảm giỏ hàng, toast & modal tự xây dựng
└── images/
├── logo.svg # Logo vector thương hiệu Nông Hộ chính thức
├── logo_icon.svg # Biểu tượng icon mầm lá mặt trời rút gọn
├── hero_basket.png # Hình ảnh giỏ nông sản tươi minh họa Hero Banner
├── nong_san_mac_dinh.svg # Ảnh vector fallback khi sản phẩm chưa có ảnh
└── products/ # Thư mục lưu ảnh nông sản được tải lên thực tế

## 4. THIẾT KẾ CƠ SỞ DỮ LIỆU & SƠ ĐỒ ERD (DATABASE SCHEMA & ERD)

### 4.1. Sơ Đồ Thực Thể Mối Quan Hệ

![1792479032637](image/README/1792479032637.png)

### 4.2. Bảng Ràng Buộc Khóa Ngoại & Hành Vi Xóa

| Bảng Chứa Khóa Ngoại | Cột Khóa Ngoại  | Bảng Tham Chiếu | Cột Tham Chiếu  | Hành Vi Khi Xóa (ON DELETE) | Ý Nghĩa Thực Tiễn Nghiệp Vụ                                                                             |
| :------------------- | :-------------- | :-------------- | :-------------- | :-------------------------- | :------------------------------------------------------------------------------------------------------ |
| `san_pham`           | `ma_nong_ho`    | `nguoi_dung`    | `ma_nguoi_dung` | `CASCADE`                   | Xóa tài khoản nông hộ sẽ dọn dẹp các sản phẩm thuộc nông hộ đó.                                         |
| `san_pham`           | `ma_danh_muc`   | `danh_muc`      | `ma_danh_muc`   | `RESTRICT`                  | Không cho phép xóa danh mục khi vẫn còn sản phẩm đang thuộc danh mục đó.                                |
| `don_hang`           | `ma_nguoi_mua`  | `nguoi_dung`    | `ma_nguoi_dung` | `CASCADE`                   | Xóa tài khoản người mua sẽ dọn dẹp đơn hàng liên quan.                                                  |
| `don_hang`           | `ma_san_pham`   | `san_pham`      | `ma_san_pham`   | `RESTRICT`                  | Bảo vệ tính toàn vẹn: Không thể xóa cứng sản phẩm nếu đã có đơn hàng (dùng cơ chế ẩn xóa mềm `an = 1`). |
| `nhu_cau`            | `ma_nguoi_mua`  | `nguoi_dung`    | `ma_nguoi_dung` | `CASCADE`                   | Xóa tài khoản người mua sẽ dọn dẹp tin nhu cầu của họ.                                                  |
| `nhu_cau`            | `ma_danh_muc`   | `danh_muc`      | `ma_danh_muc`   | `RESTRICT`                  | Không cho phép xóa danh mục khi có nhu cầu thu mua đang tham chiếu.                                     |
| `thong_bao`          | `ma_nguoi_nhan` | `nguoi_dung`    | `ma_nguoi_dung` | `CASCADE`                   | Xóa người dùng sẽ dọn dẹp toàn bộ thông báo của người đó.                                               |

### 4.3. Từ Điển Dữ Liệu Chi Tiết

#### 1. Bảng `nguoi_dung` (Tài khoản, Hồ sơ Nông hộ & Phân quyền)

- **Khóa chính:** `ma_nguoi_dung` (`INT AUTO_INCREMENT`)
- **Khóa duy nhất (Unique):** `tai_khoan` (`VARCHAR(50) NOT NULL UNIQUE`) - chuẩn hóa chữ thường không dấu (`a-z0-9_`).
- **Các trường dữ liệu:**
  - `mat_khau` (`VARCHAR(255) NOT NULL`): Chuỗi băm Bcrypt 60 ký tự.
  - `ho_ten` (`VARCHAR(100) NOT NULL`): Tên người mua hoặc Tên nhà vườn / Hợp tác xã.
  - `sdt` (`VARCHAR(15) NOT NULL`): Số điện thoại liên lạc (che với khách vãng lai, hiển thị cho thành viên).
  - `vai_tro` (`ENUM('nguoi_mua', 'nong_ho', 'admin') NOT NULL`): Định danh vai trò trong hệ thống.
  - `trang_thai` (`ENUM('cho_duyet', 'da_duyet', 'khoa') NOT NULL DEFAULT 'cho_duyet'`): Trạng thái hoạt động tài khoản.
  - `phai_doi_mk` (`TINYINT(1) NOT NULL DEFAULT 0`): Cờ ép buộc đổi mật khẩu khi đăng nhập bằng MK tạm do Admin cấp.
  - `tinh` (`VARCHAR(50) NULL`): Tỉnh thành nông hộ đặt vườn (dùng để trích xuất `DISTINCT tinh` tạo bộ lọc động).
  - `dia_chi` (`VARCHAR(255) NULL`): Địa chỉ chi tiết (Ấp, Xã, Huyện) để giao nhận nông sản.
  - `ngay_tao` (`TIMESTAMP DEFAULT CURRENT_TIMESTAMP`): Thời điểm đăng ký.

#### 2. Bảng `danh_muc` (Ngành hàng nông sản)

- **Khóa chính:** `ma_danh_muc` (`INT AUTO_INCREMENT`)
- **Khóa duy nhất (Unique):** `ten_danh_muc` (`VARCHAR(100) NOT NULL UNIQUE`)
- **Dữ liệu mẫu:** `Trái cây`, `Rau củ sạch`, `Lúa gạo`, `Nông sản khác`.

#### 3. Bảng `san_pham` (Nông sản đăng bán)

- **Khóa chính:** `ma_san_pham` (`INT AUTO_INCREMENT`)
- **Khóa ngoại:** `ma_nong_ho` (`INT NOT NULL`), `ma_danh_muc` (`INT NOT NULL`)
- **Các trường dữ liệu:**
  - `ten` (`VARCHAR(150) NOT NULL`): Tên mặt hàng (Vd: _Bưởi Da Xanh Ruột Đỏ_, _Dừa Xiêm Xanh_).
  - `san_luong` (`DECIMAL(10,2) NOT NULL DEFAULT 0`): Sản lượng kho thực tế sẵn có. Tự động trừ kho khi đơn sang `da_xac_nhan`.
  - `don_vi_san_luong` (`VARCHAR(20) NOT NULL DEFAULT 'kg'`): Đơn vị đo khối lượng kho (`tấn`, `tạ`, `kg`, `bó`, `trái`, `thùng`, `bao`).
  - `gia` (`DECIMAL(12,2) NULL`): Đơn giá niêm yết bán buôn/lẻ. `NULL` mang ý nghĩa _"Liên hệ báo giá"_.
  - `don_vi_gia` (`VARCHAR(20) NOT NULL DEFAULT 'kg'`): Đơn vị định giá chào bán (`kg`, `trái`, `bó`, `thùng`...).
  - `anh` (`VARCHAR(255) NULL`): Tên tệp ảnh đại diện nông sản trong thư mục `assets/images/products/`.
  - `tinh_trang` (`ENUM('dang_trong', 'sap_thu_hoach', 'con_hang', 'het_hang') NOT NULL DEFAULT 'con_hang'`): Tình trạng mùa vụ canh tác.
  - `ngay_thu_hoach` (`DATE NULL`): Ngày dự kiến thu hoạch (hiển thị `d/m/Y` khi tình trạng là `sap_thu_hoach`).
  - `chung_nhan` (`SET('vietgap', 'globalgap', 'huu_co', 'ocop') NULL`): Tiêu chuẩn chất lượng nông sản đã được chứng nhận.
  - `an` (`TINYINT(1) NOT NULL DEFAULT 0`): `0` = Đang hiển thị trên sàn; `1` = Ẩn (xóa mềm để bảo toàn lịch sử đơn hàng cũ).
  - `ngay_tao` (`TIMESTAMP DEFAULT CURRENT_TIMESTAMP`): Ngày đăng nông sản.

#### 4. Bảng `don_hang` (Đơn đặt mua & Thương lượng giá)

- **Khóa chính:** `ma_don_hang` (`INT AUTO_INCREMENT`)
- **Khóa ngoại:** `ma_nguoi_mua` (`INT NOT NULL`), `ma_san_pham` (`INT NOT NULL`)
- **Các trường dữ liệu:**
  - `so_luong` (`DECIMAL(10,2) NOT NULL`): Số lượng người mua muốn đặt.
  - `don_vi` (`VARCHAR(20) NOT NULL DEFAULT 'kg'`): Đơn vị tính khối lượng đặt hàng.
  - `gia` (`DECIMAL(12,2) NOT NULL`): **Cột giá thương lượng duy nhất**: Ở `moi` là giá người mua đề nghị; ở `da_bao_gia` là giá nông hộ báo lại; ở `da_xac_nhan` là giá chốt chính thức.
  - `don_vi_gia` (`VARCHAR(20) NOT NULL DEFAULT 'kg'`): Đơn vị định giá tương ứng.
  - `trang_thai` (`ENUM('moi', 'da_bao_gia', 'da_xac_nhan', 'hoan_thanh', 'da_huy') NOT NULL DEFAULT 'moi'`): Trạng thái tiến độ đơn.
  - `ghi_chu` (`VARCHAR(255) NULL`): Yêu cầu đóng gói, giao nhận hoặc lưu ý từ người mua.
  - `ngay_tao` (`TIMESTAMP DEFAULT CURRENT_TIMESTAMP`): Thời điểm gửi đơn.

#### 5. Bảng `nhu_cau` (Nhu cầu thị trường cần thu mua)

- **Khóa chính:** `ma_nhu_cau` (`INT AUTO_INCREMENT`)
- **Khóa ngoại:** `ma_nguoi_mua` (`INT NOT NULL`), `ma_danh_muc` (`INT NOT NULL`)
- **Các trường dữ liệu:**
  - `so_luong` (`DECIMAL(10,2) NOT NULL`): Khối lượng cần thu mua sỉ.
  - `don_vi` (`VARCHAR(20) NOT NULL DEFAULT 'kg'`): Đơn vị tính (`tấn`, `tạ`, `kg`...).
  - `gia_toi_da` (`DECIMAL(12,2) NULL`): Mức giá trần người mua có thể thanh toán (phục vụ chấm điểm Smart Matching %).
  - `tinh` (`VARCHAR(50) NOT NULL`): Tỉnh thành nhận hàng (ưu tiên ghép nối nông hộ cùng địa phương).
  - `han_can` (`DATE NOT NULL`): Ngày kết thúc cần nguồn hàng (so khớp với ngày dự kiến thu hoạch).
  - `ngay_tao` (`TIMESTAMP DEFAULT CURRENT_TIMESTAMP`): Thời điểm đăng tin nhu cầu.

#### 6. Bảng `thong_bao` (Hệ thống Thông báo nội bộ)

- **Khóa chính:** `ma_thong_bao` (`INT AUTO_INCREMENT`)
- **Khóa ngoại:** `ma_nguoi_nhan` (`INT NOT NULL`)
- **Các trường dữ liệu:**
  - `noi_dung` (`VARCHAR(255) NOT NULL`): Thông điệp thông báo biến động đơn hàng hoặc hồ sơ tài khoản.
  - `da_doc` (`TINYINT(1) NOT NULL DEFAULT 0`): `0` = Chưa đọc (hiện chấm đỏ trên chuông header); `1` = Đã đọc.
  - `ngay_tao` (`TIMESTAMP DEFAULT CURRENT_TIMESTAMP`): Thời điểm phát sinh thông báo.

## 5. HƯỚNG DẪN CÀI ĐẶT & TRIỂN KHAI

### 5.1. Yêu cầu môi trường

- Máy tính đã cài đặt **XAMPP** (hoặc WampServer, Laragon) hỗ trợ **PHP $\ge$ 7.4 hoặc 8.x**.
- Trình quản lý cơ sở dữ liệu **MySQL**
- Trình duyệt web (Google Chrome, Microsoft Edge, Firefox).

### 5.2. Các bước cài đặt chi tiết

#### Bước 1: Sao chép mã nguồn vào thư mục XAMPP

Sao chép toàn bộ thư mục dự án `nongho-app` vào thư mục web của XAMPP:

```
C:\xampp\htdocs\nongho-app
```

_(Nếu cài XAMPP ở ổ đĩa `D:\`, đường dẫn sẽ là `D:\xampp\htdocs\nongho-app`)_.

#### Bước 2: Khởi động máy chủ Web & Database

1. Mở ứng dụng **XAMPP Control Panel**.
2. Nhấn nút **Start** ở cả 2 dịch vụ: **Apache** và **MySQL** (hiển thị trạng thái màu xanh lá cây là được).

#### Bước 3: Nhập Cơ sở Dữ liệu (Import Database)

1. Mở trình duyệt web và truy cập vào trang: `http://localhost/phpmyadmin`
2. Nhấn vào thẻ **Databases (Cơ sở dữ liệu)**, tạo mới một CSDL với:
   - Tên database: `nong_ho_db`
   - Bảng mã (Collation): chọn `utf8mb4_unicode_ci` $\rightarrow$ Nhấn **Create (Tạo)**.
3. Chọn database `nong_ho_db` vừa tạo bên cột trái, nhấn vào thẻ **Import (Nhập)** ở thanh trên cùng.
4. Bấm **Choose File (Chọn tệp)** và dẫn đến tệp nong_ho_db.sql trong dự án:
   ```
   C:\xampp\htdocs\nongho-app\database\nong_ho_db.sql
   Nếu thư mục dự án lưu trong XAMPP ở ổ D: thì vào ổ D: tìm.
   ```
5. Cuộn xuống cuối trang và bấm nút **Import (Nhập)**. Hệ thống sẽ tạo đủ 6 bảng và nạp sẵn dữ liệu mẫu thử nghiệm hoàn chỉnh.

#### Bước 4: Kiểm tra cấu hình kết nối

Mở tệp [config.php](file:///d:/xampp/htdocs/nongho-app/config.php), mặc định kết nối XAMPP chuẩn là:

```php
$may_chu   = "localhost";
$tai_khoan = "root";
$mat_khau  = "";          // XAMPP mặc định mật khẩu rỗng
$csdl      = "nong_ho_db";
```

_Nếu bạn dùng mật khẩu MySQL khác, hãy chỉnh sửa biến `$mat_khau` cho tương ứng._

#### Bước 5: Truy cập hệ thống

Mở trình duyệt và truy cập vào địa chỉ:

```Markdown
http://localhost/nongho-app/
```

---

## 6. TÀI KHOẢN THỬ NGHIỆM SẴN CÓ

Dữ liệu mẫu trong tệp [nong_ho_db.sql](file:///d:/xampp/htdocs/nongho-app/database/nong_ho_db.sql) đã được thiết lập sẵn đầy đủ

> 🔑 **MẬT KHẨU CHUNG CHO TẤT CẢ CÁC TÀI KHOẢN MẪU:** `110123117@`
> _(Quy ước chung để tránh nông hộ không rành viết dấu đặt tên nhiều kiểu khác nhau: Tên tài khoản luôn viết thường không dấu)._

---

## 7. KỊCH BẢN CÓ THỂ KIỂM THỬ

#### 1. Trải nghiệm Sàn nông sản & Bộ lọc Tỉnh Động:

1. Truy cập `http://localhost/nongho-app/` khi chưa đăng nhập.
2. Thử lọc theo danh mục: Bấm chip **Trái cây** hoặc **Rau củ sạch**.
3. Bấm vào dropdown **Tất cả tỉnh thành**: Nhận thấy danh sách chỉ hiện các tỉnh thực tế có nông hộ (`Vĩnh Long`, `Tiền Giang`), chứng minh cơ chế lọc động hoạt động chuẩn xác.
4. Bấm vào xem chi tiết một sản phẩm: Số điện thoại của nhà vườn hiển thị dạng che `0912.xxx.xxx`. Bấm nút "Mua hàng" sẽ nhận được Toast thông báo lịch sự yêu cầu đăng nhập.

#### 2. Trải nghiệm Luồng Giỏ Hàng & Thương lượng giá (Người Mua $\rightarrow$ Nông Hộ):

1. Đăng nhập bằng tài khoản người mua: `phuocloc` / `110123117@`.
2. Biểu tượng **Giỏ Hàng** xuất hiện trên thanh Header.
3. Chọn sản phẩm **"Bưởi Da Xanh Ruột Đỏ"** $\rightarrow$ Bấm **Thêm vào giỏ**.
4. Vào trang Giỏ hàng: Đổi số lượng thành `200` kg, nhập mức giá đề xuất `42000` đ/kg và ghi chú $\rightarrow$ Bấm **Gửi Đơn Yêu Cầu Thu Mua**.
5. Đăng xuất và đăng nhập tài khoản nông hộ: `dangkhoi` / `110123117@`.
6. Chuông thông báo góc phải sáng đèn. Vào menu **Đơn đặt hàng**: Đơn hàng mới nằm ở Tab **Đang xử lý**.
7. Nông hộ bấm nút **Xác nhận**: Đơn chuyển sang Tab **Đã xác nhận**, đồng thời sản lượng kho của Bưởi Da Xanh tự động giảm đi đúng 200 kg.
8. Bấm **Hoàn thành giao dịch** khi giao hàng xong: Đơn chuyển sang Tab **Lịch sử**, tăng số đơn uy tín cho nhà vườn.

#### 3. Trải nghiệm Thuật toán Gợi ý Ghép Nối (Smart Matching %):

1. Đăng nhập tài khoản nông hộ `dangkhoi` $\rightarrow$ Truy cập menu **"Nhu cầu thị trường"** (`?trang=nong_ho_nhu_cau`).
2. Xem thẻ nhu cầu thu mua của cửa hàng Phước Lộc (Cần mua 1.5 tấn trái cây tại Vĩnh Long).
3. Hệ thống hiển thị huy hiệu **Phù hợp 90%** (do khớp ngành Trái cây, đủ sản lượng kho 3.5 tấn $\ge$ 1.5 tấn, cùng địa bàn Vĩnh Long). Nông hộ có thể bấm xem chi tiết số điện thoại của người mua để xúc tiến hợp đồng.

#### 4. Trải nghiệm Quản trị & Cấp mật khẩu tạm:

1. Đăng nhập tài khoản quản trị: `admin` / `110123117@`.
2. Kiểm tra mục **Nông hộ chờ duyệt**: Hồ sơ của `minhnhut` (Tiền Giang) hiển thị tại đây kèm Action Pill `[Duyệt hồ sơ]`.
3. Kiểm tra mục **Quản lý tài khoản**: Thử nghiệm bấm nút **Cấp mật khẩu tạm** cho một tài khoản. Hệ thống sinh mật khẩu ngẫu nhiên trên màn hình.
4. Đăng nhập tài khoản `thuyvy` / `110123117@` (tài khoản mẫu đang có cờ `phai_doi_mk = 1`):
   - Ngay lập tức người dùng bị điều hướng đến màn hình `index.php?trang=doi_mk_tam`.
   - Nếu cố ý gõ URL khác, hệ thống vẫn kiên quyết chuyển hướng về trang đổi mật khẩu.
   - Nhập mật khẩu mới thỏa mãn độ mạnh $\rightarrow$ Hệ thống cập nhật thành công và mở khóa truy cập toàn diện.

---

## 8. TỔNG KẾT & HƯỚNG PHÁT TRIỂN

- Đáp ứng đầy đủ các tiêu chuẩn ban đầu đã đề ra về tính thực tiễn, tính ứng dụng và độ hoàn thiện giao diện.
- Mã nguồn viết theo cấu trúc MVC, dễ mở rộng, bảo mật cao và chú thích đầy đủ bằng tiếng Việt.
- Đảm bảo tính khả thi cao khi triển khai tại các địa phương, hỗ trợ tích cực cho quá trình chuyển đổi số nông nghiệp.

**Hướng phát triển tiếp theo:**

- Tích hợp gửi thông báo qua Zalo khi có đơn hàng mới phát sinh.
- Mở rộng chức năng nhật ký canh tác số điện tử cho nông hộ theo từng lô mùa vụ.
- Phát triển ứng dụng di động (Mobile App) dành riêng cho bà con nông dân tại vườn.

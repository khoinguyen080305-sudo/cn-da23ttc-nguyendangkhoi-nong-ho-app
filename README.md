# Ứng Dụng Hỗ Trợ Nông Hộ Tiếp Cận Thị Trường Và Tiêu Thụ Sản Phẩm

---

## 📌 THÔNG TIN ĐỒ ÁN

- **Tên đề tài:** Xây dựng ứng dụng hỗ trợ nông hộ tiếp cận thị trường và tiêu thụ sản phẩm
- **Sinh viên thực hiện:** NGUYỄN ĐĂNG KHÔI
- **Mã số sinh viên:** 110123117
- **Lớp:** DA23TTC
- **Trường:** Trường Kỹ thuật & Công nghệ – Đại học Trà Vinh
- **Công nghệ nền tảng:** PHP 8.4.27 thuần theo mô hình kiến trúc MVC, CSDL MariaDB / MySQL (InnoDB 6 bảng), Bootstrap 5.3, JavaScript.

---

## 🚀 HƯỚNG DẪN CÀI ĐẶT & CHẠY DỰ ÁN

Bạn chỉ cần thực hiện đúng **4 bước đơn giản** dưới đây là có thể chạy ngay ứng dụng trên máy tính của mình:

### Bước 1: Chuẩn bị phần mềm & Đặt mã nguồn

1. Cài đặt phần mềm **XAMPP** (hỗ trợ PHP 7.4 hoặc PHP 8.x).
2. Sao chép (copy) toàn bộ thư mục `nongho-app` đặt vào thư mục `htdocs` của XAMPP:
   - Nếu cài XAMPP ở ổ `C`: đường dẫn là `C:\xampp\htdocs\nongho-app`
   - Nếu cài XAMPP ở ổ `D`: đường dẫn là `D:\xampp\htdocs\nongho-app`

### Bước 2: Khởi động máy chủ XAMPP

1. Mở ứng dụng **XAMPP Control Panel**.
2. Nhấn nút **Start** tại dòng **Apache**.
3. Nhấn nút **Start** tại dòng **MySQL**.  
   _(Đảm bảo cả 2 dịch vụ đều chuyển sang **màu xanh lá cây**)._

### Bước 3: Tạo Cơ Sở Dữ Liệu & Nạp dữ liệu mẫu

1. Mở trình duyệt web (Chrome, Cốc Cốc, Edge), truy cập địa chỉ:  
   👉 **`http://localhost/phpmyadmin/`**
2. Nhấp vào mục **Databases** (hoặc _Cơ sở dữ liệu_):
   - Nhập tên CSDL: **`nong_ho_db`**
   - Bảng mã (Collation): chọn **`utf8mb4_unicode_ci`**
   - Bấm nút **Create** (Tạo).
3. Nhấp chọn cơ sở dữ liệu `nong_ho_db` vừa tạo ở thanh danh sách bên trái.
4. Nhấn vào thẻ **Import** (Nhập) ở menu phía trên.
5. Tại mục chọn tệp, bấm **Choose File** (hoặc _Chọn tệp_) và chọn đến file:  
   👉 **`nongho-app/database/nong_ho_db.sql`**
6. Cuộn xuống dưới cùng và bấm nút **Import** (Nhập). Chờ 3 giây để hệ thống nạp toàn bộ cấu trúc 6 bảng và dữ liệu mẫu.

### Bước 4: Mở ứng dụng trên trình duyệt

Mở tab mới trên trình duyệt và truy cập:  
👉 **`http://localhost/nongho-app/`**

> **Lưu ý cấu hình kết nối:** Mặc định tệp `config.php` đã cấu hình sẵn cho XAMPP (`root`, mật khẩu trống `""`, CSDL `nong_ho_db`). Nếu MySQL trên máy bạn có đặt mật khẩu riêng, hãy mở file `config.php` để điền mật khẩu tương ứng.

---

## 🔑 DANH SÁCH TÀI KHOẢN MẪU ĐỂ TEST NGAY (NÔNG HỘ & NGƯỜI MUA)

Hệ thống sử dụng **Số điện thoại (10 chữ số)** làm tài khoản đăng nhập (không cần Email). Dưới đây là danh sách các tài khoản người dùng mẫu được cài đặt sẵn trong cơ sở dữ liệu để bạn có thể đăng nhập thử nghiệm ngay lập tức:

> 🔐 **MẬT KHẨU MẶC ĐỊNH DÙNG CHUNG:** **`110123117@`**

| Vai trò                         | Số điện thoại |   Mật khẩu   | Tên tài khoản                 | Mục đích kiểm thử tính năng                                                                             |
| :------------------------------ | :-----------: | :----------: | :---------------------------- | :------------------------------------------------------------------------------------------------------ |
| **👨‍🌾 Nông Hộ (Chủ vườn 1)**     | `0900000002`  | `110123117@` | Nông Hộ Anh Khôi (Vĩnh Long)  | Có sẵn bưởi, sầu riêng; tiếp nhận đơn, đàm phán giá, chốt đơn trừ kho, xem gợi ý Smart Matching %.      |
| **👨‍🌾 Nông Hộ (Chủ vườn 2)**     | `0900000003`  | `110123117@` | Nông Hộ Anh Huy (Cần Thơ)     | Đăng bán nông sản mới, quản lý mùa vụ (sắp thu hoạch, huy hiệu đếm ngược ngày cắt trái).                |
| **👨‍🌾 Nông Hộ (Chủ vườn 3)**     | `0900000004`  | `110123117@` | Trang Trại Minh Nhựt (Cà Mau) | Thử nghiệm tính năng **Ẩn / Hiện kho hàng** (món hàng bị ẩn sẽ tự động chìm xuống đáy danh sách).       |
| **🛒 Người Mua (Thương lái 1)** | `0900000005`  | `110123117@` | Cửa Hàng Phước Lộc (Đồng Nai) | Có Giỏ Hàng gom hàng, gửi đơn đề xuất giá thương lượng, xem 4 thẻ KPI chi tiêu & ưu đãi giảm giá.       |
| **🛒 Người Mua (Thương lái 2)** | `0900000006`  | `110123117@` | Trái Cây Thúy Vy (TP.HCM)     | Đang bật cờ `phai_doi_mk = 1` để kiểm thử tính năng **ép đổi mật khẩu bắt buộc** khi dùng mật khẩu tạm. |

---

## 📁 CẤU TRÚC THƯ MỤC MÃ NGUỒN MVC

```
nongho-app/
├── index.php                      # Bộ điều phối trung tâm (Router tiếp nhận và chuyển tiếp mọi trang)
├── config.php                     # Cấu hình kết nối CSDL, CSRF, hằng số và các hàm tiện ích dùng chung
│
├── models/                        # TẦNG DỮ LIỆU & NGHIỆP VỤ (MODEL)
│   ├── NguoiDung.php              # Xử lý tài khoản, đăng ký, đăng nhập, phân quyền, cấp mật khẩu tạm
│   ├── DanhMuc.php               # Xử lý danh mục nông sản, cơ chế chống trùng lặp
│   ├── SanPham.php               # Xử lý nông sản, bộ lọc AJAX, kho hàng, trừ kho, dọn rác ảnh cũ
│   ├── DonHang.php               # Quản lý vòng đời thương lượng 3 bước, tính KPI doanh thu & tiết kiệm sỉ
│   ├── NhuCau.php                # Quản lý tin thu mua, thuật toán ghép nối % (Smart Matching)
│   └── ThongBao.php              # Quản lý lịch sử sự kiện hệ thống
│
├── controllers/                   # TẦNG ĐIỀU KHIỂN (CONTROLLER)
│   ├── AuthController.php        # Điều khiển Đăng ký, Đăng nhập, Đổi mật khẩu
│   ├── SanPhamController.php     # Điều khiển sàn nông sản, xem chi tiết, quản lý kho
│   ├── DonHangController.php     # Điều khiển giỏ hàng, gửi đơn, đàm phán giá, chốt đơn, hoàn thành
│   ├── NhuCauController.php      # Điều khiển đăng tin nhu cầu thu mua và xem gợi ý ghép nối %
│   ├── AdminController.php       # Điều khiển duyệt nông hộ, cấp mật khẩu tạm, quản lý danh mục
│   └── HoSoController.php        # Điều khiển báo cáo thống kê KPI và cập nhật hồ sơ cá nhân
│
├── views/                         # TẦNG GIAO DIỆN (VIEW)
│   ├── layouts/                  # Header tinh gọn, Footer hotline gọi 1-chạm, Mobile dock
│   ├── auth/                     # Màn hình đăng nhập, đăng ký bằng SĐT
│   ├── public/                   # Sàn nông sản trang chủ, chi tiết sản phẩm
│   ├── nong_ho/                  # Bảng kho nông sản, form đăng bán, tab ghép nối %, báo cáo doanh thu
│   ├── nguoi_mua/                # Giỏ hàng thu mua, danh sách tin gom hàng, báo cáo chi tiêu
│   └── admin/                    # Bảng quản trị: duyệt nông hộ, cấp mật khẩu tạm, SĐT gọi 1-chạm
│
├── database/                      # CƠ SỞ DỮ LIỆU
│   └── nong_ho_db.sql            # File xuất CSDL chuẩn 6 bảng InnoDB kèm sẵn dữ liệu mẫu
│
├── assets/                        # TÀI NGUYÊN TĨNH
│   ├── css/style.css             # CSS toàn hệ thống, Dark Mode, Mobile tabs, Badge SĐT
│   ├── js/app.js                 # Xử lý AJAX ngầm không giật trang, Back-to-top, Toast thông báo
│   └── uploads/                  # Nơi lưu trữ ảnh nông sản đã được nén tự động
│
├── phan_tich.md                   # TÀI LIỆU PHÂN TÍCH CHI TIẾT (Use Case tổng quan, Class Diagram, ERD)
└── README.md                      # TÀI LIỆU HƯỚNG DẪN CÀI ĐẶT & SỬ DỤNG (FILE NÀY)
```

---

## 🛠️ XỬ LÝ SỰ CỐ THƯỜNG GẶP

1. **Lỗi không bật được Apache trong XAMPP (trùng cổng 80):**
   - Trong XAMPP Control Panel, tại dòng Apache bấm nút **Config** $
ightarrow$ chọn `httpd.conf`.
   - Tìm dòng `Listen 80` đổi thành `Listen 8080`, lưu lại và bấm **Start** lại.
   - Khi đó, truy cập web qua đường dẫn: `http://localhost:8080/nongho-app/`.
2. **Lỗi "Không thể kết nối cơ sở dữ liệu...":**
   - Kiểm tra nút **MySQL** trong XAMPP đã bấm Start sang màu xanh chưa.
   - Mở file `config.php` kiểm tra lại: database phải tên là `nong_ho_db`, username là `root`, nếu máy tính của bạn có đặt mật khẩu MySQL thì điền vào `$mat_khau`.
3. **Lỗi hiển thị dấu tiếng Việt:**
   - Đảm bảo khi tạo database `nong_ho_db` trong phpMyAdmin đã chọn bảng mã Collation là **`utf8mb4_unicode_ci`** trước khi bấm Import file SQL.

---

Chúc bạn có trải nghiệm tuyệt vời khi khám phá và kiểm thử dự án **Nông Hộ App**! 🌿

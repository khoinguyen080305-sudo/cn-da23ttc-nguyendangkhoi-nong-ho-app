# ĐỒ ÁN CHUYÊN NGÀNH: XÂY DỰNG ỨNG DỤNG HỖ TRỢ NÔNG HỘ TIẾP CẬN THỊ TRƯỜNG VÀ TIÊU THỤ SẢN PHẨM

## 1. THÔNG TIN ĐỒ ÁN

- **Tên đề tài:** Xây dựng ứng dụng hỗ trợ nông hộ tiếp cận thị trường và tiêu thụ sản phẩm
- **Sinh viên thực hiện:** Nguyễn Đăng Khôi
- **Mã số sinh viên (MSSV):** 110123117
- **Lớp:** DA23TTC
- **Công nghệ sử dụng:** PHP (Mô hình MVC), MySQL, HTML5, CSS3, JavaScript (Bootstrap 5).

---

## 2. CÁC TÍNH NĂNG CHÍNH

- **Sàn nông sản và bộ lọc:** Tìm kiếm theo từ khóa, lọc theo danh mục ngành hàng, lọc theo tình trạng mùa vụ và bộ lọc tỉnh thành động (chỉ hiện các tỉnh hiện có hàng đang bán).
- **Theo dõi mùa vụ và ngày thu hoạch:** Quản lý 4 trạng thái (Đang canh tác, Sắp thu hoạch, Còn hàng, Tạm hết hàng). Riêng trạng thái "Sắp thu hoạch" có hiển thị ngày dự kiến thu hoạch (d/m/Y) để người mua đặt trước.
- **Tách biệt đơn vị kho và đơn vị giá:** Quản lý sản lượng kho (tấn, tạ, kg, bó, trái...) độc lập với đơn vị định giá bán (đ/kg, đ/trái, đ/bó...) và tự động quy đổi khi trừ kho.
- **Quy trình đặt hàng và thương lượng giá:** Người mua thêm vào giỏ, nhập số lượng và đề xuất giá mua -> Nông hộ tiếp nhận, có thể Xác nhận đơn (tự động trừ kho), Báo giá lại hoặc Hủy đơn -> Hoàn thành giao dịch.
- **Gợi ý ghép nối cung cầu (Smart Matching %):** Tự động so khớp tin cần mua của người mua với kho hàng của nông hộ theo 5 tiêu chí: danh mục, sản lượng đáp ứng, mức giá, địa bàn cùng tỉnh thành và thời gian thu hoạch.
- **Quản trị hệ thống:** Duyệt hồ sơ nông hộ, khóa/mở tài khoản, quản lý danh mục và cấp mật khẩu tạm kèm cơ chế bắt buộc đổi mật khẩu mới ngay khi đăng nhập.

---

## 3. HƯỚNG DẪN CÀI ĐẶT

### Yêu cầu hệ thống:

- Phần mềm XAMPP (hỗ trợ PHP từ 7.4 trở lên và MySQL / MariaDB).
- Trình duyệt web (Chrome, Edge, Firefox, Cốc Cốc).

### Các bước cài đặt chi tiết:

#### Bước 1: Đặt mã nguồn vào thư mục XAMPP

Sao chép toàn bộ thư mục `nongho-app` vào thư mục `htdocs` của XAMPP:

- Nếu cài XAMPP ở ổ C: `C:\xampp\htdocs\nongho-app`
- Nếu cài XAMPP ở ổ D: `D:\xampp\htdocs\nongho-app`

#### Bước 2: Khởi động máy chủ

1. Mở ứng dụng **XAMPP Control Panel**.
2. Nhấn nút **Start** tại dòng **Apache**.
3. Nhấn nút **Start** tại dòng **MySQL**.
   *(Đảm bảo cả 2 dịch vụ đều chuyển sang màu xanh lá cây).*

#### Bước 3: Tạo cơ sở dữ liệu và nạp dữ liệu mẫu

1. Mở trình duyệt web, truy cập: `http://localhost/phpmyadmin/`
2. Nhấn vào mục **Databases** (Cơ sở dữ liệu) để tạo mới:
   - Tên cơ sở dữ liệu: `nong_ho_db`
   - Bảng mã (Collation): chọn `utf8mb4_unicode_ci`
   - Nhấn **Create** (Tạo).
3. Nhấp chọn cơ sở dữ liệu `nong_ho_db` vừa tạo ở cột bên trái.
4. Nhấn vào thẻ **Import** (Nhập) ở thanh menu phía trên.
5. Tại mục chọn tệp, nhấn **Choose File** (hoặc Chọn tệp) và tìm đến file:
   `nongho-app/database/nong_ho_db.sql`
6. Cuộn xuống cuối trang và nhấn nút **Import** (Nhập).

#### Bước 4: Kiểm tra tệp kết nối cơ sở dữ liệu

Mở tệp `config.php` trong thư mục dự án và kiểm tra các thông số kết nối mặc định:

```php
$may_chu   = "localhost";
$tai_khoan = "root";
$mat_khau  = "";          // XAMPP mặc định để trống
$csdl      = "nong_ho_db";
```

*(Nếu MySQL của bạn có đặt mật khẩu riêng, hãy điền vào biến `$mat_khau`).*

#### Bước 5: Truy cập ứng dụng

Mở trình duyệt và truy cập theo đường dẫn:

```text
http://localhost/nongho-app/
```

---

## 4. BẢNG TÀI KHOẢN THỬ NGHIỆM

Mật khẩu chung cho tất cả tài khoản mẫu là: `110123117@`
*(Quy ước: Tên tài khoản luôn viết thường không dấu).*

| STT | Tài khoản  |   Mật khẩu   |  Vai trò  | Họ tên / Nhà vườn               | Trạng thái | Mục đích kiểm thử                                                                                                                        |
| :-: | :----------- | :------------: | :---------: | :----------------------------------- | :----------: | :-------------------------------------------------------------------------------------------------------------------------------------------- |
|  1  | `admin`    | `110123117@` |    Admin    | Quản Trị Hệ Thống                | Đã duyệt | Duyệt nông hộ mới, khóa tài khoản, cấp mật khẩu tạm, quản lý danh mục.                                                          |
|  2  | `dangkhoi` | `110123117@` |  Nông hộ  | Nông Hộ Anh Khôi (Vĩnh Long)     | Đã duyệt | Đã có sẵn sản phẩm (Bưởi Da Xanh, Dừa Xiêm). Dùng để thử quản lý sản phẩm, tiếp nhận đơn hàng, xem gợi ý ghép nối. |
|  3  | `tuanhuy`  | `110123117@` |  Nông hộ  | Nông Hộ Anh Huy (Châu Thành)     | Đã duyệt | Đã có sản phẩm (Cà Chua Beef, Xà Lách). Dùng để thử tính năng báo giá lại đơn hàng.                                       |
|  4  | `minhnhut` | `110123117@` |  Nông hộ  | Trang Trại Minh Nhựt (Tiền Giang) | Chờ duyệt | Thử nghiệm tính năng Admin phê duyệt hồ sơ nông hộ mới.                                                                            |
|  5  | `phuocloc` | `110123117@` | Người mua | Cửa Hàng TP Phước Lộc           | Đã duyệt | Thử nghiệm giỏ hàng, đề xuất giá khi mua, đăng tin nhu cầu thu mua sỉ.                                                            |
|  6  | `thuyvy`   | `110123117@` | Người mua | Chủ Cửa Hàng Thúy Vy             | Đã duyệt | Thử nghiệm tính năng bắt buộc đổi mật khẩu mới khi đăng nhập bằng mật khẩu tạm.                                             |

---

## 5. HƯỚNG DẪN SỬ DỤNG THEO VAI TRÒ

### 5.1. Dành cho Khách vãng lai (Chưa đăng nhập)

- **Xem sàn nông sản:** Xem danh sách sản phẩm, ảnh chụp, giá niêm yết, tình trạng mùa vụ và số lượng kho.
- **Tìm kiếm và lọc:**
  - Nhập từ khóa tên sản phẩm vào ô tìm kiếm.
  - Nhấp vào danh mục (Trái cây, Rau củ sạch, Lúa gạo...) để xem theo ngành hàng.
  - Lọc theo mùa vụ: Còn hàng, Sắp thu hoạch, Đang canh tác, Tạm hết hàng.
  - Lọc theo tỉnh thành: Dropdown chỉ hiển thị các tỉnh hiện có hàng bán.
- **Xem ngày thu hoạch dự kiến:** Các mặt hàng có trạng thái "Sắp thu hoạch" sẽ hiển thị ngày dự kiến thu hoạch để khách hàng chủ động thời gian đặt.
- **Bảo mật thông tin:** Số điện thoại của nông hộ được che dưới dạng `0912.xxx.xxx`. Khi bấm mua hàng hoặc liên hệ, hệ thống sẽ yêu cầu đăng nhập.
- **Đăng ký:** Bấm nút Đăng ký ở góc trên bên phải để tạo tài khoản Người mua hoặc Nông hộ.

### 5.2. Dành cho Người mua

- **Mua hàng:**
  1. Chọn sản phẩm trên sàn -> bấm "Thêm vào giỏ".
  2. Bấm vào biểu tượng Giỏ hàng trên thanh điều hướng.
  3. Điều chỉnh số lượng cần mua.
  4. Nhập mức giá đề xuất (nếu muốn thương lượng giá sỉ) và nhập ghi chú yêu cầu đóng gói / giao hàng.
  5. Bấm "Gửi Đơn Đặt Hàng".
- **Theo dõi đơn hàng (Menu Đơn Hàng Của Tôi):**
  - Tab "Đang xử lý": Đơn mới gửi hoặc đơn nông hộ vừa báo giá lại. Người mua có thể bấm Chấp nhận giá hoặc Hủy đơn.
  - Tab "Đã xác nhận": Nông hộ đã đồng ý chốt đơn, hàng đang được chuẩn bị.
  - Tab "Lịch sử": Danh sách các đơn đã hoàn thành hoặc đã hủy.
- **Đăng tin nhu cầu thu mua (Menu Tin Nhu Cầu Thu Mua):**
  - Đăng nhu cầu mua số lượng lớn: Chọn danh mục, số lượng, giá tối đa, tỉnh thành và hạn chót cần hàng.
  - Hệ thống sẽ đưa tin này vào bảng gợi ý của các nông hộ phù hợp.

### 5.3. Dành cho Nông hộ

- **Đăng ký và duyệt:** Nông hộ sau khi đăng ký sẽ ở trạng thái chờ duyệt. Sau khi Admin phê duyệt sẽ được quyền đăng bán hàng.
- **Quản lý sản phẩm (Menu Nông Sản Của Tôi):**
  - Thêm sản phẩm mới:
    + Nhập tên, chọn danh mục.
    + Nhập sản lượng kho và chọn đơn vị kho (tấn, tạ, kg, bó, trái, thùng, bao).
    + Nhập giá bán và chọn đơn vị tính giá (đ/kg, đ/trái, đ/bó...). Nếu để trống giá sẽ hiển thị "Liên hệ báo giá".
    + Chọn tình trạng mùa vụ. Nếu chọn "Sắp thu hoạch", nhập thêm ngày dự kiến thu hoạch.
    + Chọn chứng nhận chất lượng: VietGAP, GlobalGAP, Hữu cơ, OCOP.
    + Tải ảnh đại diện và bấm Lưu.
  - Ẩn / Hiện sản phẩm: Bấm nút để tạm ẩn khỏi sàn mà không làm mất dữ liệu đơn hàng cũ.
- **Xử lý đơn đặt hàng (Menu Đơn Đặt Hàng):**
  - Xem đơn khách gửi kèm mức giá khách đề xuất tại tab "Đang xử lý".
  - Có 3 lựa chọn xử lý:
    1. Xác nhận đơn: Đồng ý bán theo giá đó -> Hệ thống tự động trừ sản lượng trong kho.
    2. Báo giá lại: Nhập mức giá mới gửi lại cho người mua thương lượng.
    3. Hủy đơn: Từ chối đơn nếu không đáp ứng được.
  - Sau khi giao hàng xong, bấm "Hoàn thành giao dịch" để chuyển đơn vào lịch sử và tích lũy uy tín.
- **Xem cơ hội thị trường (Menu Gợi Ý Ghép Nối %):**
  - Xem danh sách người mua đang cần thu mua với điểm phù hợp (%) được hệ thống tính tự động.
  - Xem số điện thoại và thông tin người mua để chủ động liên hệ chào hàng.

### 5.4. Dành cho Quản trị viên (Admin)

- Đăng nhập bằng tài khoản `admin`.
- **Duyệt nông hộ mới:** Tại mục "Hồ sơ nông hộ chờ duyệt", kiểm tra thông tin và bấm Duyệt hồ sơ hoặc Từ chối.
- **Quản lý tài khoản:** Xem danh sách thành viên, bấm nút Khóa tài khoản vi phạm hoặc Mở khóa lại.
- **Cấp mật khẩu tạm:** Khi thành viên quên mật khẩu, bấm nút "Cấp mật khẩu tạm". Hệ thống tạo mật khẩu ngẫu nhiên để gửi cho người dùng, đồng thời kích hoạt cờ bắt buộc đổi mật khẩu mới ngay khi họ đăng nhập lại.
- **Quản lý danh mục:** Thêm ngành hàng nông sản mới hoặc xóa danh mục không còn sử dụng.

---

## 6. KỊCH BẢN KIỂM THỬ MẪU

1. **Kiểm thử tìm kiếm và lọc tỉnh thành:**

   - Truy cập trang chủ khi chưa đăng nhập.
   - Thử bấm lọc danh mục Trái cây.
   - Bấm chọn dropdown tỉnh thành: chỉ thấy hiển thị Vĩnh Long và Tiền Giang (các tỉnh thực tế đang có hàng).
   - Bấm xem chi tiết một sản phẩm: số điện thoại nông hộ bị che `0912.xxx.xxx`.
2. **Kiểm thử luồng đặt hàng và trừ kho:**

   - Đăng nhập tài khoản người mua: `phuocloc`.
   - Chọn mua sản phẩm "Bưởi Da Xanh Ruột Đỏ", thêm vào giỏ.
   - Vào giỏ hàng, đặt số lượng 200 kg, đề xuất giá 42.000 đ/kg và gửi đơn.
   - Đăng xuất, đăng nhập tài khoản nông hộ: `dangkhoi`.
   - Vào Đơn đặt hàng -> tab Đang xử lý -> bấm "Xác nhận đơn".
   - Kiểm tra lại sản phẩm: kho từ 3.5 tấn tự động giảm xuống còn 3.3 tấn (trừ đúng 200 kg).
3. **Kiểm thử thuật toán gợi ý ghép nối:**

   - Đăng nhập tài khoản nông hộ: `dangkhoi`.
   - Vào menu "Gợi Ý Ghép Nối %".
   - Thấy nhu cầu mua 1.5 tấn trái cây tại Vĩnh Long của khách Phước Lộc đạt độ phù hợp 90%.
4. **Kiểm thử Admin cấp mật khẩu tạm và bắt buộc đổi mật khẩu:**

   - Đăng nhập tài khoản `thuyvy` (tài khoản đang có cờ dùng mật khẩu tạm).
   - Hệ thống lập tức chuyển hướng đến trang đổi mật khẩu mới và không cho phép truy cập trang khác cho đến khi đổi xong.

---

## 7. XỬ LÝ SỰ CỐ THƯỜNG GẶP

1. **Không khởi động được Apache (xung đột cổng 80 hoặc 443):**

   - Trong XAMPP Control Panel, bấm Config của Apache -> chọn `httpd.conf`.
   - Tìm `Listen 80` đổi thành `Listen 8080`hoặc cổng nào dễ nhớ không trùng với các cổng khác trên ứng dụng, lưu lại rồi Start lại Apache.
   - Truy cập qua địa chỉ: `http://localhost:8080/nongho-app/`.
2. **Không kết nối được CSDL ("Lỗi: Không thể kết nối cơ sở dữ liệu..."):**

   - Kiểm tra nút MySQL trong XAMPP đã Start sang màu xanh chưa.
   - Mở tệp `config.php` kiểm tra lại tên database có đúng là `nong_ho_db` và mật khẩu MySQL tương ứng với máy tính của bạn.
3. **Lỗi font chữ tiếng Việt khi xem trong CSDL:**

   - Đảm bảo khi tạo database trong phpMyAdmin đã chọn Collation là `utf8mb4_unicode_ci` trước khi nhập file SQL.
4. **Quên mật khẩu tài khoản:**

   - Đăng nhập tài khoản `admin` -> Vào Bảng Quản Trị -> Tìm tài khoản đó và bấm nút "Cấp mật khẩu tạm".
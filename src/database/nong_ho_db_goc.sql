-- =========================================================
-- CƠ SỞ DỮ LIỆU: NÔNG HỘ APP (nong_ho_db)
-- Gọn nhẹ - Dễ hiểu - Chuẩn nghiệp vụ
-- Quy chuẩn: Đăng ký & Đăng nhập 100% bằng Số điện thoại (sdt), loại bỏ hoàn toàn trường tai_khoan
-- =========================================================
CREATE DATABASE IF NOT EXISTS nong_ho_db CHARACTER
SET
    utf8mb4 COLLATE utf8mb4_unicode_ci;

USE nong_ho_db;

SET
    FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS thong_bao;

DROP TABLE IF EXISTS nhu_cau;

DROP TABLE IF EXISTS don_hang;

DROP TABLE IF EXISTS san_pham;

DROP TABLE IF EXISTS danh_muc;

DROP TABLE IF EXISTS nguoi_dung;

SET
    FOREIGN_KEY_CHECKS = 1;

-- 1. BẢNG NGƯỜI DÙNG (nguoi_dung)
-- sdt: Số điện thoại dùng làm tài khoản đăng nhập chính duy nhất (UNIQUE, NOT NULL)
-- phai_doi_mk: 1 = vừa được Admin cấp mk tạm, buộc đổi mk ngay sau khi đăng nhập
-- tinh: nông hộ khai báo, hệ thống lấy DISTINCT để lọc sàn tự động
CREATE TABLE
    nguoi_dung (
        ma_nguoi_dung INT AUTO_INCREMENT PRIMARY KEY,
        sdt VARCHAR(15) NOT NULL UNIQUE,
        mat_khau VARCHAR(255) NOT NULL,
        ho_ten VARCHAR(100) NOT NULL,
        vai_tro ENUM ('nguoi_mua', 'nong_ho', 'admin') NOT NULL,
        trang_thai ENUM ('cho_duyet', 'da_duyet', 'khoa') NOT NULL DEFAULT 'cho_duyet',
        phai_doi_mk TINYINT (1) NOT NULL DEFAULT 0,
        tinh VARCHAR(50) NULL,
        dia_chi VARCHAR(255) NULL,
        ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- 2. BẢNG DANH MỤC (danh_muc)
CREATE TABLE
    danh_muc (
        ma_danh_muc INT AUTO_INCREMENT PRIMARY KEY,
        ten_danh_muc VARCHAR(100) NOT NULL UNIQUE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- 3. BẢNG SẢN PHẨM (san_pham)
-- Tách riêng: Đơn vị sản lượng (tấn, kg, bó...) và Đơn vị tính giá (đ/kg, đ/bó...)
-- gia = NULL nghĩa là "Liên hệ báo giá"
CREATE TABLE
    san_pham (
        ma_san_pham INT AUTO_INCREMENT PRIMARY KEY,
        ma_nong_ho INT NOT NULL,
        ma_danh_muc INT NOT NULL,
        ten VARCHAR(150) NOT NULL,
        san_luong DECIMAL(10, 2) NOT NULL DEFAULT 0,
        don_vi_san_luong VARCHAR(20) NOT NULL DEFAULT 'kg',
        gia DECIMAL(12, 2) NULL,
        don_vi_gia VARCHAR(20) NOT NULL DEFAULT 'kg',
        anh VARCHAR(255) NULL,
        tinh_trang ENUM ('con_hang', 'sap_thu_hoach', 'het_hang') NOT NULL DEFAULT 'con_hang',
        ngay_thu_hoach DATE NULL,
        chung_nhan
        SET
            ('vietgap', 'globalgap', 'huu_co', 'ocop') NULL,
            an TINYINT (1) NOT NULL DEFAULT 0,
            ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (ma_nong_ho) REFERENCES nguoi_dung (ma_nguoi_dung) ON DELETE CASCADE,
            FOREIGN KEY (ma_danh_muc) REFERENCES danh_muc (ma_danh_muc)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- 4. BẢNG ĐƠN HÀNG (don_hang)
-- Chỉ người mua mới tạo đơn (từ giỏ hàng)
-- Giá 1 cột: ở 'moi' là giá đề nghị, 'da_bao_gia' là giá nông hộ báo lại, 'da_xac_nhan' là giá chốt (trừ kho)
CREATE TABLE
    don_hang (
        ma_don_hang INT AUTO_INCREMENT PRIMARY KEY,
        ma_nguoi_mua INT NOT NULL,
        ma_san_pham INT NOT NULL,
        so_luong DECIMAL(10, 2) NOT NULL,
        don_vi VARCHAR(20) NOT NULL DEFAULT 'kg',
        gia DECIMAL(12, 2) NOT NULL,
        gia_ban_dau DECIMAL(12, 2) NULL DEFAULT NULL,
        don_vi_gia VARCHAR(20) NOT NULL DEFAULT 'kg',
        trang_thai ENUM (
            'moi',
            'da_bao_gia',
            'da_xac_nhan',
            'hoan_thanh',
            'da_huy'
        ) NOT NULL DEFAULT 'moi',
        ghi_chu VARCHAR(255) NULL,
        ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (ma_nguoi_mua) REFERENCES nguoi_dung (ma_nguoi_dung) ON DELETE CASCADE,
        FOREIGN KEY (ma_san_pham) REFERENCES san_pham (ma_san_pham)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- 5. BẢNG NHU CẦU THỊ TRƯỜNG (nhu_cau)
-- Người mua đăng tìm nguồn hàng, nông hộ xem gợi ý ghép nối %
CREATE TABLE
    nhu_cau (
        ma_nhu_cau INT AUTO_INCREMENT PRIMARY KEY,
        ma_nguoi_mua INT NOT NULL,
        ma_danh_muc INT NOT NULL,
        ten_nong_san VARCHAR(100) NULL,
        so_luong DECIMAL(10, 2) NOT NULL,
        don_vi VARCHAR(20) NOT NULL DEFAULT 'kg',
        gia_toi_da DECIMAL(12, 2) NULL,
        tinh VARCHAR(50) NOT NULL,
        han_can DATE NOT NULL,
        ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (ma_nguoi_mua) REFERENCES nguoi_dung (ma_nguoi_dung) ON DELETE CASCADE,
        FOREIGN KEY (ma_danh_muc) REFERENCES danh_muc (ma_danh_muc)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- 6. BẢNG THÔNG BÁO (thong_bao)
CREATE TABLE
    thong_bao (
        ma_thong_bao INT AUTO_INCREMENT PRIMARY KEY,
        ma_nguoi_nhan INT NOT NULL,
        noi_dung VARCHAR(255) NOT NULL,
        da_doc TINYINT (1) NOT NULL DEFAULT 0,
        ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (ma_nguoi_nhan) REFERENCES nguoi_dung (ma_nguoi_dung) ON DELETE CASCADE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- =========================================================
-- DỮ LIỆU MẪU ĐỒNG BỘ - CHUẨN XÁC
-- Định danh tài khoản: 100% bằng Số điện thoại (sdt)
-- Mật khẩu mặc định cho tất cả: 110123117@
-- Hash bcrypt: $2y$10$pUTgm.3Hcbu3ZrIahwWe0ORUzo0Ded9a9/etaPX0fpS6zioUXMm62
-- =========================================================
-- 1. Tài khoản mẫu (Đăng nhập bằng Số điện thoại)
INSERT INTO
    nguoi_dung (
        ma_nguoi_dung,
        sdt,
        mat_khau,
        ho_ten,
        vai_tro,
        trang_thai,
        phai_doi_mk,
        tinh,
        dia_chi
    )
VALUES
    (
        1,
        '0900000001',
        '$2y$10$pUTgm.3Hcbu3ZrIahwWe0ORUzo0Ded9a9/etaPX0fpS6zioUXMm62',
        'Quản Trị Ứng Dụng',
        'admin',
        'da_duyet',
        0,
        NULL,
        NULL
    ),
    (
        2,
        '0900000002',
        '$2y$10$pUTgm.3Hcbu3ZrIahwWe0ORUzo0Ded9a9/etaPX0fpS6zioUXMm62',
        'Nông Hộ Anh Khôi',
        'nong_ho',
        'da_duyet',
        0,
        'Vĩnh Long',
        '123, xã Bình Phú'
    ),
    (
        3,
        '0900000003',
        '$2y$10$pUTgm.3Hcbu3ZrIahwWe0ORUzo0Ded9a9/etaPX0fpS6zioUXMm62',
        'Nông Hộ Anh Huy',
        'nong_ho',
        'da_duyet',
        0,
        'TP. Cần Thơ',
        'Ninh Kiều'
    ),
    (
        4,
        '0900000004',
        '$2y$10$pUTgm.3Hcbu3ZrIahwWe0ORUzo0Ded9a9/etaPX0fpS6zioUXMm62',
        'Trang Trại Minh Nhựt',
        'nong_ho',
        'cho_duyet',
        0,
        'Cà Mau',
        'Xã Đất Mũi'
    ),
    (
        5,
        '0900000005',
        '$2y$10$pUTgm.3Hcbu3ZrIahwWe0ORUzo0Ded9a9/etaPX0fpS6zioUXMm62',
        'Cửa Hàng Thực Phẩm Phước Lộc',
        'nguoi_mua',
        'da_duyet',
        0,
        'Đồng Nai',
        'Thạnh Phú'
    ),
    (
        6,
        '0900000006',
        '$2y$10$pUTgm.3Hcbu3ZrIahwWe0ORUzo0Ded9a9/etaPX0fpS6zioUXMm62',
        'Cửa Hàng Trái Cây Thúy Vy',
        'nguoi_mua',
        'da_duyet',
        1,
        'TP. Hồ Chí Minh',
        'Thủ Đức'
    );

-- 2. Danh mục thiết yếu
INSERT INTO
    danh_muc (ma_danh_muc, ten_danh_muc)
VALUES
    (1, 'Trái cây'),
    (2, 'Rau củ sạch'),
    (3, 'Lúa gạo'),
    (4, 'Nông sản khác');

ALTER TABLE san_pham
ADD COLUMN danh_sach_anh TEXT NULL AFTER anh;
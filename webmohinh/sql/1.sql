-- 1. TẠO BẢNG USERS (Đã sửa)
-- (Gộp file 1.sql, 2.sql, 5.sql và thêm cột 'email')
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE, -- CỘT BỊ THIẾU
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL DEFAULT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. TẠO BẢNG PRODUCTS (Giữ nguyên)
-- (Từ file 4.sql - Rất tốt)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    img VARCHAR(255) NOT NULL,
    `desc` TEXT NULL,
    price VARCHAR(50) NOT NULL,
    specs TEXT NULL,
    warranty TEXT NULL,
    reviews TEXT NULL,
    category VARCHAR(100) NULL,
    sku VARCHAR(100) NULL,
    brand VARCHAR(100) NULL,
    stock VARCHAR(50) DEFAULT 'Còn hàng'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. TẠO BẢNG ORDERS (Đã sửa)
-- (Gộp file 2.sql, 3.sql và thêm các cột từ checkout.php)
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_price INT NOT NULL,
    status VARCHAR(100) NOT NULL DEFAULT 'Đang xử lý',
    payment_method VARCHAR(50) NOT NULL DEFAULT 'cod',
    
    -- CÁC CỘT BỊ THIẾU TỪ CHECKOUT.PHP
    customer_name VARCHAR(255) NOT NULL,
    customer_address TEXT NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_note TEXT NULL,
    
    FOREIGN KEY (username) REFERENCES users(username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. TẠO TÀI KHOẢN ADMIN (Giữ nguyên)
-- (Từ file 5.sql - Rất quan trọng)
-- Lưu ý: Bạn phải tự TẠO tài khoản này bằng trang 'register.php' trước,
-- sau đó chạy lệnh UPDATE này để cấp quyền admin cho nó.
-- (Thay 'admin_username_cua_ban' bằng username bạn vừa đăng ký)

-- UPDATE users
-- SET role = 'admin'
-- WHERE username = 'admin_username_cua_ban';
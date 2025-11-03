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
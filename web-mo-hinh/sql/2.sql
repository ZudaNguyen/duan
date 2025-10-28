CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_price INT NOT NULL,
    status VARCHAR(100) NOT NULL DEFAULT 'Đang xử lý',
    FOREIGN KEY (username) REFERENCES users(username)
);

ALTER TABLE users
ADD phone VARCHAR(20) NULL DEFAULT NULL;
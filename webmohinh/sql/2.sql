-- 4. TẠO BẢNG ORDER_DETAILS (BẢNG BỊ THIẾU)
-- Bảng này lưu các sản phẩm cụ thể trong một đơn hàng
CREATE TABLE order_details (
    detail_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_price INT NOT NULL,
    quantity INT NOT NULL,
    
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
    -- Bạn cũng có thể thêm FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
--
-- Cấu trúc bảng cho `products`
-- (Tổng hợp từ 4.sql và các file admin/edit_product.php)
--
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `desc` text DEFAULT NULL,
  `price` varchar(50) NOT NULL,
  `specs` text DEFAULT NULL,
  `warranty` text DEFAULT NULL,
  `reviews` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `stock` varchar(50) DEFAULT 'Còn hàng',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Cấu trúc bảng cho `users`
-- (Tổng hợp từ 1.sql, 2.sql, 5.sql và code login/register.php)
--
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Cấu trúc bảng cho `orders`
-- (Tổng hợp từ 2.sql, 3.sql và code checkout.php)
--
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `total_price` int(11) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT 'Đang xử lý',
  `payment_method` varchar(50) NOT NULL DEFAULT 'cod',
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_note` text DEFAULT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
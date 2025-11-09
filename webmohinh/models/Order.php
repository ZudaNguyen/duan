<?php
// Tệp: models/Order.php

class Order {
    
    // Thuộc tính private để giữ kết nối CSDL
    private $conn;

    // Hàm dựng: Nhận kết nối CSDL khi tạo đối tượng
    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * Tạo một đơn hàng mới
     * (Logic lấy từ checkout.php)
     */
    public function create($data) {
        // $data là một mảng chứa (username, total_price, payment_method, 
        // fullname, address, phone, email, note)
        
        // 1. Quyết định trạng thái dựa trên phương thức thanh toán
        $status = 'Đang xử lý';
        if ($data['payment_method'] == 'online') {
            $status = 'Chờ thanh toán';
        }

        // 2. Chèn vào CSDL
        $sql = "INSERT INTO orders (username, total_price, status, payment_method, 
                    customer_name, customer_address, customer_phone, customer_email, customer_note) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sisssssss", 
            $data['username'],
            $data['total_price'],
            $status,
            $data['payment_method'],
            $data['fullname'],
            $data['address'],
            $data['phone'],
            $data['email'],
            $data['note']
        );
        
        return $stmt->execute();
    }

    /**
     * Lấy lịch sử đơn hàng của một người dùng
     * (Logic lấy từ user.php)
     */
    public function getHistoryByUsername($username) {
        $order_stmt = $this->conn->prepare("SELECT * FROM orders WHERE username = ? ORDER BY order_date DESC");
        $order_stmt->bind_param("s", $username);
        $order_stmt->execute();
        return $order_stmt->get_result();
    }
}
?>
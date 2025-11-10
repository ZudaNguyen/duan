<?php
// Tệp: models/Order.php (Đã cập nhật)

class Order {
    
    // Thuộc tính private để giữ kết nối CSDL
    private $conn;

    // Hàm dựng: Nhận kết nối CSDL khi tạo đối tượng
    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * Tạo một đơn hàng mới (Đã sửa lỗi)
     * (Logic lấy từ checkout.php)
     */
    public function create($data, $cart_contents) {
        // $data là thông tin khách hàng
        // $cart_contents là giỏ hàng từ Lớp Cart
        
        // 1. Quyết định trạng thái
        $status = ($data['payment_method'] == 'online') ? 'Chờ thanh toán' : 'Đang xử lý';

        // 2. Chèn vào bảng chính 'orders'
        $sql_order = "INSERT INTO orders (username, total_price, status, payment_method, 
                        customer_name, customer_address, customer_phone, customer_email, customer_note) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_order = $this->conn->prepare($sql_order);
        $stmt_order->bind_param("sisssssss", 
            $data['username'], $data['total_price'], $status, $data['payment_method'],
            $data['fullname'], $data['address'], $data['phone'], $data['email'], $data['note']
        );
        
        if (!$stmt_order->execute()) {
            return false; // Lỗi ngay khi tạo đơn hàng
        }

        // 3. LẤY ID CỦA ĐƠN HÀNG VỪA TẠO
        $order_id = $this->conn->insert_id;

        // 4. CHÈN VÀO BẢNG 'order_details'
        $sql_detail = "INSERT INTO order_details (order_id, product_id, product_name, product_price, quantity) 
                       VALUES (?, ?, ?, ?, ?)";
        $stmt_detail = $this->conn->prepare($sql_detail);
        
        // Biến cờ (flag) để kiểm tra lỗi
        $details_success = true; 

        foreach ($cart_contents as $id => $item) {
            $price_numeric = (int)str_replace(['.', 'đ'], '', $item['price']);
            $product_id = (int)$id; 

            $stmt_detail->bind_param("iisii", 
                $order_id, $product_id, $item['name'], $price_numeric, $item['quantity']
            );
            
            // Nếu một chi tiết bị lỗi, gán cờ = false
            if (!$stmt_detail->execute()) {
                $details_success = false; 
            }
        }
        
        // Chỉ trả về TRUE nếu cả đơn hàng VÀ chi tiết đều thành công
        return $details_success; 
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

    // ===============================================
    // CÁC HÀM MỚI CHO ADMIN (ĐÃ THÊM Ở BƯỚC 20)
    // ===============================================

    /**
     * Lấy TẤT CẢ đơn hàng (cho trang admin)
     * (Logic lấy từ admin/index.php)
     */
    public function getAll() {
        return $this->conn->query("SELECT * FROM orders ORDER BY order_date DESC");
    }

    /**
     * Cập nhật trạng thái đơn hàng
     * (Logic lấy từ admin/index.php)
     */
    public function updateStatus($order_id, $new_status) {
        $stmt = $this->conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $new_status, $order_id);
        return $stmt->execute();
    }

    /**
     * Lấy MỘT đơn hàng bằng ID
     * (Logic lấy từ admin/index.php)
     */
    public function findById($order_id) {
         $stmt = $this->conn->prepare("SELECT * FROM orders WHERE order_id = ?");
         $stmt->bind_param("i", $order_id);
         $stmt->execute();
         return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Lấy chi tiết của một đơn hàng
     * (Logic lấy từ admin/index.php)
     */
    public function getOrderDetails($order_id) {
        $stmt = $this->conn->prepare("SELECT * FROM order_details WHERE order_id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        return $stmt->get_result(); // Trả về kết quả để lặp
    }
}
?>
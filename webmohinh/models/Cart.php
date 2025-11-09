<?php

class Cart {
    
    // Hàm dựng: Sẽ tự động khởi động session
    // và tạo giỏ hàng rỗng nếu chưa có
    public function __construct() {
        // Đảm bảo session đã được khởi động
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Khởi tạo giỏ hàng nếu nó không tồn tại
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     * (Logic lấy từ add_to_cart.php)
     */
    public function add($id, $name, $price, $img, $quantity = 1) {
        // Nếu đã có, cộng dồn số lượng
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] += $quantity;
        } else {
            // Nếu chưa có, tạo mới
            $_SESSION['cart'][$id] = [
                'name'     => $name,
                'price'    => $price,
                'img'      => $img,
                'quantity' => $quantity
            ];
        }
    }

    /**
     * Cập nhật số lượng
     * (Logic lấy từ cart.php)
     */
    public function update($id, $quantity) {
        if (isset($_SESSION['cart'][$id])) {
            // Đảm bảo số lượng ít nhất là 1
            $new_quantity = (int)$quantity;
            if ($new_quantity < 1) {
                $new_quantity = 1;
            }
            $_SESSION['cart'][$id]['quantity'] = $new_quantity;
        }
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     * (Logic lấy từ cart.php)
     */
    public function remove($id) {
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
    }
    
    /**
     * Lấy toàn bộ nội dung giỏ hàng
     * (Để hiển thị ra cart.php)
     */
    public function getContents() {
        return $_SESSION['cart'];
    }
    
    /**
     * Tính tổng tiền
     * (Logic lấy từ cart.php và checkout.php)
     */
    public function getTotal() {
        $grand_total = 0;
        foreach ($_SESSION['cart'] as $id => $item) {
            // Chuyển giá từ chuỗi (vd: "429.000đ") sang số
            $price_numeric = (int)str_replace(['.', 'đ'], '', $item['price']);
            $total_item = $price_numeric * $item['quantity'];
            $grand_total += $total_item;
        }
        return $grand_total;
    }

    /**
     * Xóa sạch giỏ hàng
     * (Dùng sau khi đặt hàng thành công ở checkout.php)
     */
    public function clear() {
        $_SESSION['cart'] = [];
        // Hoặc unset($_SESSION['cart']);
    }
    
    /**
     * Kiểm tra giỏ hàng có rỗng không
     */
    public function isEmpty() {
        return empty($_SESSION['cart']);
    }
}
?>
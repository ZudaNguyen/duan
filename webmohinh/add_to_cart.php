<?php
// Tệp: add_to_cart.php (Đã sửa)

// 1. Nạp Lớp Cart.
// Không cần nạp CSDL (connect.php) vì Lớp Cart chỉ dùng Session.
include "models/Cart.php"; 

// 2. Khởi tạo đối tượng Cart
// Hàm __construct() của Lớp Cart sẽ TỰ ĐỘNG session_start()
// và khởi tạo $_SESSION['cart'] nếu chưa có.
$cart_handler = new Cart();

// Khởi tạo một mảng để trả về kết quả (Giữ nguyên)
$response = [
    'success' => false,
    'message' => 'Có lỗi xảy ra.'
];

// 3. Chỉ chạy khi người dùng bấm nút (Giữ nguyên)
if (isset($_POST['add_to_cart'])) {

    // 4. Yêu cầu đăng nhập (Giữ nguyên)
    // (Session đã được start bởi Lớp Cart)
    if (!isset($_SESSION['username'])) {
        $response['message'] = 'Bạn cần đăng nhập để thêm vào giỏ!';
        echo json_encode($response); // Trả về lỗi
        exit();
    }
    
    // 5. Lấy thông tin sản phẩm (Giữ nguyên)
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_img = $_POST['product_img'];
    $product_quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    if ($product_quantity < 1) $product_quantity = 1;

    // 6. Gọi phương thức add() của Lớp Cart
    $cart_handler->add(
        $product_id, 
        $product_name, 
        $product_price, 
        $product_img, 
        $product_quantity
    );
    
    // 7. Gửi về thông báo thành công (Giữ nguyên)
    $response['success'] = true;
    $response['message'] = 'Đã thêm vào giỏ hàng thành công!';
    echo json_encode($response); 
    exit();

} else {
    // Nếu ai đó truy cập file này trực tiếp (Giữ nguyên)
    $response['message'] = 'Hành động không hợp lệ.';
    echo json_encode($response); 
    exit();
}
?>
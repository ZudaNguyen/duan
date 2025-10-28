<?php
session_start();

// Khởi tạo một mảng để trả về kết quả
$response = [
    'success' => false,
    'message' => 'Có lỗi xảy ra.'
];

// 1. Chỉ chạy khi người dùng bấm nút
if (isset($_POST['add_to_cart'])) {

    // 2. Yêu cầu đăng nhập
    if (!isset($_SESSION['username'])) {
        $response['message'] = 'Bạn cần đăng nhập để thêm vào giỏ!';
        echo json_encode($response); // Trả về lỗi
        exit();
    }
    
    // 3. Lấy thông tin sản phẩm
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_img = $_POST['product_img'];
    
    // Lấy số lượng (từ product.php) hoặc mặc định là 1 (từ index.php)
    $product_quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    if ($product_quantity < 1) $product_quantity = 1;

    // 4. Khởi tạo giỏ hàng
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // 5. Thêm vào giỏ (cộng dồn số lượng)
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += $product_quantity;
    } else {
        $_SESSION['cart'][$product_id] = [
            'name' => $product_name,
            'price' => $product_price,
            'img' => $product_img,
            'quantity' => $product_quantity
        ];
    }
    
    // 6. Gửi về thông báo thành công
    $response['success'] = true;
    $response['message'] = 'Đã thêm vào giỏ hàng thành công!';
    echo json_encode($response); // Trả về thành công
    exit();

} else {
    // Nếu ai đó truy cập file này trực tiếp
    $response['message'] = 'Hành động không hợp lệ.';
    echo json_encode($response); // Trả về lỗi
    exit();
}
?>
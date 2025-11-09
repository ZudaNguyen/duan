<?php
// Tệp: cart.php (Đã sửa)
// 1. Nạp Lớp Cart.
// Không cần nạp CSDL (connect.php) vì Lớp Cart chỉ dùng Session.
include "models/Cart.php"; 

// 2. Khởi tạo đối tượng Cart
// Hàm __construct() của Lớp Cart sẽ TỰ ĐỘNG session_start()
$cart_handler = new Cart();

// 3. Yêu cầu đăng nhập (Giữ nguyên)
// (Session đã được start bởi Lớp Cart)
if (!isset($_SESSION['username'])) {
    header("Location: user/login.php");
    exit();
}

// 4. Xử lý logic (Xóa sản phẩm)
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $product_id_to_remove = $_GET['id'];
    
    // 5. Gọi phương thức remove() của Lớp Cart
    $cart_handler->remove($product_id_to_remove);
    
    // Chuyển hướng về giỏ hàng để xóa tham số trên URL
    header("Location: cart.php");
    exit;
}

// 6. Xử lý logic (Cập nhật số lượng)
if (isset($_POST['action']) && $_POST['action'] == 'update' && isset($_POST['id'])) {
    $product_id_to_update = $_POST['id'];
    $new_quantity = (int)$_POST['quantity'];

    // 7. Gọi phương thức update() của Lớp Cart
    // (Lớp Cart đã tự xử lý logic new_quantity < 1)
    $cart_handler->update($product_id_to_update, $new_quantity);
    
    // Chuyển hướng về giỏ hàng
    header("Location: cart.php");
    exit;
}

// 8. Lấy dữ liệu giỏ hàng và tổng tiền từ Lớp Cart
$cart = $cart_handler->getContents();
$grand_total = $cart_handler->getTotal();

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    
    <link rel="stylesheet" href="style-cart.css"> 
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="cart-page"> 
    <div class="cart-container">
        <h1><i class="fas fa-shopping-cart"></i> Giỏ hàng của bạn</h1>
        
        <?php if (empty($cart)): ?>
            <p class="cart-empty">Giỏ hàng của bạn đang trống. <a href="index.php">Quay lại mua sắm</a></p>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Tên</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Tổng cộng</th>
                        <th>Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $id => $item): ?>
                        <?php
                        $price_numeric = (int)str_replace(['.', 'đ'], '', $item['price']);
                        $total_item = $price_numeric * $item['quantity'];
                        ?>
                        <tr>
                            <td><img src="./assets/img/<?php echo htmlspecialchars($item['img']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>"></td>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo htmlspecialchars($item['price']); ?></td>
                            <td>
                                <form method="POST" action="cart.php" class="form-quantity">
                                    </form>
                            </td>
                            <td><?php echo number_format($total_item, 0, ',', '.'); ?>đ</td>
                            <td>
                                <a href="cart.php?action=remove&id=<?php echo $id; ?>" class="btn-remove">
                                    </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <div class="grand-total">
                    <strong>Tổng cộng: <?php echo number_format($grand_total, 0, ',', '.'); ?>đ</strong>
                </div>
                <div class="cart-actions">
                    <a href="index.php#wp-products" class="btn btn-secondary">Tiếp tục mua sắm</a>
                    <a href="checkout.php" class="btn btn-primary">Tiến hành thanh toán</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
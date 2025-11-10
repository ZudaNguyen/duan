<?php
// Tệp: checkout.php (Đã sửa logic OOP)
session_start();
// 1. Nạp các Lớp cần thiết
include "db/connect.php";      // Nạp kết nối $conn
include "models/Cart.php";     // Nạp Lớp Cart
include "models/User.php";     // Nạp Lớp User
include "models/Order.php";    // Nạp Lớp Order

// 2. Khởi tạo đối tượng Cart (sẽ tự động session_start())
$cart_handler = new Cart();

// 3. Yêu cầu đăng nhập (Giữ nguyên)
if (!isset($_SESSION['username'])) {
    header("Location: user/login.php");
    exit();
}

// 4. Kiểm tra giỏ hàng (dùng Lớp Cart)
if ($cart_handler->isEmpty()) {
    header("Location: cart.php"); // Nếu giỏ hàng trống, đá về giỏ hàng
    exit();
}

$username = $_SESSION['username'];

// 5. Khởi tạo các đối tượng CSDL
$user_handler = new User($conn);
$order_handler = new Order($conn);

// 6. Lấy thông tin người dùng (Email, Phone) từ Lớp User
$user = $user_handler->findByUsername($username);

// 7. Lấy giỏ hàng và tổng tiền từ Lớp Cart
$cart = $cart_handler->getContents();
$grand_total = $cart_handler->getTotal(); // Tính tổng tiền 1 lần

// 8. Xử lý khi người dùng ĐẶT HÀNG
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['place_order'])) {
    
    // Gộp tất cả dữ liệu vào một mảng $data
    $data = [
        'username'       => $username,
        'total_price'    => $grand_total, // Dùng tổng tiền đã tính
        'fullname'       => $_POST['fullname'],
        'address'        => $_POST['address'],
        'phone'          => $_POST['phone'],
        'email'          => $_POST['email'],
        'note'           => $_POST['note'],
        'payment_method' => $_POST['payment_method']
    ];

    // 9. Gọi phương thức create() của Lớp Order
    if ($order_handler->create($data, $cart)) {
        // Đặt hàng thành công
        
        // 10. Xóa giỏ hàng bằng Lớp Cart
        $cart_handler->clear();
        
        header("Location: order_success.php");
        exit;
    } else {
        $error_msg = "Đặt hàng thất bại, vui lòng thử lại.";
    }
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán</title>
    <link rel="stylesheet" href="style.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="checkout-page">
    
    <div class="checkout-container">
        <h1><i class="fas fa-credit-card"></i> Thanh toán đơn hàng</h1>

        <?php if(isset($error_msg)): ?>
            <div class="checkout-error" style="color: red; text-align: center; margin-bottom: 15px;"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <div class="checkout-grid">
            
            <div class="checkout-form">
                <h2>Thông tin giao hàng</h2>
                
                <form method="POST" action="checkout.php">
                    <div class="form-group">
                        <label for="fullname">Họ và tên</label>
                        <input type="text" id="fullname" name="fullname" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Số điện thoại</label>
                        <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Địa chỉ nhận hàng</label>
                        <input type="text" id="address" name="address" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="note">Ghi chú</label>
                        <textarea id="note" name="note" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Phương thức thanh toán</label>
                        <div class="payment-options">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="cod" checked>
                                <div class="payment-box"><i class="fas fa-truck"></i><span>Thanh toán khi nhận (COD)</span></div>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="online">
                                <div class="payment-box"><i class="fas fa-credit-card"></i><span>Thanh toán Online</span></div>
                            </label>
                        </div>
                    </div>
                    <button type="submit" name="place_order" class="btn-place-order">
                        <i class="fas fa-check"></i> Xác nhận đặt hàng
                    </button>
                </form>
                </div>

            <div class="order-summary">
                <h2>Đơn hàng của bạn</h2>

                <?php foreach($cart as $id => $item): ?>
                    <?php
                    $price_numeric = (int)str_replace(['.', 'đ'], '', $item['price']);
                    $total_item = $price_numeric * $item['quantity'];
                    ?>
                    <div class="summary-item">
                        <img src="./assets/img/<?php echo htmlspecialchars($item['img']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="item-info">
                            <span class="item-name"><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                            <span class="item-price"><?php echo number_format($total_item, 0, ',', '.'); ?>đ</span>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <hr>
                <div class="summary-total">
                    <span>Tổng cộng</span>
                    <strong><?php echo number_format($grand_total, 0, ',', '.'); ?>đ</strong>
                </div>
                <div class="summary-footer">
                    <a href="cart.php"><i class="fas fa-arrow-left"></i> Quay lại giỏ hàng</a>
                </div>
                </div>

        </div>
    </div>
</body>
</html>
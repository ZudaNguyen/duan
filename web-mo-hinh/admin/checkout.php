<?php
session_start();
include "db/connect.php";

if (!isset($_SESSION['username'])) {
    header("Location: user/login.php");
    exit();
}
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$cart = $_SESSION['cart'];
$username = $_SESSION['username'];
$grand_total = 0;

$stmt_user = $conn->prepare("SELECT email, phone FROM users WHERE username = ?");
$stmt_user->bind_param("s", $username);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

// Xử lý khi ĐẶT HÀNG
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['place_order'])) {
    $fullname = $_POST['fullname'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $note = $_POST['note'];
    $payment_method = $_POST['payment_method']; 

    // 1. Tính tổng tiền
    foreach ($cart as $item) {
        $price_numeric = (int)str_replace(['.', 'đ'], '', $item['price']);
        $grand_total += $price_numeric * $item['quantity'];
    }

    // 2. Lưu vào bảng 'orders' trước
    $status = ($payment_method == 'online') ? 'Chờ thanh toán' : 'Đang xử lý';
    $stmt_order = $conn->prepare("INSERT INTO orders (username, total_price, status, payment_method, customer_name, customer_address, customer_phone, customer_email, customer_note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_order->bind_param("sisssssss", $username, $grand_total, $status, $payment_method, $fullname, $address, $phone, $email, $note);
    
    if ($stmt_order->execute()) {
        // Lấy ID của đơn hàng vừa tạo
        $new_order_id = $conn->insert_id;

        // 3. ⭐ QUAN TRỌNG: Lưu chi tiết từng sản phẩm vào 'order_details'
        $stmt_detail = $conn->prepare("INSERT INTO order_details (order_id, product_id, product_name, product_price, quantity) VALUES (?, ?, ?, ?, ?)");
        foreach ($cart as $pid => $item) {
            $price_num = (int)str_replace(['.', 'đ'], '', $item['price']);
            $stmt_detail->bind_param("iisii", $new_order_id, $pid, $item['name'], $price_num, $item['quantity']);
            $stmt_detail->execute();
        }

        // Xóa giỏ hàng và chuyển hướng
        unset($_SESSION['cart']);
        if ($payment_method == 'online') {
            header("Location: order_success.php?payment=online");
        } else {
            header("Location: order_success.php");
        }
        exit;
    } else {
        $error_msg = "Lỗi khi tạo đơn hàng: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán</title>
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="checkout-page">
    <div class="checkout-container">
        <h1><i class="fas fa-credit-card"></i> Thanh toán đơn hàng</h1>
        <?php if(isset($error_msg)): ?><div class="checkout-error" style="color: red; text-align: center; margin-bottom: 15px;"><?php echo $error_msg; ?></div><?php endif; ?>
        <div class="checkout-grid">
            <div class="checkout-form">
                <h2>Thông tin giao hàng</h2>
                <form method="POST" action="checkout.php">
                    <div class="form-group"><label>Họ và tên</label><input type="text" name="fullname" class="form-control" required></div>
                    <div class="form-group"><label>Số điện thoại</label><input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required></div>
                    <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required></div>
                    <div class="form-group"><label>Địa chỉ nhận hàng</label><input type="text" name="address" class="form-control" required></div>
                    <div class="form-group"><label>Ghi chú</label><textarea name="note" class="form-control"></textarea></div>
                    <div class="form-group">
                        <label>Phương thức thanh toán</label>
                        <div class="payment-options">
                            <label class="payment-option"><input type="radio" name="payment_method" value="cod" checked><div class="payment-box"><i class="fas fa-truck"></i><span>Thanh toán khi nhận (COD)</span></div></label>
                            <label class="payment-option"><input type="radio" name="payment_method" value="online"><div class="payment-box"><i class="fas fa-credit-card"></i><span>Thanh toán Online</span></div></label>
                        </div>
                    </div>
                    <button type="submit" name="place_order" class="btn-place-order"><i class="fas fa-check"></i> Xác nhận đặt hàng</button>
                </form>
            </div>
            <div class="order-summary">
                <h2>Đơn hàng của bạn</h2>
                <?php $total = 0; foreach($cart as $item): 
                    $price_num = (int)str_replace(['.', 'đ'], '', $item['price']);
                    $total += $price_num * $item['quantity'];
                ?>
                    <div class="summary-item">
                        <img src="./assets/img/<?php echo htmlspecialchars($item['img']); ?>" alt="">
                        <div class="item-info">
                            <span class="item-name"><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                            <span class="item-price"><?php echo number_format($price_num * $item['quantity'], 0, ',', '.'); ?>đ</span>
                        </div>
                    </div>
                <?php endforeach; ?>
                <hr>
                <div class="summary-total"><span>Tổng cộng</span><strong><?php echo number_format($total, 0, ',', '.'); ?>đ</strong></div>
                <div class="summary-footer"><a href="cart.php"><i class="fas fa-arrow-left"></i> Quay lại giỏ hàng</a></div>
            </div>
        </div>
    </div>
</body>
</html>
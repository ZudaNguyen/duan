<?php
session_start();
include "db/connect.php"; //

// 1. Yêu cầu đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: user/login.php");
    exit();
}

// 2. Kiểm tra giỏ hàng
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php"); // Nếu giỏ hàng trống, đá về giỏ hàng
    exit();
}

$cart = $_SESSION['cart'];
$username = $_SESSION['username'];
$grand_total = 0;

// 3. Lấy thông tin người dùng (Email, Phone) để điền sẵn
$stmt_user = $conn->prepare("SELECT email, phone FROM users WHERE username = ?");
$stmt_user->bind_param("s", $username);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

// 4. Xử lý khi người dùng ĐẶT HÀNG
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['place_order'])) {
    $fullname = $_POST['fullname'];
    $address = $_POST['address']; // Lấy địa chỉ từ form
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $note = $_POST['note'];
    $payment_method = $_POST['payment_method']; 

    // Tính lại tổng tiền (để bảo mật, luôn tính lại ở server)
    foreach ($cart as $id => $item) {
        $price_numeric = (int)str_replace(['.', 'đ'], '', $item['price']);
        $grand_total += $price_numeric * $item['quantity'];
    }

    // Xử lý logic thanh toán
    if ($payment_method == 'cod') {
        // Nếu là COD, lưu vào CSDL và chuyển đi
        $status = 'Đang xử lý';
        $stmt_order = $conn->prepare("INSERT INTO orders (username, total_price, status, payment_method, customer_name, customer_address, customer_phone, customer_email, customer_note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_order->bind_param("sisssssss", $username, $grand_total, $status, $payment_method, $fullname, $address, $phone, $email, $note);
        
        if ($stmt_order->execute()) {
            unset($_SESSION['cart']);
            header("Location: order_success.php");
            exit;
        } else {
            $error_msg = "Đặt hàng thất bại, vui lòng thử lại.";
        }

    } else if ($payment_method == 'online') {
        // (Tạm thời) Nếu là Online, chúng ta cũng lưu và chuyển đi
        // (Thực tế: đây là nơi gọi API VNPAY/MoMo)
        $status = 'Chờ thanh toán';
        $stmt_order = $conn->prepare("INSERT INTO orders (username, total_price, status, payment_method, customer_name, customer_address, customer_phone, customer_email, customer_note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_order->bind_param("sisssssss", $username, $grand_total, $status, $payment_method, $fullname, $address, $phone, $email, $note);
        
        if ($stmt_order->execute()) {
            unset($_SESSION['cart']);
            header("Location: order_success.php?payment=online"); // Tạm thời chuyển đi
            exit;
        } else {
             $error_msg = "Đặt hàng thất bại, vui lòng thử lại.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán</title>
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                        <label for="note">Ghi chú (tùy chọn)</label>
                        <textarea id="note" name="note" class="form-control"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Phương thức thanh toán</label>
                        <div class="payment-options">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="cod" checked>
                                <div class="payment-box">
                                    <i class="fas fa-truck"></i>
                                    <span>Thanh toán khi nhận hàng (COD)</span>
                                </div>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="online">
                                <div class="payment-box">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Thanh toán Online (VNPAY/MoMo)</span>
                                </div>
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
                <?php $total = 0; ?>
                <?php foreach($cart as $id => $item): ?>
                    <?php
                    $price_numeric = (int)str_replace(['.', 'đ'], '', $item['price']);
                    $total_item = $price_numeric * $item['quantity'];
                    $total += $total_item;
                    ?>
                    <div class="summary-item">
                        <img src="./assets/img/<?php echo htmlspecialchars($item['img']); ?>" alt="">
                        <div class="item-info">
                            <span class="item-name"><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                            <span class="item-price"><?php echo number_format($total_item, 0, ',', '.'); ?>đ</span>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <hr>
                <div class="summary-total">
                    <span>Tổng cộng</span>
                    <strong><?php echo number_format($total, 0, ',', '.'); ?>đ</strong>
                </div>
                <div class="summary-footer">
                    <a href="cart.php"><i class="fas fa-arrow-left"></i> Quay lại giỏ hàng</a>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
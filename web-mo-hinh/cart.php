<?php
session_start();
include "db/connect.php";

// 1. Yêu cầu đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: user/login.php");
    exit();
}

// 2. Xử lý logic (Xóa sản phẩm)
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $product_id_to_remove = $_GET['id'];
    if (isset($_SESSION['cart'][$product_id_to_remove])) {
        unset($_SESSION['cart'][$product_id_to_remove]);
    }
    // Chuyển hướng về giỏ hàng để xóa tham số trên URL
    header("Location: cart.php");
    exit;
}

// 3. Xử lý logic (Cập nhật số lượng)
if (isset($_POST['action']) && $_POST['action'] == 'update' && isset($_POST['id'])) {
    $product_id_to_update = $_POST['id'];
    $new_quantity = (int)$_POST['quantity'];

    // Đảm bảo số lượng ít nhất là 1
    if ($new_quantity < 1) {
        $new_quantity = 1;
    }

    // Cập nhật số lượng trong session
    if (isset($_SESSION['cart'][$product_id_to_update])) {
        $_SESSION['cart'][$product_id_to_update]['quantity'] = $new_quantity;
    }
    
    // Chuyển hướng về giỏ hàng
    header("Location: cart.php");
    exit;
}


// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = $_SESSION['cart'];
$grand_total = 0;
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
                        // Chuyển giá từ chuỗi (vd: "429.000đ") sang số
                        $price_numeric = (int)str_replace(['.', 'đ'], '', $item['price']);
                        $total_item = $price_numeric * $item['quantity'];
                        $grand_total += $total_item;
                        ?>
                        <tr>
                            <td><img src="./assets/img/<?php echo htmlspecialchars($item['img']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>"></td>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo htmlspecialchars($item['price']); ?></td>
                            <td>
                                <form method="POST" action="cart.php" class="form-quantity">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                                    <input type="number" name="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1" class="input-quantity">
                                    <button type="submit" class="btn-update">Cập nhật</button>
                                </form>
                            </td>
                            <td><?php echo number_format($total_item, 0, ',', '.'); ?>đ</td>
                            <td>
                                <a href="cart.php?action=remove&id=<?php echo $id; ?>" class="btn-remove">
                                    <i class="fas fa-trash"></i>
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
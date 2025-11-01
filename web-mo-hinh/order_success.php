<?php
session_start();
// Yêu cầu đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: user/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt hàng thành công</title>
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Style riêng cho trang success */
        body {
            background: #1c1c1c !important;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Poppins', sans-serif;
        }
        .success-container {
            background: #2a2a2a;
            padding: 40px 50px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            border: 1px solid #444;
            text-align: center;
            color: #fff;
        }
        .success-icon {
            font-size: 5rem;
            color: #28a745; /* Màu xanh lá */
            margin-bottom: 20px;
        }
        .success-container h1 {
            color: #fff;
            margin-bottom: 15px;
        }
        .success-container p {
            color: #ddd;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        .btn-success-group a {
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            margin: 0 10px;
        }
        .btn-home {
            background-color: #ff9900;
            color: #000;
        }
        .btn-home:hover {
            background-color: #ffac33;
        }
        .btn-orders {
            background-color: #555;
            color: #fff;
        }
        .btn-orders:hover {
            background-color: #777;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1>Đặt hàng thành công!</h1>
        <p>Cảm ơn bạn đã mua hàng. Chúng tôi sẽ xử lý đơn hàng của bạn sớm nhất.</p>
        <div class="btn-success-group">
            <a href="index.php" class="btn-home">Về trang chủ</a>
            <a href="user/user.php" class="btn-orders">Xem lịch sử đơn hàng</a>
        </div>
    </div>
</body>
</html>
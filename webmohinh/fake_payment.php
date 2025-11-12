<?php
// Tệp: fake_payment.php (Đã sửa lỗi ảnh và CSS)
// Lấy thông tin từ URL
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$total_price = isset($_GET['total_price']) ? (int)$_GET['total_price'] : 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận Thanh toán</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #1c1c1c !important;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Dùng min-height để đảm bảo nội dung không bị cắt */
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 20px; /* Thêm padding tổng thể */
            box-sizing: border-box; /* Tính cả padding vào kích thước */
        }
        .payment-container {
            background: #2a2a2a;
            padding: 40px 50px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            border: 1px solid #444;
            text-align: center;
            color: #fff;
            max-width: 450px;
            width: 100%; /* Đảm bảo chiều rộng responsive */
        }
        .payment-container h1 {
            color: #ff9900;
            margin-bottom: 15px;
            font-size: 2.2rem;
        }
        .payment-container p {
            color: #ddd;
            font-size: 1.1rem;
            margin-bottom: 25px;
        }
        .qr-code {
            width: 250px;
            height: 250px;
            /* background: #fff; */ /* Bỏ background để ảnh QR có thể trong suốt nếu cần */
            padding: 10px;
            /* border-radius: 8px; */ /* Bỏ border-radius để ảnh QR không bị cắt góc */
            margin: 0 auto 25px auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .qr-code img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain; /* Đảm bảo ảnh hiển thị đầy đủ */
            display: block;
        }
        .order-info {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        .order-info strong {
            color: #ff9900;
            font-size: 1.8rem; /* Tăng cỡ chữ tổng tiền */
            display: block;
            margin-top: 5px;
        }
        .order-info small {
            display: block;
            margin-top: 10px;
            color: #aaa;
            font-size: 0.9rem;
        }
        .btn-group {
            display: flex;
            justify-content: center;
            gap: 15px; /* Khoảng cách giữa các nút */
            margin-top: 30px;
        }
        .btn-group a {
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            min-width: 150px; /* Đảm bảo nút đủ rộng */
            flex-grow: 1; /* Cho phép nút giãn ra */
            max-width: 48%; /* Giới hạn chiều rộng khi có 2 nút */
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .btn-home { background-color: #ff9900; color: #000; }
        .btn-home:hover { background-color: #ffac33; }
        .btn-orders { background-color: #555; color: #fff; }
        .btn-orders:hover { background-color: #666; }

        @media (max-width: 600px) {
            .payment-container {
                padding: 30px;
            }
            .payment-container h1 {
                font-size: 1.8rem;
            }
            .order-info strong {
                font-size: 1.5rem;
            }
            .btn-group {
                flex-direction: column; /* Xếp nút chồng lên nhau trên mobile */
                gap: 10px;
            }
            .btn-group a {
                min-width: unset;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <h1><i class="fas fa-qrcode"></i> Thanh toán Đơn hàng</h1>
        <p>Vui lòng quét mã MoMo/Ngân hàng để thanh toán cho đơn hàng của bạn.</p>
        
        <div class="qr-code">
            <img src="./assets/img/usagi.png" alt="Mã QR Thanh toán">
        </div>

        <div class="order-info">
            Số tiền cần thanh toán:
            <strong><?php echo number_format($total_price, 0, ',', '.'); ?>đ</strong>
            <small>(Nội dung: Thanh toan don hang <?php echo $order_id; ?>)</small>
        </div>

        <p style="font-size: 0.9rem; color: #888;">
            (Đây là trang thanh toán giả. Đơn hàng của bạn đã được lưu với trạng thái "Chờ thanh toán".
            Quản trị viên sẽ xác nhận sau.)
        </p>

        <div class="btn-group">
            <a href="index.php" class="btn-home">Về trang chủ</a>
            <a href="user/user.php" class="btn-orders">Xem đơn hàng</a>
        </div>
    </div>
</body>
</html>
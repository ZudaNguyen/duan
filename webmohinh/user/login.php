<?php
// Tệp: user/login.php (Đã sửa)
session_start();
include "../db/connect.php"; // Nạp kết nối $conn
include "../models/User.php";  // Nạp Lớp User

$msg = "";

// 1. Khởi tạo đối tượng User
$user_handler = new User($conn);

// 2. Nếu đã đăng nhập, tự động chuyển đến trang user
if (isset($_SESSION['username'])) {
    header("Location: user.php"); // Chuyển đến trang user.php
    exit;
}

// 3. Xử lý thông báo đăng ký thành công (Lấy từ register.php)
if (isset($_SESSION['register_success'])) {
    $msg = $_SESSION['register_success'];
    // Xóa thông báo sau khi hiển thị
    unset($_SESSION['register_success']); 
}

// 4. Xử lý form POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 5. Gọi phương thức login() của Lớp User
    $user = $user_handler->login($email, $password);

    if ($user) {
        // Đăng nhập thành công, $user là mảng chứa thông tin
        $_SESSION['user'] = $user;
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role']; // Rất quan trọng, lưu vai trò
        
        header("Location: ../index.php"); // Chuyển về trang chủ
        exit;
    } else {
        // Phương thức login() trả về false (sai email hoặc mật khẩu)
        $msg = "Sai email hoặc mật khẩu!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đăng nhập</title>
<link rel="stylesheet" href="style-user.css">
<style>
.form-box {width:400px;margin:80px auto;padding:30px;background:#fff;box-shadow:0 0 10px rgba(0,0,0,0.2);border-radius:10px;}
input[type=email], input[type=password] {width:100%;padding:10px;margin:10px 0;border:1px solid #ccc;border-radius:5px;}
button {width:100%;padding:10px;background:#ff9900;color:#fff;font-weight:bold;border:none;border-radius:5px;cursor:pointer;}
button:hover {background:#e67c00;}
p {text-align:center;}
</style>
</head>
<body>
    <div class="container">
        <div class="header">
            Đăng nhập
        </div>

        <form method="POST" class="form">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <button type="submit">Đăng nhập</button>

            <?php if (!empty($msg)): ?>
                <p style="text-align:center; color:<?php echo (isset($_SESSION['register_success'])) ? 'green' : 'red'; ?>; margin-top:15px;">
                    <?php echo $msg; ?>
                </p>
            <?php endif; ?>

            <div class="switch">
                Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
            </div>
        </form>
    </div>
</body>
</html>
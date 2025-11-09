<?php
// Tệp: user/register.php (Đã sửa)
session_start();
include "../db/connect.php"; // Nạp kết nối $conn
include "../models/User.php";  // Nạp Lớp User

$msg = "";

// 1. Khởi tạo đối tượng User
$user_handler = new User($conn);

// 2. Xử lý form POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 3. Gọi phương thức register() của Lớp User
    $result = $user_handler->register($username, $email, $password);

    if ($result === true) {
        // Đăng ký thành công
        // 4. Tạo session thông báo (Giữ nguyên)
        $_SESSION['register_success'] = "Đăng ký thành công! Vui lòng đăng nhập."; 
        
        // Chuyển hướng về trang login.php (Giữ nguyên)
        header("Location: login.php");
        exit;
    } else {
        // Nếu thất bại, $result sẽ chứa thông báo lỗi (vd: "Email đã được sử dụng!")
        $msg = $result; 
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đăng ký tài khoản</title>
<link rel="stylesheet" href="style-user.css">
<style>
.form-box {width:400px;margin:80px auto;padding:30px;background:#fff;box-shadow:0 0 10px rgba(0,0,0,0.2);border-radius:10px;}
input[type=text], input[type=email], input[type=password] {width:100%;padding:10px;margin:10px 0;border:1px solid #ccc;border-radius:5px;}
button {width:100%;padding:10px;background:#ff9900;color:#fff;font-weight:bold;border:none;border-radius:5px;cursor:pointer;}
button:hover {background:#e67c00;}
p {text-align:center;}
</style>
</head>
<body>
    <div class="container">
        <div class="header">
            Đăng ký tài khoản
        </div>

        <form method="POST" class="form">
            <input type="text" name="username" placeholder="Tên đăng nhập" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <button type="submit">Đăng ký</button>

            <?php if (!empty($msg)): ?>
                <p style="text-align:center; color:red; margin-top:15px;"><?php echo $msg; ?></p>
            <?php endif; ?>

            <div class="switch">
                Đã có tài khoản? <a href="login.php">Đăng nhập</a>
            </div>
        </form>
    </div>
</body>
</html>
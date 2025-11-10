<?php
// Tệp: admin/login.php (Đã sửa)
session_start();
include "../db/connect.php"; // Nạp kết nối $conn
include "../models/User.php";  // Nạp Lớp User

$msg = "";

// 1. Khởi tạo đối tượng User
$user_handler = new User($conn);

// 2. Nếu admin đã đăng nhập, tự động chuyển (Giữ nguyên)
if (isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: index.php");
    exit;
}

// 3. Xử lý form POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 4. Gọi phương thức login() của Lớp User
    $user = $user_handler->login($email, $password);

    if ($user) {
        // 5. KIỂM TRA QUYỀN ADMIN (role)
        if ($user['role'] === 'admin') {
            // Đăng nhập thành công VỚI QUYỀN ADMIN
            $_SESSION['user'] = $user;
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // Quan trọng
            
            header("Location: index.php"); // Chuyển đến trang index.php
            exit;
        } else {
            // Đăng nhập đúng, nhưng không phải admin
            $msg = "Tài khoản này không có quyền truy cập Admin!";
        }
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
<title>Admin Login</title>
<link rel="stylesheet" href="../user/style-user.css"> 
</head>
<body>
    <div class="container">
        <div class="header">
            Admin Login
        </div>

        <form method="POST" class="form">
            <input type="email" name="email" placeholder="Email Admin" required>
            <input type="password" name="password" placeholder="Mật khẩu Admin" required>
            <button type="submit">Đăng nhập Admin</button>
            
            <?php if (!empty($msg)): ?>
                <p style="text-align:center; color:red; margin-top:15px;"><?php echo $msg; ?></p>
            <?php endif; ?>

            <div class="switch">
                <a href="../index.php">Quay về trang chủ</a>
            </div>
        </form>
    </div>
</body>
</html>
<?php
session_start();
include "../db/connect.php"; // Đi lùi 1 cấp để vào db/connect.php

$msg = "";

// Nếu admin đã đăng nhập, tự động chuyển đến trang index (Dashboard)
if (isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: index.php"); // <--- SỬA 1
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // 1. Kiểm tra mật khẩu
        if (password_verify($password, $user['password'])) {
            
            // 2. KIỂM TRA QUYỀN ADMIN (role)
            if ($user['role'] === 'admin') {
                // Đăng nhập thành công VỚI QUYỀN ADMIN
                $_SESSION['user'] = $user;
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role']; // Quan trọng: lưu vai trò
                
                header("Location: index.php"); // <--- SỬA 2: Chuyển đến trang index.php
                exit;
            } else {
                // Đăng nhập đúng, nhưng không phải admin
                $msg = "Tài khoản này không có quyền truy cập Admin!";
            }
        } else {
            $msg = "Sai mật khẩu!";
        }
    } else {
        $msg = "Tài khoản không tồn tại!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>
<!-- Dùng chung file CSS với trang đăng nhập của user -->
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


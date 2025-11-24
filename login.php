<?php
// Tệp: admin/login.php
session_name('ADMIN_SESSION'); // Đặt tên riêng cho session Admin
session_start();
include "../db/connect.php";
include "../models/User.php";

$msg = "";
$is_admin_page = true; 

if (isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: index.php"); 
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_handler = new User($conn);
    
    $username = $_POST['username']; 
    $password = $_POST['password'];

    $user = $user_handler->findByUsername($username);

    if ($user && $user['role'] === 'admin' && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        header("Location: index.php");
        exit();
    } else {
        $msg = "Sai Tên đăng nhập, Mật khẩu hoặc bạn không phải Admin!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập Quản trị</title>
    <link rel="stylesheet" href="admin_style.css">
    <style>
        body { display: flex; justify-content: center; align-items: center; height: 100vh; background: #1c1c1c; }
        .login-box { background: #2a2a2a; padding: 40px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.5); width: 100%; max-width: 400px; text-align: center; border: 1px solid #444; }
        .login-box h2 { color: #ff9900; margin-bottom: 20px; }
        .login-box input { width: 100%; padding: 12px; margin: 10px 0; background: #333; border: 1px solid #555; color: #fff; border-radius: 5px; box-sizing: border-box; }
        .login-box button { width: 100%; padding: 12px; background: #ff9900; border: none; font-weight: bold; cursor: pointer; border-radius: 5px; margin-top: 10px; transition: 0.3s; }
        .login-box button:hover { background: #e68a00; }
        .error-msg { color: #ff4d4d; margin-top: 15px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2><i class="fas fa-user-shield"></i> Admin Panel</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Tên đăng nhập" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <button type="submit">Đăng nhập</button>
        </form>
        <?php if($msg): ?>
            <div class="error-msg"><?php echo $msg; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
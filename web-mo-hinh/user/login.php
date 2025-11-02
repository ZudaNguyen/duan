<?php
session_start();
include "../db/connect.php";

$msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            $_SESSION['username'] = $user['username'];
            header("Location: ../index.php");
            exit;
        } else $msg = "Sai mật khẩu!";
    } else $msg = "Tài khoản không tồn tại!";
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
                <p style="text-align:center; color:red; margin-top:15px;"><?php echo $msg; ?></p>
            <?php endif; ?>

            <div class="switch">
                Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
            </div>
        </form>
    </div>
</body>
</html>

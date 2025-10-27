<?php
session_start();
include "../db/connect.php";


$msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $check = $conn->prepare("SELECT * FROM users WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $msg = "Email đã được sử dụng!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);
        $stmt->execute();
        $msg = "Đăng ký thành công! Vui lòng đăng nhập.";
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
<div class="form-box">
    <h2>Đăng ký</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Tên đăng nhập" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit">Đăng ký</button>
    </form>
    <p><?php echo $msg; ?></p>
    <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
</div>
</body>
</html>

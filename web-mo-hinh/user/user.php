<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// ✅ Lấy thông tin người dùng an toàn
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$success_message = "";
$error_message = "";

// ✅ Cập nhật thông tin
if (isset($_POST['update_info'])) {
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("UPDATE users SET email = ?, phone = ? WHERE username = ?");
    $stmt->bind_param("sss", $email, $phone, $username);
    if ($stmt->execute()) {
        $success_message = "Cập nhật thông tin thành công!";
    } else {
        $error_message = "Cập nhật thất bại, vui lòng thử lại.";
    }
}

// ✅ Đổi mật khẩu
if (isset($_POST['change_password'])) {
    $old_pass = $_POST['old_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if (password_verify($old_pass, $user['password'])) {
        if ($new_pass === $confirm_pass) {
            $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
            $stmt->bind_param("ss", $new_hash, $username);
            $stmt->execute();
            $success_message = "Đổi mật khẩu thành công!";
        } else {
            $error_message = "Mật khẩu xác nhận không khớp.";
        }
    } else {
        $error_message = "Mật khẩu cũ không đúng.";
    }
}

// ✅ Lấy lịch sử đơn hàng
$order_stmt = $conn->prepare("SELECT * FROM orders WHERE username = ? ORDER BY order_date DESC");
$order_stmt->bind_param("s", $username);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Trang người dùng</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<style>
body {
    background: #f4f6f8;
}
.container-user {
    max-width: 1100px;
    margin: 50px auto;
    background: #fff;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
h2 {
    color: #333;
    font-weight: 600;
}
hr {
    margin: 20px 0;
}
.form-control:focus {
    box-shadow: 0 0 5px rgba(0,123,255,0.5);
}
.table th {
    background-color: #007bff;
    color: white;
}
</style>
</head>
<body>

<div class="container-user">
    <h2 class="text-center mb-4">Xin chào, <?= htmlspecialchars($username) ?> 👋</h2>

    <!-- Thông báo -->
    <?php if ($success_message): ?>
        <div class="alert alert-success text-center"><?= $success_message ?></div>
    <?php elseif ($error_message): ?>
        <div class="alert alert-danger text-center"><?= $error_message ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Cột trái -->
        <div class="col-md-6 border-end">
            <h4>Thông tin cá nhân</h4>
            <form method="post" class="mt-3">
                <div class="mb-3">
                    <label>Email:</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Số điện thoại:</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>" required>
                </div>
                <button type="submit" name="update_info" class="btn btn-primary w-100">Cập nhật thông tin</button>
            </form>

            <hr>
            <h4>Đổi mật khẩu</h4>
            <form method="post" class="mt-3">
                <div class="mb-3">
                    <label>Mật khẩu cũ:</label>
                    <input type="password" name="old_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Mật khẩu mới:</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Nhập lại mật khẩu mới:</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" name="change_password" class="btn btn-warning w-100">Đổi mật khẩu</button>
            </form>
        </div>

        <!-- Cột phải -->
        <div class="col-md-6">
            <h4>Lịch sử đơn hàng</h4>
            <div class="table-responsive mt-3">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($order_result->num_rows > 0): ?>
                            <?php while ($row = $order_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['order_id'] ?></td>
                                    <td><?= $row['order_date'] ?></td>
                                    <td><?= number_format($row['total_price'], 0, ',', '.') ?>đ</td>
                                    <td><?= $row['status'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center text-muted">Chưa có đơn hàng nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="logout.php" class="btn btn-secondary">Đăng xuất</a>
    </div>
</div>

</body>
</html>

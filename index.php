<?php
// Tệp: admin/index.php
session_name('ADMIN_SESSION'); // Bắt buộc phải có dòng này đầu tiên
session_start();
include "../db/connect.php";
include "../models/Product.php";
include "../models/Order.php";
include "../models/User.php";

// 1. BẢO MẬT
$is_admin = false;
if (isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $is_admin = true;
}
if (!$is_admin) {
    header("Location: login.php");
    exit();
}

// 2. KHỞI TẠO HANDLERS
$product_handler = new Product($conn);
$order_handler = new Order($conn);
$user_handler = new User($conn);

// 3. XỬ LÝ ACTION
$msg = "";
$error_msg = "";
$view = isset($_GET['view']) ? $_GET['view'] : 'dashboard'; 
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Xóa sản phẩm
if ($action === 'delete_product' && isset($_GET['id'])) {
    if ($product_handler->delete((int)$_GET['id'])) {
        $msg = "Đã xóa sản phẩm!";
    } else {
        $error_msg = "Lỗi khi xóa sản phẩm.";
    }
    $view = 'manage';
}

// Thêm sản phẩm
if (isset($_POST['submit_add_product'])) {
    $result = $product_handler->create($_POST, $_FILES['img']);
    if ($result === true) {
        $msg = "Thêm sản phẩm thành công!";
    } else {
        $error_msg = "Lỗi: " . $result;
    }
    $view = 'add';
}

// Cập nhật đơn hàng
if ($action === 'update_order_status' && isset($_GET['id']) && isset($_GET['status'])) {
    if ($order_handler->updateStatus((int)$_GET['id'], $_GET['status'])) {
        $msg = "Đã cập nhật trạng thái đơn hàng!";
    } else {
         $error_msg = "Lỗi cập nhật.";
    }
    $view = 'orders';
}

// Xóa User
if ($action === 'delete_user' && isset($_GET['id'])) {
    $uid = (int)$_GET['id'];
    // Lấy thông tin admin hiện tại từ CSDL để so sánh ID (an toàn hơn lấy session cũ)
    $current_admin = $user_handler->findByUsername($_SESSION['username']);
    
    if ($current_admin && $uid == $current_admin['id']) {
         $error_msg = "Không thể tự xóa chính mình!";
    } else {
        if ($user_handler->deleteUserById($uid)) {
            $msg = "Đã xóa người dùng!";
        } else {
            $error_msg = "Lỗi khi xóa người dùng.";
        }
    }
    $view = 'users';
}

// 4. LẤY DỮ LIỆU
$stats = []; $orders_result = null; $products_result = null; $users_result = null; $current_order = null; $order_details_result = null;

if ($view === 'dashboard') {
    $stats['revenue'] = $conn->query("SELECT SUM(total_price) as t FROM orders WHERE status = 'Đã giao hàng'")->fetch_assoc()['t'] ?? 0;
    $stats['pending'] = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status = 'Đang xử lý'")->fetch_assoc()['c'] ?? 0;
    $stats['users'] = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'] ?? 0;
    $stats['products'] = $conn->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'] ?? 0;
}
elseif ($view === 'orders') { $orders_result = $order_handler->getAll(); }
elseif ($view === 'manage') { $products_result = $product_handler->getAll(); }
elseif ($view === 'users') { $users_result = $user_handler->getAll(); }
elseif ($view === 'order_detail' && isset($_GET['id'])) {
    $current_order = $order_handler->findById((int)$_GET['id']);
    if ($current_order) $order_details_result = $order_handler->getOrderDetails((int)$_GET['id']);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Quản Trị</title>
    <link rel="stylesheet" href="admin_style.css?v=3"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>.modal { display: block; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); } .modal-content { background-color: #2a2a2a; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 900px; border-radius: 10px; } .close-modal { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; } .order-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }</style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-shield-alt"></i> Trang Quản Trị</h1>
        
        <div class="admin-nav">
            <a href="?view=dashboard" class="nav-link <?php echo $view=='dashboard'?'active':''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="?view=orders" class="nav-link <?php echo $view=='orders'?'active':''; ?>"><i class="fas fa-box-open"></i> Đơn hàng</a>
            <a href="?view=manage" class="nav-link <?php echo $view=='manage'?'active':''; ?>"><i class="fas fa-list-alt"></i> Sản phẩm</a>
            <a href="?view=add" class="nav-link <?php echo $view=='add'?'active':''; ?>"><i class="fas fa-plus-circle"></i> Thêm SP</a>
            <a href="?view=users" class="nav-link <?php echo $view=='users'?'active':''; ?>"><i class="fas fa-users"></i> Users</a>
            
            <a href="../index.php" class="nav-link" target="_blank"><i class="fas fa-store"></i> Xem Shop</a>

            <a href="logout.php" class="back-link" style="margin-left: auto; color: #dc3545;">Đăng xuất <i class="fas fa-sign-out-alt"></i></a>
        </div>

        <?php if($msg): ?><div class="message success"><?php echo $msg; ?></div><?php endif; ?>
        <?php if($error_msg): ?><div class="message error"><?php echo $error_msg; ?></div><?php endif; ?>

        <?php if ($view === 'dashboard'): ?>
            <div class="dashboard-grid">
                <div class="stat-card"><h3>Doanh thu</h3><div class="stat-value"><?php echo number_format($stats['revenue'], 0, ',', '.'); ?>đ</div></div>
                <div class="stat-card"><h3>Đơn mới</h3><div class="stat-value"><?php echo $stats['pending']; ?></div></div>
                <div class="stat-card"><h3>Sản phẩm</h3><div class="stat-value"><?php echo $stats['products']; ?></div></div>
                <div class="stat-card"><h3>User</h3><div class="stat-value"><?php echo $stats['users']; ?></div></div>
            </div>

        <?php elseif ($view === 'orders'): ?>
            <table class="product-table">
                <thead><tr><th>ID</th><th>Khách</th><th>Tổng</th><th>Ngày</th><th>Trạng thái</th><th>Xem</th></tr></thead>
                <tbody>
                    <?php while($o = $orders_result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $o['order_id']; ?></td>
                        <td><?php echo htmlspecialchars($o['customer_name']); ?></td>
                        <td><?php echo number_format($o['total_price'], 0, ',', '.'); ?>đ</td>
                        <td><?php echo date('d/m/y H:i', strtotime($o['order_date'])); ?></td>
                        <td>
                            <form action="" method="GET">
                                <input type="hidden" name="view" value="orders"><input type="hidden" name="action" value="update_order_status"><input type="hidden" name="id" value="<?php echo $o['order_id']; ?>">
                                <select name="status" class="order-status-select" onchange="this.form.submit()">
                                    <option value="<?php echo $o['status']; ?>" hidden><?php echo $o['status']; ?></option>
                                    <option value="Đang xử lý">Đang xử lý</option><option value="Đã xác nhận">Đã xác nhận</option>
                                    <option value="Đang giao hàng">Đang giao hàng</option><option value="Đã giao hàng">Đã giao hàng</option><option value="Đã hủy">Đã hủy</option>
                                </select>
                            </form>
                        </td>
                        <td><a href="?view=order_detail&id=<?php echo $o['order_id']; ?>" class="btn-edit">Chi tiết</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        <?php elseif ($view === 'manage'): ?>
            <table class="product-table">
                <thead><tr><th>Ảnh</th><th>Tên</th><th>Giá</th><th>Kho</th><th>Hành động</th></tr></thead>
                <tbody>
                    <?php while($p = $products_result->fetch_assoc()): ?>
                    <tr>
                        <td><img src="../assets/img/<?php echo $p['img']; ?>"></td>
                        <td><?php echo htmlspecialchars($p['name']); ?></td>
                        <td><?php echo $p['price']; ?></td>
                        <td><?php echo $p['stock']; ?></td>
                        <td class="action-links">
                            <a href="edit_product.php?id=<?php echo $p['id']; ?>" class="btn-edit">Sửa</a>
                            <a href="?view=manage&action=delete_product&id=<?php echo $p['id']; ?>" class="btn-delete" onclick="return confirm('Xóa?');">Xóa</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        <?php elseif ($view === 'add'): ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group"><label>Tên SP</label><input type="text" name="name" required></div>
                    <div class="form-group"><label>Giá</label><input type="text" name="price" required></div>
                    <div class="form-group"><label>SKU</label><input type="text" name="sku"></div>
                    <div class="form-group"><label>Thương hiệu</label><input type="text" name="brand"></div>
                    <div class="form-group"><label>Danh mục</label><select name="category"><option value="SieuXe">Siêu Xe</option><option value="F1">F1</option><option value="Moto">Moto</option><option value="Khac">Khác</option></select></div>
                    <div class="form-group"><label>Kho</label><select name="stock"><option value="Còn hàng">Còn hàng</option><option value="Hết hàng">Hết hàng</option></select></div>
                </div>
                <div class="form-group"><label>Ảnh</label><input type="file" name="img" required></div>
                <div class="form-group"><label>Mô tả</label><textarea name="desc"></textarea></div>
                <div class="form-group"><label>Thông số</label><textarea name="specs"></textarea></div>
                <div class="form-group"><label>Bảo hành</label><textarea name="warranty"></textarea></div>
                <div class="form-group"><label>Đánh giá</label><input type="text" name="reviews"></div>
                <button type="submit" name="submit_add_product">Thêm Sản Phẩm</button>
            </form>

        <?php elseif ($view === 'users'): ?>
            <table class="product-table">
                <thead><tr><th>ID</th><th>User</th><th>Email</th><th>Role</th><th>Hành động</th></tr></thead>
                <tbody>
                    <?php while($u = $users_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><?php echo htmlspecialchars($u['username']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><?php echo $u['role']; ?></td>
                        <td><?php if($u['username'] !== $_SESSION['username']): ?><a href="?view=users&action=delete_user&id=<?php echo $u['id']; ?>" class="btn-delete" onclick="return confirm('Xóa?');">Xóa</a><?php endif; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        <?php elseif ($view === 'order_detail' && $current_order): ?>
            <div class="modal">
                <div class="modal-content">
                    <span class="close-modal" onclick="window.location.href='?view=orders'">&times;</span>
                    <h2>Chi tiết đơn #<?php echo $current_order['order_id']; ?></h2>
                    <div class="order-info-grid">
                        <div><p><strong>Khách:</strong> <?php echo htmlspecialchars($current_order['customer_name']); ?></p><p><strong>SĐT:</strong> <?php echo htmlspecialchars($current_order['customer_phone']); ?></p><p><strong>Đ/C:</strong> <?php echo htmlspecialchars($current_order['customer_address']); ?></p></div>
                        <div><p><strong>Ngày:</strong> <?php echo $current_order['order_date']; ?></p><p><strong>Trạng thái:</strong> <?php echo $current_order['status']; ?></p><p><strong>Note:</strong> <?php echo htmlspecialchars($current_order['customer_note']); ?></p></div>
                    </div>
                    <table class="product-table">
                        <thead><tr><th>SP</th><th>Giá</th><th>SL</th><th>Tổng</th></tr></thead>
                        <tbody>
                            <?php while($item = $order_details_result->fetch_assoc()): ?>
                            <tr><td><?php echo $item['product_name']; ?></td><td><?php echo number_format($item['product_price']); ?></td><td><?php echo $item['quantity']; ?></td><td><?php echo number_format($item['product_price']*$item['quantity']); ?></td></tr>
                            <?php endwhile; ?>
                            <tr><td colspan="3" align="right"><strong>Tổng:</strong></td><td><strong><?php echo number_format($current_order['total_price']); ?>đ</strong></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
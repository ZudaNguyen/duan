<?php
// Tệp: admin/index.php (Đã sửa toàn bộ bằng OOP)
session_start();
include "../db/connect.php"; // Nạp kết nối $conn
include "../models/Product.php"; // Nạp Lớp Product
include "../models/Order.php";   // Nạp Lớp Order
include "../models/User.php";    // Nạp Lớp User

// 1. BẢO MẬT (Giữ nguyên)
$is_admin = false;
if (isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $is_admin = true;
    $admin_username = $_SESSION['username'];
}
if (!$is_admin) {
    header("Location: login.php");
    exit();
}

// 2. KHỞI TẠO CÁC HANDLERS (Đối tượng)
$product_handler = new Product($conn);
$order_handler = new Order($conn);
$user_handler = new User($conn);

// 3. XỬ LÝ CÁC HÀNH ĐỘNG (ACTIONS) TỪ URL/FORM
$msg = "";
$error_msg = "";
$view = isset($_GET['view']) ? $_GET['view'] : 'dashboard'; 
$action = isset($_GET['action']) ? $_GET['action'] : '';

// --- ACTION: XÓA SẢN PHẨM ---
if ($action === 'delete_product' && isset($_GET['id'])) {
    if ($product_handler->delete((int)$_GET['id'])) {
        $msg = "Đã xóa sản phẩm!";
    } else {
        $error_msg = "Lỗi khi xóa sản phẩm.";
    }
    $view = 'manage'; // Chuyển về trang quản lý
}

// --- ACTION: THÊM SẢN PHẨM MỚI ---
if (isset($_POST['submit_add_product'])) {
    // Gọi hàm create() từ Lớp Product
    $result = $product_handler->create($_POST, $_FILES['img']);
    
    if ($result === true) {
        $msg = "Thêm sản phẩm thành công!";
    } else {
        $error_msg = "Lỗi: " . $result; // $result chứa thông báo lỗi
    }
    $view = 'add'; // Ở lại trang thêm để tiếp tục
}

// --- ACTION: CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG ---
if ($action === 'update_order_status' && isset($_GET['id']) && isset($_GET['status'])) {
    $order_id = (int)$_GET['id'];
    $new_status = $_GET['status'];
    
    // Gọi hàm updateStatus() từ Lớp Order
    if ($order_handler->updateStatus($order_id, $new_status)) {
        $msg = "Đã cập nhật đơn hàng #$order_id sang trạng thái: $new_status";
    } else {
         $error_msg = "Lỗi khi cập nhật đơn hàng.";
    }
    $view = 'orders';
}

// --- ACTION: XÓA NGƯỜI DÙNG ---
if ($action === 'delete_user' && isset($_GET['id'])) {
    $uid = (int)$_GET['id'];
    if ($uid == $_SESSION['user']['id']) { // Không cho tự xóa mình
         $error_msg = "Không thể tự xóa chính mình!";
    } else {
        // Gọi hàm deleteUserById() từ Lớp User
        if ($user_handler->deleteUserById($uid)) {
            $msg = "Đã xóa người dùng!";
        } else {
            $error_msg = "Lỗi khi xóa người dùng.";
        }
    }
    $view = 'users';
}

// ========================================================================
// 4. LẤY DỮ LIỆU ĐỂ HIỂN THỊ (SỬ DỤNG CÁC LỚP)
// ========================================================================
$stats = []; $orders_result = null; $products_result = null; $users_result = null;
$current_order = null; $order_details_result = null;

if ($view === 'dashboard') {
    // Logic thống kê đơn giản, có thể giữ nguyên
    $stats['revenue'] = $conn->query("SELECT SUM(total_price) as t FROM orders WHERE status = 'Đã giao hàng'")->fetch_assoc()['t'] ?? 0;
    $stats['pending'] = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status = 'Đang xử lý'")->fetch_assoc()['c'] ?? 0;
    $stats['users'] = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'] ?? 0;
    $stats['products'] = $conn->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'] ?? 0;
}
elseif ($view === 'orders') {
    // Dùng Lớp Order
    $orders_result = $order_handler->getAll();
}
elseif ($view === 'manage') {
    // Dùng Lớp Product (Hàm này đã được sửa ở Bước 21)
    $products_result = $product_handler->getAll();
}
elseif ($view === 'users') {
    // Dùng Lớp User
    $users_result = $user_handler->getAll();
}
elseif ($view === 'order_detail' && isset($_GET['id'])) {
    $oid = (int)$_GET['id'];
    // Dùng Lớp Order
    $current_order = $order_handler->findById($oid);
    if ($current_order) {
        $order_details_result = $order_handler->getOrderDetails($oid);
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị</title>
    <link rel="stylesheet" href="admin_style.css?v=3"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .modal { /* ... */ }
        .modal-content { /* ... */ }
        /* ... */
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-shield-alt"></i> Trang Quản Trị</h1>
        
        <div class="admin-nav">
            <a href="?view=dashboard" class="nav-link <?php echo $view=='dashboard'?'active':''; ?>"><i class="fas fa-tachometer-alt"></i> Bảng điều khiển</a>
            <a href="?view=orders" class="nav-link <?php echo $view=='orders'?'active':''; ?>"><i class="fas fa-box-open"></i> Quản lý Đơn hàng</a>
            <a href="?view=manage" class="nav-link <?php echo $view=='manage'?'active':''; ?>"><i class="fas fa-list-alt"></i> Quản lý Sản phẩm</a>
            <a href="?view=add" class="nav-link <?php echo $view=='add'?'active':''; ?>"><i class="fas fa-plus-circle"></i> Thêm Sản phẩm</a>
            <a href="?view=users" class="nav-link <?php echo $view=='users'?'active':''; ?>"><i class="fas fa-users"></i> Quản lý Người dùng</a>
            <a href="logout.php" class="back-link" style="margin-left: auto; color: #dc3545;">Đăng xuất <i class="fas fa-sign-out-alt"></i></a>
        </div>

        <?php if($msg): ?><div class="message success"><?php echo $msg; ?></div><?php endif; ?>
        <?php if($error_msg): ?><div class="message error"><?php echo $error_msg; ?></div><?php endif; ?>

        <?php if ($view === 'dashboard'): ?>
            <h2>Tổng quan cửa hàng</h2>
            <div class="dashboard-grid">
                <div class="stat-card">
                    <h3>Doanh thu (Đã giao)</h3>
                    <div class="stat-value"><?php echo number_format($stats['revenue'], 0, ',', '.'); ?>đ</div>
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-card">
                    <h3>Đơn chờ xử lý</h3>
                    <div class="stat-value"><?php echo $stats['pending']; ?></div>
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-card">
                    <h3>Tổng sản phẩm</h3>
                    <div class="stat-value"><?php echo $stats['products']; ?></div>
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="stat-card">
                    <h3>Khách hàng</h3>
                    <div class="stat-value"><?php echo $stats['users']; ?></div>
                    <i class="fas fa-users"></i>
                </div>
            </div>
        
        <?php elseif ($view === 'orders'): ?>
            <h2>Danh sách Đơn hàng</h2>
            <table class="product-table">
                <thead><tr><th>ID</th><th>Khách hàng</th><th>Tổng tiền</th><th>Ngày đặt</th><th>Trạng thái</th><th>Chi tiết</th></tr></thead>
                <tbody>
                    <?php while($o = $orders_result->fetch_assoc()): ?>
                    <tr>
                        <td><strong>#<?php echo $o['order_id']; ?></strong></td>
                        <td><?php echo htmlspecialchars($o['customer_name']); ?><br><small><?php echo $o['customer_phone']; ?></small></td>
                        <td><?php echo number_format($o['total_price'], 0, ',', '.'); ?>đ</td>
                        <td><?php echo date('d/m/y H:i', strtotime($o['order_date'])); ?></td>
                        <td>
                            <form action="" method="GET">
                                <input type="hidden" name="view" value="orders">
                                <input type="hidden" name="action" value="update_order_status">
                                <input type="hidden" name="id" value="<?php echo $o['order_id']; ?>">
                                <select name="status" class="order-status-select" onchange="this.form.submit()">
                                    <option value="<?php echo $o['status']; ?>" hidden><?php echo $o['status']; ?></option>
                                    <option value="Đang xử lý">Đang xử lý</option><option value="Đã xác nhận">Đã xác nhận</option>
                                    <option value="Đang giao hàng">Đang giao hàng</option><option value="Đã giao hàng">Đã giao hàng</option>
                                    <option value="Đã hủy">Đã hủy</option>
                                </select>
                            </form>
                        </td>
                        <td><a href="?view=order_detail&id=<?php echo $o['order_id']; ?>" class="btn-edit" style="background:#0d6efd;color:#fff;"><i class="fas fa-eye"></i> Xem</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        <?php elseif ($view === 'manage'): ?>
            <h2>Danh sách Sản phẩm</h2>
            <table class="product-table">
                <thead><tr><th>Ảnh</th><th>Tên sản phẩm</th><th>Giá</th><th>Kho</th><th>Hành động</th></tr></thead>
                <tbody>
                    <?php while($p = $products_result->fetch_assoc()): ?>
                    <tr>
                        <td><img src="../assets/img/<?php echo $p['img']; ?>"></td>
                        <td><strong><?php echo htmlspecialchars($p['name']); ?></strong><br><small><?php echo $p['category']; ?></small></td>
                        <td><?php echo $p['price']; ?></td>
                        <td><?php echo $p['stock']; ?></td> <td class="action-links">
                            <a href="edit_product.php?id=<?php echo $p['id']; ?>" class="btn-edit">Sửa</a>
                            <a href="?view=manage&action=delete_product&id=<?php echo $p['id']; ?>" class="btn-delete" onclick="return confirm('Xóa sản phẩm này?');">Xóa</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        <?php elseif ($view === 'add'): ?>
            <h2>Thêm Sản Phẩm Mới</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group"><label>Tên sản phẩm</label><input type="text" name="name" required></div>
                    <div class="form-group"><label>Giá bán (ví dụ 123.000đ)</label><input type="text" name="price" required></div>
                    <div class="form-group"><label>Mã SKU</label><input type="text" name="sku"></div>
                    <div class="form-group"><label>Thương hiệu</label><input type="text" name="brand"></div>
                    <div class="form-group"><label>Danh mục</label>
                        <select name="category">
                            <option value="SieuXe">Siêu Xe</option><option value="F1">Đua F1</option>
                            <option value="Moto">Mô Tô</option><option value="QuanSu">Quân Sự</option><option value="Khac">Khác</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Tình trạng kho</label>
                        <select name="stock"><option value="Còn hàng">Còn hàng</option><option value="Hết hàng">Hết hàng</option></select>
                    </div>
                </div>
                
                <div class="form-grid">
                     <div class="form-group"><label>Thông số kỹ thuật (specs)</label><textarea name="specs"></textarea></div>
                     <div class="form-group"><label>Thông tin bảo hành (warranty)</label><textarea name="warranty"></textarea></div>
                </div>
                <div class="form-group"><label>Đánh giá (VD: 4.5 sao)</label><input type="text" name="reviews"></div>

                <div class="form-group"><label>Ảnh đại diện (Bắt buộc)</label><input type="file" name="img" required></div>
                <div class="form-group"><label>Mô tả chi tiết</label><textarea name="desc" rows="5"></textarea></div>
                
                <div class="form-group"><button type="submit" name="submit_add_product"><i class="fas fa-plus"></i> Thêm sản phẩm</button></div>
            </form>

        <?php elseif ($view === 'users'): ?>
            <h2>Danh sách Người dùng</h2>
            <table class="product-table">
                <thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Vai trò</th><th>Hành động</th></tr></thead>
                <tbody>
                    <?php while($u = $users_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($u['username']); ?></strong></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><?php if($u['role']=='admin'): ?><span style="color:#ff9900;font-weight:bold;">Admin</span><?php else: ?>User<?php endif; ?></td>
                        <td><?php if($u['username'] !== $_SESSION['username']): ?><a href="?view=users&action=delete_user&id=<?php echo $u['id']; ?>" class="btn-delete" onclick="return confirm('Xóa người dùng này?');">Xóa</a><?php else: ?><small>(Bạn)</small><?php endif; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        <?php elseif ($view === 'order_detail' && $current_order): ?>
            <div class="modal">
                <div class="modal-content">
                    <span class="close-modal" onclick="window.location.href='?view=orders'">&times;</span>
                    <h2>Chi tiết đơn hàng #<?php echo $current_order['order_id']; ?></h2>
                    <div class="order-info-grid">
                        <div class="info-group">
                            <h4>Thông tin khách hàng</h4>
                            <p><strong>Họ tên:</strong> <?php echo htmlspecialchars($current_order['customer_name']); ?></p>
                            <p><strong>SĐT:</strong> <?php echo htmlspecialchars($current_order['customer_phone']); ?></p>
                            <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($current_order['customer_address']); ?></p>
                        </div>
                        <div class="info-group">
                            <h4>Thông tin đơn hàng</h4>
                            <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($current_order['order_date'])); ?></p>
                            <p><strong>Trạng thái:</strong> <span style="color:#ff9900;font-weight:bold;"><?php echo $current_order['status']; ?></span></p>
                            <p><strong>Ghi chú:</strong> <?php echo nl2br(htmlspecialchars($current_order['customer_note'])); ?></p>
                        </div>
                    </div>
                    <h4>Sản phẩm đã mua</h4>
                    <table class="product-table">
                        <thead><tr><th>Sản phẩm</th><th>Đơn giá</th><th>SL</th><th>Thành tiền</th></tr></thead>
                        <tbody>
                            <?php while($item = $order_details_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo number_format($item['product_price'],0,',','.'); ?>đ</td>
                                <td>x<?php echo $item['quantity']; ?></td>
                                <td><?php echo number_format($item['product_price']*$item['quantity'],0,',','.'); ?>đ</td>
                            </tr>
                            <?php endwhile; ?>
                            <tr><td colspan="3" style="text-align:right;"><strong>Tổng cộng:</strong></td><td style="color:#ff9900;font-size:1.2rem;"><strong><?php echo number_format($current_order['total_price'],0,',','.'); ?>đ</strong></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>
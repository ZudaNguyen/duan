<?php
session_start();
include "../db/connect.php"; // Đi lùi 1 cấp để vào db/connect.php

// --- BẢO MẬT: KIỂM TRA QUYỀN ADMIN ---
$is_admin = false;
if (isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $is_admin = true;
}

if (!$is_admin) {
    header("Location: login.php"); // Về trang login CỦA ADMIN
    exit();
}
// --- KẾT THÚC BẢO MẬT ---

// Lấy tất cả sản phẩm từ CSDL để hiển thị
$result = $conn->query("SELECT id, name, price, sku, category, img FROM products ORDER BY id DESC");

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Quản lý Sản Phẩm</title>
    <link rel="stylesheet" href="admin_style.css"> <!-- Dùng chung file CSS admin -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS riêng cho bảng quản lý */
        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .product-table th, .product-table td {
            border: 1px solid #555;
            padding: 10px 12px;
            text-align: left;
            vertical-align: middle;
        }
        .product-table th {
            background-color: #333;
            color: #ff9900;
        }
        .product-table img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .action-links a {
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            margin-right: 5px;
            font-weight: 500;
        }
        .btn-edit {
            background-color: #ffc107; /* Vàng */
            color: #000;
        }
        .btn-delete {
            background-color: #dc3545; /* Đỏ */
            color: #fff;
        }
        /* CSS cho menu nav (bạn đã có) */
        .admin-nav {
            border-bottom: 1px solid #444;
            padding-bottom: 15px;
            margin-bottom: 20px;
            overflow: hidden; 
        }
        .admin-nav .nav-link {
            background: #333; color: #fff; padding: 10px 18px;
            border-radius: 5px; text-decoration: none; font-weight: 600;
            transition: all 0.3s ease; border: 1px solid #555; margin-right: 10px;
        }
        .admin-nav .nav-link:hover { background: #444; }
        .admin-nav .nav-link.active {
            background: #ff9900; color: #000; border-color: #ff9900;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-list-alt"></i> Quản lý Sản Phẩm</h1>
        
        <!-- Menu điều hướng admin -->
        <div class="admin-nav">
            <!-- (Chúng ta sẽ tạo file index.php (Dashboard) sau) -->
            <!-- <a href="index.php" class="nav-link">Dashboard</a> -->
            <a href="add_product.php" class="nav-link"><i class="fas fa-plus-circle"></i> Thêm sản phẩm mới</a>
            <a href="../index.php" class="back-link" style="float: right;">&larr; Về trang chủ</a>
            <a href="logout.php" class="back-link" style="float: right; margin-right: 15px;">Đăng xuất</a>
        </div>

        <!-- Bảng hiển thị sản phẩm -->
        <table class="product-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>SKU</th>
                    <th>Danh mục</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><img src="../assets/img/<?php echo $row['img']; ?>" alt="Ảnh"></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo $row['price']; ?></td>
                            <td><?php echo $row['sku']; ?></td>
                            <td><?php echo $row['category']; ?></td>
                            <td class="action-links">
                                <a href="#" class="btn-edit">Sửa</a> <!-- (Chúng ta sẽ làm file Sửa sau) -->
                                <!-- Nút Xóa: có JavaScript confirm -->
                                <a href="delete_product.php?id=<?php echo $row['id']; ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này? Hành động này không thể hoàn tác.');">Xóa</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">Chưa có sản phẩm nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>


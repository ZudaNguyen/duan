<?php
session_start();
include "../db/connect.php"; // Đi lùi 1 cấp để vào db/connect.php

$msg = "";
$error_msg = "";

// --- BẢO MẬT: KIỂM TRA QUYỀN ADMIN ---
$is_admin = false;
if (isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $is_admin = true;
    $username = $_SESSION['username'];
}
if (!$is_admin) {
    header("Location: login.php"); // Về trang login CỦA ADMIN
    exit();
}
// --- KẾT THÚC BẢO MẬT ---


// --- LOGIC 1: XỬ LÝ XÓA SẢN PHẨM ---
// (Copy từ file delete_product.php cũ)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_to_delete = (int)$_GET['id'];

    // 1. Lấy tên file ảnh từ CSDL để xóa
    $stmt_img = $conn->prepare("SELECT img FROM products WHERE id = ?");
    $stmt_img->bind_param("i", $id_to_delete);
    $stmt_img->execute();
    $result_img = $stmt_img->get_result();
    
    if ($result_img->num_rows === 1) {
        $img_name = $result_img->fetch_assoc()['img'];
        $file_path = "../assets/img/" . $img_name;

        // 2. Xóa file ảnh khỏi thư mục /assets/img/
        if (file_exists($file_path)) {
            unlink($file_path); // Hàm unlink() để xóa file
        }
    }
    
    // 3. Xóa sản phẩm khỏi CSDL
    $stmt_delete = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt_delete->bind_param("i", $id_to_delete);
    
    if ($stmt_delete->execute()) {
        $msg = "Xóa sản phẩm thành công!";
    } else {
        $error_msg = "Lỗi khi xóa khỏi CSDL: " . $stmt_delete->error;
    }
    // Không cần chuyển hướng, vì code sẽ chạy tiếp xuống LOGIC 2
}


// --- LOGIC 2: XỬ LÝ THÊM SẢN PHẨM ---
// (Copy từ file add_product.php cũ)
if (isset($_POST['submit_product'])) {
    
    // Lấy dữ liệu từ form
    $name = $_POST['name'];
    $desc = $_POST['desc'];
    $price = $_POST['price'];
    $specs = $_POST['specs'];
    $warranty = $_POST['warranty'];
    $reviews = $_POST['reviews'];
    $category = $_POST['category'];
    $sku = $_POST['sku'];
    $brand = $_POST['brand'];
    $stock = $_POST['stock'];
    $img_name = ""; 

    // Xử lý Upload Ảnh
    if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
        $target_dir = "../assets/img/"; 
        $img_name = uniqid() . '-' . basename($_FILES["img"]["name"]);
        $target_file = $target_dir . $img_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["img"]["tmp_name"]);
        if($check !== false) {
            if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg") {
                if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
                    // Thành công
                } else {
                    $error_msg = "Lỗi khi tải ảnh lên (không thể di chuyển file).";
                }
            } else {
                $error_msg = "Chỉ chấp nhận file JPG, JPEG & PNG.";
            }
        } else {
            $error_msg = "File không phải là ảnh.";
        }
    } else {
        $error_msg = "Vui lòng chọn ảnh cho sản phẩm.";
    }

    // Chèn vào CSDL
    if ($error_msg == "") {
        $sql = "INSERT INTO products (name, img, `desc`, price, specs, warranty, reviews, category, sku, brand, stock) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssss", 
            $name, $img_name, $desc, $price, $specs, $warranty, 
            $reviews, $category, $sku, $brand, $stock
        );

        if ($stmt->execute()) {
            $msg = "Thêm sản phẩm mới thành công!";
        } else {
            $error_msg = "Lỗi khi thêm vào CSDL: " . $stmt->error;
        }
    }
}
// --- KẾT THÚC LOGIC 2 ---


// --- LOGIC 3: LẤY DANH SÁCH SẢN PHẨM ---
// (Copy từ file manage_products.php cũ)
$products_result = $conn->query("SELECT id, name, price, sku, category, img FROM products ORDER BY id DESC");


// --- LOGIC 4: ĐIỀU HƯỚNG VIEW ---
// Quyết định xem nên hiển thị trang nào (Quản lý hay Thêm)
// Mặc định là 'manage' (Quản lý)
$view = isset($_GET['view']) ? $_GET['view'] : 'manage';

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="admin_style.css"> 
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

        .action-links-wrapper {
            display: flex;
            align-items: center;
            gap: 5px;
            vertical-align: auto;
        }
        .action-links-wrapper a {
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 8px;
            font-weight: 500;
        }
        .btn-edit {
            background-color: #ffc107; color: #000;
        }
        .btn-delete {
            background-color: #dc3545; color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-tools"></i> Admin Panel</h1>
        <p class="welcome-msg" style="text-align: center; color: #ccc; margin-top: -15px; margin-bottom: 20px;">
            Chào mừng, <?php echo htmlspecialchars($username); ?>!
        </p>
        
        <!-- Menu điều hướng admin (Tab) -->
        <div class="admin-nav">
            <a href="index.php?view=manage" 
               class="nav-link <?php echo ($view == 'manage') ? 'active' : ''; ?>">
               <i class="fas fa-list-alt"></i> Quản lý Sản phẩm
            </a>
            <a href="index.php?view=add" 
               class="nav-link <?php echo ($view == 'add') ? 'active' : ''; ?>">
               <i class="fas fa-plus-circle"></i> Thêm sản phẩm mới
            </a>
            <a href="logout.php" class="back-link" style="float: right;">Đăng xuất</a>
            <a href="../index.php" class="back-link" style="float: right; margin-right: 15px;">&larr; Về trang chủ</a>
        </div>

        <!-- Hiển thị thông báo (nếu có) -->
        <?php if ($msg): ?>
            <div class="message success"><?php echo $msg; ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="message error"><?php echo $error_msg; ?></div>
        <?php endif; ?>


        <!-- Bắt đầu Hiển thị nội dung theo View -->
        
        <?php if ($view == 'manage'): ?>
        <!-- #################### VIEW 1: QUẢN LÝ SẢN PHẨM #################### -->
        <div id="manage-view">
            <h2>Danh sách sản phẩm</h2>
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
                    <?php if ($products_result->num_rows > 0): ?>
                        <?php while($row = $products_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><img src="../assets/img/<?php echo $row['img']; ?>" alt="Ảnh"></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo $row['price']; ?></td>
                                <td><?php echo $row['sku']; ?></td>
                                <td><?php echo $row['category']; ?></td>
                                <td class="action-links-wrapper">
                                    <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn-edit">Sửa</a>
                                    <!-- Sửa link Xóa: trỏ về trang này với action=delete -->
                                    <a href="index.php?action=delete&id=<?php echo $row['id']; ?>" 
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
        
        <?php elseif ($view == 'add'): ?>
        <!-- #################### VIEW 2: THÊM SẢN PHẨM #################### -->
        <div id="add-view">
            <h2>Thêm sản phẩm mới</h2>
            <!-- Form này submit về trang hiện tại (index.php?view=add) -->
            <form action="index.php?view=add" method="POST" enctype="multipart/form-data">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Tên sản phẩm</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Giá (ví dụ: 429.000đ)</label>
                        <input type="text" id="price" name="price" required>
                    </div>
                    <div class="form-group">
                        <label for="sku">Mã sản phẩm (SKU)</label>
                        <input type="text" id="sku" name="sku">
                    </div>
                    <div class="form-group">
                        <label for="brand">Thương hiệu</label>
                        <input type="text" id="brand" name="brand">
                    </div>
                    <div class="form-group">
                        <label for="category">Danh mục</label>
                        <select id="category" name="category">
                            <option value="SieuXe">Siêu Xe</option>
                            <option value="F1">Đua F1</option>
                            <option value="Moto">Mô Tô</option>
                            <option value="QuanSu">Quân Sự</option>
                            <option value="Khac">Khác</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="stock">Tình trạng kho</label>
                        <select id="stock" name="stock">
                            <option value="Còn hàng">Còn hàng</option>
                            <option value="Hết hàng">Hết hàng</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="img">Ảnh sản phẩm (quan trọng)</label>
                    <input type="file" id="img" name="img" accept="image/png, image/jpeg, image/jpg" required>
                </div>
                <div class="form-group">
                    <label for="desc">Mô tả (desc)</label>
                    <textarea id="desc" name="desc" rows="5"></textarea>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="specs">Thông số (specs)</label>
                        <textarea id="specs" name="specs" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="warranty">Bảo hành (warranty)</label>
                        <textarea id="warranty" name="warranty" rows="3"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reviews">Đánh giá (reviews) (ví dụ: ★★★★☆ – ...)</label>
                    <input type="text" id="reviews" name="reviews">
                </div>

                <div class="form-group">
                    <button type="submit" name="submit_product">
                        <i class="fas fa-plus-circle"></i> Thêm Sản Phẩm
                    </button>
                </div>
            </form>
        </div>
        
        <?php endif; ?>
        <!-- Kết thúc Hiển thị nội dung -->

    </div>
</body>
</html>


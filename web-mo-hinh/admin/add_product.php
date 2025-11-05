<?php
session_start();
include "../db/connect.php"; // Đi lùi 1 cấp để vào db/connect.php

$msg = "";
$error_msg = "";

// --- BẢO MẬT: KIỂM TRA QUYỀN ADMIN ---
$is_admin = false;
if (isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $is_admin = true;
}

// Nếu không phải admin, không cho phép truy cập
if (!$is_admin) {
    header("Location: login.php");
    exit();
}
// --- KẾT THÚC BẢO MẬT ---


// --- XỬ LÝ FORM KHI ADMIN GỬI (SUBMIT) ---
if (isset($_POST['submit_product'])) {
    
    // 1. Lấy dữ liệu từ form
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
    
    $img_name = ""; // Tên file ảnh để lưu vào CSDL

    // 2. Xử lý Upload Ảnh
    if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
        $target_dir = "../assets/img/"; // Thư mục lưu ảnh (đi lùi 1 cấp)
        
        // Tạo tên file duy nhất để tránh trùng lặp
        $img_name = uniqid() . '-' . basename($_FILES["img"]["name"]);
        $target_file = $target_dir . $img_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Kiểm tra nếu là ảnh thật
        $check = getimagesize($_FILES["img"]["tmp_name"]);
        if($check !== false) {
            // Kiểm tra định dạng (chỉ cho phép JPG, PNG, JPEG)
            if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg") {
                // Di chuyển file vào thư mục assets/img/
                if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
                    // Upload ảnh thành công
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

    // 3. Nếu không có lỗi (ảnh đã upload), chèn vào CSDL
    if ($error_msg == "") {
        // Dùng prepared statement để chống SQL Injection
        // Đảm bảo cột `desc` được bao bởi dấu huyền (`) vì nó là từ khóa SQL
        $sql = "INSERT INTO products (name, img, `desc`, price, specs, warranty, reviews, category, sku, brand, stock) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        // "sssssssssss" = 11 chữ 's' (string) tương ứng 11 cột
        $stmt->bind_param("sssssssssss", 
            $name, 
            $img_name, 
            $desc, 
            $price, 
            $specs, 
            $warranty, 
            $reviews, 
            $category, 
            $sku, 
            $brand, 
            $stock
        );

        if ($stmt->execute()) {
            $msg = "Thêm sản phẩm mới thành công!";
        } else {
            $error_msg = "Lỗi khi thêm vào CSDL: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Thêm Sản Phẩm Mới</title>
    <!-- Link đến file CSS (nằm cùng thư mục admin/) -->
    <link rel="stylesheet" href="admin_style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-plus-circle"></i> Thêm Sản Phẩm Mới</h1>
        
        <!-- --- BƯỚC 4: SỬA THANH ĐIỀU HƯỚNG --- -->
        <div class="admin-nav">
            <a href="index.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Về Dashboard</a>
            <a href="manage_products.php" class="nav-link"><i class="fas fa-list-alt"></i> Quản lý sản phẩm</a>
            <a href="../index.php" class="back-link" style="float: right;">&larr; Về trang chủ</a>
        </div>
        <!-- --- KẾT THÚC BƯỚC 4 --- -->


        <?php if ($msg): ?>
            <div class="message success"><?php echo $msg; ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="message error"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <!-- Form thêm sản phẩm (Giữ nguyên) -->
        <form action="add_product.php" method="POST" enctype="multipart/form-data">
            
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
</body>
</html>

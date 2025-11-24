<?php
// Tệp: admin/edit_product.php
session_name('ADMIN_SESSION'); // Quan trọng
session_start();
include "../db/connect.php"; 
include "../models/Product.php"; 

$msg = "";
$error_msg = "";
$product = null;

$is_admin = false;
if (isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $is_admin = true;
}
if (!$is_admin) {
    header("Location: login.php");
    exit();
}

$product_handler = new Product($conn);

// Logic cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_edit_product'])) {
    $result = $product_handler->update($_POST, $_FILES['img']);
    if ($result === true) {
        $msg = "Cập nhật thành công! Đang quay lại...";
        header("refresh:2;url=index.php?view=manage");
    } else {
        $error_msg = $result; 
    }
} 

// Logic lấy dữ liệu
if (isset($_GET['id'])) {
    $product = $product_handler->findById((int)$_GET['id']);
    if (!$product) { header("Location: index.php?view=manage"); exit; }
} else {
    header("Location: index.php?view=manage"); exit;
}

// Logic giữ dữ liệu khi lỗi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $error_msg != "") {
    $product = $_POST;
    $product['id'] = (int)$_POST['id'];
    $product['img'] = $_POST['current_img'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Sản Phẩm</title>
    <link rel="stylesheet" href="admin_style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>.image-preview img { width: 150px; border: 1px solid #555; margin-top:10px; }</style> 
</head>
<body>
    <div class="container">
        <h1>Sửa Sản Phẩm (#<?php echo $product['id']; ?>)</h1>
        
        <div class="admin-nav">
             <a href="index.php?view=manage" class="nav-link">Quay lại</a>
        </div>

        <?php if ($msg): ?><div class="message success"><?php echo $msg; ?></div><?php endif; ?>
        <?php if ($error_msg): ?><div class="message error"><?php echo $error_msg; ?></div><?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
            <input type="hidden" name="current_img" value="<?php echo $product['img']; ?>">

            <div class="form-grid">
                <div class="form-group"><label>Tên</label><input type="text" name="name" required value="<?php echo htmlspecialchars($product['name']); ?>"></div>
                <div class="form-group"><label>Giá</label><input type="text" name="price" required value="<?php echo htmlspecialchars($product['price']); ?>"></div>
                <div class="form-group"><label>SKU</label><input type="text" name="sku" value="<?php echo htmlspecialchars($product['sku']); ?>"></div>
                <div class="form-group"><label>Brand</label><input type="text" name="brand" value="<?php echo htmlspecialchars($product['brand']); ?>"></div>
                
                <div class="form-group"><label>Danh mục</label>
                    <select name="category">
                        <option value="SieuXe" <?php echo ($product['category'] == 'SieuXe') ? 'selected' : ''; ?>>Siêu Xe</option>
                        <option value="F1" <?php echo ($product['category'] == 'F1') ? 'selected' : ''; ?>>F1</option>
                        <option value="Moto" <?php echo ($product['category'] == 'Moto') ? 'selected' : ''; ?>>Moto</option>
                        <option value="Khac" <?php echo ($product['category'] == 'Khac') ? 'selected' : ''; ?>>Khác</option>
                    </select>
                </div>
                <div class="form-group"><label>Kho</label>
                    <select name="stock">
                        <option value="Còn hàng" <?php echo ($product['stock'] == 'Còn hàng') ? 'selected' : ''; ?>>Còn hàng</option>
                        <option value="Hết hàng" <?php echo ($product['stock'] == 'Hết hàng') ? 'selected' : ''; ?>>Hết hàng</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Ảnh (Chọn nếu muốn thay đổi)</label>
                <input type="file" name="img">
                <div class="image-preview"><img src="../assets/img/<?php echo $product['img']; ?>"></div>
            </div>

            <div class="form-group"><label>Mô tả</label><textarea name="desc"><?php echo htmlspecialchars($product['desc']); ?></textarea></div>
            <div class="form-group"><label>Thông số</label><textarea name="specs"><?php echo htmlspecialchars($product['specs']); ?></textarea></div>
            <div class="form-group"><label>Bảo hành</label><textarea name="warranty"><?php echo htmlspecialchars($product['warranty']); ?></textarea></div>
            <div class="form-group"><label>Review</label><input type="text" name="reviews" value="<?php echo htmlspecialchars($product['reviews']); ?>"></div>

            <div class="form-group"><button type="submit" name="submit_edit_product">Lưu Thay Đổi</button></div>
        </form>
    </div>
</body>
</html>
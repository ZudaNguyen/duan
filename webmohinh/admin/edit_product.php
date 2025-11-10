<?php
// Tệp: admin/edit_product.php (Đã sửa lỗi)
session_start();
include "../db/connect.php"; // Nạp kết nối $conn
include "../models/Product.php"; // Nạp Lớp Product

$msg = "";
$error_msg = "";
$product = null; // Biến để lưu thông tin sản phẩm

// 1. BẢO MẬT (Giữ nguyên)
$is_admin = false;
if (isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $is_admin = true;
}
if (!$is_admin) {
    header("Location: login.php");
    exit();
}
// --- KẾT THÚC BẢO MẬT ---

// 2. KHỞI TẠO HANDLER (Đối tượng)
$product_handler = new Product($conn);

// --- LOGIC 1: XỬ LÝ KHI USER ẤN NÚT "CẬP NHẬT" (FORM SUBMIT) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_edit_product'])) {
    
    // 3. Gọi phương thức update() của Lớp Product
    $result = $product_handler->update($_POST, $_FILES['img']);

    if ($result === true) {
        $msg = "Cập nhật sản phẩm thành công! Đang quay lại...";
        header("refresh:2;url=index.php?view=manage");
    } else {
        $error_msg = $result; 
    }
} 
// --- KẾT THÚC LOGIC 1 ---


// --- LOGIC 2: LẤY THÔNG TIN SẢN PHẨM ĐỂ HIỂN THỊ RA FORM (KHI VÀO TRANG) ---
if (isset($_GET['id'])) {
    $id_to_edit = (int)$_GET['id'];

    // 4. Gọi phương thức findById() của Lớp Product
    $product = $product_handler->findById($id_to_edit);
    
    if (!$product) {
        header("Location: index.php?view=manage");
        exit;
    }
} else {
    header("Location: index.php?view=manage");
    exit;
}
// --- KẾT THÚC LOGIC 2 ---

// 5. Nếu logic 1 (UPDATE) chạy và bị lỗi, gộp dữ liệu POST để hiển thị lại
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Sửa Sản Phẩm</title>
    <link rel="stylesheet" href="admin_style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS nhỏ để xem trước ảnh (Giữ nguyên) */
        .image-preview { margin-top: 15px; }
        .image-preview img { width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 2px solid #555; }
    </style> </head>
<body>
    <div class="container">
        <h1><i class="fas fa-edit"></i> Sửa Sản Phẩm (ID: <?php echo $product['id']; ?>)</h1>
        
        <div class="admin-nav">
            <a href="index.php?view=manage" class="nav-link"><i class="fas fa-list-alt"></i> Quản lý sản phẩm</a>
            <a href="index.php?view=add" class="nav-link"><i class="fas fa-plus-circle"></i> Thêm sản phẩm mới</a>
            <a href="../index.php" class="back-link" style="float: right;">&larr; Về trang chủ</a>
        </div>

        <?php if ($msg): ?>
            <div class="message success"><?php echo $msg; ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="message error"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form action="edit_product.php" method="POST" enctype="multipart/form-data">
            
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
            <input type="hidden" name="current_img" value="<?php echo $product['img']; ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Tên sản phẩm</label>
                    <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <div class="form-group">
                    <label for="price">Giá (ví dụ: 429.000đ)</label>
                    <input type="text" id="price" name="price" required value="<?php echo htmlspecialchars($product['price']); ?>">
                </div>
                <div class="form-group">
                    <label for="sku">Mã sản phẩm (SKU)</label>
                    <input type="text" id="sku" name="sku" value="<?php echo htmlspecialchars($product['sku']); ?>">
                </div>
                <div class="form-group">
                    <label for="brand">Thương hiệu</label>
                    <input type="text" id="brand" name="brand" value="<?php echo htmlspecialchars($product['brand']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="category">Danh mục</label>
                    <select id="category" name="category">
                        <option value="SieuXe" <?php echo ($product['category'] == 'SieuXe') ? 'selected' : ''; ?>>Siêu Xe</option>
                        <option value="F1" <?php echo ($product['category'] == 'F1') ? 'selected' : ''; ?>>Đua F1</option>
                        <option value="Moto" <?php echo ($product['category'] == 'Moto') ? 'selected' : ''; ?>>Mô Tô</option>
                        <option value="QuanSu" <?php echo ($product['category'] == 'QuanSu') ? 'selected' : ''; ?>>Quân Sự</option>
                        <option value="Khac" <?php echo ($product['category'] == 'Khac') ? 'selected' : ''; ?>>Khác</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="stock">Tình trạng kho</label>
                    <select id="stock" name="stock">
                        <option value="Còn hàng" <?php echo ($product['stock'] == 'Còn hàng') ? 'selected' : ''; ?>>Còn hàng</option>
                        <option value="Hết hàng" <?php echo ($product['stock'] == 'Hết hàng') ? 'selected' : ''; ?>>Hết hàng</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="img">Ảnh sản phẩm (Để trống nếu không muốn thay đổi)</label>
                <input type="file" id="img" name="img" accept="image/png, image/jpeg, image/jpg">
                
                <div class="image-preview">
                    <label style="color: #bbb; font-size: 0.9em;">Ảnh hiện tại:</label><br>
                    <img src="../assets/img/<?php echo $product['img']; ?>" alt="Ảnh hiện tại">
                </div>
            </div>

            <div class="form-group">
                <label for="desc">Mô tả (desc)</label>
                <textarea id="desc" name="desc" rows="5"><?php echo htmlspecialchars($product['desc']); ?></textarea>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="specs">Thông số (specs)</label>
                    <textarea id="specs" name="specs" rows="3"><?php echo htmlspecialchars($product['specs']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="warranty">Bảo hành (warranty)</label>
                    <textarea id="warranty" name="warranty" rows="3"><?php echo htmlspecialchars($product['warranty']); ?></textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="reviews">Đánh giá (reviews)</label>
                <input type="text" id="reviews" name="reviews" value="<?php echo htmlspecialchars($product['reviews']); ?>">
            </div>

            <div class="form-group">
                <button type="submit" name="submit_edit_product">
                    <i class="fas fa-save"></i> Cập Nhật Sản Phẩm
                </button>
            </div>
        </form>
    </div>
</body>
</html>
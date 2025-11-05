<?php
session_start();
include "../db/connect.php"; // Kết nối CSDL

$msg = "";
$error_msg = "";
$product = null; // Biến để lưu thông tin sản phẩm

// --- BẢO MẬT: KIỂM TRA QUYỀN ADMIN (Giống file index.php) ---
$is_admin = false;
if (isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $is_admin = true;
}
if (!$is_admin) {
    header("Location: login.php");
    exit();
}
// --- KẾT THÚC BẢO MẬT ---


// --- LOGIC 1: XỬ LÝ KHI USER ẤN NÚT "CẬP NHẬT" (FORM SUBMIT) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_edit_product'])) {
    
    // 1. Lấy dữ liệu từ form
    $id_to_update = (int)$_POST['id'];
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
    
    // 2. Xử lý Ảnh
    $img_name = $_POST['current_img']; // Lấy tên ảnh cũ làm mặc định

    // Kiểm tra xem admin có tải ảnh MỚI không
    if (isset($_FILES['img']) && $_FILES['img']['error'] == 0 && !empty($_FILES['img']['name'])) {
        $target_dir = "../assets/img/"; 
        $img_name_new = uniqid() . '-' . basename($_FILES["img"]["name"]);
        $target_file = $target_dir . $img_name_new;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["img"]["tmp_name"]);
        if($check !== false) {
            if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg") {
                if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
                    // Upload ảnh mới thành công
                    $img_name = $img_name_new; // Cập nhật tên ảnh mới
                    
                    // Xóa ảnh cũ (nếu tồn tại)
                    $old_img_path = $target_dir . $_POST['current_img'];
                    if (file_exists($old_img_path) && is_file($old_img_path)) {
                        unlink($old_img_path);
                    }
                } else {
                    $error_msg = "Lỗi khi tải ảnh lên.";
                }
            } else {
                $error_msg = "Chỉ chấp nhận file JPG, JPEG & PNG.";
            }
        } else {
            $error_msg = "File không phải là ảnh.";
        }
    }
    // Nếu không tải ảnh mới, $img_name vẫn là tên ảnh cũ (lấy từ $_POST['current_img'])

    // 3. Cập nhật vào CSDL (nếu không có lỗi ảnh)
    if ($error_msg == "") {
        $sql = "UPDATE products SET 
                    name = ?, img = ?, `desc` = ?, price = ?, specs = ?, 
                    warranty = ?, reviews = ?, category = ?, sku = ?, 
                    brand = ?, stock = ?
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssssi", 
            $name, $img_name, $desc, $price, $specs, $warranty, 
            $reviews, $category, $sku, $brand, $stock, $id_to_update
        );

        if ($stmt->execute()) {
            $msg = "Cập nhật sản phẩm thành công! Đang quay lại...";
            // Chuyển hướng về trang quản lý sau 2 giây
            header("refresh:2;url=index.php?view=manage");
        } else {
            $error_msg = "Lỗi khi cập nhật CSDL: " . $stmt->error;
        }
    }

} 
// --- KẾT THÚC LOGIC 1 ---


// --- LOGIC 2: LẤY THÔNG TIN SẢN PHẨM ĐỂ HIỂN THỊ RA FORM (KHI VÀO TRANG) ---
// Kiểm tra xem có ID trên URL không
if (isset($_GET['id'])) {
    $id_to_edit = (int)$_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id_to_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $product = $result->fetch_assoc();
    } else {
        // Không tìm thấy sản phẩm, đá về trang quản lý
        header("Location: index.php?view=manage");
        exit;
    }
} else {
    // Không có ID, cũng đá về
    header("Location: index.php?view=manage");
    exit;
}
// --- KẾT THÚC LOGIC 2 ---

// Nếu logic 1 (UPDATE) chạy và bị lỗi, $product vẫn cần dữ liệu để hiển thị lại form
// nên ta gộp dữ liệu POST (đã sửa) vào biến $product để hiển thị lại
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $error_msg != "") {
    $product = $_POST;
    $product['id'] = (int)$_POST['id']; // Đảm bảo ID là số
    $product['img'] = $_POST['current_img']; // Giữ ảnh cũ nếu lỗi
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Sửa Sản Phẩm</title>
    <link rel="stylesheet" href="admin_style.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS nhỏ để xem trước ảnh */
        .image-preview {
            margin-top: 15px;
        }
        .image-preview img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #555;
        }
    </style>
</head>
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
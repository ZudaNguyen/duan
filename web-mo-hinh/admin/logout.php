<?php
session_start();
session_destroy();
// Chuyển về trang đăng nhập CỦA ADMIN
header("Location: login.php"); 
exit;
?>
```

---

### Bước 3: Sửa file `admin/add_product.php` (File CŨ)

Bây giờ, chúng ta sửa lại file `add_product.php` [cite: `add_product.php`] để nó kiểm tra quyền admin và nếu thất bại, nó sẽ chuyển hướng về `admin/login.php` (file mới tạo) thay vì `user/login.php` (file của khách).

Bạn hãy **thay thế** toàn bộ code PHP ở đầu file `add_product.php` bằng code này:

```php
<?php
session_start();

// --- BẢO MẬT MỚI ---
// 1. Kiểm tra xem session có tồn tại VÀ role có phải là 'admin' không
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Nếu không, đá về trang đăng nhập CỦA ADMIN (file login.php cùng thư mục)
    header("Location: login.php");
    exit();
}
// --- KẾT THÚC BẢO MẬT ---

include "../db/connect.php"; // Kết nối CSDL sau khi đã qua bảo mật

$msg = "";
$error_msg = "";

// (Toàn bộ code xử lý THÊM SẢN PHẨM (if isset($_POST['submit_product'])) 
//  của bạn ... giữ nguyên ... )

// --- XỬ LÝ FORM KHI ADMIN GỬI (SUBMIT) ---
if (isset($_POST['submit_product'])) {
    
    // 1. Lấy dữ liệu từ form
    $name = $_POST['name'];
    // ... (code lấy dữ liệu khác) ...
    $stock = $_POST['stock'];
    $img_name = ""; 

    // 2. Xử lý Upload Ảnh
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

    // 3. Nếu không có lỗi (ảnh đã upload), chèn vào CSDL
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
?>

<!DOCTYPE html>
<!-- (HTML của bạn giữ nguyên, không cần thay đổi) -->
<!-- ... -->
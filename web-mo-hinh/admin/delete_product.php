<?php
session_start();
include "../db/connect.php"; 

// --- BẢO MẬT: KIỂM TRA QUYỀN ADMIN ---
$is_admin = false;
if (isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $is_admin = true;
}

if (!$is_admin) {
    header("Location: login.php");
    exit();
}
// --- KẾT THÚC BẢO MẬT ---


// --- XỬ LÝ XÓA ---
if (isset($_GET['id'])) {
    $id_to_delete = (int)$_GET['id'];

    // --- RẤT QUAN TRỌNG: XÓA FILE ẢNH TRƯỚC ---
    // 1. Lấy tên file ảnh từ CSDL
    $stmt_img = $conn->prepare("SELECT img FROM products WHERE id = ?");
    $stmt_img->bind_param("i", $id_to_delete);
    $stmt_img->execute();
    $result_img = $stmt_img->get_result();
    
    if ($result_img->num_rows === 1) {
        $img_name = $result_img->fetch_assoc()['img'];
        $file_path = "../assets/img/" . $img_name;

        // 2. Xóa file ảnh khỏi thư mục /assets/img/
        // Kiểm tra xem file có tồn tại không và không phải là thư mục
        if (file_exists($file_path) && is_file($file_path)) {
            unlink($file_path); // Hàm unlink() để xóa file
        }
    }
    
    // 3. Xóa sản phẩm khỏi CSDL
    $stmt_delete = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt_delete->bind_param("i", $id_to_delete);
    
    if ($stmt_delete->execute()) {
        // Xóa thành công
    } else {
        // Có lỗi
        echo "Lỗi khi xóa khỏi CSDL: " . $stmt_delete->error;
        exit;
    }

}

// 4. Quay lại trang quản lý
header("Location: manage_products.php");
exit;
?>


<?php
// Tệp: product.php (Đã sửa)
session_start(); 
include "db/connect.php"; // Nạp kết nối $conn
include "models/Product.php"; // Nạp Lớp Product

$title = "Chi tiết sản phẩm";
$category_map = [ "SieuXe" => "Siêu Xe", "F1" => "Đua F1", "Moto" => "Mô Tô", "QuanSu" => "Quân Sự" ];

// 1. Khởi tạo đối tượng Product
$product_handler = new Product($conn);

// --- LOGIC MỚI: LẤY 1 SẢN PHẨM TỪ CSDL ---
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. Gọi phương thức findById() của Lớp Product
$product = $product_handler->findById($id);

if(!$product) {
    echo "Sản phẩm không tồn tại!";
    exit;
}
// --- KẾT THÚC LẤY 1 SẢN PHẨM ---


// --- LOGIC MỚI: LẤY SẢN PHẨM LIÊN QUAN TỪ CSDL ---
$current_category = $product['category'];
$current_id = $product['id'];

// 3. Gọi phương thức findRelated() của Lớp Product
$related_products_result = $product_handler->findRelated($current_category, $current_id);

// --- KẾT THÚC LẤY SẢN PHẨM LIÊN QUAN ---
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $product['name']; ?></title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div id="wrapper">

    <header id="header">
        </header>

    <section id="product-detail">
        <div class="img-box">
            <img src="./assets/img/<?php echo $product['img']; ?>" alt="<?php echo $product['name']; ?>">
        </div>
        <div class="info-box">
            <h1><?php echo $product['name']; ?></h1>
            <p class="desc"><?php echo $product['desc']; ?></p>
            <form method="POST" action="add_to_cart.php" id="form-add-to-cart-product">
                </form>
            </div>
    </section>

    <?php if ($related_products_result->num_rows > 0): ?>
    <section id="wp-products" class="related-products">
        <h2>Sản phẩm bạn có thể thích</h2>
        <ul id="list-products">
            <?php while($p = $related_products_result->fetch_assoc()): ?>
                <?php endwhile; ?>
        </ul>
    </section>
    <?php endif; ?>

</div>

<script>
// Code JS (Tabs, Tăng giảm, AJAX...) (Giữ nguyên)
// ...
</script>

</body>
</html>
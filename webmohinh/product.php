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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
<div id="wrapper">

    <header id="header">
        <div class="logo">
            <a href="index.php"><img src="./assets/img/logo.jpg" alt="Logo"></a>
        </div>
        <nav id="menu">
            <?php
            $menu = [
                "Trang chủ"=>"index.php",
                "Sản phẩm"=>"index.php#wp-products",
                "Liên hệ"=>"index.php#saleoff"
            ];
            foreach($menu as $name=>$link) {
                echo '<div class="item"><a href="'.$link.'">'.$name.'</a></div>';
            }
            ?>
        </nav>
        
        <div id="actions">
            <div class="item">
                <a href="<?php echo isset($_SESSION['username']) ? 'user/user.php' : 'user/login.php'; ?>">
                    <img src="./assets/img/user.jpg" alt="Tài khoản">
                </a>
            </div>
            <div class="item">
                <a href="<?php echo isset($_SESSION['username']) ? 'cart.php' : 'user/login.php'; ?>">
                    <img src="./assets/img/cart.jpg" alt="Giỏ hàng">
                </a>
            </div>
        </div>
    </header>

    <section id="product-detail">
        <div class="img-box">
            <img src="./assets/img/<?php echo $product['img']; ?>" alt="<?php echo $product['name']; ?>">
        </div>
        <div class="info-box">
            <h1><?php echo $product['name']; ?></h1>
            <p class="desc"><?php echo $product['desc']; ?></p>
            <div class="price"><?php echo $product['price']; ?></div>
            
            <div class="product-meta">
                <div class="meta-item">
                    <strong>SKU:</strong>
                    <span><?php echo $product['sku']; ?></span>
                </div>
                <div class="meta-item meta-brand-stock">
                    <div>
                        <strong>Thương hiệu:</strong>
                        <span>
                            <a href="index.php?brand=<?php echo $product['brand']; ?>#wp-products">
                                <?php echo $product['brand']; ?>
                            </a>
                        </span>
                    </div>
                    <div class="stock-status <?php echo ($product['stock'] == 'Còn hàng') ? 'in-stock' : 'out-of-stock'; ?>">
                        (<?php echo $product['stock']; ?>)
                    </div>
                </div>
            </div>
            
            <form method="POST" action="add_to_cart.php" id="form-add-to-cart-product">
                <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
                <input type="hidden" name="product_img" value="<?php echo $product['img']; ?>">
                <div class="quantity-box">
                    <button type="button" class="qty-btn" id="decrease">-</button>
                    <input type="number" id="quantity" name="quantity" value="1" min="1">
                    <button type="button" class="qty-btn" id="increase">+</button>
                </div>
                <button type="submit" name="add_to_cart" class="btn-add">
                    <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                </button>
            </form>
            
            <div id="add-to-cart-message" style="display:none; color: green; margin-top: 10px; font-weight: 600;"></div>
            
            <div class="tabs product-tabs">
                <ul id="tabs-nav">
                    <li><a href="#tab2">Thông số</a></li>
                    <li><a href="#tab3">Bảo hành</a></li>
                    <li><a href="#tab4">Đánh giá</a></li>
                </ul>
                <div id="tabs-content">
                    <div id="tab2" class="tab-content" style="display:none;">
                        <p><?php echo $product['specs']; ?></p>
                    </div>
                    <div id="tab3" class="tab-content" style="display:none;">
                        <p><?php echo $product['warranty']; ?></p>
                    </div>
                    <div id="tab4" class="tab-content" style="display:none;">
                        <p><?php echo $product['reviews']; ?></p>
                    </div>
                </div>
            </div>

            <a href="index.php" class="back-link">← Quay lại danh sách sản phẩm</a>
        </div>
    </section>

    <?php if ($related_products_result->num_rows > 0): ?>
    <section id="wp-products" class="related-products">
        <h2>Sản phẩm bạn có thể thích</h2>
        
        <ul id="list-products">
            <?php while($p = $related_products_result->fetch_assoc()): // Dùng vòng lặp while ?>
                <li class="item">
                    <a href="product.php?id=<?php echo $p['id']; ?>">
                        <img src="./assets/img/<?php echo $p['img']; ?>" alt="<?php echo $p['name']; ?>">
                    </a>
                    
                    <a href="product.php?id=<?php echo $p['id']; ?>" class="name"><?php echo $p['name']; ?></a>
                    <div class="desc"><?php echo $p['desc']; ?></div>
                    <div class="price"><?php echo $p['price']; ?></div>
                    
                    <form method="POST" action="add_to_cart.php" class="form-add-to-cart-index">
                        <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($p['name']); ?>">
                        <input type="hidden" name="product_price" value="<?php echo $p['price']; ?>">
                        <input type="hidden" name="product_img" value="<?php echo $p['img']; ?>">
                        
                        <button type="submit" name="add_to_cart" class="btn-add-cart">
                            <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                        </button>
                        
                        <div class="add-to-cart-message-index" style="display:none; color: green; text-align:center; padding-top: 5px; font-size: 0.9rem;"></div>
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>
    </section>
    <?php endif; ?>
    </div>

<script>
// Code JS (Tabs, Tăng giảm, AJAX...) (Giữ nguyên)
const tabs = document.querySelectorAll('#tabs-nav li a');
const contents = document.querySelectorAll('.tab-content');
tabs.forEach(tab => {
    tab.addEventListener('click', e => {
        e.preventDefault();
        contents.forEach(c => c.style.display = 'none');
        tabs.forEach(t => t.parentElement.classList.remove('active'));
        document.querySelector(tab.getAttribute('href')).style.display = 'block';
        tab.parentElement.classList.add('active');
    });
});
tabs[0].click(); 

const decreaseBtn = document.getElementById('decrease');
const increaseBtn = document.getElementById('increase');
const quantityInput = document.getElementById('quantity');
decreaseBtn.addEventListener('click', () => {
    let current = parseInt(quantityInput.value);
    if (current > 1) { quantityInput.value = current - 1; }
});
increaseBtn.addEventListener('click', () => {
    let current = parseInt(quantityInput.value);
    quantityInput.value = current + 1;
});
quantityInput.addEventListener('input', () => {
    let current = parseInt(quantityInput.value);
    if (isNaN(current) || current < 1) { quantityInput.value = 1; }
});

const allProductForms = document.querySelectorAll('#form-add-to-cart-product, .form-add-to-cart-index');
allProductForms.forEach(form => {
    form.addEventListener('submit', function(event) {
        event.preventDefault(); 
        const formData = new FormData(form);
        formData.append('add_to_cart', ''); 
        let messageDiv;
        if (form.id === 'form-add-to-cart-product') {
            messageDiv = document.getElementById('add-to-cart-message');
        } else {
            messageDiv = form.querySelector('.add-to-cart-message-index');
        }
        fetch('add_to_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageDiv.textContent = data.message;
                messageDiv.style.color = 'green';
            } else {
                messageDiv.textContent = data.message;
                messageDiv.style.color = 'red';
            }
            messageDiv.style.display = 'block';
            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 3000);
        })
        .catch(error => {
            console.error('Lỗi:', error);
            messageDiv.textContent = 'Có lỗi, thử lại.';
            messageDiv.style.color = 'red';
            messageDiv.style.display = 'block';
        });
    });
});
</script>

</body>
</html>
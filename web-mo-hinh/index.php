<?php
session_start();
include "db/connect.php"; // Kết nối CSDL, đường dẫn đúng với cấu trúc thư mục

$title = "Web bán mô hình xe đua";

// Danh sách sản phẩm (thêm nhiều sản phẩm cho trang 2)
$products = [
    ["img"=>"lambor.jpg", "name"=>"Mô hình xe Lamborghini Huracan Performante Orange 1:24 Bburago", "desc"=>"Mô tả sản phẩm", "price"=>"429.000đ"],
    ["img"=>"AMGG63.jpg", "name"=>"Mô hình xe Mercedes-AMG G63 2022 1:43 SOLIDO", "desc"=>"Mô tả sản phẩm", "price"=>"679.000đ"],
    ["img"=>"moto.jpg", "name"=>"Mô hình mô tô KTM 1290 Super Duke 1:12 Maisto", "desc"=>"Mô tả sản phẩm", "price"=>"289.000đ"],
    ["img"=>"moto2.jpg", "name"=>"Mô hình xe mô tô Ducati Monster 1:18 Maisto", "desc"=>"Mô tả sản phẩm", "price"=>"189.000đ"],
    ["img"=>"F1.jpg", "name"=>"Mô hình xe Mercedes F1 2016 W007 Hybrid 1:18 Bburago", "desc"=>"Mô tả sản phẩm", "price"=>"1.849.000đ"],
    ["img"=>"F1_2.jpg", "name"=>"Mô hình xe F1 Mercedes-AMG W14 E Performance 1:43 BBURAGO", "desc"=>"Mô tả sản phẩm", "price"=>"479.000đ"],
    ["img"=>"ferr.jpg", "name"=>"Mô hình xe Ferrari FXX K 1:24 Bburago", "desc"=>"Mô tả sản phẩm", "price"=>"549.000đ"],
    ["img"=>"qp.jpg", "name"=>"Mô hình xe quân sự Hummer Humvee Battlefield Vehicle Military 1:18 KDW", "desc"=>"Mô tả sản phẩm", "price"=>"869.000đ"]
];

// Xử lý tìm kiếm
$searchResults = $products;
$tukhoa = '';
if (isset($_GET['timkiem']) && !empty($_GET['tukhoa'])) {
    $tukhoa = strtolower($_GET['tukhoa']);
    $searchResults = array_filter($products, function($p) use ($tukhoa) {
        return strpos(strtolower($p['name']), $tukhoa) !== false;
    });
}

// Phân trang
$itemsPerPage = 4;
$totalProducts = count($searchResults);
$totalPages = ceil($totalProducts / $itemsPerPage);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, min($totalPages, $page));
$startIndex = ($page - 1) * $itemsPerPage;
$displayProducts = array_slice($searchResults, $startIndex, $itemsPerPage);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $title; ?></title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
      integrity="sha512-papH7Y6X7k1Q2Zt2g0a9yqX3sR0kY2oA2J0+Jd2xXz9q9G5b0zFvX6vTnX4FZ6sG/4w6fH0v1u6m3zYcK3v9AQ==" 
      crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
html { scroll-behavior: smooth; }
</style>
</head>
<body>
<div id="wrapper">

    <header id="header">
        <div class="logo">
            <a href="index.php">
                <img src="./assets/img/logo.jpg" alt="Logo trang web">
            </a>
        </div>

        <nav id="menu">
            <?php
            $menu = ["Trang chủ"=>"index.php","Sản phẩm"=>"index.php#wp-products","Liên hệ"=>"index.php#saleoff"];
            foreach ($menu as $name => $link) {
                echo '<div class="item"><a href="'.$link.'">'.$name.'</a></div>';
            }
            ?>
        </nav>

        <div id="search">
            <form action="index.php" method="GET">
                <input type="text" placeholder="Tìm kiếm sản phẩm..." name="tukhoa" value="<?php echo htmlspecialchars($tukhoa); ?>">
                <input type="submit" name="timkiem" value="Tìm kiếm">
            </form>
        </div>

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

    <section id="banner">
        <div class="box-left">
            <h2><span>MÔ HÌNH</span><br><span>XE ĐUA</span></h2>
            <button onclick="window.location.href='#wp-products'">Mua ngay</button>
        </div>
        <div class="box-right">
            <img src="./assets/img/img_1.jfif" alt="Mô hình 1">
            <img src="./assets/img/img_2.jpg" alt="Mô hình 2">
            <img src="./assets/img/img_3.png" alt="Mô hình 3">
        </div>
    </section>

    <section id="wp-products">
        <h2>SẢN PHẨM</h2>

        <?php if(!empty($tukhoa)): ?>
            <p style="text-align:center;">Kết quả tìm kiếm cho: <strong><?php echo htmlspecialchars($tukhoa); ?></strong></p>
        <?php endif; ?>

<ul id="list-products">
    <?php foreach($displayProducts as $index => $p): ?>
        <li class="item">
            <a href="product.php?id=<?php echo $startIndex + $index; ?>">
                <img src="./assets/img/<?php echo $p['img']; ?>" alt="<?php echo $p['name']; ?>">
            </a>
            
            <a href="product.php?id=<?php echo $startIndex + $index; ?>" class="name"><?php echo $p['name']; ?></a>
            <div class="desc"><?php echo $p['desc']; ?></div>
            <div class="price"><?php echo $p['price']; ?></div>
            
            <form method="POST" action="add_to_cart.php" class="form-add-to-cart-index">
                <input type="hidden" name="product_id" value="<?php echo $startIndex + $index; // Dùng ID duy nhất ?>">
                <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($p['name']); ?>">
                <input type="hidden" name="product_price" value="<?php echo $p['price']; ?>">
                <input type="hidden" name="product_img" value="<?php echo $p['img']; ?>">
                
                <button type="submit" name="add_to_cart" class="btn-add-cart">
                    <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                </button>

                <div class="add-to-cart-message-index" style="display:none; color: green; text-align:center; padding-top: 5px; font-size: 0.9rem;"></div>
            </form>
        </li>
    <?php endforeach; ?>
</ul>
<div class="list-page">
            <?php for($i=1; $i<=$totalPages; $i++): ?>
                <div class="item">
                    <a href="?<?php echo !empty($tukhoa) ? "timkiem=1&tukhoa=".urlencode($tukhoa)."&" : ""; ?>page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </div>
            <?php endfor; ?>
        </div>

        <div id="saleoff">
            <div class="box-left">
                <h1><span>GIẢM GIÁ LÊN ĐẾN</span><span>45%</span></h1>
            </div>
            <div class="box-right">
                <h2>Luôn sẵn sàng hỗ trợ</h2>
                <div class="contact-info">
                    <div class="info-item">
                        <i class="icon fas fa-envelope"></i>
                        <div class="info-text">
                            <span>Email</span>
                            <a href="mailto:usagi@vromvrom.adu">usagi@vromvrom.adu</a>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="icon fas fa-phone-alt"></i>
                        <div class="info-text">
                            <span>Hotline</span>
                            <a href="tel:01357924680">01357924680</a>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="icon fas fa-map-marker-alt"></i>
                        <div class="info-text">
                            <span>Địa chỉ</span>
                            <strong>Số 17, Đường Trần Phú, TP. Thanh Hóa</strong>
                        </div>
                    </div>
                </div>
                <a href="#footer" class="contact-btn"><i class="fas fa-paper-plane"></i> Gửi tin nhắn cho chúng tôi</a>
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Code animation cho #saleoff (Giữ nguyên)
    const leftBox = document.querySelector("#saleoff .box-left");
    const rightBox = document.querySelector("#saleoff .box-right");

    leftBox.style.opacity = "0";
    leftBox.style.transform = "translateX(-150px)";
    rightBox.style.opacity = "0";
    rightBox.style.transform = "translateX(150px)";

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                leftBox.style.transition = "all 1s ease-out";
                leftBox.style.opacity = "1";
                leftBox.style.transform = "translateX(0)";

                rightBox.style.transition = "all 1s ease-out 0.3s";
                rightBox.style.opacity = "1";
                rightBox.style.transform = "translateX(0)";
            }
        });
    }, { threshold: 0.3 });

    observer.observe(document.querySelector("#saleoff"));

    // --- CODE AJAX MỚI THÊM VÀO ---
    const allForms = document.querySelectorAll('.form-add-to-cart-index');
    
    allForms.forEach(form => {
        form.addEventListener('submit', function(event) {
            // 1. Ngăn trang tải lại
            event.preventDefault(); 

            // 2. Lấy dữ liệu form này
            const formData = new FormData(form);
            formData.append('add_to_cart', '')
            const messageDiv = form.querySelector('.add-to-cart-message-index');

            // 3. Gửi dữ liệu ngầm (AJAX)
            fetch('add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // 4. Hiển thị thông báo
                if (data.success) {
                    messageDiv.textContent = data.message;
                    messageDiv.style.color = 'green';
                } else {
                    messageDiv.textContent = data.message;
                    messageDiv.style.color = 'red';
                }
                messageDiv.style.display = 'block';

                // 5. Ẩn thông báo sau 3 giây
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
    
});
</script>
</body>
</html>
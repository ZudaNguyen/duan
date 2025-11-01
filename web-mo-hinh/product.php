<?php
// product.php

// Thêm session_start() ở đầu để kiểm tra đăng nhập (nếu cần)
session_start(); 

$title = "Chi tiết sản phẩm";

// --- BƯỚC 1: CẬP NHẬT MẢNG PRODUCTS (Thêm "category") ---
$products = [
[
        "img"=>"lambor.jpg",
        "name"=>"Mô hình xe Lamborghini Huracan Performante Orange 1:24 Bburago",
        "desc"=>"Được sinh ra từ tinh thần đường đua và hiệu suất đỉnh cao, Lamborghini Huracan Performante là cỗ máy siêu xe đầy mê hoặc. Mô hình tỉ lệ 1:24 từ Bburago tái hiện sống động phiên bản màu cam rực rỡ – mạnh mẽ, sắc sảo và đầy khí chất. Thiết kế khí động học đặc trưng, cánh gió sau nổi bật, và nội thất mô phỏng chi tiết khiến chiếc xe thu nhỏ này không chỉ đẹp mà còn đầy chân thật. Từng đường cong, từng khe hút gió đều toát lên chất Lamborghini – sự hòa quyện giữa nghệ thuật và tốc độ. Phù hợp để trưng bày, sưu tầm hoặc làm quà cho người mê siêu xe.",
        "price"=>"429.000đ",
        "specs"=>"Mô hình xe Lamborghini Huracan Performante 1:24 Bburago | Màu: Cam | Kích thước: 19×9×5 cm | Chất liệu: Kim loại + Nhựa + Cao su | Mở cửa: Có | Bánh xe đánh lái: Có | Trọng lượng: 650g",
        "warranty"=>"Bảo hành 3 tháng đối với lỗi nhà sản xuất, đổi mới trong 7 ngày nếu lỗi kỹ thuật.",
        "reviews"=>"★★★★☆ – Mô hình chi tiết, sơn bóng đẹp, cửa mở được, bánh xe đánh lái tiện trưng bày; giá hợp lý.",
        "category"=>"SieuXe"
    ],
    [
        "img"=>"AMGG63.jpg",
        "name"=>"Mô hình xe Mercedes-AMG G63 2022 1:43 SOLIDO",
        "desc"=>"Mercedes-AMG G63 2022 là phiên bản hiệu suất cao của huyền thoại G-Class, một trong số ít những chiếc SUV duy trì thiết kế hình hộp vuông vức mang tính biểu tượng kể từ khi ra mắt vào năm 1979. Từ khởi điểm là một chiếc xe quân sự và địa hình, G-Class đã phát triển thành biểu tượng của sự sang trọng, địa vị và sức mạnh, đặc biệt là dưới bàn tay của AMG. Chiếc G63 hiện đại nổi bật với ngoại hình hầm hố hơn bản tiêu chuẩn: lưới tản nhiệt Panamericana đặc trưng của AMG, hốc hút gió lớn, mâm xe cỡ lớn và ống xả kép bên hông. Đây là sự kết hợp hoàn hảo giữa khả năng off-road bền bỉ của Mercedes-Benz và hiệu suất tốc độ cao của AMG.",
        "price"=>"679.000đ",
        "specs"=>"Mô hình Mercedes-AMG G63 1:43 SOLIDO | Kích thước: 10×5×5 cm | Chất liệu: Kim loại + Nhựa | Cửa mở: Có | Lốp: Cao su | Trọng lượng: 320g",
        "warranty"=>"Bảo hành 6 tháng cho lỗi kỹ thuật, hỗ trợ đổi trả sản phẩm lỗi từ nhà sản xuất.",
        "reviews"=>"★★★★☆ – Chi tiết đẹp, nhỏ gọn, dễ trưng bày; sơn sắc nét, đáng giá tiền.",
        "category"=>"SieuXe"
    ],
    [
        "img"=>"moto.jpg",
        "name"=>"Mô hình mô tô KTM 1290 Super Duke 1:12 Maisto",
        "desc"=>"Chiếc xe có thiết kế như thật với những đường nét mô phỏng chi tiết, được sơn tĩnh điện tinh tế rất phù hợp để trưng bày ở phòng khách, phòng làm việc, showroom. Mô hình mô tô với kích cỡ nhỏ nhắn sẽ trở thành một món sưu tập thú vị trong phòng của bạn.",
        "price"=>"289.000đ",
        "specs"=>"Mô hình KTM 1290 Super Duke 1:12 Maisto | Chất liệu: Kim loại + Nhựa | Kích thước: 17×6×9 cm | Bánh xe cao su, có giảm xóc giả",
        "warranty"=>"Bảo hành 3 tháng đối với lỗi nhà sản xuất.",
        "reviews"=>"★★★★☆ – Mô tô chi tiết, bánh xe mềm, đẹp mắt; giá hợp lý cho tỉ lệ 1:12.",
        "category"=>"Moto"
    ],
    [
        "img"=>"moto2.jpg",
        "name"=>"Mô hình xe mô tô Ducati Monster 1:18 Maisto",
        "desc"=>"Chiếc xe có thiết kế như thật với những đường nét mô phỏng chi tiết, được sơn tĩnh điện tinh tế rất phù hợp để trưng bày ở phòng khách, phòng làm việc, showroom..Mô hình xe mô tô với kích cỡ nhỏ nhắn sẽ trở thành một món sưu tập thú vị trong phòng của bạn.",
        "price"=>"189.000đ",
        "specs"=>"Mô hình Ducati Monster 1:18 Maisto | Kích thước: 14×5×8 cm | Chất liệu: Kim loại + Nhựa | Bánh xe cao su | Trọng lượng: 250g",
        "warranty"=>"Bảo hành 3 tháng, đổi trả nếu lỗi kỹ thuật từ nhà sản xuất.",
        "reviews"=>"★★★☆☆ – Mô hình nhỏ gọn, phù hợp trưng bày; sơn hơi mỏng, nhưng tổng thể đẹp.",
        "category"=>"Moto"
    ],
    [
        "img"=>"F1.jpg",
        "name"=>"Mô hình xe Mercedes F1 2016 W007 Hybrid 1:18 Bburago",
        "desc"=>"Mô hình xe Mercedes-AMG W14 E Performance 1:43 của BBURAGO là sự kết hợp tinh tế giữa thiết kế xe đua hiện đại và nghệ thuật chế tác mô hình. Mercedes-AMG W14 E Performance là mẫu xe đua công thức 1 mang đậm dấu ấn công nghệ tiên tiến của đội đua Mercedes-AMG Petronas Formula One Team, được ra mắt trong mùa giải 2023. Với những cải tiến vượt bậc về khí động học, động cơ hybrid mạnh mẽ và thiết kế nổi bật, W14 không chỉ là biểu tượng của tốc độ mà còn đại diện cho tinh thần đổi mới không ngừng trong ngành đua xe thể thao.",
        "price"=>"1.849.000đ",
        "specs"=>"Mô hình Mercedes F1 2016 W007 Hybrid 1:18 Bburago | Kích thước: 25×10×8 cm | Chất liệu: Kim loại + Nhựa | Chi tiết nội thất đầy đủ | Trọng lượng: 1kg",
        "warranty"=>"Bảo hành 6 tháng, hỗ trợ sửa chữa hoặc đổi mới nếu lỗi kỹ thuật.",
        "reviews"=>"★★★★★ – Chi tiết tuyệt vời, màu sắc chính xác, cửa mở được; phù hợp để trưng bày sưu tầm.",
        "category"=>"F1"
    ],
    [
        "img"=>"F1_2.jpg",
        "name"=>"Mô hình xe F1 Mercedes-AMG W14 E Performance 1:43 BBURAGO",
        "desc"=>"Mô hình xe Mercedes-AMG W14 E Performance 1:43 của BBURAGO là sự kết hợp tinh tế giữa thiết kế xe đua hiện đại và nghệ thuật chế tác mô hình. Mercedes-AMG W14 E Performance là mẫu xe đua công thức 1 mang đậm dấu ấn công nghệ tiên tiến của đội đua Mercedes-AMG Petronas Formula One Team, được ra mắt trong mùa giải 2023. Với những cải tiến vượt bậc về khí động học, động cơ hybrid mạnh mẽ và thiết kế nổi bật, W14 không chỉ là biểu tượng của tốc độ mà còn đại diện cho tinh thần đổi mới không ngừng trong ngành đua xe thể thao.",
        "price"=>"479.000đ",
        "specs"=>"Mô hình Mercedes-AMG W14 E Performance 1:43 BBURAGO | Kích thước: 11×5×4 cm | Chất liệu: Kim loại + Nhựa | Bánh xe: Cao su | Trọng lượng: 350g",
        "warranty"=>"Bảo hành 6 tháng đối với lỗi kỹ thuật, hỗ trợ đổi mới sản phẩm lỗi từ nhà sản xuất.",
        "reviews"=>"★★★★☆ – Mô hình chi tiết, sơn bóng đẹp; tỉ lệ 1:43 chính xác, đáng giá tiền.",
        "category"=>"F1"
    ],
    [
        "img"=>"ferr.jpg",
        "name"=>"Mô hình xe Ferrari FXX K 1:24 Bburago",
        "desc"=>"Ferrari đã chính thức ra mắt phiên bản đua Ferrari FXX K của siêu xe LaFerrari tại sự kiện Finali Mondiali, diễn ra ở trường đua Yas Marina, Abu Dhabi. FXX K là một phần của chương trình thử nghiệm đặc biệt mà Ferrari xây dựng dành cho những khách hàng thân thiết nhất của hãng. Trong đó, chữ K trong tên xe là viết tắt của hệ thống phục hồi động năng KERS.",
        "price"=>"549.000đ",
        "specs"=>"Mô hình Ferrari FXX K 1:24 Bburago | Kích thước: 20×9×6 cm | Chất liệu: Kim loại + Nhựa | Cửa mở được | Bánh xe đánh lái | Trọng lượng: 700g",
        "warranty"=>"Bảo hành 3 tháng với lỗi kỹ thuật, đổi mới nếu lỗi sản xuất.",
        "reviews"=>"★★★★☆ – Mô hình đẹp, chi tiết, sơn bóng, bánh xe đánh lái; giá hợp lý.",
        "category"=>"SieuXe"
    ],
    [
        "img"=>"qp.jpg",
        "name"=>"Mô hình xe quân sự Hummer Humvee Battlefield Vehicle Military 1:18 KDW",
        "desc"=>"Chiếc mô hình xe quân sự Hummer Humvee Battlefield Vehicle Military tỉ lệ 1:18 KDW là một tác phẩm die-cast tinh xảo, tái hiện một cách chân thực và mạnh mẽ biểu tượng của sức mạnh trên chiến trường. Được chế tạo chất lượng cao, mô hình này gây ấn tượng bởi độ chi tiết vượt trội, từ lớp sơn rằn ri đặc trưng của quân đội, lốp cao su có vân gai y như thật, cho đến nội thất được mô phỏng tỉ mỉ. Đây không chỉ là một vật phẩm trưng bày tĩnh mà còn có tính năng động: bạn có thể mở được tất cả các cửa và nắp capo, cho phép người sưu tập khám phá cấu trúc bên trong của chiếc Humvee huyền thoại.",
        "price"=>"869.000đ",
        "specs"=>"Mô hình Hummer Humvee 1:18 KDW | Kích thước: 23×10×10 cm | Chất liệu: Kim loại + Nhựa | Cửa mở được | Nội thất chi tiết | Trọng lượng: 950g",
        "warranty"=>"Bảo hành 6 tháng đối với lỗi kỹ thuật, hỗ trợ đổi mới sản phẩm lỗi từ nhà sản xuất.",
        "reviews"=>"★★★★★ – Mô hình quân sự chi tiết, chắc chắn, cửa mở được; phù hợp trưng bày sưu tầm.",
        "category"=>"QuanSu"
    ]
];
// (Kết thúc danh sách sản phẩm)

// Lấy ID sản phẩm từ URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if(!isset($products[$id])) {
    echo "Sản phẩm không tồn tại!";
    exit;
}
$product = $products[$id];

// --- BƯỚC 2: THÊM CODE LỌC SẢN PHẨM LIÊN QUAN ---
$current_category = $product['category'];
$current_id = $id;

$related_products = array_filter($products, function($key) use ($products, $current_category, $current_id) {
    $p = $products[$key];
    $p_category = $p['category'];
    return ($p_category === $current_category && $key != $current_id);
}, ARRAY_FILTER_USE_KEY); 

$related_products = array_slice($related_products, 0, 4, true); 
// --- KẾT THÚC BƯỚC 2 ---
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

    <?php if (!empty($related_products)): ?>
    <section id="wp-products" class="related-products">
        <h2>Sản phẩm bạn có thể thích</h2>
        
        <ul id="list-products">
            <?php foreach($related_products as $key => $p): // $key là ID gốc ?>
                <li class="item">
                    <a href="product.php?id=<?php echo $key; ?>">
                        <img src="./assets/img/<?php echo $p['img']; ?>" alt="<?php echo $p['name']; ?>">
                    </a>
                    
                    <a href="product.php?id=<?php echo $key; ?>" class="name"><?php echo $p['name']; ?></a>
                    <div class="desc"><?php echo $p['desc']; ?></div>
                    <div class="price"><?php echo $p['price']; ?></div>
                    
                    <form method="POST" action="add_to_cart.php" class="form-add-to-cart-index">
                        <input type="hidden" name="product_id" value="<?php echo $key; ?>">
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
    </section>
    <?php endif; ?>
    </div>

<script>
// Tabs JS (Giữ nguyên)
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
tabs[0].click(); // Active tab đầu tiên

// Tăng giảm số lượng (Giữ nguyên)
const decreaseBtn = document.getElementById('decrease');
const increaseBtn = document.getElementById('increase');
const quantityInput = document.getElementById('quantity');

decreaseBtn.addEventListener('click', () => {
    let current = parseInt(quantityInput.value);
    if (current > 1) {
        quantityInput.value = current - 1;
    }
});

increaseBtn.addEventListener('click', () => {
    let current = parseInt(quantityInput.value);
    quantityInput.value = current + 1;
});

quantityInput.addEventListener('input', () => {
    let current = parseInt(quantityInput.value);
    if (isNaN(current) || current < 1) {
        quantityInput.value = 1;
    }
});


// --- BƯỚC 4: CẬP NHẬT JAVASCRIPT AJAX (Xử lý nhiều form) ---
// 1. Tìm TẤT CẢ các form 'thêm vào giỏ' trên trang
const allProductForms = document.querySelectorAll('#form-add-to-cart-product, .form-add-to-cart-index');

allProductForms.forEach(form => {
    form.addEventListener('submit', function(event) {
        // 2. Ngăn trang tải lại
        event.preventDefault(); 
        const formData = new FormData(form);
        formData.append('add_to_cart', ''); // Sửa lỗi gửi thiếu

        // 3. Tìm div thông báo tương ứng
        let messageDiv;
        if (form.id === 'form-add-to-cart-product') {
            // Nếu là form CHÍNH, tìm div bên ngoài
            messageDiv = document.getElementById('add-to-cart-message');
        } else {
            // Nếu là form LIÊN QUAN, tìm div bên trong
            messageDiv = form.querySelector('.add-to-cart-message-index');
        }

        // 4. Gửi AJAX
        fetch('add_to_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // 5. Hiển thị thông báo
            if (data.success) {
                messageDiv.textContent = data.message;
                messageDiv.style.color = 'green';
            } else {
                messageDiv.textContent = data.message;
                messageDiv.style.color = 'red';
            }
            messageDiv.style.display = 'block';

            // 6. Ẩn thông báo sau 3 giây
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
// --- KẾT THÚC BƯỚC 4 ---
</script>

</body>
</html>
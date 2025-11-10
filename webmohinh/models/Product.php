<?php
// Tệp: models/Product.php (Đã cập nhật)

class Product {
    
    // Thuộc tính private để giữ kết nối CSDL
    private $conn;

    // Hàm dựng: Nhận kết nối CSDL khi tạo đối tượng
    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * Lấy MỘT sản phẩm bằng ID
     * (Logic lấy từ product.php)
     */
    public function findById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Lấy TẤT CẢ sản phẩm (cho trang admin)
     * (Đã cập nhật để lấy "stock")
     */
    public function getAll() {
        // ĐÃ THÊM 'stock' VÀO CÂU LỆNH SELECT
        $result = $this->conn->query("SELECT id, name, price, sku, category, img, stock FROM products ORDER BY id DESC");
        return $result;
    }

    /**
     * Lấy các sản phẩm liên quan
     * (Logic lấy từ product.php)
     */
    public function findRelated($category, $current_id) {
        $sql = "SELECT * FROM products 
                WHERE category = ? AND id != ? 
                LIMIT 4";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $category, $current_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * Xử lý upload ảnh (hàm dùng chung, private)
     * (Logic lấy từ admin/add_product.php và admin/edit_product.php)
     */
    private function handleImageUpload($file) {
        $target_dir = "../assets/img/"; // Thư mục lưu ảnh
        
        // Tạo tên file duy nhất
        $img_name = uniqid() . '-' . basename($file["name"]);
        $target_file = $target_dir . $img_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Kiểm tra nếu là ảnh thật
        $check = getimagesize($file["tmp_name"]);
        if($check === false) {
            return [false, "File không phải là ảnh."];
        }
        
        // Kiểm tra định dạng
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            return [false, "Chỉ chấp nhận file JPG, JPEG & PNG."];
        }
        
        // Di chuyển file
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return [true, $img_name]; // Trả về tên file nếu thành công
        } else {
            return [false, "Lỗi khi tải ảnh lên (không thể di chuyển file)."];
        }
    }

    /**
     * Thêm sản phẩm mới
     * (Logic lấy từ admin/add_product.php, gộp trong admin/index.php)
     */
    public function create($data, $file) {
        // 1. Xử lý upload ảnh
        list($upload_success, $img_name_or_error) = $this->handleImageUpload($file);
        
        if (!$upload_success) {
            return $img_name_or_error; // Trả về thông báo lỗi
        }
        $img_name = $img_name_or_error;

        // 2. Chèn vào CSDL
        $sql = "INSERT INTO products (name, img, `desc`, price, specs, warranty, reviews, category, sku, brand, stock) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssssssssss", 
            $data['name'], 
            $img_name, // Tên file ảnh đã xử lý
            $data['desc'], 
            $data['price'], 
            $data['specs'], 
            $data['warranty'], 
            $data['reviews'], 
            $data['category'], 
            $data['sku'], 
            $data['brand'], 
            $data['stock']
        );

        if ($stmt->execute()) {
            return true;
        } else {
            return "Lỗi khi thêm vào CSDL: " . $stmt->error;
        }
    }

    /**
     * Cập nhật sản phẩm
     * (Logic lấy từ admin/edit_product.php)
     */
    public function update($data, $file) {
        $img_name = $data['current_img']; // Mặc định là ảnh cũ

        // 1. Kiểm tra xem có tải ảnh MỚI không
        if (isset($file) && $file['error'] == 0 && !empty($file['name'])) {
            list($upload_success, $img_name_or_error) = $this->handleImageUpload($file);

            if (!$upload_success) {
                return $img_name_or_error; // Trả về lỗi upload
            }
            
            $img_name = $img_name_or_error; // Cập nhật tên ảnh mới
            
            // Xóa ảnh cũ
            $old_img_path = "../assets/img/" . $data['current_img'];
            if (file_exists($old_img_path) && is_file($old_img_path)) {
                unlink($old_img_path);
            }
        }

        // 2. Cập nhật vào CSDL
        $sql = "UPDATE products SET 
                    name = ?, img = ?, `desc` = ?, price = ?, specs = ?, 
                    warranty = ?, reviews = ?, category = ?, sku = ?, 
                    brand = ?, stock = ?
                WHERE id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssssssssssi", 
            $data['name'], $img_name, $data['desc'], $data['price'], $data['specs'], $data['warranty'], 
            $data['reviews'], $data['category'], $data['sku'], $data['brand'], $data['stock'], $data['id']
        );

        if ($stmt->execute()) {
            return true;
        } else {
            return "Lỗi khi cập nhật CSDL: " . $stmt->error;
        }
    }

    /**
     * Xóa sản phẩm
     * (Logic lấy từ admin/delete_product.php, gộp trong admin/index.php)
     */
    public function delete($id) {
        // 1. Lấy tên file ảnh từ CSDL để xóa file
        $product = $this->findById($id);
        
        if ($product) {
            $file_path = "../assets/img/" . $product['img'];
            // Kiểm tra xem file có tồn tại không
            if (file_exists($file_path) && is_file($file_path)) {
                unlink($file_path); // Xóa file ảnh
            }
        }
        
        // 2. Xóa sản phẩm khỏi CSDL
        $stmt_delete = $this->conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt_delete->bind_param("i", $id);
        
        return $stmt_delete->execute();
    }
    
    /**
     * Lấy sản phẩm (có Lọc, Tìm kiếm, Phân trang)
     * (Logic lấy từ index.php)
     */
    public function getFiltered($options) {
        // $options là mảng chứa: tukhoa, category, page, itemsPerPage
        $tukhoa = $options['tukhoa'];
        $category = $options['category'];
        $page = $options['page'];
        $itemsPerPage = $options['itemsPerPage'];
        
        $startIndex = ($page - 1) * $itemsPerPage;

        $base_sql = "FROM products";
        $where_clauses = [];
        $params = [];
        $types = ""; // Chuỗi kiểu dữ liệu cho bind_param

        // 1. Lọc theo Tìm kiếm
        if (!empty($tukhoa)) {
            $where_clauses[] = "name LIKE ?";
            $params[] = "%" . $tukhoa . "%";
            $types .= "s";
        }

        // 2. Lọc theo Danh mục
        if (!empty($category)) {
            $where_clauses[] = "category = ?";
            $params[] = $category;
            $types .= "s";
        }

        // Nối các điều kiện WHERE
        if (!empty($where_clauses)) {
            $base_sql .= " WHERE " . implode(" AND ", $where_clauses);
        }

        // 3. Lấy TỔNG SỐ sản phẩm (để phân trang)
        $sql_total = "SELECT COUNT(id) as total " . $base_sql;
        $stmt_total = $this->conn->prepare($sql_total);
        if (!empty($params)) {
            $stmt_total->bind_param($types, ...$params);
        }
        $stmt_total->execute();
        $totalProducts = $stmt_total->get_result()->fetch_assoc()['total'];
        $totalPages = ($totalProducts > 0) ? ceil($totalProducts / $itemsPerPage) : 1;

        // 4. Lấy sản phẩm cho trang hiện tại
        $sql = "SELECT id, name, price, img, `desc` " . $base_sql . " LIMIT ?, ?";
        $types .= "ii";
        $params[] = $startIndex;
        $params[] = $itemsPerPage;

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $displayProducts = $stmt->get_result();
        
        // 5. Trả về cả 2 kết quả
        return [
            'products' => $displayProducts,
            'totalPages' => $totalPages
        ];
    }
    
}
?>
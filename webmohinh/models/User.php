<?php
// Tệp: models/User.php (Đã cập nhật)

class User {
    
    // Thuộc tính private để giữ kết nối CSDL
    private $conn;

    // Hàm dựng (Constructor)
    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * Lấy thông tin người dùng bằng username
     */
    public function findByUsername($username) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Lấy thông tin người dùng bằng email
     */
    private function findByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * Xử lý đăng ký
     */
    public function register($username, $email, $password) {
        // 1. Kiểm tra email tồn tại
        $result = $this->findByEmail($email);
        if ($result->num_rows > 0) {
            return "Email đã được sử dụng!"; 
        }
        
        // 2. Băm mật khẩu
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        
        // 3. Thêm user mới
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password_hash);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return "Đăng ký thất bại, vui lòng thử lại.";
        }
    }

    /**
     * Xử lý đăng nhập
     */
    public function login($email, $password) {
        $result = $this->findByEmail($email);

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                return $user; 
            }
        }
        return false; 
    }

    /**
     * Cập nhật thông tin (email, SĐT)
     */
    public function updateInfo($username, $email, $phone) {
        $stmt = $this->conn->prepare("UPDATE users SET email = ?, phone = ? WHERE username = ?");
        $stmt->bind_param("sss", $email, $phone, $username);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return "Cập nhật thất bại, vui lòng thử lại.";
        }
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword($username, $old_pass, $new_pass, $confirm_pass, $current_hash) {
        if (!password_verify($old_pass, $current_hash)) {
            return "Mật khẩu cũ không đúng.";
        }
        if ($new_pass !== $confirm_pass) {
            return "Mật khẩu xác nhận không khớp.";
        }
        
        $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $new_hash, $username);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return "Lỗi khi đổi mật khẩu, vui lòng thử lại.";
        }
    }

    // ===============================================
    // CÁC HÀM MỚI CHO ADMIN (ĐÃ THÊM Ở BƯỚC 19)
    // ===============================================

    /**
     * Lấy TẤT CẢ user (cho trang admin)
     * (Logic lấy từ admin/index.php)
     */
    public function getAll() {
        return $this->conn->query("SELECT * FROM users ORDER BY id DESC");
    }

    /**
     * Xóa user bằng ID
     * (Logic lấy từ admin/index.php)
     */
    public function deleteUserById($id) {
        $stmt_del = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt_del->bind_param("i", $id);
        return $stmt_del->execute();
    }
}
?>
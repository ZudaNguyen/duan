<?php
// Tệp: models/User.php

class User {
    
    // Thuộc tính private để giữ kết nối CSDL
    private $conn;

    // Hàm dựng (Constructor)
    // Hàm này sẽ tự động chạy khi bạn viết "new User($conn)"
    // Nó nhận kết nối CSDL ($conn) từ file connect.php
    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * Lấy thông tin người dùng bằng username
     * (Logic lấy từ user.php)
     */
    public function findByUsername($username) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Lấy thông tin người dùng bằng email
     * (Logic lấy từ login.php)
     */
    private function findByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * Xử lý đăng ký
     * (Logic lấy từ register.php)
     */
    public function register($username, $email, $password) {
        // 1. Kiểm tra email tồn tại
        $result = $this->findByEmail($email);
        if ($result->num_rows > 0) {
            return "Email đã được sử dụng!"; // Trả về thông báo lỗi
        }
        
        // 2. Băm mật khẩu
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        
        // 3. Thêm user mới
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password_hash);
        
        if ($stmt->execute()) {
            return true; // Đăng ký thành công
        } else {
            return "Đăng ký thất bại, vui lòng thử lại."; // Lỗi CSDL
        }
    }

    /**
     * Xử lý đăng nhập
     * (Logic lấy từ login.php và admin/login.php)
     */
    public function login($email, $password) {
        $result = $this->findByEmail($email);

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // 1. Kiểm tra mật khẩu
            if (password_verify($password, $user['password'])) {
                // Đăng nhập thành công, trả về dữ liệu user
                return $user; 
            }
        }
        
        // Sai email hoặc mật khẩu
        return false; 
    }

    /**
     * Cập nhật thông tin (email, SĐT)
     * (Logic lấy từ user.php)
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
     * (Logic lấy từ user.php)
     */
    public function changePassword($username, $old_pass, $new_pass, $confirm_pass, $current_hash) {
        // 1. Xác thực mật khẩu cũ
        if (!password_verify($old_pass, $current_hash)) {
            return "Mật khẩu cũ không đúng.";
        }
        
        // 2. Xác thực mật khẩu mới
        if ($new_pass !== $confirm_pass) {
            return "Mật khẩu xác nhận không khớp.";
        }
        
        // 3. Cập nhật mật khẩu mới
        $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $new_hash, $username);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return "Lỗi khi đổi mật khẩu, vui lòng thử lại.";
        }
    }
}
?>
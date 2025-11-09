<?php
$servername = "localhost";
$username = "root"; // thường mặc định là root
$password = "";     // mặc định XAMPP là rỗng
$dbname = "webmohinh_db"; // phải giống tên database bạn tạo

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>

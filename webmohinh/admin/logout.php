<?php
session_start();
session_destroy();
// Chuyển hướng về trang đăng nhập CỦA ADMIN
header("Location: login.php"); 
exit;
?>
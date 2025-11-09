<?php
session_start();
session_destroy();
header("Location: login.php"); // Chuyển về trang đăng nhập CỦA USER
exit;
?>
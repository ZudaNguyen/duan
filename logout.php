<?php
session_name('ADMIN_SESSION'); // Quan trọng: Phải khớp với tên lúc login
session_start();
session_destroy();
header("Location: login.php"); 
exit;
?>
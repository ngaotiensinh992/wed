<?php
session_start();

// Xóa tất cả dữ liệu trong session
session_unset();

// Hủy session
session_destroy();

// Chuyển hướng về trang login
header("Location: login.php");
exit;
?>

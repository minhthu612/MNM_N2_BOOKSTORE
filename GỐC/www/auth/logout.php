<?php
include '../config.php';

// Xóa tất cả session
session_destroy();

// Chuyển hướng về trang chủ
header("Location: ../index.php");
exit();
?>

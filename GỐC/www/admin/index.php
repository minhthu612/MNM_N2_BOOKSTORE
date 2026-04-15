<?php
// 1. Nhúng cấu hình hệ thống
require_once '../config.php';

// 2. Nhúng kiểm tra quyền (File này thường chứa session_start() và check login)
require_once '../includes/admin_check.php';

// 3. Kiểm tra logic phân quyền (Admin hoặc Manager mới được vào)
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Manager')) {
    // Nếu không đủ quyền, đẩy về trang chủ hoặc trang báo lỗi
    $_SESSION['error'] = "Bạn không có quyền truy cập khu vực này.";
    header("Location: ../index.php");
    exit();
}

/**
 * 4. Chuyển hướng thẳng vào trang quản lý mục tiêu
 * Bạn có thể thay đổi 'books/index.php' thành trang khác nếu muốn
 */
header("Location: books/index.php");
exit();

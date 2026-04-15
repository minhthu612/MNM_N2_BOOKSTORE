<?php
/**
 * client_check.php - Kiểm tra quyền khách hàng
 */

// Bắt đầu session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Kiểm tra đăng nhập (Viết kiểu SV rành mạch)
$da_dang_nhap = false;
if (isset($_SESSION['user_id'])) {
    $da_dang_nhap = true;
}

if ($da_dang_nhap == false) {
    $_SESSION['error'] = "Vui lòng đăng nhập để tiếp tục!";
    header('Location: ../auth/login.php');
    exit();
}

// 2. Kiểm tra vai trò người dùng (Role)
$vai_tro = '';
if (isset($_SESSION['role'])) {
    $vai_tro = $_SESSION['role'];
}

// Nếu không phải là Khách hàng (Customer)
if ($vai_tro != 'Customer') {
    $_SESSION['error'] = "Bạn không có quyền vào khu vực dành cho khách hàng!";
    
    // Nếu là Admin thì đẩy về trang Admin
    if ($vai_tro == 'Admin') {
        header('Location: ../admin/index.php');
    } else {
        // Các trường hợp khác về trang chủ
        header('Location: ../index.php');
    }
    exit();
}

// 3. Kiểm tra trạng thái tài khoản (Có bị khóa hay không)
$trang_thai_khoa = false;
if (isset($_SESSION['is_active'])) {
    if ($_SESSION['is_active'] == 0) {
        $trang_thai_khoa = true;
    }
}

if ($trang_thai_khoa == true) {
    // Xóa hết phiên đăng nhập
    session_unset();
    session_destroy();
    
    // Khởi động lại session mới để lưu thông báo lỗi
    session_start();
    $_SESSION['error'] = "Tài khoản của bạn hiện đang bị khóa. Vui lòng liên hệ Admin.";
    header('Location: ../auth/login.php');
    exit();
}

// Nếu chạy đến đây tức là mọi thứ OK, cho phép khách hàng xem trang
?>
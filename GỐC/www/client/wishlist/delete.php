<?php
session_start();
require_once '../../config.php';

// 1. KIỂM TRA ĐĂNG NHẬP (Kiểu rành mạch nhất)
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    // Nếu chưa đăng nhập thì đẩy về trang login
    header("Location: ../../auth/login.php");
    exit();
}

// 2. LẤY ID CỦA DÒNG CẦN XÓA TỪ URL (Gỡ bỏ toán tử ??)
$wishlist_id = 0;
if (isset($_GET['id'])) {
    $wishlist_id = (int)$_GET['id'];
}

// 3. XỬ LÝ LOGIC XÓA TRẦN
if ($wishlist_id > 0) {
    
    // Sử dụng query trần nối biến trực tiếp (Không dùng prepare)
    // Thêm điều kiện user_id để đảm bảo người dùng chỉ xóa được đồ của chính họ
    $sql_xoa = "DELETE FROM wishlist WHERE wishlist_id = '$wishlist_id' AND user_id = '$user_id'";
    
    if ($conn->query($sql_xoa)) {
        // Nếu câu lệnh SQL chạy thành công
        $_SESSION['success'] = "Đã bỏ sản phẩm ra khỏi danh sách yêu thích!";
    } else {
        // Nếu có lỗi SQL (thường do sai tên cột hoặc bảng)
        $thong_bao_loi = $conn->error;
        $_SESSION['error'] = "Lỗi hệ thống: " . $thong_bao_loi;
    }
}

// 4. QUAY LẠI TRANG DANH SÁCH YÊU THÍCH
header("Location: index.php");
exit();
?>
<?php
session_start();
require_once '../../config.php';

// 1. KIỂM TRA ĐĂNG NHẬP KIỂU TRUYỀN THỐNG
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $_SESSION['error'] = "Vui lòng đăng nhập để thêm vào yêu thích!";
    header("Location: ../../auth/login.php");
    exit();
}

// 2. LẤY ID SÁCH TỪ URL (Gỡ bỏ toán tử rút gọn ??)
$id_to_add = 0;
if (isset($_GET['book_id'])) {
    $id_to_add = (int)$_GET['book_id'];
}

// 3. XỬ LÝ LOGIC CHÍNH
if ($id_to_add > 0) {
    
    // Kiểm tra xem sách này đã có trong wishlist của user chưa (Dùng query trần)
    $sql_kiem_tra = "SELECT wishlist_id FROM wishlist WHERE user_id = '$user_id' AND book_id = '$id_to_add'";
    $ket_qua_kiem_tra = $conn->query($sql_kiem_tra);

    if ($ket_qua_kiem_tra->num_rows == 0) {
        // Nếu chưa có thì thực hiện thêm mới
        $sql_them = "INSERT INTO wishlist (user_id, book_id) VALUES ('$user_id', '$id_to_add')";
        $thuc_thi = $conn->query($sql_them);
        
        if ($thuc_thi) {
            $_SESSION['success'] = "Đã thêm vào danh sách yêu thích thành công!";
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra, không thể thêm.";
        }
    } else {
        // Nếu đã có rồi
        $_SESSION['info'] = "Sách này đã nằm trong danh sách yêu thích của bạn rồi.";
    }
}

// 4. QUAY LẠI TRANG TRƯỚC ĐÓ (Gỡ bỏ hoàn toàn logic phức tạp)
$duong_dan_cu = '../../index.php';
if (isset($_SERVER['HTTP_REFERER'])) {
    $duong_dan_cu = $_SERVER['HTTP_REFERER'];
}

header("Location: " . $duong_dan_cu);
exit();
?>
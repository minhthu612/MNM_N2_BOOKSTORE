<?php
require_once '../../includes/client_check.php';
require_once '../../config.php';

// 1. LẤY ID TỪ URL (Phải dùng đúng tên 'id' để khớp với link từ trang giỏ hàng)
$cart_item_id = 0;
if (isset($_GET['id'])) {
    $cart_item_id = (int)$_GET['id'];
}

// 2. KIỂM TRA NẾU CÓ ID THÌ MỚI XỬ LÝ
if ($cart_item_id != 0) {
    
    // Sử dụng query trần (Xóa bỏ đoạn 'OR id =' gây lỗi lúc nãy)
    $sql_xoa = "DELETE FROM cart_items WHERE cart_item_id = '$cart_item_id'";
    
    if ($conn->query($sql_xoa)) {
        // Nếu xóa thành công
        $_SESSION['success'] = "Đã gỡ sản phẩm này khỏi giỏ hàng!";
    } else {
        // Nếu có lỗi SQL xảy ra (như sai tên cột)
        $loi_he_thong = $conn->error;
        $_SESSION['error'] = "Lỗi hệ thống: " . $loi_he_thong;
    }
} else {
    // Trường hợp này xảy ra nếu URL không có ?id=...
    $_SESSION['error'] = "Không tìm thấy mã sản phẩm để xóa.";
}

// 3. QUAY LẠI TRANG GIỎ HÀNG
header('Location: index.php');
exit();
?>
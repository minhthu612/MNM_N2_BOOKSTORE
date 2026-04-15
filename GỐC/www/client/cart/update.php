<?php
require_once '../../includes/client_check.php';
require_once '../../config.php';

// 1. CHỈ XỬ LÝ KHI CÓ DỮ LIỆU GỬI LÊN (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Lấy ID dòng sản phẩm trong giỏ (ép kiểu số nguyên cho an toàn)
    $cart_item_id = 0;
    if (isset($_POST['cart_item_id'])) {
        $cart_item_id = (int)$_POST['cart_item_id'];
    }

    // Lấy số lượng mới từ ô nhập
    $quantity = 0;
    if (isset($_POST['quantity'])) {
        $quantity = (int)$_POST['quantity'];
    }

    // 2. KIỂM TRA LOGIC CƠ BẢN
    if ($cart_item_id != 0) {
        
        if ($quantity >= 1) {
            // Sử dụng query trần, nối chuỗi biến trực tiếp
            // Chỉ dùng cart_item_id vì bảng của bạn không có cột id
            $sql_update = "UPDATE cart_items SET quantity = '$quantity' WHERE cart_item_id = '$cart_item_id'";
            
            if ($conn->query($sql_update)) {
                $_SESSION['success'] = "Đã cập nhật số lượng mới thành công!";
            } else {
                $loi_sql = $conn->error;
                $_SESSION['error'] = "Lỗi hệ thống không thể cập nhật: " . $loi_sql;
            }
        } else {
            // Nếu số lượng nhỏ hơn 1 thì báo lỗi hoặc giữ nguyên
            $_SESSION['error'] = "Số lượng đặt hàng phải ít nhất là 1 cuốn.";
        }
        
    } else {
        $_SESSION['error'] = "Yêu cầu không hợp lệ, không tìm thấy sản phẩm.";
    }
}

// 3. QUAY LẠI TRANG GIỎ HÀNG ĐỂ XEM KẾT QUẢ
header('Location: index.php');
exit();
?>
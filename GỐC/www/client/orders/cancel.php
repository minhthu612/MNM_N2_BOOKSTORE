<?php
session_start();
require_once '../../config.php';

// 1. LẤY ID ĐƠN HÀNG VÀ USER ID KIỂU TRUYỀN THỐNG
$order_id = 0;
if (isset($_GET['id'])) {
    $order_id = (int)$_GET['id'];
}

$user_id = '';
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

// Nếu không có mã đơn thì quay về danh sách ngay
if ($order_id == 0) {
    header("Location: index.php");
    exit();
}

// 2. KIỂM TRA ĐƠN HÀNG (Dùng query trần)
// Chỉ cho phép hủy nếu đơn hàng thuộc về mình và đang ở trạng thái 'pending'
$sql_check = "SELECT status FROM orders WHERE order_id = '$order_id' AND user_id = '$user_id' AND status = 'pending'";
$res_check = $conn->query($sql_check);

if ($res_check->num_rows > 0) {
    
    // 3. CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG THÀNH 'cancelled'
    $sql_update_order = "UPDATE orders SET status = 'cancelled' WHERE order_id = '$order_id'";
    $thuc_thi_huy = $conn->query($sql_update_order);

    if ($thuc_thi_huy == true) {
        
        // 4. HOÀN TRẢ SỐ LƯỢNG VÀO KHO (Lấy danh sách sản phẩm trong đơn)
        $sql_get_items = "SELECT book_id, quantity FROM order_items WHERE order_id = '$order_id'";
        $res_items = $conn->query($sql_get_items);

        if ($res_items->num_rows > 0) {
            // Duyệt từng món hàng để trả lại kho và trừ số lượng đã bán
            while ($row_item = $res_items->fetch_assoc()) {
                $ma_sach = $row_item['book_id'];
                $so_luong = $row_item['quantity'];

                // Cộng lại kho trong bảng inventory
                $sql_hoan_kho = "UPDATE inventory SET stock = stock + $so_luong WHERE book_id = '$ma_sach'";
                $conn->query($sql_hoan_kho);

                // Trừ bớt số lượng đã bán trong bảng books
                $sql_tru_da_ban = "UPDATE books SET sold_quantity = sold_quantity - $so_luong WHERE book_id = '$ma_sach'";
                $conn->query($sql_tru_da_ban);
            }
        }

        $_SESSION['success'] = "Đã hủy đơn hàng #$order_id thành công!";
    } else {
        $_SESSION['error'] = "Có lỗi xảy ra trong quá trình hủy đơn hàng.";
    }

} else {
    // Nếu đơn hàng đã giao hoặc không phải của mình
    $_SESSION['error'] = "Đơn hàng này không thể hủy.";
}

// 5. QUAY LẠI TRANG DANH SÁCH ĐƠN HÀNG
header("Location: index.php");
exit();
?>
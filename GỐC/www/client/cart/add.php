<?php
require_once '../../includes/client_check.php';
require_once '../../config.php';

// 1. LẤY USER ID VÀ BOOK ID KIỂU TRUYỀN THỐNG
$user_id = $_SESSION['user_id'];

$book_id = 0;
if (isset($_POST['book_id'])) {
    $book_id = (int)$_POST['book_id'];
} else {
    if (isset($_GET['book_id'])) {
        $book_id = (int)$_GET['book_id'];
    }
}

// Nếu không có mã sách thì quay về trang chủ ngay
if ($book_id == 0) {
    header('Location: ../../index.php');
    exit();
}

// 2. TÌM HOẶC TẠO GIỎ HÀNG CHO NGƯỜI DÙNG (Dùng query trần)
$sql_check_cart = "SELECT cart_id FROM cart WHERE user_id = '$user_id'";
$res_cart = $conn->query($sql_check_cart);
$row_cart = $res_cart->fetch_assoc();

$cart_id = 0;
if ($row_cart != null) {
    // Nếu đã có giỏ hàng trong bảng cart
    $cart_id = $row_cart['cart_id'];
} else {
    // Nếu chưa có thì chèn mới một dòng vào bảng cart
    $sql_create_cart = "INSERT INTO cart (user_id) VALUES ('$user_id')";
    if ($conn->query($sql_create_cart)) {
        $cart_id = $conn->insert_id;
    }
}

// 3. KIỂM TRA XEM SÁCH NÀY ĐÃ CÓ TRONG GIỎ HÀNG CHI TIẾT CHƯA
$sql_check_item = "SELECT * FROM cart_items WHERE cart_id = '$cart_id' AND book_id = '$book_id'";
$res_item = $conn->query($sql_check_item);
$existing = $res_item->fetch_assoc();

if ($existing != null) {
    // TRƯỜNG HỢP 1: SÁCH ĐÃ CÓ - Tăng số lượng lên thêm 1
    $so_luong_cu = $existing['quantity'];
    $so_luong_moi = $so_luong_cu + 1;
    
    // Tự động nhận diện cột ID (Tránh lỗi sai tên cột id hay cart_item_id)
    $c_item_id = 0;
    if (isset($existing['cart_item_id'])) {
        $c_item_id = $existing['cart_item_id'];
    }

    $sql_update = "UPDATE cart_items SET quantity = '$so_luong_moi' WHERE cart_item_id = '$c_item_id'";
    $conn->query($sql_update);

} else {
    // TRƯỜNG HỢP 2: SÁCH CHƯA CÓ - Thêm dòng mới với số lượng là 1
    $sql_insert = "INSERT INTO cart_items (cart_id, book_id, quantity) VALUES ('$cart_id', '$book_id', 1)";
    $conn->query($sql_insert);
}

// 4. THÔNG BÁO VÀ CHUYỂN HƯỚNG
$_SESSION['success'] = "Đã thêm cuốn sách này vào giỏ hàng của bạn!";
header('Location: index.php'); 
exit();
?>
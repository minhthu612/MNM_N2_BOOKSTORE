<?php
// Đảm bảo file client_check.php đã có session_start()
require_once '../../includes/client_check.php';
require_once '../../config.php';

// 1. LẤY DỮ LIỆU TỪ SESSION VÀ POST
$user_id = $_SESSION['user_id'];
$set_id = 0;

if (isset($_POST['set_id'])) {
    $set_id = (int)$_POST['set_id'];
}

// Nếu không có set_id hoặc truy cập trái phép thì quay về trang chủ
if ($set_id == 0) {
    header('Location: ../../index.php');
    exit();
}

// 2. TÌM GIỎ HÀNG HIỆN TẠI CỦA USER
$sql_cart = "SELECT cart_id FROM cart WHERE user_id = '$user_id'";
$res_cart = $conn->query($sql_cart);
$row_cart = $res_cart->fetch_assoc();

$cart_id = 0;
if ($row_cart != null) {
    $cart_id = $row_cart['cart_id'];
} else {
    // Nếu chưa có giỏ thì tạo mới ngay
    $sql_new_cart = "INSERT INTO cart (user_id) VALUES ('$user_id')";
    $conn->query($sql_new_cart);
    $cart_id = $conn->insert_id;
}

// 3. LẤY DANH SÁCH SÁCH CON TRONG BỘ (SET) NÀY
$sql_items = "SELECT book_id, quantity FROM book_set_items WHERE set_id = '$set_id'";
$res_items = $conn->query($sql_items);

// 4. KIỂM TRA VÀ THÊM TỪNG MÓN VÀO GIỎ
if ($res_items->num_rows > 0) {
    
    // Duyệt trực tiếp kết quả truy vấn (Cách làm chuẩn nhất của SV)
    while ($item = $res_items->fetch_assoc()) {
        $id_sach = $item['book_id'];
        $so_luong_trong_bo = $item['quantity'];

        // Kiểm tra xem cuốn sách này đã có trong giỏ hàng chi tiết (cart_items) chưa
        $sql_check_exists = "SELECT cart_item_id, quantity FROM cart_items 
                             WHERE cart_id = '$cart_id' AND book_id = '$id_sach'";
        $res_exists = $conn->query($sql_check_exists);
        $existing_row = $res_exists->fetch_assoc();

        if ($existing_row != null) {
            // TRƯỜNG HỢP 1: ĐÃ CÓ - Cập nhật cộng thêm số lượng
            $sl_moi = $existing_row['quantity'] + $so_luong_trong_bo;
            $c_item_id = $existing_row['cart_item_id'];
            
            $sql_update = "UPDATE cart_items SET quantity = '$sl_moi' 
                           WHERE cart_item_id = '$c_item_id'";
            $conn->query($sql_update);
        } else {
            // TRƯỜNG HỢP 2: CHƯA CÓ - Thêm mới hoàn toàn vào giỏ
            $sql_insert_item = "INSERT INTO cart_items (cart_id, book_id, quantity) 
                                VALUES ('$cart_id', '$id_sach', '$so_luong_trong_bo')";
            $conn->query($sql_insert_item);
        }
    }
    
    $_SESSION['success'] = "Đã thêm toàn bộ sản phẩm trong bộ sách vào giỏ hàng!";
} else {
    $_SESSION['error'] = "Bộ sách này hiện đang bảo trì nội dung, vui lòng thử lại sau.";
}

// 5. CHUYỂN HƯỚNG VỀ TRANG GIỎ HÀNG ĐỂ NGƯỜI DÙNG THẤY KẾT QUẢ
header('Location: index.php');
exit();
?>
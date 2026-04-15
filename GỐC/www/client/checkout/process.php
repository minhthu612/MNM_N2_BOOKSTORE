<?php
session_start();
require_once '../../config.php';

// Kiểm tra nếu người dùng nhấn nút đặt hàng (gửi dữ liệu POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $user_id = $_SESSION['user_id'];
    
    // 1. LẤY DỮ LIỆU TỪ FORM (Dùng cách gán biến đơn giản nhất)
    $notes = '';
    if (isset($_POST['notes'])) {
        $notes = $_POST['notes'];
    }
    
    $payment_method = 'COD';
    if (isset($_POST['payment_method'])) {
        $payment_method = $_POST['payment_method'];
    }
    
    $shipping_address = '';
    if (isset($_POST['address'])) {
        $shipping_address = $_POST['address'];
    }

    // Nếu địa chỉ trống (do chưa chọn), lôi địa chỉ mặc định từ DB ra làm dự phòng
    if ($shipping_address == '') {
        $sql_default = "SELECT street, ward, district, city FROM addresses WHERE user_id = '$user_id' AND is_default = 1 LIMIT 1";
        $res_default = $conn->query($sql_default);
        $row_default = $res_default->fetch_assoc();
        
        if ($row_default != null) {
            $shipping_address = $row_default['street'] . ', ' . $row_default['ward'] . ', ' . $row_default['district'] . ', ' . $row_default['city'];
        } else {
            $shipping_address = "Chưa xác định địa chỉ";
        }
    }

    // 2. TÍNH TOÁN TIỀN BẠC (Dùng foreach sau khi lấy dữ liệu ra mảng)
    $sql_cart = "SELECT ci.*, b.price FROM cart_items ci 
                 JOIN books b ON ci.book_id = b.book_id 
                 JOIN cart c ON ci.cart_id = c.cart_id 
                 WHERE c.user_id = '$user_id'";
    $res_cart = $conn->query($sql_cart);
    
    $cart_list = array();
    $subtotal = 0;
    
    if ($res_cart->num_rows > 0) {
        while ($row = $res_cart->fetch_assoc()) {
            $cart_list[] = $row;
            $subtotal = $subtotal + ($row['price'] * $row['quantity']);
        }
    } else {
        header("Location: ../cart/index.php");
        exit();
    }

    $discount = 0;
    if (isset($_SESSION['discount_amount'])) {
        $discount = $_SESSION['discount_amount'];
    }
    
    $shipping_fee = 30000;
    $total_amount = $subtotal - $discount + $shipping_fee;
    if ($total_amount < 0) {
        $total_amount = 0;
    }

    // 3. LƯU ĐƠN HÀNG VÀO DATABASE (Dùng If lồng nhau thay vì Transaction phức tạp)
    $sql_order = "INSERT INTO orders (user_id, shipping_address, notes, total_amount, payment_method, status, created_at) 
                  VALUES ('$user_id', '$shipping_address', '$notes', '$total_amount', '$payment_method', 'pending', NOW())";
    
    if ($conn->query($sql_order)) {
        $order_id = $conn->insert_id;

        // 4. LƯU CHI TIẾT ĐƠN HÀNG VÀ TRỪ KHO (Dùng foreach)
        foreach ($cart_list as $item) {
            $b_id = $item['book_id'];
            $qty = $item['quantity'];
            $price = $item['price'];

            // Chèn vào bảng chi tiết đơn hàng
            $conn->query("INSERT INTO order_items (order_id, book_id, quantity, price) VALUES ('$order_id', '$b_id', '$qty', '$price')");
            
            // Trừ kho hàng trong bảng inventory
            $conn->query("UPDATE inventory SET stock = stock - $qty WHERE book_id = '$b_id'");
            
            // Tăng số lượng đã bán trong bảng books
            $conn->query("UPDATE books SET sold_quantity = sold_quantity + $qty WHERE book_id = '$b_id'");
        }

        // 5. DỌN DẸP GIỎ HÀNG VÀ SESSION
        $conn->query("DELETE ci FROM cart_items ci JOIN cart c ON ci.cart_id = c.cart_id WHERE c.user_id = '$user_id'");
        
        if (isset($_SESSION['discount_amount'])) {
            unset($_SESSION['discount_amount']);
        }
        if (isset($_SESSION['coupon_code_used'])) {
            unset($_SESSION['coupon_code_used']);
        }

        // CHUYỂN ĐẾN TRANG THÀNH CÔNG
        header("Location: success.php?id=" . $order_id);
        exit();

    } else {
        // Nếu lỗi tạo đơn hàng
        $_SESSION['error'] = "Không thể tạo đơn hàng. Vui lòng thử lại!";
        header("Location: index.php");
        exit();
    }
}
?>
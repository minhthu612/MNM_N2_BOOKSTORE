<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

// 1. Lấy ID người dùng từ URL theo kiểu truyền thống
$user_id = 0;
if (isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
}

if ($user_id == 0) {
    $_SESSION['error'] = "Không tìm thấy người dùng cần kích hoạt.";
    header('Location: index.php');
    exit();
}

// 2. Lấy thông tin người dùng bằng query trần (nối chuỗi trực tiếp)
$sql_user = "SELECT username, status FROM users WHERE user_id = '$user_id'";
$res_user = $conn->query($sql_user);
$user = $res_user->fetch_assoc();

// Kiểm tra người dùng có tồn tại không
if ($user == null) {
    $_SESSION['error'] = "Người dùng không tồn tại trên hệ thống.";
    header('Location: index.php');
    exit();
}

// 3. Kiểm tra trạng thái hiện tại
if ($user['status'] == 'Active') {
    $_SESSION['warning'] = "Tài khoản này đã ở trạng thái Kích hoạt rồi.";
    header('Location: index.php');
    exit();
}

// 4. Thực hiện cập nhật trạng thái bằng query trần
$update_sql = "UPDATE users SET status = 'Active' WHERE user_id = '$user_id'";

if ($conn->query($update_sql)) {
    $u_name = $user['username'];
    $_SESSION['success'] = "Đã kích hoạt thành công tài khoản: " . $u_name;
} else {
    $loi = $conn->error;
    $_SESSION['error'] = "Lỗi khi kích hoạt tài khoản: " . $loi;
}

// Quay lại trang danh sách
header('Location: index.php');
exit();
?>
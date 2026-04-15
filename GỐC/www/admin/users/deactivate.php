<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

// 1. Lấy ID người dùng từ URL (Viết kiểu tường minh)
$user_id = 0;
if (isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
}

if ($user_id == 0) {
    $_SESSION['error'] = "Không tìm thấy mã người dùng để xử lý.";
    header('Location: index.php');
    exit();
}

// 2. Lấy thông tin người dùng bằng query trần (nối chuỗi trực tiếp)
$sql_check = "SELECT username, status FROM users WHERE user_id = '$user_id'";
$res_check = $conn->query($sql_check);
$user = $res_check->fetch_assoc();

// Kiểm tra người dùng có tồn tại không
if ($user == null) {
    $_SESSION['error'] = "Người dùng này không tồn tại trên hệ thống.";
    header('Location: index.php');
    exit();
}

// 3. Kiểm tra trạng thái hiện tại (Nếu đã khóa rồi thì không làm nữa)
if ($user['status'] == 'Inactive') {
    $_SESSION['warning'] = "Tài khoản này hiện đang ở trạng thái Bị khóa rồi.";
    header('Location: index.php');
    exit();
}

// 4. Thực hiện cập nhật trạng thái sang 'Inactive' bằng query trần
$sql_update = "UPDATE users SET status = 'Inactive' WHERE user_id = '$user_id'";

if ($conn->query($sql_update)) {
    $ten_dang_nhap = $user['username'];
    $_SESSION['success'] = "Đã khóa thành công tài khoản người dùng: " . $ten_dang_nhap;
} else {
    $loi_he_thong = $conn->error;
    $_SESSION['error'] = "Lỗi kỹ thuật, không thể khóa tài khoản: " . $loi_he_thong;
}

// 5. Quay lại trang danh sách người dùng
header('Location: index.php');
exit();
?>
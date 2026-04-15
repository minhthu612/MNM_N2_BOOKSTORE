<?php
require_once '../../includes/client_check.php';
require_once '../../config.php';

/* Lấy user_id từ session */
$user_id = $_SESSION['user_id'];

/* Lấy id địa chỉ cần xóa */
$id = 0;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
}

/* Chỉ xử lý khi id hợp lệ */
if ($id > 0) {

    /*
        Chỉ cho phép:
        - Xóa địa chỉ của chính user đang đăng nhập
        - Không được xóa địa chỉ mặc định (is_default = 0)
    */
    $sql = "
        DELETE FROM addresses
        WHERE address_id = $id
          AND user_id = '$user_id'
          AND is_default = 0
    ";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        if (mysqli_affected_rows($conn) > 0) {
            $_SESSION['success'] = "Đã xóa địa chỉ thành công!";
        } else {
            $_SESSION['error'] = "Không thể xóa địa chỉ mặc định hoặc địa chỉ không tồn tại!";
        }
    } else {
        $_SESSION['error'] = "Có lỗi xảy ra khi xóa địa chỉ!";
    }
}

/* Quay về trang checkout */
header("Location: ../checkout/index.php");
exit();
?>

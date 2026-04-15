<?php
// Dòng số 1, không có khoảng trắng phía trên
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Đường dẫn file config nối chuỗi đơn giản
require_once __DIR__ . '/../config.php';

/* =========================================================
   1. HÀM KIỂM TRA ĐĂNG NHẬP (require_auth)
   Mục đích: Bắt buộc người dùng phải đăng nhập mới được xem trang
   ========================================================= */
function require_auth($redirect_url = '../auth/login.php') {
    // Gọi biến kết nối toàn cục
    global $conn;
   
    // Kiểm tra xem đã có User ID trong Session chưa
    if (isset($_SESSION['user_id']) == false) {
        $_SESSION['error'] = "Vui lòng đăng nhập để tiếp tục!";
        header("Location: $redirect_url");
        exit();
    }
   
    $id_nguoi_dung = $_SESSION['user_id'];
    
    // Sử dụng query trần, nối chuỗi trực tiếp kiểu SV
    $lenh_sql = "SELECT * FROM users WHERE user_id = '$id_nguoi_dung'";
    $ket_qua = $conn->query($lenh_sql);
   
    if ($ket_qua && $ket_qua->num_rows == 1) {
        $user_data = $ket_qua->fetch_assoc();
       
        // Kiểm tra trạng thái tài khoản
        if ($user_data['status'] == 'Pending') {
            $_SESSION['warning'] = "Tài khoản của bạn đang chờ quản trị viên phê duyệt.";
        }
        
        return $user_data;
    } else {
        // Nếu không tìm thấy hoặc lỗi, xóa session và bắt đăng nhập lại
        if (isset($_SESSION['user_id'])) {
            unset($_SESSION['user_id']);
        }
        $_SESSION['error'] = "Tài khoản không hợp lệ hoặc đã bị xóa.";
        header("Location: $redirect_url");
        exit();
    }
}

/* =========================================================
   2. HÀM KIỂM TRA QUYỀN (check_role)
   Mục đích: Chỉ cho Admin hoặc Manager vào trang quản trị
   ========================================================= */
function check_role($allowed_roles = array(), $redirect_url = '../index.php') {
    // Trước tiên phải đăng nhập đã
    $user = require_auth();
    
    $quyen_hien_tai = $user['role'];
    $co_quyen = false;

    // Duyệt mảng bằng foreach để kiểm tra quyền (đúng chất code thủ công)
    foreach ($allowed_roles as $role_check) {
        if ($quyen_hien_tai == $role_check) {
            $co_quyen = true;
        }
    }
   
    if ($co_quyen == false) {
        $_SESSION['error'] = "Bạn không có đủ quyền hạn để vào khu vực này!";
        header("Location: $redirect_url");
        exit();
    }
   
    return $user;
}

/* =========================================================
   3. HÀM HIỂN THỊ THÔNG BÁO (display_message)
   Mục đích: Hiện các thông báo Xanh/Đỏ/Vàng trên đầu trang
   ========================================================= */
function display_message($type = 'success') {
    if (isset($_SESSION[$type])) {
        $thong_bao = $_SESSION[$type];
        
        // Xác định màu sắc alert bằng if/else rành mạch
        $mau_sac = 'alert-success';
        if ($type == 'error') {
            $mau_sac = 'alert-danger';
        } else {
            if ($type == 'warning') {
                $mau_sac = 'alert-warning';
            }
        }
        
        // In ra HTML trần, dùng link Bootstrap (Đã gỡ nút tắt JS)
        echo "<div class='alert $mau_sac' style='border-radius: 10px; margin-bottom: 20px;'>";
        echo "<strong>Thông báo:</strong> " . $thong_bao;
        echo "</div>";
        
        // Xóa thông báo sau khi hiện để không hiện lại lần sau
        unset($_SESSION[$type]);
    }
}
?>
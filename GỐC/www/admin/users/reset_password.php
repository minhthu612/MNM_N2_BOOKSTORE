<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

// 1. Lấy ID từ URL kiểu truyền thống
$user_id = 0;
if (isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
}

if ($user_id == 0) {
    $_SESSION['error'] = "Không tìm thấy người dùng.";
    header('Location: index.php');
    exit();
}

// 2. Lấy thông tin người dùng bằng query trần
$sql_user = "SELECT user_id, username, email, fullname FROM users WHERE user_id = '$user_id'";
$res_user = $conn->query($sql_user);
$user = $res_user->fetch_assoc();

if ($user == null) {
    $_SESSION['error'] = "Người dùng không tồn tại.";
    header('Location: index.php');
    exit();
}

$error = '';

// 3. Xử lý khi nhấn nút Đặt lại mật khẩu (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Kiểm tra logic bằng PHP (Thay cho JS)
    if (strlen($new_password) < 6) {
        $error = "Mật khẩu mới phải có ít nhất 6 ký tự!";
    } else {
        if ($new_password != $confirm_password) {
            $error = "Xác nhận mật khẩu không khớp, vui lòng nhập lại!";
        } else {
            // Hash mật khẩu (md5 cho đơn giản kiểu SV)
            $password_hashed = md5($new_password);
            
            // Cập nhật bằng query trần nối chuỗi
            $sql_update = "UPDATE users SET 
                           password_hashed = '$password_hashed', 
                           PASSWORD = '$new_password' 
                           WHERE user_id = '$user_id'";
            
            if ($conn->query($sql_update)) {
                $_SESSION['success'] = "Đã đặt lại mật khẩu mới cho tài khoản: " . $user['username'];
                header('Location: index.php');
                exit();
            } else {
                $error = "Lỗi hệ thống: " . $conn->error;
            }
        }
    }
}

admin_layout_start("Đặt lại mật khẩu", 'users');
?>

<style>
    .khung-reset { background: #fff; padding: 30px; border-radius: 15px; border: 1px solid #eee; }
    .o-nhap { border-radius: 10px !important; padding: 12px; }
    .nut-bam { border-radius: 25px !important; padding: 10px 30px !important; font-weight: bold; }
    .thong-tin-nhanh { background: #f0f7ff; border-left: 5px solid #007bff; padding: 15px; border-radius: 8px; }
</style>

<div class="container">
    <div class="khung-reset shadow-sm mx-auto" style="max-width: 600px;">
        <div class="text-center mb-4">
            <h4 class="text-primary fw-bold mb-0"><i class="fas fa-key me-2"></i>ĐẶT LẠI MẬT KHẨU</h4>
            <p class="text-muted small">Cấp lại mật khẩu mới cho thành viên hệ thống</p>
        </div>

        <?php if ($error != '') { ?>
            <div class="alert alert-danger border-0 shadow-sm">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
            </div>
        <?php } ?>

        <div class="thong-tin-nhanh mb-4">
            <div class="row">
                <div class="col-6">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Tài khoản:</small>
                    <div class="fw-bold"><?php echo $user['username']; ?></div>
                </div>
                <div class="col-6 text-end">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Họ tên:</small>
                    <div class="fw-bold"><?php echo htmlspecialchars($user['fullname']); ?></div>
                </div>
            </div>
        </div>

        <form method="POST" action="">
            <div class="mb-4">
                <label class="fw-bold mb-2">Mật khẩu mới *</label>
                <input type="password" name="new_password" class="form-control o-nhap" 
                       placeholder="Nhập tối thiểu 6 ký tự" required>
            </div>

            <div class="mb-4">
                <label class="fw-bold mb-2">Xác nhận mật khẩu mới *</label>
                <input type="password" name="confirm_password" class="form-control o-nhap" 
                       placeholder="Nhập lại mật khẩu phía trên" required>
            </div>

            <div class="alert alert-warning border-0 small mb-4">
                <i class="fas fa-info-circle"></i> <b>Lưu ý:</b> Sau khi cập nhật, người dùng sẽ không thể dùng mật khẩu cũ để đăng nhập được nữa.
            </div>

            <div class="pt-3 border-top d-flex gap-2">
                <button type="submit" class="btn btn-primary nut-bam shadow w-100">
                    <i class="fas fa-check-circle me-2"></i> XÁC NHẬN ĐỔI MẬT KHẨU
                </button>
                <a href="detail.php?id=<?php echo $user_id; ?>" class="btn btn-light nut-bam border">HỦY</a>
            </div>
        </form>
    </div>
</div>

<?php admin_layout_end(); ?>
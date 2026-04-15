<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

// Khởi tạo các biến để giữ giá trị khi load lại trang
$username = '';
$email = '';
$fullname = '';
$password = '';
$role = 'Customer';
$status = 'Active';
$phone = '';
$birthdate = '';
$gender = 'other';
$points = 0;
$membership_level = 'regular';
$error = '';

// Xử lý khi nhấn nút Thêm người dùng (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $fullname = $_POST['fullname'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];
    $status = $_POST['status'];
    $phone = $_POST['phone'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $points = (int)$_POST['points'];
    $membership_level = $_POST['membership_level'];

    // 1. Kiểm tra mật khẩu khớp nhau (Thay cho JS)
    if ($password != $confirm_password) {
        $error = "Mật khẩu xác nhận không khớp, vui lòng nhập lại!";
    } else {
        if (strlen($password) < 6) {
            $error = "Mật khẩu phải có ít nhất 6 ký tự!";
        } else {
            // 2. Kiểm tra trùng Username (Dùng query trần)
            $sql_check_user = "SELECT user_id FROM users WHERE username = '$username'";
            $res_user = $conn->query($sql_check_user);
            
            if ($res_user->num_rows > 0) {
                $error = "Tên đăng nhập này đã có người sử dụng!";
            } else {
                // 3. Kiểm tra trùng Email
                $sql_check_email = "SELECT user_id FROM users WHERE email = '$email'";
                $res_email = $conn->query($sql_check_email);
                
                if ($res_email->num_rows > 0) {
                    $error = "Địa chỉ Email này đã được đăng ký rồi!";
                } else {
                    // 4. Hash mật khẩu và chèn dữ liệu
                    $password_hashed = md5($password);
                    
                    $sql_insert = "INSERT INTO users (username, password_hashed, PASSWORD, email, fullname, role, status, phone, birthdate, gender, points, membership_level, created_at) 
                                   VALUES ('$username', '$password_hashed', '$password', '$email', '$fullname', '$role', '$status', '$phone', '$birthdate', '$gender', '$points', '$membership_level', NOW())";
                    
                    if ($conn->query($sql_insert)) {
                        $_SESSION['success'] = "Đã thêm thành viên mới thành công!";
                        header('Location: index.php');
                        exit();
                    } else {
                        $error = "Lỗi khi thêm: " . $conn->error;
                    }
                }
            }
        }
    }
}

admin_layout_start("Thêm người dùng mới", 'users');
?>

<style>
    .khung-nhap { background: #fff; padding: 30px; border-radius: 15px; border: 1px solid #eee; }
    .o-nhap { border-radius: 10px !important; padding: 12px; }
    .nut-bam { border-radius: 25px !important; padding: 10px 30px !important; font-weight: bold; }
    .tieu-de-phu { color: #555; border-bottom: 2px solid #007bff; display: inline-block; margin-bottom: 20px; padding-bottom: 5px; }
</style>

<div class="container">
    <div class="khung-nhap shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-primary fw-bold mb-0">THÊM THÀNH VIÊN MỚI</h4>
            <a href="index.php" class="btn btn-outline-secondary rounded-pill px-3">Quay lại</a>
        </div>

        <?php if ($error != '') { ?>
            <div class="alert alert-danger border-0 shadow-sm">
                <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error; ?>
            </div>
        <?php } ?>

        <form method="POST" action="">
            <h6 class="tieu-de-phu">THÔNG TIN TÀI KHOẢN</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="fw-bold mb-2">Tên đăng nhập *</label>
                    <input type="text" name="username" class="form-control o-nhap" required value="<?php echo $username; ?>">
                </div>
                <div class="col-md-6">
                    <label class="fw-bold mb-2">Địa chỉ Email *</label>
                    <input type="email" name="email" class="form-control o-nhap" required value="<?php echo $email; ?>">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="fw-bold mb-2">Mật khẩu *</label>
                    <input type="password" name="password" class="form-control o-nhap" required>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold mb-2">Xác nhận mật khẩu *</label>
                    <input type="password" name="confirm_password" class="form-control o-nhap" required>
                </div>
            </div>

            <h6 class="tieu-de-phu">THÔNG TIN CÁ NHÂN</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="fw-bold mb-2">Họ và tên *</label>
                    <input type="text" name="fullname" class="form-control o-nhap" required value="<?php echo $fullname; ?>">
                </div>
                <div class="col-md-6">
                    <label class="fw-bold mb-2">Số điện thoại</label>
                    <input type="tel" name="phone" class="form-control o-nhap" value="<?php echo $phone; ?>">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="fw-bold mb-2">Ngày sinh</label>
                    <input type="date" name="birthdate" class="form-control o-nhap" value="<?php echo $birthdate; ?>">
                </div>
                <div class="col-md-4">
                    <label class="fw-bold mb-2">Giới tính</label>
                    <select name="gender" class="form-select o-nhap">
                        <option value="male" <?php if($gender == 'male') echo 'selected'; ?>>Nam</option>
                        <option value="female" <?php if($gender == 'female') echo 'selected'; ?>>Nữ</option>
                        <option value="other" <?php if($gender == 'other') echo 'selected'; ?>>Khác</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="fw-bold mb-2">Vai trò hệ thống</label>
                    <select name="role" class="form-select o-nhap">
                        <option value="Customer" <?php if($role == 'Customer') echo 'selected'; ?>>Khách hàng</option>
                        <option value="Manager" <?php if($role == 'Manager') echo 'selected'; ?>>Quản lý</option>
                        <option value="Admin" <?php if($role == 'Admin') echo 'selected'; ?>>Quản trị viên</option>
                    </select>
                </div>
            </div>

            <h6 class="tieu-de-phu">ƯU ĐÃI THÀNH VIÊN</h6>
            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="fw-bold mb-2">Trạng thái</label>
                    <select name="status" class="form-select o-nhap">
                        <option value="Active" <?php if($status == 'Active') echo 'selected'; ?>>Đang hoạt động</option>
                        <option value="Inactive" <?php if($status == 'Inactive') echo 'selected'; ?>>Bị khóa</option>
                        <option value="Pending" <?php if($status == 'Pending') echo 'selected'; ?>>Chờ duyệt</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="fw-bold mb-2">Hạng thành viên</label>
                    <select name="membership_level" class="form-select o-nhap">
                        <option value="regular" <?php if($membership_level == 'regular') echo 'selected'; ?>>Thường (Regular)</option>
                        <option value="gold" <?php if($membership_level == 'gold') echo 'selected'; ?>>Vàng (Gold)</option>
                        <option value="vip" <?php if($membership_level == 'vip') echo 'selected'; ?>>VIP</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="fw-bold mb-2">Điểm thưởng</label>
                    <input type="number" name="points" class="form-control o-nhap" value="<?php echo $points; ?>">
                </div>
            </div>

            <div class="pt-4 border-top">
                <button type="submit" class="btn btn-primary nut-bam shadow">
                    <i class="fas fa-save me-2"></i> THÊM NGƯỜI DÙNG NGAY
                </button>
                <button type="reset" class="btn btn-light nut-bam border ms-2">NHẬP LẠI</button>
            </div>
        </form>
    </div>
</div>

<?php admin_layout_end(); ?>
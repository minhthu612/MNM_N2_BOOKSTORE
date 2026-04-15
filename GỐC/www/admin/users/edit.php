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

// 2. Lấy thông tin người dùng hiện tại bằng query trần
$sql_get = "SELECT * FROM users WHERE user_id = '$user_id'";
$res_get = $conn->query($sql_get);
$user = $res_get->fetch_assoc();

if ($user == null) {
    $_SESSION['error'] = "Người dùng không tồn tại.";
    header('Location: index.php');
    exit();
}

$error = '';

// 3. Xử lý khi nhấn nút Cập nhật (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $fullname = $_POST['fullname'];
    $role = $_POST['role'];
    $status = $_POST['status'];
    $phone = $_POST['phone'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $points = (int)$_POST['points'];
    $membership_level = $_POST['membership_level'];
    
    // Kiểm tra trùng username (trừ chính mình) bằng query trần
    $sql_check_user = "SELECT user_id FROM users WHERE username = '$username' AND user_id != '$user_id'";
    $res_check_user = $conn->query($sql_check_user);
    
    if ($res_check_user->num_rows > 0) {
        $error = "Tên đăng nhập này đã được người khác sử dụng!";
    } else {
        // Kiểm tra trùng email (trừ chính mình)
        $sql_check_email = "SELECT user_id FROM users WHERE email = '$email' AND user_id != '$user_id'";
        $res_check_email = $conn->query($sql_check_email);
        
        if ($res_check_email->num_rows > 0) {
            $error = "Địa chỉ email này đã được đăng ký bởi tài khoản khác!";
        } else {
            // Thực hiện cập nhật dữ liệu (Nối chuỗi trực tiếp)
            $sql_update = "UPDATE users SET 
                           username = '$username', 
                           email = '$email', 
                           fullname = '$fullname', 
                           role = '$role', 
                           status = '$status', 
                           phone = '$phone', 
                           birthdate = '$birthdate', 
                           gender = '$gender', 
                           points = '$points', 
                           membership_level = '$membership_level'
                           WHERE user_id = '$user_id'";
            
            if ($conn->query($sql_update)) {
                $_SESSION['success'] = "Đã cập nhật thông tin thành viên thành công!";
                header('Location: index.php');
                exit();
            } else {
                $error = "Lỗi khi cập nhật dữ liệu: " . $conn->error;
            }
        }
    }
}

admin_layout_start("Sửa thông tin: " . $user['username'], 'users');
?>

<style>
    .khung-sua { background-color: #ffffff; padding: 30px; border-radius: 15px; border: 1px solid #e0e0e0; }
    .o-nhap { border-radius: 10px !important; padding: 12px; }
    .nut-bam { border-radius: 25px !important; padding: 10px 30px !important; font-weight: bold; }
    .tieu-de-nho { color: #555; border-bottom: 2px solid #007bff; display: inline-block; margin-bottom: 20px; padding-bottom: 5px; text-uppercase; font-size: 0.8rem; letter-spacing: 1px; }
</style>

<div class="container">
    <div class="khung-sua shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-primary fw-bold mb-0">CHỈNH SỬA THÔNG TIN THÀNH VIÊN</h4>
            <span class="badge rounded-pill bg-dark px-3 py-2">Mã số: #<?php echo $user['user_id']; ?></span>
        </div>

        <?php if ($error != '') { ?>
            <div class="alert alert-danger border-0 shadow-sm">
                <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error; ?>
            </div>
        <?php } ?>

        <form method="POST" action="">
            <div class="row">
                <div class="col-md-8">
                    <h6 class="tieu-de-nho">THÔNG TIN TÀI KHOẢN</h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold mb-2">Tên đăng nhập *</label>
                            <input type="text" name="username" class="form-control o-nhap" 
                                   value="<?php echo $user['username']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold mb-2">Email liên hệ *</label>
                            <input type="email" name="email" class="form-control o-nhap" 
                                   value="<?php echo $user['email']; ?>" required>
                        </div>
                    </div>

                    <h6 class="tieu-de-nho">THÔNG TIN CÁ NHÂN</h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold mb-2">Họ và tên *</label>
                            <input type="text" name="fullname" class="form-control o-nhap" 
                                   value="<?php echo $user['fullname']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold mb-2">Số điện thoại</label>
                            <input type="tel" name="phone" class="form-control o-nhap" 
                                   value="<?php echo $user['phone']; ?>">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold mb-2">Ngày sinh</label>
                            <input type="date" name="birthdate" class="form-control o-nhap" 
                                   value="<?php echo $user['birthdate']; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold mb-2">Giới tính</label>
                            <select name="gender" class="form-select o-nhap">
                                <option value="male" <?php if($user['gender'] == 'male') echo 'selected'; ?>>Nam</option>
                                <option value="female" <?php if($user['gender'] == 'female') echo 'selected'; ?>>Nữ</option>
                                <option value="other" <?php if($user['gender'] == 'other') echo 'selected'; ?>>Khác</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 bg-light rounded-3 p-3 mb-4">
                        <h6 class="fw-bold mb-3">THIẾT LẬP HỆ THỐNG</h6>
                        
                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Vai trò</label>
                            <select name="role" class="form-select o-nhap">
                                <option value="Customer" <?php if($user['role'] == 'Customer') echo 'selected'; ?>>Khách hàng</option>
                                <option value="Manager" <?php if($user['role'] == 'Manager') echo 'selected'; ?>>Quản lý</option>
                                <option value="Admin" <?php if($user['role'] == 'Admin') echo 'selected'; ?>>Quản trị viên</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Trạng thái</label>
                            <select name="status" class="form-select o-nhap">
                                <option value="Active" <?php if($user['status'] == 'Active') echo 'selected'; ?>>Đang hoạt động</option>
                                <option value="Inactive" <?php if($user['status'] == 'Inactive') echo 'selected'; ?>>Đang bị khóa</option>
                                <option value="Pending" <?php if($user['status'] == 'Pending') echo 'selected'; ?>>Chờ phê duyệt</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Hạng thành viên</label>
                            <select name="membership_level" class="form-select o-nhap">
                                <option value="regular" <?php if($user['membership_level'] == 'regular') echo 'selected'; ?>>Thường</option>
                                <option value="gold" <?php if($user['membership_level'] == 'gold') echo 'selected'; ?>>Vàng</option>
                                <option value="vip" <?php if($user['membership_level'] == 'vip') echo 'selected'; ?>>VIP</option>
                            </select>
                        </div>

                        <div class="mb-0">
                            <label class="small fw-bold mb-1">Điểm tích lũy</label>
                            <input type="number" name="points" class="form-control o-nhap" 
                                   value="<?php echo $user['points']; ?>">
                        </div>
                    </div>

                    <div class="alert alert-info border-0 small">
                        <i class="fas fa-info-circle"></i> Tài khoản này được tạo vào: <br>
                        <b><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></b>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary nut-bam shadow">
                        <i class="fas fa-save me-2"></i> LƯU THAY ĐỔI
                    </button>
                    <a href="index.php" class="btn btn-outline-secondary nut-bam">HỦY BỎ</a>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="reset_password.php?id=<?php echo $user_id; ?>" class="btn btn-info text-white nut-bam">
                        <i class="fas fa-key"></i> ĐỔI MẬT KHẨU
                    </a>
                    <a href="delete.php?id=<?php echo $user_id; ?>" class="btn btn-danger nut-bam">
                        <i class="fas fa-trash-alt"></i> XÓA
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php admin_layout_end(); ?>
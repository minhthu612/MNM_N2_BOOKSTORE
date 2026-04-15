<?php
require_once '../includes/client_check.php';
require_once '../config.php';

$user_id = $_SESSION['user_id'];

// 1. LẤY THÔNG TIN USER (Dùng query trần)
$sql_select = "SELECT * FROM users WHERE user_id = '$user_id'";
$result_user = $conn->query($sql_select);
$user = $result_user->fetch_assoc();

// Xác định tab đang mở (Mặc định là info) để không cần dùng JavaScript
$tab_hien_tai = 'info';
if (isset($_GET['tab'])) {
    $tab_hien_tai = $_GET['tab'];
}

// 2. XỬ LÝ CẬP NHẬT THÔNG TIN
if (isset($_POST['update_profile'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    // Kiểm tra email trùng kiểu thủ công
    $sql_check_mail = "SELECT user_id FROM users WHERE email = '$email' AND user_id != '$user_id'";
    $res_mail = $conn->query($sql_check_mail);
    
    if ($res_mail->num_rows > 0) {
        $_SESSION['error'] = "Email này đã có người khác sử dụng rồi!";
    } else {
        $sql_update = "UPDATE users SET 
                      fullname = '$fullname', 
                      email = '$email', 
                      phone = '$phone'
                      WHERE user_id = '$user_id'";
        
        if ($conn->query($sql_update)) {
            // Cập nhật lại Session cho đồng bộ
            $_SESSION['fullname'] = $fullname;
            $_SESSION['success'] = "Đã lưu thay đổi thông tin cá nhân!";
            header('Location: profile.php?tab=info');
            exit();
        } else {
            $_SESSION['error'] = "Lỗi cập nhật: " . $conn->error;
        }
    }
}

// 3. XỬ LÝ ĐỔI MẬT KHẨU
if (isset($_POST['change_password'])) {
    $pass_cu = $_POST['current_password'];
    $pass_moi = $_POST['new_password'];
    $pass_nhap_lai = $_POST['confirm_password'];
    
    // Kiểm tra logic mật khẩu kiểu rành mạch
    if (password_verify($pass_cu, $user['password']) == false) {
        $_SESSION['error'] = "Mật khẩu hiện tại bạn nhập không đúng";
    } else {
        if ($pass_moi != $pass_nhap_lai) {
            $_SESSION['error'] = "Hai lần nhập mật khẩu mới không khớp nhau";
        } else {
            if (strlen($pass_moi) < 6) {
                $_SESSION['error'] = "Mật khẩu mới phải từ 6 ký tự trở lên";
            } else {
                $mk_ma_hoa = password_hash($pass_moi, PASSWORD_DEFAULT);
                $sql_pass = "UPDATE users SET password = '$mk_ma_hoa' WHERE user_id = '$user_id'";
                
                if ($conn->query($sql_pass)) {
                    $_SESSION['success'] = "Chúc mừng! Bạn đã đổi mật khẩu thành công";
                    header('Location: profile.php?tab=password');
                    exit();
                }
            }
        }
    }
}

$page_title = "Hồ sơ của tôi";
include '../header.php';
?>

<style>
    /* CSS Gộp trực tiếp - Giữ nguyên giao diện đẹp nhưng bỏ các hiệu ứng phức tạp */
    body { background-color: #f8f9fa; }
    .khung-profile {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-top: 30px;
    }
    .header-mau {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px 20px;
        text-align: center;
    }
    .anh-dai-dien {
        width: 100px; height: 100px;
        background: white;
        border-radius: 50%;
        margin: 0 auto 15px;
        display: flex; align-items: center; justify-content: center;
        font-size: 40px; color: #667eea;
        border: 4px solid rgba(255,255,255,0.3);
    }
    .vung-noi-dung { padding: 30px; }
    
    /* Style cho Tab kiểu thủ công (không dùng JS) */
    .menu-tab { display: flex; border-bottom: 2px solid #eee; margin-bottom: 25px; }
    .item-tab { 
        padding: 10px 20px; 
        text-decoration: none; 
        color: #666; 
        font-weight: bold;
        border-bottom: 3px solid transparent;
    }
    .item-tab.active { 
        color: #667eea; 
        border-bottom-color: #667eea; 
    }
    
    .o-nhap {
        border-radius: 8px !important;
        padding: 12px;
        border: 1px solid #ddd;
    }
    .nut-bam {
        border-radius: 8px !important;
        padding: 10px 25px;
        font-weight: bold;
    }
</style>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="khung-profile shadow">
                <div class="header-mau">
                    <div class="anh-dai-dien">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3 class="m-0"><?php echo $user['fullname']; ?></h3>
                    <p class="small opacity-75 m-0">Khách hàng thành viên</p>
                </div>

                <div class="vung-noi-dung">
                    <?php if (isset($_SESSION['success'])) { ?>
                        <div class="alert alert-success border-0 shadow-sm mb-4">
                            <i class="fas fa-check-circle me-2"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        </div>
                    <?php } ?>

                    <?php if (isset($_SESSION['error'])) { ?>
                        <div class="alert alert-danger border-0 shadow-sm mb-4 text-white" style="background: #f44336;">
                            <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php } ?>

                    <div class="menu-tab">
                        <a href="profile.php?tab=info" class="item-tab <?php if($tab_hien_tai == 'info') { echo 'active'; } ?>">
                            <i class="fas fa-id-card me-2"></i>Thông tin cá nhân
                        </a>
                        <a href="profile.php?tab=password" class="item-tab <?php if($tab_hien_tai == 'password') { echo 'active'; } ?>">
                            <i class="fas fa-shield-alt me-2"></i>Bảo mật & Mật khẩu
                        </a>
                    </div>

                    <?php if ($tab_hien_tai == 'info') { ?>
                        <form method="POST" action="profile.php?tab=info">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Họ và tên của bạn</label>
                                    <input type="text" name="fullname" class="form-control o-nhap" value="<?php echo $user['fullname']; ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Địa chỉ Email</label>
                                    <input type="email" name="email" class="form-control o-nhap" value="<?php echo $user['email']; ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Số điện thoại</label>
                                    <input type="text" name="phone" class="form-control o-nhap" value="<?php echo $user['phone']; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Ngày gia nhập hệ thống</label>
                                    <input type="text" class="form-control o-nhap bg-light" value="<?php echo date('d/m/Y', strtotime($user['created_at'])); ?>" readonly>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                                <button type="submit" name="update_profile" class="btn btn-primary nut-bam px-4 shadow">
                                    LƯU THÔNG TIN
                                </button>
                                <a href="index.php" class="btn btn-outline-secondary nut-bam">Quay lại</a>
                            </div>
                        </form>
                    <?php } ?>

                    <?php if ($tab_hien_tai == 'password') { ?>
                        <form method="POST" action="profile.php?tab=password">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Mật khẩu hiện tại</label>
                                <input type="password" name="current_password" class="form-control o-nhap" placeholder="Nhập mật khẩu đang dùng" required>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Mật khẩu mới</label>
                                    <input type="password" name="new_password" class="form-control o-nhap" placeholder="Tối thiểu 6 ký tự" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Xác nhận lại mật khẩu</label>
                                    <input type="password" name="confirm_password" class="form-control o-nhap" placeholder="Nhập lại mật khẩu mới" required>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                                <button type="submit" name="change_password" class="btn btn-dark nut-bam px-4 shadow">
                                    ĐỔI MẬT KHẨU NGAY
                                </button>
                                <a href="index.php" class="btn btn-outline-secondary nut-bam">Quay lại</a>
                            </div>
                        </form>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>
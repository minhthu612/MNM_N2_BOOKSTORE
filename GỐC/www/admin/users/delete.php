<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

// 1. Lấy ID người dùng từ URL theo kiểu truyền thống
$user_id = 0;
if (isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
}

if ($user_id == 0) {
    $_SESSION['error'] = "Không tìm thấy người dùng cần xóa.";
    header('Location: index.php');
    exit();
}

// 2. Kiểm tra nếu người dùng đang xóa chính mình
if ($user_id == $_SESSION['user_id']) {
    $_SESSION['error'] = "Bạn không thể tự xóa tài khoản của chính mình khi đang đăng nhập!";
    header('Location: index.php');
    exit();
}

// 3. Lấy thông tin người dùng bằng query trần
$sql_user = "SELECT username, role FROM users WHERE user_id = '$user_id'";
$res_user = $conn->query($sql_user);
$user = $res_user->fetch_assoc();

if ($user == null) {
    $_SESSION['error'] = "Người dùng này không tồn tại trên hệ thống.";
    header('Location: index.php');
    exit();
}

// 4. Kiểm tra số đơn hàng liên quan (để cảnh báo)
$sql_check_orders = "SELECT COUNT(*) as total FROM orders WHERE user_id = '$user_id'";
$res_orders = $conn->query($sql_check_orders);
$order_count = 0;
if ($res_orders) {
    $row_orders = $res_orders->fetch_assoc();
    $order_count = $row_orders['total'];
}

$error = '';

// 5. Xử lý khi nhấn nút Xác nhận xóa (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // SV kiểm tra logic DELETE bằng PHP thay vì JS
    $confirm_txt = '';
    if (isset($_POST['confirm_text'])) {
        $confirm_txt = $_POST['confirm_text'];
    }
    
    if ($confirm_txt == 'DELETE') {
        // Thực hiện lệnh xóa trần (Lưu ý: Có thể lỗi FK nếu DB có ràng buộc, nhưng đây là code SV)
        $sql_delete = "DELETE FROM users WHERE user_id = '$user_id'";
        
        if ($conn->query($sql_delete)) {
            $_SESSION['success'] = "Đã xóa thành công tài khoản: " . $user['username'];
            header('Location: index.php');
            exit();
        } else {
            $error = "Lỗi hệ thống: " . $conn->error;
        }
    } else {
        $error = "Vui lòng nhập đúng chữ 'DELETE' để xác nhận hành động này.";
    }
}

admin_layout_start("Xác nhận xóa thành viên", 'users');
?>

<style>
    .khung-xoa { background: #fff; padding: 30px; border-radius: 15px; border: 1px solid #eee; }
    .o-nhap { border-radius: 10px !important; padding: 12px; }
    .nut-bam { border-radius: 25px !important; padding: 10px 30px !important; font-weight: bold; }
    .vung-canh-bao { background: #fff5f5; border-left: 5px solid #dc3545; padding: 15px; border-radius: 8px; }
</style>

<div class="container">
    <div class="khung-xoa shadow-sm mx-auto" style="max-width: 650px;">
        <div class="text-center mb-4">
            <h3 class="text-danger fw-bold"><i class="fas fa-user-times"></i> XÓA THÀNH VIÊN</h3>
            <p class="text-muted">Lưu ý: Hành động xóa sẽ gỡ bỏ hoàn toàn tài khoản khỏi hệ thống.</p>
        </div>

        <?php if ($error != '') { ?>
            <div class="alert alert-danger border-0 shadow-sm">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
            </div>
        <?php } ?>

        <div class="vung-canh-bao mb-4">
            <h6 class="text-danger fw-bold mb-3 text-uppercase">Thông tin tài khoản:</h6>
            <div class="row mb-2">
                <div class="col-5 text-muted small fw-bold">TÊN ĐĂNG NHẬP:</div>
                <div class="col-7 fw-bold"><?php echo $user['username']; ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-5 text-muted small fw-bold">VAI TRÒ:</div>
                <div class="col-7">
                    <span class="badge bg-secondary"><?php echo $user['role']; ?></span>
                </div>
            </div>
            
            <?php if ($order_count > 0) { ?>
                <div class="alert alert-warning py-2 mt-3 mb-0 small">
                    <i class="fas fa-shopping-cart"></i> Người dùng này đang có <b><?php echo $order_count; ?></b> đơn hàng trong lịch sử.
                </div>
            <?php } ?>
        </div>

        <form method="POST" action="">
            <div class="mb-4">
                <label class="fw-bold mb-2">Nhập chữ <span class="text-danger">DELETE</span> để xác nhận xóa:</label>
                <input type="text" name="confirm_text" class="form-control o-nhap text-center fs-5 fw-bold" 
                       placeholder="Gõ chính xác chữ DELETE" required autocomplete="off">
            </div>

            <div class="d-flex justify-content-center gap-2 pt-3 border-top">
                <button type="submit" class="btn btn-danger nut-bam shadow">
                    <i class="fas fa-trash-alt me-2"></i> XÓA NGƯỜI DÙNG
                </button>
                <a href="index.php" class="btn btn-light nut-bam border">
                    QUAY LẠI
                </a>
            </div>
        </form>
    </div>
</div>

<?php admin_layout_end(); ?>
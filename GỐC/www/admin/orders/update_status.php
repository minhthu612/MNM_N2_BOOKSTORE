<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

// 1. Lấy ID đơn hàng và hành động từ URL kiểu truyền thống
$order_id = 0;
if (isset($_GET['id'])) {
    $order_id = (int)$_GET['id'];
}

$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

if ($order_id <= 0) {
    $_SESSION['error'] = 'Không tìm thấy đơn hàng';
    header('Location: index.php');
    exit;
}

// 2. Lấy thông tin đơn hàng bằng query trần
$sql_get = "SELECT o.*, u.username, u.fullname 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.user_id 
            WHERE o.order_id = '$order_id'";
$res_order = $conn->query($sql_get);
$order = $res_order->fetch_assoc();

if ($order == null) {
    $_SESSION['error'] = 'Đơn hàng không tồn tại';
    header('Location: index.php');
    exit;
}

$current_status = strtolower($order['status']);

// 3. XỬ LÝ ACTION TỪ CÁC NÚT BẤM NHANH (GET)
// Thay vì JS confirm, ta xử lý trực tiếp nếu có thêm tham số confirmed
if ($action != '') {
    if (isset($_GET['confirmed'])) {
        $new_status = '';
        $sql_add = "";
        
        if ($action == 'ship') {
            $new_status = 'shipped';
        } else {
            if ($action == 'deliver') {
                $new_status = 'delivered';
                $sql_add = ", delivered_at = NOW() ";
            } else {
                if ($action == 'cancel') {
                    $new_status = 'cancelled';
                }
            }
        }

        if ($new_status != '') {
            $sql_update_fast = "UPDATE orders SET status = '$new_status' $sql_add WHERE order_id = '$order_id'";
            $conn->query($sql_update_fast);
            $_SESSION['success'] = 'Cập nhật trạng thái thành công';
            header("Location: detail.php?id=$order_id");
            exit;
        }
    }
}

// 4. XỬ LÝ FORM CẬP NHẬT CHI TIẾT (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];
    $track = $_POST['tracking_number'];
    $notes = $_POST['notes'];

    $sql_update_full = "UPDATE orders SET status = '$status' ";

    if ($status == 'shipped') {
        $sql_update_full = $sql_update_full . ", tracking_number = '$track' ";
    }

    if ($status == 'delivered') {
        $sql_update_full = $sql_update_full . ", delivered_at = NOW() ";
    }

    if ($notes != '') {
        $time_now = date('d/m/Y H:i');
        $new_note = $order['notes'] . "\n[" . $time_now . " ADMIN]: " . $notes;
        $sql_update_full = $sql_update_full . ", notes = '$new_note' ";
    }

    $sql_update_full = $sql_update_full . " WHERE order_id = '$order_id'";
    
    if ($conn->query($sql_update_full)) {
        $_SESSION['success'] = 'Đã cập nhật đơn hàng thành công';
        header("Location: detail.php?id=$order_id");
        exit;
    }
}

admin_layout_start('Cập nhật đơn hàng #' . $order_id, 'orders');
?>

<style>
    .khung-cap-nhat { background: #fff; padding: 30px; border-radius: 15px; border: 1px solid #eee; }
    .o-nhap { border-radius: 10px !important; padding: 12px; }
    .nut-bam { border-radius: 25px !important; padding: 10px 30px !important; font-weight: bold; }
    .thong-tin-don { background: #f8f9fa; border-left: 5px solid #007bff; padding: 15px; border-radius: 8px; }
</style>

<div class="container">
    <?php 
    // GIAO DIỆN XÁC NHẬN THAY CHO JS CONFIRM
    if ($action != '' && !isset($_GET['confirmed'])) { 
        $msg = 'Bạn có chắc chắn muốn thực hiện thao tác này?';
        if ($action == 'cancel') $msg = 'Cảnh báo: Bạn đang yêu cầu HỦY đơn hàng này?';
    ?>
        <div class="card border-danger shadow mb-4">
            <div class="card-body text-center py-4">
                <h4 class="text-danger fw-bold mb-3"><i class="fas fa-question-circle"></i> XÁC NHẬN</h4>
                <p class="fs-5"><?php echo $msg; ?></p>
                <div class="d-flex justify-content-center gap-3 mt-4">
                    <a href="update_status.php?id=<?php echo $order_id; ?>&action=<?php echo $action; ?>&confirmed=1" class="btn btn-danger nut-bam">ĐỒNG Ý</a>
                    <a href="detail.php?id=<?php echo $order_id; ?>" class="btn btn-light nut-bam border">HỦY BỎ</a>
                </div>
            </div>
        </div>
    <?php } else { ?>

    <div class="khung-cap-nhat shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-primary fw-bold mb-0">CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG</h4>
            <a href="detail.php?id=<?php echo $order_id; ?>" class="btn btn-outline-secondary rounded-pill px-3">Quay lại chi tiết</a>
        </div>

        <div class="row">
            <div class="col-md-7">
                <div class="thong-tin-don mb-4">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Mã đơn hàng:</small>
                            <div class="fw-bold fs-5">#<?php echo $order_id; ?></div>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-muted">Khách hàng:</small>
                            <div class="fw-bold"><?php echo htmlspecialchars($order['fullname']); ?></div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="">
                    <div class="mb-4">
                        <label class="fw-bold mb-2">Trạng thái xử lý</label>
                        <select name="status" class="form-select o-nhap">
                            <option value="pending" <?php if($current_status == 'pending') echo 'selected'; ?>>Chờ xử lý</option>
                            <option value="processing" <?php if($current_status == 'processing') echo 'selected'; ?>>Đang đóng gói</option>
                            <option value="shipped" <?php if($current_status == 'shipped') echo 'selected'; ?>>Đang giao hàng</option>
                            <option value="delivered" <?php if($current_status == 'delivered') echo 'selected'; ?>>Đã giao thành công</option>
                            <option value="cancelled" <?php if($current_status == 'cancelled') echo 'selected'; ?>>Đã hủy đơn</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2">Mã vận đơn (Nếu có)</label>
                        <input type="text" name="tracking_number" class="form-control o-nhap" 
                               value="<?php echo htmlspecialchars($order['tracking_number']); ?>" placeholder="Nhập mã từ đơn vị vận chuyển...">
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2">Ghi chú nội bộ</label>
                        <textarea name="notes" class="form-control o-nhap" rows="3" placeholder="Nhập lý do thay đổi hoặc lời nhắn cho khách..."></textarea>
                    </div>

                    <div class="pt-3 border-top">
                        <button type="submit" class="btn btn-primary nut-bam shadow w-100">
                            <i class="fas fa-save me-2"></i> LƯU THAY ĐỔI
                        </button>
                    </div>
                </form>
            </div>

            <div class="col-md-5">
                <div class="card border-0 bg-light rounded-3">
                    <div class="card-body">
                        <h6 class="fw-bold text-dark"><i class="fas fa-history me-2"></i>LỊCH SỬ GHI CHÚ</h6>
                        <hr>
                        <div class="small text-muted" style="white-space: pre-line;">
                            <?php 
                            if ($order['notes'] != '') {
                                echo htmlspecialchars($order['notes']);
                            } else {
                                echo "Chưa có ghi chú nào.";
                            }
                            ?>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info border-0 mt-4 small">
                    <i class="fas fa-info-circle"></i> <b>Lưu ý:</b> Khi chuyển sang trạng thái <b>Đã giao</b>, hệ thống sẽ tự động ghi nhận thời gian hoàn tất đơn hàng.
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<?php admin_layout_end(); ?>
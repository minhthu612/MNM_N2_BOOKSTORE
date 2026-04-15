<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

// 1. Lấy ID người dùng từ URL kiểu truyền thống
$user_id = 0;
if (isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
}

if ($user_id == 0) {
    $_SESSION['error'] = "Không tìm thấy mã người dùng.";
    header('Location: index.php');
    exit();
}

// 2. Lấy thông tin người dùng bằng query trần
$sql_user = "SELECT * FROM users WHERE user_id = '$user_id'";
$res_user = $conn->query($sql_user);
$user = $res_user->fetch_assoc();

if ($user == null) {
    $_SESSION['error'] = "Người dùng không tồn tại trên hệ thống.";
    header('Location: index.php');
    exit();
}

// 3. Lấy thống kê đơn hàng (Query trần)
$sql_orders = "SELECT 
        COUNT(*) as total_orders,
        SUM(total_amount) as total_spent,
        MAX(created_at) as last_order
        FROM orders 
        WHERE user_id = '$user_id'";
$res_order_stats = $conn->query($sql_orders);
$order_stats = $res_order_stats->fetch_assoc();

// 4. Lấy thống kê đánh giá (Query trần)
$sql_reviews = "SELECT 
        COUNT(*) as total_reviews,
        AVG(rating) as avg_rating
        FROM reviews 
        WHERE user_id = '$user_id'";
$res_review_stats = $conn->query($sql_reviews);
$review_stats = $res_review_stats->fetch_assoc();

// 5. Lấy danh sách hoạt động (Đơn hàng và Đánh giá gần nhất) để dùng foreach
$activities = array();

// Đơn hàng gần đây
$sql_recent_orders = "SELECT order_id as id, total_amount as info, status, created_at, 'order' as type 
                      FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC LIMIT 5";
$res_recent_orders = $conn->query($sql_recent_orders);
if($res_recent_orders) {
    while($row = $res_recent_orders->fetch_assoc()) {
        $activities[] = $row;
    }
}

// Đánh giá gần đây
$sql_recent_reviews = "SELECT r.review_id as id, b.title as info, r.rating as status, r.created_at, 'review' as type 
                       FROM reviews r JOIN books b ON r.book_id = b.book_id 
                       WHERE r.user_id = '$user_id' ORDER BY r.created_at DESC LIMIT 5";
$res_recent_reviews = $conn->query($sql_recent_reviews);
if($res_recent_reviews) {
    while($row = $res_recent_reviews->fetch_assoc()) {
        $activities[] = $row;
    }
}

admin_layout_start("Chi tiết: " . $user['username'], 'users');
?>

<style>
    .khung-trang { background: #fff; border-radius: 15px; border: 1px solid #eee; padding: 25px; }
    .avatar-tron { 
        width: 100px; height: 100px; background: #e9ecef; 
        border-radius: 50%; display: flex; align-items: center; 
        justify-content: center; font-size: 40px; font-weight: bold; color: #007bff;
        margin: 0 auto 15px; border: 4px solid #fff; shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .the-thong-ke { border-radius: 12px; border: none; padding: 20px; color: #fff; height: 100%; }
    .nut-bam { border-radius: 20px !important; padding: 8px 20px; font-weight: bold; }
    .dong-hoat-dong { border-left: 3px solid #dee2e6; padding-left: 20px; position: relative; margin-bottom: 20px; }
    .dong-hoat-dong::before { 
        content: ''; position: absolute; left: -9px; top: 0; 
        width: 15px; height: 15px; border-radius: 50%; background: #007bff; 
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0">HỒ SƠ NGƯỜI DÙNG</h4>
        <a href="index.php" class="btn btn-outline-secondary nut-bam">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="khung-trang text-center mb-4 shadow-sm">
                <div class="avatar-tron shadow-sm">
                    <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                </div>
                <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($user['fullname']); ?></h4>
                <p class="text-muted small">@<?php echo $user['username']; ?></p>
                
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-primary rounded-pill px-3"><?php echo $user['role']; ?></span>
                    <?php if ($user['status'] == 'Active') { ?>
                        <span class="badge bg-success rounded-pill px-3">Hoạt động</span>
                    <?php } else { ?>
                        <span class="badge bg-danger rounded-pill px-3">Bị khóa</span>
                    <?php } ?>
                </div>

                <hr>

                <div class="text-start">
                    <div class="mb-3">
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Email liên hệ:</small>
                        <span class="text-primary"><?php echo $user['email']; ?></span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Số điện thoại:</small>
                        <span><?php echo ($user['phone'] != '' ? $user['phone'] : 'Chưa cập nhật'); ?></span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Ngày sinh / Giới tính:</small>
                        <span>
                            <?php echo ($user['birthdate'] != '' ? date('d/m/Y', strtotime($user['birthdate'])) : 'N/A'); ?> 
                            - <?php echo ($user['gender'] == 'male' ? 'Nam' : ($user['gender'] == 'female' ? 'Nữ' : 'Khác')); ?>
                        </span>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <a href="edit.php?id=<?php echo $user_id; ?>" class="btn btn-warning nut-bam">Sửa thông tin</a>
                    <a href="delete.php?id=<?php echo $user_id; ?>" class="btn btn-outline-danger nut-bam">Xóa tài khoản</a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="row g-3 mb-4 text-center">
                <div class="col-md-4">
                    <div class="the-thong-ke bg-primary shadow-sm">
                        <h3 class="fw-bold mb-0"><?php echo $order_stats['total_orders'] ?? 0; ?></h3>
                        <small class="text-uppercase fw-bold" style="font-size: 0.7rem;">Đơn hàng</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="the-thong-ke bg-success shadow-sm">
                        <h3 class="fw-bold mb-0"><?php echo number_format($order_stats['total_spent'] ?? 0); ?>đ</h3>
                        <small class="text-uppercase fw-bold" style="font-size: 0.7rem;">Tổng chi tiêu</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="the-thong-ke bg-info shadow-sm">
                        <h3 class="fw-bold mb-0"><?php echo number_format($review_stats['avg_rating'] ?? 0, 1); ?>★</h3>
                        <small class="text-uppercase fw-bold" style="font-size: 0.7rem;">Đánh giá TB</small>
                    </div>
                </div>
            </div>

            <div class="khung-trang shadow-sm">
                <h5 class="fw-bold mb-4"><i class="fas fa-history me-2 text-primary"></i>HOẠT ĐỘNG GẦN ĐÂY</h5>
                
                <?php if (count($activities) > 0) { ?>
                    <div class="ms-2">
                        <?php foreach ($activities as $act) { ?>
                            <div class="dong-hoat-dong">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <?php if ($act['type'] == 'order') { ?>
                                            <span class="fw-bold text-dark">Đặt đơn hàng #<?php echo $act['id']; ?></span>
                                            <div class="small text-muted">Giá trị: <?php echo number_format($act['info']); ?>đ - Trạng thái: <?php echo $act['status']; ?></div>
                                        <?php } else { ?>
                                            <span class="fw-bold text-dark">Đánh giá sách: <?php echo $act['info']; ?></span>
                                            <div class="small text-warning">
                                                <?php for($i=1; $i<=5; $i++) { echo ($i <= $act['status'] ? '★' : '☆'); } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($act['created_at'])); ?></small>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-folder-open fa-3x mb-3"></i>
                        <p>Người dùng này chưa có hoạt động nào.</p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php admin_layout_end(); ?>
<?php
require_once '../../includes/client_check.php';
require_once '../../config.php';

$page_title = "Đặt hàng thành công";
include '../../header.php';

// Lấy mã đơn hàng từ URL kiểu truyền thống
$order_id = 0;
if (isset($_GET['id'])) {
    $order_id = (int)$_GET['id'];
}
?>

<style>
    /* CSS thuần túy viết trực tiếp trong file */
    .khung-thanh-cong {
        background: #ffffff;
        border-radius: 20px;
        padding: 50px;
        border: 1px solid #eee;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
    .bieu-tuong-check {
        font-size: 80px;
        color: #28a745;
        margin-bottom: 20px;
    }
    .vung-thong-tin {
        background-color: #f8f9fa;
        border-radius: 12px;
        padding: 25px;
        margin-top: 30px;
        margin-bottom: 30px;
        text-align: left;
    }
    .nut-hanh-dong {
        border-radius: 30px !important;
        padding: 12px 30px !important;
        font-weight: bold;
        transition: 0.3s;
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="khung-thanh-cong text-center">
                <div class="bieu-tuong-check">
                    <i class="fas fa-check-circle"></i>
                </div>
                
                <h2 class="fw-bold text-dark mb-2">ĐẶT HÀNG THÀNH CÔNG!</h2>
                <p class="text-muted">Cảm ơn bạn đã lựa chọn mua sắm tại cửa hàng chúng tôi.</p>
                
                <div class="vung-thong-tin border">
                    <p class="mb-2 d-flex justify-content-between">
                        <span>Mã đơn hàng:</span>
                        <strong class="text-primary">#<?php echo $order_id; ?></strong>
                    </p>
                    <p class="mb-2 d-flex justify-content-between">
                        <span>Trạng thái:</span>
                        <span class="badge bg-warning text-dark px-3 py-2">Đang chờ xử lý</span>
                    </p>
                    <hr>
                    <p class="mb-0 small text-muted italic">
                        <i class="fas fa-info-circle me-1"></i> 
                        Nhân viên sẽ liên hệ với bạn qua số điện thoại để xác nhận đơn hàng trong thời gian sớm nhất.
                    </p>
                </div>

                <div class="row g-3">
                    <div class="col-sm-6">
                        <a href="../../index.php" class="btn btn-outline-primary w-100 nut-hanh-dong shadow-sm">
                            <i class="fas fa-shopping-bag me-2"></i>Tiếp tục mua
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <a href="../orders/index.php" class="btn btn-primary w-100 nut-hanh-dong shadow-sm">
                            <i class="fas fa-file-invoice me-2"></i>Xem đơn hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../footer.php'; ?>
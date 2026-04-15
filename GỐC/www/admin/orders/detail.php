<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

// 1. Lấy ID đơn hàng từ URL kiểu truyền thống
$order_id = 0;
if (isset($_GET['id'])) {
    $order_id = (int)$_GET['id'];
}

if ($order_id == 0) {
    $_SESSION['error'] = "Không tìm thấy mã đơn hàng.";
    header('Location: index.php');
    exit();
}

// 2. Lấy thông tin đơn hàng bằng query trần (nối chuỗi trực tiếp)
$sql_order = "SELECT o.*, u.username, u.fullname, u.email, u.phone
              FROM orders o
              LEFT JOIN users u ON o.user_id = u.user_id
              WHERE o.order_id = '$order_id'";
              
$res_order = $conn->query($sql_order);
$order = $res_order->fetch_assoc();

if ($order == null) {
    $_SESSION['error'] = "Đơn hàng này không tồn tại trên hệ thống.";
    header('Location: index.php');
    exit();
}

// 3. Lấy chi tiết sản phẩm (Đổ vào mảng để dùng foreach)
$sql_items = "SELECT oi.*, b.title, b.link_images
              FROM order_items oi
              LEFT JOIN books b ON oi.book_id = b.book_id
              WHERE oi.order_id = '$order_id'";

$res_items = $conn->query($sql_items);
$items_list = array();
if ($res_items) {
    while ($row = $res_items->fetch_assoc()) {
        $items_list[] = $row;
    }
}

// 4. Bắt đầu Layout
$page_title = "Xem đơn hàng #" . $order_id;
admin_layout_start($page_title, 'orders');
?>

<style>
    .khung-don-hang { background: #fff; padding: 25px; border-radius: 15px; border: 1px solid #eee; }
    .o-thong-tin { background: #f8f9fa; border-radius: 10px; padding: 15px; height: 100%; }
    .anh-sach { width: 50px; height: 70px; object-fit: cover; border-radius: 5px; }
    .nut-tron { border-radius: 20px !important; padding: 8px 20px !important; font-weight: bold; }
    
    /* CSS cho in ấn đơn giản */
    @media print {
        .d-print-none, .sidebar, .navbar, .btn { display: none !important; }
        .khung-don-hang { border: none !important; padding: 0 !important; }
        .table { width: 100% !important; border-collapse: collapse !important; }
        .table th, .table td { border: 1px solid #000 !important; padding: 5px !important; }
    }
</style>

<div class="container-fluid">
    <div class="khung-don-hang shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h4 class="fw-bold text-primary mb-0">CHI TIẾT ĐƠN HÀNG #<?php echo $order_id; ?></h4>
                <small class="text-muted">Ngày đặt: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></small>
            </div>
            <div>
                <?php 
                $st = $order['status'];
                if ($st == 'pending') { echo '<span class="badge rounded-pill bg-warning text-dark px-3 py-2">Đang chờ xử lý</span>'; }
                else if ($st == 'processing') { echo '<span class="badge rounded-pill bg-info px-3 py-2">Đang đóng gói</span>'; }
                else if ($st == 'shipped') { echo '<span class="badge rounded-pill bg-primary px-3 py-2">Đang giao hàng</span>'; }
                else if ($st == 'delivered') { echo '<span class="badge rounded-pill bg-success px-3 py-2">Đã giao thành công</span>'; }
                else { echo '<span class="badge rounded-pill bg-danger px-3 py-2">Đã hủy đơn</span>'; }
                ?>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="o-thong-tin">
                    <h6 class="fw-bold text-secondary border-bottom pb-2 mb-3">
                        <i class="fas fa-user me-2"></i>THÔNG TIN NGƯỜI NHẬN
                    </h6>
                    <p class="mb-1">Họ tên: <strong><?php echo $order['fullname']; ?></strong></p>
                    <p class="mb-1">Số điện thoại: <strong><?php echo $order['phone']; ?></strong></p>
                    <p class="mb-1">Email: <?php echo $order['email']; ?></p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="o-thong-tin">
                    <h6 class="fw-bold text-secondary border-bottom pb-2 mb-3">
                        <i class="fas fa-map-marker-alt me-2"></i>ĐỊA CHỈ GIAO HÀNG
                    </h6>
                    <p class="mb-0"><?php echo nl2br($order['shipping_address']); ?></p>
                    <div class="mt-2 small text-muted">Phương thức: <b><?php echo $order['payment_method']; ?></b></div>
                </div>
            </div>
        </div>

        <h6 class="fw-bold mb-3"><i class="fas fa-list me-2"></i>DANH SÁCH SÁCH ĐÃ ĐẶT</h6>
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th width="80">Ảnh</th>
                        <th class="text-start">Tên sách</th>
                        <th width="100">Số lượng</th>
                        <th width="150">Đơn giá</th>
                        <th width="150">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $tong_sl = 0;
                    foreach ($items_list as $item) { 
                        $thanh_tien = $item['quantity'] * $item['price'];
                        $tong_sl = $tong_sl + $item['quantity'];
                    ?>
                        <tr>
                            <td class="text-center">
                                <img src="<?php echo $item['link_images']; ?>" class="anh-sach shadow-sm">
                            </td>
                            <td>
                                <div class="fw-bold"><?php echo $item['title']; ?></div>
                                <small class="text-muted">Mã sách: #<?php echo $item['book_id']; ?></small>
                            </td>
                            <td class="text-center"><?php echo $item['quantity']; ?></td>
                            <td class="text-end"><?php echo number_format($item['price']); ?>đ</td>
                            <td class="text-end fw-bold text-primary"><?php echo number_format($thanh_tien); ?>đ</td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end fw-bold">TỔNG CỘNG THANH TOÁN:</td>
                        <td class="text-end fw-bold text-danger fs-5"><?php echo number_format($order['total_amount']); ?>đ</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="pt-3 border-top d-flex justify-content-between d-print-none">
            <div class="d-flex gap-2">
                <a href="index.php" class="btn btn-outline-secondary nut-tron">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
                <button onclick="window.print()" class="btn btn-dark nut-tron">
                    <i class="fas fa-print me-2"></i>In hóa đơn
                </button>
            </div>

            <div class="d-flex gap-2">
                <?php if ($order['status'] == 'pending') { ?>
                    <a href="update_status.php?id=<?php echo $order_id; ?>&status=processing" class="btn btn-info text-white nut-tron">Xác nhận đơn</a>
                <?php } ?>

                <?php if ($order['status'] == 'processing') { ?>
                    <a href="update_status.php?id=<?php echo $order_id; ?>&status=shipped" class="btn btn-primary nut-tron">Giao hàng</a>
                <?php } ?>

                <?php if ($order['status'] == 'shipped') { ?>
                    <a href="update_status.php?id=<?php echo $order_id; ?>&status=delivered" class="btn btn-success nut-tron">Hoàn tất đơn</a>
                <?php } ?>

                <?php if ($order['status'] != 'delivered' && $order['status'] != 'cancelled') { ?>
                    <a href="update_status.php?id=<?php echo $order_id; ?>&status=cancelled" 
                       class="btn btn-danger nut-tron" 
                       onclick="return confirm('Bạn có chắc muốn hủy đơn này?')">Hủy đơn hàng</a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php admin_layout_end(); ?>
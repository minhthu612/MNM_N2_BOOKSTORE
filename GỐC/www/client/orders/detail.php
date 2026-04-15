<?php
require_once '../../includes/client_check.php';
require_once '../../config.php';

// 1. LẤY ID ĐƠN HÀNG TỪ URL
$order_id = 0;
if (isset($_GET['id'])) {
    $order_id = (int)$_GET['id'];
}

$user_id = $_SESSION['user_id'];

// 2. LẤY THÔNG TIN ĐƠN HÀNG (Dùng query trần)
$sql_order = "SELECT o.*, u.fullname, u.phone 
              FROM orders o 
              JOIN users u ON o.user_id = u.user_id 
              WHERE o.order_id = '$order_id' AND o.user_id = '$user_id' LIMIT 1";
$res_order = $conn->query($sql_order);
$order = $res_order->fetch_assoc();

if ($order == null) {
    header("Location: index.php");
    exit();
}

// 3. LẤY DANH SÁCH SẢN PHẨM TRONG ĐƠN (Đổ vào mảng để dùng foreach)
$sql_items = "SELECT oi.*, b.title, b.link_images 
              FROM order_items oi 
              JOIN books b ON oi.book_id = b.book_id 
              WHERE oi.order_id = '$order_id'";
$res_items = $conn->query($sql_items);
$order_items_list = array();
if ($res_items) {
    while ($row_item = $res_items->fetch_assoc()) {
        $order_items_list[] = $row_item;
    }
}

$page_title = "Chi tiết đơn hàng #" . $order_id;
include '../../header.php';
?>

<style>
    body { background-color: #f8f9fa; }
    .khung-trang { background: #fff; border-radius: 15px; padding: 25px; border: 1px solid #eee; margin-bottom: 20px; }
    .anh-sach-nho { width: 55px; height: 75px; object-fit: cover; border-radius: 8px; border: 1px solid #f1f1f1; }
    .tieu-de-muc { border-bottom: 2px solid #f8f9fa; padding-bottom: 12px; margin-bottom: 20px; font-weight: bold; color: #333; }
    .breadcrumb-custom { background: #fff; padding: 12px 20px; border-radius: 10px; border: 1px solid #eee; margin-bottom: 25px; }
</style>

<div class="container py-5">
    <div class="breadcrumb-custom shadow-sm">
        <a href="index.php" class="text-decoration-none fw-bold"><i class="fas fa-history me-1"></i> Lịch sử đơn hàng</a> 
        <span class="mx-2 text-muted">/</span> 
        <span class="text-secondary">Chi tiết đơn hàng #<?php echo $order_id; ?></span>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="khung-trang shadow-sm">
                <h6 class="tieu-de-muc"><i class="fas fa-map-marker-alt text-danger me-2"></i>THÔNG TIN NHẬN HÀNG</h6>
                <div class="small">
                    <p class="mb-2"><strong>Người nhận:</strong> <?php echo $order['fullname']; ?></p>
                    <p class="mb-2"><strong>Số điện thoại:</strong> <?php echo $order['phone']; ?></p>
                    <p class="mb-3"><strong>Địa chỉ giao:</strong> <br><span class="text-muted"><?php echo $order['shipping_address']; ?></span></p>
                    <div class="p-2 bg-light rounded border small">
                        <strong>Ghi chú:</strong> <?php if ($order['notes'] != '') { echo $order['notes']; } else { echo 'Không có ghi chú.'; } ?>
                    </div>
                </div>
            </div>

            <div class="khung-trang shadow-sm">
                <h6 class="tieu-de-muc"><i class="fas fa-info-circle text-primary me-2"></i>TRẠNG THÁI ĐƠN HÀNG</h6>
                <div class="small">
                    <p class="mb-2"><strong>Thanh toán:</strong> <?php echo $order['payment_method']; ?></p>
                    <p class="mb-3"><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                    
                    <div class="mb-4">
                        <strong>Trạng thái:</strong> <br>
                        <?php
                            $css = 'bg-warning text-dark';
                            $txt = 'Đang chờ xác nhận';
                            if($order['status'] == 'completed') { $css = 'bg-success text-white'; $txt = 'Giao hàng thành công'; }
                            if($order['status'] == 'cancelled') { $css = 'bg-danger text-white'; $txt = 'Đơn hàng đã hủy'; }
                        ?>
                        <span class="badge <?php echo $css; ?> px-3 py-2 mt-1 rounded-pill">
                            <?php echo $txt; ?>
                        </span>
                    </div>

                    <?php if ($order['status'] == 'pending') { ?>
                        <hr>
                        <div class="alert alert-warning border-0 small mb-3">Bạn có thể hủy đơn khi đơn hàng ở trạng thái chờ xác nhận.</div>
                        <a href="cancel.php?id=<?php echo $order_id; ?>" 
                           class="btn btn-danger w-100 fw-bold rounded-pill shadow-sm"
                           onclick="return confirm('Bạn chắc chắn muốn hủy đơn hàng này? Thao tác này không thể hoàn tác.')">
                            <i class="fas fa-times-circle me-1"></i> HỦY ĐƠN HÀNG NGAY
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="khung-trang shadow-sm p-0 overflow-hidden">
                <div class="p-4 border-bottom">
                    <h6 class="fw-bold m-0"><i class="fas fa-box-open me-2 text-primary"></i>SẢN PHẨM TRONG ĐƠN</h6>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light small text-muted text-uppercase">
                            <tr>
                                <th class="ps-4">Sách</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end pe-4">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_items_list as $item) { ?>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $item['link_images']; ?>" class="anh-sach-nho me-3">
                                            <div>
                                                <div class="fw-bold text-dark small"><?php echo $item['title']; ?></div>
                                                <div class="text-muted small">Giá: <?php echo number_format($item['price'], 0, ',', '.'); ?>đ</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-3">x<?php echo $item['quantity']; ?></span>
                                    </td>
                                    <td class="text-end pe-4 fw-bold text-primary">
                                        <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="2" class="text-end ps-4 py-3 fw-bold fs-6">TỔNG TIỀN THANH TOÁN:</td>
                                <td class="text-end pe-4 py-3 fw-bold text-danger fs-4">
                                    <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <div class="p-3 bg-white rounded-4 border shadow-sm mt-3">
                <div class="d-flex align-items-center text-muted small">
                    <i class="fas fa-shield-alt fa-2x me-3 opacity-50"></i>
                    <div>
                        <b>Chính sách đơn hàng:</b><br>
                        Nếu có bất kỳ vấn đề gì về sản phẩm sau khi nhận hàng, vui lòng liên hệ hotline trong vòng 7 ngày để được hỗ trợ đổi trả.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../footer.php'; ?>
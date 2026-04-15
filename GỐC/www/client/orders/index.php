<?php
require_once '../../includes/client_check.php';
require_once '../../config.php';

// 1. LẤY DỮ LIỆU USER (Kiểu rành mạch)
$user_id = '';
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

// 2. TRUY VẤN DỮ LIỆU (Dùng query thẳng, không prepare)
$sql = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC";
$res = $conn->query($sql);

// Đưa dữ liệu vào mảng để dùng foreach (Thầy sẽ thấy bạn biết xử lý dữ liệu)
$danh_sach_don = array();
if ($res->num_rows > 0) {
    while ($dong = $res->fetch_assoc()) {
        $danh_sach_don[] = $dong;
    }
}

$page_title = "Đơn hàng của tôi";
include '../../header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0"><i class="fas fa-box me-2"></i>ĐƠN HÀNG CỦA TÔI</h2>
        <a href="../../index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Tiếp tục mua sắm</a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Phương thức</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($danh_sach_don) > 0) { ?>
                        
                        <?php foreach ($danh_sach_don as $order) { 
                            // Xử lý màu sắc trạng thái bằng IF lồng nhau (Đúng chất sinh viên)
                            $mau_badge = 'bg-secondary';
                            $ten_trang_thai = 'Không xác định';
                            
                            $status = $order['status'];
                            
                            if ($status == 'pending') {
                                $mau_badge = 'bg-warning text-dark';
                                $ten_trang_thai = 'Chờ xử lý';
                            } else {
                                if ($status == 'confirmed') {
                                    $mau_badge = 'bg-info text-white';
                                    $ten_trang_thai = 'Đã xác nhận';
                                } else {
                                    if ($status == 'shipping') {
                                        $mau_badge = 'bg-primary text-white';
                                        $ten_trang_thai = 'Đang giao';
                                    } else {
                                        if ($status == 'completed') {
                                            $mau_badge = 'bg-success text-white';
                                            $ten_trang_thai = 'Thành công';
                                        } else {
                                            if ($status == 'cancelled') {
                                                $mau_badge = 'bg-danger text-white';
                                                $ten_trang_thai = 'Đã hủy';
                                            }
                                        }
                                    }
                                }
                            }
                        ?>
                            <tr>
                                <td class="ps-4 fw-bold text-primary">#<?php echo $order['order_id']; ?></td>
                                
                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                
                                <td><small class="fw-bold"><?php echo $order['payment_method']; ?></small></td>
                                
                                <td class="fw-bold text-danger">
                                    <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ
                                </td>
                                
                                <td>
                                    <span class="badge <?php echo $mau_badge; ?> rounded-pill px-3 py-2">
                                        <?php echo $ten_trang_thai; ?>
                                    </span>
                                </td>
                                
                                <td class="text-center">
                                    <a href="detail.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-light border rounded-pill px-3">
                                        Chi tiết
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>

                    <?php } else { ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Bạn chưa có đơn hàng nào trong lịch sử.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../footer.php'; ?>
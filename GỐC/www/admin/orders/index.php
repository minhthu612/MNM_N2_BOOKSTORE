<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

// 1. Lấy dữ liệu lọc từ GET (Viết kiểu tường minh từng biến)
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$status = '';
if (isset($_GET['status'])) {
    $status = $_GET['status'];
}

$date_from = '';
if (isset($_GET['date_from'])) {
    $date_from = $_GET['date_from'];
}

$date_to = '';
if (isset($_GET['date_to'])) {
    $date_to = $_GET['date_to'];
}

// 2. Xây dựng câu lệnh WHERE (Nối chuỗi trực tiếp)
$where = " WHERE 1=1 ";

if ($search != '') {
    $where = $where . " AND (o.order_id LIKE '%$search%' OR u.fullname LIKE '%$search%' OR u.username LIKE '%$search%' OR o.tracking_number LIKE '%$search%') ";
}

if ($status != '') {
    $where = $where . " AND o.status = '$status' ";
}

if ($date_from != '') {
    $where = $where . " AND DATE(o.created_at) >= '$date_from' ";
}

if ($date_to != '') {
    $where = $where . " AND DATE(o.created_at) <= '$date_to' ";
}

// 3. Tính toán phân trang
$page = 1;
if (isset($_GET['page'])) {
    $page = (int)$_GET['page'];
}
if ($page < 1) {
    $page = 1;
}

$limit = 10;
$offset = ($page - 1) * $limit;

// 4. Lấy tổng số dòng để tính trang
$sql_count = "SELECT COUNT(*) as total FROM orders o LEFT JOIN users u ON o.user_id = u.user_id " . $where;
$res_count = $conn->query($sql_count);
$row_count = $res_count->fetch_assoc();
$total = $row_count['total'];
$total_pages = ceil($total / $limit);

// 5. Lấy danh sách đơn hàng (Dùng Sub-query trần cho đúng chất SV)
$sql_main = "SELECT o.*, u.username, u.fullname,
            (SELECT COUNT(*) FROM order_items WHERE order_id = o.order_id) AS item_count,
            (SELECT SUM(quantity) FROM order_items WHERE order_id = o.order_id) AS total_qty
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.user_id 
            $where 
            ORDER BY o.created_at ASC 
            LIMIT $limit OFFSET $offset";

$res_main = $conn->query($sql_main);
$orders_list = array();
if ($res_main) {
    while ($row = $res_main->fetch_assoc()) {
        $orders_list[] = $row;
    }
}

// 6. Lấy thống kê nhanh
$sql_stats = "SELECT 
            COUNT(*) as total_orders,
            SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending_count,
            SUM(total_amount) as total_revenue
            FROM orders";
$stats = $conn->query($sql_stats)->fetch_assoc();

admin_layout_start("Quản lý đơn hàng", 'orders');
?>

<style>
    .the-bang { background: #fff; border-radius: 12px; }
    .bang-du-lieu th { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; }
    .form-control, .form-select { border-radius: 20px; }
    .stat-box { border-radius: 15px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .nut-hanh-dong {
        border-radius: 20px !important;
        padding: 5px 15px !important;
        margin: 0 3px;
        font-size: 0.85rem;
        display: inline-block;
        text-decoration: none;
    }
    .badge-tron { border-radius: 20px; padding: 6px 12px; }
</style>

<div class="container-fluid">
    <div class="row g-3 mb-4 text-center">
        <div class="col-md-4">
            <div class="card stat-box bg-primary text-white p-3">
                <h3 class="mb-0 fw-bold"><?php echo $stats['total_orders']; ?></h3>
                <div class="small">Tổng số đơn hàng</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-box bg-warning text-dark p-3">
                <h3 class="mb-0 fw-bold"><?php echo $stats['pending_count']; ?></h3>
                <div class="small">Đơn chờ xử lý</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-box bg-success text-white p-3">
                <h3 class="mb-0 fw-bold"><?php echo number_format($stats['total_revenue']); ?>đ</h3>
                <div class="small">Tổng doanh thu</div>
            </div>
        </div>
    </div>

    <div class="card the-bang border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 text-primary fw-bold"><i class="fas fa-shopping-cart me-2"></i>DANH SÁCH ĐƠN HÀNG</h5>
        </div>

        <div class="card-body">
            <form method="GET" action="" class="row g-2 mb-4">
                <div class="col-md-3">
                    <input type="text" class="form-control px-3" name="search" placeholder="Mã đơn, tên khách..." value="<?php echo $search; ?>">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">-- Trạng thái --</option>
                        <option value="pending" <?php if($status == 'pending') echo 'selected'; ?>>Chờ xử lý</option>
                        <option value="processing" <?php if($status == 'processing') echo 'selected'; ?>>Đang xử lý</option>
                        <option value="shipped" <?php if($status == 'shipped') echo 'selected'; ?>>Đang giao</option>
                        <option value="delivered" <?php if($status == 'delivered') echo 'selected'; ?>>Đã giao</option>
                        <option value="cancelled" <?php if($status == 'cancelled') echo 'selected'; ?>>Đã hủy</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" value="<?php echo $date_from; ?>">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" value="<?php echo $date_to; ?>">
                </div>
                <div class="col-md-3 d-flex gap-1">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Lọc</button>
                    <a href="index.php" class="btn btn-outline-secondary w-100 rounded-pill">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover bang-du-lieu align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="100">Mã đơn</th>
                            <th class="text-start">Khách hàng</th>
                            <th>Số lượng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                            <th width="220">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($orders_list) > 0) { ?>
                            <?php foreach ($orders_list as $o) { ?>
                                <tr class="text-center">
                                    <td><span class="fw-bold text-primary">#<?php echo $o['order_id']; ?></span></td>
                                    <td class="text-start">
                                        <div class="fw-bold"><?php echo htmlspecialchars($o['fullname'] ? $o['fullname'] : $o['username']); ?></div>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-info text-dark px-3 py-2">
                                            <?php echo $o['total_qty']; ?> cuốn
                                        </span>
                                    </td>
                                    <td><span class="fw-bold text-danger"><?php echo number_format($o['total_amount']); ?>đ</span></td>
                                    <td>
                                        <?php 
                                        $st = $o['status'];
                                        if ($st == 'pending') { echo '<span class="badge bg-warning text-dark badge-tron">CHỜ XỬ LÝ</span>'; }
                                        else if ($st == 'processing') { echo '<span class="badge bg-info text-white badge-tron">ĐANG XỬ LÝ</span>'; }
                                        else if ($st == 'shipped') { echo '<span class="badge bg-primary badge-tron">ĐANG GIAO</span>'; }
                                        else if ($st == 'delivered') { echo '<span class="badge bg-success badge-tron">ĐÃ GIAO</span>'; }
                                        else { echo '<span class="badge bg-danger badge-tron">ĐÃ HỦY</span>'; }
                                        ?>
                                    </td>
                                    <td>
                                        <div class="small"><?php echo date('d/m/Y', strtotime($o['created_at'])); ?></div>
                                        <div class="text-muted" style="font-size: 0.75rem;"><?php echo date('H:i', strtotime($o['created_at'])); ?></div>
                                    </td>
                                    <td>
                                        <a href="detail.php?id=<?php echo $o['order_id']; ?>" class="btn btn-info text-white nut-hanh-dong">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                        <a href="update_status.php?id=<?php echo $o['order_id']; ?>" class="btn btn-warning text-dark nut-hanh-dong">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted">Không có đơn hàng nào.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1) { ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                            <li class="page-item <?php if($i == $page) { echo 'active'; } ?>">
                                <a class="page-link shadow-none" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&status=<?php echo $status; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </nav>
            <?php } ?>
        </div>
    </div>
</div>

<?php admin_layout_end(); ?>
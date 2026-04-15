<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

// 1. Lấy dữ liệu lọc từ GET (Viết tường minh kiểu truyền thống)
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$status_filter = '';
if (isset($_GET['status'])) {
    $status_filter = $_GET['status'];
}

// 2. Xây dựng câu lệnh WHERE (Nối chuỗi trực tiếp)
$where_sql = " WHERE 1=1 ";
if ($search != '') {
    $where_sql = $where_sql . " AND (b.title LIKE '%$search%' OR b.author LIKE '%$search%') ";
}

if ($status_filter != '') {
    $where_sql = $where_sql . " AND i.stock_status = '$status_filter' ";
}

// 3. Tính toán phân trang
$page = 1;
if (isset($_GET['page'])) {
    $page = (int)$_GET['page'];
}
if ($page < 1) { $page = 1; }

$limit = 15;
$offset = ($page - 1) * $limit;

// 4. Lấy tổng số dòng để tính trang
$sql_count = "SELECT COUNT(*) as total FROM inventory i JOIN books b ON i.book_id = b.book_id " . $where_sql;
$res_count = $conn->query($sql_count);
$row_count = $res_count->fetch_assoc();
$total = $row_count['total'];
$total_pages = ceil($total / $limit);

// 5. Lấy danh sách tồn kho (Query trần)
$sql_main = "SELECT i.*, b.title, b.author
             FROM inventory i 
             JOIN books b ON i.book_id = b.book_id 
             $where_sql 
             ORDER BY i.last_updated DESC 
             LIMIT $limit OFFSET $offset";

$res_main = $conn->query($sql_main);
$inventory_list = array();
if ($res_main) {
    while ($row = $res_main->fetch_assoc()) {
        $inventory_list[] = $row;
    }
}

// 6. Lấy số liệu thống kê nhanh
$stats_sql = "SELECT 
        SUM(CASE WHEN stock > 0 THEN 1 ELSE 0 END) as in_stock,
        SUM(CASE WHEN stock <= 0 THEN 1 ELSE 0 END) as out_of_stock,
        SUM(stock) as total_qty
        FROM inventory";
$stats = $conn->query($stats_sql)->fetch_assoc();

admin_layout_start("Báo cáo Tồn kho", 'inventory');
?>

<style>
    .the-bang { background: #fff; border-radius: 12px; }
    .bang-du-lieu th { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; }
    .form-control, .form-select { border-radius: 20px; }
    
    .the-thong-ke { 
        border-radius: 15px; 
        border: none; 
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        transition: 0.3s;
    }
    
    .nut-hanh-dong {
        border-radius: 20px !important;
        padding: 5px 15px !important;
        font-size: 0.85rem;
        display: inline-block;
        text-decoration: none;
    }
    
    .badge-tron {
        border-radius: 20px;
        padding: 6px 12px;
    }
</style>

<div class="container-fluid">
    <div class="row g-3 mb-4 text-center">
        <div class="col-md-4">
            <div class="card the-thong-ke bg-primary text-white p-3">
                <h3 class="fw-bold mb-0"><?php echo number_format($stats['total_qty']); ?></h3>
                <div class="small">Tổng số lượng tồn kho</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card the-thong-ke bg-success text-white p-3">
                <h3 class="fw-bold mb-0"><?php echo $stats['in_stock']; ?></h3>
                <div class="small">Đầu sách còn hàng</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card the-thong-ke bg-danger text-white p-3">
                <h3 class="fw-bold mb-0"><?php echo $stats['out_of_stock']; ?></h3>
                <div class="small">Đầu sách hết hàng</div>
            </div>
        </div>
    </div>

    <div class="card the-bang border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary fw-bold">CHI TIẾT TỒN KHO HIỆN TẠI</h5>
            <span class="badge rounded-pill bg-light text-dark border">Cập nhật lúc: <?php echo date('H:i d/m/Y'); ?></span>
        </div>

        <div class="card-body">
            <form method="GET" action="" class="row g-2 mb-4">
                <div class="col-md-5">
                    <input type="text" class="form-control px-4" name="search" placeholder="Tìm tên sách hoặc tác giả..." value="<?php echo $search; ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-select px-4" name="status">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="ACTIVE" <?php if($status_filter == 'ACTIVE') { echo 'selected'; } ?>>Còn hàng</option>
                        <option value="LOW_STOCK" <?php if($status_filter == 'LOW_STOCK') { echo 'selected'; } ?>>Sắp hết hàng</option>
                        <option value="OUT_OF_STOCK" <?php if($status_filter == 'OUT_OF_STOCK') { echo 'selected'; } ?>>Đã hết hàng</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Lọc kết quả</button>
                </div>
                <div class="col-md-2">
                    <a href="history.php" class="btn btn-outline-secondary w-100 rounded-pill">Xóa lọc</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover bang-du-lieu align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="150">Cập nhật cuối</th>
                            <th class="text-start">Thông tin sản phẩm</th>
                            <th width="120">Số lượng</th>
                            <th width="150">Trạng thái</th>
                            <th width="120">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($inventory_list) > 0) { ?>
                            <?php foreach ($inventory_list as $row) { ?>
                                <tr class="text-center">
                                    <td>
                                        <div class="small fw-bold"><?php echo date('d/m/Y', strtotime($row['last_updated'])); ?></div>
                                        <div class="text-muted small"><?php echo date('H:i', strtotime($row['last_updated'])); ?></div>
                                    </td>
                                    <td class="text-start">
                                        <div class="fw-bold"><?php echo $row['title']; ?></div>
                                        <small class="text-muted"><?php echo $row['author']; ?></small>
                                    </td>
                                    <td>
                                        <span class="fw-bold <?php echo ($row['stock'] <= 0) ? 'text-danger' : 'text-dark'; ?>">
                                            <?php echo $row['stock']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                            if ($row['stock_status'] == 'ACTIVE') {
                                                echo '<span class="badge bg-success badge-tron">CÒN HÀNG</span>';
                                            } else if ($row['stock_status'] == 'LOW_STOCK') {
                                                echo '<span class="badge bg-warning text-dark badge-tron">SẮP HẾT</span>';
                                            } else {
                                                echo '<span class="badge bg-danger badge-tron">HẾT HÀNG</span>';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="update.php?id=<?php echo $row['inventory_id']; ?>" class="btn btn-info text-white nut-hanh-dong">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Không tìm thấy dữ liệu tồn kho.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1) { ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for($i = 1; $i <= $total_pages; $i++) { ?>
                            <li class="page-item <?php if($i == $page) { echo 'active'; } ?>">
                                <a class="page-link shadow-none" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&status=<?php echo $status_filter; ?>">
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
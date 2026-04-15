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

$stock_filter = '';
if (isset($_GET['stock_filter'])) {
    $stock_filter = $_GET['stock_filter'];
}

// 2. Xây dựng câu lệnh WHERE (Nối chuỗi trực tiếp)
$where_sql = " WHERE 1=1 ";

if ($search != '') {
    $where_sql = $where_sql . " AND (b.title LIKE '%$search%' OR b.author LIKE '%$search%') ";
}

if ($status != '' && $status != 'all') {
    $where_sql = $where_sql . " AND i.stock_status = '$status' ";
}

if ($stock_filter != '') {
    if ($stock_filter == 'negative') {
        $where_sql = $where_sql . " AND i.stock < 0 ";
    } else {
        if ($stock_filter == 'zero') {
            $where_sql = $where_sql . " AND i.stock = 0 ";
        } else {
            if ($stock_filter == 'low') {
                $where_sql = $where_sql . " AND i.stock < i.reorder_level AND i.stock > 0 ";
            } else {
                if ($stock_filter == 'good') {
                    $where_sql = $where_sql . " AND i.stock >= i.reorder_level ";
                }
            }
        }
    }
}

// 3. Xử lý Tự động cập nhật trạng thái (Nếu có yêu cầu từ URL)
if (isset($_GET['auto_update'])) {
    if ($_GET['auto_update'] == 'true') {
        $sql_auto = "UPDATE inventory 
                     SET stock_status = CASE 
                        WHEN stock <= 0 THEN 'OUT_OF_STOCK'
                        WHEN stock < reorder_level THEN 'LOW_STOCK'
                        ELSE 'ACTIVE'
                     END,
                     last_updated = NOW()";
        if ($conn->query($sql_auto)) {
            $_SESSION['success'] = "Đã cập nhật trạng thái kho tự động!";
            header("Location: index.php");
            exit();
        }
    }
}

// 4. Tính toán phân trang
$page = 1;
if (isset($_GET['page'])) {
    $page = (int)$_GET['page'];
}
if ($page < 1) { $page = 1; }

$limit = 20;
$offset = ($page - 1) * $limit;

// 5. Lấy tổng số dòng để tính tổng số trang
$sql_count = "SELECT COUNT(*) as total FROM inventory i JOIN books b ON i.book_id = b.book_id " . $where_sql;
$res_count = $conn->query($sql_count);
$row_count = $res_count->fetch_assoc();
$total = $row_count['total'];
$total_pages = ceil($total / $limit);

// 6. Lấy danh sách dữ liệu (Query trần, nối biến trực tiếp)
$sql_main = "SELECT i.*, b.title, b.author, b.price
             FROM inventory i 
             JOIN books b ON i.book_id = b.book_id 
             $where_sql 
             ORDER BY i.stock DESC, i.last_updated ASC 
             LIMIT $limit OFFSET $offset";

$res_main = $conn->query($sql_main);
$inventory_list = array();
if ($res_main) {
    while ($row = $res_main->fetch_assoc()) {
        $inventory_list[] = $row;
    }
}

// 7. Lấy số liệu thống kê nhanh
$sql_stats = "SELECT 
        COUNT(*) as total_items,
        SUM(CASE WHEN stock < 0 THEN 1 ELSE 0 END) as negative,
        SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as zero,
        SUM(CASE WHEN stock < reorder_level AND stock > 0 THEN 1 ELSE 0 END) as low,
        SUM(CASE WHEN stock >= reorder_level THEN 1 ELSE 0 END) as good
        FROM inventory";
$stats = $conn->query($sql_stats)->fetch_assoc();

admin_layout_start("Quản lý Tồn kho", 'inventory');
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
    
    <?php if (isset($_SESSION['success'])) { ?>
        <div class="alert alert-success border-0 shadow-sm"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php } ?>

    <div class="row g-3 mb-4 text-center">
        <div class="col">
            <div class="card stat-box bg-dark text-white p-3">
                <h3 class="mb-0 fw-bold"><?php echo $stats['total_items']; ?></h3>
                <div class="small">Tổng sách</div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-box bg-danger text-white p-3">
                <h3 class="mb-0 fw-bold"><?php echo $stats['zero']; ?></h3>
                <div class="small">Hết hàng</div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-box bg-warning text-dark p-3">
                <h3 class="mb-0 fw-bold"><?php echo $stats['low']; ?></h3>
                <div class="small">Sắp hết</div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-box bg-success text-white p-3">
                <h3 class="mb-0 fw-bold"><?php echo $stats['good']; ?></h3>
                <div class="small">Đủ hàng</div>
            </div>
        </div>
    </div>

    <div class="card the-bang border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary fw-bold">DANH SÁCH TỒN KHO</h5>
            <a href="index.php?auto_update=true" class="btn btn-warning btn-sm rounded-pill px-3" onclick="return confirm('Cập nhật trạng thái tự động?')">
                <i class="fas fa-sync"></i> Tự động cập nhật
            </a>
        </div>

        <div class="card-body">
            <form method="GET" action="" class="row g-2 mb-4">
                <div class="col-md-4">
                    <input type="text" class="form-control px-3" name="search" placeholder="Tên sách, tác giả..." value="<?php echo $search; ?>">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="all">Mọi trạng thái</option>
                        <option value="ACTIVE" <?php if($status == 'ACTIVE') { echo 'selected'; } ?>>Đủ hàng</option>
                        <option value="LOW_STOCK" <?php if($status == 'LOW_STOCK') { echo 'selected'; } ?>>Sắp hết</option>
                        <option value="OUT_OF_STOCK" <?php if($status == 'OUT_OF_STOCK') { echo 'selected'; } ?>>Hết hàng</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="stock_filter" class="form-select">
                        <option value="">Mọi số lượng</option>
                        <option value="zero" <?php if($stock_filter == 'zero') { echo 'selected'; } ?>>Hết hàng (0)</option>
                        <option value="low" <?php if($stock_filter == 'low') { echo 'selected'; } ?>>Dưới mức đặt lại</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Lọc</button>
                </div>
                <div class="col-md-2">
                    <a href="index.php" class="btn btn-outline-secondary w-100 rounded-pill">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="60">ID</th>
                            <th class="text-start">Tên sách</th>
                            <th>Tồn kho</th>
                            <th>Mức tối thiểu</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($inventory_list) > 0) { ?>
                            <?php foreach ($inventory_list as $item) { ?>
                                <tr class="text-center">
                                    <td><?php echo $item['inventory_id']; ?></td>
                                    <td class="text-start">
                                        <div class="fw-bold"><?php echo $item['title']; ?></div>
                                        <small class="text-muted"><?php echo $item['author']; ?></small>
                                    </td>
                                    <td>
                                        <span class="fw-bold <?php if($item['stock'] <= 0) { echo 'text-danger'; } else { echo 'text-dark'; } ?>">
                                            <?php echo $item['stock']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $item['reorder_level']; ?></td>
                                    <td>
                                        <?php 
                                            if ($item['stock_status'] == 'ACTIVE') {
                                                echo '<span class="badge bg-success badge-tron text-white">ĐỦ HÀNG</span>';
                                            } else if ($item['stock_status'] == 'LOW_STOCK') {
                                                echo '<span class="badge bg-warning badge-tron text-dark">SẮP HẾT</span>';
                                            } else {
                                                echo '<span class="badge bg-danger badge-tron text-white">HẾT HÀNG</span>';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="update.php?id=<?php echo $item['inventory_id']; ?>" class="btn btn-info text-white nut-hanh-dong">
                                            <i class="fas fa-edit"></i> Sửa kho
                                        </a>
                                        <a href="history.php?id=<?php echo $item['inventory_id']; ?>" class="btn btn-secondary nut-hanh-dong text-white">
                                            <i class="fas fa-history"></i> Log
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">Không có dữ liệu kho.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1) { ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                            <li class="page-item <?php if($i == $page) { echo 'active'; } ?>">
                                <a class="page-link shadow-none" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&status=<?php echo $status; ?>&stock_filter=<?php echo $stock_filter; ?>">
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
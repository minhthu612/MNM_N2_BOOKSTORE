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

// 2. Xây dựng câu lệnh WHERE (Nối chuỗi trực tiếp)
$where = " WHERE 1=1 ";
if ($search != '') {
    $where = $where . " AND (bs.name LIKE '%$search%' OR bs.description LIKE '%$search%') ";
}

if ($status != '') {
    if ($status == 'active') {
        $where = $where . " AND (SELECT COUNT(*) FROM book_set_items WHERE set_id = bs.set_id) > 0 ";
    } else {
        if ($status == 'empty') {
            $where = $where . " AND (SELECT COUNT(*) FROM book_set_items WHERE set_id = bs.set_id) = 0 ";
        }
    }
}

// 3. Tính toán phân trang
$page = 1;
if (isset($_GET['page'])) {
    $page = (int)$_GET['page'];
}
if ($page < 1) {
    $page = 1;
}

$limit = 15;
$offset = ($page - 1) * $limit;

// 4. Lấy tổng số dòng để tính tổng số trang
$sql_count = "SELECT COUNT(*) as total FROM book_sets bs " . $where;
$res_count = $conn->query($sql_count);
$row_count = $res_count->fetch_assoc();
$total_sets = $row_count['total'];

$total_pages = ceil($total_sets / $limit);

// 5. Lấy danh sách bộ sách (Query trần, dùng sub-query để tính số sách và giá)
$sql_main = "SELECT bs.*, 
            (SELECT COUNT(*) FROM book_set_items WHERE set_id = bs.set_id) as book_count,
            (SELECT SUM(b.price * bsi.quantity) FROM book_set_items bsi 
             JOIN books b ON bsi.book_id = b.book_id WHERE bsi.set_id = bs.set_id) as total_price
            FROM book_sets bs 
            $where 
            ORDER BY bs.created_at DESC 
            LIMIT $limit OFFSET $offset";

$res_main = $conn->query($sql_main);

// Đưa dữ liệu vào mảng để dùng foreach
$sets_list = array();
if ($res_main) {
    while ($row = $res_main->fetch_assoc()) {
        $sets_list[] = $row;
    }
}

admin_layout_start('Quản lý bộ sách', 'book_sets');
?>

<style>
    .anh-bo-sach { width: 50px; height: 70px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px; }
    .gia-goc { text-decoration: line-through; color: #888; font-size: 0.9em; }
    .gia-moi { color: #d9534f; font-weight: bold; }
    .bang-du-lieu th { background-color: #f8f9fa; }
    
    /* Nút bấm bo tròn và có khoảng cách như file books */
    .nut-hanh-dong {
        border-radius: 20px !important;
        padding: 5px 15px !important;
        margin: 0 3px;
        font-size: 0.85rem;
        transition: all 0.3s;
        display: inline-block;
        text-decoration: none;
    }
    .nut-hanh-dong:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 text-primary fw-bold">DANH SÁCH BỘ SÁCH (COMBO)</h5>
            <p class="mb-0 small text-muted">Tìm thấy <?php echo $total_sets; ?> bộ sách</p>
        </div>
        <a href="create.php" class="btn btn-success rounded-pill px-4">
            <i class="fas fa-plus"></i> Thêm bộ mới
        </a>
    </div>
    
    <div class="card-body">
        <form method="GET" action="" class="row g-2 mb-4">
            <div class="col-md-4">
                <input type="text" class="form-control rounded-pill" name="search" placeholder="Tên bộ sách..." value="<?php echo $search; ?>">
            </div>
            <div class="col-md-4">
                <select class="form-select rounded-pill" name="status">
                    <option value="">-- Trạng thái bộ sách --</option>
                    <option value="active" <?php if($status == 'active') { echo 'selected'; } ?>>Bộ có sách (Sẵn sàng)</option>
                    <option value="empty" <?php if($status == 'empty') { echo 'selected'; } ?>>Bộ chưa có sách</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 rounded-pill">Lọc dữ liệu</button>
                <a href="index.php" class="btn btn-outline-secondary w-100 rounded-pill">Xóa lọc</a>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-bordered table-hover bang-du-lieu align-middle">
                <thead>
                    <tr>
                        <th width="60">ID</th>
                        <th width="90">Ảnh bìa</th>
                        <th>Tên bộ sách</th>
                        <th>Số lượng sách</th>
                        <th>Giá Combo</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($sets_list) > 0) { ?>
                        <?php foreach ($sets_list as $s) { 
                            $goc = $s['total_price'] != null ? $s['total_price'] : 0;
                            $ban = $goc - ($goc * $s['discount'] / 100);
                        ?>
                            <tr>
                                <td><?php echo $s['set_id']; ?></td>
                                <td class="text-center">
                                    <?php 
                                        $anh = $s['link_images'];
                                        if ($anh == '') { $anh = 'https://via.placeholder.com/50x70?text=Set'; }
                                    ?>
                                    <img src="<?php echo $anh; ?>" class="anh-bo-sach shadow-sm">
                                </td>
                                <td>
                                    <div class="fw-bold"><?php echo $s['name']; ?></div>
                                    <div class="small text-muted"><?php echo substr($s['description'], 0, 50); ?>...</div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-info text-dark">
                                        <?php echo $s['book_count']; ?> quyển sách
                                    </span>
                                </td>
                                <td>
                                    <?php if ($s['discount'] > 0) { ?>
                                        <div class="gia-goc"><?php echo number_format($goc); ?>đ</div>
                                        <div class="gia-moi"><?php echo number_format($ban); ?>đ</div>
                                    <?php } else { ?>
                                        <div class="fw-bold"><?php echo number_format($goc); ?>đ</div>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <a href="edit.php?id=<?php echo $s['set_id']; ?>" class="btn btn-info text-white nut-hanh-dong">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <a href="delete.php?set_id=<?php echo $s['set_id']; ?>" class="btn btn-danger nut-hanh-dong" onclick="return confirm('Xác nhận xóa bộ sách này?')">
                                        <i class="fas fa-trash"></i> Xóa
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Không tìm thấy bộ sách nào.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_pages > 1) { ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                        <li class="page-item <?php if($i == $page) { echo 'active'; } ?>">
                            <a class="page-link shadow-none" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&status=<?php echo $status; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </nav>
        <?php } ?>
    </div>
</div>

<?php admin_layout_end(); ?>
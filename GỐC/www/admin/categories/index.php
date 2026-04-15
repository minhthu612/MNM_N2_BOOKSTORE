<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

// 1. Lấy dữ liệu lọc từ GET
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// 2. Khởi tạo biến $where (ĐÃ XÓA DESCRIPTION Ở ĐÂY)
$where = " WHERE 1=1 ";
if ($search != '') {
    $where = $where . " AND (category_name LIKE '%$search%') ";
}

// 3. Xử lý xóa nhiều
if (isset($_POST['delete_selected'])) {
    if (isset($_POST['selected_ids'])) {
        $ids = $_POST['selected_ids'];
        foreach ($ids as $cat_id) {
            $cat_id = (int)$cat_id;
            $conn->query("DELETE FROM categories WHERE category_id = '$cat_id'");
        }
        $_SESSION['success'] = "Đã xóa các danh mục được chọn!";
        header('Location: index.php');
        exit();
    }
}

// 4. Tính toán phân trang
$page = 1;
if (isset($_GET['page'])) {
    $page = (int)$_GET['page'];
}
if ($page < 1) { $page = 1; }

$limit = 15;
$offset = ($page - 1) * $limit;

// 5. Lấy tổng số dòng
$sql_count = "SELECT COUNT(*) as total FROM categories " . $where;
$res_count = $conn->query($sql_count);
$row_count = $res_count->fetch_assoc();
$total = $row_count['total'];
$total_pages = ceil($total / $limit);

// 6. Lấy danh sách danh mục
$sql_main = "SELECT c.*, 
            (SELECT COUNT(*) FROM books WHERE category_id = c.category_id) as book_count
            FROM categories c 
            $where 
            ORDER BY c.category_id ASC 
            LIMIT $limit OFFSET $offset";

$res_main = $conn->query($sql_main);

$categories_list = array();
if ($res_main) {
    while ($row = $res_main->fetch_assoc()) {
        $categories_list[] = $row;
    }
}

admin_layout_start('Quản lý danh mục', 'categories');
?>

<style>
    .the-bang { background: #fff; border-radius: 12px; }
    .bang-du-lieu th { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; }
    .form-control, .form-select { border-radius: 20px; }
    .nut-hanh-dong {
        border-radius: 20px !important;
        padding: 5px 15px !important;
        margin: 0 3px;
        font-size: 0.85rem;
        display: inline-block;
        text-decoration: none;
        transition: all 0.3s;
    }
    .nut-hanh-dong:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>

<div class="container-fluid">
    
    <?php if (isset($_SESSION['success'])) { ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
            <i class="fas fa-check-circle me-2"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php } ?>

    <div class="card the-bang border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 text-primary fw-bold">DANH SÁCH DANH MỤC</h5>
                <p class="mb-0 small text-muted">Tổng số: <?php echo $total; ?> loại sách</p>
            </div>
            <a href="create.php" class="btn btn-success rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus"></i> Thêm mới
            </a>
        </div>

        <div class="card-body">
            <form method="GET" action="" class="row g-2 mb-4">
                <div class="col-md-6">
                    <input type="text" class="form-control px-4" name="search" placeholder="Tìm kiếm danh mục..." value="<?php echo $search; ?>">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Lọc dữ liệu</button>
                    <a href="index.php" class="btn btn-outline-secondary w-100 rounded-pill">Xóa lọc</a>
                </div>
            </form>

            <form method="POST" action="">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover bang-du-lieu align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th width="40"><input type="checkbox" onclick="var checkboxes = document.getElementsByName('selected_ids[]'); for(var i in checkboxes) checkboxes[i].checked = this.checked;"></th>
                                <th width="100">Mã ID</th>
                                <th class="text-start">Tên danh mục sách</th>
                                <th>Số lượng sách</th>
                                <th width="220">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($categories_list) > 0) { ?>
                                <?php foreach ($categories_list as $cat) { ?>
                                    <tr class="text-center">
                                        <td>
                                            <input type="checkbox" name="selected_ids[]" value="<?php echo $cat['category_id']; ?>">
                                        </td>
                                        <td>#<?php echo $cat['category_id']; ?></td>
                                        <td class="text-start">
                                            <div class="fw-bold text-dark"><?php echo $cat['category_name']; ?></div>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill bg-info text-dark px-3 py-2">
                                                <i class="fas fa-book me-1"></i> <?php echo $cat['book_count']; ?> quyển sách
                                            </span>
                                        </td>
                                        <td>
                                            <a href="edit.php?category_id=<?php echo $cat['category_id']; ?>" class="btn btn-info text-white nut-hanh-dong">
                                                <i class="fas fa-edit"></i> Sửa
                                            </a>
                                            <a href="delete.php?category_id=<?php echo $cat['category_id']; ?>" class="btn btn-danger nut-hanh-dong">
                                                <i class="fas fa-trash"></i> Xóa
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">Dữ liệu trống.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <button type="submit" name="delete_selected" class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="return confirm('Xóa những mục đã chọn?')">
                        <i class="fas fa-trash-alt"></i> Xóa mục đã chọn
                    </button>

                    <?php if ($total_pages > 1) { ?>
                        <ul class="pagination pagination-sm mb-0">
                            <?php for($i = 1; $i <= $total_pages; $i++) { ?>
                                <li class="page-item <?php if($i == $page) { echo 'active'; } ?>">
                                    <a class="page-link shadow-none" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
</div>

<?php admin_layout_end(); ?>
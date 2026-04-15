<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

// 1. Lấy dữ liệu lọc từ GET (Viết tường minh kiểu truyền thống)
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$category_id = '';
if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
}

$status = '';
if (isset($_GET['status'])) {
    $status = $_GET['status'];
}

// 2. Xây dựng câu lệnh WHERE (Nối chuỗi trực tiếp)
$where = " WHERE 1=1 ";
if ($search != '') {
    $where = $where . " AND (b.title LIKE '%$search%' OR b.author LIKE '%$search%') ";
}

if ($category_id != '') {
    $where = $where . " AND b.category_id = '$category_id' ";
}

if ($status != '') {
    if ($status == 'active') {
        $where = $where . " AND i.stock > 0 ";
    } else {
        if ($status == 'out_of_stock') {
            $where = $where . " AND (i.stock <= 0 OR i.stock IS NULL) ";
        }
    }
}

// 3. Tính toán phân trang
$page = 1;
if (isset($_GET['page'])) {
    $page = (int)$_GET['page'];
}
if ($page < 1) { $page = 1; }

$limit = 15;
$offset = ($page - 1) * $limit;

// 4. Lấy tổng số dòng để tính tổng số trang
$sql_count = "SELECT COUNT(*) as total FROM books b LEFT JOIN inventory i ON b.book_id = i.book_id " . $where;
$res_count = $conn->query($sql_count);
$row_count = $res_count->fetch_assoc();
$total_books = $row_count['total'];
$total_pages = ceil($total_books / $limit);

// 5. Lấy danh sách sách (Query trần, nối biến trực tiếp)
$sql_main = "SELECT b.*, c.category_name, i.stock 
             FROM books b 
             LEFT JOIN categories c ON b.category_id = c.category_id 
             LEFT JOIN inventory i ON b.book_id = i.book_id 
             $where 
             ORDER BY b.book_id ASC 
             LIMIT $limit OFFSET $offset";

$res_main = $conn->query($sql_main);

// Đưa dữ liệu vào mảng để dùng foreach
$books_list = array();
if ($res_main) {
    while ($row = $res_main->fetch_assoc()) {
        $books_list[] = $row;
    }
}

// Lấy danh mục cho bộ lọc
$res_categories = $conn->query("SELECT * FROM categories ORDER BY category_name");
$categories_data = array();
if ($res_categories) {
    while ($cat_row = $res_categories->fetch_assoc()) {
        $categories_data[] = $cat_row;
    }
}

admin_layout_start('Quản lý sách', 'books');
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
    
    .anh-sach-nho {
        width: 45px;
        height: 60px;
        object-fit: cover;
        border-radius: 5px;
        border: 1px solid #eee;
    }
    
    .gia-goc { text-decoration: line-through; color: #888; font-size: 0.85rem; }
    .gia-moi { color: #d9534f; font-weight: bold; }
    .badge-tron { border-radius: 20px; padding: 6px 12px; }
</style>

<div class="container-fluid">
    
    <?php if (isset($_SESSION['success'])) { ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php } ?>

    <div class="card the-bang border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 text-primary fw-bold">QUẢN LÝ KHO SÁCH</h5>
                <p class="mb-0 small text-muted">Hiện có <?php echo $total_books; ?> cuốn sách trong hệ thống</p>
            </div>
            <a href="create.php" class="btn btn-success rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus-circle me-1"></i> Thêm sách mới
            </a>
        </div>

        <div class="card-body">
            <form method="GET" action="" class="row g-2 mb-4">
                <div class="col-md-4">
                    <input type="text" class="form-control px-3" name="search" placeholder="Tên sách, tác giả..." value="<?php echo $search; ?>">
                </div>
                <div class="col-md-2">
                    <select name="category_id" class="form-select">
                        <option value="">-- Danh mục --</option>
                        <?php foreach ($categories_data as $cat) { ?>
                            <option value="<?php echo $cat['category_id']; ?>" <?php if($cat['category_id'] == $category_id) echo 'selected'; ?>>
                                <?php echo $cat['category_name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">-- Trạng thái --</option>
                        <option value="active" <?php if($status == 'active') echo 'selected'; ?>>Còn hàng</option>
                        <option value="out_of_stock" <?php if($status == 'out_of_stock') echo 'selected'; ?>>Hết hàng</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Lọc</button>
                </div>
                <div class="col-md-2">
                    <a href="index.php" class="btn btn-outline-secondary w-100 rounded-pill">Xóa lọc</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover bang-du-lieu align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="60">ID</th>
                            <th width="80">Ảnh</th>
                            <th class="text-start">Thông tin sách</th>
                            <th>Danh mục</th>
                            <th>Giá bán</th>
                            <th>Tồn kho</th>
                            <th width="250">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($books_list) > 0) { ?>
                            <?php foreach ($books_list as $b) { ?>
                                <tr class="text-center">
                                    <td class="text-muted">#<?php echo $b['book_id']; ?></td>
                                    <td>
                                        <?php 
                                            $img = $b['link_images'];
                                            if ($img == '') { $img = 'https://via.placeholder.com/60x80?text=No+Image'; }
                                        ?>
                                        <img src="<?php echo $img; ?>" class="anh-sach-nho shadow-sm">
                                    </td>
                                    <td class="text-start">
                                        <div class="fw-bold text-dark"><?php echo $b['title']; ?></div>
                                        <small class="text-muted">Tác giả: <?php echo $b['author']; ?></small>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-light text-dark border px-2 py-1">
                                            <?php echo $b['category_name']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($b['discount'] > 0) { ?>
                                            <div class="gia-goc"><?php echo number_format($b['price']); ?>đ</div>
                                            <?php $gia_giam = $b['price'] - ($b['price'] * $b['discount'] / 100); ?>
                                            <div class="gia-moi text-danger"><?php echo number_format($gia_giam); ?>đ</div>
                                        <?php } else { ?>
                                            <div class="fw-bold"><?php echo number_format($b['price']); ?>đ</div>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php $sl = (int)$b['stock']; ?>
                                        <span class="badge badge-tron <?php if($sl > 0) { echo 'bg-info text-dark'; } else { echo 'bg-danger text-white'; } ?>">
                                            <?php echo $sl; ?> cuốn
                                        </span>
                                    </td>
                                    <td>
                                        <a href="edit.php?id=<?php echo $b['book_id']; ?>" class="btn btn-info text-white nut-hanh-dong">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <a href="delete.php?id=<?php echo $b['book_id']; ?>" class="btn btn-danger nut-hanh-dong" onclick="return confirm('Xác nhận xóa sách này?')">
                                            <i class="fas fa-trash"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted">Không tìm thấy cuốn sách nào.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1) { ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                            <li class="page-item <?php if($i == $page) { echo 'active'; } ?>">
                                <a class="page-link shadow-none" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&category_id=<?php echo $category_id; ?>&status=<?php echo $status; ?>">
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
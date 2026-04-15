<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

// 1. Lấy dữ liệu lọc từ GET (Viết kiểu tường minh từng biến)
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

$min_price = '';
if (isset($_GET['min_price'])) {
    $min_price = $_GET['min_price'];
}

$max_price = '';
if (isset($_GET['max_price'])) {
    $max_price = $_GET['max_price'];
}

// 2. Xây dựng câu lệnh WHERE (Nối chuỗi trực tiếp)
$where = " WHERE 1=1 ";

if ($search != '') {
    $where = $where . " AND (b.title LIKE '%$search%' OR b.author LIKE '%$search%') ";
}

if ($category_id != '') {
    $where = $where . " AND b.category_id = '$category_id' ";
}

if ($min_price != '') {
    $where = $where . " AND b.price >= '$min_price' ";
}

if ($max_price != '') {
    $where = $where . " AND b.price <= '$max_price' ";
}

// Lọc trạng thái dựa trên bảng inventory (i)
if ($status != '') {
    if ($status == 'active') {
        $where = $where . " AND i.stock > 0 ";
    } else {
        if ($status == 'out_of_stock') {
            $where = $where . " AND (i.stock <= 0 OR i.stock IS NULL) ";
        }
    }
}

// 3. Thực thi truy vấn (Query trần)
$sql = "SELECT b.*, c.category_name, i.stock 
        FROM books b 
        LEFT JOIN categories c ON b.category_id = c.category_id 
        LEFT JOIN inventory i ON b.book_id = i.book_id 
        $where 
        ORDER BY b.created_at DESC";

$res_main = $conn->query($sql);

// Đưa kết quả vào mảng để dùng foreach hiển thị
$books_list = array();
if ($res_main) {
    while ($row = $res_main->fetch_assoc()) {
        $books_list[] = $row;
    }
}

// Lấy số lượng kết quả
$total_results = count($books_list);

// Lấy danh mục cho bộ lọc
$res_categories = $conn->query("SELECT * FROM categories ORDER BY category_name");
$categories_data = array();
if ($res_categories) {
    while ($cat_row = $res_categories->fetch_assoc()) {
        $categories_data[] = $cat_row;
    }
}

admin_layout_start('Tìm kiếm nâng cao', 'books');
?>

<style>
    .khung-tim-kiem {
        background-color: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 30px;
    }
    .form-control, .form-select {
        border-radius: 8px;
    }
    .anh-sach-nho {
        border: 1px solid #ddd;
        border-radius: 4px;
        object-fit: cover;
    }
    .nut-hanh-dong {
        border-radius: 20px !important;
        padding: 5px 15px !important;
        margin: 0 3px;
        font-size: 0.85rem;
        display: inline-block;
        text-decoration: none;
    }
    .nut-tim-ngay {
        border-radius: 20px !important;
        padding: 10px 30px !important;
        font-weight: bold;
    }
</style>

<div class="container-fluid">
    <div class="khung-tim-kiem shadow-sm">
        <h5 class="mb-4 text-primary fw-bold"><i class="fas fa-search-plus"></i> CÔNG CỤ TÌM KIẾM NÂNG CAO</h5>
        <form method="GET" action="search.php">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="fw-bold mb-1 small">Từ khóa cần tìm</label>
                    <input type="text" class="form-control" name="search" value="<?php echo $search; ?>" placeholder="Tên sách, tác giả...">
                </div>
                <div class="col-md-2">
                    <label class="fw-bold mb-1 small">Danh mục</label>
                    <select class="form-select" name="category_id">
                        <option value="">-- Tất cả --</option>
                        <?php foreach ($categories_data as $cat) { ?>
                            <option value="<?php echo $cat['category_id']; ?>" <?php if($cat['category_id'] == $category_id) { echo 'selected'; } ?>>
                                <?php echo $cat['category_name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="fw-bold mb-1 small">Tình trạng kho</label>
                    <select class="form-select" name="status">
                        <option value="">-- Tất cả --</option>
                        <option value="active" <?php if($status == 'active') { echo 'selected'; } ?>>Còn hàng</option>
                        <option value="out_of_stock" <?php if($status == 'out_of_stock') { echo 'selected'; } ?>>Hết hàng</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="fw-bold mb-1 small">Giá thấp nhất</label>
                    <input type="number" class="form-control" name="min_price" value="<?php echo $min_price; ?>" placeholder="0">
                </div>
                <div class="col-md-2">
                    <label class="fw-bold mb-1 small">Giá cao nhất</label>
                    <input type="number" class="form-control" name="max_price" value="<?php echo $max_price; ?>" placeholder="999.000">
                </div>
                <div class="col-12 text-end mt-4">
                    <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4 me-2">Quay lại</a>
                    <button type="submit" class="btn btn-primary nut-tim-ngay shadow"><i class="fas fa-filter"></i> LỌC KẾT QUẢ</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">KẾT QUẢ TÌM THẤY (<?php echo $total_results; ?>)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle border">
                    <thead class="table-light">
                        <tr>
                            <th width="60">ID</th>
                            <th>Thông tin sách</th>
                            <th>Danh mục</th>
                            <th>Giá niêm yết</th>
                            <th>Tồn kho</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_results > 0) { ?>
                            <?php foreach ($books_list as $b) { ?>
                                <tr>
                                    <td><?php echo $b['book_id']; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php 
                                                $anh = $b['link_images'];
                                                if ($anh == '') { $anh = 'https://via.placeholder.com/45x60?text=Sách'; }
                                            ?>
                                            <img src="<?php echo $anh; ?>" class="anh-sach-nho me-3" width="45" height="60">
                                            <div>
                                                <div class="fw-bold"><?php echo $b['title']; ?></div>
                                                <small class="text-muted">Tác giả: <?php echo $b['author']; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?php echo $b['category_name']; ?></span></td>
                                    <td><strong class="text-primary"><?php echo number_format($b['price']); ?> đ</strong></td>
                                    <td>
                                        <?php 
                                            $ton = (int)$b['stock']; 
                                            if ($ton > 0) {
                                                echo '<span class="text-success fw-bold small">● Còn ' . $ton . ' cuốn</span>';
                                            } else {
                                                echo '<span class="text-danger fw-bold small">○ Hết hàng</span>';
                                            }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="edit.php?id=<?php echo $b['book_id']; ?>" class="btn btn-info text-white nut-hanh-dong">Sửa</a>
                                        <a href="delete.php?id=<?php echo $b['book_id']; ?>" class="btn btn-danger nut-hanh-dong" onclick="return confirm('Xác nhận xóa?')">Xóa</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-search-minus fa-3x mb-3"></i>
                                        <p>Rất tiếc, không tìm thấy kết quả nào khớp với yêu cầu.</p>
                                        <a href="search.php" class="btn btn-sm btn-link">Tải lại trang</a>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php admin_layout_end(); ?>
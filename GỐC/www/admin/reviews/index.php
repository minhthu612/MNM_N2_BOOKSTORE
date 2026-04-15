<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

// 1. Lấy dữ liệu lọc từ GET (Viết kiểu tường minh từng biến)
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$rating_filter = '';
if (isset($_GET['rating'])) {
    $rating_filter = $_GET['rating'];
}

$book_id = '';
if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];
}

$user_id = '';
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
}

$date_from = '';
if (isset($_GET['date_from'])) {
    $date_from = $_GET['date_from'];
}

$date_to = '';
if (isset($_GET['date_to'])) {
    $date_to = $_GET['date_to'];
}

// 2. Xây dựng câu lệnh WHERE (Nối chuỗi trực tiếp kiểu SV)
$where_sql = " WHERE 1=1 ";

if ($search != '') {
    $where_sql = $where_sql . " AND (r.comment LIKE '%$search%' OR b.title LIKE '%$search%' OR u.username LIKE '%$search%') ";
}

if ($rating_filter != '') {
    $where_sql = $where_sql . " AND r.rating = '$rating_filter' ";
}

if ($book_id != '') {
    $where_sql = $where_sql . " AND r.book_id = '$book_id' ";
}

if ($user_id != '') {
    $where_sql = $where_sql . " AND r.user_id = '$user_id' ";
}

if ($date_from != '') {
    $where_sql = $where_sql . " AND DATE(r.created_at) >= '$date_from' ";
}

if ($date_to != '') {
    $where_sql = $where_sql . " AND DATE(r.created_at) <= '$date_to' ";
}

// 3. Tính toán phân trang
$page = 1;
if (isset($_GET['page'])) {
    $page = (int)$_GET['page'];
}
if ($page < 1) { $page = 1; }

$limit = 15;
$offset = ($page - 1) * $limit;

// 4. Lấy tổng số dòng để tính trang (Query trần)
$sql_count = "SELECT COUNT(*) as total FROM reviews r 
              LEFT JOIN books b ON r.book_id = b.book_id 
              LEFT JOIN users u ON r.user_id = u.user_id " . $where_sql;
$res_count = $conn->query($sql_count);
$row_count = $res_count->fetch_assoc();
$total = $row_count['total'];
$total_pages = ceil($total / $limit);

// 5. Lấy danh sách đánh giá (Query trần nối biến trực tiếp)
$sql_main = "SELECT r.*, b.title as book_title, b.author, b.link_images, 
                    u.username, u.fullname, u.email
             FROM reviews r
             LEFT JOIN books b ON r.book_id = b.book_id
             LEFT JOIN users u ON r.user_id = u.user_id
             $where_sql 
             ORDER BY r.created_at DESC 
             LIMIT $limit OFFSET $offset";

$res_main = $conn->query($sql_main);
$reviews_list = array();
if ($res_main) {
    while ($row = $res_main->fetch_assoc()) {
        $reviews_list[] = $row;
    }
}

// 6. Lấy thống kê nhanh
$stats_sql = "SELECT 
        COUNT(*) as total_reviews,
        AVG(rating) as avg_rating,
        SUM(CASE WHEN rating >= 4 THEN 1 ELSE 0 END) as positive,
        SUM(CASE WHEN rating <= 2 THEN 1 ELSE 0 END) as negative
        FROM reviews";
$stats = $conn->query($stats_sql)->fetch_assoc();

// 7. Lấy dữ liệu cho các thẻ Select (Filter)
$books_list = $conn->query("SELECT book_id, title FROM books ORDER BY title LIMIT 50");
$users_list = $conn->query("SELECT user_id, username FROM users WHERE role = 'Customer' ORDER BY username LIMIT 50");

admin_layout_start("Quản lý đánh giá", 'reviews');
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
    .anh-sach-mini { width: 40px; height: 55px; object-fit: cover; border-radius: 4px; }
</style>

<div class="container-fluid">
    <div class="row g-3 mb-4 text-center">
        <div class="col-md-3">
            <div class="card stat-box bg-primary text-white p-3">
                <h3 class="mb-0 fw-bold"><?php echo $stats['total_reviews']; ?></h3>
                <div class="small">Tổng đánh giá</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-box bg-warning text-dark p-3">
                <h3 class="mb-0 fw-bold"><?php echo number_format($stats['avg_rating'], 1); ?> ★</h3>
                <div class="small">Điểm trung bình</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-box bg-success text-white p-3">
                <h3 class="mb-0 fw-bold"><?php echo $stats['positive']; ?></h3>
                <div class="small">Đánh giá tốt (4-5★)</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-box bg-danger text-white p-3">
                <h3 class="mb-0 fw-bold"><?php echo $stats['negative']; ?></h3>
                <div class="small">Đánh giá tệ (1-2★)</div>
            </div>
        </div>
    </div>

    <div class="card the-bang border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-2">
                <div class="col-md-4">
                    <input type="text" class="form-control px-3" name="search" placeholder="Nội dung, tên sách, người dùng..." value="<?php echo $search; ?>">
                </div>
                <div class="col-md-2">
                    <select name="rating" class="form-select">
                        <option value="">-- Mức sao --</option>
                        <?php for($i=5; $i>=1; $i--) { ?>
                            <option value="<?php echo $i; ?>" <?php if($rating_filter == $i) echo 'selected'; ?>><?php echo $i; ?> Sao</option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" value="<?php echo $date_from; ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Lọc</button>
                </div>
                <div class="col-md-2">
                    <a href="index.php" class="btn btn-outline-secondary w-100 rounded-pill">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card the-bang border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 text-primary fw-bold"><i class="fas fa-comments me-2"></i>DANH SÁCH PHẢN HỒI</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="200" class="text-start">Sách</th>
                            <th width="150">Người gửi</th>
                            <th width="120">Điểm</th>
                            <th class="text-start">Nội dung bình luận</th>
                            <th width="120">Ngày đăng</th>
                            <th width="260">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($reviews_list) > 0) { ?>
                            <?php foreach ($reviews_list as $r) { ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $r['link_images']; ?>" class="anh-sach-mini me-2 shadow-sm">
                                            <div class="small fw-bold text-truncate" style="max-width: 130px;"><?php echo $r['book_title']; ?></div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-bold small"><?php echo ($r['fullname'] != '' ? $r['fullname'] : $r['username']); ?></div>
                                        <div class="text-muted" style="font-size: 0.7rem;"><?php echo $r['email']; ?></div>
                                    </td>
                                    <td class="text-center">
                                        <div class="text-warning small mb-1">
                                            <?php for($i=1; $i<=5; $i++) {
                                                if($i <= $r['rating']) echo '<i class="fas fa-star"></i>';
                                                else echo '<i class="far fa-star"></i>';
                                            } ?>
                                        </div>
                                        <span class="badge rounded-pill bg-light text-dark border"><?php echo $r['rating']; ?>/5</span>
                                    </td>
                                    <td>
                                        <div class="small text-dark" style="max-height: 50px; overflow: hidden;"><?php echo $r['comment']; ?></div>
                                    </td>
                                    <td class="text-center">
                                        <div class="small"><?php echo date('d/m/y', strtotime($r['created_at'])); ?></div>
                                    </td>
                                    <td class="text-center">
                                        <a href="edit.php?id=<?php echo $r['review_id']; ?>" class="btn btn-info text-white nut-hanh-dong">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <a href="delete.php?id=<?php echo $r['review_id']; ?>" class="btn btn-danger nut-hanh-dong">
                                            <i class="fas fa-trash"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">Không tìm thấy đánh giá nào.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1) { ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                            <li class="page-item <?php if($i == $page) { echo 'active'; } ?>">
                                <a class="page-link shadow-none" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&rating=<?php echo $rating_filter; ?>">
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
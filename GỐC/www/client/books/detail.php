<?php
require_once '../../includes/client_check.php';
require_once '../../config.php';

// 1. LẤY ID TỪ URL KIỂU TRUYỀN THỐNG
$book_id = 0;
if (isset($_GET['id'])) {
    $book_id = (int)$_GET['id'];
}

if ($book_id == 0) {
    header("Location: ../../index.php");
    exit();
}

// 2. GHI NHẬN LƯỢT XEM (Dùng query trần)
$v_user_id = 'NULL';
if (isset($_SESSION['user_id'])) {
    $v_user_id = $_SESSION['user_id'];
}
$sql_view = "INSERT INTO book_views (user_id, book_id, viewed_at) VALUES ($v_user_id, $book_id, NOW())";
$conn->query($sql_view);

// 3. LẤY THÔNG TIN SÁCH VÀ TỒN KHO (Dùng query trần)
$sql_book = "SELECT b.*, c.category_name, i.stock 
             FROM books b 
             LEFT JOIN categories c ON b.category_id = c.category_id 
             LEFT JOIN inventory i ON b.book_id = i.book_id 
             WHERE b.book_id = '$book_id'";
$res_book = $conn->query($sql_book);
$book = $res_book->fetch_assoc();

if ($book == null) {
    header("Location: ../../index.php");
    exit();
}

// 4. LẤY TỔNG LƯỢT XEM
$sql_count_view = "SELECT COUNT(*) as total FROM book_views WHERE book_id = '$book_id'";
$res_count_view = $conn->query($sql_count_view);
$view_data = $res_count_view->fetch_assoc();
$total_views = 0;
if (isset($view_data['total'])) {
    $total_views = $view_data['total'];
}

// 5. LẤY THÔNG TIN ĐÁNH GIÁ TRUNG BÌNH
$sql_rating = "SELECT AVG(rating) as avg_r, COUNT(*) as total_r 
               FROM reviews WHERE book_id = '$book_id'";
$res_rating = $conn->query($sql_rating);
$rating_stats = $res_rating->fetch_assoc();

$avg_rating = 0;
if (isset($rating_stats['avg_r'])) {
    $avg_rating = round($rating_stats['avg_r'], 1);
}

$total_reviews = 0;
if (isset($rating_stats['total_r'])) {
    $total_reviews = $rating_stats['total_r'];
}

// 6. LẤY DANH SÁCH ĐÁNH GIÁ (Đổ vào mảng để dùng foreach)
$sql_reviews = "SELECT r.*, u.fullname, u.username 
                FROM reviews r 
                JOIN users u ON r.user_id = u.user_id 
                WHERE r.book_id = '$book_id' 
                ORDER BY r.created_at DESC";
$res_reviews = $conn->query($sql_reviews);
$list_reviews = array();
if ($res_reviews) {
    while ($row = $res_reviews->fetch_assoc()) {
        $list_reviews[] = $row;
    }
}

$current_stock = 0;
if (isset($book['stock'])) {
    $current_stock = (int)$book['stock'];
}

$page_title = $book['title'];
include '../../header.php'; 
?>

<style>
    .khung-chi-tiet { background: #fff; padding: 30px; border-radius: 15px; border: 1px solid #eee; }
    .anh-sach-lon { max-width: 100%; border-radius: 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    .gia-ban { font-size: 2rem; color: #e74c3c; font-weight: bold; }
    .gia-cu { text-decoration: line-through; color: #95a5a6; margin-left: 10px; }
    .nut-mua { border-radius: 25px !important; padding: 12px 40px !important; font-weight: bold; text-transform: uppercase; }
    .thong-tin-phu { color: #7f8c8d; font-size: 0.9rem; margin-bottom: 5px; }
    .o-nhap-sl { width: 80px; border-radius: 10px !important; text-align: center; font-weight: bold; border: 1px solid #ddd; }
    .vung-danh-gia { border-bottom: 1px solid #f1f1f1; padding: 15px 0; }
    .sao-vang { color: #f1c40f; }
</style>

<div class="container py-5">
    <nav class="mb-4">
        <a href="../../index.php" class="text-decoration-none text-muted">Trang chủ</a> / 
        <a href="../../index.php?category=<?php echo $book['category_id']; ?>" class="text-decoration-none text-muted"><?php echo $book['category_name']; ?></a> / 
        <span class="text-dark fw-bold"><?php echo $book['title']; ?></span>
    </nav>

    <div class="khung-chi-tiet shadow-sm">
        <div class="row g-5">
            <div class="col-md-5">
                <div class="text-center p-3 bg-light rounded-3">
                    <?php 
                        $img_path = $book['link_images'];
                        if ($img_path == '') { $img_path = '../../images/no-image.jpg'; }
                    ?>
                    <img src="<?php echo $img_path; ?>" class="anh-sach-lon" alt="Book Image">
                </div>
            </div>

            <div class="col-md-7">
                <div class="mb-2">
                    <span class="badge bg-info text-dark rounded-pill px-3"><?php echo $book['category_name']; ?></span>
                    <?php if ($current_stock > 0) { ?>
                        <span class="badge bg-success rounded-pill px-3">Còn hàng (<?php echo $current_stock; ?>)</span>
                    <?php } else { ?>
                        <span class="badge bg-danger rounded-pill px-3">Hết hàng</span>
                    <?php } ?>
                </div>

                <h1 class="fw-bold mb-3"><?php echo $book['title']; ?></h1>

                <div class="mb-3">
                    <span class="sao-vang">
                        <?php 
                        for($i=1; $i<=5; $i++) {
                            if($i <= floor($avg_rating)) { echo '<i class="fas fa-star"></i>'; }
                            else { echo '<i class="far fa-star"></i>'; }
                        }
                        ?>
                    </span>
                    <span class="text-muted ms-2">(<?php echo $total_reviews; ?> nhận xét)</span>
                </div>

                <div class="mb-4 pb-4 border-bottom">
                    <div class="thong-tin-phu">Tác giả: <b class="text-dark"><?php echo $book['author']; ?></b></div>
                    <div class="thong-tin-phu">Lượt xem: <b class="text-dark"><?php echo number_format($total_views); ?></b></div>
                    <div class="thong-tin-phu">Đã bán: <b class="text-success"><?php echo ($book['sold_quantity'] ? $book['sold_quantity'] : 0); ?> cuốn</b></div>
                </div>

                <div class="mb-4">
                    <?php 
                        $price = $book['price'];
                        $discount = $book['discount'];
                        $final_price = $price - ($price * $discount / 100);
                    ?>
                    <div class="gia-ban">
                        <?php echo number_format($final_price, 0, ',', '.'); ?>đ
                        <?php if ($discount > 0) { ?>
                            <span class="gia-cu"><?php echo number_format($price, 0, ',', '.'); ?>đ</span>
                            <span class="badge bg-danger ms-2">-<?php echo $discount; ?>%</span>
                        <?php } ?>
                    </div>
                </div>

                <?php if ($current_stock > 0) { ?>
                <form action="../cart/add.php" method="POST">
                    <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <label class="fw-bold">Số lượng:</label>
                        <input type="number" name="quantity" class="form-control o-nhap-sl" value="1" min="1" max="<?php echo $current_stock; ?>">
                        <span class="small text-muted">Có sẵn: <?php echo $current_stock; ?></span>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg nut-mua shadow w-100">
                            <i class="fas fa-cart-plus me-2"></i>Thêm vào giỏ hàng
                        </button>
                        
                        <?php if (isset($_SESSION['user_id'])) { ?>
                            <a href="../wishlist/add.php?book_id=<?php echo $book_id; ?>" class="btn btn-outline-danger btn-lg rounded-circle" style="width: 50px; height: 50px; padding: 10px;">
                                <i class="far fa-heart"></i>
                            </a>
                        <?php } ?>
                    </div>
                </form>
                <?php } ?>
            </div>
        </div>

        <div class="row mt-5 pt-5 border-top">
            <div class="col-md-8">
                <h5 class="fw-bold mb-4 text-primary">GIỚI THIỆU SÁCH</h5>
                <div class="text-muted" style="line-height: 1.8; text-align: justify;">
                    <?php 
                        if ($book['description'] != '') {
                            echo nl2br($book['description']);
                        } else {
                            echo "Đang cập nhật nội dung cho cuốn sách này...";
                        }
                    ?>
                </div>

                <h5 class="fw-bold mt-5 mb-4 text-primary">ĐÁNH GIÁ TỪ BẠN ĐỌC</h5>
                <?php if (count($list_reviews) > 0) { ?>
                    <?php foreach ($list_reviews as $rev) { ?>
                        <div class="vung-danh-gia">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <b class="text-dark"><?php echo ($rev['fullname'] != '' ? $rev['fullname'] : $rev['username']); ?></b>
                                <small class="text-muted"><?php echo date('d/m/Y', strtotime($rev['created_at'])); ?></small>
                            </div>
                            <div class="sao-vang mb-2" style="font-size: 0.8rem;">
                                <?php 
                                for($i=1; $i<=5; $i++) {
                                    if($i <= $rev['rating']) { echo '<i class="fas fa-star"></i>'; }
                                    else { echo '<i class="far fa-star"></i>'; }
                                }
                                ?>
                            </div>
                            <p class="small text-secondary mb-0"><?php echo nl2br($rev['comment']); ?></p>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="alert alert-light border text-center py-4">
                        <p class="mb-0 text-muted small">Cuốn sách này chưa có đánh giá nào. Hãy là người đầu tiên nhận xét!</p>
                    </div>
                <?php } ?>
            </div>

            <div class="col-md-4">
                <div class="card border-0 bg-light rounded-3">
                    <div class="card-body">
                        <h6 class="fw-bold"><i class="fas fa-truck me-2"></i>CHÍNH SÁCH GIAO HÀNG</h6>
                        <ul class="small text-muted ps-3 mt-3">
                            <li>Giao hàng nhanh trong 24h.</li>
                            <li>Đổi trả sách trong vòng 7 ngày.</li>
                            <li>Kiểm tra hàng trước khi thanh toán.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../footer.php'; ?>
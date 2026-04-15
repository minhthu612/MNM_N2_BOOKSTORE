<?php
require_once '../../includes/client_check.php';
require_once '../../config.php';

// 1. LẤY ID TỪ URL KIỂU TRUYỀN THỐNG
$set_id = 0;
if (isset($_GET['id'])) {
    $set_id = (int)$_GET['id'];
}

if ($set_id == 0) {
    header("Location: ../../index.php");
    exit();
}

// 2. GHI NHẬN LƯỢT XEM (Dùng query trần nối chuỗi)
$v_user_id = 'NULL';
if (isset($_SESSION['user_id'])) {
    $v_user_id = $_SESSION['user_id'];
}
$sql_view = "INSERT INTO book_views (user_id, book_id, viewed_at) VALUES ($v_user_id, $set_id, NOW())";
$conn->query($sql_view);

// 3. LẤY THÔNG TIN BỘ SÁCH VÀ TỒN KHO
$sql_set = "SELECT bs.*, i.stock 
            FROM book_sets bs 
            LEFT JOIN inventory i ON bs.set_id = i.book_id 
            WHERE bs.set_id = '$set_id'";
$res_set = $conn->query($sql_set);
$set = $res_set->fetch_assoc();

if ($set == null) {
    header("Location: ../../index.php");
    exit();
}

// 4. LẤY TỔNG LƯỢT XEM
$sql_count_view = "SELECT COUNT(*) as total FROM book_views WHERE book_id = '$set_id'";
$res_count_view = $conn->query($sql_count_view);
$view_row = $res_count_view->fetch_assoc();
$total_views = 0;
if (isset($view_row['total'])) {
    $total_views = $view_row['total'];
}

// 5. LẤY DANH SÁCH SÁCH TRONG BỘ (Đổ vào mảng để dùng foreach)
$sql_items = "SELECT b.* FROM books b 
              JOIN book_set_items bsi ON b.book_id = bsi.book_id 
              WHERE bsi.set_id = '$set_id'";
$res_items = $conn->query($sql_items);
$list_items = array();
if ($res_items) {
    while ($row_item = $res_items->fetch_assoc()) {
        $list_items[] = $row_item;
    }
}

// 6. LẤY DANH SÁCH ĐÁNH GIÁ (Đổ vào mảng để dùng foreach)
$sql_reviews = "SELECT r.*, u.fullname, u.username 
                FROM reviews r 
                JOIN users u ON r.user_id = u.user_id 
                WHERE r.book_id = '$set_id' 
                ORDER BY r.created_at DESC";
$res_reviews = $conn->query($sql_reviews);
$list_reviews = array();
if ($res_reviews) {
    while ($row_rev = $res_reviews->fetch_assoc()) {
        $list_reviews[] = $row_rev;
    }
}

$current_stock = 0;
if (isset($set['stock'])) {
    $current_stock = (int)$set['stock'];
}

$page_title = $set['name'];
include '../../header.php'; 
?>

<style>
    .khung-combo { background: #fff; padding: 30px; border-radius: 15px; border: 1px solid #eee; }
    .anh-combo-lon { max-width: 100%; border-radius: 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    .gia-combo { font-size: 2.2rem; color: #d9534f; font-weight: bold; }
    .item-nho { border: 1px solid #f1f1f1; border-radius: 10px; padding: 10px; margin-bottom: 10px; background: #fafafa; }
    .nut-dat-mua { border-radius: 25px !important; padding: 12px 40px !important; font-weight: bold; text-transform: uppercase; }
    .o-sl { width: 80px; border-radius: 10px !important; text-align: center; font-weight: bold; }
    .vung-danh-gia { border-bottom: 1px solid #f5f5f5; padding: 15px 0; }
</style>

<div class="container py-5">
    <div class="mb-4">
        <a href="../../index.php" class="text-decoration-none text-muted">Trang chủ</a> / 
        <span class="text-dark fw-bold">Bộ sách: <?php echo $set['name']; ?></span>
    </div>

    <div class="khung-combo shadow-sm">
        <div class="row g-5">
            <div class="col-md-5">
                <div class="text-center p-3 bg-light rounded-3">
                    <?php 
                        $img = $set['link_images'];
                        if ($img == '') { $img = '../../images/no-image.jpg'; }
                    ?>
                    <img src="<?php echo $img; ?>" class="anh-combo-lon" alt="Combo Image">
                </div>
                
                <div class="mt-4 card border-0 bg-light rounded-3">
                    <div class="card-body">
                        <h6 class="fw-bold text-primary mb-3">SÁCH TRONG BỘ NÀY:</h6>
                        <?php foreach ($list_items as $item) { ?>
                            <div class="item-nho d-flex align-items-center gap-3">
                                <img src="<?php echo $item['link_images']; ?>" width="40" height="55" style="object-fit: cover; border-radius: 5px;">
                                <div class="small fw-bold text-dark"><?php echo $item['title']; ?></div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="mb-2">
                    <span class="badge bg-warning text-dark rounded-pill px-3">BỘ SÁCH TIẾT KIỆM</span>
                    <?php if ($current_stock > 0) { ?>
                        <span class="badge bg-success rounded-pill px-3">Sẵn có (<?php echo $current_stock; ?>)</span>
                    <?php } else { ?>
                        <span class="badge bg-danger rounded-pill px-3">Tạm hết hàng</span>
                    <?php } ?>
                </div>

                <h1 class="fw-bold mb-3"><?php echo $set['name']; ?></h1>

                <div class="mb-4 pb-4 border-bottom">
                    <div class="text-muted small">Lượt xem bộ sách: <b><?php echo number_format($total_views); ?></b></div>
                    <div class="text-muted small">Mã Combo: <b>#SET-<?php echo $set['set_id']; ?></b></div>
                </div>

                <div class="mb-4">
                    <?php 
                        $goc = $set['price'];
                        $giam = $set['discount'];
                        $cuoi = $goc - ($goc * $giam / 100);
                    ?>
                    <div class="gia-combo">
                        <?php echo number_format($cuoi, 0, ',', '.'); ?>đ
                        <?php if ($giam > 0) { ?>
                            <small class="text-muted text-decoration-line-through fs-5 ms-2"><?php echo number_format($goc, 0, ',', '.'); ?>đ</small>
                            <span class="badge bg-danger fs-6 ms-2">-<?php echo $giam; ?>%</span>
                        <?php } ?>
                    </div>
                    <div class="text-success small fw-bold mt-1"><i class="fas fa-check-circle"></i> Tiết kiệm hơn khi mua theo bộ</div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold">Mô tả bộ sản phẩm:</h6>
                    <div class="text-secondary small" style="text-align: justify;">
                        <?php echo nl2br($set['description']); ?>
                    </div>
                </div>

                <?php if ($current_stock > 0) { ?>
                <form action="../cart/add.php" method="POST">
                    <input type="hidden" name="set_id" value="<?php echo $set_id; ?>">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <label class="fw-bold">Số lượng bộ:</label>
                        <input type="number" name="quantity" class="form-control o-sl" value="1" min="1" max="<?php echo $current_stock; ?>">
                    </div>
                    
                    <div class="d-flex gap-2 w-100">
                        <form action="../cart/add_set.php" method="POST" class="w-100">
                            <input type="hidden" name="set_id" value="<?php echo $set_id; ?>">
                            
                            <button type="submit" class="btn btn-danger btn-lg nut-dat-mua shadow w-100">
                                <i class="fas fa-shopping-basket me-2"></i>MUA TRỌN BỘ NGAY
                            </button>
                        </form>
                    </div>
                </form>
                <?php } ?>
            </div>
        </div>

        <div class="row mt-5 pt-5 border-top">
            <div class="col-md-8">
                <h5 class="fw-bold mb-4 text-primary"><i class="fas fa-star text-warning me-2"></i>NHẬN XÉT TỪ KHÁCH HÀNG</h5>
                
                <?php if (count($list_reviews) > 0) { ?>
                    <?php foreach ($list_reviews as $rev) { ?>
                        <div class="vung-danh-gia">
                            <div class="d-flex justify-content-between mb-2">
                                <b class="text-dark"><?php echo ($rev['fullname'] != '' ? $rev['fullname'] : $rev['username']); ?></b>
                                <small class="text-muted"><?php echo date('d/m/Y', strtotime($rev['created_at'])); ?></small>
                            </div>
                            <div class="text-warning mb-2" style="font-size: 0.8rem;">
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
                        <p class="mb-0 text-muted small">Chưa có đánh giá nào cho combo này.</p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php include '../../footer.php'; ?>
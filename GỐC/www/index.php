<?php
include 'config.php';
$page_title = "Trang chủ";

// --- XỬ LÝ LOGIC LỌC DỮ LIỆU (Cách viết truyền thống) ---
$category_id = '';
if (isset($_GET['category'])) {
    $category_id = $_GET['category'];
}

$view_type = '';
if (isset($_GET['view'])) {
    $view_type = $_GET['view'];
}

$set_id = '';
if (isset($_GET['set_id'])) {
    $set_id = $_GET['set_id'];
}

$category_name = "Tất cả sách";

// Xác định tên danh mục hiển thị
if ($view_type == 'best_seller') {
    $category_name = "Sách bán chạy";
} else if ($category_id != '' && $category_id != 'all') {
    $cat_sql = "SELECT category_name FROM categories WHERE category_id = $category_id";
    $cat_result = mysqli_query($conn, $cat_sql);
    $cat_row = mysqli_fetch_assoc($cat_result);
    if ($cat_row) {
        $category_name = $cat_row['category_name'];
    }
} else if ($view_type == 'new') {
    $category_name = "Sách mới";
}

if ($set_id != '') {
    $set_info_sql = "SELECT name FROM book_sets WHERE set_id = $set_id";
    $set_info_res = mysqli_query($conn, $set_info_sql);
    $set_info_row = mysqli_fetch_assoc($set_info_res);
    if ($set_info_row) {
        $category_name = $set_info_row['name'];
    }
}

// --- LOGIC PHÂN TRANG ---
$items_per_page = 18;
$current_page = 1;
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $current_page = (int)$_GET['page'];
}
if ($current_page < 1) {
    $current_page = 1;
}
$offset = ($current_page - 1) * $items_per_page;

include 'header.php';
?>

<style>
    .wishlist-btn-fast {
        position: absolute; top: 10px; right: 10px; z-index: 10;
        width: 32px; height: 32px; background: rgba(255, 255, 255, 0.9);
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        color: #ff4757; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: all 0.3s ease; text-decoration: none !important;
    }
    .wishlist-btn-fast:hover { background: #ff4757; color: white; transform: scale(1.1); }
    .book-card { position: relative; border: none; transition: transform 0.3s; height: 100%; border: 1px solid #eee; border-radius: 10px; overflow: hidden; }
    .book-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .book-image { width:100%; height:180px; object-fit:contain; padding: 10px; }
    .text-truncate-2 {
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .discount-badge {
        position:absolute; top:10px; left:10px; background:#ff4757; color:white; 
        padding:2px 8px; border-radius:5px; font-weight:bold; font-size:0.8rem; z-index:5;
    }
</style>

<?php if ($category_id != '' || $view_type != '' || $set_id != '') { ?>
<div class="row mb-3 mt-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded border">
            <h4 class="mb-0 text-primary fw-bold">
                <i class="fas fa-book-reader me-2"></i> <?php echo $category_name; ?>
            </h4>
            <div class="d-flex align-items-center gap-2">
                <?php if ($category_name == "Sách giáo khoa" || $set_id != '') { ?>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-layer-group me-1"></i> Bộ SGK theo lớp
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><a class="dropdown-item" href="?category=<?php echo $category_id; ?>">-- Hiện tất cả sách lẻ --</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <?php
                        $dropdown_sql = "SELECT set_id, name FROM book_sets WHERE stock_status != 'DELETED' ORDER BY set_id ASC";
                        $dropdown_res = mysqli_query($conn, $dropdown_sql);
                        
                        // Chuyển sang mảng để dùng foreach
                        $all_sets = [];
                        while ($row_s = mysqli_fetch_assoc($dropdown_res)) {
                            $all_sets[] = $row_s;
                        }

                        foreach ($all_sets as $d_set) { ?>
                        <li>
                            <a class="dropdown-item" href="?category=<?php echo $category_id; ?>&set_id=<?php echo $d_set['set_id']; ?>">
                                <?php echo $d_set['name']; ?>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
                <?php } ?>
                <a href="index.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Xóa lọc</a>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<div class="row mt-4">
    <?php
    // --- XÂY DỰNG TRUY VẤN SQL ---
    if ($set_id != '') {
        $sql_chinh = "SELECT set_id as book_id, name as title, link_images, price, discount, sold_quantity, 'set' as loai, '' as author FROM book_sets WHERE set_id = $set_id";
        $sql_dem = "SELECT 1 as total";
    } else {
        $where = "WHERE 1=1";
        if ($category_id != '' && $category_id != 'all') {
            $where = $where . " AND category_id = $category_id";
        }
        
        $order = "ORDER BY sold_quantity DESC";
        if ($view_type == 'new') {
            $order = "ORDER BY created_at DESC";
        }

        $sql_chinh = "SELECT *, 'single' as loai FROM books $where $order";
        $sql_dem = "SELECT COUNT(*) as total FROM books $where";
    }

    // Tính tổng trang
    $res_dem = mysqli_query($conn, $sql_dem);
    $row_dem = mysqli_fetch_assoc($res_dem);
    $total_items = (isset($row_dem['total'])) ? $row_dem['total'] : 0;
    $total_pages = ceil($total_items / $items_per_page);

    // Lấy dữ liệu trang hiện tại
    $sql_final = $sql_chinh . " LIMIT $items_per_page OFFSET $offset";
    $result = mysqli_query($conn, $sql_final);

    // Đổ dữ liệu vào mảng để dùng FOREACH theo yêu cầu
    $books_list = [];
    while ($row_b = mysqli_fetch_assoc($result)) {
        $books_list[] = $row_b;
    }

    if (count($books_list) > 0) {
        foreach ($books_list as $book) {
            $gia_gốc = $book['price'];
            $phần_trăm_giảm = $book['discount'];
            $gia_giam = $gia_gốc * (100 - $phần_trăm_giảm) / 100;
            
            $b_id = $book['book_id'];
            $is_set = ($book['loai'] == 'set');
            
            // Xử lý ảnh lỗi (Thay thế logic JS onerror bằng PHP đơn giản hơn)
            $anh_hien_thi = $book['link_images'];
            if ($anh_hien_thi == '') {
                $anh_hien_thi = 'images/no-image.jpg';
            }
    ?>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card book-card shadow-sm">
                    
                    <?php if (isset($_SESSION['user_id'])) { ?>
                        <a href="client/wishlist/add.php?book_id=<?php echo $b_id; ?>" class="wishlist-btn-fast">
                            <i class="far fa-heart"></i>
                        </a>
                    <?php } ?>

                    <?php if ($phần_trăm_giảm > 0) { ?>
                        <div class="discount-badge">-<?php echo (int)$phần_trăm_giảm; ?>%</div>
                    <?php } ?>

                    <div class="p-2 text-center">
                        <img src="<?php echo $anh_hien_thi; ?>" class="book-image rounded">
                    </div>

                    <div class="card-body p-3 d-flex flex-column">
                        <h6 class="book-title mb-2 fw-bold text-dark text-truncate-2" style="height: 2.5rem;">
                            <?php if ($is_set) { ?>
                                <span class="badge bg-primary mb-1">TRỌN BỘ</span><br>
                            <?php } ?>
                            <?php echo $book['title']; ?>
                        </h6>
                        
                        <p class="text-muted small mb-2">
                            <?php 
                                if (isset($book['author']) && $book['author'] != '') {
                                    echo $book['author'];
                                } else {
                                    echo "Nhiều tác giả";
                                }
                            ?>
                        </p>
                        
                        <div class="mt-auto mb-2">
                            <div class="text-danger fw-bold"><?php echo number_format($gia_giam, 0, ',', '.'); ?> đ</div>
                            <?php if ($phần_trăm_giảm > 0) { ?>
                                <div class="text-muted small text-decoration-line-through"><?php echo number_format($gia_gốc, 0, ',', '.'); ?> đ</div>
                            <?php } ?>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="fas fa-shopping-cart"></i> <?php echo $book['sold_quantity']; ?></small>
                            
                            <?php 
                            if (isset($_SESSION['user_id'])) { 
                                if ($is_set) {
                                    $link_chi_tiet = "client/book_sets/detail.php?id=$b_id";
                                } else {
                                    $link_chi_tiet = "client/books/detail.php?id=$b_id";
                                }
                            ?>
                                <a href="<?php echo $link_chi_tiet; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">Chi tiết</a>
                            <?php } else { ?>
                                <a href="auth/login.php?msg=require" class="btn btn-sm btn-outline-primary rounded-pill px-3">Chi tiết</a>
                            <?php } ?>
                        </div>
                    </div>

                    <?php if (isset($_SESSION['user_id'])) { ?>
                    <div class="card-footer bg-white border-top-0 pt-0 pb-3">
                        <?php
                            $action_cart = "client/cart/add.php";
                            $input_name = "book_id";
                            $btn_class = "btn-success";
                            $btn_text = "Thêm vào giỏ";
                            
                            if ($is_set) {
                                $action_cart = "client/cart/add_set.php";
                                $input_name = "set_id";
                                $btn_class = "btn-primary";
                                $btn_text = "Thêm trọn bộ";
                            }
                        ?>
                        <form action="<?php echo $action_cart; ?>" method="POST">
                            <input type="hidden" name="<?php echo $input_name; ?>" value="<?php echo $b_id; ?>">
                            <button type="submit" class="btn <?php echo $btn_class; ?> btn-sm w-100 rounded-pill shadow-sm">
                                <i class="fas fa-cart-plus"></i> <?php echo $btn_text; ?>
                            </button>
                        </form>
                    </div>
                    <?php } ?>
                </div>
            </div>
    <?php
        } // Kết thúc foreach
    } else {
        echo '<div class="col-12 text-center py-5"><h5 class="text-muted">Không có sách nào</h5></div>';
    }
    ?>
</div>

<?php if ($total_pages > 1) { ?>
<div class="row mt-4 mb-5">
    <div class="col-12">
        <nav>
            <ul class="pagination justify-content-center">
                <?php
                $current_params = $_GET;
                for ($i = 1; $i <= $total_pages; $i++) {
                    $current_params['page'] = $i;
                    // Tạo lại link dựa trên các tham số cũ
                    $query_string = http_build_query($current_params);
                ?>
                <li class="page-item <?php if ($i == $current_page) { echo 'active'; } ?>">
                    <a class="page-link shadow-sm mx-1 rounded" href="index.php?<?php echo $query_string; ?>"><?php echo $i; ?></a>
                </li>
                <?php } ?>
            </ul>
        </nav>
    </div>
</div>
<?php } ?>

<?php include 'footer.php'; ?>
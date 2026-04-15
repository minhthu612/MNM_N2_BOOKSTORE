<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include 'config.php';


    /* LẤY TỪ KHÓA */
    $keyword = '';
    if (isset($_GET['q'])) {
        $keyword = trim($_GET['q']);
    }


    $page_title = 'Kết quả tìm kiếm';
    if ($keyword != '') {
        $page_title = 'Kết quả tìm kiếm: ' . $keyword;
    }


    include 'header.php';
    ?>


    <style>
    .wishlist-btn-fast{
        position:absolute;
        top:10px;
        right:10px;
        z-index:10;
        width:32px;
        height:32px;
        background:rgba(255,255,255,.9);
        border-radius:50%;
        display:flex;
        align-items:center;
        justify-content:center;
        color:#ff4757;
        text-decoration:none;
    }
    .book-card{
        osition:relative;
        transition:transform .3s;
    }
    .book-card:hover{
        transform:translateY(-5px);
    }
    .text-truncate-2{
        display:-webkit-box;
        -webkit-line-clamp:2;
        -webkit-box-orient:vertical;
        overflow:hidden;
    }
    </style>


    <div class="container py-4">


        <div class="border-bottom mb-4 pb-2">
            <h4 class="fw-bold text-primary">
                <i class="fas fa-search me-2"></i>
                Kết quả tìm kiếm:
                <span class="text-dark"><?php echo $keyword; ?></span>
            </h4>
        </div>


    <?php
    if ($keyword != '') {


        $keyword_safe = $conn->real_escape_string($keyword);


        $sql = "
            SELECT b.*, i.stock
            FROM books b
            LEFT JOIN inventory i ON b.book_id = i.book_id
            WHERE b.title LIKE '%$keyword_safe%'
            OR b.author LIKE '%$keyword_safe%'
            ORDER BY b.sold_quantity DESC
        ";


        $result = $conn->query($sql);
        $total = $result->num_rows;


        if ($total > 0) {
            echo '<p class="text-muted small mb-4">Tìm thấy <b>'.$total.'</b> kết quả.</p>';
            echo '<div class="row">';


            foreach ($result as $book) {


                $discount_price = $book['price'] * (100 - $book['discount']) / 100;
                $out_stock = ($book['stock'] <= 0);
                $book_id = $book['book_id'];
    ?>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card book-card h-100 shadow-sm">


                    <?php if (isset($_SESSION['user_id'])) { ?>
                        <a href="client/wishlist/add.php?book_id=<?php echo $book_id; ?>" class="wishlist-btn-fast">
                            <i class="far fa-heart"></i>
                        </a>
                    <?php } ?>


                    <?php if ($book['discount'] > 0) { ?>
                        <div class="discount-badge position-absolute top-0 start-0 m-2 bg-danger text-white px-2 rounded small">
                            -<?php echo $book['discount']; ?>%
                        </div>
                    <?php } ?>


                    <div class="p-2 text-center">
                        <img src="<?php echo $book['link_images'] != '' ? $book['link_images'] : 'images/no-image.jpg'; ?>"
                            style="width:100%;height:180px;object-fit:contain;">
                    </div>


                    <div class="card-body d-flex flex-column">
                        <h6 class="fw-bold text-truncate-2 mb-2" style="height:2.5rem">
                            <?php echo $book['title']; ?>
                        </h6>
                        <p class="text-muted small mb-2">
                            <?php echo $book['author'] != '' ? $book['author'] : 'Nhiều tác giả'; ?>
                        </p>


                        <div class="mt-auto mb-2">
                            <div class="text-danger fw-bold">
                                <?php echo number_format($discount_price,0,',','.'); ?> đ
                            </div>
                            <?php if ($book['discount'] > 0) { ?>
                                <div class="text-muted small text-decoration-line-through">
                                    <?php echo number_format($book['price'],0,',','.'); ?> đ
                                </div>
                            <?php } ?>
                        </div>


                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">
                                <i class="fas fa-shopping-cart"></i> <?php echo $book['sold_quantity']; ?>
                            </small>


                            <?php if (isset($_SESSION['user_id'])) { ?>
                                <a href="client/books/detail.php?id=<?php echo $book_id; ?>"
                                class="btn btn-sm btn-outline-primary rounded-pill">
                                    Chi tiết
                                </a>
                            <?php } else { ?>
                                <a href="auth/login.php"
                                class="btn btn-sm btn-outline-primary rounded-pill">
                                    Chi tiết
                                </a>
                            <?php } ?>
                        </div>
                    </div>


                    <div class="card-footer bg-white border-0 pt-0 pb-3">
                        <?php if ($out_stock) { ?>
                            <button class="btn btn-secondary btn-sm w-100 rounded-pill" disabled>
                                Hết hàng
                            </button>
                        <?php } else if (isset($_SESSION['user_id'])) { ?>
                            <form action="client/cart/add.php" method="POST">
                                <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                                <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill">
                                    <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                                </button>
                            </form>
                        <?php } else { ?>
                            <a href="auth/login.php" class="btn btn-outline-secondary btn-sm w-100 rounded-pill">
                                Đăng nhập để mua
                            </a>
                        <?php } ?>
                    </div>


                </div>
            </div>
    <?php
            }
            echo '</div>';
        } else {
    ?>
            <div class="text-center py-5">
                <h5 class="text-muted">Không tìm thấy sách phù hợp</h5>
                <a href="index.php" class="btn btn-primary rounded-pill mt-3">Về trang chủ</a>
            </div>
    <?php
        }


    } else {
        echo '<div class="alert alert-warning text-center">Vui lòng nhập từ khóa tìm kiếm</div>';
    }
?>


</div>


<?php include 'footer.php'; ?>




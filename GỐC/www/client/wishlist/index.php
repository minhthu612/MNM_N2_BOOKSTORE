<?php
require_once '../../includes/client_check.php';
require_once '../../config.php';

$page_title = "Sách tôi yêu thích";
include '../../header.php';

$user_id = $_SESSION['user_id'];

// 1. TRUY VẤN DỮ LIỆU (Dùng LEFT JOIN trần nối chuỗi trực tiếp)
$sql = "SELECT w.wishlist_id, 
               b.book_id as b_id, b.title as b_title, b.link_images as b_img, b.price as b_price, b.discount as b_disc,
               bs.set_id as s_id, bs.name as s_title, bs.link_images as s_img, bs.price as s_price, bs.discount as s_disc
        FROM wishlist w
        LEFT JOIN books b ON w.book_id = b.book_id
        LEFT JOIN book_sets bs ON w.book_id = bs.set_id
        WHERE w.user_id = '$user_id'
        ORDER BY w.wishlist_id DESC";

$res = $conn->query($sql);

// 2. ĐỔ DỮ LIỆU VÀO MẢNG ĐỂ DÙNG FOREACH (Đúng chất bài tập kỹ lưỡng)
$list_fav = array();
if ($res->num_rows > 0) {
    while ($dong = $res->fetch_assoc()) {
        $list_fav[] = $dong;
    }
}
?>

<style>
    /* CSS thuần nhét trực tiếp vào file */
    body { background-color: #f8f9fa; }
    .khung-yeu-thich {
        background: #fff;
        border-radius: 15px;
        border: none;
        transition: 0.3s;
        height: 100%;
    }
    .khung-yeu-thich:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .anh-san-pham {
        height: 180px;
        object-fit: contain;
        padding: 15px;
        background: #fdfdfd;
    }
    .nut-xoa-nhanh {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        z-index: 10;
        transition: 0.3s;
    }
    .nut-xoa-nhanh:hover {
        background: #dc3545;
        color: #fff;
    }
    .tieu-de-sach {
        font-size: 0.9rem;
        font-weight: bold;
        color: #333;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 2.4rem;
    }
</style>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h3 class="fw-bold text-dark m-0">
            <i class="fas fa-heart text-danger me-2"></i>DANH SÁCH YÊU THÍCH
        </h3>
        <span class="badge bg-primary rounded-pill">Đang lưu <?php echo count($list_fav); ?> mục</span>
    </div>

    <div class="row g-4">
        <?php if (count($list_fav) > 0) { ?>
            
            <?php foreach ($list_fav as $item) { 
                // 3. LOGIC KIỂM TRA LOẠI HÀNG (SÁCH LẺ HAY BỘ)
                $la_bo_sach = false;
                if ($item['s_id'] != null) {
                    $la_bo_sach = true;
                }

                if ($la_bo_sach == true) {
                    $ten_hien_thi = $item['s_title'];
                    $anh_hien_thi = $item['s_img'];
                    $gia_goc = $item['s_price'];
                    $phan_tram_giam = $item['s_disc'];
                    $ma_id = $item['s_id'];
                    $duong_dan = "../book_sets/detail.php?id=";
                } else {
                    $ten_hien_thi = $item['b_title'];
                    $anh_hien_thi = $item['b_img'];
                    $gia_goc = $item['b_price'];
                    $phan_tram_giam = $item['b_disc'];
                    $ma_id = $item['b_id'];
                    $duong_dan = "../books/detail.php?id=";
                }

                // Tính toán giá sau khi giảm
                $gia_sau_giam = $gia_goc * (100 - $phan_tram_giam) / 100;
            ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card khung-yeu-thich shadow-sm position-relative overflow-hidden">
                        <a href="delete.php?id=<?php echo $item['wishlist_id']; ?>" 
                           class="nut-xoa-nhanh" 
                           onclick="return confirm('Bạn muốn bỏ sản phẩm này khỏi mục yêu thích?')">
                            <i class="fas fa-times"></i>
                        </a>

                        <div class="text-center">
                            <img src="<?php echo $anh_hien_thi; ?>" class="anh-san-pham img-fluid">
                        </div>

                        <div class="card-body d-flex flex-column p-3">
                            <?php if ($la_bo_sach == true) { ?>
                                <div class="mb-1"><span class="badge bg-info text-dark" style="font-size: 9px;">TRỌN BỘ</span></div>
                            <?php } ?>

                            <div class="tieu-de-sach mb-2"><?php echo $ten_hien_thi; ?></div>
                            
                            <div class="mt-auto">
                                <div class="text-danger fw-bold fs-5 mb-3">
                                    <?php echo number_format($gia_sau_giam, 0, ',', '.'); ?>đ
                                </div>
                                <a href="<?php echo $duong_dan . $ma_id; ?>" class="btn btn-primary btn-sm w-100 rounded-pill fw-bold">
                                    XEM CHI TIẾT
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

        <?php } else { ?>
            <div class="col-12 text-center py-5">
                <div class="py-5">
                    <i class="far fa-heart fa-4x mb-3 text-muted opacity-25"></i>
                    <h5 class="text-muted">Bạn chưa yêu thích sản phẩm nào.</h5>
                    <p class="small text-secondary mb-4">Hãy dạo quanh cửa hàng và chọn những cuốn sách ưng ý nhé!</p>
                    <a href="../../index.php" class="btn btn-outline-primary rounded-pill px-5">KHÁM PHÁ NGAY</a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php include '../../footer.php'; ?>
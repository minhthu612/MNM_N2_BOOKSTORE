<?php
require_once '../../includes/client_check.php';
require_once '../../config.php';

$page_title = "Giỏ hàng của tôi";
include '../../header.php';

// Lấy ID người dùng từ Session
$user_id = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

// 1. XỬ LÝ MÃ GIẢM GIÁ
if (isset($_POST['apply_coupon'])) {
    $coupon_code = $_POST['coupon_code'];
    if ($coupon_code == "GIAM50K") {
        $_SESSION['discount_amount'] = 50000;
    } else {
        if (isset($_SESSION['discount_amount'])) {
            unset($_SESSION['discount_amount']);
        }
    }
}

// 2. LẤY DỮ LIỆU GIỎ HÀNG
$sql_cart = "SELECT ci.*, b.title, b.price, b.link_images 
             FROM cart_items ci 
             LEFT JOIN books b ON ci.book_id = b.book_id 
             LEFT JOIN cart c ON ci.cart_id = c.cart_id 
             WHERE c.user_id = '$user_id'";

$res_cart = $conn->query($sql_cart);

$cart_list = array();
if ($res_cart) {
    while ($row = $res_cart->fetch_assoc()) {
        $cart_list[] = $row;
    }
}

$subtotal = 0;
$shipping_fee = 30000; 
?>

<style>
    body { background-color: #f8f9fa; }
    /* Khung chính của giỏ hàng */
    .cart-card { background: #ffffff; border-radius: 20px; border: none; overflow: hidden; }
    
    /* Hình ảnh sản phẩm */
    .product-img { width: 70px; height: 100px; object-fit: cover; border-radius: 12px; transition: 0.3s; }
    .product-img:hover { transform: scale(1.05); }

    /* Nút xóa sản phẩm */
    .delete-link { color: #ced4da; transition: 0.3s; font-size: 1.2rem; }
    .delete-link:hover { color: #dc3545; }

    /* Ô nhập số lượng */
    .qty-input { width: 65px; border-radius: 10px !important; text-align: center; font-weight: 700; border: 1px solid #dee2e6; height: 35px; }
    
    /* Nút cập nhật nhỏ gọn */
    .btn-update-sm { font-size: 10px; color: #6c757d; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; }
    .btn-update-sm:hover { color: #0d6efd; }

    /* Vùng tổng kết tiền */
    .summary-box { background: #ffffff; border-radius: 20px; padding: 25px; border: 1px solid rgba(0,0,0,0.05); }
    .summary-line { padding: 12px 0; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #eee; }
    .summary-line:last-of-type { border-bottom: none; }
    
    /* Nút bấm bo tròn đậm chất hiện đại */
    .pill-btn { border-radius: 30px !important; font-weight: 700; padding: 12px 25px; transition: 0.3s; }
    .pill-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
</style>

<div class="container py-5">
    <div class="row align-items-end mb-4">
        <div class="col">
            <h2 class="fw-bold text-dark m-0">Giỏ hàng của bạn</h2>
            <p class="text-muted small m-0">Bạn đang có <?php echo count($cart_list); ?> sản phẩm trong giỏ</p>
        </div>
    </div>

    <?php if (count($cart_list) > 0) { ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="cart-card shadow-sm p-4">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead class="text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-0" width="50%">Sách</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Tổng tiền</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_list as $item) { 
                                    $gia_hien_tai = (int)$item['price'];
                                    $sl_hien_tai = (int)$item['quantity'];
                                    $thanh_tien = $gia_hien_tai * $sl_hien_tai;
                                    $subtotal += $thanh_tien;
                                    
                                    $id_dong = isset($item['cart_item_id']) ? $item['cart_item_id'] : $item['id'];
                                ?>
                                    <tr style="border-bottom: 1px solid #f8f9fa;">
                                        <td class="ps-0 py-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?php echo $item['link_images']; ?>" class="product-img shadow-sm border">
                                                <div>
                                                    <div class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($item['title']); ?></div>
                                                    <div class="text-primary fw-bold small"><?php echo number_format($gia_hien_tai, 0, ',', '.'); ?>đ</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <form action="update.php" method="POST" class="d-inline-block">
                                                <input type="number" name="quantity" class="qty-input mb-1" value="<?php echo $sl_hien_tai; ?>" min="1">
                                                <input type="hidden" name="cart_item_id" value="<?php echo $id_dong; ?>">
                                                <br>
                                                <button type="submit" class="btn btn-link btn-update-sm p-0 text-decoration-none">Cập nhật</button>
                                            </form>
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            <?php echo number_format($thanh_tien, 0, ',', '.'); ?>đ
                                        </td>
                                        <td class="text-end pe-0">
                                            <a href="delete.php?id=<?php echo $id_dong; ?>" class="delete-link" onclick="return confirm('Bạn muốn bỏ sản phẩm này?')">
                                                <i class="fas fa-times-circle"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="../../index.php" class="text-muted text-decoration-none small fw-bold">
                        <i class="fas fa-long-arrow-alt-left me-2"></i> Tiếp tục mua thêm sách
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="summary-box shadow-sm mb-4">
                    <h5 class="fw-bold mb-4" style="text-align: center">Tổng đơn hàng</h5>
                    
                    <form method="POST" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="coupon_code" class="form-control px-3" placeholder="Mã giảm giá..." style="border-radius: 12px 0 0 12px;">
                            <button class="btn btn-dark px-3" type="submit" name="apply_coupon" style="border-radius: 0 12px 12px 0;">Dùng</button>
                        </div>
                    </form>

                    <div class="mb-4">
                        <div class="summary-line">
                            <span class="text-muted">Tạm tính</span>
                            <span class="fw-bold"><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</span>
                        </div>
                        
                        <?php 
                            $discount_val = isset($_SESSION['discount_amount']) ? $_SESSION['discount_amount'] : 0;
                            $final_total = $subtotal - $discount_val + $shipping_fee;
                        ?>

                        <div class="summary-line">
                            <span class="text-muted">Giảm giá</span>
                            <span class="text-success fw-bold">-<?php echo number_format($discount_val, 0, ',', '.'); ?>đ</span>
                        </div>
                        <div class="summary-line">
                            <span class="text-muted">Phí giao hàng</span>
                            <span class="fw-bold">+<?php echo number_format($shipping_fee, 0, ',', '.'); ?>đ</span>
                        </div>
                        <div class="summary-line border-0 pt-4">
                            <span class="fs-5 fw-bold text-dark">Tổng cộng</span>
                            <span class="fs-4 fw-bold text-danger"><?php echo number_format($final_total, 0, ',', '.'); ?>đ</span>
                        </div>
                    </div>

                    <a href="../checkout/index.php" class="btn btn-primary w-100 pill-btn shadow py-3">
                        ĐẶT HÀNG NGAY <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
                
                <div class="alert alert-info border-0 rounded-4 p-3 small">
                    <i class="fas fa-shield-alt me-2"></i> Đảm bảo thanh toán an toàn 100%.
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="text-center py-5 cart-card shadow-sm border">
            <div class="py-5">
                <i class="fas fa-shopping-bag fa-5x text-light mb-4"></i>
                <h4 class="text-muted fw-bold">Giỏ hàng của bạn đang trống</h4>
                <p class="text-muted small mb-4">Có vẻ như bạn chưa thêm bất kỳ sản phẩm nào.</p>
                <a href="../../index.php" class="btn btn-primary pill-btn px-5 shadow">KHÁM PHÁ CỬA HÀNG</a>
            </div>
        </div>
    <?php } ?>
</div>

<?php include '../../footer.php'; ?>
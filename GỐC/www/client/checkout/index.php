<?php
require_once '../../includes/client_check.php';
require_once '../../config.php';

// --- PHẦN 1: XỬ LÝ LOGIC PHP TRẦN ---
$user_id = $_SESSION['user_id'];

$selected_fullname = '';
$selected_phone = '';
$selected_address_text = '';
$selected_address_id = 0;

// Xử lý các hành động GET
if (isset($_GET['action'])) {
    $id = 0;
    if (isset($_GET['id'])) { 
        $id = (int)$_GET['id']; 
    }

    if ($_GET['action'] == 'set_default') {
        $conn->query("UPDATE addresses SET is_default = 0 WHERE user_id = '$user_id'");
        $conn->query("UPDATE addresses SET is_default = 1 WHERE address_id = '$id' AND user_id = '$user_id'");
        $_SESSION['success'] = "Đã thay đổi địa chỉ mặc định!";
    } 
    
    if ($_GET['action'] == 'select_addr') {
        $res_select = $conn->query("SELECT * FROM addresses WHERE address_id = '$id'");
        $row_select = $res_select->fetch_assoc();
        if ($row_select) {
            $selected_fullname = $row_select['fullname'];
            $selected_phone = $row_select['phone'];
            $selected_address_text = $row_select['street'].', '.$row_select['ward'].', '.$row_select['district'].', '.$row_select['city'];
            $selected_address_id = $id;
        }
    }
    
    header("Location: index.php?selected_id=$selected_address_id");
    exit();
}

if (isset($_GET['selected_id'])) {
    $selected_address_id = (int)$_GET['selected_id'];
}

// 1. Kiểm tra giỏ hàng
$res_check = $conn->query("SELECT ci.cart_item_id FROM cart_items ci JOIN cart c ON ci.cart_id = c.cart_id WHERE c.user_id = '$user_id'");
if ($res_check->num_rows == 0) {
    header("Location: ../cart/index.php");
    exit();
}

// 2. Lấy dữ liệu giỏ hàng
$sql_data = "SELECT ci.*, b.title, b.price FROM cart_items ci JOIN books b ON ci.book_id = b.book_id JOIN cart c ON ci.cart_id = c.cart_id WHERE c.user_id = '$user_id'";
$result_main = $conn->query($sql_data);

$cart_items = array();
$subtotal = 0;
while ($row = $result_main->fetch_assoc()) {
    $cart_items[] = $row;
    $subtotal = $subtotal + ($row['price'] * $row['quantity']);
}

// 3. Xử lý mã giảm giá
if (isset($_POST['apply_coupon_checkout'])) {
    $coupon_code = '';
    if (isset($_POST['coupon_code'])) { $coupon_code = $_POST['coupon_code']; }
    $res_cp = $conn->query("SELECT * FROM coupons WHERE code = '$coupon_code' AND status = 'active' LIMIT 1");
    if ($res_cp->num_rows > 0) {
        $cp_data = $res_cp->fetch_assoc();
        $dis = $cp_data['discount'];
        $_SESSION['discount_amount'] = ($dis <= 100) ? ($subtotal * $dis / 100) : $dis;
        $_SESSION['coupon_code_used'] = $coupon_code;
    }
    header("Location: index.php?selected_id=$selected_address_id");
    exit();
}

$shipping_fee = 30000;
$discount_val = 0;
if (isset($_SESSION['discount_amount'])) { $discount_val = $_SESSION['discount_amount']; }
$final_total = $subtotal - $discount_val + $shipping_fee;
if ($final_total < 0) { $final_total = 0; }

// 4. Lấy danh sách địa chỉ
$res_addr = $conn->query("SELECT * FROM addresses WHERE user_id = '$user_id' ORDER BY is_default DESC");
$addresses = array();
while ($a = $res_addr->fetch_assoc()) {
    $addresses[] = $a;
    if ($selected_address_id == 0 && $a['is_default'] == 1) {
        $selected_address_id = $a['address_id'];
    }
    if ($a['address_id'] == $selected_address_id) {
        $selected_fullname = $a['fullname'];
        $selected_phone = $a['phone'];
        $selected_address_text = $a['street'].', '.$a['ward'].', '.$a['district'].', '.$a['city'];
    }
}

$page_title = "Thanh toán";
include '../../header.php';
?>

<style>
    body { background-color: #f8f9fa; }
    .khung-thanh-toan { background: #fff; border-radius: 20px; padding: 30px; border: 1px solid #eee; }
    .dia-chi-card { border: 2px solid #f1f1f1; border-radius: 15px; padding: 20px; margin-bottom: 15px; position: relative; transition: 0.3s; }
    .dia-chi-card.active { border-color: #0d6efd; background-color: #f0f7ff; box-shadow: 0 5px 15px rgba(13,110,253,0.05); }
    .nut-thanh-toan { border-radius: 30px !important; padding: 15px; font-weight: bold; }
    .phuong-thuc-item { border: 1px solid #eee; border-radius: 12px; padding: 15px; margin-bottom: 12px; transition: 0.2s; cursor: pointer; }
    .phuong-thuc-item:hover { border-color: #0d6efd; background-color: #f8faff; }
    .hanh-dong-dia-chi { font-size: 12px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; }
    .hanh-dong-dia-chi:hover { opacity: 0.8; }
</style>

<div class="container py-5">
    <h3 class="fw-bold mb-4 text-center">XÁC NHẬN THANH TOÁN</h3>

    <?php if (isset($_SESSION['success'])) { ?>
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            <i class="fas fa-check-circle me-2"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php } ?>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="khung-thanh-toan shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <h5 class="fw-bold m-0"><i class="fas fa-map-marker-alt text-danger me-2"></i>1. ĐỊA CHỈ NHẬN HÀNG</h5>
                    <a href="../address/add.php" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                        <i class="fas fa-plus-circle me-1"></i> Thêm mới
                    </a>
                </div>

                <?php foreach ($addresses as $addr) { ?>
                    <div class="dia-chi-card <?php if($addr['address_id'] == $selected_address_id) echo 'active'; ?>">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="fw-bold text-dark fs-5">
                                    <?php echo $addr['fullname']; ?> 
                                    <?php if ($addr['is_default'] == 1) { echo '<span class="badge bg-danger-subtle text-danger ms-2" style="font-size:10px"><i class="fas fa-star me-1"></i>MẶC ĐỊNH</span>'; } ?>
                                </div>
                                <div class="small text-muted mt-2"><i class="fas fa-phone-alt me-2"></i><?php echo $addr['phone']; ?></div>
                                <div class="small text-secondary mt-1"><i class="fas fa-location-arrow me-2"></i><?php echo $addr['street'].', '.$addr['ward'].', '.$addr['district'].', '.$addr['city']; ?></div>
                                
                                <div class="mt-3">
                                    <?php if ($addr['address_id'] != $selected_address_id) { ?>
                                        <a href="index.php?action=select_addr&id=<?php echo $addr['address_id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-2">
                                            <i class="fas fa-check-mouse me-1"></i> Chọn địa chỉ này
                                        </a>
                                    <?php } else { ?>
                                        <span class="badge bg-success rounded-pill px-3 py-2"><i class="fas fa-check-circle me-1"></i> Đang chọn để giao hàng</span>
                                    <?php } ?>
                                </div>
                            </div>
                            
                            <div class="col-md-4 text-end border-start d-flex flex-column gap-2">
                                <a href="../address/edit.php?id=<?php echo $addr['address_id']; ?>" class="hanh-dong-dia-chi text-primary">
                                    <i class="fas fa-edit"></i> Chỉnh sửa
                                </a>
                                
                                <?php if ($addr['is_default'] == 0) { ?>
                                    <a href="index.php?action=set_default&id=<?php echo $addr['address_id']; ?>" class="hanh-dong-dia-chi text-info">
                                        <i class="fas fa-thumbtack"></i> Đặt mặc định
                                    </a>
                                    <a href="../address/delete.php?id=<?php echo $addr['address_id']; ?>" class="hanh-dong-dia-chi text-danger" onclick="return confirm('Bạn muốn xóa địa chỉ này?')">
                                        <i class="fas fa-trash-alt"></i> Xóa bỏ
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <form action="process.php" method="POST" id="checkoutForm">
                    <input type="hidden" name="address_id" value="<?php echo $selected_address_id; ?>">
                    <input type="hidden" name="fullname" value="<?php echo $selected_fullname; ?>">
                    <input type="hidden" name="phone" value="<?php echo $selected_phone; ?>">
                    <input type="hidden" name="address" value="<?php echo $selected_address_text; ?>">

                    <div class="mt-4">
                        <label class="fw-bold small text-secondary mb-2">GHI CHÚ ĐƠN HÀNG (Shipper sẽ đọc cái này):</label>
                        <textarea name="notes" class="form-control rounded-3" rows="2" placeholder="Ví dụ: Giao sau 5h chiều, gọi trước khi đến..."></textarea>
                    </div>
            </div>

            <div class="khung-thanh-toan shadow-sm">
                <h5 class="fw-bold mb-4 border-bottom pb-3"><i class="fas fa-wallet text-primary me-2"></i>2. PHƯƠNG THỨC THANH TOÁN</h5>
                
                <div class="phuong-thuc-item">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="pay_cod" value="COD" checked required>
                        <label class="form-check-label fw-bold w-100" for="pay_cod">
                            <i class="fas fa-money-bill-wave text-success me-2"></i>Tiền mặt khi nhận hàng (COD)
                        </label>
                    </div>
                </div>

                <div class="phuong-thuc-item">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="pay_bank" value="Banking">
                        <label class="form-check-label fw-bold w-100" for="pay_bank">
                            <i class="fas fa-university text-primary me-2"></i>Chuyển khoản Ngân hàng
                        </label>
                    </div>
                </div>

                <div class="phuong-thuc-item">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="pay_momo" value="Momo">
                        <label class="form-check-label fw-bold w-100" for="pay_momo">
                            <i class="fas fa-wallet text-danger me-2"></i>Ví điện tử Momo
                        </label>
                    </div>
                </div>

                <div class="phuong-thuc-item">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="pay_zalo" value="ZaloPay">
                        <label class="form-check-label fw-bold w-100" for="pay_zalo">
                            <i class="fas fa-coins text-warning me-2"></i>Ví ZaloPay
                        </label>
                    </div>
                </div>
            </div>
            </form>
        </div>

        <div class="col-lg-5">
            <div class="khung-thanh-toan shadow-sm sticky-top" style="top: 20px;">
                <h5 class="fw-bold mb-4 text-center">TÓM TẮT ĐƠN HÀNG</h5>
                <div class="mb-4" style="max-height: 250px; overflow-y: auto;">
                    <?php foreach ($cart_items as $item) { ?>
                        <div class="d-flex justify-content-between mb-3 small">
                            <div style="max-width: 75%;">
                                <div class="fw-bold text-dark text-truncate"><?php echo $item['title']; ?></div>
                                <div class="text-muted">Số lượng: <?php echo $item['quantity']; ?></div>
                            </div>
                            <div class="fw-bold text-dark"><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ</div>
                        </div>
                    <?php } ?>
                </div>

                <hr>

                <form method="POST" action="" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="coupon_code" class="form-control" placeholder="Mã giảm giá..." style="border-radius: 12px 0 0 12px;">
                        <button class="btn btn-dark px-3" type="submit" name="apply_coupon_checkout" style="border-radius: 0 12px 12px 0;">ÁP DỤNG</button>
                    </div>
                </form>

                <div class="bg-light p-3 rounded-4 mb-4">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Tiền hàng (tạm tính):</span>
                        <span class="fw-bold"><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small text-success">
                        <span class="text-muted">Giảm giá:</span>
                        <span class="fw-bold">-<?php echo number_format($discount_val, 0, ',', '.'); ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2 small">
                        <span class="text-muted">Phí vận chuyển:</span>
                        <span class="fw-bold">+<?php echo number_format($shipping_fee, 0, ',', '.'); ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark fs-5">TỔNG CỘNG:</span>
                        <span class="fw-bold text-danger fs-3"><?php echo number_format($final_total, 0, ',', '.'); ?>đ</span>
                    </div>
                </div>

                <?php if ($selected_address_id > 0) { ?>
                    <button type="submit" form="checkoutForm" class="btn btn-primary w-100 nut-thanh-toan shadow mb-3">
                        <i class="fas fa-shopping-bag me-2"></i> ĐẶT HÀNG NGAY
                    </button>
                <?php } else { ?>
                    <div class="alert alert-danger small py-2 text-center">Vui lòng thêm địa chỉ nhận hàng</div>
                <?php } ?>

                <div class="text-center">
                    <a href="../cart/index.php" class="text-muted small text-decoration-none"><i class="fas fa-arrow-left me-1"></i> Sửa lại giỏ hàng</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../footer.php'; ?>
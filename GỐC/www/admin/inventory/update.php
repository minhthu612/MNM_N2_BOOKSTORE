<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

// 1. Lấy ID từ URL kiểu truyền thống
$id = 0;
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
}

if ($id == 0) {
    header('Location: history.php');
    exit();
}

// 2. Lấy thông tin tồn kho bằng query trần
$sql_inv = "SELECT i.*, b.title, b.author 
            FROM inventory i 
            JOIN books b ON i.book_id = b.book_id 
            WHERE i.inventory_id = '$id'";
$res_inv = $conn->query($sql_inv);
$inventory = $res_inv->fetch_assoc();

if ($inventory == null) {
    header('Location: history.php');
    exit();
}

$error = '';

// 3. Xử lý khi nhấn nút Cập nhật (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adjustment = (int)$_POST['adjustment']; // Số lượng thêm bớt
    $note = $_POST['note'];
    
    // Tính toán số lượng mới hoàn toàn bằng PHP
    $old_stock = (int)$inventory['stock'];
    $new_stock = $old_stock + $adjustment;
    
    // Tự động xác định trạng thái dựa trên số lượng mới
    $stock_status = 'ACTIVE';
    $reorder = (int)$inventory['reorder_level'];
    
    if ($new_stock <= 0) {
        $stock_status = 'OUT_OF_STOCK';
        $new_stock = 0; // Không để tồn kho âm cho thực tế
    } else {
        if ($new_stock < $reorder) {
            $stock_status = 'LOW_STOCK';
        }
    }
    
    // Cập nhật bảng inventory bằng query nối chuỗi
    $sql_update = "UPDATE inventory SET 
                   stock = '$new_stock', 
                   stock_status = '$stock_status', 
                   last_updated = NOW() 
                   WHERE inventory_id = '$id'";
    
    if ($conn->query($sql_update)) {
        // Ghi vào lịch sử (nếu bảng tồn tại) - Dùng query trần
        $user_id = $_SESSION['user_id'];
        $sql_log = "INSERT INTO inventory_history (inventory_id, old_stock, new_stock, adjustment, note, created_by, created_at)
                    VALUES ('$id', '$old_stock', '$new_stock', '$adjustment', '$note', '$user_id', NOW())";
        
        // Chạy log thầm lặng, không cần try-catch phức tạp
        $conn->query($sql_log);
        
        $_SESSION['success'] = "Đã cập nhật tồn kho cho sách: " . $inventory['title'];
        header("Location: history.php");
        exit();
    } else {
        $error = "Lỗi cập nhật: " . $conn->error;
    }
}

admin_layout_start("Cập nhật tồn kho", 'inventory');
?>

<style>
    .khung-trang { background: #fff; padding: 30px; border-radius: 15px; border: 1px solid #eee; }
    .o-nhap { border-radius: 10px !important; padding: 12px; }
    .nut-hanh-dong { border-radius: 25px !important; padding: 10px 30px !important; font-weight: bold; }
    .thong-tin-sach { background: #f8f9fa; border-left: 5px solid #007bff; padding: 15px; border-radius: 8px; }
</style>

<div class="container">
    <div class="khung-trang shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-primary fw-bold mb-0">ĐIỀU CHỈNH SỐ LƯỢNG KHO</h4>
            <a href="history.php" class="btn btn-outline-secondary rounded-pill px-3">Quay lại</a>
        </div>

        <?php if ($error != '') { ?>
            <div class="alert alert-danger border-0 shadow-sm"><?php echo $error; ?></div>
        <?php } ?>

        <div class="row">
            <div class="col-md-7">
                <form method="POST" action="">
                    <div class="thong-tin-sach mb-4">
                        <div class="small text-muted text-uppercase">Sản phẩm điều chỉnh:</div>
                        <div class="fw-bold fs-5"><?php echo $inventory['title']; ?></div>
                        <div class="small text-secondary">Tác giả: <?php echo $inventory['author']; ?></div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="fw-bold mb-2">Tồn kho hiện tại</label>
                            <input type="text" class="form-control o-nhap bg-white" value="<?php echo $inventory['stock']; ?> cuốn" disabled>
                        </div>
                        <div class="col-6">
                            <label class="fw-bold mb-2">Mức tối thiểu</label>
                            <input type="text" class="form-control o-nhap bg-white" value="<?php echo $inventory['reorder_level']; ?> cuốn" disabled>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2 text-danger">Số lượng thay đổi (±) *</label>
                        <div class="input-group">
                            <input type="number" name="adjustment" class="form-control o-nhap text-center fs-5 fw-bold" placeholder="0" required>
                            <span class="input-group-text bg-light px-4">Cuốn</span>
                        </div>
                        <small class="text-muted">Nhập số dương (VD: 10) để <b>nhập thêm</b>, số âm (VD: -5) để <b>xuất kho</b>.</small>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2">Ghi chú lý do</label>
                        <textarea name="note" class="form-control o-nhap" rows="3" placeholder="Ví dụ: Nhập hàng đợt 2, Kiểm kho định kỳ..."></textarea>
                    </div>

                    <div class="pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-primary nut-hanh-dong shadow">
                            <i class="fas fa-save me-2"></i> CẬP NHẬT KHO
                        </button>
                        <a href="history.php" class="btn btn-light nut-hanh-dong border">HỦY BỎ</a>
                    </div>
                </form>
            </div>

            <div class="col-md-5">
                <div class="card border-0 bg-light rounded-3 h-100">
                    <div class="card-body">
                        <h6 class="fw-bold text-primary"><i class="fas fa-info-circle"></i> QUY TẮC CẬP NHẬT</h6>
                        <hr>
                        <div class="mb-3">
                            <strong>1. Cách tính số lượng:</strong>
                            <p class="small text-muted">Số mới = Tồn hiện tại + Số lượng thay đổi.</p>
                        </div>
                        <div class="mb-3">
                            <strong>2. Tự động chuyển trạng thái:</strong>
                            <ul class="small text-muted ps-3">
                                <li>Dưới mức tối thiểu: <span class="badge bg-warning text-dark">SẮP HẾT</span></li>
                                <li>Về bằng 0: <span class="badge bg-danger">HẾT HÀNG</span></li>
                                <li>Đạt mức tối thiểu: <span class="badge bg-success">CÒN HÀNG</span></li>
                            </ul>
                        </div>
                        <div class="alert alert-info border-0 small mt-4">
                            <i class="fas fa-history"></i> Cập nhật gần nhất:<br>
                            <b><?php echo date('d/m/Y - H:i', strtotime($inventory['last_updated'])); ?></b>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php admin_layout_end(); ?>
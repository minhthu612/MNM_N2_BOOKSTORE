<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

// 1. Lấy ID từ URL (Viết rành mạch kiểu SV)
$id = 0;
if (isset($_GET['category_id'])) {
    $id = (int)$_GET['category_id'];
} else {
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
    }
}

if ($id == 0) {
    header('Location: index.php');
    exit();
}

// 2. Lấy thông tin danh mục (Query trần)
$sql_cat = "SELECT * FROM categories WHERE category_id = '$id'";
$res_cat = $conn->query($sql_cat);
$category = $res_cat->fetch_assoc();

if ($category == null) {
    header('Location: index.php');
    exit();
}

// 3. Kiểm tra số lượng sách thuộc danh mục này
$sql_check = "SELECT COUNT(*) as count FROM books WHERE category_id = '$id'";
$res_check = $conn->query($sql_check);
$row_check = $res_check->fetch_assoc();
$book_count = $row_check['count'];

// 4. Lấy danh sách các danh mục khác để hiện vào thẻ Select
$res_others = $conn->query("SELECT * FROM categories WHERE category_id != '$id' ORDER BY category_name");
$other_categories = array();
if ($res_others) {
    while ($row = $res_others->fetch_assoc()) {
        $other_categories[] = $row;
    }
}

$error = '';

// 5. Xử lý khi nhấn nút "Xác nhận xóa" (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirm'])) {
        
        // A. Nếu có sách bên trong thì xử lý trước theo lựa chọn
        if ($book_count > 0) {
            $action = $_POST['action'];
            
            if ($action == 'move') {
                $new_cat_id = (int)$_POST['new_category_id'];
                if ($new_cat_id > 0) {
                    // Chuyển sách sang danh mục mới
                    $conn->query("UPDATE books SET category_id = '$new_cat_id' WHERE category_id = '$id'");
                }
            } else {
                if ($action == 'delete') {
                    // Xóa hết sách trong danh mục này
                    $conn->query("DELETE FROM books WHERE category_id = '$id'");
                }
            }
        }
        
        // B. Tiến hành xóa danh mục
        $sql_delete = "DELETE FROM categories WHERE category_id = '$id'";
        
        if ($conn->query($sql_delete)) {
            $_SESSION['success'] = "Đã xóa danh mục '" . $category['category_name'] . "' thành công!";
            header('Location: index.php');
            exit();
        } else {
            $error = "Lỗi hệ thống không thể xóa: " . $conn->error;
        }
    } else {
        // Nếu nhấn hủy bỏ hoặc quay lại
        header('Location: index.php');
        exit();
    }
}

admin_layout_start('Xác nhận xóa danh mục', 'categories');
?>

<style>
    .khung-xoa { background: #fff; border: 1px solid #dee2e6; border-radius: 12px; padding: 30px; }
    .tieu-de-canh-bao { color: #dc3545; font-weight: bold; margin-bottom: 20px; }
    .o-nhap { border-radius: 8px !important; }
    .nut-bam { border-radius: 20px !important; padding: 10px 30px !important; font-weight: bold; }
</style>

<div class="container">
    <div class="khung-xoa shadow-sm">
        <h3 class="tieu-de-canh-bao text-center">
            <i class="fas fa-exclamation-triangle"></i> CẢNH BÁO XÓA DANH MỤC
        </h3>

        <?php if ($error != '') { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

        <div class="row">
            <div class="col-md-5">
                <div class="card bg-light border-0 rounded-3 mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold">Thông tin hiện tại:</h6>
                        <hr>
                        <p>Tên loại: <strong><?php echo $category['category_name']; ?></strong></p>
                        <p>Mô tả: 
                            <small class="text-muted">
                                <?php 
                                // Nếu có cột description và nó không trống thì in ra, ngược lại in "Không có mô tả"
                                if (isset($category['description']) && $category['description'] != '') {
                                    echo $category['description'];
                                } else {
                                    echo "Chưa có mô tả cho danh mục này.";
                                }
                                ?>
                            </small>
                        </p>
                        <p>Số lượng sách bên trong: 
                            <span class="badge bg-danger"><?php echo $book_count; ?> quyển</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <form method="POST" action="">
                    <?php if ($book_count > 0) { ?>
                        <div class="alert alert-warning border-warning shadow-sm">
                            <h6 class="fw-bold"><i class="fas fa-tools"></i> BẠN CẦN XỬ LÝ SÁCH TRƯỚC:</h6>
                            <p class="small">Vì đang có <?php echo $book_count; ?> cuốn sách thuộc loại này, hãy chọn 1 trong 2 cách sau:</p>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="action" id="r1" value="move" checked>
                                <label class="form-check-label fw-bold text-success" for="r1">
                                    1. Chuyển sách sang loại khác:
                                </label>
                                <select name="new_category_id" class="form-select mt-2 o-nhap">
                                    <option value="">-- Chọn danh mục đích --</option>
                                    <?php foreach ($other_categories as $c) { ?>
                                        <option value="<?php echo $c['category_id']; ?>">
                                            <?php echo $c['category_name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-check mt-4">
                                <input class="form-check-input" type="radio" name="action" id="r2" value="delete">
                                <label class="form-check-label fw-bold text-danger" for="r2">
                                    2. XÓA LUÔN TẤT CẢ SÁCH TRONG LOẠI NÀY
                                </label>
                                <div class="small text-muted mt-1">(Cẩn thận: Sách sẽ bị xóa mất vĩnh viễn khỏi kho)</div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="mt-4 text-center">
                        <p class="text-secondary small">Sau khi bấm "Xác nhận", mọi dữ liệu về danh mục này sẽ biến mất.</p>
                        <div class="d-flex justify-content-center gap-2">
                            <button type="submit" name="confirm" class="btn btn-danger nut-bam shadow">
                                <i class="fas fa-trash-alt me-2"></i> XÁC NHẬN XÓA
                            </button>
                            <a href="index.php" class="btn btn-outline-secondary nut-bam">
                                QUAY LẠI
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php admin_layout_end(); ?>
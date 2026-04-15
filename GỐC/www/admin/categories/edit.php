<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

// 1. Lấy ID từ URL (Viết kiểu tường minh)
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

$error = '';
$success = '';

// 2. Lấy thông tin danh mục hiện tại (Query trần)
$sql_get = "SELECT * FROM categories WHERE category_id = '$id'";
$res_get = $conn->query($sql_get);
$category = $res_get->fetch_assoc();

if ($category == null) {
    header('Location: index.php');
    exit();
}

// 3. Xử lý khi nhấn nút Lưu thay đổi (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['category_name'];
    $description = $_POST['description'];
    
    // Kiểm tra trùng tên (trừ chính nó)
    $sql_check = "SELECT category_id FROM categories WHERE category_name = '$name' AND category_id != '$id'";
    $res_check = $conn->query($sql_check);
    
    if ($res_check->num_rows > 0) {
        $error = "Tên danh mục này đã tồn tại rồi, vui lòng chọn tên khác!";
    } else {
        // Cập nhật dữ liệu (Nối chuỗi trực tiếp)
        $sql_update = "UPDATE categories SET 
                       category_name = '$name', 
                       description = '$description' 
                       WHERE category_id = '$id'";
        
        if ($conn->query($sql_update)) {
            $_SESSION['success'] = "Đã cập nhật thông tin danh mục thành công!";
            header('Location: index.php');
            exit();
        } else {
            $error = "Lỗi khi cập nhật dữ liệu: " . $conn->error;
        }
    }
}

// 4. Lấy số lượng sách trong danh mục này để hiển thị thông tin
$sql_count = "SELECT COUNT(*) as total FROM books WHERE category_id = '$id'";
$res_count = $conn->query($sql_count);
$row_count = $res_count->fetch_assoc();
$book_count = $row_count['total'];

admin_layout_start('Sửa danh mục: ' . $category['category_name'], 'categories');
?>

<style>
    .khung-sua {
        background-color: #ffffff;
        padding: 30px;
        border-radius: 15px;
        border: 1px solid #e0e0e0;
    }
    .o-nhap {
        border-radius: 10px !important;
        padding: 12px;
    }
    .nut-hanh-dong {
        border-radius: 25px !important;
        padding: 10px 30px !important;
        font-weight: bold;
        text-decoration: none;
        display: inline-block;
    }
</style>

<div class="container-fluid">
    <div class="khung-sua shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-primary fw-bold mb-0">CHỈNH SỬA DANH MỤC</h4>
            <span class="badge rounded-pill bg-dark px-3 py-2">Mã số: #<?php echo $category['category_id']; ?></span>
        </div>

        <?php if ($error != '') { ?>
            <div class="alert alert-danger border-0 shadow-sm">
                <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error; ?>
            </div>
        <?php } ?>

        <form method="POST" action="">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-4">
                        <label class="fw-bold mb-2">Tên danh mục *</label>
                        <input type="text" class="form-control o-nhap" name="category_name" 
                               value="<?php echo $category['category_name']; ?>" required>
                        <div class="small text-muted mt-1">Lưu ý: Không nên để tên quá dài.</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 bg-light rounded-3 mb-3">
                        <div class="card-body">
                            <h6 class="fw-bold"><i class="fas fa-info-circle text-info"></i> THÔNG TIN KÈM THEO</h6>
                            <hr>
                            <p class="small mb-2">Số lượng sách đang thuộc loại này:</p>
                            <h3 class="text-primary fw-bold"><?php echo $book_count; ?> <small style="font-size: 1rem;">cuốn sách</small></h3>
                            
                            <?php if ($book_count > 0) { ?>
                                <a href="../books/index.php?category_id=<?php echo $id; ?>" class="btn btn-sm btn-link p-0 text-decoration-none">
                                    <i class="fas fa-external-link-alt"></i> Xem danh sách sách
                                </a>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="alert alert-warning border-0 small">
                        <i class="fas fa-lightbulb"></i> <strong>Mẹo:</strong> Nếu bạn đổi tên danh mục, tất cả sách thuộc danh mục này sẽ tự động được cập nhật theo tên mới.
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary nut-hanh-dong shadow">
                        <i class="fas fa-save me-2"></i> LƯU THAY ĐỔI
                    </button>
                    <a href="index.php" class="btn btn-outline-secondary nut-hanh-dong">
                        HỦY BỎ
                    </a>
                </div>
                
                <a href="delete.php?category_id=<?php echo $id; ?>" class="btn btn-danger nut-hanh-dong">
                    <i class="fas fa-trash-alt"></i> XÓA DANH MỤC
                </a>
            </div>
        </form>
    </div>
</div>

<?php admin_layout_end(); ?>
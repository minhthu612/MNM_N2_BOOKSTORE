<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

$error = '';
$success = '';

// Xử lý khi người dùng nhấn nút Lưu (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Lấy dữ liệu từ form (Dùng cách gán trực tiếp đơn giản)
    $name = $_POST['name'];
    $description = $_POST['description'];
    
    // 1. Kiểm tra trùng tên danh mục (Dùng query trần nối chuỗi)
    $sql_check = "SELECT category_id FROM categories WHERE category_name = '$name'";
    $result_check = $conn->query($sql_check);
    
    if ($result_check->num_rows > 0) {
        $error = "Tên danh mục này đã tồn tại trong hệ thống rồi!";
    } else {
        // 2. Nếu không trùng thì tiến hành thêm mới
        $sql_insert = "INSERT INTO categories (category_name, description) 
                       VALUES ('$name', '$description')";
        
        if ($conn->query($sql_insert)) {
            $_SESSION['success'] = "Đã thêm danh mục mới thành công!";
            header('Location: index.php');
            exit();
        } else {
            $error = "Lỗi khi lưu dữ liệu: " . $conn->error;
        }
    }
}

admin_layout_start('Thêm danh mục', 'categories');
?>

<style>
    .khung-trang {
        background-color: #ffffff;
        padding: 30px;
        border-radius: 15px;
        border: 1px solid #e0e0e0;
    }
    .tieu-de {
        color: #2c3e50;
        font-weight: bold;
        margin-bottom: 25px;
        border-left: 5px solid #007bff;
        padding-left: 15px;
    }
    .o-nhap {
        border-radius: 10px !important;
        padding: 12px;
    }
    .nut-hanh-dong {
        border-radius: 25px !important;
        padding: 10px 30px !important;
        font-weight: bold;
    }
</style>

<div class="container-fluid">
    <div class="khung-trang shadow-sm">
        <h4 class="tieu-de">TẠO DANH MỤC SÁCH MỚI</h4>

        <?php if ($error != '') { ?>
            <div class="alert alert-danger border-0 shadow-sm">
                <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error; ?>
            </div>
        <?php } ?>

        <form method="POST" action="">
            <div class="row">
                <div class="col-md-7">
                    <div class="mb-4">
                        <label class="fw-bold mb-2">Tên danh mục sách *</label>
                        <input type="text" class="form-control o-nhap" name="name" 
                               placeholder="Ví dụ: Sách Kỹ Năng Sống" required>
                        <small class="text-muted">Gợi ý: Tên danh mục nên ngắn gọn (2-3 từ).</small>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2">Mô tả tóm tắt</label>
                        <textarea class="form-control o-nhap" name="description" rows="6" 
                                  placeholder="Nhập vài dòng giới thiệu về loại sách này..."></textarea>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card border-0 bg-light rounded-3">
                        <div class="card-body">
                            <h6 class="fw-bold text-primary"><i class="fas fa-info-circle"></i> QUY ĐỊNH THÊM MỚI</h6>
                            <hr>
                            <ul class="small text-muted" style="line-height: 2;">
                                <li>Không được đặt tên danh mục trùng nhau.</li>
                                <li>Tên danh mục không nên chứa ký tự đặc biệt.</li>
                                <li>Mô tả có thể để trống nếu chưa cần thiết.</li>
                                <li>Sau khi thêm, bạn có thể vào danh sách để chỉnh sửa lại.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-4 p-3 border rounded-3 bg-white">
                        <label class="fw-bold mb-2 small text-uppercase text-secondary">Xác nhận thông tin</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="check1" required>
                            <label class="form-check-label small" for="check1">Tôi đã kiểm tra tính chính xác của tên.</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check2" required>
                            <label class="form-check-label small" for="check2">Danh mục này phù hợp với cửa hàng.</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex gap-2">
                <button type="submit" class="btn btn-primary nut-hanh-dong shadow">
                    <i class="fas fa-save me-2"></i> LƯU DANH MỤC
                </button>
                <a href="index.php" class="btn btn-outline-secondary nut-hanh-dong">
                    HỦY BỎ
                </a>
            </div>
        </form>
    </div>
</div>

<?php admin_layout_end(); ?>
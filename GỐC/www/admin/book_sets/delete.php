<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

// 1. Lấy ID từ URL (Viết kiểu tường minh)
$set_id = 0;
if (isset($_GET['set_id'])) {
    $set_id = (int)$_GET['set_id'];
}

if ($set_id == 0) {
    header('Location: index.php');
    exit();
}

// 2. Lấy thông tin bộ sách (Dùng query trần)
$sql = "SELECT * FROM book_sets WHERE set_id = '$set_id'";
$result = $conn->query($sql);
$book_set = $result->fetch_assoc();

if ($book_set == null) {
    header('Location: index.php');
    exit();
}

// 3. Đếm số sách trong bộ (Dùng query trần)
$count_sql = "SELECT COUNT(*) as total FROM book_set_items WHERE set_id = '$set_id'";
$count_res = $conn->query($count_sql);
$count_data = $count_res->fetch_assoc();
$book_count = $count_data['total'];

$error = '';

// 4. Xử lý khi nhấn nút Xác nhận xóa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirm'])) {
        
        // Bước A: Xóa các liên kết sách trong bộ trước (Tránh lỗi khóa ngoại)
        $del_items = "DELETE FROM book_set_items WHERE set_id = '$set_id'";
        $conn->query($del_items);
        
        // Bước B: Xóa file ảnh vật lý nếu có
        $image_path = $book_set['images']; // Cột images lưu đường dẫn file upload
        if ($image_path != '') {
            if (file_exists('../../' . $image_path)) {
                unlink('../../' . $image_path);
            }
        }
        
        // Bước C: Xóa bản ghi bộ sách trong DB
        $del_set = "DELETE FROM book_sets WHERE set_id = '$set_id'";
        
        if ($conn->query($del_set)) {
            $_SESSION['success'] = "Đã xóa bộ sách thành công!";
            header('Location: index.php');
            exit();
        } else {
            $error = "Lỗi khi xóa dữ liệu: " . $conn->error;
        }
    } else {
        header('Location: index.php');
        exit();
    }
}

admin_layout_start('Xác nhận xóa bộ sách', 'book_sets');
?>

<style>
    .khung-canh-bao {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 30px;
        margin-top: 20px;
    }
    .thong-tin-anh {
        border: 1px solid #ddd;
        padding: 5px;
        background: #f9f9f9;
        max-height: 250px;
        border-radius: 5px;
    }
    .tieu-de-canh-bao {
        color: #d9534f;
        font-weight: bold;
        border-bottom: 2px solid #d9534f;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
</style>

<div class="container">
    <div class="khung-canh-bao shadow-sm">
        <h3 class="tieu-de-canh-bao text-center">
            <i class="fas fa-exclamation-triangle"></i> XÁC NHẬN XÓA VĨNH VIỄN
        </h3>

        <?php if ($error != '') { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

        <div class="row">
            <div class="col-md-5 text-center mb-4">
                <?php 
                    $anh_hien_thi = $book_set['link_images'];
                    if ($anh_hien_thi == '') {
                        $anh_hien_thi = 'https://via.placeholder.com/300x200?text=Khong+co+anh';
                    }
                ?>
                <img src="<?php echo $anh_hien_thi; ?>" class="thong-tin-anh img-fluid" alt="Ảnh bộ sách">
                <div class="mt-3">
                    <span class="badge bg-secondary p-2">ID Bộ sách: #<?php echo $book_set['set_id']; ?></span>
                </div>
            </div>

            <div class="col-md-7">
                <div class="alert alert-warning border-warning">
                    <h5>Bạn đang thực hiện xóa bộ sách:</h5>
                    <p class="display-6" style="font-size: 1.5rem; font-weight: bold;">
                        <?php echo $book_set['name']; ?>
                    </p>
                    <hr>
                    <ul class="mb-0">
                        <li>Số lượng sách thành phần đang có: <strong><?php echo $book_count; ?> cuốn</strong>.</li>
                        <li>Giá bán niêm yết: <strong><?php echo number_format($book_set['price']); ?> đ</strong>.</li>
                        <li class="text-danger fw-bold">Dữ liệu này sẽ bị xóa hoàn toàn khỏi hệ thống!</li>
                    </ul>
                </div>

                <div class="card border-danger mt-4">
                    <div class="card-body bg-light">
                        <p class="text-center text-muted">Bấm "Đồng ý xóa" để hoàn tất hoặc "Quay lại" để hủy bỏ thao tác này.</p>
                        <form method="POST" action="">
                            <div class="row g-2">
                                <div class="col-6">
                                    <button type="submit" name="confirm" class="btn btn-danger btn-lg w-100 fw-bold">
                                        <i class="fas fa-check-circle"></i> ĐỒNG Ý XÓA
                                    </button>
                                </div>
                                <div class="col-6">
                                    <a href="index.php" class="btn btn-secondary btn-lg w-100 fw-bold">
                                        <i class="fas fa-arrow-left"></i> QUAY LẠI
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php admin_layout_end(); ?>
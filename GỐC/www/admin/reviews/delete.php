<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

// 1. Lấy ID đánh giá từ URL kiểu truyền thống
$review_id = 0;
if (isset($_GET['id'])) {
    $review_id = (int)$_GET['id'];
}

if ($review_id == 0) {
    $_SESSION['error'] = "Không tìm thấy đánh giá cần xóa.";
    header('Location: index.php');
    exit();
}

// 2. Lấy thông tin đánh giá bằng query trần để hiển thị xác nhận
$sql_review = "SELECT r.*, b.title as book_title, u.username, u.fullname
               FROM reviews r
               LEFT JOIN books b ON r.book_id = b.book_id
               LEFT JOIN users u ON r.user_id = u.user_id
               WHERE r.review_id = '$review_id'";
$res_review = $conn->query($sql_review);
$review = $res_review->fetch_assoc();

if ($review == null) {
    $_SESSION['error'] = "Đánh giá không tồn tại.";
    header('Location: index.php');
    exit();
}

$error = '';

// 3. Xử lý khi người dùng nhấn nút Xác nhận xóa (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // SV thường kiểm tra điều kiện đơn giản bằng PHP thay vì JS
    $confirm_txt = $_POST['confirm_text'];
    
    if ($confirm_txt == 'DELETE') {
        // Thực hiện lệnh xóa trần
        $sql_delete = "DELETE FROM reviews WHERE review_id = '$review_id'";
        
        if ($conn->query($sql_delete)) {
            $_SESSION['success'] = "Đã xóa đánh giá thành công!";
            header('Location: index.php');
            exit();
        } else {
            $error = "Lỗi hệ thống không thể xóa: " . $conn->error;
        }
    } else {
        $error = "Bạn phải nhập đúng chữ 'DELETE' (viết hoa) để xác nhận xóa.";
    }
}

admin_layout_start("Xác nhận xóa đánh giá", 'reviews');
?>

<style>
    .khung-xoa { background: #fff; padding: 30px; border-radius: 15px; border: 1px solid #eee; }
    .o-nhap { border-radius: 10px !important; padding: 12px; }
    .nut-bam { border-radius: 25px !important; padding: 10px 30px !important; font-weight: bold; }
    .vung-thong-tin { background: #fff5f5; border-left: 5px solid #ff4d4d; padding: 15px; border-radius: 8px; }
    .sao-vang { color: #f1c40f; }
</style>

<div class="container">
    <div class="khung-xoa shadow-sm mx-auto" style="max-width: 700px;">
        <div class="text-center mb-4">
            <h3 class="text-danger fw-bold"><i class="fas fa-exclamation-triangle"></i> XÁC NHẬN XÓA</h3>
            <p class="text-muted">Hành động này sẽ xóa vĩnh viễn dữ liệu và không thể khôi phục.</p>
        </div>

        <?php if ($error != '') { ?>
            <div class="alert alert-danger border-0 shadow-sm">
                <i class="fas fa-times-circle me-2"></i> <?php echo $error; ?>
            </div>
        <?php } ?>

        <div class="vung-thong-tin mb-4">
            <div class="mb-2">
                <small class="text-muted text-uppercase small fw-bold">Sách:</small>
                <div class="fw-bold"><?php echo $review['book_title']; ?></div>
            </div>
            <div class="mb-2">
                <small class="text-muted text-uppercase small fw-bold">Người đăng:</small>
                <div><?php echo ($review['fullname'] != '' ? $review['fullname'] : $review['username']); ?></div>
            </div>
            <div class="mb-2">
                <small class="text-muted text-uppercase small fw-bold">Đánh giá:</small>
                <div class="text-warning">
                    <?php 
                    for($i = 1; $i <= 5; $i++) {
                        if($i <= $review['rating']) {
                            echo '<i class="fas fa-star sao-vang"></i>';
                        } else {
                            echo '<i class="far fa-star sao-vang"></i>';
                        }
                    }
                    ?>
                    <span class="text-dark ms-1">(<?php echo $review['rating']; ?> sao)</span>
                </div>
            </div>
            <div class="mb-0">
                <small class="text-muted text-uppercase small fw-bold">Nội dung:</small>
                <div class="small italic text-secondary">"<?php echo $review['comment']; ?>"</div>
            </div>
        </div>

        <form method="POST" action="">
            <div class="mb-4">
                <label class="fw-bold mb-2">Để tiếp tục, vui lòng nhập chữ <span class="text-danger">DELETE</span> vào ô bên dưới:</label>
                <input type="text" name="confirm_text" class="form-control o-nhap text-center fs-5 fw-bold" 
                       placeholder="Gõ DELETE để xác nhận" required autocomplete="off">
            </div>

            <div class="d-flex justify-content-center gap-2 pt-3 border-top">
                <button type="submit" class="btn btn-danger nut-bam shadow">
                    <i class="fas fa-trash-alt me-2"></i> XÓA NGAY
                </button>
                <a href="index.php" class="btn btn-light nut-bam border">
                    QUAY LẠI
                </a>
            </div>
        </form>

        <div class="card border-0 bg-light rounded-3 mt-4">
            <div class="card-body py-2">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i> Lưu ý: Thầy/Cô thường đánh giá cao việc bạn tạo ra rào cản xác nhận (nhập chữ DELETE) thay vì chỉ bấm nút xóa thông thường để tránh người dùng nhấn nhầm.
                </small>
            </div>
        </div>
    </div>
</div>

<?php admin_layout_end(); ?>
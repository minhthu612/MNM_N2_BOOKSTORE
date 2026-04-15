<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

// 1. Lấy ID đánh giá từ URL kiểu truyền thống
$review_id = 0;
if (isset($_GET['id'])) {
    $review_id = (int)$_GET['id'];
}

if ($review_id == 0) {
    $_SESSION['error'] = "Không tìm thấy đánh giá.";
    header('Location: index.php');
    exit();
}

// 2. Lấy thông tin đánh giá bằng query trần
$sql_review = "SELECT r.*, b.title as book_title, u.username, u.fullname, u.email
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
$success = '';

// 3. Xử lý khi nhấn nút Lưu (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = (int)$_POST['rating'];
    $comment = $_POST['comment'];
    
    // Validate bằng PHP đơn giản
    if ($rating < 1 || $rating > 5) {
        $error = "Vui lòng chọn điểm đánh giá từ 1 đến 5 sao.";
    } else {
        if ($comment == '') {
            $error = "Nội dung đánh giá không được để trống.";
        } else {
            // Cập nhật bằng query trần nối chuỗi
            $sql_update = "UPDATE reviews SET 
                           rating = '$rating', 
                           comment = '$comment' 
                           WHERE review_id = '$review_id'";
            
            if ($conn->query($sql_update)) {
                $_SESSION['success'] = "Cập nhật đánh giá thành công!";
                header('Location: index.php');
                exit();
            } else {
                $error = "Lỗi hệ thống: " . $conn->error;
            }
        }
    }
}

admin_layout_start("Chỉnh sửa đánh giá", 'reviews');
?>

<style>
    .khung-trang { background: #fff; padding: 30px; border-radius: 15px; border: 1px solid #eee; }
    .o-nhap { border-radius: 10px !important; padding: 12px; }
    .nut-bam { border-radius: 25px !important; padding: 10px 30px !important; font-weight: bold; }
    .thong-tin-phu { background: #f8f9fa; border-left: 5px solid #007bff; padding: 15px; border-radius: 8px; }
    .sao-vang { color: #f1c40f; }
    .sao-xam { color: #bdc3c7; }
</style>

<div class="container">
    <div class="khung-trang shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-primary fw-bold mb-0">CHỈNH SỬA ĐÁNH GIÁ</h4>
            <a href="index.php" class="btn btn-outline-secondary rounded-pill px-3">Quay lại danh sách</a>
        </div>

        <?php if ($error != '') { ?>
            <div class="alert alert-danger border-0 shadow-sm"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST" action="">
            <div class="row">
                <div class="col-md-8">
                    <div class="thong-tin-phu mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted text-uppercase">Sách được đánh giá:</small>
                                <div class="fw-bold fs-6"><?php echo $review['book_title']; ?></div>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <small class="text-muted text-uppercase">Người đăng:</small>
                                <div class="fw-bold"><?php echo ($review['fullname'] != '' ? $review['fullname'] : $review['username']); ?></div>
                                <div class="small text-secondary"><?php echo $review['email']; ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-3">Thay đổi số sao đánh giá:</label>
                        <div class="d-flex flex-wrap gap-3">
                            <?php 
                            // Dùng vòng lặp for để tạo các mức sao
                            for($i = 1; $i <= 5; $i++) { 
                            ?>
                                <div class="form-check p-2 border rounded-3 bg-light" style="min-width: 100px;">
                                    <input class="form-check-input ms-1" type="radio" name="rating" 
                                           id="r<?php echo $i; ?>" value="<?php echo $i; ?>"
                                           <?php if($review['rating'] == $i) { echo 'checked'; } ?>>
                                    <label class="form-check-label ms-2" for="r<?php echo $i; ?>">
                                        <?php echo $i; ?> <i class="fas fa-star sao-vang"></i>
                                    </label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2">Nội dung nhận xét:</label>
                        <textarea name="comment" class="form-control o-nhap" rows="8" required><?php echo $review['comment']; ?></textarea>
                        <div class="small text-muted mt-2 text-end">Tối đa 1000 ký tự.</div>
                    </div>

                    <div class="pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-primary nut-bam shadow">
                            <i class="fas fa-save me-2"></i> LƯU THAY ĐỔI
                        </button>
                        <a href="index.php" class="btn btn-light nut-bam border">HỦY BỎ</a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 bg-light rounded-3 h-100">
                        <div class="card-body">
                            <h6 class="fw-bold text-dark"><i class="fas fa-info-circle me-2"></i>QUY TẮC SỬA ĐỔI</h6>
                            <hr>
                            <ul class="small text-muted ps-3" style="line-height: 2;">
                                <li>Ngày đăng: <b><?php echo date('d/m/Y - H:i', strtotime($review['created_at'])); ?></b></li>
                                <li>Chỉ sửa khi nội dung có từ ngữ nhạy cảm hoặc sai lệch.</li>
                                <li>Nên giữ nguyên ý kiến thực tế của khách hàng.</li>
                                <li>Không nên nâng sao ảo để lừa dối khách hàng khác.</li>
                            </ul>
                            
                            <div class="alert alert-warning border-0 small mt-4">
                                <i class="fas fa-exclamation-triangle"></i> Nếu đánh giá vi phạm nghiêm trọng chính sách, hãy sử dụng tính năng <b>Xóa</b> ở trang danh sách thay vì sửa nội dung.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php admin_layout_end(); ?>
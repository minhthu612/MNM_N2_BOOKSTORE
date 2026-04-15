<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

/* ===== LẤY ID ===== */
$id = 0;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
}

if (!$id) {
    header('Location: index.php');
    exit();
}

/* ===== STEP (CONFIRM) ===== */
$step = '';
if (isset($_GET['step'])) {
    $step = $_GET['step'];
}

/* ===== LẤY SÁCH ===== */
$book_rs = $conn->query("
    SELECT b.*, i.stock
    FROM books b
    LEFT JOIN inventory i ON b.book_id = i.book_id
    WHERE b.book_id = $id
");
$book = $book_rs->fetch_assoc();

if (!$book) {
    header('Location: index.php');
    exit();
}

/* ===== KIỂM TRA ĐƠN HÀNG ===== */
$check_rs = $conn->query("
    SELECT COUNT(*) AS count 
    FROM order_items 
    WHERE book_id = $id
");
$check_result = array();
if ($check_rs) {
    foreach ($check_rs as $row) {
        $check_result = $row;
        break;
    }
}

/* ===== XỬ LÝ XÓA ===== */
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {

    if (!empty($book['images'])) {
        $img_path = '../../uploads/books/' . $book['images'];
        if (file_exists($img_path)) {
            unlink($img_path);
        }
    }

    if ($conn->query("DELETE FROM books WHERE book_id = $id")) {
        $_SESSION['success'] = 'Xóa sách thành công!';
        header('Location: index.php');
        exit();
    } else {
        $error = 'Lỗi khi xóa: ' . $conn->error;
    }
}

admin_layout_start('Xóa sách', 'books');

/* ===== GIAO DIỆN CONFIRM GIỐNG BROWSER ===== */
if ($step === 'confirm') {
?>
    <div class="d-flex justify-content-center align-items-center" style="height:300px;">
        <div class="border rounded shadow p-4 bg-white" style="min-width:320px;">
            <div class="mb-3 fw-bold">
                localhost says
            </div>
            <div class="mb-4">
                Xóa?
            </div>
            <div class="text-end">
                <form method="POST" class="d-inline">
                    <button type="submit" name="confirm" class="btn btn-primary btn-sm">
                        OK
                    </button>
                </form>
                <a href="index.php" class="btn btn-outline-secondary btn-sm ms-2">
                    Cancel
                </a>
            </div>
        </div>
    </div>
<?php
    admin_layout_end();
    exit();
}
?>

<?php if ($error) { ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php } ?>

<!-- ===== TRANG THÔNG TIN SÁCH (TRƯỚC KHI BẤM XÓA) ===== -->
<div class="card">
    <div class="card-header bg-white border-bottom">
        <h4 class="mb-0">Xóa sách</h4>
    </div>
    <div class="card-body">

        <?php if (!empty($check_result['count']) && $check_result['count'] > 0) { ?>
            <div class="alert alert-warning">
                Sách đang tồn tại trong <strong><?php echo $check_result['count']; ?></strong> đơn hàng.
            </div>
        <?php } ?>

        <div class="row align-items-center">

            <div class="col-md-3">
                <strong><?php echo $book['title']; ?></strong>
                <div class="text-muted small"><?php echo $book['author']; ?></div>
            </div>

            <div class="col-md-2">
                <?php echo number_format($book['price']); ?> đ
            </div>

            <div class="col-md-2">
                <?php
                $stock = 0;
                if (isset($book['stock'])) {
                    $stock = $book['stock'];
                }
                ?>
                <?php if ($stock <= 0) { ?>
                    <span class="badge bg-danger"><?php echo $stock; ?></span>
                <?php } else { ?>
                    <span class="badge bg-success"><?php echo $stock; ?></span>
                <?php } ?>
            </div>

            <div class="col-md-2">
                <?php if ($stock <= 0) { ?>
                    <span class="badge bg-danger">Hết hàng</span>
                <?php } else { ?>
                    <span class="badge bg-success">Còn hàng</span>
                <?php } ?>
            </div>

            <div class="border-top pt-4 mt-4">
                <form method="POST">
                    <div class="text-center">
                        <h5 class="text-danger mb-3">
                            Bạn có chắc chắn muốn xóa vĩnh viễn sách này?
                        </h5>

                        <button type="submit" name="confirm" class="btn btn-danger">
                            XÁC NHẬN XÓA
                        </button>

                        <a href="index.php" class="btn btn-secondary">
                            HỦY
                        </a>
                    </div>
                </form>
            </div>


        </div>

    </div>
</div>

<?php admin_layout_end(); ?>

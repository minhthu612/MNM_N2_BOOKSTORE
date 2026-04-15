<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

$error = '';
$success = '';

// Xử lý POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']);
    $author      = trim($_POST['author']);
    $category_id = (int)$_POST['category_id'];
    $price       = (int)$_POST['price'];
    $discount    = (int)$_POST['discount'];
    $stock       = (int)$_POST['stock'];
    $description = trim($_POST['description']);
    $link_images = trim($_POST['link_images']);

    $image_name = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $upload_dir = '../../uploads/books/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $image_name = 'uploads/books/' . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../../' . $image_name);
        } else {
            $error = "Chỉ chấp nhận file ảnh JPG, PNG, GIF.";
        }
    }

    if (!$error) {
        $conn->begin_transaction();
        try {
            // Thêm sách
            $sql_book = "INSERT INTO books (title, author, category_id, price, discount, description, images, link_images, created_at)
                         VALUES ('$title', '$author', $category_id, $price, $discount, '$description', '$image_name', '$link_images', NOW())";
            if (!$conn->query($sql_book)) throw new Exception($conn->error);

            $new_book_id = $conn->insert_id;

            // Thêm inventory
            $sql_inv = "INSERT INTO inventory (book_id, stock, last_updated) VALUES ($new_book_id, $stock, NOW())";
            if (!$conn->query($sql_inv)) throw new Exception($conn->error);

            $conn->commit();
            $_SESSION['success'] = 'Thêm sách thành công!';
            header('Location: index.php');
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Lỗi hệ thống: ' . $e->getMessage();
        }
    }
}

// Lấy danh sách categories
$categories = [];
$categories_result = $conn->query("SELECT * FROM categories ORDER BY category_name");
if ($categories_result) {
    foreach ($categories_result as $cat) {
        $categories[] = $cat;
    }
}

admin_layout_start('Thêm sách mới', 'books');
?>

<?php if ($error) { ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php } ?>

<div class="card">
    <div class="card-header bg-white border-bottom">
        <h4 class="mb-0">THÔNG TIN SÁCH</h4>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Tên sách *</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="description" rows="5"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tác giả *</label>
                                <input type="text" class="form-control" name="author" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Danh mục *</label>
                                <select class="form-control" name="category_id" required>
                                    <option value="">-- Chọn --</option>
                                    <?php foreach ($categories as $cat) { ?>
                                        <option value="<?php echo $cat['category_id']; ?>">
                                            <?php echo $cat['category_name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Link ảnh ngoài</label>
                        <input type="url" class="form-control" name="link_images">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="row">
                        <div class="col-8">
                            <div class="mb-3">
                                <label class="form-label">Giá *</label>
                                <input type="number" class="form-control" name="price" min="0" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <label class="form-label">Giảm %</label>
                                <input type="number" class="form-control" name="discount" min="0" max="100" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số lượng nhập *</label>
                        <input type="number" class="form-control" name="stock" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ảnh bìa</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                    </div>
                </div>
            </div>

            <div class="border-top pt-4 mt-4">
                <button type="submit" class="btn btn-primary">Lưu sách</button>
                <a href="index.php" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>

<?php admin_layout_end(); ?>

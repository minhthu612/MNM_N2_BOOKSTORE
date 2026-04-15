<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

/* ===== LẤY ID TỪ URL ===== */
$id = 0;
if (isset($_GET['book_id'])) { 
    $id = intval($_GET['book_id']); 
} elseif (isset($_GET['id'])) { 
    $id = intval($_GET['id']); 
}

if ($id <= 0) {
    header('Location: index.php');
    exit();
}

/* ===== LẤY DỮ LIỆU SÁCH VÀ KHO (STOCK) ===== */
$book_rs = $conn->query("
    SELECT b.*, i.stock 
    FROM books b
    LEFT JOIN inventory i ON b.book_id = i.book_id
    WHERE b.book_id = $id
");

$book = [];
if ($book_rs && $book_rs->num_rows > 0) {
    $book = $book_rs->fetch_assoc();
}

if (empty($book)) {
    header('Location: index.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = $_POST['title'];
    $author      = $_POST['author'];
    $category_id = intval($_POST['category_id']);
    $price       = intval($_POST['price']);
    $discount    = intval($_POST['discount']);
    $stock       = intval($_POST['stock']);
    $description = $_POST['description'];
    $link_images = $_POST['link_images'];

    /* ===== XỬ LÝ ẢNH (GIỮ LẠI ẢNH CŨ NẾU KHÔNG UPLOAD MỚI) ===== */
    $image_name = $book['images']; // Gán tên ảnh hiện tại từ Database
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        $file_parts = explode('.', $_FILES['image']['name']);
        $ext = strtolower(end($file_parts));
        
        if (in_array($ext, $allowed)) {
            // Xóa ảnh cũ trên server nếu có
            if ($image_name != '' && file_exists('../../' . $image_name)) {
                unlink('../../' . $image_name);
            }
            // Tạo tên ảnh mới
            $image_name = 'uploads/books/' . time() . '_' . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], '../../' . $image_name);
        }
    }

    /* ===== CẬP NHẬT DATABASE (Bỏ status để tránh lỗi Unknown column) ===== */
    $sql = "UPDATE books SET
            title = '$title',
            author = '$author',
            category_id = '$category_id',
            price = '$price',
            discount = '$discount',
            description = '$description',
            images = '$image_name',
            link_images = '$link_images'
            WHERE book_id = $id";

    if ($conn->query($sql)) {
        // Cập nhật số lượng kho (Inventory)
        $check_inv = $conn->query("SELECT * FROM inventory WHERE book_id = $id");
        if ($check_inv && $check_inv->num_rows > 0) {
            $conn->query("UPDATE inventory SET stock = $stock, last_updated = NOW() WHERE book_id = $id");
        } else {
            $conn->query("INSERT INTO inventory (book_id, stock, last_updated) VALUES ($id, $stock, NOW())");
        }

        $_SESSION['success'] = "Cập nhật thành công!";
        header('Location: index.php');
        exit();
    } else {
        $error = "Lỗi SQL: " . $conn->error;
    }
}

/* ===== LẤY DANH SÁCH DANH MỤC ĐỂ HIỆN THỊ ===== */
$categories = [];
$categories_result = $conn->query("SELECT * FROM categories ORDER BY category_name");
if ($categories_result) {
    while ($cat = $categories_result->fetch_assoc()) {
        $categories[] = $cat;
    }
}

admin_layout_start('Sửa sách: ' . $book['title'], 'books');

if ($error != '') { echo '<div class="alert alert-danger">' . $error . '</div>'; }
?>

<div class="card">
    <div class="card-header bg-white border-bottom">
        <h4 class="mb-0">CHỈNH SỬA THÔNG TIN SÁCH</h4>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Tên sách *</label>
                        <input type="text" class="form-control" name="title" value="<?php echo $book['title']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="description" rows="5"><?php echo $book['description']; ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Tác giả *</label>
                            <input type="text" class="form-control" name="author" value="<?php echo $book['author']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Danh mục *</label>
                            <select class="form-select" name="category_id" required>
                                <?php foreach ($categories as $cat) { 
                                    $selected = ($cat['category_id'] == $book['category_id']) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $cat['category_id']; ?>" <?php echo $selected; ?>>
                                        <?php echo $cat['category_name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Link ảnh ngoài (URL)</label>
                        <input type="url" class="form-control" name="link_images" value="<?php echo $book['link_images']; ?>">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 bg-light rounded shadow-sm">
                        <div class="row">
                            <div class="col-8">
                                <label class="form-label">Giá bán *</label>
                                <input type="number" class="form-control" name="price" value="<?php echo $book['price']; ?>" required>
                            </div>
                            <div class="col-4">
                                <label class="form-label">Giảm %</label>
                                <input type="number" class="form-control" name="discount" value="<?php echo $book['discount']; ?>">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Số lượng kho *</label>
                            <input type="number" class="form-control" name="stock" value="<?php echo isset($book['stock']) ? $book['stock'] : 0; ?>" required>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Thay đổi ảnh bìa</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <small class="text-muted">Bỏ qua nếu muốn giữ ảnh cũ</small>
                        </div>
                        
                        <div class="mt-3 text-center">
                            <?php 
                            $img_display = ''; 
                            if ($book['link_images'] != '') {
                                $img_display = '' . $book['link_images'];
                            } elseif ($book['link_images'] != '') {
                                $img_display = $book['link_images'];
                            }

                            if ($img_display != '') {
                            ?>
                                <label class="d-block mb-2 small">Ảnh hiện tại:</label>
                                <img src="<?php echo $img_display; ?>" style="max-height: 200px; max-width: 100%; border: 1px solid #ddd;">
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-top pt-4 mt-4 d-flex justify-content-between">
                <div>
                    <button type="submit" class="btn btn-primary px-4">LƯU THAY ĐỔI</button>
                    <a href="index.php" class="btn btn-secondary px-4">HỦY</a>
                </div>
                <a href="delete.php?id=<?php echo $book['book_id']; ?>" class="btn btn-outline-danger" onclick="return confirm('Bạn chắc chắn muốn xóa sách này?')">XÓA SÁCH</a>
            </div>
        </form>
    </div>
</div>

<?php admin_layout_end(); ?>
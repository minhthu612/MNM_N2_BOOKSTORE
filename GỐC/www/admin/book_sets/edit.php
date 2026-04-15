<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

// 1. Lấy ID bộ sách từ URL (Dùng IF tường minh)
$set_id = 0;
if (isset($_GET['set_id'])) {
    $set_id = (int)$_GET['set_id'];
} else {
    if (isset($_GET['id'])) {
        $set_id = (int)$_GET['id'];
    }
}

if ($set_id == 0) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

// Lấy thông tin bộ sách hiện tại bằng query trần
$res_set = $conn->query("SELECT * FROM book_sets WHERE set_id = '$set_id'");
$book_set = $res_set->fetch_assoc();

if ($book_set == null) {
    header('Location: index.php');
    exit();
}

// 2. Xử lý dữ liệu gửi lên (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // A. Xử lý ảnh bìa (Dùng lại ảnh cũ hoặc upload mới)
    $image_name = $book_set['images']; 
    if (isset($_FILES['image_file'])) {
        if ($_FILES['image_file']['error'] == 0) {
            $file_name = $_FILES['image_file']['name'];
            $file_ext_arr = explode('.', $file_name);
            $ext = strtolower(end($file_ext_arr));
            $allowed = array('jpg', 'jpeg', 'png', 'gif', 'webp');
            
            if (in_array($ext, $allowed)) {
                $upload_dir = '../../uploads/book_sets/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                // Xóa ảnh cũ nếu có
                if ($image_name != '') {
                    if (file_exists('../../' . $image_name)) {
                        unlink('../../' . $image_name);
                    }
                }
                $image_name = 'uploads/book_sets/set_' . time() . '_' . $file_name;
                move_uploaded_file($_FILES['image_file']['tmp_name'], '../../' . $image_name);
            }
        }
    }

    // B. Xóa sách khỏi bộ (Dùng foreach)
    if (isset($_POST['remove_items'])) {
        $remove_ids = $_POST['remove_items'];
        foreach ($remove_ids as $bid) {
            $conn->query("DELETE FROM book_set_items WHERE set_id = '$set_id' AND book_id = '$bid'");
        }
    }

    // C. Cập nhật số lượng và thêm sách mới (Dùng foreach và nối chuỗi)
    foreach ($_POST as $key => $val) {
        if (strpos($key, 'quantity_') === 0) {
            $bid = (int)str_replace('quantity_', '', $key);
            $qty = (int)$val;
            $conn->query("UPDATE book_set_items SET quantity = '$qty' WHERE set_id = '$set_id' AND book_id = '$bid'");
        }
    }

    if (isset($_POST['new_book_ids'])) {
        $new_book_ids = $_POST['new_book_ids'];
        foreach ($new_book_ids as $new_id) {
            $new_qty = 1;
            if (isset($_POST['new_quantity_' . $new_id])) {
                $new_qty = (int)$_POST['new_quantity_' . $new_id];
            }
            $conn->query("INSERT IGNORE INTO book_set_items (set_id, book_id, quantity) VALUES ('$set_id', '$new_id', '$new_qty')");
        }
    }

    // D. Cập nhật thông tin chính (Query nối chuỗi trực tiếp)
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $discount = $_POST['discount'];
    $link_images = $_POST['link_images'];
    
    $sql_update = "UPDATE book_sets SET 
                   name = '$name', 
                   description = '$description', 
                   images = '$image_name', 
                   link_images = '$link_images', 
                   price = '$price', 
                   discount = '$discount' 
                   WHERE set_id = '$set_id'";
    
    if ($conn->query($sql_update)) {
        $_SESSION['success'] = "Đã cập nhật bộ sách thành công!";
        header("Location: edit.php?set_id=$set_id");
        exit();
    } else {
        $error = "Lỗi cập nhật: " . $conn->error;
    }
}

// 3. Lấy dữ liệu hiển thị (Đổ vào mảng để dùng foreach)
$res_items = $conn->query("SELECT bsi.*, b.title, b.author, b.price, b.link_images, c.category_name 
                           FROM book_set_items bsi 
                           JOIN books b ON bsi.book_id = b.book_id 
                           LEFT JOIN categories c ON b.category_id = c.category_id
                           WHERE bsi.set_id = '$set_id'");
$items_in_set = array();
while ($row = $res_items->fetch_assoc()) {
    $items_in_set[] = $row;
}

$res_all = $conn->query("SELECT b.book_id, b.title, b.author, b.price, b.link_images, c.category_name 
                         FROM books b 
                         LEFT JOIN categories c ON b.category_id = c.category_id
                         WHERE b.book_id NOT IN (SELECT book_id FROM book_set_items WHERE set_id = '$set_id') 
                         ORDER BY b.title");
$books_to_add = array();
while ($row = $res_all->fetch_assoc()) {
    $books_to_add[] = $row;
}

admin_layout_start('Sửa bộ sách: ' . $book_set['name'], 'book_sets');
?>

<style>
    .khung-anh { border: 1px solid #ddd; padding: 5px; background: #f9f9f9; max-height: 200px; }
    .bang-cuon { max-height: 300px; overflow-y: auto; border: 1px solid #eee; }
    .the-label { font-weight: bold; margin-bottom: 5px; display: block; }
</style>

<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between">
            <h5 class="mb-0">CHỈNH SỬA THÔNG TIN BỘ SÁCH</h5>
            <a href="index.php" class="btn btn-sm btn-light">Quay lại</a>
        </div>
        <div class="card-body">
            
            <?php if ($error != '') { ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>
            
            <?php if (isset($_SESSION['success'])) { ?>
                <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php } ?>

            <form method="POST" action="" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="the-label">Tên bộ sách *</label>
                            <input type="text" class="form-control" name="name" value="<?php echo $book_set['name']; ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="the-label">Giá Combo (VNĐ)</label>
                                <input type="number" class="form-control" name="price" value="<?php echo $book_set['price']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="the-label">Giảm giá (%)</label>
                                <input type="number" class="form-control" name="discount" value="<?php echo $book_set['discount']; ?>">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="the-label">Mô tả</label>
                            <textarea class="form-control" name="description" rows="4"><?php echo $book_set['description']; ?></textarea>
                        </div>
                        <div class="mt-3">
                            <label class="the-label">Link ảnh ngoài</label>
                            <input type="text" class="form-control" name="link_images" value="<?php echo $book_set['link_images']; ?>">
                        </div>
                    </div>

                    <div class="col-md-4 text-center">
                        <label class="the-label">Ảnh bìa hiện tại</label>
                        <?php 
                            $anh_hien_tai = $book_set['link_images'];
                            if ($book_set['link_images'] != '') { $anh_hien_tai = '' . $book_set['link_images']; }
                            if ($anh_hien_tai == '') { $anh_hien_tai = 'https://via.placeholder.com/150x200'; }
                        ?>
                        <img src="<?php echo $anh_hien_tai; ?>" class="khung-anh mb-2">
                        <input type="file" class="form-control mt-2" name="image_file">
                        <small class="text-muted">Chọn file nếu muốn thay đổi ảnh</small>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="text-danger">1. DANH SÁCH SÁCH TRONG BỘ (Tích chọn để Xóa)</h5>
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">Xóa</th>
                            <th width="70">Ảnh</th>
                            <th>Tên sách</th>
                            <th width="150">Số lượng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($items_in_set) > 0) { ?>
                            <?php foreach ($items_in_set as $item) { ?>
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="remove_items[]" value="<?php echo $item['book_id']; ?>">
                                    </td>
                                    <td><img src="<?php echo $item['link_images']; ?>" width="40"></td>
                                    <td>
                                        <strong><?php echo $item['title']; ?></strong><br>
                                        <small><?php echo number_format($item['price']); ?>đ</small>
                                    </td>
                                    <td>
                                        <input type="number" name="quantity_<?php echo $item['book_id']; ?>" class="form-control form-control-sm" value="<?php echo $item['quantity']; ?>" min="1">
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr><td colspan="4" class="text-center text-muted">Bộ sách đang trống.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>

                <h5 class="text-success mt-4">2. THÊM SÁCH KHÁC VÀO BỘ</h5>
                <div class="bang-cuon">
                    <table class="table table-bordered align-middle table-hover">
                        <thead class="table-success">
                            <tr>
                                <th width="50">Chọn</th>
                                <th width="70">Ảnh</th>
                                <th>Tên sách</th>
                                <th width="150">Số lượng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($books_to_add) > 0) { ?>
                                <?php foreach ($books_to_add as $b) { ?>
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" name="new_book_ids[]" value="<?php echo $b['book_id']; ?>">
                                        </td>
                                        <td>
                                            <?php 
                                                $img_path = (!empty($b['link_images'])) ? $b['link_images'] : 'https://via.placeholder.com/40x60';
                                            ?>
                                            <img src="<?php echo $img_path; ?>" width="40" alt="book">
                                        </td>
                                        <td>
                                            <strong><?php echo $b['title']; ?></strong><br>
                                            <small class="text-muted"><?php echo number_format($b['price']); ?>đ</small>
                                        </td>
                                        <td>
                                            <input type="number" name="new_quantity_<?php echo $b['book_id']; ?>" 
                                                class="form-control form-control-sm" value="1" min="1">
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Không còn sách nào khác để thêm.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary btn-lg px-5">LƯU TẤT CẢ THAY ĐỔI</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php admin_layout_end(); ?>
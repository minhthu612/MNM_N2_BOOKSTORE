<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

$set_id = $_GET['set_id'] ?? 0;
$set_id = intval($set_id);

if (isset($_POST['remove_item'])) {
    $book_id_to_remove = intval($_POST['remove_item']);
    
    // Dùng cả 2 ID để xóa đúng cuốn sách của đúng bộ sách đó
    $delete_sql = "DELETE FROM book_set_items WHERE set_id = ? AND book_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    
    // $set_id lấy từ đầu file (GET), $book_id_to_remove lấy từ POST
    $delete_stmt->bind_param("ii", $set_id, $book_id_to_remove); 
    
    if ($delete_stmt->execute()) {
        $_SESSION['success'] = "Đã xóa sách khỏi bộ!";
        header("Location: items.php?set_id=$set_id");
        exit();
    }
}

// Lấy thông tin bộ sách
$set_sql = "SELECT * FROM book_sets WHERE set_id = ?";
$set_stmt = $conn->prepare($set_sql);
$set_stmt->bind_param("i", $set_id);
$set_stmt->execute();
$book_set = $set_stmt->get_result()->fetch_assoc();

if (!$book_set) {
    header('Location: index.php');
    exit();
}

// Xử lý thêm sách vào bộ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_books'])) {
        $book_ids = $_POST['book_ids'] ?? [];
        foreach ($book_ids as $book_id) {
            $quantity = $_POST['quantity_' . $book_id] ?? 1;
            // Kiểm tra xem sách đã có trong bộ chưa
            $check_sql = "SELECT * FROM book_set_items WHERE set_id = ? AND book_id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("ii", $set_id, $book_id);
            $check_stmt->execute();
            
            if ($check_stmt->get_result()->num_rows == 0) {
                $insert_sql = "INSERT INTO book_set_items (set_id, book_id, quantity) VALUES (?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("iii", $set_id, $book_id, $quantity);
                $insert_stmt->execute();
            }
        }
        $_SESSION['success'] = "Đã thêm sách vào bộ!";
        header("Location: items.php?set_id=$set_id");
        exit();
    }
    
    // Xóa sách khỏi bộ
    if (isset($_POST['remove_item'])) {
        $item_id = $_POST['remove_item'];
        $delete_sql = "DELETE FROM book_set_items WHERE item_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $item_id);
        $delete_stmt->execute();
        $_SESSION['success'] = "Đã xóa sách khỏi bộ!";
        header("Location: items.php?set_id=$set_id");
        exit();
    }
}

// Lấy sách trong bộ
// Lấy sách trong bộ (ĐÃ XÓA b.stock)
$items_sql = "SELECT bsi.*, b.title, b.author, b.price, b.link_images 
              FROM book_set_items bsi 
              JOIN books b ON bsi.book_id = b.book_id 
              WHERE bsi.set_id = ? 
              ORDER BY b.title";
$items_stmt = $conn->prepare($items_sql);
$items_stmt->bind_param("i", $set_id);
$items_stmt->execute();
$items = $items_stmt->get_result();

// Lấy danh sách sách chưa có trong bộ
$excluded_books = [];
while($item = $items->fetch_assoc()) {
    $excluded_books[] = $item['book_id'];
}
$items->data_seek(0); // Reset pointer

$exclude_sql = "";
if (!empty($excluded_books)) {
    $placeholders = implode(',', array_fill(0, count($excluded_books), '?'));
    $exclude_sql = "WHERE book_id NOT IN ($placeholders)";
}

$all_books_sql = "SELECT book_id, title, author, price, link_images FROM books $exclude_sql ORDER BY title";
$all_books_stmt = $conn->prepare($all_books_sql);
if (!empty($excluded_books)) {
    $types = str_repeat('i', count($excluded_books));
    $all_books_stmt->bind_param($types, ...$excluded_books);
}
$all_books_stmt->execute();
$all_books = $all_books_stmt->get_result();

// Tính tổng giá
$total_price = 0;
$total_books = 0;
$items->data_seek(0); // Reset pointer
while($item = $items->fetch_assoc()) {
    $total_price += $item['price'] * $item['quantity'];
    $total_books += $item['quantity'];
}
$items->data_seek(0); // Reset pointer

admin_layout_start('Quản lý sách trong bộ: ' . $book_set['name'], 'book_sets');
?>

<!-- Thông báo -->
<?php if (isset($_SESSION['success'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['success']); endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0"><i class="fas fa-list"></i> SÁCH TRONG BỘ: <?php echo htmlspecialchars($book_set['name']); ?></h5>
            <small class="text-muted">
                Tổng: <?php echo $total_books; ?> sách • 
                Giá gốc: <?php echo number_format($total_price); ?> đ • 
                Giá bán: <?php 
                    $sale_price = $total_price * (100 - $book_set['discount']) / 100;
                    echo number_format($sale_price); 
                ?> đ
                <?php if($book_set['discount'] > 0): ?>
                <span class="badge bg-success">-<?php echo $book_set['discount']; ?>%</span>
                <?php endif; ?>
            </small>
        </div>
        <div>
            <a href="edit.php?set_id=<?php echo $set_id; ?>" class="btn btn-outline-primary">
                <i class="fas fa-edit"></i> Sửa bộ sách
            </a>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Danh sách sách hiện có trong bộ -->
        <div class="mb-5">
            <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-book"></i> SÁCH HIỆN CÓ TRONG BỘ</h6>
            
            <?php if ($items->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50">STT</th>
                                <th width="80">Ảnh</th>
                                <th>Tên sách</th>
                                <th width="120">Tác giả</th>
                                <th width="120">Giá</th>
                                <th width="100">Số lượng</th>
                                <th width="100">Tồn kho</th>
                                <th width="120">Thành tiền</th>
                                <th width="80">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $stt = 1; while($item = $items->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $stt++; ?></td>
                                    <td>
                                        <img src="<?php echo $item['link_images']; ?>" 
                                             width="50" height="60"
                                             onerror="this.src='https://via.placeholder.com/50x60?text=No+Image'"
                                             style="object-fit: cover;">
                                    </td>
                                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                                    <td><?php echo htmlspecialchars($item['author']); ?></td>
                                    <td><?php echo number_format($item['price']); ?> đ</td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>
                                        <?php 
                                        // Dùng ?? 0 để nếu không có cột stock vẫn hiện số 0, không báo lỗi
                                        $stock_display = $item['stock'] ?? 0; 
                                        ?>
                                        <span class="badge bg-<?php echo $stock_display > 0 ? 'success' : 'danger'; ?>">
                                            <?php echo $stock_display; ?>
                                        </span>
                                    </td>
                                    <td class="fw-bold">
                                        <?php echo number_format($item['price'] * $item['quantity']); ?> đ
                                    </td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="remove_item" value="<?php echo $item['book_id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Xóa sách này khỏi bộ?')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <tr class="table-light">
                                <td colspan="7" class="text-end fw-bold">Tổng cộng:</td>
                                <td colspan="2" class="fw-bold text-primary">
                                    <?php echo number_format($total_price); ?> đ
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <h5>Chưa có sách nào trong bộ</h5>
                    <p>Hãy thêm sách vào bộ từ danh sách bên dưới</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Thêm sách mới vào bộ -->
        <div class="mt-5">
            <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-plus-circle"></i> THÊM SÁCH MỚI VÀO BỘ</h6>
            
            <?php if ($all_books->num_rows > 0): ?>
                <form method="POST">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th width="50">Chọn</th>
                                    <th width="80">Ảnh</th>
                                    <th>Tên sách</th>
                                    <th width="120">Tác giả</th>
                                    <th width="120">Giá</th>
                                    <th width="100">Số lượng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($book = $all_books->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="book_ids[]" 
                                                   value="<?php echo $book['book_id']; ?>"
                                                   id="book_<?php echo $book['book_id']; ?>">
                                        </td>
                                        <td>
                                            <img src="<?php echo $book['link_images']; ?>" 
                                                 width="40" height="50"
                                                 onerror="this.src='https://via.placeholder.com/40x50?text=No+Image'"
                                                 style="object-fit: cover;">
                                        </td>
                                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                                        <td><?php echo number_format($book['price']); ?> đ</td>
                                        <td>
                                            <input type="number" name="quantity_<?php echo $book['book_id']; ?>" 
                                                   class="form-control form-control-sm" value="1" min="1" 
                                                   style="width: 70px;">
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" name="add_books" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm sách đã chọn vào bộ
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="selectAllBooks()">
                            <i class="fas fa-check-square"></i> Chọn tất cả
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="text-center py-3 text-muted">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <p>Tất cả sách đã có trong bộ hoặc không có sách nào để thêm</p>
                    <a href="../books/create.php" class="btn btn-sm btn-outline-primary">Thêm sách mới</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function selectAllBooks() {
    var checkboxes = document.querySelectorAll('input[name="book_ids[]"]');
    var allChecked = true;
    
    // Kiểm tra xem tất cả đã được chọn chưa
    checkboxes.forEach(function(checkbox) {
        if (!checkbox.checked) allChecked = false;
    });
    
    // Nếu tất cả đã chọn thì bỏ chọn hết, ngược lại thì chọn hết
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = !allChecked;
    });
}
</script>

<?php admin_layout_end(); ?>
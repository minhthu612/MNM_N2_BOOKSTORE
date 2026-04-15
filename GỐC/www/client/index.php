<?php
require_once '../includes/client_check.php';
require_once '../config.php';

$page_title = "Trang chủ Client";

// Lấy thông tin từ Session kiểu rành mạch
$user_id = '';
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

$fullname = '';
if (isset($_SESSION['fullname'])) {
    $fullname = $_SESSION['fullname'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?> - BookStore</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    /* GIỮ NGUYÊN CSS GỐC CỦA BẠN */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); min-height: 100vh; }
    .dashboard-container { padding: 20px; max-width: 1400px; margin: 0 auto; }
    .dashboard-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 25px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2); display: flex; justify-content: space-between; align-items: center; }
    .header-left h1 { font-weight: 700; margin-bottom: 5px; font-size: 1.8rem; }
    .header-left p { opacity: 0.9; margin-bottom: 0; font-size: 0.95rem; }
    .header-right { display: flex; align-items: center; gap: 15px; }
    .back-to-main-btn { background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: white; padding: 8px 20px; border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s; display: flex; align-items: center; gap: 8px; }
    .back-to-main-btn:hover { background: rgba(255, 255, 255, 0.3); transform: translateY(-2px); color: white; }
    .user-badge { background: rgba(255, 255, 255, 0.2); padding: 8px 15px; border-radius: 20px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
    .main-layout { display: grid; grid-template-columns: 250px 1fr; gap: 20px; }
    .sidebar { background: white; border-radius: 12px; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06); overflow: hidden; height: fit-content; position: sticky; top: 90px; }
    .sidebar-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; color: white; }
    .sidebar-header h3 { margin: 0; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; }
    .sidebar-menu { list-style: none; padding: 0; margin: 0; }
    .sidebar-menu li { border-bottom: 1px solid #f0f0f0; }
    .sidebar-menu a { display: flex; align-items: center; justify-content: flex-start; gap: 12px; padding: 15px 20px; color: #555; text-decoration: none; font-weight: 500; transition: all 0.25s; border-left: 3px solid transparent; }
    .sidebar-menu a:hover { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-left: 3px solid white; padding-left: 25px; }
    .logout-btn { background: #dc3545; color: white; border: none; width: 100%; padding: 12px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.3s; cursor: pointer;}
    .main-content { display: flex; flex-direction: column; gap: 20px; }
    .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .stat-card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06); transition: all 0.3s; text-align: center; }
    .stat-card:hover { transform: translateY(-5px); }
    .stat-icon { font-size: 2.5rem; margin-bottom: 15px; opacity: 0.9; }
    .stat-title { font-size: 0.95rem; color: #666; margin-bottom: 10px; font-weight: 600; }
    .stat-number { font-size: 2.2rem; font-weight: 700; margin-bottom: 15px; }
    .stat-btn { padding: 8px 20px; border-radius: 20px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s; }
    .orders-stat { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
    .wishlist-stat { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; }
    .cart-stat { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333; }
    .orders-stat .stat-btn { background: white; color: #4facfe; }
    .wishlist-stat .stat-btn { background: white; color: #fa709a; }
    .cart-stat .stat-btn { background: #667eea; color: white; }
    .recent-orders { background: white; border-radius: 12px; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06); overflow: hidden; }
    .section-header { padding: 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-bottom: 1px solid #dee2e6; }
    .section-header h3 { margin: 0; font-size: 1.3rem; color: #333; display: flex; align-items: center; gap: 10px; }
    .section-content { padding: 25px; }
    .orders-table { width: 100%; border-collapse: collapse; }
    .orders-table th { background: #f8f9ff; padding: 15px; text-align: left; font-weight: 600; color: #667eea; border-bottom: 2px solid #dee2e6; }
    .orders-table td { padding: 15px; border-bottom: 1px solid #f0f0f0; }
    .status-badge { padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 0.8rem; min-width: 100px; text-align: center; display: inline-block; }
    .status-pending { background: #ffd54f; color: #333; }
    .status-completed { background: #81c784; color: white; }
    .status-cancelled { background: #e57373; color: white; }
    .view-btn { padding: 8px 16px; border-radius: 20px; font-weight: 600; text-decoration: none; border: 1px solid #667eea; color: #667eea; transition: 0.3s; }
    .view-btn:hover { background: #667eea; color: white; }
    .empty-state { text-align: center; padding: 40px 20px; color: #999; }
    .empty-state i { font-size: 4rem; margin-bottom: 20px; opacity: 0.3; }
    .mini-footer { text-align: center; margin-top: 30px; padding: 15px; color: #666; font-size: 0.9rem; border-top: 1px solid #dee2e6; }
    </style>
</head>
<body>

<div class="dashboard-container">

    <div class="dashboard-header">
        <div class="header-left">
            <h1>Xin chào, <?php echo $fullname; ?>! 👋</h1>
            <p>Chúc bạn một ngày mua sắm vui vẻ</p>
        </div>
        <div class="header-right">
            <a href="../index.php" class="back-to-main-btn"><i class="fas fa-home"></i> Trang chính</a>
            <div class="user-badge"><i class="fas fa-user"></i> Khách hàng</div>
        </div>
    </div>

    <div class="main-layout">
        <div class="sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-user-circle"></i> Menu Client</h3>
            </div>
            <ul class="sidebar-menu">
                <li><a href="profile.php"><i class="fas fa-user"></i> Hồ sơ</a></li>
                <li><a href="cart/"><i class="fas fa-shopping-cart"></i> Giỏ hàng</a></li>
                <li><a href="orders/"><i class="fas fa-clipboard-list"></i> Đơn hàng</a></li>
                <li><a href="wishlist/"><i class="fas fa-heart"></i> Yêu thích</a></li>
                <li>
                    <form action="../auth/logout.php" method="POST">
                        <button type="submit" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i> Đăng xuất
                        </button>
                    </form>
                </li>
            </ul>
        </div>

        <div class="main-content">
            <?php
            // ================== THỐNG KÊ (PHP TRẦN) ==================
            $orders_total = 0;
            $res_orders = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE user_id = '$user_id'");
            if ($res_orders) {
                $row = $res_orders->fetch_assoc();
                $orders_total = $row['total'];
            }

            $wishlist_total = 0;
            $res_wish = $conn->query("SELECT COUNT(*) AS total FROM wishlist WHERE user_id = '$user_id'");
            if ($res_wish) {
                $row = $res_wish->fetch_assoc();
                $wishlist_total = $row['total'];
            }

            $cart_total = 0;
            $res_cart = $conn->query("SELECT COUNT(*) AS total FROM cart_items ci JOIN cart c ON ci.cart_id = c.cart_id WHERE c.user_id = '$user_id'");
            if ($res_cart) {
                $row = $res_cart->fetch_assoc();
                $cart_total = $row['total'];
            }
            ?>

            <div class="stats-grid">
                <div class="stat-card orders-stat">
                    <div class="stat-icon"><i class="fas fa-shopping-bag"></i></div>
                    <div class="stat-title">Tổng đơn hàng</div>
                    <div class="stat-number"><?php echo $orders_total; ?></div>
                    <a href="orders/" class="stat-btn shadow-sm">Xem chi tiết</a>
                </div>
                <div class="stat-card wishlist-stat">
                    <div class="stat-icon"><i class="fas fa-heart"></i></div>
                    <div class="stat-title">Sách yêu thích</div>
                    <div class="stat-number"><?php echo $wishlist_total; ?></div>
                    <a href="wishlist/" class="stat-btn shadow-sm">Xem danh sách</a>
                </div>
                <div class="stat-card cart-stat">
                    <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
                    <div class="stat-title">Sản phẩm trong giỏ</div>
                    <div class="stat-number"><?php echo $cart_total; ?></div>
                    <a href="cart/" class="stat-btn shadow-sm">Vào giỏ hàng</a>
                </div>
            </div>

            <div class="recent-orders">
                <div class="section-header">
                    <h3><i class="fas fa-history"></i> Đơn hàng vừa đặt</h3>
                </div>

                <div class="section-content">
                    <?php
                    // Lấy dữ liệu vào mảng trước khi dùng foreach (Đúng chất logic SV)
                    $orders_list = array();
                    $res_recent = $conn->query("SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC LIMIT 5");
                    
                    if ($res_recent) {
                        while ($dong = $res_recent->fetch_assoc()) {
                            $orders_list[] = $dong;
                        }
                    }

                    if (count($orders_list) > 0) {
                    ?>
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders_list as $item) { 
                                    // Xử lý Class màu sắc trạng thái kiểu IF rành mạch
                                    $class_tt = 'status-pending';
                                    $ten_tt = 'Chờ xử lý';
                                    
                                    if ($item['status'] == 'completed') {
                                        $class_tt = 'status-completed';
                                        $ten_tt = 'Hoàn thành';
                                    } else {
                                        if ($item['status'] == 'cancelled') {
                                            $class_tt = 'status-cancelled';
                                            $ten_tt = 'Đã hủy';
                                        }
                                    }
                                ?>
                                    <tr>
                                        <td class="fw-bold text-primary">#<?php echo $item['order_id']; ?></td>
                                        <td class="small"><?php echo date('d/m/Y', strtotime($item['created_at'])); ?></td>
                                        <td class="fw-bold"><?php echo number_format($item['total_amount'], 0, ',', '.'); ?>đ</td>
                                        <td>
                                            <span class="status-badge <?php echo $class_tt; ?>">
                                                <?php echo $ten_tt; ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a class="view-btn shadow-sm" href="orders/detail.php?id=<?php echo $item['order_id']; ?>"> Chi tiết </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                        <div class="empty-state">
                            <i class="fas fa-shopping-bag"></i>
                            <h4>Bạn chưa có đơn hàng nào</h4>
                            <p class="small text-muted mb-4">Hãy bắt đầu hành trình mua sắm ngay bây giờ!</p>
                            <a href="../index.php" class="btn btn-primary rounded-pill px-5">MUA SẮM NGAY</a>
                        </div>
                    <?php } ?>
                </div>
            </div>

        </div>
    </div>

    <div class="mini-footer">
        © <?php echo date('Y'); ?> <b>BookStore System</b> - Phiên bản dành cho Khách hàng
    </div>

</div>

</body>
</html>
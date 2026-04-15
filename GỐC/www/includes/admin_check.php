<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Kiểm tra đăng nhập (Viết kiểu SV rành mạch)
function require_admin() {
    if (isset($_SESSION['user_id']) == false) {
        $_SESSION['error'] = "Bạn cần đăng nhập để vào trang này!";
        header("Location: ../auth/login.php");
        exit();
    } else {
        $role = $_SESSION['role'];
        if ($role != 'Admin' && $role != 'Manager') {
            $_SESSION['error'] = "Bạn không có quyền quản trị!";
            header("Location: ../auth/login.php");
            exit();
        }
    }
    
    // Trả về mảng thông tin (Xử lý fullname thủ công)
    $f_name = $_SESSION['username'];
    if (isset($_SESSION['fullname'])) {
        $f_name = $_SESSION['fullname'];
    }
    
    return [
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'fullname' => $f_name,
        'role' => $_SESSION['role']
    ];
}

// 2. Bắt đầu Layout
function admin_layout_start($page_title, $active_menu = 'books') {
    $admin = require_admin();
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $page_title; ?> - Quản lý cửa hàng</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <style>
            body { background-color: #f4f6f9; font-family: 'Segoe UI', Arial, sans-serif; }
            
            /* Sidebar kiểu sinh viên hay làm: Cố định bên trái */
            .thanh-ben {
                width: 250px;
                height: 100vh;
                background: #2c3e50;
                position: fixed;
                left: 0;
                top: 0;
                color: white;
                padding-top: 20px;
                z-index: 1000;
            }
            .logo-admin {
                padding: 0 20px 20px;
                border-bottom: 1px solid #455a64;
                margin-bottom: 20px;
                text-align: center;
            }
            .menu-item {
                padding: 12px 25px;
                display: block;
                color: #bdc3c7;
                text-decoration: none;
                transition: 0.3s;
                border-left: 4px solid transparent;
            }
            .menu-item:hover {
                background: #34495e;
                color: white;
            }
            .menu-item.active {
                background: #3498db;
                color: white;
                border-left: 4px solid #fff;
            }
            .menu-item i { width: 25px; }

            /* Nội dung chính */
            .noi-dung-chinh {
                margin-left: 250px;
                padding: 30px;
            }
            .tieu-de-trang {
                background: white;
                padding: 20px;
                border-radius: 10px;
                margin-bottom: 30px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }

            /* Định dạng chung cho bảng và khung */
            .khung-trang {
                background: white;
                padding: 25px;
                border-radius: 10px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }
            .nut-bam-tron { border-radius: 20px !important; }
            
            /* Các loại Badge kiểu truyền thống */
            .nhan-status { padding: 5px 12px; border-radius: 15px; font-size: 12px; font-weight: bold; }
            
            @media (max-width: 768px) {
                .thanh-ben { width: 60px; }
                .noi-dung-chinh { margin-left: 60px; }
                .menu-item span, .logo-admin span { display: none; }
            }
        </style>
    </head>
    <body>
        <div class="thanh-ben">
            <div class="logo-admin">
                <h4 class="fw-bold"><i class="fas fa-book-open"></i> <span>ADMIN</span></h4>
                <div class="small text-info mt-2">
                    Chào, <?php echo $admin['fullname']; ?><br>
                    <span class="badge bg-warning text-dark mt-1"><?php echo $admin['role']; ?></span>
                </div>
            </div>
            
            <nav>
                <a href="../books/index.php" class="menu-item <?php if($active_menu == 'books') echo 'active'; ?>">
                    <i class="fas fa-book"></i> <span>Quản lý Sách</span>
                </a>
                <a href="../book_sets/index.php" class="menu-item <?php if($active_menu == 'book_sets') echo 'active'; ?>">
                    <i class="fas fa-layer-group"></i> <span>Bộ sách</span>
                </a>
                <a href="../categories/index.php" class="menu-item <?php if($active_menu == 'categories') echo 'active'; ?>">
                    <i class="fas fa-tags"></i> <span>Danh mục</span>
                </a>
                <a href="../users/index.php" class="menu-item <?php if($active_menu == 'users') echo 'active'; ?>">
                    <i class="fas fa-user-shield"></i> <span>Người dùng</span>
                </a>
                <a href="../orders/index.php" class="menu-item <?php if($active_menu == 'orders') echo 'active'; ?>">
                    <i class="fas fa-file-invoice-dollar"></i> <span>Đơn hàng</span>
                </a>
                <a href="../inventory/index.php" class="menu-item <?php if($active_menu == 'inventory') echo 'active'; ?>">
                    <i class="fas fa-warehouse"></i> <span>Kho hàng</span>
                </a>
                <a href="../reviews/index.php" class="menu-item <?php if($active_menu == 'reviews') echo 'active'; ?>">
                    <i class="fas fa-comment-dots"></i> <span>Đánh giá</span>
                </a>
                
                <div class="mt-5 border-top border-secondary pt-3">
                    <a href="../../auth/logout.php" class="menu-item text-danger" onclick="return confirm('Bạn muốn đăng xuất?')">
                        <i class="fas fa-sign-out-alt"></i> <span>Đăng xuất</span>
                    </a>
                </div>
            </nav>
        </div>

        <div class="noi-dung-chinh">
            <div class="tieu-de-trang d-flex justify-content-between align-items-center">
                <h2 class="mb-0 fw-bold text-dark"><?php echo $page_title; ?></h2>
                <div class="text-muted small"><?php echo date('d/m/Y - H:i'); ?></div>
            </div>
    <?php
}

// 3. Kết thúc Layout
function admin_layout_end() {
    ?>
        </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <script>
            // Tự động ẩn thông báo sau 3 giây cho gọn (Code đơn giản nhất)
            setTimeout(function() {
                var alerts = document.getElementsByClassName('alert');
                for (var i = 0; i < alerts.length; i++) {
                    alerts[i].style.display = 'none';
                }
            }, 3000);
        </script>
    </body>
    </html>
    <?php
}
?>
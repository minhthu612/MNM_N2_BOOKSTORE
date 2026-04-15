<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'config.php';

/* ================= SEARCH BLOCK ================= */
if (isset($_GET['q']) && !isset($_SESSION['user_id'])) {
    // SỬA TẠI ĐÂY: Thêm msg=require để khi nhảy qua trang login nó hiện thông báo
    header('Location: /auth/login.php?msg=require');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Book Store</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        html, body { height:100%; margin:0; }
        body{
            display:flex;
            flex-direction:column;
            padding-top:70px;
            font-family:Arial,sans-serif
        }

        .navbar{
            background:linear-gradient(135deg,#667eea 0%,#764ba2 100%)!important;
            box-shadow:0 4px 12px rgba(102,126,234,.2)
        }
        .navbar-brand, .nav-link{ color:#fff!important }
        .nav-link:hover{ color:#ffdd57!important }

        /* Style cho dropdown menu để icon căn đều */
        .dropdown-item i {
            width: 20px;
            text-align: center;
            margin-right: 8px;
        }

        /* ===== FOOTER ===== */
        .footer{
            background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);
            color:#fff;
            padding:30px 0;
            margin-top:auto;
            flex-shrink:0
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
<div class="container">

    <a class="navbar-brand" href="/index.php">
        <i class="fas fa-book"></i> Book Store
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">

        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <a class="nav-link" href="/index.php">
                    <i class="fas fa-home"></i> Trang chủ
                </a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    <i class="fas fa-list"></i> Danh mục
                </a>
                <ul class="dropdown-menu">
                    <?php
                    $sql_cat = "SELECT category_id, category_name FROM categories ORDER BY category_name";
                    $res_cat = mysqli_query($conn, $sql_cat);
                    while ($row = mysqli_fetch_assoc($res_cat)) {
                    ?>
                        <li>
                            <a class="dropdown-item" href="/index.php?category=<?php echo $row['category_id']; ?>">
                                <?php echo $row['category_name']; ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="/index.php?view=best_seller">
                    <i class="fas fa-fire"></i> Bán chạy
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="/index.php?view=new">
                    <i class="fas fa-newspaper"></i> Mới nhất
                </a>
            </li>
        </ul>

        <form class="d-flex me-3" method="GET" action="/search.php">
            <input class="form-control me-2" name="q" placeholder="Tìm sách...">
            <button class="btn btn-outline-light" type="submit">
                <i class="fas fa-search"></i>
            </button>
        </form>

        <ul class="navbar-nav">
        <?php if (isset($_SESSION['user_id'])) { 
            $uid = $_SESSION['user_id'];
            $user_query = mysqli_query($conn, "SELECT * FROM users WHERE user_id=$uid");
            $u = mysqli_fetch_assoc($user_query);
        ?>
            <li class="nav-item">
                <a class="nav-link" href="/client/cart/index.php">
                    <i class="fas fa-shopping-cart"></i> Giỏ hàng
                </a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle"></i>
                    <?php echo ($u['fullname'] != '') ? $u['fullname'] : $u['username']; ?>
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow">
                <?php if ($u['role'] == 'Admin' || $u['role'] == 'Manager') { ?>
                    <li>
                        <a class="dropdown-item" href="/admin/">
                            <i class="fas fa-cog text-primary"></i> <strong>Quản trị hệ thống</strong>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                <?php } ?>
                
                    <li><a class="dropdown-item" href="/client/profile.php"><i class="fas fa-user-circle text-muted"></i> Hồ sơ</a></li>
                    <li><a class="dropdown-item" href="/client/orders/index.php"><i class="fas fa-shopping-bag text-muted"></i> Đơn hàng</a></li>
                    <li><a class="dropdown-item" href="/client/wishlist/index.php"><i class="fas fa-heart text-danger"></i> Yêu thích</a></li>
                    <li><hr class="dropdown-divider"></li>
                    
                    <li>
                        <a class="dropdown-item" href="/auth/logout.php">
                            <i class="fas fa-sign-out-alt text-muted"></i> Đăng xuất
                        </a>
                    </li>
                </ul>
            </li>

        <?php } else { ?>
            <li class="nav-item">
                <a class="nav-link" href="/auth/login.php">
                    <i class="fas fa-sign-in-alt"></i> Đăng nhập
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/auth/register.php">
                    <i class="fas fa-user-plus"></i> Đăng ký
                </a>
            </li>
        <?php } ?>
        </ul>

    </div>
</div>
</nav>

<main style="flex:1 0 auto">
<div class="container mt-4">
<?php
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
    unset($_SESSION['success']);
}
?>
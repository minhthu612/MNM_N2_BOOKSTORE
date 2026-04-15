<?php
include '../config.php';

/* Nếu đã đăng nhập thì chuyển hướng */
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$error = '';

/* Xử lý đăng nhập */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($username == '' || $password == '') {
        $error = "<div class='alert alert-danger'>Vui lòng nhập đầy đủ thông tin đăng nhập.</div>";
    } else {

        // Sử dụng Prepared Statement để chống SQL Injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows == 1) {
            $user = $result->fetch_assoc();
            $is_valid = false;

            /* 1. Kiểm tra theo chuẩn mới (password_hash) */
            if (password_verify($password, $user['password_hashed'])) {
                $is_valid = true;
            } 
            /* 2. Kiểm tra theo chuẩn cũ (MD5) cho các tài khoản Admin cũ */
            elseif (md5($password) === $user['password_hashed']) {
                $is_valid = true;
                
                // TỰ ĐỘNG NÂNG CẤP: Chuyển MD5 sang password_hash để bảo mật hơn
                $new_secure_hash = password_hash($password, PASSWORD_DEFAULT);
                $u_id = $user['user_id'];
                $update_sql = "UPDATE users SET password_hashed = '$new_secure_hash' WHERE user_id = $u_id";
                $conn->query($update_sql);
            }

            if ($is_valid) {
                /* Kiểm tra trạng thái tài khoản */
                if ($user['status'] == 'Active') {
                    // Lưu Session
                    $_SESSION['user_id']  = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role']     = $user['role'];
                    $_SESSION['fullname'] = $user['fullname'];

                    /* Chuyển hướng theo role */
                    if ($user['role'] == 'Admin' || $user['role'] == 'Manager') {
                        header("Location: ../admin/");
                    } else {
                        header("Location: ../index.php");
                    }
                    exit();
                } else {
                    $error = "<div class='alert alert-warning'>Tài khoản của bạn đang bị khóa hoặc chưa kích hoạt.</div>";
                }
            } else {
                $error = "<div class='alert alert-danger'>Mật khẩu không đúng.</div>";
            }
        } else {
            $error = "<div class='alert alert-danger'>Tên đăng nhập hoặc email không tồn tại.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Book Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-container { background: white; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); padding: 40px; width: 100%; max-width: 400px; }
        .login-header { text-align: center; margin-bottom: 30px; }
        .btn-login { background-color: #3498db; border: none; color: white; padding: 10px; width: 100%; font-weight: bold; }
        .links { text-align: center; margin-top: 20px; }

        /* PHẦN CSS THÊM MỚI CHO THÔNG BÁO */
        .discovery-msg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; padding: 15px; border-radius: 12px;
            margin-bottom: 25px; display: flex; align-items: center;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            animation: fadeInDown 0.5s ease;
        }
        .discovery-msg i { font-size: 1.5rem; margin-right: 15px; }
        .discovery-msg p { margin: 0; font-size: 0.9rem; font-weight: 500; line-height: 1.4; }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2><i class="fas fa-book"></i> Book Store</h2>
            <p>Đăng nhập vào tài khoản của bạn</p>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'require'): ?>
            <div class="discovery-msg">
                <i class="fas fa-rocket"></i>
                <p>Bạn hãy đăng nhập hoặc đăng ký để bắt đầu khám phá <strong>Bookstore</strong> của chúng tôi nhé!</p>
            </div>
        <?php endif; ?>
        
        <?php if($error != '') echo $error; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Tên đăng nhập hoặc Email</label>
                <input type="text" class="form-control" name="username" required autofocus>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-login">Đăng nhập</button>
        </form>
        
        <div class="links">
            <a href="forgot_password.php">Quên mật khẩu?</a> |
            <a href="register.php">Đăng ký</a> |
            <a href="../index.php">Về trang chủ</a>
        </div>
    </div>
</body>
</html>
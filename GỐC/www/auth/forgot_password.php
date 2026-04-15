<?php
include '../config.php';


/* Đảm bảo session đã start trong config.php */


if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}


$message = '';
$error = '';
$reset_link = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
    } else {
        $email = '';
    }


    if ($email == '') {
        $error = "<div class='alert alert-danger'>Vui lòng nhập địa chỉ email.</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "<div class='alert alert-danger'>Địa chỉ email không hợp lệ.</div>";
    } else {


        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($sql);


        if ($result && $result->num_rows == 1) {


            $user = $result->fetch_assoc();


            $token = bin2hex(random_bytes(32));


            $_SESSION['reset_token'] = $token;
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_time']  = time();


            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $reset_link = $protocol . "://" . $_SERVER['HTTP_HOST'] .
                "/auth/reset_password.php?token=" . $token . "&email=" . urlencode($email);


            $message = "
            <div class='alert alert-success'>
                <h5>Yêu cầu đã được xử lý!</h5>
                <p>Email: <strong>$email</strong></p>


                <p class='mb-1'>Link đặt lại mật khẩu (demo):</p>
                <input type='text' class='form-control mb-2' value='$reset_link' readonly>


                <p class='text-muted mb-2'>
                    Link có hiệu lực trong 1 giờ.<br>
                    Nếu bạn không yêu cầu, hãy bỏ qua.
                </p>


                <a href='reset_password.php?token=$token&email=" . urlencode($email) . "' class='btn btn-primary'>
                    Đi đến trang đặt lại mật khẩu
                </a>
            </div>
            ";


        } else {
            $error = "
            <div class='alert alert-warning'>
                Email <strong>$email</strong> không tồn tại trong hệ thống.
            </div>
            ";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - Book Store</title>


    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }


        .forgot-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 500px;
            padding: 40px;
        }


        .forgot-header {
            text-align: center;
            margin-bottom: 30px;
        }


        .forgot-header i {
            font-size: 50px;
            color: #764ba2;
            margin-bottom: 15px;
        }


        .forgot-header h2 {
            color: #2c3e50;
            font-weight: bold;
        }


        .forgot-header p {
            color: #7f8c8d;
        }


        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
        }


        .links {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }


        .links a {
            color: #764ba2;
            text-decoration: none;
            margin: 0 10px;
        }


        .instruction-box {
            background-color: #f0f7ff;
            border: 1px solid #b3d7ff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>


<div class="forgot-container">
    <div class="forgot-header">
        <i class="fas fa-key"></i>
        <h2>Quên mật khẩu</h2>
        <p>Nhập email của bạn để nhận liên kết đặt lại mật khẩu</p>
    </div>


    <!-- Hiển thị lỗi -->
    <?php
    if ($error != '') {
        echo $error;
    }
    ?>


    <!-- Hiển thị thành công hoặc form -->
    <?php
    if ($message != '') {
        echo $message;
    } else {
    ?>
        <div class="instruction-box">
            <h5><i class="fas fa-info-circle"></i> Hướng dẫn:</h5>
            <ul class="mb-0">
                <li>Nhập email bạn đã đăng ký</li>
                <li>Hệ thống sẽ gửi link đặt lại mật khẩu</li>
                <li>Link có hiệu lực trong 1 giờ</li>
                <li>Kiểm tra cả Spam/Junk</li>
            </ul>
        </div>


        <form method="POST">
            <div class="mb-4">
                <label class="form-label">
                    <i class="fas fa-envelope"></i> Địa chỉ email
                </label>


                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-at"></i>
                    </span>
                    <input
                        type="email"
                        class="form-control"
                        name="email"
                        placeholder="example@email.com"
                        value="<?php if (isset($_POST['email'])) { echo $_POST['email']; } ?>"
                    >
                </div>


                <div class="form-text">
                    Email phải khớp với email bạn đã đăng ký
                </div>
            </div>


            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Gửi yêu cầu
                </button>
            </div>


            <div class="mt-3 text-center">
                <small class="text-muted">
                    Bằng việc gửi yêu cầu, bạn xác nhận đây là tài khoản của bạn.
                </small>
            </div>
        </form>
    <?php
    }
    ?>


    <div class="links">
        <a href="login.php">Đăng nhập</a> |
        <a href="register.php">Đăng ký</a> |
        <a href="../index.php">Trang chủ</a>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

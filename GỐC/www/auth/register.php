<?php
include '../config.php';


/* Nếu đã đăng nhập thì chuyển hướng */
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}


$error = '';
$success = '';


/* Xử lý đăng ký */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    if (isset($_POST['username'])) {
        $username = trim($_POST['username']);
    } else {
        $username = '';
    }


    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
    } else {
        $email = '';
    }


    if (isset($_POST['password'])) {
        $password = $_POST['password'];
    } else {
        $password = '';
    }


    if (isset($_POST['confirm_password'])) {
        $confirm_password = $_POST['confirm_password'];
    } else {
        $confirm_password = '';
    }


    if (isset($_POST['fullname'])) {
        $fullname = trim($_POST['fullname']);
    } else {
        $fullname = '';
    }


    if ($username == '' || $email == '' || $password == '' || $confirm_password == '' || $fullname == '') {


        $error = "<div class='alert alert-danger'>Vui lòng nhập đầy đủ thông tin.</div>";


    } elseif ($password != $confirm_password) {


        $error = "<div class='alert alert-danger'>Mật khẩu xác nhận không khớp.</div>";


    } elseif (strlen($password) < 6) {


        $error = "<div class='alert alert-danger'>Mật khẩu phải có ít nhất 6 ký tự.</div>";


    } else {


        /* Kiểm tra username / email đã tồn tại */
        $check_sql = "
            SELECT *
            FROM users
            WHERE username = '$username'
               OR email = '$email'
        ";


        $check_result = $conn->query($check_sql);


        if ($check_result && $check_result->num_rows > 0) {


            $error = "<div class='alert alert-danger'>Tên đăng nhập hoặc email đã tồn tại.</div>";


        } else {


            /* Mã hóa mật khẩu (đồng bộ với login dùng md5) */
            $hashed_password = md5($password);


            /* Thêm user */
            $insert_sql = "
                INSERT INTO users
                    (username, password_hashed, email, fullname, role, status, created_at)
                VALUES
                    ('$username', '$hashed_password', '$email', '$fullname', 'Customer', 'Active', NOW())
            ";


            if ($conn->query($insert_sql)) {


                $success = "<div class='alert alert-success'>Đăng ký thành công! Bạn có thể đăng nhập ngay.</div>";


            } else {


                $error = "<div class='alert alert-danger'>Đăng ký thất bại. Vui lòng thử lại.</div>";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Book Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
        }
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .register-header h2 {
            color: #2c3e50;
        }
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        .btn-register {
            background-color: #2ecc71;
            border: none;
            color: white;
            padding: 10px;
            width: 100%;
            font-weight: bold;
        }
        .btn-register:hover {
            background-color: #27ae60;
        }
        .links {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h2><i class="fas fa-book"></i> Book Store</h2>
            <p>Đăng ký tài khoản mới</p>
        </div>


        <!-- Hiển thị lỗi -->
        <?php
        if ($error != '') {
            echo $error;
        }
        ?>


        <!-- Hiển thị thành công -->
        <?php
        if ($success != '') {
            echo $success;
        }
        ?>


        <!-- Form đăng ký -->
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tên đăng nhập *</label>
                    <input
                        type="text"
                        class="form-control"
                        name="username"
                        value="<?php
                            if (isset($_POST['username'])) {
                                echo $_POST['username'];
                            }
                        ?>"
                        required
                    >
                </div>


                <div class="col-md-6 mb-3">
                    <label class="form-label">Email *</label>
                    <input
                        type="email"
                        class="form-control"
                        name="email"
                        value="<?php
                            if (isset($_POST['email'])) {
                                echo $_POST['email'];
                            }
                        ?>"
                        required
                    >
                </div>
            </div>


            <div class="mb-3">
                <label class="form-label">Họ và tên *</label>
                <input
                    type="text"
                    class="form-control"
                    name="fullname"
                    value="<?php
                        if (isset($_POST['fullname'])) {
                            echo $_POST['fullname'];
                        }
                    ?>"
                    required
                >
            </div>


            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mật khẩu *</label>
                    <input type="password" class="form-control" name="password" required>
                </div>


                <div class="col-md-6 mb-3">
                    <label class="form-label">Xác nhận mật khẩu *</label>
                    <input type="password" class="form-control" name="confirm_password" required>
                </div>
            </div>


            <div class="mb-3 form-check">
                <input
                    type="checkbox"
                    class="form-check-input"
                    name="agree"
                    value="1"
                    <?php
                        if (isset($_POST['agree'])) {
                            echo 'checked';
                        }
                    ?>
                    required
                >
                <label class="form-check-label">
                    Tôi đồng ý với <a href="#">điều khoản sử dụng</a>
                </label>
            </div>


            <button type="submit" class="btn btn-register w-100">
                Đăng ký
            </button>
        </form>


        <div class="links mt-3 text-center">
            <a href="login.php">Đã có tài khoản? Đăng nhập</a> |
            <a href="../index.php">Về trang chủ</a>
        </div>
    </div>


    <!-- Bootstrap JS (được phép) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>


</html>
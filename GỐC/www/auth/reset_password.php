<?php
include '../config.php';


/* =========================
   KIỂM TRA TOKEN & EMAIL
========================= */


$token = '';
$email = '';


if (isset($_GET['token'])) {
    $token = $_GET['token'];
}


if (isset($_GET['email'])) {
    $email = $_GET['email'];
}


if ($token == '' || $email == '') {
    $_SESSION['error'] = 'Link đặt lại mật khẩu không hợp lệ.';
    header('Location: forgot_password.php');
    exit();
}


/* =========================
   KIỂM TRA TOKEN TRONG SESSION
========================= */


if (
    !isset($_SESSION['reset_token']) ||
    !isset($_SESSION['reset_email']) ||
    $token != $_SESSION['reset_token'] ||
    $email != $_SESSION['reset_email']
) {
    $_SESSION['error'] = 'Token không hợp lệ hoặc đã hết hạn.';
    header('Location: forgot_password.php');
    exit();
}


/* =========================
   KIỂM TRA THỜI GIAN TOKEN
========================= */


if (isset($_SESSION['reset_time'])) {
    if (time() - $_SESSION['reset_time'] > 3600) {
        unset($_SESSION['reset_token'], $_SESSION['reset_email'], $_SESSION['reset_time']);
        $_SESSION['error'] = 'Token đã hết hạn. Vui lòng yêu cầu lại.';
        header('Location: forgot_password.php');
        exit();
    }
}


/* =========================
   XỬ LÝ RESET PASSWORD
========================= */


$error = '';
$password_strength_text = '';
$password_strength_class = '';
$password_tips = array();
$confirm_message = '';
$confirm_class = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    $new_password = '';
    $confirm_password = '';


    if (isset($_POST['new_password'])) {
        $new_password = $_POST['new_password'];
    }


    if (isset($_POST['confirm_password'])) {
        $confirm_password = $_POST['confirm_password'];
    }


    /* ===== validate ===== */
    if ($new_password == '' || $confirm_password == '') {
        $error = 'Vui lòng nhập đầy đủ thông tin.';
    }


    if ($error == '' && strlen($new_password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự.';
    }


    if ($error == '' && $new_password != $confirm_password) {
        $error = 'Mật khẩu xác nhận không khớp.';
        $confirm_message = '✗ Mật khẩu không khớp';
        $confirm_class = 'text-danger';
    }


    if ($error == '') {


        /* ===== đánh giá độ mạnh ===== */
        $strength = 0;


        if (strlen($new_password) >= 6) {
            $strength = $strength + 1;
        }


        $has_upper = false;
        $has_lower = false;
        $has_number = false;
        $has_special = false;


        foreach (str_split($new_password) as $ch) {


            if ($ch >= 'A' && $ch <= 'Z') {
                $has_upper = true;
            }


            if ($ch >= 'a' && $ch <= 'z') {
                $has_lower = true;
            }


            if ($ch >= '0' && $ch <= '9') {
                $has_number = true;
            }


            if (
                !($ch >= 'A' && $ch <= 'Z') &&
                !($ch >= 'a' && $ch <= 'z') &&
                !($ch >= '0' && $ch <= '9')
            ) {
                $has_special = true;
            }
        }


        if ($has_upper && $has_lower) {
            $strength = $strength + 1;
        }


        if ($has_number) {
            $strength = $strength + 1;
        }


        if ($has_special) {
            $strength = $strength + 1;
        }


        if ($strength <= 1) {
            $password_strength_text = 'Yếu';
            $password_strength_class = 'text-danger';
        }


        if ($strength == 2) {
            $password_strength_text = 'Trung bình';
            $password_strength_class = 'text-warning';
        }


        if ($strength == 3) {
            $password_strength_text = 'Tốt';
            $password_strength_class = 'text-info';
        }


        if ($strength >= 4) {
            $password_strength_text = 'Mạnh';
            $password_strength_class = 'text-success';
        }


        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);


        $sql = "
            UPDATE users
            SET password_hashed = '$hashed_password'
            WHERE email = '$email'
        ";


        if ($conn->query($sql)) {
            unset($_SESSION['reset_token'], $_SESSION['reset_email'], $_SESSION['reset_time']);
            $_SESSION['success'] = 'Đặt lại mật khẩu thành công!';
            header('Location: login.php');
            exit();
        } else {
            $error = 'Có lỗi xảy ra. Vui lòng thử lại.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đặt lại mật khẩu</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="d-flex align-items-center justify-content-center" style="height:100vh;background:linear-gradient(135deg,#667eea,#764ba2)">


<div class="reset-card bg-white p-4 rounded shadow" style="max-width:450px;width:100%">
    <h3 class="text-center mb-3"><i class="fas fa-key"></i> Đặt lại mật khẩu</h3>


    <?php if ($error != '') { ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php } ?>


    <form method="POST">
        <div class="mb-3">
            <label>Mật khẩu mới</label>
            <input type="password" name="new_password" class="form-control" required autofocus>
        </div>


        <div class="mb-3">
            <label>Xác nhận mật khẩu</label>
            <input type="password" name="confirm_password" class="form-control" required>
            <?php if ($confirm_message != '') { ?>
                <small class="<?php echo $confirm_class; ?>"><?php echo $confirm_message; ?></small>
            <?php } ?>
        </div>


        <?php if ($password_strength_text != '') { ?>
            <div class="mb-3 <?php echo $password_strength_class; ?>">
                Độ mạnh mật khẩu: <strong><?php echo $password_strength_text; ?></strong>
            </div>
        <?php } ?>


        <button class="btn btn-primary w-100">
            <i class="fas fa-save"></i> Đặt lại mật khẩu
        </button>
    </form>
</div>


</body>
</html>

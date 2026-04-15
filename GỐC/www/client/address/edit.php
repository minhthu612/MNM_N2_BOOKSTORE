<?php
require_once '../../includes/client_check.php';
require_once '../../config.php';

/* Lấy user_id từ session */
$user_id = $_SESSION['user_id'];

/* Lấy id địa chỉ */
$id = 0;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
}

/* Lấy dữ liệu địa chỉ cũ, đảm bảo thuộc về user đang đăng nhập */
$sql_select = "
    SELECT * 
    FROM addresses 
    WHERE address_id = $id 
      AND user_id = '$user_id'
";
$res = mysqli_query($conn, $sql_select);
$address = mysqli_fetch_assoc($res);

/* Nếu không tìm thấy địa chỉ → quay về checkout */
if (!$address) {
    header("Location: ../checkout/index.php");
    exit();
}

/* Xử lý khi submit form */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $fullname = '';
    if (isset($_POST['fullname'])) {
        $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    }

    $phone = '';
    if (isset($_POST['phone'])) {
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    }

    $city = '';
    if (isset($_POST['city'])) {
        $city = mysqli_real_escape_string($conn, $_POST['city']);
    }

    $district = '';
    if (isset($_POST['district'])) {
        $district = mysqli_real_escape_string($conn, $_POST['district']);
    }

    $ward = '';
    if (isset($_POST['ward'])) {
        $ward = mysqli_real_escape_string($conn, $_POST['ward']);
    }

    $street = '';
    if (isset($_POST['street'])) {
        $street = mysqli_real_escape_string($conn, $_POST['street']);
    }

    /* Cập nhật địa chỉ */
    $sql_update = "
        UPDATE addresses SET
            fullname = '$fullname',
            phone    = '$phone',
            city     = '$city',
            district = '$district',
            ward     = '$ward',
            street   = '$street'
        WHERE address_id = $id
          AND user_id = '$user_id'
    ";

    $update = mysqli_query($conn, $sql_update);

    if ($update) {
        $_SESSION['success'] = "Cập nhật địa chỉ thành công!";
        header("Location: ../checkout/index.php");
        exit();
    }
}

include '../../header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h4 class="fw-bold mb-4 text-center text-primary">
                    <i class="fas fa-edit me-2"></i>Chỉnh sửa địa chỉ
                </h4>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Họ và tên *</label>
                        <input type="text" name="fullname" class="form-control rounded-3"
                               value="<?php echo htmlspecialchars($address['fullname']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Số điện thoại *</label>
                        <input type="text" name="phone" class="form-control rounded-3"
                               value="<?php echo htmlspecialchars($address['phone']); ?>" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Tỉnh/Thành *</label>
                            <input type="text" name="city" class="form-control rounded-3"
                                   value="<?php echo htmlspecialchars($address['city']); ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Quận/Huyện *</label>
                            <input type="text" name="district" class="form-control rounded-3"
                                   value="<?php echo htmlspecialchars($address['district']); ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Phường/Xã *</label>
                            <input type="text" name="ward" class="form-control rounded-3"
                                   value="<?php echo htmlspecialchars($address['ward']); ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small">
                            Địa chỉ chi tiết (Số nhà, tên đường) *
                        </label>
                        <textarea name="street" class="form-control rounded-3" rows="3" required><?php
                            echo htmlspecialchars($address['street']);
                        ?></textarea>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <a href="../checkout/index.php"
                               class="btn btn-light w-100 rounded-pill py-2 border">
                                Hủy bỏ
                            </a>
                        </div>
                        <div class="col-6">
                            <button type="submit"
                                    class="btn btn-primary w-100 rounded-pill py-2 shadow-sm">
                                Cập nhật ngay
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?php include '../../footer.php'; ?>

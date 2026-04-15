<?php
require_once '../../includes/client_check.php';
require_once '../../config.php';

$user_id = $_SESSION['user_id'];

/* ===================== XỬ LÝ FORM ===================== */
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

    $is_default = 0;
    if (isset($_POST['is_default'])) {
        $is_default = 1;
    }

    /* NẾU CHỌN LÀM MẶC ĐỊNH → RESET CÁI CŨ */
    if ($is_default == 1) {
        $reset_sql = "UPDATE addresses 
                      SET is_default = 0 
                      WHERE user_id = '$user_id'";
        mysqli_query($conn, $reset_sql);
    }

    /* THÊM ĐỊA CHỈ */
    $insert_sql = "
        INSERT INTO addresses 
        (user_id, fullname, phone, city, district, ward, street, is_default, created_at)
        VALUES 
        ('$user_id', '$fullname', '$phone', '$city', '$district', '$ward', '$street', '$is_default', NOW())
    ";

    if (mysqli_query($conn, $insert_sql)) {
        $_SESSION['success'] = 'Thêm địa chỉ mới thành công!';
        header('Location: ../checkout/index.php');
        exit();
    }
}

include '../../header.php';
?>

<style>
.card {
    border-radius: 16px;
}

.form-control, textarea {
    border-radius: 10px;
}

.btn {
    font-weight: 600;
}
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card border-0 shadow-sm p-4">
                <h4 class="fw-bold mb-4 text-center">
                    <i class="fas fa-map-marked-alt text-primary me-2"></i>
                    Thêm địa chỉ mới
                </h4>

                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Họ và tên *</label>
                        <input type="text" name="fullname" class="form-control"
                               placeholder="Nhập tên người nhận" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Số điện thoại *</label>
                        <input type="text" name="phone" class="form-control"
                               placeholder="Ví dụ: 0912345678" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Tỉnh/Thành *</label>
                            <input type="text" name="city" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Quận/Huyện *</label>
                            <input type="text" name="district" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Phường/Xã *</label>
                            <input type="text" name="ward" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">
                            Địa chỉ chi tiết (Số nhà, tên đường) *
                        </label>
                        <textarea name="street" class="form-control" rows="2"
                                  placeholder="VD: 123 Đường ABC..." required></textarea>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="is_default" id="is_default">
                            <label class="form-check-label fw-bold small" for="is_default">
                                Đặt làm địa chỉ mặc định
                            </label>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <a href="../checkout/index.php"
                               class="btn btn-light w-100 border rounded-pill py-2">
                                Quay lại
                            </a>
                        </div>
                        <div class="col-6">
                            <button type="submit"
                                    class="btn btn-primary w-100 rounded-pill py-2">
                                Lưu địa chỉ
                            </button>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

<?php include '../../footer.php'; ?>

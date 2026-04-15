<?php
require_once '../../config.php';
require_once '../../includes/admin_check.php';

// 1. Lấy dữ liệu lọc từ GET (Viết kiểu tường minh từng biến)
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$role = '';
if (isset($_GET['role'])) {
    $role = $_GET['role'];
}

$status = '';
if (isset($_GET['status'])) {
    $status = $_GET['status'];
}

$membership = '';
if (isset($_GET['membership'])) {
    $membership = $_GET['membership'];
}

// 2. Xây dựng câu lệnh WHERE (Nối chuỗi trực tiếp kiểu SV)
$where_sql = " WHERE 1=1 ";

if ($search != '') {
    $where_sql = $where_sql . " AND (username LIKE '%$search%' OR email LIKE '%$search%' OR fullname LIKE '%$search%') ";
}

if ($role != '') {
    $where_sql = $where_sql . " AND role = '$role' ";
}

if ($status != '') {
    $where_sql = $where_sql . " AND status = '$status' ";
}

if ($membership != '') {
    $where_sql = $where_sql . " AND membership_level = '$membership' ";
}

// 3. Tính toán phân trang
$page = 1;
if (isset($_GET['page'])) {
    $page = (int)$_GET['page'];
}
if ($page < 1) { $page = 1; }

$limit = 15;
$offset = ($page - 1) * $limit;

// 4. Lấy tổng số dòng để tính trang (Query trần)
$sql_count = "SELECT COUNT(*) as total FROM users " . $where_sql;
$res_count = $conn->query($sql_count);
$row_count = $res_count->fetch_assoc();
$total = $row_count['total'];
$total_pages = ceil($total / $limit);

// 5. Lấy danh sách người dùng (Query trần nối biến trực tiếp)
$sql_main = "SELECT * FROM users $where_sql ORDER BY user_id ASC LIMIT $limit OFFSET $offset";
$res_main = $conn->query($sql_main);

$users_list = array();
if ($res_main) {
    while ($row = $res_main->fetch_assoc()) {
        $users_list[] = $row;
    }
}

// 6. Thống kê nhanh
$sql_stats = "SELECT 
        COUNT(*) as total_users,
        SUM(CASE WHEN role = 'Admin' THEN 1 ELSE 0 END) as admins,
        SUM(CASE WHEN role = 'Customer' THEN 1 ELSE 0 END) as customers,
        SUM(CASE WHEN status = 'Active' THEN 1 ELSE 0 END) as active_count
        FROM users";
$stats = $conn->query($sql_stats)->fetch_assoc();

admin_layout_start("Quản lý người dùng", 'users');
?>

<style>
    .the-bang { background: #fff; border-radius: 12px; }
    .bang-du-lieu th { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; }
    .form-control, .form-select { border-radius: 20px; }
    .stat-box { border-radius: 15px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .nut-hanh-dong {
        border-radius: 20px !important;
        padding: 5px 12px !important;
        margin: 0 2px;
        font-size: 0.8rem;
        display: inline-block;
        text-decoration: none;
    }
    .badge-tron { border-radius: 20px; padding: 5px 12px; font-size: 0.75rem; }
</style>

<div class="container-fluid">
    <div class="row g-3 mb-4 text-center">
        <div class="col-md-3">
            <div class="card stat-box bg-primary text-white p-3">
                <h3 class="mb-0 fw-bold"><?php echo $stats['total_users']; ?></h3>
                <div class="small">Người dùng</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-box bg-dark text-white p-3">
                <h3 class="mb-0 fw-bold"><?php echo $stats['admins']; ?></h3>
                <div class="small">Quản trị viên</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-box bg-info text-white p-3">
                <h3 class="mb-0 fw-bold"><?php echo $stats['customers']; ?></h3>
                <div class="small">Khách hàng</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-box bg-success text-white p-3">
                <h3 class="mb-0 fw-bold"><?php echo $stats['active_count']; ?></h3>
                <div class="small">Đang hoạt động</div>
            </div>
        </div>
    </div>

    <div class="card the-bang border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary fw-bold"><i class="fas fa-users me-2"></i>DANH SÁCH THÀNH VIÊN</h5>
            <a href="create.php" class="btn btn-success rounded-pill px-4 shadow-sm">
                <i class="fas fa-user-plus"></i> Thêm thành viên
            </a>
        </div>

        <div class="card-body">
            <form method="GET" action="" class="row g-2 mb-4">
                <div class="col-md-4">
                    <input type="text" class="form-control px-3" name="search" placeholder="Tìm tên, email..." value="<?php echo $search; ?>">
                </div>
                <div class="col-md-2">
                    <select name="role" class="form-select">
                        <option value="">-- Vai trò --</option>
                        <option value="Admin" <?php if($role == 'Admin') echo 'selected'; ?>>Admin</option>
                        <option value="Manager" <?php if($role == 'Manager') echo 'selected'; ?>>Manager</option>
                        <option value="Customer" <?php if($role == 'Customer') echo 'selected'; ?>>Customer</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">-- Trạng thái --</option>
                        <option value="Active" <?php if($status == 'Active') echo 'selected'; ?>>Hoạt động</option>
                        <option value="Inactive" <?php if($status == 'Inactive') echo 'selected'; ?>>Bị khóa</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Lọc</button>
                </div>
                <div class="col-md-2">
                    <a href="index.php" class="btn btn-outline-secondary w-100 rounded-pill">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="60">ID</th>
                            <th class="text-start">Tài khoản</th>
                            <th>Họ và tên</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th width="340">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($users_list) > 0) { ?>
                            <?php foreach ($users_list as $u) { ?>
                                <tr class="text-center">
                                    <td>#<?php echo $u['user_id']; ?></td>
                                    <td class="text-start">
                                        <div class="fw-bold"><?php echo $u['username']; ?></div>
                                        <small class="text-muted"><?php echo $u['email']; ?></small>
                                    </td>
                                    <td><?php echo $u['fullname']; ?></td>
                                    <td>
                                        <?php if($u['role'] == 'Admin') { ?>
                                            <span class="badge bg-danger badge-tron">ADMIN</span>
                                        <?php } else { ?>
                                            <span class="badge bg-info text-white badge-tron"><?php echo strtoupper($u['role']); ?></span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if($u['status'] == 'Active') { ?>
                                            <span class="badge bg-success badge-tron">HOẠT ĐỘNG</span>
                                        <?php } else { ?>
                                            <span class="badge bg-secondary badge-tron">BỊ KHÓA</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <a href="detail.php?id=<?php echo $u['user_id']; ?>" class="btn btn-info text-white nut-hanh-dong">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                        <a href="edit.php?id=<?php echo $u['user_id']; ?>" class="btn btn-warning text-dark nut-hanh-dong">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        
                                        <?php if ($u['status'] == 'Active') { ?>
                                            <a href="deactivate.php?id=<?php echo $u['user_id']; ?>" class="btn btn-outline-danger nut-hanh-dong" title="Khóa">
                                                <i class="fas fa-lock"></i> Khóa
                                            </a>
                                        <?php } else { ?>
                                            <a href="activate.php?id=<?php echo $u['user_id']; ?>" class="btn btn-outline-success nut-hanh-dong" title="Mở khóa">
                                                <i class="fas fa-unlock"></i> Mở khóa
                                            </a>
                                        <?php } ?>

                                        <a href="delete.php?id=<?php echo $u['user_id']; ?>" class="btn btn-danger text-white nut-hanh-dong">
                                            <i class="fas fa-trash"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">Không tìm thấy thành viên nào.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1) { ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                            <li class="page-item <?php if($i == $page) { echo 'active'; } ?>">
                                <a class="page-link shadow-none" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&role=<?php echo $role; ?>&status=<?php echo $status; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </nav>
            <?php } ?>
        </div>
    </div>
</div>

<?php admin_layout_end(); ?>
@extends('layouts.app')
@section('title', 'Thêm người dùng mới')
@section('content')


<style>
    .khung-nhap { background: #fff; padding: 30px; border-radius: 15px; border: 1px solid #eee; }
    .o-nhap { border-radius: 10px !important; padding: 12px; }
    .nut-bam { border-radius: 25px !important; padding: 10px 30px !important; font-weight: bold; }
    .tieu-de-phu { color: #555; border-bottom: 2px solid #007bff; display: inline-block; margin-bottom: 20px; padding-bottom: 5px; }
</style>


<div class="container">
    <div class="khung-nhap shadow-sm">


        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-primary fw-bold mb-0">THÊM THÀNH VIÊN MỚI</h4>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary rounded-pill px-3">Quay lại</a>
        </div>


        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf


            <!-- THÔNG TIN TÀI KHOẢN -->
            <h6 class="tieu-de-phu">THÔNG TIN TÀI KHOẢN</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="fw-bold mb-2">Tên đăng nhập *</label>
                    <input type="text" name="username" class="form-control o-nhap" required value="{{ old('username') }}">
                </div>
                <div class="col-md-6">
                    <label class="fw-bold mb-2">Địa chỉ Email *</label>
                    <input type="email" name="email" class="form-control o-nhap" required value="{{ old('email') }}">
                </div>
            </div>


            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="fw-bold mb-2">Mật khẩu *</label>
                    <input type="password" name="password" class="form-control o-nhap" required>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold mb-2">Xác nhận mật khẩu *</label>
                    <input type="password" name="confirm_password" class="form-control o-nhap" required>
                </div>
            </div>


            <!-- THÔNG TIN CÁ NHÂN -->
            <h6 class="tieu-de-phu">THÔNG TIN CÁ NHÂN</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="fw-bold mb-2">Họ và tên *</label>
                    <input type="text" name="fullname" class="form-control o-nhap" required value="{{ old('fullname') }}">
                </div>
                <div class="col-md-6">
                    <label class="fw-bold mb-2">Số điện thoại</label>
                    <input type="tel" name="phone" class="form-control o-nhap" value="{{ old('phone') }}">
                </div>
            </div>


            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="fw-bold mb-2">Ngày sinh</label>
                    <input type="date" name="birthdate" class="form-control o-nhap" value="{{ old('birthdate') }}">
                </div>
                <div class="col-md-4">
                    <label class="fw-bold mb-2">Giới tính</label>
                    <select name="gender" class="form-select o-nhap">
                        <option value="male" {{ old('gender')=='male'?'selected':'' }}>Nam</option>
                        <option value="female" {{ old('gender')=='female'?'selected':'' }}>Nữ</option>
                        <option value="other" {{ old('gender')=='other'?'selected':'' }}>Khác</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="fw-bold mb-2">Vai trò hệ thống</label>
                    <select name="role" class="form-select o-nhap">
                        <option value="Customer" {{ old('role')=='Customer'?'selected':'' }}>Khách hàng</option>
                        <option value="Manager" {{ old('role')=='Manager'?'selected':'' }}>Quản lý</option>
                        <option value="Admin" {{ old('role')=='Admin'?'selected':'' }}>Quản trị viên</option>
                    </select>
                </div>
            </div>


            <!-- ƯU ĐÃI -->
            <h6 class="tieu-de-phu">ƯU ĐÃI THÀNH VIÊN</h6>
            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="fw-bold mb-2">Trạng thái</label>
                    <select name="status" class="form-select o-nhap">
                        <option value="Active" {{ old('status')=='Active'?'selected':'' }}>Đang hoạt động</option>
                        <option value="Inactive" {{ old('status')=='Inactive'?'selected':'' }}>Bị khóa</option>
                        <option value="Pending" {{ old('status')=='Pending'?'selected':'' }}>Chờ duyệt</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="fw-bold mb-2">Hạng thành viên</label>
                    <select name="membership_level" class="form-select o-nhap">
                        <option value="regular" {{ old('membership_level')=='regular'?'selected':'' }}>Thường (Regular)</option>
                        <option value="gold" {{ old('membership_level')=='gold'?'selected':'' }}>Vàng (Gold)</option>
                        <option value="vip" {{ old('membership_level')=='vip'?'selected':'' }}>VIP</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="fw-bold mb-2">Điểm thưởng</label>
                    <input type="number" name="points" class="form-control o-nhap" value="{{ old('points',0) }}">
                </div>
            </div>


            <!-- BUTTON -->
            <div class="pt-4 border-top">
                <button type="submit" class="btn btn-primary nut-bam shadow">
                    <i class="fas fa-save me-2"></i> THÊM NGƯỜI DÙNG NGAY
                </button>
                <button type="reset" class="btn btn-light nut-bam border ms-2">NHẬP LẠI</button>
            </div>


        </form>
    </div>
</div>


@endsection

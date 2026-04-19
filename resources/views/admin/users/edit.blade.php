@extends('layouts.app')
@section('title', 'Sửa thông tin: ' . $user->username)
@section('content')


<style>
    .khung-sua { background-color: #ffffff; padding: 30px; border-radius: 15px; border: 1px solid #e0e0e0; }
    .o-nhap { border-radius: 10px !important; padding: 12px; }
    .nut-bam { border-radius: 25px !important; padding: 10px 30px !important; font-weight: bold; }
    .tieu-de-nho { color: #555; border-bottom: 2px solid #007bff; display: inline-block; margin-bottom: 20px; padding-bottom: 5px; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; }
</style>


<div class="container">
    <div class="khung-sua shadow-sm">


        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-primary fw-bold mb-0">CHỈNH SỬA THÔNG TIN THÀNH VIÊN</h4>
            <span class="badge rounded-pill bg-dark px-3 py-2">
                Mã số: #{{ $user->user_id }}
            </span>
        </div>


        {{-- ERROR --}}
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            </div>
        @endif


        <form method="POST" action="{{ route('admin.users.update', $user->user_id) }}">
            @csrf


            <div class="row">
                <!-- LEFT -->
                <div class="col-md-8">


                    <h6 class="tieu-de-nho">THÔNG TIN TÀI KHOẢN</h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold mb-2">Tên đăng nhập *</label>
                            <input type="text" name="username" class="form-control o-nhap"
                                   value="{{ $user->username }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold mb-2">Email liên hệ *</label>
                            <input type="email" name="email" class="form-control o-nhap"
                                   value="{{ $user->email }}" required>
                        </div>
                    </div>


                    <h6 class="tieu-de-nho">THÔNG TIN CÁ NHÂN</h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold mb-2">Họ và tên *</label>
                            <input type="text" name="fullname" class="form-control o-nhap"
                                   value="{{ $user->fullname }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold mb-2">Số điện thoại</label>
                            <input type="tel" name="phone" class="form-control o-nhap"
                                   value="{{ $user->phone }}">
                        </div>
                    </div>


                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold mb-2">Ngày sinh</label>
                            <input type="date" name="birthdate" class="form-control o-nhap"
                                   value="{{ $user->birthdate }}">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold mb-2">Giới tính</label>
                            <select name="gender" class="form-select o-nhap">
                                <option value="male" {{ $user->gender=='male'?'selected':'' }}>Nam</option>
                                <option value="female" {{ $user->gender=='female'?'selected':'' }}>Nữ</option>
                                <option value="other" {{ $user->gender=='other'?'selected':'' }}>Khác</option>
                            </select>
                        </div>
                    </div>


                </div>


                <!-- RIGHT -->
                <div class="col-md-4">


                    <div class="card border-0 bg-light rounded-3 p-3 mb-4">
                        <h6 class="fw-bold mb-3">THIẾT LẬP HỆ THỐNG</h6>


                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Vai trò</label>
                            <select name="role" class="form-select o-nhap">
                                <option value="Customer" {{ $user->role=='Customer'?'selected':'' }}>Khách hàng</option>
                                <option value="Manager" {{ $user->role=='Manager'?'selected':'' }}>Quản lý</option>
                                <option value="Admin" {{ $user->role=='Admin'?'selected':'' }}>Quản trị viên</option>
                            </select>
                        </div>


                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Trạng thái</label>
                            <select name="status" class="form-select o-nhap">
                                <option value="Active" {{ $user->status=='Active'?'selected':'' }}>Đang hoạt động</option>
                                <option value="Inactive" {{ $user->status=='Inactive'?'selected':'' }}>Đang bị khóa</option>
                                <option value="Pending" {{ $user->status=='Pending'?'selected':'' }}>Chờ phê duyệt</option>
                            </select>
                        </div>


                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Hạng thành viên</label>
                            <select name="membership_level" class="form-select o-nhap">
                                <option value="regular" {{ $user->membership_level=='regular'?'selected':'' }}>Thường</option>
                                <option value="gold" {{ $user->membership_level=='gold'?'selected':'' }}>Vàng</option>
                                <option value="vip" {{ $user->membership_level=='vip'?'selected':'' }}>VIP</option>
                            </select>
                        </div>


                        <div class="mb-0">
                            <label class="small fw-bold mb-1">Điểm tích lũy</label>
                            <input type="number" name="points" class="form-control o-nhap"
                                   value="{{ $user->points }}">
                        </div>
                    </div>


                    <div class="alert alert-info border-0 small">
                        <i class="fas fa-info-circle"></i> Tài khoản này được tạo vào: <br>
                        <b>{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i') }}</b>
                    </div>


                </div>
            </div>


            <!-- ACTION -->
            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">


                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary nut-bam shadow">
                        <i class="fas fa-save me-2"></i> LƯU THAY ĐỔI
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary nut-bam">
                        HỦY BỎ
                    </a>
                </div>


                <div class="d-flex gap-2">
                    <a href="{{ route('admin.users.reset_password', $user->user_id) }}" class="btn btn-info text-white nut-bam">
                        <i class="fas fa-key"></i> ĐỔI MẬT KHẨU
                    </a>
                    <a href="{{ route('admin.users.delete', $user->user_id) }}" class="btn btn-danger nut-bam">
                        <i class="fas fa-trash-alt"></i> XÓA
                    </a>
                </div>


            </div>


        </form>
    </div>
</div>


@endsection

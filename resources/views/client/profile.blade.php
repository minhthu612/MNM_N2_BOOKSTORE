@extends('layouts.client')

@section('content')
<style>
    body { background:#f5f7fb; }
    .profile-box { background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.06); margin-top: 30px;}
    .profile-header { background:linear-gradient(135deg,#667eea,#764ba2); color:#fff; text-align:center; padding:40px 20px; }
    .avatar { width:90px; height:90px; border-radius:50%; background:#fff; color:#667eea; display:flex; align-items:center; justify-content:center; font-size:38px; margin:0 auto 10px; border:4px solid rgba(255,255,255,0.3); }
    .tab-menu { display:flex; border-bottom:2px solid #eee; }
    .tab-menu a { padding:12px 18px; text-decoration:none; font-weight:600; color:#666; border-bottom:3px solid transparent; }
    .tab-menu a.active { color:#667eea; border-bottom:3px solid #667eea; }
    .form-box { padding:25px; }
    .input { border-radius:10px !important; padding:12px; border:1px solid #ddd; }
    .btn-main { border-radius:10px; padding:10px 25px; font-weight:600; }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="profile-box">
                {{-- HEADER --}}
                <div class="profile-header">
                    <div class="avatar"><i class="fas fa-user"></i></div>
                    <h4 class="mb-0">{{ $user->fullname }}</h4>
                    <small>{{ $user->role ?? 'Khách hàng thành viên' }}</small>
                </div>

                {{-- TAB MENU --}}
                <div class="tab-menu">
                    <a href="{{ url('/profile?tab=info') }}" class="{{ $tab == 'info' ? 'active' : '' }}">
                        <i class="fas fa-id-card me-2"></i>Thông tin cá nhân
                    </a>
                    <a href="{{ url('/profile?tab=password') }}" class="{{ $tab == 'password' ? 'active' : '' }}">
                        <i class="fas fa-shield-alt me-2"></i>Đổi mật khẩu
                    </a>
                </div>

                <div class="form-box">
                    {{-- THÔNG BÁO --}}
                    @if(session('success'))
                        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
                    @endif

                    {{-- TAB INFO --}}
                    @if($tab == 'info')
                    <form method="POST" action="{{ url('/profile/update') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="fw-bold small mb-1">Họ tên</label>
                                <input name="fullname" class="form-control input" value="{{ $user->fullname }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold small mb-1">Email</label>
                                <input name="email" type="email" class="form-control input" value="{{ $user->email }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold small mb-1">Số điện thoại</label>
                                <input name="phone" class="form-control input" value="{{ $user->phone }}">
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold small mb-1">Ngày tạo</label>
                                <input class="form-control input bg-light" value="{{ $user->created_at }}" readonly>
                            </div>
                        </div>
                        <div class="mt-4 d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary btn-main shadow">LƯU THÔNG TIN</button>
                            <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-main">QUAY LẠI</a>
                        </div>
                    </form>
                    @endif

                    {{-- TAB PASSWORD --}}
                    @if($tab == 'password')
                    <form method="POST" action="{{ url('/profile/password') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="fw-bold small mb-1">Mật khẩu hiện tại</label>
                            <input type="password" name="current_password" class="form-control input" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="fw-bold small mb-1">Mật khẩu mới</label>
                                <input type="password" name="new_password" class="form-control input" required>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold small mb-1">Xác nhận mật khẩu</label>
                                <input type="password" name="confirm_password" class="form-control input" required>
                            </div>
                        </div>
                        <div class="mt-4 d-flex justify-content-between">
                            <button type="submit" class="btn btn-dark btn-main shadow">ĐỔI MẬT KHẨU</button>
                            <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-main">QUAY LẠI</a>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
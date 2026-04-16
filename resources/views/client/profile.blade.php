@extends('layouts.client')

@section('content')

<style>
body{
    background:#f5f7fb;
}

.profile-box{
    background:#fff;
    border-radius:16px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,0.06);
}

.profile-header{
    background:linear-gradient(135deg,#667eea,#764ba2);
    color:#fff;
    text-align:center;
    padding:40px 20px;
}

.avatar{
    width:90px;
    height:90px;
    border-radius:50%;
    background:#fff;
    color:#667eea;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:38px;
    margin:0 auto 10px;
    border:4px solid rgba(255,255,255,0.3);
}

.tab-menu{
    display:flex;
    border-bottom:2px solid #eee;
}

.tab-menu a{
    padding:12px 18px;
    text-decoration:none;
    font-weight:600;
    color:#666;
    border-bottom:3px solid transparent;
}

.tab-menu a.active{
    color:#667eea;
    border-bottom:3px solid #667eea;
}

.form-box{
    padding:25px;
}

.input{
    border-radius:10px !important;
    padding:12px;
    border:1px solid #ddd;
}

.btn-main{
    border-radius:10px;
    padding:10px 25px;
    font-weight:600;
}
</style>

<div class="container py-5">
<div class="row justify-content-center">
<div class="col-lg-8">

<div class="profile-box">

    {{-- HEADER --}}
    <div class="profile-header">
        <div class="avatar">
            <i class="fas fa-user"></i>
        </div>

        <h4 class="mb-0">{{ $user->name }}</h4>
        <small>Khách hàng thành viên</small>
    </div>

    {{-- TAB --}}
    <div class="tab-menu">
        <a href="/profile?tab=info"
           class="{{ $tab=='info' ? 'active' : '' }}">
            Thông tin cá nhân
        </a>

        <a href="/profile?tab=password"
           class="{{ $tab=='password' ? 'active' : '' }}">
            Đổi mật khẩu
        </a>
    </div>

    {{-- BODY --}}
    <div class="form-box">

        {{-- MESSAGE --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        {{-- INFO --}}
        @if($tab == 'info')
        <form method="POST" action="/profile/update">
            @csrf

            <div class="row g-3">

                <div class="col-md-6">
                    <label>Họ tên</label>
                    <input name="fullname" class="form-control input"
                           value="{{ $user->name }}">
                </div>

                <div class="col-md-6">
                    <label>Email</label>
                    <input name="email" class="form-control input"
                           value="{{ $user->email }}">
                </div>

                <div class="col-md-6">
                    <label>Số điện thoại</label>
                    <input name="phone" class="form-control input"
                           value="{{ $user->phone }}">
                </div>

                <div class="col-md-6">
                    <label>Ngày tạo</label>
                    <input class="form-control input bg-light"
                           value="{{ $user->created_at }}" readonly>
                </div>

            </div>

            <div class="mt-4 d-flex justify-content-between">
                <button class="btn btn-primary btn-main">
                    LƯU THÔNG TIN
                </button>

                <a href="/" class="btn btn-outline-secondary btn-main">
                    QUAY LẠI
                </a>
            </div>
        </form>
        @endif

        {{-- PASSWORD --}}
        @if($tab == 'password')
        <form method="POST" action="/profile/password">
            @csrf

            <div class="mb-3">
                <label>Mật khẩu hiện tại</label>
                <input type="password" name="current_password"
                       class="form-control input">
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label>Mật khẩu mới</label>
                    <input type="password" name="new_password"
                           class="form-control input">
                </div>

                <div class="col-md-6">
                    <label>Xác nhận mật khẩu</label>
                    <input type="password" name="confirm_password"
                           class="form-control input">
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-between">
                <button class="btn btn-dark btn-main">
                    ĐỔI MẬT KHẨU
                </button>

                <a href="/" class="btn btn-outline-secondary btn-main">
                    QUAY LẠI
                </a>
            </div>
        </form>
        @endif

    </div>

</div>

</div>
</div>
</div>

@endsection
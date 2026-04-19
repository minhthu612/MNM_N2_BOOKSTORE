@extends('layouts.app')


@section('title', 'Đặt lại mật khẩu')


@section('content')


<style>
    .khung-reset { background: #fff; padding: 30px; border-radius: 15px; border: 1px solid #eee; }
    .o-nhap { border-radius: 10px !important; padding: 12px; }
    .nut-bam { border-radius: 25px !important; padding: 10px 30px !important; font-weight: bold; }
    .thong-tin-nhanh { background: #f0f7ff; border-left: 5px solid #007bff; padding: 15px; border-radius: 8px; }
</style>


<div class="container">
    <div class="khung-reset shadow-sm mx-auto" style="max-width: 600px;">


        <div class="text-center mb-4">
            <h4 class="text-primary fw-bold mb-0">
                <i class="fas fa-key me-2"></i>ĐẶT LẠI MẬT KHẨU
            </h4>
            <p class="text-muted small">Cấp lại mật khẩu mới cho thành viên hệ thống</p>
        </div>


        {{-- ERROR --}}
        @if ($error != '')
            <div class="alert alert-danger border-0 shadow-sm">
                <i class="fas fa-exclamation-circle me-2"></i> {{ $error }}
            </div>
        @endif


        {{-- INFO --}}
        <div class="thong-tin-nhanh mb-4">
            <div class="row">
                <div class="col-6">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Tài khoản:</small>
                    <div class="fw-bold">{{ $user->username }}</div>
                </div>
                <div class="col-6 text-end">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Họ tên:</small>
                    <div class="fw-bold">{{ $user->fullname }}</div>
                </div>
            </div>
        </div>


        <form method="POST" action="{{ route('admin.users.reset_password', $user->user_id) }}">
            @csrf


            <div class="mb-4">
                <label class="fw-bold mb-2">Mật khẩu mới *</label>
                <input type="password" name="new_password" class="form-control o-nhap"
                       placeholder="Nhập tối thiểu 6 ký tự" required>
            </div>


            <div class="mb-4">
                <label class="fw-bold mb-2">Xác nhận mật khẩu mới *</label>
                <input type="password" name="confirm_password" class="form-control o-nhap"
                       placeholder="Nhập lại mật khẩu phía trên" required>
            </div>


            <div class="alert alert-warning border-0 small mb-4">
                <i class="fas fa-info-circle"></i>
                <b>Lưu ý:</b> Sau khi cập nhật, người dùng sẽ không thể dùng mật khẩu cũ để đăng nhập được nữa.
            </div>


            <div class="pt-3 border-top d-flex gap-2">
                <button type="submit" class="btn btn-primary nut-bam shadow w-100">
                    <i class="fas fa-check-circle me-2"></i> XÁC NHẬN ĐỔI MẬT KHẨU
                </button>


                <a href="{{ route('admin.users.show', $user->user_id) }}"
                   class="btn btn-light nut-bam border">
                    HỦY
                </a>
            </div>
        </form>


    </div>
</div>


@endsection

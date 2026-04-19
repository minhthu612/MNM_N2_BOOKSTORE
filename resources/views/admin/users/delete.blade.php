@extends('layouts.app')


@section('title', 'Xác nhận xóa thành viên')


@section('content')


<style>
    .khung-xoa { background: #fff; padding: 30px; border-radius: 15px; border: 1px solid #eee; }
    .o-nhap { border-radius: 10px !important; padding: 12px; }
    .nut-bam { border-radius: 25px !important; padding: 10px 30px !important; font-weight: bold; }
    .vung-canh-bao { background: #fff5f5; border-left: 5px solid #dc3545; padding: 15px; border-radius: 8px; }
</style>


<div class="container">
    <div class="khung-xoa shadow-sm mx-auto" style="max-width: 650px;">
        <div class="text-center mb-4">
            <h3 class="text-danger fw-bold">
                <i class="fas fa-user-times"></i> XÓA THÀNH VIÊN
            </h3>
            <p class="text-muted">
                Lưu ý: Hành động xóa sẽ gỡ bỏ hoàn toàn tài khoản khỏi hệ thống.
            </p>
        </div>


        {{-- ERROR --}}
        @if (session('error'))
            <div class="alert alert-danger border-0 shadow-sm">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            </div>
        @endif


        <div class="vung-canh-bao mb-4">
            <h6 class="text-danger fw-bold mb-3 text-uppercase">Thông tin tài khoản:</h6>


            <div class="row mb-2">
                <div class="col-5 text-muted small fw-bold">TÊN ĐĂNG NHẬP:</div>
                <div class="col-7 fw-bold">{{ $user->username }}</div>
            </div>


            <div class="row mb-2">
                <div class="col-5 text-muted small fw-bold">VAI TRÒ:</div>
                <div class="col-7">
                    <span class="badge bg-secondary">{{ $user->role }}</span>
                </div>
            </div>


            @if ($order_count > 0)
                <div class="alert alert-warning py-2 mt-3 mb-0 small">
                    <i class="fas fa-shopping-cart"></i>
                    Người dùng này đang có <b>{{ $order_count }}</b> đơn hàng trong lịch sử.
                </div>
            @endif
        </div>


        <form method="POST" action="{{ route('admin.users.destroy', $user->user_id) }}">
            @csrf
            @method('DELETE')


            <div class="mb-4">
                <label class="fw-bold mb-2">
                    Nhập chữ <span class="text-danger">DELETE</span> để xác nhận xóa:
                </label>
                <input type="text" name="confirm_text"
                       class="form-control o-nhap text-center fs-5 fw-bold"
                       placeholder="Gõ chính xác chữ DELETE"
                       required autocomplete="off">
            </div>


            <div class="d-flex justify-content-center gap-2 pt-3 border-top">
                <button type="submit" class="btn btn-danger nut-bam shadow">
                    <i class="fas fa-trash-alt me-2"></i> XÓA NGƯỜI DÙNG
                </button>


                <a href="{{ route('admin.users.index') }}"
                   class="btn btn-light nut-bam border">
                    QUAY LẠI
                </a>
            </div>
        </form>
    </div>
</div>


@endsection

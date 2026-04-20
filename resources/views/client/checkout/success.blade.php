@extends('layouts.client')

@section('content')
<style>
    body {
        background-color: #f8f9fa;
    }
    .khung-thanh-cong {
        max-width: 600px;
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.04);
        margin-top: 50px;
    }
    .circle-check {
        width: 100px;
        height: 100px;
        background: #28a745;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 50px;
        margin: 0 auto;
        box-shadow: 0 10px 20px rgba(40, 167, 69, 0.2);
    }
    .info-box {
        background: #ffffff;
        border: 1px solid #f1f1f1;
        border-radius: 16px;
        padding: 30px;
    }
    .ma-don-hang {
        font-size: 18px; /* Chỉnh nhỏ lại cho giống hình cũ */
        font-weight: 700;
        color: #0d6efd;
    }
    .label-text {
        color: #6c757d;
        font-size: 16px;
    }
    .note-box {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 15px;
        font-size: 13.5px;
        color: #6c757d;
        line-height: 1.5;
        border: none;
    }
    .btn-custom {
        padding: 12px 30px;
        font-weight: 600;
        border-radius: 30px !important;
        transition: 0.3s;
    }
    .badge-status {
        background-color: #ffc107;
        color: #000;
        padding: 8px 15px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
    }
</style>

<div class="container py-5">
    <div class="khung-thanh-cong p-5 mx-auto text-center border">
        <div class="circle-check mb-4">
            <i class="fas fa-check"></i>
        </div>

        <h1 class="fw-bold mb-4" style="letter-spacing: -1px;">ĐẶT HÀNG THÀNH CÔNG!</h1>
        <p class="text-muted mb-5">Cảm ơn bạn đã lựa chọn mua sắm tại cửa hàng chúng tôi.</p>

        <div class="info-box text-start mb-5 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="label-text">Mã đơn hàng:</span>
                <span class="ma-don-hang">#{{ $order_id }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <span class="label-text">Trạng thái:</span>
                <span class="badge-status">Đang chờ xử lý</span>
            </div>
            
            <hr class="my-4" style="opacity: 0.05;">

            <div class="note-box d-flex align-items-start">
                <i class="fas fa-info-circle mt-1 me-3 opacity-50"></i>
                <span>Nhân viên sẽ liên hệ với bạn qua số điện thoại để xác nhận đơn hàng trong thời gian sớm nhất.</span>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-6">
                <a href="{{ url('/') }}" class="btn btn-outline-primary w-100 btn-custom border-2">
                    <i class="fas fa-shopping-bag me-2"></i> Tiếp tục mua
                </a>
            </div>
            <div class="col-6">
                <a href="{{ route('orders.index') }}" class="btn btn-primary w-100 btn-custom shadow-sm">
                    <i class="fas fa-file-invoice me-2"></i> Xem đơn hàng
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
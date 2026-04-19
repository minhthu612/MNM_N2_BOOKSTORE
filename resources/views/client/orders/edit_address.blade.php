@extends('layouts.client')

@section('content')
<style>
    .card { border-radius: 16px; }
    .form-control, textarea { border-radius: 10px; }
    .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm p-4 border-0">

                <h4 class="fw-bold mb-3 text-center">
                    <i class="fas fa-edit text-primary me-2"></i>
                    Cập nhật địa chỉ giao hàng
                </h4>

                <div class="alert alert-warning text-center small mb-4">
                    ⏳ Thời gian còn lại:
                    <strong id="countdown"></strong>
                </div>

                {{-- Đảm bảo ID form đúng để Javascript bắt được sự kiện --}}
                <form id="formUpdateAddress" method="POST" action="{{ route('orders.update_address', $order->order_id) }}">
                    @csrf

                    <div class="mb-3">
                        <label class="fw-bold small">Họ tên</label>
                        <input type="text" name="fullname" class="form-control" value="{{ $order->fullname }}" placeholder="Nhập họ tên người nhận" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold small">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" value="{{ $order->phone }}" placeholder="Nhập số điện thoại mới" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="fw-bold small">Tỉnh / Thành</label>
                            <input type="text" name="city" class="form-control" placeholder="Ví dụ: Đồng Nai" required>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold small">Quận / Huyện</label>
                            <input type="text" name="district" class="form-control" placeholder="Ví dụ: Vĩnh Cửu" required>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold small">Phường / Xã</label>
                            <input type="text" name="ward" class="form-control" placeholder="Ví dụ: Bình Hoà" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold small">Địa chỉ chi tiết (Số nhà, tên đường...)</label>
                        <textarea name="street" class="form-control" rows="2" placeholder="Ví dụ: 186 đường ABC..." required></textarea>
                    </div>

                    <button type="submit" id="btnSave" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">
                        <span id="btnText">Lưu địa chỉ mới</span>
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    // 1. Logic Đếm ngược
    let remaining = Math.floor({{ $remainingSeconds }});
    const cd = document.getElementById('countdown');
    const btn = document.getElementById('btnSave');
    const btnText = document.getElementById('btnText');
    const form = document.getElementById('formUpdateAddress');

    function formatTime(seconds) {
        const totalSeconds = Math.max(0, seconds);
        const m = Math.floor(totalSeconds / 60);
        const s = Math.floor(totalSeconds % 60);
        return m.toString().padStart(2, '0') + ':' + s.toString().padStart(2, '0');
    }

    if(cd) cd.innerText = formatTime(remaining);

    const timer = setInterval(() => {
        remaining--;
        if (remaining <= 0) {
            clearInterval(timer);
            if(cd) cd.innerText = '00:00';
            btn.disabled = true;
            btn.classList.add('disabled');
            alert('⛔ Đã hết thời gian cập nhật địa chỉ!');
            return;
        }
        if(cd) cd.innerText = formatTime(remaining);
    }, 1000);

    // 2. Logic Hiệu ứng khi bấm Lưu
    // Sử dụng form.submit() để chắc chắn dữ liệu được gửi đi sau khi đổi UI
    form.addEventListener('submit', function(e) {
        // Chỉ vô hiệu hóa nút sau khi trình duyệt đã xác nhận gửi form
        btn.style.pointerEvents = 'none'; 
        btn.style.opacity = '0.7';
        btnText.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Đang lưu thay đổi...';
    });
</script>
@endsection
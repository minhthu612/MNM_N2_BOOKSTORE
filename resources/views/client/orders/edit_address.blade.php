@extends('layouts.client')


@section('content')
<style>
    .card { border-radius: 16px; }
    .form-control, textarea { border-radius: 10px; }
    /* Giúp các ô nhập liệu trông chuyên nghiệp hơn */
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


                <form method="POST" action="{{ route('orders.update_address', $order->order_id) }}">
                    @csrf


                    <div class="mb-3">
                        <label class="fw-bold small">Họ tên</label>
                        {{-- Đã xóa disabled và thêm name="fullname" --}}
                        <input type="text"
                               name="fullname"
                               class="form-control"
                               value="{{ $order->fullname }}"
                               placeholder="Nhập họ tên người nhận"
                               required>
                    </div>


                    <div class="mb-3">
                        <label class="fw-bold small">Số điện thoại</label>
                        {{-- Đã xóa disabled và thêm name="phone" --}}
                        <input type="text"
                               name="phone"
                               class="form-control"
                               value="{{ $order->phone }}"
                               placeholder="Nhập số điện thoại mới"
                               required>
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
                        <textarea name="street"
                                  class="form-control"
                                  rows="2"
                                  placeholder="Ví dụ: 186 đường ABC..."
                                  required></textarea>
                    </div>


                    <button type="submit"
                            id="btnSave"
                            class="btn btn-primary w-100 rounded-pill py-2 fw-bold">
                        Lưu địa chỉ mới
                    </button>
                </form>


            </div>
        </div>
    </div>
</div>


<script>
    let remaining = Math.floor({{ $remainingSeconds }});
    const cd = document.getElementById('countdown');
    const btn = document.getElementById('btnSave');


    function formatTime(seconds) {
        const totalSeconds = Math.max(0, seconds);
        const m = Math.floor(totalSeconds / 60);
        const s = Math.floor(totalSeconds % 60);
        return m.toString().padStart(2, '0') + ':' + s.toString().padStart(2, '0');
    }


    cd.innerText = formatTime(remaining);


    const timer = setInterval(() => {
        remaining--;
        if (remaining <= 0) {
            clearInterval(timer);
            cd.innerText = '00:00';
            btn.disabled = true;
            btn.classList.add('disabled');
            alert('⛔ Đã hết thời gian cập nhật địa chỉ!');
            return;
        }
        cd.innerText = formatTime(remaining);
    }, 1000);
</script>
@endsection

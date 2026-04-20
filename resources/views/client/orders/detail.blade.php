@extends('layouts.client')

@section('content')
<style>
    body { background-color: #f8f9fa; }
    .khung-trang {
        background: #fff;
        border-radius: 15px;
        padding: 25px;
        border: 1px solid #eee;
        margin-bottom: 20px;
    }
    .anh-sach-nho {
        width: 55px;
        height: 75px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #f1f1f1;
    }
    .tieu-de-muc {
        border-bottom: 2px solid #f8f9fa;
        padding-bottom: 12px;
        margin-bottom: 20px;
        font-weight: bold;
        color: #333;
    }
</style>

<div class="container py-5">

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-pill px-4 shadow-sm mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

    <div class="mb-4 bg-white p-3 rounded-3 shadow-sm border">
        <a href="{{ route('orders.index') }}" class="text-decoration-none fw-bold">
            <i class="fas fa-history me-1"></i> Lịch sử đơn hàng
        </a>
        <span class="mx-2 text-muted">/</span>
        <span class="text-secondary">Chi tiết đơn hàng #{{ $order->order_id }}</span>
    </div>

    <div class="row g-4">
        {{-- CỘT TRÁI --}}
        <div class="col-lg-4">
            {{-- THÔNG TIN NHẬN HÀNG --}}
            <div class="khung-trang shadow-sm">
                <h5 class="fw-bold"><i class="fas fa-map-marker-alt text-danger me-2"></i> THÔNG TIN NHẬN HÀNG</h5>
                <hr>
                <div class="small">
                    <p class="mb-1"><strong>Người nhận:</strong> {{ $order->fullname ?? Auth::user()->fullname }}</p>
                    <p class="mb-1"><strong>Số điện thoại:</strong> {{ $order->phone ?? Auth::user()->phone }}</p>
                    <p class="mb-1"><strong>Địa chỉ giao:</strong></p>
                    <p class="text-muted mb-3">{{ $order->shipping_address }}</p>
                    <div class="p-2 bg-light rounded border small">
                        <strong>Ghi chú:</strong> {{ $order->notes ?: 'Không có ghi chú.' }}
                    </div>
                </div>

                @if ($order->status === 'pending')
                    <hr>
                    @if ($remainingSeconds > 0)
                        <div class="small text-warning mb-2">
                            ⏳ Còn <strong id="countdown"></strong> để cập nhật địa chỉ
                        </div>
                        <a id="updateBtn"
                           href="{{ route('orders.edit_address', $order->order_id) }}"
                           class="btn btn-outline-primary btn-sm w-100 rounded-pill">
                            <i class="fas fa-edit me-1"></i> Cập nhật địa chỉ giao hàng
                        </a>
                    @else
                        <div class="alert alert-warning small mb-0">
                            Đã quá 1 giờ, không thể chỉnh sửa địa chỉ.
                        </div>
                    @endif
                @endif
            </div>

            {{-- TRẠNG THÁI --}}
            <div class="khung-trang shadow-sm">
                <h6 class="tieu-de-muc">
                    <i class="fas fa-info-circle text-primary me-2"></i>TRẠNG THÁI
                </h6>
                <div class="small">
                    <p class="mb-2"><strong>Thanh toán:</strong> {{ $order->payment_method }}</p>
                    <p class="mb-3">
                        <strong>Ngày đặt:</strong>
                        {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}
                    </p>

                    @php
                        $badge = 'bg-warning text-dark';
                        $txt = 'Đang chờ xác nhận';
                        if ($order->status === 'completed' || $order->status === 'delivered') {
                            $badge = 'bg-success text-white';
                            $txt = 'Giao hàng thành công';
                        } elseif ($order->status === 'cancelled') {
                            $badge = 'bg-danger text-white';
                            $txt = 'Đơn hàng đã hủy';
                        } elseif ($order->status === 'shipping' || $order->status === 'shipped') {
                            $badge = 'bg-primary text-white';
                            $txt = 'Đang giao hàng';
                        }
                    @endphp

                    <span class="badge {{ $badge }} px-3 py-2 rounded-pill mb-3">
                        {{ $txt }}
                    </span>

                    @if ($order->status === 'pending')
                        <form action="{{ route('orders.cancel', $order->order_id) }}"
                              method="POST"
                              onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn?')">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100 fw-bold rounded-pill">
                                HỦY ĐƠN HÀNG
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI --}}
        <div class="col-lg-8">
            <div class="khung-trang shadow-sm p-0 overflow-hidden">
                <div class="p-4 border-bottom bg-light">
                    <h6 class="fw-bold m-0">SẢN PHẨM TRONG ĐƠN</h6>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            @php
                                                $extensions = ['webp', 'jpg', 'png', 'jpeg'];
                                                $imagePath = 'images/no-image.jpg'; 

                                                // Logic tìm ảnh: check theo book_id hoặc set_id
                                                $isSet = (isset($item->loai) && $item->loai == 'set');
                                                $id = $item->book_id ?? $item->set_id; 
                                                $fileName = $isSet ? ($id . '_' . $id) : $id;

                                                foreach ($extensions as $ext) {
                                                    if (file_exists(storage_path("app/public/image/{$fileName}.{$ext}"))) {
                                                        $imagePath = "storage/image/{$fileName}.{$ext}";
                                                        break;
                                                    }
                                                }
                                            @endphp

                                            <img src="{{ asset($imagePath) }}" 
                                                 class="anh-sach-nho me-3" 
                                                 onerror="this.src='{{ asset('images/no-image.jpg') }}'">
                                            
                                            <div>
                                                <div class="fw-bold small">{{ $item->title }}</div>
                                                <div class="text-muted small">
                                                    Giá: {{ number_format($item->price, 0, ',', '.') }}đ
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">x{{ $item->quantity }}</td>
                                    <td class="text-end pe-4 fw-bold">
                                        {{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="2" class="text-end ps-4 py-3 fw-bold">TỔNG CỘNG:</td>
                                <td class="text-end pe-4 py-3 fw-bold text-danger fs-5">
                                    {{ number_format($order->total_amount, 0, ',', '.') }}đ
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- COUNTDOWN SCRIPT (FIXED) --}}
@if ($order->status === 'pending' && $remainingSeconds > 0)
<script>
    let remaining = Math.floor({{ $remainingSeconds }});
    const cd = document.getElementById('countdown');
    const btn = document.getElementById('updateBtn');

    function updateDisplay() {
        if (remaining <= 0) {
            clearInterval(timer);
            if (cd) cd.innerHTML = "00:00";
            if (btn) btn.style.display = "none";
            return;
        }
        const m = Math.floor(remaining / 60);
        const s = Math.floor(remaining % 60);
        if (cd) {
            cd.innerHTML = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
        }
    }
    updateDisplay();
    const timer = setInterval(() => {
        remaining--;
        updateDisplay();
    }, 1000);
</script>
@endif
@endsection
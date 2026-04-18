@extends('layouts.client')

@section('content')

<style>
/* GIỮ NGUYÊN 100% */
body { background-color: #f8f9fa; }

.cart-card { background: #ffffff; border-radius: 20px; border: none; overflow: hidden; }

.product-img { width: 70px; height: 100px; object-fit: cover; border-radius: 12px; transition: 0.3s; }
.product-img:hover { transform: scale(1.05); }

.delete-link { color: #ced4da; transition: 0.3s; font-size: 1.2rem; }
.delete-link:hover { color: #dc3545; }

.qty-input {
    width: 65px;
    border-radius: 10px !important;
    text-align: center;
    font-weight: 700;
    border: 1px solid #dee2e6;
    height: 35px;
}

.btn-update-sm {
    font-size: 10px;
    color: #6c757d;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 0.5px;
}
.btn-update-sm:hover { color: #0d6efd; }

.summary-box {
    background: #ffffff;
    border-radius: 20px;
    padding: 25px;
    border: 1px solid rgba(0,0,0,0.05);
}
.summary-line {
    padding: 12px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px dashed #eee;
}
.summary-line:last-of-type { border-bottom: none; }

.pill-btn {
    border-radius: 30px !important;
    font-weight: 700;
    padding: 12px 25px;
    transition: 0.3s;
}
.pill-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* ================= THÊM CSS CHO ALERT ================= */
.alert {
    border-radius: 15px;
    border: none;
}
.alert-success {
    background-color: #d1e7dd;
    color: #0f5132;
}
.alert-danger {
    background-color: #f8d7da;
    color: #842029;
}
.btn-outline-danger {
    border-radius: 20px;
    padding: 5px 12px;
    font-size: 13px;
}
/* ================= KẾT THÚC CSS THÊM ================= */
</style>

<div class="container py-5">

    <!-- ================= HIỂN THỊ THÔNG BÁO SESSION (CHỈ 1 LẦN VÀ TỰ XÓA) ================= -->
    @php
        $successMessage = session('success');
        $errorMessage = session('error');
    @endphp

    @if($successMessage)
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ $successMessage }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @php session()->forget('success'); @endphp
    @endif

    @if($errorMessage)
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ $errorMessage }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @php session()->forget('error'); @endphp
    @endif
    <!-- ================= KẾT THÚC THÔNG BÁO SESSION ================= -->

    <div class="row align-items-end mb-4">
        <div class="col">
            <h2 class="fw-bold text-dark m-0">Giỏ hàng của bạn</h2>
            <p class="text-muted small m-0">
                Bạn đang có {{ count($cart_list) }} sản phẩm trong giỏ
            </p>
        </div>
    </div>

    @if(count($cart_list) > 0)

    <div class="row g-4">

        <!-- LEFT -->
        <div class="col-lg-8">
            <div class="cart-card shadow-sm p-4">

                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">

                        <thead class="text-muted small text-uppercase">
                        <tr>
                            <th class="ps-0" width="50%">Sách</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Tổng tiền</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody>

                        @php $subtotal = 0; @endphp

                        @foreach($cart_list as $item)

                            @php
                                $price = (int)$item->price;
                                $qty = (int)$item->quantity;
                                $total = $price * $qty;
                                $subtotal += $total;
                            @endphp

                            <tr style="border-bottom: 1px solid #f8f9fa;">

                                <!-- INFO -->
                                <td class="ps-0 py-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ asset($item->link_images) }}"
                                             class="product-img shadow-sm border">

                                        <div>
                                            <div class="fw-bold text-dark mb-1">
                                                {{ $item->title }}
                                            </div>

                                            <div class="text-primary fw-bold small">
                                                {{ number_format($price,0,',','.') }}đ
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- QTY -->
                                <td class="text-center">
                                    <form action="{{ route('cart.update') }}" method="POST" class="d-inline-block">
                                        @csrf

                                        <input type="hidden" name="cart_item_id" value="{{ $item->cart_item_id }}">

                                        <input type="number"
                                               name="quantity"
                                               class="qty-input mb-1"
                                               value="{{ $qty }}"
                                               min="1">

                                        <br>

                                        <button type="submit"
                                                class="btn btn-link btn-update-sm p-0 text-decoration-none">
                                            Cập nhật
                                        </button>
                                    </form>
                                </td>

                                <!-- TOTAL -->
                                <td class="text-end fw-bold text-dark">
                                    {{ number_format($total,0,',','.') }}đ
                                </td>

                                <!-- DELETE (FIX LOGIC) -->
                                <td class="text-end pe-0">
                                    <form action="{{ route('cart.delete') }}" method="POST"
                                          onsubmit="return confirm('Bạn muốn bỏ sản phẩm này?')">
                                        @csrf

                                        <input type="hidden" name="id" value="{{ $item->cart_item_id }}">

                                        <button type="submit" class="delete-link border-0 bg-transparent">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    </form>
                                </td>

                            </tr>

                        @endforeach

                        </tbody>

                    </table>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ url('/') }}"
                   class="text-muted text-decoration-none small fw-bold">
                    <i class="fas fa-long-arrow-alt-left me-2"></i>
                    Tiếp tục mua thêm sách
                </a>
            </div>
        </div>

        <!-- RIGHT -->
        <div class="col-lg-4">

            @php
                $shipping_fee = 30000;
                $discount_val = session('discount_amount', 0);
                $final_total = $subtotal - $discount_val + $shipping_fee;
                // Đảm bảo tổng không âm
                if ($final_total < 0) $final_total = 0;
                $applied_coupon = session('applied_coupon');
            @endphp

            <div class="summary-box shadow-sm mb-4">

                <h5 class="fw-bold mb-4 text-center">Tổng đơn hàng</h5>

                <!-- FIX COUPON ROUTE - ĐÃ SỬA HOÀN CHỈNH -->
                <form method="GET" action="{{ route('cart.index') }}" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="coupon_code"
                               class="form-control px-3"
                               placeholder="Mã giảm giá..."
                               value="{{ $applied_coupon['code'] ?? '' }}">

                        <button class="btn btn-dark px-3"
                                name="apply_coupon"
                                type="submit">
                            {{ $applied_coupon ? 'Cập nhật' : 'Dùng' }}
                        </button>
                    </div>
                </form>

                <!-- ================= THÊM HIỂN THỊ COUPON ĐÃ ÁP DỤNG ================= -->
                @if($applied_coupon)
                <div class="alert alert-success mb-4 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-ticket-alt me-2"></i>
                            <strong>Đã áp dụng:</strong> Mã <span class="fw-bold">{{ $applied_coupon['code'] }}</span>
                            - Giảm <strong>{{ number_format($applied_coupon['discount'],0,',','.') }}đ</strong>
                        </div>
                        <a href="{{ route('cart.remove_coupon') }}" 
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Xóa mã giảm giá?')">
                            <i class="fas fa-trash-alt me-1"></i> Xóa
                        </a>
                    </div>
                </div>
                @endif
                <!-- ================= KẾT THÚC HIỂN THỊ COUPON ================= -->

                <div class="mb-4">

                    <div class="summary-line">
                        <span class="text-muted">Tạm tính</span>
                        <span class="fw-bold">{{ number_format($subtotal,0,',','.') }}đ</span>
                    </div>

                    <div class="summary-line">
                        <span class="text-muted">Giảm giá</span>
                        <span class="text-success fw-bold">
                            -{{ number_format($discount_val,0,',','.') }}đ
                        </span>
                    </div>

                    <div class="summary-line">
                        <span class="text-muted">Phí giao hàng</span>
                        <span class="fw-bold">
                            +{{ number_format($shipping_fee,0,',','.') }}đ
                        </span>
                    </div>

                    <div class="summary-line border-0 pt-4">
                        <span class="fs-5 fw-bold text-dark">Tổng cộng</span>
                        <span class="fs-4 fw-bold text-danger">
                            {{ number_format($final_total,0,',','.') }}đ
                        </span>
                    </div>

                </div>

                <a href="{{ route('checkout.index') }}"
                   class="btn btn-primary w-100 pill-btn shadow py-3">
                    ĐẶT HÀNG NGAY <i class="fas fa-arrow-right ms-2"></i>
                </a>

            </div>

        </div>

    </div>

    @else

    <div class="text-center py-5 cart-card shadow-sm border">
        <div class="py-5">
            <i class="fas fa-shopping-bag fa-5x text-light mb-4"></i>
            <h4 class="text-muted fw-bold">Giỏ hàng của bạn đang trống</h4>
            <p class="text-muted small mb-4">
                Có vẻ như bạn chưa thêm bất kỳ sản phẩm nào.
            </p>
            <a href="{{ url('/') }}"
               class="btn btn-primary pill-btn px-5 shadow">
               KHÁM PHÁ CỬA HÀNG
            </a>
        </div>
    </div>

    @endif

</div>

@endsection
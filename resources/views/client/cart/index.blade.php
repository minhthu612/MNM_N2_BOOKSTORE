@extends('layouts.client')


@section('content')


<style>
/* GIỮ NGUYÊN 100% CODE CŨ CỦA BẠN */
body { background-color: #f8f9fa; }
.cart-card { background: #ffffff; border-radius: 20px; border: none; overflow: hidden; }
.product-img { width: 70px; height: 100px; object-fit: cover; border-radius: 12px; transition: 0.3s; }
.product-img:hover { transform: scale(1.05); }
.delete-link { color: #ced4da; transition: 0.3s; font-size: 1.2rem; }
.delete-link:hover { color: #dc3545; }
.qty-input { width: 65px; border-radius: 10px !important; text-align: center; font-weight: 700; border: 1px solid #dee2e6; height: 35px; }
.btn-update-sm { font-size: 10px; color: #6c757d; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; }
.btn-update-sm:hover { color: #0d6efd; }
.summary-box { background: #ffffff; border-radius: 20px; padding: 25px; border: 1px solid rgba(0,0,0,0.05); }
.summary-line { padding: 12px 0; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #eee; }
.summary-line:last-of-type { border-bottom: none; }
.pill-btn { border-radius: 30px !important; font-weight: 700; padding: 12px 25px; transition: 0.3s; }
.pill-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
.alert { border-radius: 15px; border: none; }
.alert-success { background-color: #d1e7dd; color: #0f5132; }
.alert-danger { background-color: #f8d7da; color: #842029; }
.btn-outline-danger { border-radius: 20px; padding: 5px 12px; font-size: 13px; }


/* CHỈ THÊM CSS MỚI CHO CHECKBOX - KHÔNG XÓA CÁI CŨ */
.form-check-input { width: 20px; height: 20px; cursor: pointer; border: 2px solid #667eea; }
.cart-item-row.unselected { opacity: 0.4; filter: grayscale(1); }
</style>


<div class="container py-5">


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


        <div class="col-lg-8">
            <div class="cart-card shadow-sm p-4">


                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">


                        <thead class="text-muted small text-uppercase">
                        <tr>
                            <th width="5%"><input type="checkbox" id="check-all" checked class="form-check-input"></th>
                            <th class="ps-0" width="45%">Sách</th>
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
                                $total_item = $price * $qty;
                                $subtotal += $total_item;
                            @endphp


                            <tr class="cart-item-row" style="border-bottom: 1px solid #f8f9fa;">
                                <td>
                                    <input type="checkbox" class="item-checkbox form-check-input" checked
                                           data-price="{{ $total_item }}">
                                </td>


                                <td class="ps-0 py-4">
                                    <div class="d-flex align-items-center gap-3">
                                        @php
                                            $extensions = ['webp', 'jpg', 'png', 'jpeg'];
                                            $imagePath = 'images/no-image.jpg';
                                            
                                            // Xác định ID và quy tắc đặt tên: 
                                            // Nếu có loai == 'set' thì dùng ID_ID, ngược lại dùng ID
                                            $isSetItem = (isset($item->loai) && $item->loai == 'set');
                                            $id = $item->book_id ?? $item->set_id; // Tùy vào biến bạn query ra
                                            $fileName = $isSetItem ? ($id . '_' . $id) : $id;

                                            foreach ($extensions as $ext) {
                                                if (file_exists(storage_path("app/public/image/{$fileName}.{$ext}"))) {
                                                    $imagePath = "storage/image/{$fileName}.{$ext}";
                                                    break;
                                                }
                                            }
                                        @endphp

                                        <img src="{{ asset($imagePath) }}" 
                                            class="product-img shadow-sm border"
                                            style="width: 60px; height: 80px; object-fit: cover; border-radius: 5px;"
                                            onerror="this.src='{{ asset('images/no-image.jpg') }}'">

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


                                <td class="text-center">
                                    <form action="{{ route('cart.update') }}" method="POST" class="d-inline-block">
                                        @csrf
                                        <input type="hidden" name="cart_item_id" value="{{ $item->cart_item_id }}">
                                        <input type="number" name="quantity" class="qty-input mb-1" value="{{ $qty }}" min="1">
                                        <br>
                                        <button type="submit" class="btn btn-link btn-update-sm p-0 text-decoration-none">Cập nhật</button>
                                    </form>
                                </td>


                                <td class="text-end fw-bold text-dark">
                                    {{ number_format($total_item,0,',','.') }}đ
                                </td>


                                <td class="text-end pe-0">
                                    <form action="{{ route('cart.delete') }}" method="POST" onsubmit="return confirm('Bạn muốn bỏ sản phẩm này?')">
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
                <a href="{{ url('/') }}" class="text-muted text-decoration-none small fw-bold">
                    <i class="fas fa-long-arrow-alt-left me-2"></i> Tiếp tục mua thêm sách
                </a>
            </div>
        </div>


        <div class="col-lg-4">


            @php
                $shipping_fee = 30000;
                $discount_val = session('discount_amount', 0);
                $final_total = $subtotal - $discount_val + $shipping_fee;
                if ($final_total < 0) $final_total = 0;
                $applied_coupon = session('applied_coupon');
            @endphp


            <div class="summary-box shadow-sm mb-4">


                <h5 class="fw-bold mb-4 text-center">Tổng đơn hàng</h5>


                <form method="GET" action="{{ route('cart.index') }}" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="coupon_code" class="form-control px-3" placeholder="Mã giảm giá..." value="{{ $applied_coupon['code'] ?? '' }}">
                        <button class="btn btn-dark px-3" name="apply_coupon" type="submit">{{ $applied_coupon ? 'Cập nhật' : 'Dùng' }}</button>
                    </div>
                </form>


                @if($applied_coupon)
                <div class="alert alert-success mb-4 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-ticket-alt me-2"></i>
                            <strong>Đã áp dụng:</strong> Mã <span class="fw-bold">{{ $applied_coupon['code'] }}</span>
                        </div>
                        <a href="{{ route('cart.remove_coupon') }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa mã giảm giá?')">
                             Xóa
                        </a>
                    </div>
                </div>
                @endif


                <div class="mb-4">
                    <div class="summary-line">
                        <span class="text-muted">Tạm tính</span>
                        <span class="fw-bold js-subtotal">{{ number_format($subtotal,0,',','.') }}đ</span>
                    </div>


                    <div class="summary-line">
                        <span class="text-muted">Giảm giá</span>
                        <span class="text-success fw-bold">
                            -<span id="discount-val" data-value="{{ $discount_val }}">{{ number_format($discount_val,0,',','.') }}</span>đ
                        </span>
                    </div>


                    <div class="summary-line">
                        <span class="text-muted">Phí giao hàng</span>
                        <span class="fw-bold">+{{ number_format($shipping_fee,0,',','.') }}đ</span>
                    </div>


                    <div class="summary-line border-0 pt-4">
                        <span class="fs-5 fw-bold text-dark">Tổng cộng</span>
                        <span class="fs-4 fw-bold text-danger js-final-total">
                            {{ number_format($final_total,0,',','.') }}đ
                        </span>
                    </div>
                </div>


                <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100 pill-btn shadow py-3 btn-checkout">
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
            <a href="{{ url('/') }}" class="btn btn-primary pill-btn px-5 shadow">KHÁM PHÁ CỬA HÀNG</a>
        </div>
    </div>
    @endif


</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const shipping = 30000;


    function formatNumber(n) {
        return n.toLocaleString('vi-VN') + 'đ';
    }


    function reCalculate() {
        let subtotal = 0;
       
        $('.item-checkbox:checked').each(function() {
            subtotal += parseFloat($(this).data('price'));
            $(this).closest('.cart-item-row').removeClass('unselected');
        });


        $('.item-checkbox:not(:checked)').each(function() {
            $(this).closest('.cart-item-row').addClass('unselected');
        });


        let discount = parseFloat($('#discount-val').data('value')) || 0;
        let total = subtotal - discount + shipping;
        if (subtotal === 0) total = 0; // Nếu không chọn cái nào thì tiền bằng 0
        if (total < 0) total = 0;


        $('.js-subtotal').text(formatNumber(subtotal));
        $('.js-final-total').text(formatNumber(total));


        // Khóa nút đặt hàng nếu không tick cái nào
        if (subtotal === 0) {
            $('.btn-checkout').addClass('disabled').css('pointer-events', 'none').css('opacity', '0.5');
        } else {
            $('.btn-checkout').removeClass('disabled').css('pointer-events', 'auto').css('opacity', '1');
        }
    }


    $('.item-checkbox').change(function() {
        reCalculate();
        // Check-all logic
        $('#check-all').prop('checked', $('.item-checkbox:checked').length === $('.item-checkbox').length);
    });


    $('#check-all').change(function() {
        $('.item-checkbox').prop('checked', $(this).prop('checked'));
        reCalculate();
    });
});
</script>


@endsection

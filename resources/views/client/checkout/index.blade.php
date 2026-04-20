@extends('layouts.client')

@section('content')
<style>
    body { background-color: #f8f9fa; }
    .khung-thanh-toan { background: #fff; border-radius: 20px; padding: 30px; border: 1px solid #eee; }
    .dia-chi-card { border: 2px solid #f1f1f1; border-radius: 15px; padding: 20px; margin-bottom: 15px; position: relative; transition: 0.3s; }
    .dia-chi-card.active { border-color: #0d6efd; background-color: #f0f7ff; box-shadow: 0 5px 15px rgba(13,110,253,0.05); }
    .nut-thanh-toan { border-radius: 30px !important; padding: 15px; font-weight: bold; }
    .phuong-thuc-item { border: 1px solid #eee; border-radius: 12px; padding: 15px; margin-bottom: 12px; transition: 0.2s; cursor: pointer; }
    .phuong-thuc-item:hover { border-color: #0d6efd; background-color: #f8faff; }
    .hanh-dong-dia-chi { font-size: 13px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; border: none; background: none; padding: 0; }
</style>

<div class="container py-5">
    <h3 class="fw-bold mb-4 text-center text-uppercase">Xác nhận thanh toán</h3>

    {{-- Hiển thị thông báo lỗi nếu Backend trả về (Ví dụ: chưa chọn địa chỉ) --}}
    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="khung-thanh-toan shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <h5 class="fw-bold m-0"><i class="fas fa-map-marker-alt text-danger me-2"></i>1. ĐỊA CHỈ NHẬN HÀNG</h5>
                    <a href="{{ route('address.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                        <i class="fas fa-plus-circle me-1"></i> Thêm mới
                    </a>
                </div>

                {{-- THÔNG BÁO NẾU CHƯA CÓ ĐỊA CHỈ --}}
                @if($addresses->isEmpty())
                    <div class="alert alert-warning rounded-4 text-center py-4">
                        <i class="fas fa-info-circle fa-2x mb-2 text-warning"></i>
                        <p class="mb-0">Bạn chưa có địa chỉ nhận hàng nào. Vui lòng bấm <b>"Thêm mới"</b> để tiếp tục đặt hàng!</p>
                    </div>
                @endif

                @foreach ($addresses as $addr)
                    <div class="dia-chi-card {{ $addr->address_id == $selected_address_id ? 'active' : '' }}">
                        <div class="row align-items-stretch">
                            <div class="col-md-8">
                                <div class="fw-bold text-dark fs-5 d-flex align-items-center">
                                    {{ $addr->fullname }} 
                                    @if ($addr->is_default == 1) 
                                        <span class="badge bg-danger-subtle text-danger ms-2" style="font-size:10px; padding: 5px 10px;">
                                            <i class="fas fa-star me-1"></i>MẶC ĐỊNH
                                        </span> 
                                    @endif
                                </div>
                                <div class="small text-muted mt-2"><i class="fas fa-phone-alt me-2"></i>{{ $addr->phone }}</div>
                                <div class="small text-secondary mt-1">
                                    <i class="fas fa-location-arrow me-2"></i>{{ $addr->street }}, {{ $addr->ward }}, {{ $addr->district }}, {{ $addr->city }}
                                </div>
                                
                                <div class="mt-3">
                                    @if ($addr->address_id != $selected_address_id)
                                        <a href="{{ route('checkout.index', ['selected_id' => $addr->address_id]) }}" class="btn btn-sm btn-outline-primary rounded-pill px-4">Chọn địa chỉ này</a>
                                    @else
                                        <span class="badge bg-success rounded-pill px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i> Đang chọn để giao hàng
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4 border-start d-flex flex-column justify-content-center ps-4"> 
                                <a href="{{ route('address.edit', $addr->address_id) }}" class="hanh-dong-dia-chi text-primary mb-2">
                                    <i class="fas fa-edit"></i> Chỉnh sửa
                                </a>

                                @if ($addr->is_default == 0)
                                    <form action="{{ route('address.default', $addr->address_id) }}" method="POST" class="mb-2">
                                        @csrf
                                        <button type="submit" class="hanh-dong-dia-chi text-info">
                                            <i class="fas fa-thumbtack"></i> Đặt mặc định
                                        </button>
                                    </form>

                                    <form action="{{ route('address.delete', $addr->address_id) }}" method="POST" onsubmit="return confirm('Xác nhận xóa địa chỉ này?')">
                                        @csrf
                                        <button type="submit" class="hanh-dong-dia-chi text-danger">
                                            <i class="fas fa-trash-alt"></i> Xóa bỏ
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
                    @csrf
                    {{-- Các input hidden mang dữ liệu địa chỉ đã chọn --}}
                    <input type="hidden" name="fullname" value="{{ $current_addr->fullname ?? '' }}">
                    <input type="hidden" name="phone" value="{{ $current_addr->phone ?? '' }}">
                    <input type="hidden" name="address" value="{{ $selected_address_text }}">

                    <div class="mt-4">
                        <label class="fw-bold small text-secondary mb-2 text-uppercase">Ghi chú đơn hàng:</label>
                        <textarea name="notes" class="form-control rounded-3" rows="2" placeholder="Ví dụ: Giao sau 5h chiều..."></textarea>
                    </div>
            </div>

            <div class="khung-thanh-toan shadow-sm">
                <h5 class="fw-bold mb-4 border-bottom pb-3"><i class="fas fa-wallet text-primary me-2"></i>2. PHƯƠNG THỨC THANH TOÁN</h5>
                <div class="phuong-thuc-item">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="pay_cod" value="COD" checked required>
                        <label class="form-check-label fw-bold ms-2" for="pay_cod">Tiền mặt khi nhận hàng (COD)</label>
                    </div>
                </div>
                <div class="phuong-thuc-item">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="pay_bank" value="Banking">
                        <label class="form-check-label fw-bold ms-2" for="pay_bank">Chuyển khoản Ngân hàng</label>
                    </div>
                </div>
            </div>
            </form>
        </div>

        <div class="col-lg-5">
            <div class="khung-thanh-toan shadow-sm sticky-top" style="top: 90px;">
                <h5 class="fw-bold mb-4 text-center">TÓM TẮT ĐƠN HÀNG</h5>
                @foreach ($cart_items as $item)
                    <div class="d-flex justify-content-between mb-3 small">
                        <span>{{ $item->title }} (x{{ $item->quantity }})</span>
                        <span class="fw-bold">{{ number_format($item->price * $item->quantity) }}đ</span>
                    </div>
                @endforeach
                <hr>
                <div class="bg-light p-3 rounded-4">
                    <div class="d-flex justify-content-between mb-2"><span>Tạm tính:</span><b>{{ number_format($subtotal) }}đ</b></div>
                    <div class="d-flex justify-content-between mb-2 text-success"><span>Giảm giá:</span><b>-{{ number_format($discount_val) }}đ</b></div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2"><span>Phí ship:</span><b>+{{ number_format($shipping_fee) }}đ</b></div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold fs-5">TỔNG CỘNG:</span>
                        <span class="fw-bold text-danger fs-3">{{ number_format($final_total) }}đ</span>
                    </div>
                </div>
                
                {{-- Nút đặt hàng: Nếu không có địa chỉ sẽ bị chặn bởi JS bên dưới --}}
                <button type="submit" id="btnSubmit" form="checkoutForm" class="btn btn-primary w-100 nut-thanh-toan shadow mt-4">
                    ĐẶT HÀNG NGAY
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    // 1. Lấy toàn bộ dữ liệu trong form
    let formData = new FormData(this);
    
    // 2. Kiểm tra địa chỉ
    let address = formData.get('address');
    let fullname = formData.get('fullname');

    if (!address || address.trim() === "" || !fullname) {
        e.preventDefault();
        alert("Vui lòng chọn hoặc thêm địa chỉ nhận hàng trước khi đặt hàng!");
        window.scrollTo({ top: 0, behavior: 'smooth' });
        return false;
    }

    // 3. KIỂM TRA PHƯƠNG THỨC THANH TOÁN (Sửa chỗ này)
    let payment = formData.get('payment_method'); // Lấy trực tiếp giá trị đang chọn
    
    if (!payment) {
        e.preventDefault();
        alert("Vui lòng chọn phương thức thanh toán!");
        return false;
    }

    // Nếu mọi thứ ok thì đổi trạng thái nút
    let btn = document.getElementById('btnSubmit');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> ĐANG XỬ LÝ...';
    btn.disabled = true;
});
</script>
@endsection
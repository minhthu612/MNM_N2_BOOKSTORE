@extends('layouts.client')

@section('content')
<style>
    body { background-color: #f8f9fa; }
    .khung-trang { background: #fff; border-radius: 15px; padding: 25px; border: 1px solid #eee; margin-bottom: 20px; }
    .anh-sach-nho { width: 55px; height: 75px; object-fit: cover; border-radius: 8px; border: 1px solid #f1f1f1; }
    .tieu-de-muc { border-bottom: 2px solid #f8f9fa; padding-bottom: 12px; margin-bottom: 20px; font-weight: bold; color: #333; }
</style>

<div class="container py-5">
    <div class="mb-4 bg-white p-3 rounded-3 shadow-sm border">
        <a href="{{ route('orders.index') }}" class="text-decoration-none fw-bold"><i class="fas fa-history me-1"></i> Lịch sử đơn hàng</a> 
        <span class="mx-2 text-muted">/</span> 
        <span class="text-secondary">Chi tiết đơn hàng #{{ $order->order_id }}</span>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="khung-trang shadow-sm">
                <h6 class="tieu-de-muc"><i class="fas fa-map-marker-alt text-danger me-2"></i>THÔNG TIN NHẬN HÀNG</h6>
                <div class="small">
                    <p class="mb-2"><strong>Người nhận:</strong> {{ $order->fullname }}</p>
                    <p class="mb-2"><strong>Số điện thoại:</strong> {{ $order->phone }}</p>
                    <p class="mb-3"><strong>Địa chỉ giao:</strong> <br><span class="text-muted">{{ $order->shipping_address }}</span></p>
                    <div class="p-2 bg-light rounded border small">
                        <strong>Ghi chú:</strong> {{ $order->notes ?: 'Không có ghi chú.' }}
                    </div>
                </div>
            </div>

            <div class="khung-trang shadow-sm">
                <h6 class="tieu-de-muc"><i class="fas fa-info-circle text-primary me-2"></i>TRẠNG THÁI</h6>
                <div class="small">
                    <p class="mb-2"><strong>Thanh toán:</strong> {{ $order->payment_method }}</p>
                    <p class="mb-3"><strong>Ngày đặt:</strong> {{ date('d/m/Y H:i', strtotime($order->created_at)) }}</p>
                    
                    @php
                        $badge = 'bg-warning text-dark'; $txt = 'Đang chờ xác nhận';
                        if($order->status == 'completed') { $badge = 'bg-success text-white'; $txt = 'Giao hàng thành công'; }
                        if($order->status == 'cancelled') { $badge = 'bg-danger text-white'; $txt = 'Đơn hàng đã hủy'; }
                        if($order->status == 'shipping') { $badge = 'bg-primary text-white'; $txt = 'Đang giao hàng'; }
                    @endphp
                    <span class="badge {{ $badge }} px-3 py-2 rounded-pill">{{ $txt }}</span>

                    @if ($order->status == 'pending')
                        <hr>
                        <form action="{{ route('orders.cancel', $order->order_id) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn?')">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100 fw-bold rounded-pill">HỦY ĐƠN HÀNG</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

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
                                            <img src="{{ $item->link_images }}" class="anh-sach-nho me-3">
                                            <div>
                                                <div class="fw-bold small">{{ $item->title }}</div>
                                                <div class="text-muted small">Giá: {{ number_format($item->price, 0, ',', '.') }}đ</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">x{{ $item->quantity }}</td>
                                    <td class="text-end pe-4 fw-bold">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="2" class="text-end ps-4 py-3 fw-bold">TỔNG CỘNG:</td>
                                <td class="text-end pe-4 py-3 fw-bold text-danger fs-5">{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')


@section('content')


<style>
    .khung-don-hang { background: #fff; padding: 25px; border-radius: 15px; border: 1px solid #eee; }
    .o-thong-tin { background: #f8f9fa; border-radius: 10px; padding: 15px; height: 100%; }
    .anh-sach { width: 50px; height: 70px; object-fit: cover; border-radius: 5px; }
    .nut-tron { border-radius: 20px !important; padding: 8px 20px !important; font-weight: bold; }


    @media print {
        .d-print-none, .sidebar, .navbar, .btn { display: none !important; }
        .khung-don-hang { border: none !important; padding: 0 !important; }
        .table { width: 100% !important; border-collapse: collapse !important; }
        .table th, .table td { border: 1px solid #000 !important; padding: 5px !important; }
    }
</style>


<div class="container-fluid">
    <div class="khung-don-hang shadow-sm">


        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h4 class="fw-bold text-primary mb-0">
                    CHI TIẾT ĐƠN HÀNG #{{ $order->order_id }}
                </h4>
                <small class="text-muted">
                    Ngày đặt:
                    {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}
                </small>
            </div>


            <div>
                @if($order->status == 'pending')
                    <span class="badge rounded-pill bg-warning text-dark px-3 py-2">Đang chờ xử lý</span>
                @elseif($order->status == 'processing')
                    <span class="badge rounded-pill bg-info px-3 py-2">Đang đóng gói</span>
                @elseif($order->status == 'shipped')
                    <span class="badge rounded-pill bg-primary px-3 py-2">Đang giao hàng</span>
                @elseif($order->status == 'delivered')
                    <span class="badge rounded-pill bg-success px-3 py-2">Đã giao thành công</span>
                @else
                    <span class="badge rounded-pill bg-danger px-3 py-2">Đã hủy đơn</span>
                @endif
            </div>
        </div>


        {{-- INFO --}}
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="o-thong-tin">
                    <h6 class="fw-bold text-secondary border-bottom pb-2 mb-3">
                        <i class="fas fa-user me-2"></i>THÔNG TIN NGƯỜI NHẬN
                    </h6>
                    <p class="mb-1">Họ tên: <strong>{{ $order->fullname }}</strong></p>
                    <p class="mb-1">SĐT: <strong>{{ $order->phone }}</strong></p>
                    <p class="mb-1">Email: {{ $order->email }}</p>
                </div>
            </div>


            <div class="col-md-6">
                <div class="o-thong-tin">
                    <h6 class="fw-bold text-secondary border-bottom pb-2 mb-3">
                        <i class="fas fa-map-marker-alt me-2"></i>ĐỊA CHỈ GIAO HÀNG
                    </h6>
                    <p class="mb-0">{!! nl2br($order->shipping_address) !!}</p>
                    <div class="mt-2 small text-muted">
                        Phương thức: <b>{{ $order->payment_method }}</b>
                    </div>
                </div>
            </div>
        </div>


        {{-- ITEMS --}}
        <h6 class="fw-bold mb-3">
            <i class="fas fa-list me-2"></i>DANH SÁCH SÁCH ĐÃ ĐẶT
        </h6>


        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th width="80">Ảnh</th>
                        <th class="text-start">Tên sách</th>
                        <th width="100">SL</th>
                        <th width="150">Đơn giá</th>
                        <th width="150">Thành tiền</th>
                    </tr>
                </thead>


                <tbody>
                    @php $tong_sl = 0; @endphp


                    @foreach($items as $item)
                        @php
                            $thanh_tien = $item->quantity * $item->price;
                            $tong_sl += $item->quantity;
                        @endphp


                        <tr>
                            <td class="text-center">
                                <img src="{{ $item->link_images }}" class="anh-sach shadow-sm">
                            </td>


                            <td>
                                <div class="fw-bold">{{ $item->title }}</div>
                                <small class="text-muted">Mã sách: #{{ $item->book_id }}</small>
                            </td>


                            <td class="text-center">{{ $item->quantity }}</td>


                            <td class="text-end">
                                {{ number_format($item->price) }}đ
                            </td>


                            <td class="text-end fw-bold text-primary">
                                {{ number_format($thanh_tien) }}đ
                            </td>
                        </tr>
                    @endforeach
                </tbody>


                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end fw-bold">
                            TỔNG CỘNG:
                        </td>
                        <td class="text-end fw-bold text-danger fs-5">
                            {{ number_format($order->total_amount) }}đ
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>


        {{-- ACTION --}}
        <div class="pt-3 border-top d-flex justify-content-between d-print-none">


            {{-- LEFT --}}
            <div class="d-flex gap-2">
                <a href="{{ route('admin.orders.index') }}"
                   class="btn btn-outline-secondary nut-tron">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>


                <button onclick="window.print()" class="btn btn-dark nut-tron">
                    <i class="fas fa-print me-2"></i>In hóa đơn
                </button>
            </div>


            {{-- RIGHT --}}
            <div class="d-flex gap-2">


                @if($order->status == 'pending')
                    <a href="{{ route('admin.orders.update', $order->order_id) }}?action=ship"
                       class="btn btn-info text-white nut-tron">
                        Xác nhận đơn
                    </a>
                @endif


                @if($order->status == 'processing')
                    <a href="{{ route('admin.orders.update', $order->order_id) }}?action=ship"
                       class="btn btn-primary nut-tron">
                        Giao hàng
                    </a>
                @endif


                @if($order->status == 'shipped')
                    <a href="{{ route('admin.orders.update', $order->order_id) }}?action=deliver"
                       class="btn btn-success nut-tron">
                        Hoàn tất
                    </a>
                @endif


                @if(!in_array($order->status, ['delivered','cancelled']))
                    <a href="{{ route('admin.orders.update', $order->order_id) }}?action=cancel"
                       class="btn btn-danger nut-tron"
                       onclick="return confirm('Bạn có chắc muốn hủy đơn này?')">
                        Hủy đơn
                    </a>
                @endif


            </div>
        </div>


    </div>
</div>


@endsection

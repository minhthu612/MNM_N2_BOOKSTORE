@extends('layouts.client')


@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0"><i class="fas fa-box me-2"></i>ĐƠN HÀNG CỦA TÔI</h2>
        <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Tiếp tục mua sắm</a>
    </div>


    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-3">{{ session('success') }}</div>
    @endif


    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Phương thức</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        @php
                            $status_map = [
                                'pending'   => ['class' => 'bg-warning text-dark', 'text' => 'Chờ xử lý'],
                                'confirmed' => ['class' => 'bg-info text-white', 'text' => 'Đã xác nhận'],
                                'shipping'  => ['class' => 'bg-primary text-white', 'text' => 'Đang giao'],
                                'completed' => ['class' => 'bg-success text-white', 'text' => 'Thành công'],
                                'cancelled' => ['class' => 'bg-danger text-white', 'text' => 'Đã hủy'],
                            ];
                            $curr = $status_map[$order->status] ?? ['class' => 'bg-secondary', 'text' => 'Không xác định'];
                        @endphp
                        <tr>
                            <td class="ps-4 fw-bold text-primary">#{{ $order->order_id }}</td>
                            <td>{{ \Carbon\Carbon::parse($order->created_at)->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}</td>
                            <td><small class="fw-bold">{{ $order->payment_method }}</small></td>
                            <td class="fw-bold text-danger">{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                            <td>
                                <span class="badge {{ $curr['class'] }} rounded-pill px-3 py-2">
                                    {{ $curr['text'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('orders.show', $order->order_id) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                                    Chi tiết
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Bạn chưa có đơn hàng nào trong lịch sử.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

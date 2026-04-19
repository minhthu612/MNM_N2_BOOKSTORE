@extends('layouts.app')
@section('title', 'Quản lý đơn hàng')


@section('content')




<style>
   




    .the-bang { background: #fff; border-radius: 12px; }
    .bang-du-lieu th { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; }
    .form-control, .form-select { border-radius: 20px; }
    .stat-box { border-radius: 15px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .nut-hanh-dong {
        border-radius: 20px !important;
        padding: 5px 15px !important;
        margin: 0 3px;
        font-size: 0.85rem;
        display: inline-block;
        text-decoration: none;
    }
    .badge-tron { border-radius: 20px; padding: 6px 12px; }
</style>




<div class="container-fluid">




    {{-- STATS --}}
    <div class="row g-3 mb-4 text-center">
        <div class="col-md-4">
            <div class="card stat-box bg-primary text-white p-3">
                <h3 class="mb-0 fw-bold">{{ $stats->total_orders }}</h3>
                <div class="small">Tổng số đơn hàng</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-box bg-warning text-dark p-3">
                <h3 class="mb-0 fw-bold">{{ $stats->pending_count }}</h3>
                <div class="small">Đơn chờ xử lý</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-box bg-success text-white p-3">
                <h3 class="mb-0 fw-bold">{{ number_format($stats->total_revenue) }}đ</h3>
                <div class="small">Tổng doanh thu</div>
            </div>
        </div>
    </div>




    <div class="card the-bang border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 text-primary fw-bold">
                <i class="fas fa-shopping-cart me-2"></i>DANH SÁCH ĐƠN HÀNG
            </h5>
        </div>




        <div class="card-body">




            {{-- FILTER --}}
            <form method="GET" class="row g-2 mb-4">
                <div class="col-md-3">
                    <input type="text" class="form-control px-3"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Mã đơn, tên khách...">
                </div>




                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">-- Trạng thái --</option>
                        <option value="pending" {{ $status=='pending'?'selected':'' }}>Chờ xử lý</option>
                        <option value="processing" {{ $status=='processing'?'selected':'' }}>Đang xử lý</option>
                        <option value="shipped" {{ $status=='shipped'?'selected':'' }}>Đang giao</option>
                        <option value="delivered" {{ $status=='delivered'?'selected':'' }}>Đã giao</option>
                        <option value="cancelled" {{ $status=='cancelled'?'selected':'' }}>Đã hủy</option>
                    </select>
                </div>




                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" value="{{ $date_from }}">
                </div>




                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" value="{{ $date_to }}">
                </div>




                <div class="col-md-3 d-flex gap-1">
                    <button class="btn btn-primary w-100 rounded-pill">Lọc</button>
                    <a href="{{ route('admin.orders.index') }}"
                       class="btn btn-outline-secondary w-100 rounded-pill">Reset</a>
                </div>
            </form>




            {{-- TABLE --}}
            <div class="table-responsive">
                <table  class="table table-bordered table-hover bang-du-lieu align-middle">
                    <thead class="table-light text-center">
                    <tr>
                        <th width="100">Mã đơn</th>
                        <th class="text-start">Khách hàng</th>
                        <th>Số lượng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày đặt</th>
                        <th width="220">Thao tác</th>
                    </tr>
                    </thead>




                    <tbody>
                    @forelse($orders as $o)
                        <tr class="text-center">
                            <td>
                                <span class="fw-bold text-primary">#{{ $o->order_id }}</span>
                            </td>




                            <td class="text-start">
                                <div class="fw-bold">
                                    {{ $o->fullname ?? $o->username }}
                                </div>
                            </td>




                            <td>
                                <span class="badge bg-info text-dark px-3 py-2">
                                    {{ $o->total_qty }} cuốn
                                </span>
                            </td>




                            <td>
                                <span class="fw-bold text-danger">
                                    {{ number_format($o->total_amount) }}đ
                                </span>
                            </td>




                            <td>
                                @if($o->status=='pending')
                                    <span class="badge bg-warning text-dark badge-tron">CHỜ XỬ LÝ</span>
                                @elseif($o->status=='processing')
                                    <span class="badge bg-info badge-tron">ĐANG XỬ LÝ</span>
                                @elseif($o->status=='shipped')
                                    <span class="badge bg-primary badge-tron">ĐANG GIAO</span>
                                @elseif($o->status=='delivered')
                                    <span class="badge bg-success badge-tron">ĐÃ GIAO</span>
                                @else
                                    <span class="badge bg-danger badge-tron">ĐÃ HỦY</span>
                                @endif
                            </td>




                            <td>
                                <div class="small">
                                    {{ \Carbon\Carbon::parse($o->created_at)->format('d/m/Y') }}
                                </div>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    {{ \Carbon\Carbon::parse($o->created_at)->format('H:i') }}
                                </div>
                            </td>




                            <td>
                                <a href="{{ route('admin.orders.show', $o->order_id) }}?{{ http_build_query(request()->all()) }}"
                                   class="btn btn-info text-white nut-hanh-dong">
                                    <i class="fas fa-eye"></i> Xem
                                </a>




                                <a href="{{ route('admin.orders.update',$o->order_id) }}?{{ http_build_query(request()->all()) }}"
                                   class="btn btn-warning text-dark nut-hanh-dong">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                Không có đơn hàng nào.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                </div>




                {{-- pagination --}}
                <div class="mt-4 d-flex justify-content-center">
                    {{ $orders->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>




</div>


@endsection


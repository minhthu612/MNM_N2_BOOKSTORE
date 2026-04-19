@extends('layouts.app')
@section('title', 'Báo cáo tồn kho')
@section('content')


<style>
    .the-bang { background: #fff; border-radius: 12px; }
    .bang-du-lieu th { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; }
    .form-control, .form-select { border-radius: 20px; }
   
    .the-thong-ke {
        border-radius: 15px;
        border: none;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        transition: 0.3s;
    }
   
    .nut-hanh-dong {
        border-radius: 20px !important;
        padding: 5px 15px !important;
        font-size: 0.85rem;
        display: inline-block;
        text-decoration: none;
    }
   
    .badge-tron {
        border-radius: 20px;
        padding: 6px 12px;
    }
</style>




<div class="container-fluid">




    <div class="row g-3 mb-4 text-center">
        <div class="col-md-4">
            <div class="card the-thong-ke bg-primary text-white p-3">
                <h3 class="fw-bold mb-0">{{ number_format($stats->total_qty) }}</h3>
                <div class="small">Tổng số lượng tồn kho</div>
            </div>
        </div>




        <div class="col-md-4">
            <div class="card the-thong-ke bg-success text-white p-3">
                <h3 class="fw-bold mb-0">{{ $stats->in_stock }}</h3>
                <div class="small">Đầu sách còn hàng</div>
            </div>
        </div>




        <div class="col-md-4">
            <div class="card the-thong-ke bg-danger text-white p-3">
                <h3 class="fw-bold mb-0">{{ $stats->out_of_stock }}</h3>
                <div class="small">Đầu sách hết hàng</div>
            </div>
        </div>
    </div>




    <div class="card the-bang border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary fw-bold">CHI TIẾT TỒN KHO HIỆN TẠI</h5>
            <span class="badge rounded-pill bg-light text-dark border">
                Cập nhật lúc: {{ date('H:i d/m/Y') }}
            </span>
        </div>




        <div class="card-body">
            <form method="GET" action="" class="row g-2 mb-4">
                <div class="col-md-5">
                    <input type="text" class="form-control px-4" name="search"
                           placeholder="Tìm tên sách hoặc tác giả..."
                           value="{{ $search }}">
                </div>




                <div class="col-md-3">
                    <select class="form-select px-4" name="status">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="ACTIVE" {{ $status_filter=='ACTIVE'?'selected':'' }}>Còn hàng</option>
                        <option value="LOW_STOCK" {{ $status_filter=='LOW_STOCK'?'selected':'' }}>Sắp hết hàng</option>
                        <option value="OUT_OF_STOCK" {{ $status_filter=='OUT_OF_STOCK'?'selected':'' }}>Đã hết hàng</option>
                    </select>
                </div>




                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Lọc kết quả</button>
                </div>




                <div class="col-md-2">
                    <a href="{{ route('admin.inventory.history') }}"
                       class="btn btn-outline-secondary w-100 rounded-pill">Xóa lọc</a>
                </div>
            </form>




            <div class="table-responsive">
                <table class="table table-bordered table-hover bang-du-lieu align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="150">Cập nhật cuối</th>
                            <th class="text-start">Thông tin sản phẩm</th>
                            <th width="120">Số lượng</th>
                            <th width="150">Trạng thái</th>
                            <th width="120">Thao tác</th>
                        </tr>
                    </thead>




                    <tbody>
                        @if(count($inventory_list) > 0)




                            @foreach ($inventory_list as $row)
                                <tr class="text-center">




                                    <td>
                                        <div class="small fw-bold">
                                            {{ date('d/m/Y', strtotime($row->last_updated)) }}
                                        </div>
                                        <div class="text-muted small">
                                            {{ date('H:i', strtotime($row->last_updated)) }}
                                        </div>
                                    </td>




                                    <td class="text-start">
                                        <div class="fw-bold">{{ $row->title }}</div>
                                        <small class="text-muted">{{ $row->author }}</small>
                                    </td>




                                    <td>
                                        <span class="fw-bold {{ $row->stock <= 0 ? 'text-danger':'text-dark' }}">
                                            {{ $row->stock }}
                                        </span>
                                    </td>




                                    <td>
                                        @if($row->stock_status == 'ACTIVE')
                                            <span class="badge bg-success badge-tron">CÒN HÀNG</span>
                                        @elseif($row->stock_status == 'LOW_STOCK')
                                            <span class="badge bg-warning text-dark badge-tron">SẮP HẾT</span>
                                        @else
                                            <span class="badge bg-danger badge-tron">HẾT HÀNG</span>
                                        @endif
                                    </td>




                                    <td>
                                        <a href="{{ route('admin.inventory.edit', $row->inventory_id) }}"
                                           class="btn btn-info text-white nut-hanh-dong">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                    </td>




                                </tr>
                            @endforeach




                        @else
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    Không tìm thấy dữ liệu tồn kho.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>




            @if($total_pages > 1)
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        @for($i = 1; $i <= $total_pages; $i++)
                            <li class="page-item {{ $i == $page ? 'active' : '' }}">
                                <a class="page-link shadow-none"
                                   href="?page={{ $i }}&search={{ $search }}&status={{ $status_filter }}">
                                    {{ $i }}
                                </a>
                            </li>
                        @endfor
                    </ul>
                </nav>
            @endif




        </div>
    </div>
</div>


@endsection

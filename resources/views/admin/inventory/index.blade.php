@extends('layouts.app')
@section('title', 'Quản lý tồn kho')
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


    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">
            {{ session('success') }}
        </div>
    @endif


    <div class="row g-3 mb-4 text-center">
        <div class="col">
            <div class="card stat-box bg-dark text-white p-3">
                <h3 class="mb-0 fw-bold">{{ $stats->total_items }}</h3>
                <div class="small">Tổng sách</div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-box bg-danger text-white p-3">
                <h3 class="mb-0 fw-bold">{{ $stats->zero }}</h3>
                <div class="small">Hết hàng</div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-box bg-warning text-dark p-3">
                <h3 class="mb-0 fw-bold">{{ $stats->low }}</h3>
                <div class="small">Sắp hết</div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-box bg-success text-white p-3">
                <h3 class="mb-0 fw-bold">{{ $stats->good }}</h3>
                <div class="small">Đủ hàng</div>
            </div>
        </div>
    </div>


    <div class="card the-bang border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary fw-bold">DANH SÁCH TỒN KHO</h5>


            <a href="{{ route('admin.inventory.index', ['auto_update'=>'true']) }}"
               class="btn btn-warning btn-sm rounded-pill px-3"
               onclick="return confirm('Cập nhật trạng thái tự động?')">
                <i class="fas fa-sync"></i> Tự động cập nhật
            </a>
        </div>


        <div class="card-body">
            <form method="GET" action="" class="row g-2 mb-4">
                <div class="col-md-4">
                    <input type="text" class="form-control px-3" name="search"
                           placeholder="Tên sách, tác giả..."
                           value="{{ $search }}">
                </div>


                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="all">Mọi trạng thái</option>
                        <option value="ACTIVE" {{ $status=='ACTIVE'?'selected':'' }}>Đủ hàng</option>
                        <option value="LOW_STOCK" {{ $status=='LOW_STOCK'?'selected':'' }}>Sắp hết</option>
                        <option value="OUT_OF_STOCK" {{ $status=='OUT_OF_STOCK'?'selected':'' }}>Hết hàng</option>
                    </select>
                </div>


                <div class="col-md-2">
                    <select name="stock_filter" class="form-select">
                        <option value="">Mọi số lượng</option>
                        <option value="zero" {{ $stock_filter=='zero'?'selected':'' }}>Hết hàng (0)</option>
                        <option value="low" {{ $stock_filter=='low'?'selected':'' }}>Dưới mức đặt lại</option>
                    </select>
                </div>


                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Lọc</button>
                </div>


                <div class="col-md-2">
                    <a href="{{ route('admin.inventory.index') }}"
                       class="btn btn-outline-secondary w-100 rounded-pill">Reset</a>
                </div>
            </form>


            <div class="table-responsive">
                <table id="book-table" class="table table-bordered table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="60">ID</th>
                            <th class="text-start">Tên sách</th>
                            <th>Tồn kho</th>
                            <th>Mức tối thiểu</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>


                    <tbody>
                        @if(count($inventory_list) > 0)
                            @foreach($inventory_list as $item)
                                <tr class="text-center">
                                    <td>{{ $item->inventory_id }}</td>


                                    <td class="text-start">
                                        <div class="fw-bold">{{ $item->title }}</div>
                                        <small class="text-muted">{{ $item->author }}</small>
                                    </td>


                                    <td>
                                        <span class="fw-bold {{ $item->stock <= 0 ? 'text-danger':'text-dark' }}">
                                            {{ $item->stock }}
                                        </span>
                                    </td>


                                    <td>{{ $item->reorder_level }}</td>


                                    <td>
                                        @if($item->stock_status == 'ACTIVE')
                                            <span class="badge bg-success badge-tron text-white">ĐỦ HÀNG</span>
                                        @elseif($item->stock_status == 'LOW_STOCK')
                                            <span class="badge bg-warning badge-tron text-dark">SẮP HẾT</span>
                                        @else
                                            <span class="badge bg-danger badge-tron text-white">HẾT HÀNG</span>
                                        @endif
                                    </td>


                                    <td>
                                        <a href="{{ route('admin.inventory.edit', $item->inventory_id) }}"
                                           class="btn btn-info text-white nut-hanh-dong">
                                            <i class="fas fa-edit"></i> Sửa kho
                                        </a>


                                        <a href="{{ route('admin.inventory.history') }}"
                                           class="btn btn-secondary nut-hanh-dong text-white">
                                            <i class="fas fa-history"></i> Log
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    Không có dữ liệu kho.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>


            @if($total_pages > 1)
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        @for($i=1;$i<=$total_pages;$i++)
                            <li class="page-item {{ $i==$page?'active':'' }}">
                                <a class="page-link shadow-none"
                                   href="?page={{ $i }}&search={{ $search }}&status={{ $status }}&stock_filter={{ $stock_filter }}">
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


<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>


<script>
$(document).ready(function () {
    $('#book-table').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],  
        stateSave: true,
        autoWidth: false
    });
});


@endsection
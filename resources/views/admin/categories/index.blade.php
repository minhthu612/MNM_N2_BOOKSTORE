@extends('layouts.app')
@section('title', 'Quản lý danh mục')
@section('content')


<div class="container-fluid">
   
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif


    <div class="card the-bang border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 text-primary fw-bold">DANH SÁCH DANH MỤC</h5>
                <p class="mb-0 small text-muted">Tổng số: {{ $total }} loại sách</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-success rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus"></i> Thêm mới
            </a>
        </div>


        <div class="card-body">
            <form method="GET" class="row g-2 mb-4">
                <div class="col-md-6">
                    <input type="text" class="form-control px-4" name="search" value="{{ $search }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary w-100 rounded-pill">Lọc</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary w-100 rounded-pill">Xóa</a>
                </div>
            </form>


            <form method="POST">
                @csrf
                <div class="table-responsive">
                    <table id="book-table" class="table table-bordered table-hover">
                        <thead class="text-center">
                            <tr>
                                <th><input type="checkbox"></th>
                                <th>ID</th>
                                <th class="text-start">Tên</th>
                                <th>Số sách</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $cat)
                            <tr class="text-center">
                                <td><input type="checkbox" name="selected_ids[]" value="{{ $cat->category_id }}"></td>
                                <td>#{{ $cat->category_id }}</td>
                                <td class="text-start">{{ $cat->category_name }}</td>
                                <td>{{ $cat->book_count }}</td>
                                <td>
                                    <a href="{{ route('admin.categories.edit',$cat->category_id) }}" class="btn btn-info text-white">Sửa</a>
                                    <a href="{{ route('admin.categories.delete',$cat->category_id) }}" class="btn btn-danger">Xóa</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>


                <button name="delete_selected" class="btn btn-danger">Xóa đã chọn</button>
            </form>
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
        lengthMenu: [10, 25, 50, 100]
    });
});


@endsection
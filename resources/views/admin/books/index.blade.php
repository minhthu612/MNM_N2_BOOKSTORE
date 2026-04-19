@extends('layouts.app')
@section('title', 'Quản lý sách')
@section('content')


<style>
    .the-bang { background: #fff; border-radius: 12px; }
    .bang-du-lieu th { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; }
    .form-control, .form-select { border-radius: 20px; }
   
    .nut-hanh-dong {
        border-radius: 20px !important;
        padding: 5px 15px !important;
        margin: 0 3px;
        font-size: 0.85rem;
        display: inline-block;
        text-decoration: none;
        transition: all 0.3s;
    }
    .nut-hanh-dong:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
   
    .anh-sach-nho {
        width: 45px;
        height: 60px;
        object-fit: cover;
        border-radius: 5px;
        border: 1px solid #eee;
    }
   
    .gia-goc { text-decoration: line-through; color: #888; font-size: 0.85rem; }
    .gia-moi { color: #d9534f; font-weight: bold; }
    .badge-tron { border-radius: 20px; padding: 6px 12px; }


   
</style>


<div class="container-fluid">

<div class="card the-bang border-0 shadow-sm">
<div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
<div>
<h5 class="mb-0 text-primary fw-bold">QUẢN LÝ KHO SÁCH</h5>
<p class="mb-0 small text-muted">Hiện có {{$total_books}} cuốn sách trong hệ thống</p>
</div>
<a href="{{route('admin.books.create')}}" class="btn btn-success rounded-pill px-4 shadow-sm">
<i class="fas fa-plus-circle me-1"></i> Thêm sách mới
</a>
</div>


<div class="card-body">


<form method="GET" action="{{ route('admin.books.index') }}" class="row g-2 mb-4">
<div class="col-md-4">
<input type="text" class="form-control px-3" name="search" value="{{$search}}">
</div>


<div class="col-md-2">
<select name="category_id" class="form-select">
<option value="">-- Danh mục --</option>
@foreach($categories_data as $cat)
<option value="{{$cat->category_id}}" @if($cat->category_id==$category_id) selected @endif>
{{$cat->category_name}}
</option>
@endforeach
</select>
</div>


<div class="col-md-2">
<select name="status" class="form-select">
<option value="">-- Trạng thái --</option>
<option value="active" @if($status=='active') selected @endif>Còn hàng</option>
<option value="out_of_stock" @if($status=='out_of_stock') selected @endif>Hết hàng</option>
</select>
</div>


<div class="col-md-2">
<button class="btn btn-primary w-100 rounded-pill">Lọc</button>
</div>


<div class="col-md-2">
<a href="{{route('admin.books.index')}}" class="btn btn-outline-secondary w-100 rounded-pill">Xóa lọc</a>
</div>
</form>


<div class="table-responsive">
<table class="table table-bordered table-hover bang-du-lieu align-middle">
<thead class="table-light text-center">
<tr>
<th>ID</th>
<th>Ảnh</th>
<th>Thông tin sách</th>
<th>Danh mục</th>
<th>Giá</th>
<th>Tồn</th>
<th>Thao tác</th>
</tr>
</thead>


<tbody>
@foreach($books_list as $b)
<tr class="text-center">
<td>#{{$b->book_id}}</td>


<td>
@php
$img = $b->link_images;
if($img==''){ $img='https://via.placeholder.com/60x80?text=No+Image'; }
@endphp
<img src="{{$img}}" class="anh-sach-nho">
</td>


<td class="text-start">
<div class="fw-bold">{{$b->title}}</div>
<small>Tác giả: {{$b->author}}</small>
</td>


<td>{{ $b->category_name ?? 'Chưa có' }}</td>


<td>
@if($b->discount>0)
<div class="gia-goc">{{number_format($b->price)}}đ</div>
@php $gia = $b->price - ($b->price*$b->discount/100); @endphp
<div class="gia-moi">{{number_format($gia)}}đ</div>
@else
{{number_format($b->price)}}đ
@endif
</td>


<td>{{ $b->stock ?? 0 }}</td>


<td>
<a href="{{route('admin.books.edit',['id'=>$b->book_id])}}" class="btn btn-info text-white nut-hanh-dong">Sửa</a>
<a href="{{route('admin.books.delete',['id'=>$b->book_id])}}" class="btn btn-danger nut-hanh-dong">Xóa</a>
</td>
</tr>
@endforeach
</tbody>
</table>




</div>


</div>
</div>
</div>
<div class="d-flex justify-content-center mt-4">
    <ul class="pagination">


        {{-- Prev --}}
        <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
            <a class="page-link" href="?page={{ $page - 1 }}&search={{ $search }}&category_id={{ $category_id }}&status={{ $status }}">
                «
            </a>
        </li>


        {{-- Trang đầu --}}
        <li class="page-item {{ $page == 1 ? 'active' : '' }}">
            <a class="page-link" href="?page=1&search={{ $search }}&category_id={{ $category_id }}&status={{ $status }}">1</a>
        </li>


        {{-- ... trước --}}
        @if($start_page > 2)
            <li class="page-item disabled"><span class="page-link">...</span></li>
        @endif


        {{-- Pages giữa --}}
        @for($i = $start_page; $i <= $end_page; $i++)
            @if($i != 1 && $i != $total_pages)
                <li class="page-item {{ $page == $i ? 'active' : '' }}">
                    <a class="page-link" href="?page={{ $i }}&search={{ $search }}&category_id={{ $category_id }}&status={{ $status }}">
                        {{ $i }}
                    </a>
                </li>
            @endif
        @endfor


        {{-- ... sau --}}
        @if($end_page < $total_pages - 1)
            <li class="page-item disabled"><span class="page-link">...</span></li>
        @endif


        {{-- Trang cuối --}}
        @if($total_pages > 1)
        <li class="page-item {{ $page == $total_pages ? 'active' : '' }}">
            <a class="page-link" href="?page={{ $total_pages }}&search={{ $search }}&category_id={{ $category_id }}&status={{ $status }}">
                {{ $total_pages }}
            </a>
        </li>
        @endif


        {{-- Next --}}
        <li class="page-item {{ $page >= $total_pages ? 'disabled' : '' }}">
            <a class="page-link" href="?page={{ $page + 1 }}&search={{ $search }}&category_id={{ $category_id }}&status={{ $status }}">
                »
            </a>
        </li>


    </ul>
</div>
@endsection

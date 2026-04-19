@extends('layouts.app')


@section('title', 'Xóa sách')


@section('content')


{{-- ===== GIAO DIỆN CONFIRM KIỂU BROWSER ===== --}}
@if(request('step') == 'confirm')


<div class="d-flex justify-content-center align-items-center" style="height:300px;">
    <div class="border rounded shadow p-4 bg-white" style="min-width:320px;">
        <div class="mb-3 fw-bold">
            localhost says
        </div>


        <div class="mb-4">
            Xóa?
        </div>


        <div class="text-end">
            <form method="POST" action="{{ route('admin.books.destroy', $book->book_id) }}" class="d-inline">
                @csrf
                <button type="submit" name="confirm" class="btn btn-primary btn-sm">
                    OK
                </button>
            </form>


            <a href="{{ route('admin.books.index') }}" class="btn btn-outline-secondary btn-sm ms-2">
                Cancel
            </a>
        </div>
    </div>
</div>


@else


{{-- ===== HIỂN THỊ LỖI ===== --}}
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif


<div class="card">
    <div class="card-header bg-white border-bottom">
        <h4 class="mb-0">Xóa sách</h4>
    </div>


    <div class="card-body">


        {{-- ===== CẢNH BÁO ĐƠN HÀNG ===== --}}
        @if (!empty($check->count) && $check->count > 0)
            <div class="alert alert-warning">
                Sách đang tồn tại trong <strong>{{ $check->count }}</strong> đơn hàng.
            </div>
        @endif


        <div class="row align-items-center">


            <div class="col-md-3">
                <strong>{{ $book->title }}</strong>
                <div class="text-muted small">{{ $book->author }}</div>
            </div>


            <div class="col-md-2">
                {{ number_format($book->price) }} đ
            </div>


            <div class="col-md-2">
                @php
                    $stock = 0;
                    if (isset($book->stock)) {
                        $stock = $book->stock;
                    }
                @endphp


                @if ($stock <= 0)
                    <span class="badge bg-danger">{{ $stock }}</span>
                @else
                    <span class="badge bg-success">{{ $stock }}</span>
                @endif
            </div>


            <div class="col-md-2">
                @if ($stock <= 0)
                    <span class="badge bg-danger">Hết hàng</span>
                @else
                    <span class="badge bg-success">Còn hàng</span>
                @endif
            </div>


            <div class="border-top pt-4 mt-4">
                <form method="POST" action="{{ route('admin.books.destroy', $book->book_id) }}">
                    @csrf


                    <div class="text-center">
                        <h5 class="text-danger mb-3">
                            Bạn có chắc chắn muốn xóa vĩnh viễn sách này?
                        </h5>


                        <button type="submit" name="confirm" class="btn btn-danger">
                            XÁC NHẬN XÓA
                        </button>


                        <a href="{{ route('admin.books.index') }}" class="btn btn-secondary">
                            HỦY
                        </a>
                    </div>
                </form>
            </div>


        </div>


    </div>
</div>


@endif


@endsection

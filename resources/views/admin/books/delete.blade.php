@extends('layouts.app')

@section('title', 'Xóa sách')

@section('content')

<style>
    .khung-canh-bao {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 30px;
        margin-top: 20px;
    }
    .thong-tin-anh {
        border: 1px solid #ddd;
        padding: 5px;
        background: #f9f9f9;
        max-height: 250px;
        border-radius: 5px;
        object-fit: contain;
    }
    .tieu-de-canh-bao {
        color: #d9534f;
        font-weight: bold;
        border-bottom: 2px solid #d9534f;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
</style>

{{-- ===== GIAO DIỆN CONFIRM KIỂU BROWSER (Giữ nguyên logic cũ) ===== --}}
@if(request('step') == 'confirm')
<div class="d-flex justify-content-center align-items-center" style="height:300px;">
    <div class="border rounded shadow p-4 bg-white" style="min-width:320px;">
        <div class="mb-3 fw-bold">localhost says</div>
        <div class="mb-4">Xóa?</div>
        <div class="text-end">
            <form method="POST" action="{{ route('admin.books.destroy', $book->book_id) }}" class="d-inline">
                @csrf
                <button type="submit" name="confirm" class="btn btn-primary btn-sm">OK</button>
            </form>
            <a href="{{ route('admin.books.index') }}" class="btn btn-outline-secondary btn-sm ms-2">Cancel</a>
        </div>
    </div>
</div>

@else

<div class="container">
    <div class="khung-canh-bao shadow-sm">
        
        <h3 class="tieu-de-canh-bao text-center">
            <i class="fas fa-exclamation-triangle"></i> XÁC NHẬN XÓA VĨNH VIỄN
        </h3>

        {{-- HIỂN THỊ LỖI --}}
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- CẢNH BÁO ĐƠN HÀNG --}}
        @if (!empty($check->count) && $check->count > 0)
            <div class="alert alert-warning border-warning">
                <i class="fas fa-exclamation-circle me-2"></i>
                Sách này đang tồn tại trong <strong>{{ $check->count }}</strong> đơn hàng. Hãy cân nhắc kỹ trước khi xóa!
            </div>
        @endif

        <div class="row">
            <div class="col-md-5 text-center mb-4">
                @php
                    $extensions = ['webp', 'jpg', 'png', 'jpeg'];
                    $imagePath = 'images/no-image.jpg'; 
                    $id = $book->book_id;

                    foreach ($extensions as $ext) {
                        if (file_exists(storage_path("app/public/image/{$id}.{$ext}"))) {
                            $imagePath = "storage/image/{$id}.{$ext}";
                            break;
                        }
                    }

                    if ($imagePath == 'images/no-image.jpg' && !empty($book->link_images)) {
                        $finalSrc = $book->link_images;
                    } else {
                        $finalSrc = asset($imagePath);
                    }
                @endphp

                <img src="{{ $finalSrc }}" 
                     class="thong-tin-anh img-fluid" 
                     alt="{{ $book->title }}"
                     onerror="this.src='{{ asset('images/no-image.jpg') }}'">

                <div class="mt-3">
                    <span class="badge bg-secondary p-2">ID Sách: #{{ $book->book_id }}</span>
                </div>
            </div>

            <div class="col-md-7">
                <div class="alert alert-danger border-danger bg-light">
                    <h5>Bạn đang thực hiện xóa sách:</h5>
                    <p class="display-6" style="font-size: 1.5rem; font-weight: bold; color: #333;">
                        {{ $book->title }}
                    </p>
                    <hr>
                    <ul class="mb-0">
                        <li>Tác giả: <strong>{{ $book->author }}</strong></li>
                        <li>Giá bán: <strong>{{ number_format($book->price) }} đ</strong></li>
                        <li>
                            Trạng thái kho: 
                            @php $stock = $book->stock ?? 0; @endphp
                            @if ($stock <= 0)
                                <span class="badge bg-danger">Hết hàng ({{ $stock }})</span>
                            @else
                                <span class="badge bg-success">Còn hàng ({{ $stock }})</span>
                            @endif
                        </li>
                        <li class="mt-2 text-danger fw-bold">
                            <i class="fas fa-radiation"></i> Dữ liệu này sẽ bị xóa hoàn toàn khỏi hệ thống!
                        </li>
                    </ul>
                </div>

                <div class="card border-danger mt-4">
                    <div class="card-body bg-light">
                        <p class="text-center text-muted small">
                            Bấm "XÁC NHẬN XÓA" để hoàn tất hoặc "QUAY LẠI" để hủy bỏ thao tác này.
                        </p>

                        <form method="POST" action="{{ route('admin.books.destroy', $book->book_id) }}">
                            @csrf
                            <div class="row g-2">
                                <div class="col-6">
                                    <button type="submit" name="confirm" class="btn btn-danger btn-lg w-100 fw-bold">
                                        <i class="fas fa-check-circle"></i> XÁC NHẬN XÓA
                                    </button>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('admin.books.index') }}" class="btn btn-secondary btn-lg w-100 fw-bold">
                                        <i class="fas fa-arrow-left"></i> QUAY LẠI
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
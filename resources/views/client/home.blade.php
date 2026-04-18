@extends('layouts.client')

@section('content')

<style>
    /* CSS để làm nút trái tim và tag giảm giá đè lên ảnh */
    .book-card {
        position: relative;
        transition: 0.3s;
        border: 1px solid #eee;
    }
    .book-card:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
    }
    .badge-discount {
        position: absolute;
        top: 10px;
        left: 10px;
        background-color: #ff4d4f;
        color: white;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: bold;
        font-size: 12px;
        z-index: 5;
    }
    .btn-wishlist {
        position: absolute;
        top: 10px;
        right: 10px;
        background: white;
        color: #ff4d4f;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        text-decoration: none;
        transition: 0.3s;
        z-index: 5;
        border: none;
    }
    .btn-wishlist:hover {
        background: #ff4d4f;
        color: white;
    }
</style>

<div class="container mt-4">
    {{-- PHẦN THÔNG BÁO (HIỆN TRÊN ĐẦU DANH SÁCH) --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center mb-4" role="alert" style="background-color: #d1e7dd; color: #0f5132;">
            <i class="fas fa-check-circle me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info border-0 shadow-sm rounded-3 d-flex align-items-center mb-4" role="alert" style="background-color: #cff4fc; color: #055160;">
            <i class="fas fa-info-circle me-2"></i>
            <div>{{ session('info') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3 d-flex align-items-center mb-4" role="alert" style="background-color: #f8d7da; color: #842029;">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    {{-- ===== LIST SÁCH ===== --}}
    <div class="row justify-content-center">

    @forelse($books as $book)

        @php
            $gia_goc = $book->price;
            $giam = $book->discount ?? 0;
            $gia_moi = $gia_goc * (100 - $giam) / 100;
        @endphp

        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="bg-white p-3 rounded shadow-sm h-100 book-card">
                
                {{-- TAG GIẢM GIÁ (Nếu có) --}}
                @if($giam > 0)
                    <div class="badge-discount">-{{ $giam }}%</div>
                @endif

                {{-- NÚT YÊU THÍCH --}}
                <a href="{{ route('wishlist.add', ['book_id' => $book->book_id]) }}" class="btn-wishlist">
                    <i class="far fa-heart"></i>
                </a>

                {{-- ẢNH --}}
                <div class="text-center position-relative">
                    <img src="{{ asset($book->link_images ?? 'images/no-image.jpg') }}"
                        class="img-fluid mb-2"
                        style="height:180px; object-fit:contain;">
                </div>

                {{-- TÊN SÁCH --}}
                <h6 class="fw-bold text-dark mt-2"
                    style="height:40px; overflow:hidden; font-size: 15px;">
                    {{ $book->title ?? $book->name }}
                </h6>

                {{-- TÁC GIẢ --}}
                <div class="text-muted small mb-2 text-truncate">
                    {{ $book->author ?? 'Đang cập nhật' }}
                </div>

                {{-- GIÁ --}}
                <div class="mt-2">
                    <div class="text-danger fw-bold fs-5">
                        {{ number_format($gia_moi, 0, ',', '.') }} đ
                    </div>

                    @if($giam > 0)
                        <div class="text-muted small text-decoration-line-through">
                            {{ number_format($gia_goc, 0, ',', '.') }} đ
                        </div>
                    @endif
                </div>

                {{-- FOOTER CARD: SOLD & CHI TIẾT --}}
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        <i class="fas fa-shopping-cart"></i> {{ $book->sold_quantity ?? 0 }}
                    </div>

                    @if(isset($book->loai) && $book->loai == 'set')
                        <a href="{{ url('/book-set/'.$book->book_id) }}" 
                           class="btn btn-sm btn-outline-primary rounded-pill px-3">
                           Chi tiết
                        </a>
                    @else
                        <a href="{{ url('/books/'.$book->book_id) }}" 
                           class="btn btn-sm btn-outline-primary rounded-pill px-3">
                           Chi tiết
                        </a>
                    @endif
                </div>

            </div>
        </div>

    @empty
        <p class="text-center w-100 py-5">Không có sách nào được tìm thấy.</p>
    @endforelse

    </div>

    {{-- ===== PAGINATION ===== --}}
    @if ($books->hasPages())
    <div class="row mt-4 mb-5">
        <div class="col-12">
            <nav>
                <ul class="pagination justify-content-center">
                    {{ $books->links('pagination::bootstrap-5') }}
                </ul>
            </nav>
        </div>
    </div>
    @endif
</div>

@endsection
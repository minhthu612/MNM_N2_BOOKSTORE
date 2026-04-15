@extends('layouts.client')

@section('content')

<h2 class="text-xl font-bold mb-4 text-blue-600">
    {{ $category_name }}
</h2>

<div class="row mt-4">

@forelse($books as $book)

    @php
        $gia_goc = $book->price;
        $giam = $book->discount ?? 0;
        $gia_moi = $gia_goc * (100 - $giam) / 100;
    @endphp

    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-4">
        <div class="bg-white p-3 rounded shadow hover:shadow-lg h-100">

            <!-- ảnh -->
            <img src="{{ $book->link_images ?? 'images/no-image.jpg' }}"
                 class="img-fluid mb-2"
                 style="height:180px; object-fit:contain;">

            <!-- tên -->
            <h6 class="fw-bold" style="height:40px; overflow:hidden;">
                {{ $book->title ?? $book->name }}
            </h6>

            <!-- giá -->
            <div class="mt-2">
                <div class="text-danger fw-bold">
                    {{ number_format($gia_moi) }} đ
                </div>

                @if($giam > 0)
                    <div class="text-muted small text-decoration-line-through">
                        {{ number_format($gia_goc) }} đ
                    </div>
                @endif
            </div>

            <!-- sold -->
            <div class="text-muted small mt-1">
                Đã bán: {{ $book->sold_quantity ?? 0 }}
            </div>

            <!-- button -->
            <div class="mt-2">
                @auth
                    <a href="#" class="btn btn-sm btn-outline-primary">
                        Chi tiết
                    </a>
                @else
                    <a href="{{ route('login', ['msg'=>'require']) }}"
                       class="btn btn-sm btn-outline-primary">
                        Chi tiết
                    </a>
                @endauth
            </div>

        </div>
    </div>

@empty
    <p>Không có sách</p>
@endforelse

</div>

{{-- ✅ PAGINATION (CHỈ 1 CHỖ DUY NHẤT) --}}
@if ($books->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $books->links('pagination::bootstrap-5') }}
    </div>
@endif

@endsection
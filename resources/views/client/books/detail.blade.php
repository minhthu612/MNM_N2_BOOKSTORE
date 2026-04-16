@extends('layouts.client')

@section('content')

<div class="container py-4">

    <nav class="mb-3 text-muted">
        <a href="{{ url('/') }}">Trang chủ</a> /
        <span>{{ $book->title }}</span>
    </nav>

    <div class="row">

        {{-- IMAGE --}}
        <div class="col-md-5">
            <img src="{{ asset($book->link_images ?? 'images/no-image.jpg') }}"
                 class="img-fluid rounded shadow">
        </div>

        {{-- INFO --}}
        <div class="col-md-7">

            <h2 class="fw-bold">{{ $book->title }}</h2>

            <p class="text-muted">Tác giả: {{ $book->author }}</p>

            @php
                $gia_goc = $book->price;
                $giam = $book->discount ?? 0;
                $gia_moi = $gia_goc * (100 - $giam) / 100;
            @endphp

            <h4 class="text-danger fw-bold">
                {{ number_format($gia_moi) }} đ

                @if($giam > 0)
                    <small class="text-muted text-decoration-line-through ms-2">
                        {{ number_format($gia_goc) }} đ
                    </small>
                @endif
            </h4>

            <p class="mt-3">
                {{ $book->description ?? 'Đang cập nhật mô tả...' }}
            </p>

            {{-- BUTTON --}}
            <div class="mt-4">

                <form action="{{ url('/cart/add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="book_id" value="{{ $book->book_id }}">

                    <div class="mb-3">
                        <input type="number" name="quantity" value="1" min="1"
                               class="form-control w-25">
                    </div>

                    <button class="btn btn-primary">
                        Thêm vào giỏ hàng
                    </button>
                </form>

                <a href="{{ url('/') }}" class="btn btn-outline-secondary mt-2">
                    Quay lại
                </a>

            </div>

        </div>

    </div>
</div>

@endsection
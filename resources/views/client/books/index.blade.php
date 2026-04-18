@extends('layouts.client')

@section('content')

<h2 class="text-xl font-bold mb-4 text-blue-600">
    Danh sách sách
</h2>

<div class="row">

@forelse($books as $book)

@php
    $gia_goc = $book->price;
    $giam = $book->discount ?? 0;
    $gia_moi = $gia_goc * (100 - $giam) / 100;
@endphp

<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-4">

    <div class="bg-white p-3 rounded shadow h-100">

        <img src="{{ asset($book->link_images ?? 'images/no-image.jpg') }}"
             class="img-fluid mb-2"
             style="height:180px; object-fit:contain;">

        <h6 class="fw-bold" style="height:40px; overflow:hidden;">
            {{ $book->title }}
        </h6>

        <div class="text-danger fw-bold">
            {{ number_format($gia_moi) }} đ
        </div>

        <div class="mt-2">
            <a href="{{ url('/books/'.$book->book_id) }}"
               class="btn btn-sm btn-outline-primary w-100">
                Chi tiết
            </a>
        </div>

    </div>

</div>

@empty
    <p>Không có sách</p>
@endforelse

</div>

<div class="d-flex justify-content-center mt-4">
    {{ $books->links('pagination::bootstrap-5') }}
</div>

@endsection
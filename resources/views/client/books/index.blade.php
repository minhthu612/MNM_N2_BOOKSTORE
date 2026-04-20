@extends('layouts.client')

@section('content')
<style>
    .book-card { position: relative; transition: 0.3s; }
    .btn-wishlist { 
        position: absolute; top: 10px; right: 10px; background: white; color: #ff4d4f; 
        width: 30px; height: 30px; border-radius: 50%; display: flex; 
        align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
        z-index: 5; text-decoration: none; 
    }
</style>

<h2 class="text-xl font-bold mb-4 text-blue-600">Danh sách sách</h2>

<div class="row">
@forelse($books as $book)
    @php
        $gia_moi = $book->price * (100 - ($book->discount ?? 0)) / 100;
        $da_thich = in_array($book->book_id, $wishlist_ids ?? []);
        
        $extensions = ['webp', 'jpg', 'png', 'jpeg'];
        $imagePath = 'images/no-image.jpg'; 
        foreach ($extensions as $ext) {
            if (file_exists(storage_path("app/public/image/{$book->book_id}.{$ext}"))) {
                $imagePath = "storage/image/{$book->book_id}.{$ext}";
                break;
            }
        }
    @endphp

    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-4">
        <div class="bg-white p-3 rounded shadow h-100 book-card">
            <a href="{{ route('wishlist.toggle', ['book_id' => $book->book_id]) }}" class="btn-wishlist">
                <i class="{{ $da_thich ? 'fas text-danger' : 'far' }} fa-heart"></i>
            </a>

            <img src="{{ asset($imagePath) }}" class="img-fluid mb-2" style="height:180px; object-fit:contain;" onerror="this.src='{{ asset('images/no-image.jpg') }}'">

            <h6 class="fw-bold" style="height:40px; overflow:hidden;">{{ $book->title }}</h6>
            <div class="text-danger fw-bold">{{ number_format($gia_moi) }} đ</div>

            <div class="mt-2">
                <a href="{{ url('/books/'.$book->book_id) }}" class="btn btn-sm btn-outline-primary w-100">Chi tiết</a>
            </div>
        </div>
    </div>
@empty
    <p class="text-center w-100">Không có sách</p>
@endforelse
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $books->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>
@endsection
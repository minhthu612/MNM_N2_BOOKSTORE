@extends('layouts.client')

@section('content')

<style>
.wishlist-btn-fast{
    position:absolute;
    top:10px;
    right:10px;
    z-index:10;
    width:32px;
    height:32px;
    background:rgba(255,255,255,.9);
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#ff4757;
    text-decoration:none;
}
.book-card{
    position:relative;
    transition:transform .3s;
}
.book-card:hover{
    transform:translateY(-5px);
}
.text-truncate-2{
    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;
    overflow:hidden;
}
</style>

<div class="container py-4">

    <div class="border-bottom mb-4 pb-2">
        <h4 class="fw-bold text-primary">
            <i class="fas fa-search me-2"></i>
            Kết quả tìm kiếm:
            <span class="text-dark">{{ $keyword }}</span>
        </h4>
    </div>

    @if($keyword != '')

        @if(count($books) > 0)

            <p class="text-muted small mb-4">
                Tìm thấy <b>{{ $books->total() }}</b> kết quả.
            </p>

            <div class="row">

                @foreach($books as $book)

                    @php
                        $discount_price = $book->price * (100 - $book->discount) / 100;
                        $out_stock = ($book->stock <= 0);
                    @endphp

                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-4">

                        <div class="card book-card h-100 shadow-sm">

                            {{-- wishlist --}}
                            @auth
                                <a href="{{ url('client/wishlist/add?book_id='.$book->book_id) }}"
                                   class="wishlist-btn-fast">
                                    <i class="far fa-heart"></i>
                                </a>
                            @endauth

                            {{-- discount --}}
                            @if($book->discount > 0)
                                <div class="position-absolute top-0 start-0 m-2 bg-danger text-white px-2 rounded small">
                                    -{{ $book->discount }}%
                                </div>
                            @endif

                            <div class="p-2 text-center">
                                <img src="{{ $book->link_images ?: asset('images/no-image.jpg') }}"
                                     style="width:100%;height:180px;object-fit:contain;">
                            </div>

                            <div class="card-body d-flex flex-column">

                                <h6 class="fw-bold text-truncate-2 mb-2">
                                    {{ $book->title }}
                                </h6>

                                <p class="text-muted small mb-2">
                                    {{ $book->author ?: 'Nhiều tác giả' }}
                                </p>

                                <div class="mt-auto mb-2">
                                    <div class="text-danger fw-bold">
                                        {{ number_format($discount_price,0,',','.') }} đ
                                    </div>

                                    @if($book->discount > 0)
                                        <div class="text-muted small text-decoration-line-through">
                                            {{ number_format($book->price,0,',','.') }} đ
                                        </div>
                                    @endif
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-2">

                                    <small class="text-muted">
                                        <i class="fas fa-shopping-cart"></i>
                                        {{ $book->sold_quantity }}
                                    </small>

                                    @auth
                                        <a href="{{ url('/books/'.$book->book_id) }}"
                                           class="btn btn-sm btn-outline-primary rounded-pill">
                                            Chi tiết
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}"
                                           class="btn btn-sm btn-outline-primary rounded-pill">
                                            Chi tiết
                                        </a>
                                    @endauth

                                </div>
                            </div>

                            <div class="card-footer bg-white border-0 pt-0 pb-3">

                                @if($out_stock)
                                    <button class="btn btn-secondary btn-sm w-100 rounded-pill" disabled>
                                        Hết hàng
                                    </button>

                                @elseif(auth()->check())
                                    <form action="{{ url('client/cart/add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="book_id" value="{{ $book->book_id }}">
                                        <button class="btn btn-success btn-sm w-100 rounded-pill">
                                            <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                                        </button>
                                    </form>

                                @else
                                    <a href="{{ route('login') }}"
                                       class="btn btn-outline-secondary btn-sm w-100 rounded-pill">
                                        Đăng nhập để mua
                                    </a>
                                @endif

                            </div>

                        </div>
                    </div>

                @endforeach

            </div>

            <div class="mt-3">
                {{ $books->links() }}
            </div>

        @else
            <div class="text-center py-5">
                <h5 class="text-muted">Không tìm thấy sách phù hợp</h5>
                <a href="{{ route('home') }}" class="btn btn-primary rounded-pill mt-3">
                    Về trang chủ
                </a>
            </div>
        @endif

    @else
        <div class="alert alert-warning text-center">
            Vui lòng nhập từ khóa tìm kiếm
        </div>
    @endif

</div>

@endsection
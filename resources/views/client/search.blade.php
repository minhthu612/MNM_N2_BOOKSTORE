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
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transition: 0.3s;
}
.wishlist-btn-fast:hover {
    background: #ff4757;
    color: white;
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
/* Hiệu ứng rung nhẹ cho icon giỏ hàng khi thêm thành công */
.shake-element {
    animation: shake 0.5s;
}
@keyframes shake {
    0% { transform: rotate(0deg); }
    25% { transform: rotate(15deg); }
    50% { transform: rotate(-15deg); }
    75% { transform: rotate(10deg); }
    100% { transform: rotate(0deg); }
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

        @if($books->count() > 0)

            <p class="text-muted small mb-4">
                Tìm thấy <b>{{ $books->total() }}</b> kết quả.
            </p>

            <div class="row">

                @foreach($books as $book)

                    @php
                        $discount_price = $book->price * (100 - $book->discount) / 100;
                        $out_stock = ($book->stock <= 0);
                        
                        $isFav = in_array($book->book_id, $wishlist_ids ?? []);

                        $extensions = ['webp', 'jpg', 'png', 'jpeg'];
                        $finalImg = 'images/no-image.jpg'; 
                        foreach ($extensions as $ext) {
                            if (file_exists(storage_path("app/public/image/{$book->book_id}.{$ext}"))) {
                                $finalImg = "storage/image/{$book->book_id}.{$ext}";
                                break;
                            }
                        }
                        if($finalImg == 'images/no-image.jpg' && !empty($book->link_images)){
                            $imgSrc = $book->link_images;
                        } else {
                            $imgSrc = asset($finalImg);
                        }
                    @endphp

                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card book-card h-100 shadow-sm border-0">

                            @auth
                                <a href="{{ route('wishlist.toggle', ['book_id' => $book->book_id]) }}"
                                   class="wishlist-btn-fast">
                                    <i class="{{ $isFav ? 'fas text-danger' : 'far' }} fa-heart"></i>
                                </a>
                            @endauth

                            @if($book->discount > 0)
                                <div class="position-absolute top-0 start-0 m-2 bg-danger text-white px-2 rounded small" style="z-index: 5;">
                                    -{{ $book->discount }}%
                                </div>
                            @endif

                            <div class="p-2 text-center mt-2">
                                {{-- Thêm class 'img-fly' để bắt được ảnh cần bay --}}
                                <img src="{{ $imgSrc }}"
                                     class="img-fly"
                                     style="width:100%;height:180px;object-fit:contain;"
                                     onerror="this.src='{{ asset('images/no-image.jpg') }}'">
                            </div>

                            <div class="card-body d-flex flex-column p-3">
                                <h6 class="fw-bold text-truncate-2 mb-2" style="height:2.5rem; font-size: 0.9rem;">
                                    {{ $book->title }}
                                </h6>

                                <p class="text-muted small mb-2 text-truncate">
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
                                        <i class="fas fa-shopping-cart"></i> {{ $book->sold_quantity ?? 0 }}
                                    </small>

                                    <a href="{{ url('/books/'.$book->book_id) }}"
                                       class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        Chi tiết
                                    </a>
                                </div>
                            </div>

                            <div class="card-footer bg-white border-0 pt-0 pb-3 p-3">
                                @if($out_stock)
                                    <button class="btn btn-secondary btn-sm w-100 rounded-pill" disabled>
                                        Hết hàng
                                    </button>
                                @elseif(auth()->check())
                                    <button type="button" 
                                            class="btn btn-success btn-sm w-100 rounded-pill fw-bold nut-them-ajax"
                                            data-id="{{ $book->book_id }}">
                                        <i class="fas fa-cart-plus me-1"></i> THÊM VÀO GIỎ
                                    </button>
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

            <div class="d-flex justify-content-center mt-4">
                {{ $books->links('pagination::bootstrap-5') }}
            </div>

        @else
            <div class="text-center py-5">
                <i class="fas fa-search-minus fa-3x text-muted opacity-25 mb-3"></i>
                <h5 class="text-muted">Không tìm thấy sách phù hợp với "{{ $keyword }}"</h5>
                <a href="{{ url('/') }}" class="btn btn-primary rounded-pill mt-3 px-4">
                    Quay lại trang chủ
                </a>
            </div>
        @endif

    @else
        <div class="alert alert-warning text-center rounded-pill">
            <i class="fas fa-info-circle me-2"></i> Vui lòng nhập từ khóa tìm kiếm
        </div>
    @endif

</div>

@section('scripts')
{{-- Nạp thư viện jQuery và jQuery UI để dùng hiệu ứng animate Expo --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
$(document).ready(function() {
    $('.nut-them-ajax').on('click', function() {
        let bookId = $(this).data('id');
        let btn = $(this);
        let cartIcon = $('.fa-shopping-cart').first(); // Icon giỏ hàng trên menu
        
        // Tìm ảnh trong đúng cái card mà mình vừa bấm nút
        let imgToDrag = btn.closest('.book-card').find('.img-fly');

        $.ajax({
            url: "{{ route('cart.add') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                book_id: bookId,
                quantity: 1
            },
            success: function(response) {
                if(response.status === 'success') {
                    // Cập nhật số lượng Badge trước
                    $('.cart-count-badge').text(response.total_count);

                    // HIỆU ỨNG BAY ẢNH
                    if (imgToDrag.length && cartIcon.length) {
                        let imgClone = imgToDrag.clone()
                            .offset({
                                top: imgToDrag.offset().top,
                                left: imgToDrag.offset().left
                            })
                            .css({
                                'opacity': '0.8',
                                'position': 'absolute',
                                'height': '150px',
                                'width': '110px',
                                'z-index': '9999',
                                'border-radius': '8px',
                                'pointer-events': 'none'
                            })
                            .appendTo($('body'))
                            .animate({
                                'top': cartIcon.offset().top + 10,
                                'left': cartIcon.offset().left + 15,
                                'width': 40,
                                'height': 55
                            }, 1000, 'easeInOutExpo');

                        imgClone.animate({
                            'width': 0,
                            'height': 0
                        }, function() {
                            $(this).detach();
                            
                            // Sau khi bay xong thì rung icon giỏ hàng
                            cartIcon.parent().addClass('shake-element');
                            setTimeout(() => cartIcon.parent().removeClass('shake-element'), 500);
                        });
                    }
                }
            },
            error: function(xhr) {
                if(xhr.status === 401) {
                    window.location.href = "{{ route('login') }}";
                }
            }
        });
    });
});
</script>
@endsection

@endsection
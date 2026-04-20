@extends('layouts.client')

@section('content')
<style>
.khung-chi-tiet { background:#fff;padding:40px;border-radius:15px;border:1px solid #eee }
.anh-sach-lon { max-width:100%;border-radius:10px;box-shadow:0 10px 20px rgba(0,0,0,.1) }
.gia-ban { font-size:2.5rem;color:#e74c3c;font-weight:bold }
.gia-cu { font-size:1.8rem; text-decoration:line-through;color:#95a5a6;margin-left:10px }
.nut-mua { border-radius: 50px !important; padding: 15px !important; font-weight: bold; background-color: #0d6efd; border: none; box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3); text-transform: uppercase; letter-spacing: 1px; }
.nut-mua:hover { background-color: #0b5ed7; transform: translateY(-2px); transition: 0.3s; }
.nut-yeu-thich-tron { width: 55px; height: 55px; border-radius: 50% !important; display: flex; align-items: center; justify-content: center; border: 1px solid #ff4d4f !important; color: #ff4d4f !important; background: white; transition: 0.3s; text-decoration: none; }
.nut-yeu-thich-tron:hover, .nut-yeu-thich-tron.active { background: #ff4d4f !important; color: white !important; }
.thong-tin-phu { color:#7f8c8d;font-size:1rem;margin-bottom:8px }
.o-nhap-sl { width:80px;text-align:center;border-radius:10px; font-weight: bold; }
.sao-vang { color:#f1c40f; font-size: 1.2rem; }
.badge-disc-large { font-size: 1.2rem; padding: 8px 15px; border-radius: 8px; background-color: #ff4d4f; color: white; }
.shake-element { animation: shake 0.5s; }
@keyframes shake { 0% { transform: rotate(0deg); } 25% { transform: rotate(15deg); } 50% { transform: rotate(-15deg); } 75% { transform: rotate(10deg); } 100% { transform: rotate(0deg); } }
</style>

<div class="container py-5">
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center mb-4" style="background-color: #d1e7dd; color: #0f5132;">
            <i class="fas fa-check-circle me-2"></i> <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info border-0 shadow-sm rounded-3 d-flex align-items-center mb-4" style="background-color: #cff4fc; color: #055160;">
            <i class="fas fa-info-circle me-2"></i> <div>{{ session('info') }}</div>
        </div>
    @endif

    <div class="mb-4 text-muted small">
        <a href="{{ url('/') }}" class="text-decoration-none text-muted">Trang chủ</a> /
        <a href="{{ url('/?category='.$book->category_id) }}" class="text-decoration-none text-muted">{{ $book->category_name }}</a> /
        <b class="text-dark">{{ $book->title }}</b>
    </div>

    <div class="khung-chi-tiet shadow-sm">
        <div class="row g-5">
            <div class="col-md-5">
                <div class="text-center bg-light p-4 rounded-4 d-flex align-items-center justify-content-center" style="min-height: 450px;">
                    @php
                        $extensions = ['webp', 'jpg', 'png', 'jpeg'];
                        $imagePath = 'images/no-image.jpg'; 
                        foreach ($extensions as $ext) {
                            if (file_exists(storage_path("app/public/image/{$book->book_id}.{$ext}"))) {
                                $imagePath = "storage/image/{$book->book_id}.{$ext}";
                                break;
                            }
                        }
                    @endphp
                    <img src="{{ asset($imagePath) }}" class="anh-sach-lon" onerror="this.src='{{ asset('images/no-image.jpg') }}'">
                </div>
            </div>

            <div class="col-md-7">
                <div class="mb-3">
                    <span class="badge rounded-pill bg-info text-dark px-3 py-2">{{ $book->category_name }}</span>
                    <span class="badge rounded-pill bg-{{ ($book->stock ?? 0) > 0 ? 'success' : 'danger' }} px-3 py-2">
                        {{ ($book->stock ?? 0) > 0 ? 'Còn hàng ('.$book->stock.')' : 'Hết hàng' }}
                    </span>
                </div>

                <h1 class="fw-bold mb-3" style="font-size: 2.5rem;">{{ $book->title }}</h1>

                <div class="mb-3 d-flex align-items-center">
                    <span class="sao-vang">
                        @for($i=1;$i<=5;$i++)
                            <i class="{{ $i <= floor($avg_rating ?? 0) ? 'fas' : 'far' }} fa-star"></i>
                        @endfor
                    </span>
                    <span class="text-muted ms-3">({{ $total_reviews ?? 0 }} nhận xét)</span>
                </div>

                <div class="mb-4">
                    <div class="thong-tin-phu">Tác giả: <b class="text-dark">{{ $book->author }}</b></div>
                    <div class="thong-tin-phu">Lượt xem: <b class="text-dark">{{ number_format($total_views ?? 0) }}</b></div>
                    <div class="thong-tin-phu">Đã bán: <b class="text-success">{{ $book->sold_quantity ?? 0 }} cuốn</b></div>
                </div>

                <hr class="my-4" style="opacity: 0.1;">

                @php
                    $gia_moi = $book->price * (100 - ($book->discount ?? 0)) / 100;
                    $da_thich = in_array($book->book_id, $wishlist_ids ?? []);
                @endphp

                <div class="mb-4">
                    <div class="d-flex align-items-center">
                        <span class="gia-ban">{{ number_format($gia_moi, 0, ',', '.') }}đ</span>
                        @if($book->discount > 0)
                            <span class="gia-cu">{{ number_format($book->price, 0, ',', '.') }}đ</span>
                            <span class="badge badge-disc-large ms-3">-{{ $book->discount }}%</span>
                        @endif
                    </div>
                </div>

                @if(($book->stock ?? 0) > 0)
                <div id="add-to-cart-form">
                    <input type="hidden" id="book_id" value="{{ $book->book_id }}">
                    <div class="d-flex align-items-center gap-4 mb-4">
                        <label class="fw-bold fs-5">Số lượng:</label>
                        <input type="number" id="quantity" value="1" min="1" max="{{ $book->stock }}" class="form-control o-nhap-sl fs-5 p-2">
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <button type="button" class="btn btn-primary flex-grow-1 nut-mua nut-them-ajax fs-5 shadow">
                            <i class="fas fa-cart-plus me-2"></i> THÊM VÀO GIỎ HÀNG
                        </button>
                        {{-- Nút Wishlist Toggle --}}
                        <a href="{{ route('wishlist.toggle', ['book_id' => $book->book_id]) }}" 
                           class="btn nut-yeu-thich-tron shadow-sm {{ $da_thich ? 'active' : '' }}">
                            <i class="{{ $da_thich ? 'fas' : 'far' }} fa-heart fs-4"></i>
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="row mt-5 pt-5 border-top">
            <div class="col-md-8">
                <h5 class="fw-bold text-primary mb-4 text-uppercase">Giới thiệu sách</h5>
                <div class="text-muted fs-5" style="line-height:1.9">
                    {!! $book->description ? nl2br($book->description) : 'Đang cập nhật nội dung...' !!}
                </div>

                <h5 class="fw-bold text-primary mt-5 mb-4 text-uppercase">Đánh giá từ bạn đọc</h5>
                @forelse($list_reviews as $rev)
                    <div class="bg-light p-4 rounded-4 mb-3 border-0">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <b class="fs-5">{{ $rev->fullname ?: $rev->username }}</b>
                            <span class="text-muted small">Khách hàng đã mua</span>
                        </div>
                        <div class="sao-vang mb-2">
                            @for($i=1;$i<=5;$i++)
                                <i class="{{ $i <= $rev->rating ? 'fas' : 'far' }} fa-star"></i>
                            @endfor
                        </div>
                        <p class="mb-0 text-secondary">"{!! nl2br($rev->comment) !!}"</p>
                    </div>
                @empty
                    <div class="text-center py-4 bg-light rounded-4">
                        <p class="text-muted mb-0">Chưa có nhận xét nào.</p>
                    </div>
                @endforelse
            </div>

            <div class="col-md-4">
                <div class="bg-light p-4 rounded-4 sticky-top" style="top: 100px;">
                    <h6 class="fw-bold mb-3"><i class="fas fa-truck-moving me-2 text-primary"></i>CHÍNH SÁCH</h6>
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Giao hàng toàn quốc</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Đổi trả 7 ngày</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Sách chính bản 100%</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i> Kiểm hàng trước khi nhận</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.nut-them-ajax').on('click', function() {
        let bookId = $('#book_id').val();
        let qty = $('#quantity').val();
        let cartIcon = $('.fa-shopping-cart').first();
        let imgToDrag = $('.anh-sach-lon');

        $.ajax({
            url: "{{ route('cart.add') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                book_id: bookId,
                quantity: qty
            },
            success: function(response) {
                if (imgToDrag.length && cartIcon.length) {
                    let imgClone = imgToDrag.clone().offset({
                        top: imgToDrag.offset().top,
                        left: imgToDrag.offset().left
                    }).css({
                        'opacity': '0.7', 'position': 'absolute', 'height': '200px', 'width': '150px',
                        'z-index': '9999', 'border-radius': '10px', 'pointer-events': 'none'
                    }).appendTo($('body')).animate({
                        'top': cartIcon.offset().top + 10,
                        'left': cartIcon.offset().left + 15,
                        'width': 50, 'height': 70
                    }, 1000);

                    imgClone.animate({ 'width': 0, 'height': 0 }, function() {
                        $(this).detach();
                        $('.cart-count-badge').text(response.total_count);
                        cartIcon.parent().addClass('shake-element');
                        setTimeout(() => cartIcon.parent().removeClass('shake-element'), 500);
                    });
                }
            },
            error: function(xhr) {
                if(xhr.status === 401) {
                    alert('Vui lòng đăng nhập!');
                    window.location.href = "{{ route('login') }}";
                }
            }
        });
    });
});
</script>
@endsection
@endsection
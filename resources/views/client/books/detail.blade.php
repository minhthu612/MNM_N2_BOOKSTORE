@extends('layouts.client')

@section('content')

<style>
.khung-chi-tiet { background:#fff;padding:40px;border-radius:15px;border:1px solid #eee }
.anh-sach-lon { max-width:100%;border-radius:10px;box-shadow:0 10px 20px rgba(0,0,0,.1) }
.gia-ban { font-size:2.5rem;color:#e74c3c;font-weight:bold }
.gia-cu { font-size:1.8rem; text-decoration:line-through;color:#95a5a6;margin-left:10px }
/* Nút thêm giỏ hàng kiểu mới giống hình mẫu */
.nut-mua { 
    border-radius: 50px !important; 
    padding: 15px !important; 
    font-weight: bold; 
    background-color: #0d6efd; 
    border: none;
    box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
    text-transform: uppercase;
    letter-spacing: 1px;
}
.nut-mua:hover { background-color: #0b5ed7; transform: translateY(-2px); transition: 0.3s; }
/* Nút trái tim kiểu mới */
.nut-yeu-thich-tron {
    width: 55px;
    height: 55px;
    border-radius: 50% !important;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #ff4d4f !important;
    color: #ff4d4f !important;
    background: white;
    transition: 0.3s;
}
.nut-yeu-thich-tron:hover {
    background: #ff4d4f !important;
    color: white !important;
}
.thong-tin-phu { color:#7f8c8d;font-size:1rem;margin-bottom:8px }
.o-nhap-sl { width:80px;text-align:center;border-radius:10px; font-weight: bold; }
.sao-vang { color:#f1c40f; font-size: 1.2rem; }
.badge-disc-large {
    font-size: 1.2rem;
    padding: 8px 15px;
    border-radius: 8px;
    background-color: #ff4d4f;
}
</style>

<div class="container py-5">

    {{-- PHẦN THÔNG BÁO (HIỆN TRÊN ĐẦU GIỐNG HÌNH MẪU) --}}
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

    {{-- BREADCRUMB --}}
    <div class="mb-4 text-muted">
        <a href="{{ url('/') }}" class="text-decoration-none text-muted">Trang chủ</a> /
        <a href="{{ url('/?category='.$book->category_id) }}" class="text-decoration-none text-muted">
            {{ $book->category_name }}
        </a> /
        <b class="text-dark">{{ $book->title }}</b>
    </div>

    <div class="khung-chi-tiet shadow-sm">
        <div class="row g-5">

            {{-- IMAGE --}}
            <div class="col-md-5">
                <div class="text-center bg-light p-4 rounded-4 d-flex align-items-center justify-content-center" style="min-height: 450px;">
                    <img src="{{ asset($book->link_images ?? 'images/no-image.jpg') }}"
                         class="anh-sach-lon">
                </div>
            </div>

            {{-- INFO --}}
            <div class="col-md-7">

                {{-- BADGE --}}
                <div class="mb-3">
                    <span class="badge rounded-pill bg-info text-dark px-3 py-2">
                        {{ $book->category_name }}
                    </span>

                    @if(($book->stock ?? 0) > 0)
                        <span class="badge rounded-pill bg-success px-3 py-2">
                            Còn hàng ({{ $book->stock }})
                        </span>
                    @else
                        <span class="badge rounded-pill bg-danger px-3 py-2">
                            Hết hàng
                        </span>
                    @endif
                </div>

                <h1 class="fw-bold mb-3" style="font-size: 3rem;">
                    {{ $book->title }}
                </h1>

                {{-- RATING --}}
                <div class="mb-3 d-flex align-items-center">
                    <span class="sao-vang">
                        @for($i=1;$i<=5;$i++)
                            <i class="{{ $i <= floor($avg_rating ?? 0) ? 'fas' : 'far' }} fa-star"></i>
                        @endfor
                    </span>
                    <span class="text-muted ms-3">
                        ({{ $total_reviews ?? 0 }} nhận xét)
                    </span>
                </div>

                {{-- INFO CHI TIẾT --}}
                <div class="mb-4">
                    <div class="thong-tin-phu">
                        Tác giả: <b class="text-dark">{{ $book->author }}</b>
                    </div>

                    <div class="thong-tin-phu">
                        Lượt xem: <b class="text-dark">{{ number_format($total_views ?? 0) }}</b>
                    </div>

                    <div class="thong-tin-phu">
                        Đã bán:
                        <b class="text-success">
                            {{ $book->sold_quantity ?? 0 }} cuốn
                        </b>
                    </div>
                </div>

                <hr class="my-4" style="opacity: 0.1;">

                {{-- GIÁ --}}
                @php
                    $gia_goc = $book->price;
                    $giam = $book->discount ?? 0;
                    $gia_moi = $gia_goc * (100 - $giam) / 100;
                @endphp

                <div class="mb-4">
                    <div class="d-flex align-items-center">
                        <span class="gia-ban">{{ number_format($gia_moi, 0, ',', '.') }}đ</span>

                        @if($giam > 0)
                            <span class="gia-cu">{{ number_format($gia_goc, 0, ',', '.') }}đ</span>
                            <span class="badge badge-disc-large ms-3">
                                -{{ $giam }}%
                            </span>
                        @endif
                    </div>
                </div>

                {{-- FORM ĐẶT HÀNG --}}
                @if(($book->stock ?? 0) > 0)
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="book_id" value="{{ $book->book_id }}">

                    <div class="d-flex align-items-center gap-4 mb-4">
                        <label class="fw-bold fs-5">Số lượng:</label>

                        <input type="number"
                               name="quantity"
                               value="1"
                               min="1"
                               max="{{ $book->stock }}"
                               class="form-control o-nhap-sl fs-5 p-2">

                        <span class="text-muted">
                            Có sẵn: {{ $book->stock }}
                        </span>
                    </div>

                    <div class="d-flex align-items-center gap-3">

                        <button class="btn btn-primary flex-grow-1 nut-mua fs-5 shadow">
                            <i class="fas fa-cart-plus me-2"></i> THÊM VÀO GIỎ HÀNG
                        </button>

                        @auth
                        <a href="{{ route('wishlist.add', ['book_id' => $book->book_id]) }}"
                           class="btn nut-yeu-thich-tron shadow-sm">
                            <i class="far fa-heart fs-4"></i>
                        </a>
                        @endauth

                    </div>

                </form>
                @endif

            </div>
        </div>

        {{-- PHẦN MÔ TẢ & ĐÁNH GIÁ (GIỮ NGUYÊN) --}}
        <div class="row mt-5 pt-5 border-top">

            <div class="col-md-8">

                <h5 class="fw-bold text-primary mb-4 text-uppercase" style="letter-spacing: 1px;">
                    Giới thiệu sách
                </h5>

                <div class="text-muted fs-5" style="line-height:1.9">
                    {!! $book->description
                        ? nl2br($book->description)
                        : 'Đang cập nhật nội dung cho cuốn sách này...' !!}
                </div>

                <h5 class="fw-bold text-primary mt-5 mb-4 text-uppercase" style="letter-spacing: 1px;">
                    Đánh giá từ bạn đọc
                </h5>

                @forelse($list_reviews as $rev)
                    <div class="bg-light p-4 rounded-4 mb-3 border-0">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <b class="fs-5">
                                {{ $rev->fullname ?: $rev->username }}
                            </b>
                            <span class="text-muted small">Khách hàng đã mua</span>
                        </div>

                        <div class="sao-vang mb-2">
                            @for($i=1;$i<=5;$i++)
                                <i class="{{ $i <= $rev->rating ? 'fas' : 'far' }} fa-star"></i>
                            @endfor
                        </div>

                        <p class="mb-0 text-secondary italic">
                            "{!! nl2br($rev->comment) !!}"
                        </p>
                    </div>
                @empty
                    <div class="text-center py-4 bg-light rounded-4">
                        <i class="far fa-comment-dots fa-3x text-muted mb-3 opacity-25"></i>
                        <p class="text-muted mb-0">Chưa có nhận xét nào cho sản phẩm này.</p>
                    </div>
                @endforelse

            </div>

            {{-- POLICY --}}
            <div class="col-md-4">
                <div class="bg-light p-4 rounded-4 sticky-top" style="top: 100px;">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-truck-moving me-2 text-primary"></i>
                        CHÍNH SÁCH CỦA CHÚNG TÔI
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2 small d-flex align-items-center"><i class="fas fa-check-circle text-success me-2"></i> Giao hàng toàn quốc trong 24h</li>
                        <li class="mb-2 small d-flex align-items-center"><i class="fas fa-check-circle text-success me-2"></i> Đổi trả miễn phí trong 7 ngày</li>
                        <li class="mb-2 small d-flex align-items-center"><i class="fas fa-check-circle text-success me-2"></i> Cam kết sách chính bản 100%</li>
                        <li class="small d-flex align-items-center"><i class="fas fa-check-circle text-success me-2"></i> Kiểm tra hàng trước khi nhận</li>
                    </ul>
                </div>
            </div>

        </div>

    </div>
</div>

@endsection
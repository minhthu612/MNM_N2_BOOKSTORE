@extends('layouts.client')

@section('title', $set->name)

@section('content')
<style>
    .khung-combo { background: #fff; padding: 30px; border-radius: 15px; border: 1px solid #eee; }
    .anh-combo-lon { max-width: 100%; border-radius: 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    .gia-combo { font-size: 2.5rem; color: #d9534f; font-weight: bold; }
    .item-nho { border: 1px solid #f1f1f1; border-radius: 10px; padding: 10px; margin-bottom: 10px; background: #fafafa; transition: 0.3s; }
    .item-nho:hover { background: #fdfdfd; border-color: #667eea; }
    
    .nut-dat-mua { 
        border-radius: 50px !important; 
        padding: 15px 40px !important; 
        font-weight: bold; 
        text-transform: uppercase; 
        letter-spacing: 1px;
        box-shadow: 0 4px 15px rgba(217, 83, 79, 0.3);
    }
    
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
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        text-decoration: none;
    }
    /* Thêm trạng thái Active để nút đỏ hẳn lên khi đã thích */
    .nut-yeu-thich-tron:hover, .nut-yeu-thich-tron.active {
        background: #ff4d4f !important;
        color: white !important;
    }

    .o-sl { width: 80px; border-radius: 10px !important; text-align: center; font-weight: bold; height: 45px; }
    .vung-danh-gia { border-bottom: 1px solid #f5f5f5; padding: 15px 0; }
</style>

<div class="container py-5">
    
    {{-- THÔNG BÁO --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center mb-4" role="alert" style="background-color: #d1e7dd; color: #0f5132;">
            <i class="fas fa-check-circle me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="mb-4">
        <a href="{{ url('/') }}" class="text-decoration-none text-muted">Trang chủ</a> / 
        <span class="text-dark fw-bold">Bộ sách: {{ $set->name }}</span>
    </div>

    <div class="khung-combo shadow-sm">
        <div class="row g-5">
            <div class="col-md-5">
                <div class="text-center p-3 bg-light rounded-3">
                    <img src="{{ asset($set->link_images ?? 'images/no-image.jpg') }}" class="anh-combo-lon" alt="{{ $set->name }}">
                </div>
                
                <div class="mt-4 card border-0 bg-light rounded-3">
                    <div class="card-body">
                        <h6 class="fw-bold text-primary mb-3 text-uppercase">Sách trong bộ này:</h6>
                        @foreach ($list_items as $item)
                            <div class="item-nho d-flex align-items-center gap-3">
                                <img src="{{ asset($item->link_images) }}" width="40" height="55" style="object-fit: cover; border-radius: 5px;">
                                <div class="small fw-bold text-dark">{{ $item->title }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="mb-2">
                    <span class="badge bg-warning text-dark rounded-pill px-3">BỘ SÁCH TIẾT KIỆM</span>
                    @if (($set->stock ?? 0) > 0)
                        <span class="badge bg-success rounded-pill px-3">Sẵn có ({{ $set->stock }})</span>
                    @else
                        <span class="badge bg-danger rounded-pill px-3">Tạm hết hàng</span>
                    @endif
                </div>

                <h1 class="fw-bold mb-3" style="font-size: 2.8rem;">{{ $set->name }}</h1>

                <div class="mb-4 pb-4 border-bottom">
                    <div class="text-muted small">Lượt xem bộ sách: <b>{{ number_format($total_views) }}</b></div>
                    <div class="text-muted small">Mã Combo: <b>#SET-{{ $set->set_id }}</b></div>
                </div>

                <div class="mb-4">
                    @php 
                        $goc = $set->price;
                        $giam = $set->discount ?? 0;
                        $cuoi = $goc - ($goc * $giam / 100);
                        // Kiểm tra xem bộ sách này đã được thích chưa
                        $da_thich = in_array($set->set_id, $wishlist_ids ?? []);
                    @endphp
                    <div class="gia-combo">
                        {{ number_format($cuoi, 0, ',', '.') }}đ
                        @if ($giam > 0)
                            <small class="text-muted text-decoration-line-through fs-4 ms-2">{{ number_format($goc, 0, ',', '.') }}đ</small>
                            <span class="badge bg-danger fs-6 ms-2" style="vertical-align: middle;">-{{ $giam }}%</span>
                        @endif
                    </div>
                    <div class="text-success small fw-bold mt-1">
                        <i class="fas fa-check-circle me-1"></i>Tiết kiệm hơn khi mua theo bộ
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold">Mô tả bộ sản phẩm:</h6>
                    <div class="text-secondary" style="text-align: justify; line-height: 1.8; font-size: 1.05rem;">
                        {!! nl2br(e($set->description)) !!}
                    </div>
                </div>

                @if (($set->stock ?? 0) > 0)
                <form action="{{ route('cart.addSet') }}" method="POST">
                    @csrf
                    <input type="hidden" name="set_id" value="{{ $set->set_id }}">
                    
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <label class="fw-bold text-dark fs-5">Số lượng bộ:</label>
                        <input type="number" name="quantity" class="form-control o-sl" value="1" min="1" max="{{ $set->stock }}">
                    </div>
                    
                    <div class="d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-danger btn-lg nut-dat-mua flex-grow-1 shadow">
                            <i class="fas fa-shopping-basket me-2"></i>MUA TRỌN BỘ NGAY
                        </button>

                        @auth
                        {{-- CẬP NHẬT TRẠNG THÁI NÚT TRÁI TIM --}}
                        <a href="{{ route('wishlist.add', ['book_id' => $set->set_id]) }}"
                           class="btn nut-yeu-thich-tron shadow-sm {{ $da_thich ? 'active' : '' }}">
                            <i class="{{ $da_thich ? 'fas' : 'far' }} fa-heart fs-4"></i>
                        </a>
                        @endauth
                    </div>
                </form>
                @endif
            </div>
        </div>

        <div class="row mt-5 pt-5 border-top">
            <div class="col-md-8">
                <h5 class="fw-bold mb-4 text-primary text-uppercase">
                    <i class="fas fa-star text-warning me-2"></i>Nhận xét từ khách hàng
                </h5>
                
                @forelse ($list_reviews as $rev)
                    <div class="vung-danh-gia">
                        <div class="d-flex justify-content-between mb-2">
                            <b class="text-dark fs-5">{{ $rev->fullname ?: $rev->username }}</b>
                            <small class="text-muted">{{ date('d/m/Y', strtotime($rev->created_at)) }}</small>
                        </div>
                        <div class="text-warning mb-2" style="font-size: 0.9rem;">
                            @for($i=1; $i<=5; $i++)
                                <i class="{{ $i <= $rev->rating ? 'fas' : 'far' }} fa-star"></i>
                            @endfor
                        </div>
                        <p class="text-secondary mb-0">{!! nl2br(e($rev->comment)) !!}</p>
                    </div>
                @empty
                    <div class="alert alert-light border text-center py-4">
                        <p class="mb-0 text-muted">Chưa có đánh giá nào cho combo này.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
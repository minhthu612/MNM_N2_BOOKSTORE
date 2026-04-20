@extends('layouts.client')

@section('content')
<style>
    body { background-color: #f8f9fa; }
    .khung-yeu-thich {
        background: #fff;
        border-radius: 15px;
        border: none;
        transition: 0.3s;
        height: 100%;
    }
    .khung-yeu-thich:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .anh-san-pham {
        height: 180px;
        object-fit: contain;
        padding: 15px;
        background: #fdfdfd;
    }
    .nut-xoa-nhanh {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        z-index: 10;
        transition: 0.3s;
        border: none;
    }
    .nut-xoa-nhanh:hover {
        background: #dc3545;
        color: #fff;
    }
    .tieu-de-sach {
        font-size: 0.9rem;
        font-weight: bold;
        color: #333;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 2.4rem;
    }
</style>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h3 class="fw-bold text-dark m-0">
            <i class="fas fa-heart text-danger me-2"></i>DANH SÁCH YÊU THÍCH
        </h3>
        <span class="badge bg-primary rounded-pill">Đang lưu {{ count($list_fav) }} mục</span>
    </div>

    {{-- PHẦN THÔNG BÁO --}}
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

    <div class="row g-4">
        @if (count($list_fav) > 0)
            @foreach ($list_fav as $item) 
                @php
                    $la_bo_sach = !is_null($item->s_id);
                    
                    if ($la_bo_sach) {
                        $ten_hien_thi = $item->s_title;
                        $ma_id = $item->s_id;
                        $gia_goc = $item->s_price;
                        $phan_tram_giam = $item->s_disc;
                        $url_detail = url('/book-set/'.$ma_id);
                        $fileNameBase = $ma_id . '_' . $ma_id; 
                    } else {
                        $ten_hien_thi = $item->b_title;
                        $ma_id = $item->b_id;
                        $gia_goc = $item->b_price;
                        $phan_tram_giam = $item->b_disc;
                        $url_detail = url('/books/'.$ma_id);
                        $fileNameBase = $ma_id; 
                    }
                    $gia_sau_giam = $gia_goc * (100 - $phan_tram_giam) / 100;

                    // LOGIC TÌM ẢNH TRONG STORAGE
                    $extensions = ['webp', 'jpg', 'png', 'jpeg'];
                    $anh_hien_thi = 'images/no-image.jpg'; 

                    foreach ($extensions as $ext) {
                        if (file_exists(storage_path("app/public/image/{$fileNameBase}.{$ext}"))) {
                            $anh_hien_thi = "storage/image/{$fileNameBase}.{$ext}";
                            break;
                        }
                    }
                @endphp

                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card khung-yeu-thich shadow-sm position-relative overflow-hidden">
                        
                        {{-- Nút xóa nhanh: Dùng route toggle để bấm là bỏ yêu thích luôn --}}
                        <a href="{{ route('wishlist.toggle', ['book_id' => $ma_id]) }}" 
                           class="nut-xoa-nhanh" 
                           onclick="return confirm('Bỏ sản phẩm này khỏi mục yêu thích?')">
                            <i class="fas fa-times"></i>
                        </a>

                        <div class="text-center mt-3">
                            <img src="{{ asset($anh_hien_thi) }}" 
                                 class="anh-san-pham img-fluid"
                                 onerror="this.src='{{ asset('images/no-image.jpg') }}'">
                        </div>

                        <div class="card-body d-flex flex-column p-3">
                            @if ($la_bo_sach)
                                <div class="mb-1"><span class="badge bg-info text-dark" style="font-size: 9px;">TRỌN BỘ</span></div>
                            @endif

                            <div class="tieu-de-sach mb-2">{{ $ten_hien_thi }}</div>
                            
                            <div class="mt-auto">
                                <div class="text-danger fw-bold fs-5 mb-3">
                                    {{ number_format($gia_sau_giam, 0, ',', '.') }}đ
                                </div>
                                <a href="{{ $url_detail }}" class="btn btn-primary btn-sm w-100 rounded-pill fw-bold">
                                    XEM CHI TIẾT
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12 text-center py-5">
                <div class="py-5">
                    <i class="far fa-heart fa-4x mb-3 text-muted opacity-25"></i>
                    <h5 class="text-muted">Bạn chưa yêu thích sản phẩm nào.</h5>
                    <p class="small text-secondary mb-4">Hãy dạo quanh cửa hàng và chọn những cuốn sách ưng ý nhé!</p>
                    <a href="{{ url('/') }}" class="btn btn-outline-primary rounded-pill px-5">KHÁM PHÁ NGAY</a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
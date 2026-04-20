@extends('layouts.client')

@section('content')

<style>
    .book-card { position: relative; transition: 0.3s; border: 1px solid #eee; }
    .book-card:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important; }
    .badge-discount { position: absolute; top: 10px; left: 10px; background-color: #ff4d4f; color: white; padding: 4px 8px; border-radius: 6px; font-weight: bold; font-size: 12px; z-index: 5; }
    .btn-wishlist { position: absolute; top: 10px; right: 10px; background: white; color: #ff4d4f; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-decoration: none; transition: 0.3s; z-index: 5; border: none; }
    .btn-wishlist:hover { background: #ff4d4f; color: white; }
</style>

<div class="container mt-4">
    {{-- PHẦN THÔNG BÁO --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center mb-4" role="alert" style="background-color: #d1e7dd; color: #0f5132;">
            <i class="fas fa-check-circle me-2"></i> <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info border-0 shadow-sm rounded-3 d-flex align-items-center mb-4" role="alert" style="background-color: #cff4fc; color: #055160;">
            <i class="fas fa-info-circle me-2"></i> <div>{{ session('info') }}</div>
        </div>
    @endif

    <div class="row justify-content-center">
    @forelse($books as $book)
        @php
            $gia_goc = $book->price;
            $giam = $book->discount ?? 0;
            $gia_moi = $gia_goc * (100 - $giam) / 100;

            // ==========================================
            // LOGIC XỬ LÝ ẢNH (BOOK VÀ SET)
            // ==========================================
            $extensions = ['webp', 'jpg', 'png', 'jpeg'];
            $imagePath = 'images/no-image.jpg'; // Ảnh mặc định

            // Kiểm tra xem item hiện tại là Sách lẻ hay Bộ sách
            $isSet = (isset($book->loai) && $book->loai == 'set');
            
            // Nếu là Bộ sách (Set): tên file là ID_ID (VD: 1_1.jpg)
            // Nếu là Sách lẻ (Book): tên file là ID (VD: 1.jpg)
            $fileNameBase = $isSet ? ($book->book_id . '_' . $book->book_id) : $book->book_id;

            foreach ($extensions as $ext) {
                // Kiểm tra file thực tế trong storage/app/public/image/
                if (file_exists(storage_path("app/public/image/{$fileNameBase}.{$ext}"))) {
                    $imagePath = "storage/image/{$fileNameBase}.{$ext}";
                    break;
                }
            }

            // Check xem sách này đã được yêu thích chưa để đổi icon
            $da_yeu_thich = in_array($book->book_id, $wishlist_ids ?? []);
        @endphp

        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="bg-white p-3 rounded shadow-sm h-100 book-card d-flex flex-column">
                @if($giam > 0)
                    <div class="badge-discount">-{{ (int)$giam }}%</div>
                @endif

                {{-- NÚT YÊU THÍCH: Đã đổi sang route toggle và xử lý icon fas/far --}}
                <a href="{{ route('wishlist.toggle', ['book_id' => $book->book_id]) }}" class="btn-wishlist">
                    <i class="{{ $da_yeu_thich ? 'fas text-danger' : 'far' }} fa-heart"></i>
                </a>

                <div class="text-center position-relative">
                    <img src="{{ asset($imagePath) }}" 
                         class="img-fluid mb-2" 
                         style="height:180px; object-fit:contain;"
                         alt="{{ $book->title ?? $book->name }}">
                </div>

                <h6 class="fw-bold text-dark mt-2" style="height:40px; overflow:hidden; font-size: 15px;">
                    {{ $book->title ?? $book->name }}
                </h6>

                <div class="text-muted small mb-2 text-truncate">
                    {{ $book->author ?? 'Sách bản quyền' }}
                </div>

                <div class="mt-auto">
                    <div class="text-danger fw-bold fs-5">{{ number_format($gia_moi, 0, ',', '.') }} đ</div>
                    @if($giam > 0)
                        <div class="text-muted small text-decoration-line-through">{{ number_format($gia_goc, 0, ',', '.') }} đ</div>
                    @endif
                </div>

                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        <i class="fas fa-shopping-cart"></i> {{ $book->sold_quantity ?? 0 }}
                    </div>
                    <a href="{{ url(($isSet ? '/book-set/' : '/books/') . $book->book_id) }}" 
                       class="btn btn-sm btn-outline-primary rounded-pill px-3">
                       Chi tiết
                    </a>
                </div>
            </div>
        </div>
    @empty
        <p class="text-center w-100 py-5">Không có sách nào được tìm thấy.</p>
    @endforelse
    </div>

    @if ($books->hasPages())
    <div class="row mt-4 mb-5">
        <div class="col-12">
            <nav>
                {{ $books->appends(request()->query())->links('pagination::bootstrap-5') }}
            </nav>
        </div>
    </div>
    @endif
</div>
@endsection
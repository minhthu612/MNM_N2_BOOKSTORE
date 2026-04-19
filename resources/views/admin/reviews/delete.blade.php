@extends('layouts.app')

@section('title', 'Xác nhận xóa đánh giá')
@section('content')


<style>
    .khung-xoa { background: #fff; padding: 30px; border-radius: 15px; border: 1px solid #eee; }
    .o-nhap { border-radius: 10px !important; padding: 12px; }
    .nut-bam { border-radius: 25px !important; padding: 10px 30px !important; font-weight: bold; }
    .vung-thong-tin { background: #fff5f5; border-left: 5px solid #ff4d4d; padding: 15px; border-radius: 8px; }
    .sao-vang { color: #f1c40f; }
</style>


<div class="container">
    <div class="khung-xoa shadow-sm mx-auto" style="max-width: 700px;">
        <div class="text-center mb-4">
            <h3 class="text-danger fw-bold">
                <i class="fas fa-exclamation-triangle"></i> XÁC NHẬN XÓA
            </h3>
            <p class="text-muted">
                Hành động này sẽ xóa vĩnh viễn dữ liệu và không thể khôi phục.
            </p>
        </div>


        {{-- ERROR --}}
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm">
                <i class="fas fa-times-circle me-2"></i> {{ session('error') }}
            </div>
        @endif


        {{-- THÔNG TIN --}}
        <div class="vung-thong-tin mb-4">
            <div class="mb-2">
                <small class="text-muted text-uppercase small fw-bold">Sách:</small>
                <div class="fw-bold">{{ $review->book_title }}</div>
            </div>


            <div class="mb-2">
                <small class="text-muted text-uppercase small fw-bold">Người đăng:</small>
                <div>
                    {{ $review->fullname != '' ? $review->fullname : $review->username }}
                </div>
            </div>


            <div class="mb-2">
                <small class="text-muted text-uppercase small fw-bold">Đánh giá:</small>
                <div class="text-warning">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $review->rating)
                            <i class="fas fa-star sao-vang"></i>
                        @else
                            <i class="far fa-star sao-vang"></i>
                        @endif
                    @endfor


                    <span class="text-dark ms-1">
                        ({{ $review->rating }} sao)
                    </span>
                </div>
            </div>


            <div class="mb-0">
                <small class="text-muted text-uppercase small fw-bold">Nội dung:</small>
                <div class="small italic text-secondary">
                    "{{ $review->comment }}"
                </div>
            </div>
        </div>


        {{-- FORM --}}
        <form method="POST" action="">
            @csrf


            <div class="mb-4">
                <label class="fw-bold mb-2">
                    Để tiếp tục, vui lòng nhập chữ
                    <span class="text-danger">DELETE</span> vào ô bên dưới:
                </label>


                <input type="text" name="confirm_text"
                       class="form-control o-nhap text-center fs-5 fw-bold"
                       placeholder="Gõ DELETE để xác nhận"
                       required autocomplete="off">
            </div>


            <div class="d-flex justify-content-center gap-2 pt-3 border-top">
                <button type="submit" class="btn btn-danger nut-bam shadow">
                    <i class="fas fa-trash-alt me-2"></i> XÓA NGAY
                </button>


                <a href="{{ route('admin.reviews.index') }}" class="btn btn-light nut-bam border">
                    QUAY LẠI
                </a>
            </div>
        </form>


        {{-- NOTE --}}
        <div class="card border-0 bg-light rounded-3 mt-4">
            <div class="card-body py-2">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Lưu ý: Thầy/Cô thường đánh giá cao việc bạn tạo ra rào cản xác nhận
                    (nhập chữ DELETE) thay vì chỉ bấm nút xóa thông thường để tránh người dùng nhấn nhầm.
                </small>
            </div>
        </div>


    </div>
</div>


@endsection

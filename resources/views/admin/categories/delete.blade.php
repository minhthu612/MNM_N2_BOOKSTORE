@extends('layouts.app')

@section('title', 'Xác nhận xóa danh mục')

@section('content')
<style>
    /* Style đồng bộ phong cách WWW */
    .khung-xoa { 
        background: #fff; 
        border: 1px solid #dee2e6; 
        border-radius: 12px; 
        padding: 30px; 
    }
    .tieu-de-canh-bao { 
        color: #dc3545; 
        font-weight: bold; 
        margin-bottom: 25px; 
        text-transform: uppercase;
    }
    .o-nhap { 
        border-radius: 8px !important; 
    }
    .nut-bam { 
        border-radius: 20px !important; 
        padding: 10px 30px !important; 
        font-weight: bold; 
        transition: all 0.3s;
    }
    .nut-bam:hover {
        transform: translateY(-2px);
    }
    .card-info {
        border: none;
        background-color: #f8f9fa;
        border-radius: 10px;
    }
</style>

<div class="container">
    <div class="khung-xoa shadow-sm mt-3">
        <h3 class="tieu-de-canh-bao text-center">
            <i class="fas fa-exclamation-triangle me-2"></i> CẢNH BÁO XÓA DANH MỤC
        </h3>

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="row">
            {{-- Cột trái: Thông tin tóm tắt --}}
            <div class="col-md-5">
                <div class="card card-info shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold text-dark"><i class="fas fa-file-alt me-2 text-secondary"></i>Thông tin hiện tại:</h6>
                        <hr>
                        <p class="mb-2">Tên loại: <span class="fw-bold text-primary">{{ $category->category_name }}</span></p>
                        <p class="mb-2 text-muted small">Mô tả: 
                            <i>{{ $category->description ?? 'Chưa có mô tả cho danh mục này.' }}</i>
                        </p>
                        <p class="mb-0">Số lượng sách bên trong: 
                            <span class="badge bg-danger px-3 py-2 rounded-pill">{{ $book_count }} quyển</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Cột phải: Form xử lý --}}
            <div class="col-md-7">
                <form method="POST" action="{{ route('admin.categories.destroy', $category->category_id) }}">
                    @csrf
                    
                    @if($book_count > 0)
                        <div class="alert alert-warning border-warning shadow-sm p-4">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="fas fa-tools me-2"></i> BẠN CẦN XỬ LÝ SÁCH TRƯỚC:
                            </h6>
                            <p class="small text-dark">Vì đang có <strong>{{ $book_count }}</strong> cuốn sách thuộc loại này, hãy chọn 1 trong 2 cách sau:</p>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="action" id="r1" value="move" checked>
                                <label class="form-check-label fw-bold text-success" for="r1">
                                    1. Chuyển sách sang loại khác:
                                </label>
                                <select name="new_category_id" class="form-select mt-2 o-nhap shadow-sm">
                                    <option value="">-- Chọn danh mục đích --</option>
                                    @foreach($other_categories as $c)
                                        <option value="{{ $c->category_id }}">
                                            {{ $c->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <hr>

                            <div class="form-check mt-3">
                                <input class="form-check-input" type="radio" name="action" id="r2" value="delete">
                                <label class="form-check-label fw-bold text-danger" for="r2">
                                    2. XÓA LUÔN TẤT CẢ SÁCH TRONG LOẠI NÀY
                                </label>
                                <div class="small text-muted mt-1 ps-4">
                                    (Cẩn thận: Tất cả sách sẽ bị xóa vĩnh viễn khỏi hệ thống)
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info border-0 shadow-sm">
                            <i class="fas fa-info-circle me-2"></i> Danh mục này đang trống, bạn có thể xóa an toàn.
                        </div>
                    @endif

                    <div class="mt-4 text-center">
                        <p class="text-secondary small mb-4">Lưu ý: Hành động xóa danh mục không thể hoàn tác.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <button type="submit" name="confirm" class="btn btn-danger nut-bam shadow">
                                <i class="fas fa-trash-alt me-2"></i> XÁC NHẬN XÓA
                            </button>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary nut-bam">
                                <i class="fas fa-times me-2"></i> QUAY LẠI
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
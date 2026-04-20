@extends('layouts.app')

@section('title', 'Thêm danh mục mới')

@section('content')
<style>
    /* Mang style xịn từ trang WWW sang */
    .khung-trang {
        background-color: #ffffff;
        padding: 30px;
        border-radius: 15px;
        border: 1px solid #e0e0e0;
    }
    .tieu-de {
        color: #2c3e50;
        font-weight: bold;
        margin-bottom: 25px;
        border-left: 5px solid #007bff;
        padding-left: 15px;
    }
    .o-nhap {
        border-radius: 10px !important;
        padding: 12px;
    }
    .nut-hanh-dong {
        border-radius: 25px !important;
        padding: 10px 30px !important;
        font-weight: bold;
        transition: all 0.3s;
    }
    .nut-hanh-dong:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
    }
    .bg-light-custom {
        background-color: #f8f9fa;
        border: 1px solid #eee;
    }
</style>

<div class="container-fluid">
    <div class="khung-trang shadow-sm">
        <h4 class="tieu-de text-uppercase">Tạo danh mục sách mới</h4>

        {{-- Hiển thị thông báo lỗi từ Controller --}}
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf

            <div class="row">
                {{-- Cột bên trái: Nhập liệu chính --}}
                <div class="col-md-7">
                    <div class="mb-4">
                        <label class="fw-bold mb-2 text-dark">Tên danh mục sách <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control o-nhap @error('name') is-invalid @enderror" 
                               name="name" 
                               value="{{ old('name') }}"
                               placeholder="Ví dụ: Sách Kỹ Năng Sống" 
                               required>
                        <small class="text-muted">Gợi ý: Tên danh mục nên ngắn gọn, dễ nhớ.</small>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2 text-dark">Mô tả tóm tắt</label>
                        <textarea class="form-control o-nhap" 
                                  name="description" 
                                  rows="6" 
                                  placeholder="Nhập vài dòng giới thiệu về loại sách này...">{{ old('description') }}</textarea>
                    </div>
                </div>

                {{-- Cột bên phải: Hướng dẫn & Xác nhận --}}
                <div class="col-md-5">
                    <div class="card border-0 bg-light-custom rounded-3">
                        <div class="card-body">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="fas fa-info-circle me-1"></i> QUY ĐỊNH THÊM MỚI
                            </h6>
                            <hr>
                            <ul class="small text-muted" style="line-height: 2;">
                                <li>Không được đặt tên danh mục trùng nhau.</li>
                                <li>Tên danh mục không nên chứa ký tự đặc biệt.</li>
                                <li>Mô tả có thể để trống nếu chưa cần thiết.</li>
                                <li>Sau khi thêm, bạn có thể chỉnh sửa tại danh sách.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-4 p-3 border rounded-3 bg-white shadow-sm">
                        <label class="fw-bold mb-2 small text-uppercase text-secondary">Xác nhận thông tin</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="check1" required>
                            <label class="form-check-label small" for="check1">
                                Tôi đã kiểm tra tính chính xác của tên.
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check2" required>
                            <label class="form-check-label small" for="check2">
                                Danh mục này phù hợp với cửa hàng.
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Nút hành động ở cuối --}}
            <div class="mt-4 pt-3 border-top d-flex gap-2 justify-content-start">
                <button type="submit" class="btn btn-primary nut-hanh-dong shadow-sm">
                    <i class="fas fa-save me-2"></i> LƯU DANH MỤC
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary nut-hanh-dong">
                    HỦY BỎ
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
@extends('layouts.app')


@section('title', 'Chỉnh sửa danh mục')


@section('content')
<style>
    /* Style đồng bộ với bản WWW */
    .khung-sua {
        background-color: #ffffff;
        padding: 30px;
        border-radius: 15px;
        border: 1px solid #e0e0e0;
    }
    .o-nhap {
        border-radius: 10px !important;
        padding: 12px;
    }
    .nut-hanh-dong {
        border-radius: 25px !important;
        padding: 10px 30px !important;
        font-weight: bold;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s;
    }
    .nut-hanh-dong:hover {
        transform: translateY(-2px);
    }
    .bg-light-custom {
        background-color: #f8f9fa;
        border: 1px solid #eee;
    }
</style>


<div class="container-fluid">
    <div class="khung-sua shadow-sm">
        {{-- Header của khung sửa --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-primary fw-bold mb-0">CHỈNH SỬA DANH MỤC</h4>
            <span class="badge rounded-pill bg-dark px-3 py-2">Mã số: #{{ $category->category_id }}</span>
        </div>


        {{-- Hiển thị lỗi nếu có --}}
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            </div>
        @endif


        <form method="POST" action="{{ route('admin.categories.update', $category->category_id) }}">
            @csrf
            {{-- Trong Laravel dùng PUT hoặc POST tùy Route, nếu bạn dùng Route::post thì để nguyên --}}
           
            <div class="row">
                {{-- Cột bên trái: Nhập liệu --}}
                <div class="col-md-8">
                    <div class="mb-4">
                        <label class="fw-bold mb-2 text-dark">Tên danh mục *</label>
                        <input type="text"
                               class="form-control o-nhap @error('category_name') is-invalid @enderror"
                               name="category_name"
                               value="{{ old('category_name', $category->category_name) }}"
                               required>
                        <div class="small text-muted mt-1">Lưu ý: Không nên đặt tên quá dài gây tràn giao diện.</div>
                        @error('category_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="mb-4">
                        <label class="fw-bold mb-2 text-dark">Mô tả chi tiết</label>
                        <textarea class="form-control o-nhap"
                            name="description"
                            rows="6"
                            placeholder="Nhập mô tả về loại sách này...">
                            {{ old('description', $category->description ?? '') }}
                        </textarea>
                    </div>
                </div>


                {{-- Cột bên phải: Thống kê & Mẹo --}}
                <div class="col-md-4">
                    <div class="card border-0 bg-light-custom rounded-3 mb-3 shadow-sm">
                        <div class="card-body text-center py-4">
                            <h6 class="fw-bold text-secondary"><i class="fas fa-book me-1"></i> THỐNG KÊ KHO</h6>
                            <hr>
                            <p class="small mb-1">Số lượng sách hiện có:</p>
                            <h2 class="text-primary fw-bold mb-2">
                                {{ $book_count ?? 0 }}
                                <span style="font-size: 1rem;">cuốn</span>
                            </h2>
                           
                            @if(($book_count ?? 0) > 0)
                                <a href="{{ route('admin.books.index', ['category_id' => $category->category_id]) }}"
                                   class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fas fa-search me-1"></i> Xem danh sách
                                </a>
                            @endif
                        </div>
                    </div>


                    <div class="alert alert-warning border-0 small shadow-sm">
                        <i class="fas fa-lightbulb me-1"></i>
                        <strong>Mẹo:</strong> Khi bạn thay đổi tên, các liên kết sách liên quan vẫn giữ nguyên nhưng sẽ hiển thị theo tên mới này.
                    </div>
                </div>
            </div>


            {{-- Footer hành động --}}
            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary nut-hanh-dong shadow">
                        <i class="fas fa-save me-2"></i> LƯU THAY ĐỔI
                    </button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary nut-hanh-dong">
                        HỦY BỎ
                    </a>
                </div>
               
                {{-- Nút xóa nhanh --}}
                <a href="{{ route('admin.categories.delete', $category->category_id) }}"
                   class="btn btn-danger nut-hanh-dong shadow-sm">
                    <i class="fas fa-trash-alt me-1"></i> XÓA DANH MỤC
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

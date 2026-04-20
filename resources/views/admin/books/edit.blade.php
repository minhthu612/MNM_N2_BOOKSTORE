@extends('layouts.app')

@section('title', 'Sửa sách: ' . $book->title)

@section('content')

@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-header bg-white border-bottom">
        <h4 class="mb-0">CHỈNH SỬA THÔNG TIN SÁCH</h4>
    </div>

    <div class="card-body">
        <form method="POST" enctype="multipart/form-data" action="{{ route('admin.books.update', $book->book_id) }}">
            @csrf

            <div class="row">
                <div class="col-md-8">

                    <div class="mb-3">
                        <label class="form-label">Tên sách *</label>
                        <input type="text" class="form-control" name="title" value="{{ $book->title }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="description" rows="5">{{ $book->description }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Tác giả *</label>
                            <input type="text" class="form-control" name="author" value="{{ $book->author }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Danh mục *</label>
                            <select class="form-select" name="category_id" required>

                                @foreach ($categories as $cat)
                                    @php
                                        $selected = ($cat->category_id == $book->category_id) ? 'selected' : '';
                                    @endphp

                                    <option value="{{ $cat->category_id }}" {{ $selected }}>
                                        {{ $cat->category_name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Link ảnh ngoài (URL)</label>
                        <input type="url" class="form-control" name="link_images" value="{{ $book->link_images }}">
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="p-3 bg-light rounded shadow-sm">

                        <div class="row">
                            <div class="col-8">
                                <label class="form-label">Giá bán *</label>
                                <input type="number" class="form-control" name="price" value="{{ $book->price }}" required>
                            </div>

                            <div class="col-4">
                                <label class="form-label">Giảm %</label>
                                <input type="number" class="form-control" name="discount" value="{{ $book->discount }}">
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Số lượng kho *</label>
                            <input type="number" class="form-control" name="stock" value="{{ isset($book->stock) ? $book->stock : 0 }}" required>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Thay đổi ảnh bìa</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <small class="text-muted">Bỏ qua nếu muốn giữ ảnh cũ</small>
                        </div>

                        <div class="mt-3 text-center">
                            @php
                                $img_display = '';
                                $extensions = ['webp', 'jpg', 'png', 'jpeg'];
                                
                                // Tìm ảnh trong storage trước
                                foreach ($extensions as $ext) {
                                    if (file_exists(storage_path("app/public/image/{$book->book_id}.{$ext}"))) {
                                        $img_display = asset("storage/image/{$book->book_id}.{$ext}");
                                        break;
                                    }
                                }

                                // Nếu không có trong storage thì dùng link_images
                                if ($img_display == '' && !empty($book->link_images)) {
                                    $img_display = $book->link_images;
                                }
                            @endphp

                            @if ($img_display != '')
                                <label class="d-block mb-2 small">Ảnh hiện tại:</label>
                                <img src="{{ $img_display }}" style="max-height: 200px; max-width: 100%; border: 1px solid #ddd;" onerror="this.src='https://via.placeholder.com/150x200?text=No+Image'">
                            @endif
                        </div>

                    </div>
                </div>
            </div>

            <div class="border-top pt-4 mt-4 d-flex justify-content-between">

                <div>
                    <button type="submit" class="btn btn-primary px-4">
                        LƯU THAY ĐỔI
                    </button>

                    <a href="{{ route('admin.books.index') }}" class="btn btn-secondary px-4">
                        HỦY
                    </a>
                </div>

                <a href="{{ route('admin.books.delete', $book->book_id) }}"
                   class="btn btn-outline-danger"
                   onclick="return confirm('Bạn chắc chắn muốn xóa sách này?')">
                    XÓA SÁCH
                </a>

            </div>

        </form>
    </div>
</div>

@endsection
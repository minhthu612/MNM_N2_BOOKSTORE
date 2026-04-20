@extends('layouts.app')

@section('title', 'Thêm sách mới')

@section('content')


<div class="card">
    <div class="card-header bg-white border-bottom">
        <h4 class="mb-0">THÊM SÁCH MỚI VÀO HỆ THỐNG</h4>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data" action="{{ route('admin.books.store') }}">
            @csrf

            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên sách *</label>
                        <input type="text" class="form-control" name="title" placeholder="Nhập tên sách..." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả nội dung</label>
                        <textarea class="form-control" name="description" rows="5" placeholder="Viết mô tả ngắn gọn về cuốn sách..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tác giả *</label>
                                <input type="text" class="form-control" name="author" placeholder="Tên tác giả" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Danh mục *</label>
                                <select class="form-select" name="category_id" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->category_id }}">
                                            {{ $cat->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Link ảnh ngoài (Nếu không upload file)</label>
                        <input type="url" class="form-control" name="link_images" placeholder="https://example.com/image.jpg">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 bg-light rounded border">
                        <div class="row">
                            <div class="col-8">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Giá bán *</label>
                                    <input type="number" class="form-control" name="price" min="0" placeholder="0" required>
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Giảm %</label>
                                    <input type="number" class="form-control" name="discount" min="0" max="100" value="0">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Số lượng nhập kho *</label>
                            <input type="number" class="form-control" name="stock" min="0" placeholder="Số lượng" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tải ảnh lên (Storage)</label>
                            <input type="file" class="form-control" name="image" accept="image/*" onchange="previewImage(this)">
                        </div>

                        <div class="mt-3 text-center">
                            <label class="d-block mb-2 small text-muted">Xem trước ảnh:</label>
                            <img id="img-preview" src="{{ asset('images/no-image.jpg') }}" 
                                 style="max-height: 200px; max-width: 100%; border: 1px solid #ddd; border-radius: 5px; object-fit: contain;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-top pt-4 mt-4 text-end">
                <a href="{{ route('admin.books.index') }}" class="btn btn-secondary px-4 me-2">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary px-5 fw-bold">LƯU SÁCH MỚI</button>
            </div>

        </form>
    </div>
</div>

<script>
    // Hàm xem trước ảnh khi chọn file
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('img-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

@endsection
@extends('layouts.app')

@section('title', 'Sửa bộ sách')

@section('content')

<style>
    .khung-anh {
        border: 1px solid #ddd;
        padding: 5px;
        background: #f9f9f9;
        max-height: 200px;
        object-fit: contain;
    }
    .bang-cuon {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #eee;
    }
    .the-label {
        font-weight: bold;
        margin-bottom: 5px;
        display: block;
    }
</style>

<div class="container-fluid">
    <div class="card shadow-sm">

        <div class="card-header bg-primary text-white d-flex justify-content-between">
            <h5 class="mb-0">CHỈNH SỬA THÔNG TIN BỘ SÁCH</h5>
            <a href="{{ route('admin.book_sets.index') }}" class="btn btn-sm btn-light">
                Quay lại
            </a>
        </div>

        <div class="card-body">
            <form method="POST"
                  action="{{ route('admin.book_sets.update', $book_set->set_id) }}"
                  enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="the-label">Tên bộ sách *</label>
                            <input type="text"
                                   class="form-control"
                                   name="name"
                                   value="{{ $book_set->name }}"
                                   required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="the-label">Giá Combo (VNĐ)</label>
                                <input type="number"
                                       class="form-control"
                                       name="price"
                                       value="{{ $book_set->price }}">
                            </div>

                            <div class="col-md-6">
                                <label class="the-label">Giảm giá (%)</label>
                                <input type="number"
                                       class="form-control"
                                       name="discount"
                                       value="{{ $book_set->discount }}">
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="the-label">Mô tả</label>
                            <textarea class="form-control"
                                      name="description"
                                      rows="4">{{ $book_set->description }}</textarea>
                        </div>

                        <div class="mt-3">
                            <label class="the-label">Link ảnh ngoài</label>
                            <input type="text"
                                   class="form-control"
                                   name="link_images"
                                   value="{{ $book_set->link_images }}">
                        </div>
                    </div>

                    <div class="col-md-4 text-center">
                        <label class="the-label">Ảnh bìa hiện tại</label>

                        @php
                            // Logic tìm ảnh bộ sách trong Storage (ID_ID)
                            $extensions = ['webp', 'jpg', 'png', 'jpeg'];
                            $anh_hien_tai = 'https://via.placeholder.com/150x200';
                            $fileNameSet = $book_set->set_id . '_' . $book_set->set_id;

                            foreach ($extensions as $ext) {
                                if (file_exists(storage_path("app/public/image/{$fileNameSet}.{$ext}"))) {
                                    $anh_hien_tai = asset("storage/image/{$fileNameSet}.{$ext}");
                                    break;
                                }
                            }

                            // Nếu storage không có thì mới dùng link_images
                            if ($anh_hien_tai == 'https://via.placeholder.com/150x200' && !empty($book_set->link_images)) {
                                $anh_hien_tai = $book_set->link_images;
                            }
                        @endphp

                        <img src="{{ $anh_hien_tai }}" class="khung-anh mb-2" id="preview-img" onerror="this.src='https://via.placeholder.com/150x200'">

                        <input type="file"
                               class="form-control mt-2"
                               name="image_file">

                        <small class="text-muted">
                            Chọn file nếu muốn thay đổi ảnh
                        </small>
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="text-danger">
                    1. DANH SÁCH SÁCH TRONG BỘ (Tích chọn để Xóa)
                </h5>

                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="50" class="text-center">Xóa</th>
                            <th width="70" class="text-center">Ảnh</th>
                            <th class="text-center">Tên sách</th>
                            <th width="150" class="text-center">Số lượng</th>
                        </tr>
                    </thead>

                    <tbody>
                        @if(count($items_in_set) > 0)
                            @foreach($items_in_set as $item)
                                @php
                                    // Logic tìm ảnh sách lẻ trong Storage (ID)
                                    $anh_item = 'https://via.placeholder.com/40x55';
                                    foreach ($extensions as $ext) {
                                        if (file_exists(storage_path("app/public/image/{$item->book_id}.{$ext}"))) {
                                            $anh_item = asset("storage/image/{$item->book_id}.{$ext}");
                                            break;
                                        }
                                    }
                                    if ($anh_item == 'https://via.placeholder.com/40x55' && !empty($item->link_images)) {
                                        $anh_item = $item->link_images;
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox"
                                               name="remove_items[]"
                                               value="{{ $item->book_id }}">
                                    </td>

                                    <td class="text-center">
                                        <img src="{{ $anh_item }}" width="40" height="55" style="object-fit: cover;" onerror="this.src='https://via.placeholder.com/40x55'">
                                    </td>

                                    <td>
                                        <strong>{{ $item->title }}</strong><br>
                                        <small>{{ number_format($item->price) }}đ</small>
                                    </td>

                                    <td>
                                        <input type="number"
                                               name="quantity_{{ $item->book_id }}"
                                               class="form-control form-control-sm"
                                               value="{{ $item->quantity }}"
                                               min="1">
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    Bộ sách đang trống.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <h5 class="text-success mt-4">
                    2. THÊM SÁCH KHÁC VÀO BỘ
                </h5>

                <div class="bang-cuon">
                    <table class="table table-sm table-hover align-middle"> 
                        <thead class="table-success">
                            <tr>
                                <th width="50" class="text-center">Chọn</th>
                                <th width="70" class="text-center">Ảnh</th>
                                <th class="text-center">Tên sách</th>
                                <th width="100" class="text-center">Số lượng</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($books_to_add as $b)
                                @php
                                    // Logic tìm ảnh cho danh sách thêm mới
                                    $anh_add = 'https://via.placeholder.com/40x55';
                                    foreach ($extensions as $ext) {
                                        if (file_exists(storage_path("app/public/image/{$b->book_id}.{$ext}"))) {
                                            $anh_add = asset("storage/image/{$b->book_id}.{$ext}");
                                            break;
                                        }
                                    }
                                    if ($anh_add == 'https://via.placeholder.com/40x55' && !empty($b->link_images)) {
                                        $anh_add = $b->link_images;
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox"
                                               name="new_book_ids[]"
                                               value="{{ $b->book_id }}">
                                    </td>

                                    <td class="text-center">
                                        <img src="{{ $anh_add }}" width="40" height="55" style="object-fit: cover;" onerror="this.src='https://via.placeholder.com/40x55'">
                                    </td>

                                    <td>
                                        {{ $b->title }} -
                                        <small>{{ number_format($b->price) }}đ</small>
                                    </td>

                                    <td>
                                        <input type="number"
                                               name="new_quantity_{{ $b->book_id }}"
                                               class="form-control form-control-sm"
                                               value="1"
                                               min="1">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit"
                            class="btn btn-primary btn-lg px-5">
                        LƯU TẤT CẢ THAY ĐỔI
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
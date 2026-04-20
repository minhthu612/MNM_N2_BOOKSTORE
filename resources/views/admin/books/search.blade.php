@extends('layouts.app')

@section('title', 'Tìm kiếm nâng cao')

@section('content')

<style>
    .khung-tim-kiem {
        background-color: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 30px;
    }
    .form-control, .form-select {
        border-radius: 8px;
    }
    .anh-sach-nho {
        border: 1px solid #ddd;
        border-radius: 4px;
        object-fit: cover;
    }
    .nut-hanh-dong {
        border-radius: 20px !important;
        padding: 5px 15px !important;
        margin: 0 3px;
        font-size: 0.85rem;
        display: inline-block;
        text-decoration: none;
    }
    .nut-tim-ngay {
        border-radius: 20px !important;
        padding: 10px 30px !important;
        font-weight: bold;
    }
</style>

<div class="container-fluid">

    <div class="khung-tim-kiem shadow-sm">
        <h5 class="mb-4 text-primary fw-bold">
            <i class="fas fa-search-plus"></i> CÔNG CỤ TÌM KIẾM NÂNG CAO
        </h5>

        <form method="GET" action="{{ route('admin.books.search') }}">
            <div class="row g-3">

                <div class="col-md-4">
                    <label class="fw-bold mb-1 small">Từ khóa cần tìm</label>
                    <input type="text" class="form-control"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Tên sách, tác giả...">
                </div>

                <div class="col-md-2">
                    <label class="fw-bold mb-1 small">Danh mục</label>
                    <select class="form-select" name="category_id">
                        <option value="">-- Tất cả --</option>
                        @foreach ($categories_data as $cat)
                            <option value="{{ $cat->category_id }}"
                                @if($cat->category_id == $category_id) selected @endif>
                                {{ $cat->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="fw-bold mb-1 small">Tình trạng kho</label>
                    <select class="form-select" name="status">
                        <option value="">-- Tất cả --</option>
                        <option value="active" @if($status == 'active') selected @endif> Còn hàng </option>
                        <option value="out_of_stock" @if($status == 'out_of_stock') selected @endif> Hết hàng </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="fw-bold mb-1 small">Giá thấp nhất</label>
                    <input type="number" class="form-control"
                           name="min_price"
                           value="{{ $min_price }}"
                           placeholder="0">
                </div>

                <div class="col-md-2">
                    <label class="fw-bold mb-1 small">Giá cao nhất</label>
                    <input type="number" class="form-control"
                           name="max_price"
                           value="{{ $max_price }}"
                           placeholder="999.000">
                </div>

                <div class="col-12 text-end mt-4">
                    <a href="{{ route('admin.books.index') }}"
                       class="btn btn-outline-secondary rounded-pill px-4 me-2">
                        Quay lại
                    </a>

                    <button type="submit"
                            class="btn btn-primary nut-tim-ngay shadow">
                        <i class="fas fa-filter"></i> LỌC KẾT QUẢ
                    </button>
                </div>

            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">
                KẾT QUẢ TÌM THẤY ({{ $total_results }})
            </h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-hover align-middle border">
                    <thead class="table-light">
                        <tr>
                            <th width="60">ID</th>
                            <th>Thông tin sách</th>
                            <th>Danh mục</th>
                            <th>Giá niêm yết</th>
                            <th>Tồn kho</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>

                        @if ($total_results > 0)

                            @foreach ($books_list as $b)
                                <tr>
                                    <td>{{ $b->book_id }}</td>

                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                // LOGIC TÌM ẢNH THÔNG MINH TRONG STORAGE
                                                $extensions = ['webp', 'jpg', 'png', 'jpeg'];
                                                $anh = 'https://via.placeholder.com/45x60?text=Sách';

                                                foreach ($extensions as $ext) {
                                                    if (file_exists(storage_path("app/public/image/{$b->book_id}.{$ext}"))) {
                                                        $anh = asset("storage/image/{$b->book_id}.{$ext}");
                                                        break;
                                                    }
                                                }

                                                // Nếu không có trong storage mới dùng link_images
                                                if ($anh == 'https://via.placeholder.com/45x60?text=Sách' && !empty($b->link_images)) {
                                                    $anh = $b->link_images;
                                                }
                                            @endphp

                                            <img src="{{ $anh }}"
                                                 class="anh-sach-nho me-3"
                                                 width="45" height="60"
                                                 onerror="this.src='https://via.placeholder.com/45x60?text=Sách'">

                                            <div>
                                                <div class="fw-bold">{{ $b->title }}</div>
                                                <small class="text-muted">
                                                    Tác giả: {{ $b->author }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $b->category_name }}
                                        </span>
                                    </td>

                                    <td>
                                        <strong class="text-primary">
                                            {{ number_format($b->price) }} đ
                                        </strong>
                                    </td>

                                    <td>
                                        @php $ton = (int)$b->stock; @endphp

                                        @if ($ton > 0)
                                            <span class="text-success fw-bold small">
                                                ● Còn {{ $ton }} cuốn
                                            </span>
                                        @else
                                            <span class="text-danger fw-bold small">
                                                ○ Hết hàng
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('admin.books.edit', $b->book_id) }}"
                                           class="btn btn-info text-white nut-hanh-dong">
                                            Sửa
                                        </a>

                                        <a href="{{ route('admin.books.delete', $b->book_id) }}"
                                           class="btn btn-danger nut-hanh-dong"
                                           onclick="return confirm('Xác nhận xóa?')">
                                            Xóa
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                        @else

                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-search-minus fa-3x mb-3"></i>
                                        <p>Rất tiếc, không tìm thấy kết quả nào khớp với yêu cầu.</p>
                                        <a href="{{ route('admin.books.search') }}" class="btn btn-sm btn-link">
                                            Tải lại trang
                                        </a>
                                    </div>
                                </td>
                            </tr>

                        @endif

                    </tbody>
                </table>

            </div>
        </div>
    </div>

</div>

@endsection
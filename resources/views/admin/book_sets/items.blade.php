@extends('layouts.app')

@section('title', 'Quản lý sách trong bộ')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">
                <i class="fas fa-list"></i>
                SÁCH TRONG BỘ: {{ $book_set->name }}
            </h5>
            <small class="text-muted">
                Tổng: {{ $total_books }} sách •
                Giá gốc: {{ number_format($total_price) }} đ •
                Giá bán:
                {{ number_format($total_price * (100 - $book_set->discount) / 100) }} đ

                @if($book_set->discount > 0)
                <span class="badge bg-success">-{{ $book_set->discount }}%</span>
                @endif
            </small>
        </div>

        <div>
            <a href="{{ route('admin.book_sets.edit', $book_set->set_id) }}" class="btn btn-outline-primary">
                <i class="fas fa-edit"></i> Sửa bộ sách
            </a>

            <a href="{{ route('admin.book_sets.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="card-body">

        <div class="mb-5">
            <h6 class="border-bottom pb-2 mb-3">
                <i class="fas fa-book"></i> SÁCH HIỆN CÓ TRONG BỘ
            </h6>

            @if(count($items) > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="50">STT</th>
                            <th width="80">Ảnh</th>
                            <th>Tên sách</th>
                            <th width="120">Tác giả</th>
                            <th width="120">Giá</th>
                            <th width="100">Số lượng</th>
                            <th width="100">Tồn kho</th>
                            <th width="120">Thành tiền</th>
                            <th width="80">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $index => $item)
                        @php
                            // LOGIC TÌM ẢNH THÔNG MINH CHO SÁCH LẺ
                            $extensions = ['webp', 'jpg', 'png', 'jpeg'];
                            $anh_item = 'https://via.placeholder.com/50x60?text=No+Image';

                            foreach ($extensions as $ext) {
                                if (file_exists(storage_path("app/public/image/{$item->book_id}.{$ext}"))) {
                                    $anh_item = asset("storage/image/{$item->book_id}.{$ext}");
                                    break;
                                }
                            }

                            if ($anh_item == 'https://via.placeholder.com/50x60?text=No+Image' && !empty($item->link_images)) {
                                $anh_item = $item->link_images;
                            }
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>

                            <td>
                                <img src="{{ $anh_item }}"
                                     width="50" height="60"
                                     onerror="this.src='https://via.placeholder.com/50x60?text=No+Image'"
                                     style="object-fit: cover; border-radius: 4px; border: 1px solid #eee;">
                            </td>

                            <td>{{ $item->title }}</td>
                            <td>{{ $item->author }}</td>

                            <td>{{ number_format($item->price) }} đ</td>

                            <td>{{ $item->quantity }}</td>

                            <td>
                                @php
                                    $stock_display = isset($item->stock) ? $item->stock : 0;
                                @endphp
                                <span class="badge bg-{{ $stock_display > 0 ? 'success' : 'danger' }}">
                                    {{ $stock_display }}
                                </span>
                            </td>

                            <td class="fw-bold">
                                {{ number_format($item->price * $item->quantity) }} đ
                            </td>

                            <td>
                                <form method="POST" action="{{ route('admin.book_sets.items.action', $book_set->set_id) }}">
                                    @csrf
                                    <input type="hidden" name="remove_item" value="{{ $item->book_id }}">
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Xóa sách này khỏi bộ?')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach

                        <tr class="table-light">
                            <td colspan="7" class="text-end fw-bold">Tổng cộng:</td>
                            <td colspan="2" class="fw-bold text-primary">
                                {{ number_format($total_price) }} đ
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4 text-muted">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <h5>Chưa có sách nào trong bộ</h5>
                <p>Hãy thêm sách vào bộ từ danh sách bên dưới</p>
            </div>
            @endif
        </div>

        <div class="mt-5">
            <h6 class="border-bottom pb-2 mb-3">
                <i class="fas fa-plus-circle"></i> THÊM SÁCH MỚI VÀO BỘ
            </h6>

            @if(count($all_books) > 0)
            <form method="POST" action="{{ route('admin.book_sets.items.action', $book_set->set_id) }}">
                @csrf

                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th width="50">Chọn</th>
                                <th width="80">Ảnh</th>
                                <th>Tên sách</th>
                                <th width="120">Tác giả</th>
                                <th width="120">Giá</th>
                                <th width="100">Số lượng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($all_books as $book)
                            @php
                                // LOGIC TÌM ẢNH CHO DANH SÁCH THÊM MỚI
                                $anh_add = 'https://via.placeholder.com/40x50?text=No+Image';
                                foreach ($extensions as $ext) {
                                    if (file_exists(storage_path("app/public/image/{$book->book_id}.{$ext}"))) {
                                        $anh_add = asset("storage/image/{$book->book_id}.{$ext}");
                                        break;
                                    }
                                }
                                if ($anh_add == 'https://via.placeholder.com/40x50?text=No+Image' && !empty($book->link_images)) {
                                    $anh_add = $book->link_images;
                                }
                            @endphp
                            <tr>
                                <td>
                                    <input type="checkbox" name="book_ids[]" value="{{ $book->book_id }}">
                                </td>

                                <td>
                                    <img src="{{ $anh_add }}"
                                         width="40" height="50"
                                         onerror="this.src='https://via.placeholder.com/40x50?text=No+Image'"
                                         style="object-fit: cover; border-radius: 4px;">
                                </td>

                                <td>{{ $book->title }}</td>
                                <td>{{ $book->author }}</td>

                                <td>{{ number_format($book->price) }} đ</td>

                                <td>
                                    <input type="number"
                                           name="quantity_{{ $book->book_id }}"
                                           class="form-control form-control-sm"
                                           value="1" min="1"
                                           style="width: 70px;">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button type="submit" name="add_books" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Thêm sách đã chọn vào bộ
                    </button>

                    <button type="button" class="btn btn-outline-secondary" onclick="selectAllBooks()">
                        <i class="fas fa-check-square"></i> Chọn tất cả
                    </button>
                </div>
            </form>
            @else
            <div class="text-center py-3 text-muted">
                <i class="fas fa-check-circle fa-2x mb-2"></i>
                <p>Tất cả sách đã có trong bộ hoặc không có sách nào để thêm</p>
                <a href="{{ route('admin.books.create') }}" class="btn btn-sm btn-outline-primary">
                    Thêm sách mới
                </a>
            </div>
            @endif
        </div>

    </div>
</div>

<script>
function selectAllBooks() {
    var checkboxes = document.querySelectorAll('input[name="book_ids[]"]');
    var allChecked = true;

    checkboxes.forEach(function(checkbox) {
        if (!checkbox.checked) allChecked = false;
    });

    checkboxes.forEach(function(checkbox) {
        checkbox.checked = !allChecked;
    });
}
</script>

@endsection
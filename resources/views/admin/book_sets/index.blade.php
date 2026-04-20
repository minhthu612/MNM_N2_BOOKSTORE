@extends('layouts.app')

@section('title', 'Quản lý bộ sách')

@section('content')

<style>
    /* CSS Giữ nguyên của bạn và thêm tinh chỉnh */
    .anh-bo-sach {
        width: 50px;
        height: 70px;
        object-fit: cover;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .gia-goc {
        text-decoration: line-through;
        color: #888;
        font-size: 0.9em;
    }
    .gia-moi {
        color: #d9534f;
        font-weight: bold;
    }
    
    /* Căn giữa tiêu đề bảng và chỉnh màu nền nhẹ */
    .bang-du-lieu thead th {
        background-color: #f8f9fa;
        text-align: center;
        vertical-align: middle;
    }

    .nut-hanh-dong {
        border-radius: 20px !important;
        padding: 5px 15px !important;
        margin: 0 3px;
        font-size: 0.85rem;
        transition: all 0.3s;
        display: inline-block;
        text-decoration: none;
    }
    .nut-hanh-dong:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    /* Bo góc card và đổ bóng cho giống Header tổng */
    .card-custom {
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }
</style>

<div class="card card-custom shadow-sm">
    {{-- Đã sửa: bg-white để hết màu xám, py-3 để rộng rãi hơn --}}
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 text-primary fw-bold">
                DANH SÁCH BỘ SÁCH (COMBO)
            </h5>
            <p class="mb-0 small text-muted">
                Tìm thấy {{ $total_sets }} bộ sách
            </p>
        </div>

        <a href="{{ route('admin.book_sets.create') }}" class="btn btn-success rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus"></i> Thêm bộ mới
        </a>
    </div>

    <div class="card-body">

        <form method="GET" action="" class="row g-2 mb-4">
            <div class="col-md-4">
                <input type="text"
                       class="form-control rounded-pill"
                       name="search"
                       placeholder="Nhập tên hoặc ID để tìm..."
                       value="{{ $search }}">
            </div>

            <div class="col-md-4">
                <select class="form-select rounded-pill" name="status">
                    <option value="">-- Trạng thái bộ sách --</option>
                    <option value="active" {{ $status == 'active' ? 'selected' : '' }}>
                        Bộ có sách (Sẵn sàng)
                    </option>
                    <option value="empty" {{ $status == 'empty' ? 'selected' : '' }}>
                        Bộ chưa có sách
                    </option>
                </select>
            </div>

            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 rounded-pill shadow-sm">
                    Lọc dữ liệu
                </button>

                <a href="{{ route('admin.book_sets.index') }}"
                   class="btn btn-outline-secondary w-100 rounded-pill">
                    Xóa lọc
                </a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover bang-du-lieu align-middle">
                <thead>
                    {{-- Đã thêm text-center cho toàn bộ hàng tiêu đề --}}
                    <tr class="text-center">
                        <th width="60">ID</th>
                        <th width="90">Ảnh bìa</th>
                        <th>Tên bộ sách</th>
                        <th>Số lượng sách</th>
                        <th>Giá Combo</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    @if(count($sets_list) > 0)
                        @foreach($sets_list as $s)
                            @php
                                $goc = $s->total_price ? $s->total_price : 0;
                                $ban = $goc - ($goc * $s->discount / 100);

                                // LOGIC TÌM ẢNH BỘ SÁCH TRONG STORAGE (QUY TẮC ID_ID)
                                $extensions = ['webp', 'jpg', 'png', 'jpeg'];
                                $anh = 'https://via.placeholder.com/50x70?text=Set';
                                $fileNameBase = $s->set_id . '_' . $s->set_id;

                                foreach ($extensions as $ext) {
                                    if (file_exists(storage_path("app/public/image/{$fileNameBase}.{$ext}"))) {
                                        $anh = asset("storage/image/{$fileNameBase}.{$ext}");
                                        break;
                                    }
                                }

                                // Nếu storage không có thì dùng link_images trong DB
                                if ($anh == 'https://via.placeholder.com/50x70?text=Set' && !empty($s->link_images)) {
                                    $anh = $s->link_images;
                                }
                            @endphp

                            {{-- Đã thêm text-center cho các cột cần thiết --}}
                            <tr>
                                <td class="text-center">{{ $s->set_id }}</td>

                                <td class="text-center">
                                    <img src="{{ $anh }}" class="anh-bo-sach shadow-sm" onerror="this.src='https://via.placeholder.com/50x70?text=Set'">
                                </td>

                                <td>
                                    <div class="fw-bold text-dark">{{ $s->name }}</div>
                                    <div class="small text-muted">
                                        {{ substr($s->description, 0, 50) }}...
                                    </div>
                                </td>

                                <td class="text-center">
                                    <span class="badge rounded-pill bg-info text-dark">
                                        {{ $s->book_count }} quyển sách
                                    </span>
                                </td>

                                <td class="text-center">
                                    @if($s->discount > 0)
                                        <div class="gia-goc">
                                            {{ number_format($goc) }}đ
                                        </div>
                                        <div class="gia-moi">
                                            {{ number_format($ban) }}đ
                                        </div>
                                    @else
                                        <div class="fw-bold text-dark">
                                            {{ number_format($goc) }}đ
                                        </div>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <a href="{{ route('admin.book_sets.edit', $s->set_id) }}"
                                       class="btn btn-info text-white nut-hanh-dong">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>

                                    <a href="{{ route('admin.book_sets.delete', $s->set_id) }}"
                                       class="btn btn-danger nut-hanh-dong"
                                       onclick="return confirm('Xác nhận xóa bộ sách này?')">
                                        <i class="fas fa-trash"></i> Xóa
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                Không tìm thấy bộ sách nào.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Phần phân trang giữ nguyên đầy đủ --}}
        @if($total_pages > 1)
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    {{-- Prev --}}
                    <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                        <a class="page-link"
                        href="?page={{ $page - 1 }}&search={{ $search }}&status={{ $status }}">
                            «
                        </a>
                    </li>

                    {{-- Trang 1 --}}
                    <li class="page-item {{ $page == 1 ? 'active' : '' }}">
                        <a class="page-link"
                        href="?page=1&search={{ $search }}&status={{ $status }}">
                            1
                        </a>
                    </li>

                    @if($start_page > 2)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif

                    @for($i = $start_page; $i <= $end_page; $i++)
                        @if($i != 1 && $i != $total_pages)
                            <li class="page-item {{ $i == $page ? 'active' : '' }}">
                                <a class="page-link"
                                href="?page={{ $i }}&search={{ $search }}&status={{ $status }}">
                                    {{ $i }}
                                </a>
                            </li>
                        @endif
                    @endfor

                    @if($end_page < $total_pages - 1)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif

                    @if($total_pages > 1)
                        <li class="page-item {{ $page == $total_pages ? 'active' : '' }}">
                            <a class="page-link"
                            href="?page={{ $total_pages }}&search={{ $search }}&status={{ $status }}">
                                {{ $total_pages }}
                            </a>
                        </li>
                    @endif

                    {{-- Next --}}
                    <li class="page-item {{ $page >= $total_pages ? 'disabled' : '' }}">
                        <a class="page-link"
                        href="?page={{ $page + 1 }}&search={{ $search }}&status={{ $status }}">
                            »
                        </a>
                    </li>
                </ul>
            </nav>
        @endif

    </div>
</div>

@endsection
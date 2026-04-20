@extends('layouts.app')
@section('title', 'Quản lý đánh giá')

@section('content')

<style>
    .the-bang { background: #fff; border-radius: 12px; }
    .bang-du-lieu th { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; }
    .form-control, .form-select { border-radius: 20px; }
    .stat-box { border-radius: 15px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .nut-hanh-dong {
        border-radius: 20px !important;
        padding: 5px 15px !important;
        margin: 0 3px;
        font-size: 0.85rem;
        display: inline-block;
        text-decoration: none;
        transition: all 0.3s;
    }
    .badge-tron { border-radius: 20px; padding: 6px 12px; }
    .anh-sach-mini { width: 40px; height: 55px; object-fit: cover; border-radius: 4px; }
</style>

<div class="container-fluid">

    {{-- ===== STATS ===== --}}
    <div class="row g-3 mb-4 text-center">
        <div class="col-md-3">
            <div class="card stat-box bg-primary text-white p-3">
                <h3 class="mb-0 fw-bold">{{ $stats->total_reviews }}</h3>
                <div class="small">Tổng đánh giá</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-box bg-warning text-dark p-3">
                <h3 class="mb-0 fw-bold">{{ number_format($stats->avg_rating,1) }} ★</h3>
                <div class="small">Điểm trung bình</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-box bg-success text-white p-3">
                <h3 class="mb-0 fw-bold">{{ $stats->positive }}</h3>
                <div class="small">Đánh giá tốt (4-5★)</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-box bg-danger text-white p-3">
                <h3 class="mb-0 fw-bold">{{ $stats->negative }}</h3>
                <div class="small">Đánh giá tệ (1-2★)</div>
            </div>
        </div>
    </div>

    {{-- ===== FILTER ===== --}}
    <div class="card the-bang border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <input type="text" class="form-control px-3" name="search"
                        placeholder="Nội dung, tên sách, người dùng..."
                        value="{{ $search }}">
                </div>

                <div class="col-md-2">
                    <select name="rating" class="form-select">
                        <option value="">-- Mức sao --</option>
                        @for($i=5;$i>=1;$i--)
                            <option value="{{ $i }}" {{ $rating_filter==$i?'selected':'' }}>
                                {{ $i }} Sao
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" value="{{ $date_from }}">
                </div>

                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" value="{{ $date_to }}">
                </div>

                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Lọc</button>
                </div>

                <div class="col-md-1">
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary w-100 rounded-pill">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== TABLE ===== --}}
    <div class="card the-bang border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 text-primary fw-bold">
                <i class="fas fa-comments me-2"></i>DANH SÁCH PHẢN HỒI
            </h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="book-table" class="table table-bordered table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="200" class="text-start">Sách</th>
                            <th width="150">Người gửi</th>
                            <th width="120">Điểm</th>
                            <th class="text-start">Nội dung bình luận</th>
                            <th width="120">Ngày đăng</th>
                            <th width="260">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        @if($reviews_list->count() > 0)
                            @foreach($reviews_list as $r)
                                @php
                                    // LOGIC TÌM ẢNH SÁCH THÔNG MINH TRONG STORAGE
                                    $extensions = ['webp', 'jpg', 'png', 'jpeg'];
                                    $anh_review = 'https://via.placeholder.com/40x55?text=No+Img';
                                    
                                    foreach ($extensions as $ext) {
                                        if (file_exists(storage_path("app/public/image/{$r->book_id}.{$ext}"))) {
                                            $anh_review = asset("storage/image/{$r->book_id}.{$ext}");
                                            break;
                                        }
                                    }

                                    if ($anh_review == 'https://via.placeholder.com/40x55?text=No+Img' && !empty($r->link_images)) {
                                        $anh_review = $r->link_images;
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $anh_review }}" class="anh-sach-mini me-2 shadow-sm" onerror="this.src='https://via.placeholder.com/40x55?text=No+Img'">
                                            <div class="small fw-bold text-truncate" style="max-width: 130px;">
                                                {{ $r->book_title }}
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <div class="fw-bold small">
                                            {{ $r->fullname != '' ? $r->fullname : $r->username }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.7rem;">
                                            {{ $r->email }}
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <div class="text-warning small mb-1">
                                            @for($i=1;$i<=5;$i++)
                                                @if($i <= $r->rating)
                                                    <i class="fas fa-star"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="badge rounded-pill bg-light text-dark border">
                                            {{ $r->rating }}/5
                                        </span>
                                    </td>

                                    <td>
                                        <div class="small text-dark" style="max-height: 50px; overflow: hidden;">
                                            {{ $r->comment }}
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <div class="small">
                                            {{ date('d/m/y', strtotime($r->created_at)) }}
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('admin.reviews.edit',$r->review_id) }}"
                                            class="btn btn-info text-white nut-hanh-dong">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>

                                        <a href="{{ route('admin.reviews.delete',$r->review_id) }}"
                                            class="btn btn-danger nut-hanh-dong">
                                            <i class="fas fa-trash"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    Không tìm thấy đánh giá nào.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- ===== PAGINATION ===== --}}
            @if($reviews_list->lastPage() > 1)
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        @for($i = 1; $i <= $reviews_list->lastPage(); $i++)
                            <li class="page-item {{ $i == $reviews_list->currentPage() ? 'active' : '' }}">
                                <a class="page-link shadow-none"
                                    href="?page={{ $i }}&search={{ $search }}&rating={{ $rating_filter }}">
                                    {{ $i }}
                                </a>
                            </li>
                        @endfor
                    </ul>
                </nav>
            @endif

        </div>
    </div>

</div>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function () {
    // Không cần khởi tạo DataTable thủ công nếu bạn đã dùng Phân trang của Laravel
    // Nhưng nếu muốn dùng các tính năng lọc/search nhanh của Jquery:
    $('#book-table').DataTable({
        paging: false, // Tắt phân trang của Datatable vì đã có Laravel phân trang
        info: false,
        responsive: true,
        autoWidth: false,
        searching: false // Tắt search của Datatable vì đã có form lọc ở trên
    });
});
</script>

@endsection
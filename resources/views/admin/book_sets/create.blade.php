@extends('layouts.app')


@section('title', 'Thêm bộ sách mới')


@section('content')


<style>
    .khung-nhap {
        background: #fff;
        padding: 25px;
        border-radius: 12px;
    }
    .vung-chon-sach {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #eee;
        border-radius: 8px;
        background: #fcfcfc;
    }
    .form-control, .form-select {
        border-radius: 8px;
    }
    .anh-nho {
        width: 40px;
        height: 55px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #ddd;
    }
    .nut-bam {
        border-radius: 20px !important;
        padding: 8px 25px !important;
        font-weight: bold;
    }
</style>


<div class="container-fluid">
    <div class="card border-0 shadow-sm khung-nhap">


        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-primary fw-bold">
                THÊM BỘ SÁCH MỚI
            </h4>


            <a href="{{ route('admin.book_sets.index') }}"
               class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                Quay lại
            </a>
        </div>


        {{-- ERROR --}}
        @if(isset($error) && $error != '')
            <div class="alert alert-danger border-0 shadow-sm">
                {{ $error }}
            </div>
        @endif


        <form method="POST"
              action="{{ route('admin.book_sets.store') }}"
              enctype="multipart/form-data">
            @csrf


            <div class="row g-4">


                <!-- LEFT -->
                <div class="col-md-7">


                    <div class="mb-3">
                        <label class="fw-bold mb-1">Tên bộ sách *</label>
                        <input type="text"
                               class="form-control"
                               name="name"
                               required
                               placeholder="Ví dụ: Bộ sách rèn luyện tư duy">
                    </div>


                    <div class="mb-3">
                        <label class="fw-bold mb-1">Mô tả chi tiết</label>
                        <textarea class="form-control"
                                  name="description"
                                  rows="4"
                                  placeholder="Nhập mô tả ngắn gọn về bộ sách..."></textarea>
                    </div>


                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fw-bold mb-1">Giá Combo (VNĐ)</label>
                            <input type="number"
                                   class="form-control"
                                   name="price"
                                   value="0">
                        </div>


                        <div class="col-md-6">
                            <label class="fw-bold mb-1">Giảm giá (%)</label>
                            <input type="number"
                                   class="form-control"
                                   name="discount"
                                   value="0"
                                   min="0" max="100">
                        </div>
                    </div>


                    <div class="mt-3">
                        <label class="fw-bold mb-1">Link ảnh bên ngoài (URL)</label>
                        <input type="text"
                               class="form-control"
                               name="link_images"
                               placeholder="https://...">
                    </div>


                </div>


                <!-- RIGHT -->
                <div class="col-md-5">


                    <div class="p-3 bg-light rounded-3 border border-dashed">
                        <label class="fw-bold mb-2">
                            <i class="fas fa-image"></i> Tải ảnh bìa lên
                        </label>


                        <input type="file"
                               class="form-control mb-3"
                               name="image">


                        <div class="alert alert-warning border-0 small mb-0">
                            <h6 class="fw-bold small">
                                <i class="fas fa-info-circle"></i> LƯU Ý:
                            </h6>
                            <ul class="mb-0 ps-3">
                                <li>Giá nên thấp hơn tổng giá các sách lẻ.</li>
                                <li>Tích chọn sách ở danh sách bên dưới để thêm vào bộ.</li>
                                <li>Hình ảnh sẽ hiển thị ở trang danh sách sau khi lưu.</li>
                            </ul>
                        </div>
                    </div>


                </div>


            </div>


            <!-- DANH SÁCH SÁCH -->
            <div class="mt-5">
                <h5 class="fw-bold text-dark mb-3 border-start border-4 border-primary ps-2">
                    DANH SÁCH SÁCH THÀNH PHẦN
                </h5>


                <div class="vung-chon-sach shadow-sm">


                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th width="60" class="text-center">Chọn</th>
                                <th width="70" class="text-center">Ảnh</th>
                                <th class="text-center">Tên sách lẻ</th>
                                <th class="text-center">Giá lẻ</th>
                                <th width="120" class="text-center">Số lượng</th>
                            </tr>
                        </thead>


                        <tbody>


                            @if(count($books_list) > 0)


                                @foreach($books_list as $sach)


                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox"
                                                   name="book_ids[]"
                                                   value="{{ $sach->book_id }}"
                                                   class="form-check-input">
                                        </td>


                                        <td>
                                            @php
                                                $img = $sach->link_images;
                                                if ($img == '') {
                                                    $img = 'https://via.placeholder.com/40x55';
                                                }
                                            @endphp


                                            <img src="{{ $img }}" class="anh-nho">
                                        </td>


                                        <td>
                                            <div class="fw-bold small">
                                                {{ $sach->title }}
                                            </div>
                                            <div class="text-muted small">
                                                {{ $sach->author }}
                                            </div>
                                        </td>


                                        <td>
                                            <span class="text-primary fw-bold small">
                                                {{ number_format($sach->price) }}đ
                                            </span>
                                        </td>


                                        <td>
                                            <input type="number"
                                                   name="quantity_{{ $sach->book_id }}"
                                                   value="1"
                                                   min="1"
                                                   class="form-control form-control-sm text-center"
                                                   style="max-width: 80px;">
                                        </td>
                                    </tr>


                                @endforeach


                            @else
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Chưa có dữ liệu sách lẻ để chọn.
                                    </td>
                                </tr>
                            @endif


                        </tbody>
                    </table>


                </div>
            </div>


            <!-- BUTTON -->
            <div class="mt-5 pt-3 border-top d-flex gap-2">


                <button type="submit"
                        class="btn btn-primary nut-bam px-5 shadow">
                    <i class="fas fa-save me-2"></i>
                    LƯU BỘ SÁCH
                </button>


                <a href="{{ route('admin.book_sets.index') }}"
                   class="btn btn-outline-secondary nut-bam px-4">
                    HỦY BỎ
                </a>


            </div>


        </form>
    </div>
</div>


@endsection

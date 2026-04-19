@extends('layouts.app')


@section('title', 'Xác nhận xóa bộ sách')


@section('content')


<style>
    .khung-canh-bao {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 30px;
        margin-top: 20px;
    }
    .thong-tin-anh {
        border: 1px solid #ddd;
        padding: 5px;
        background: #f9f9f9;
        max-height: 250px;
        border-radius: 5px;
    }
    .tieu-de-canh-bao {
        color: #d9534f;
        font-weight: bold;
        border-bottom: 2px solid #d9534f;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
</style>


<div class="container">
    <div class="khung-canh-bao shadow-sm">


        <h3 class="tieu-de-canh-bao text-center">
            <i class="fas fa-exclamation-triangle"></i>
            XÁC NHẬN XÓA VĨNH VIỄN
        </h3>


        {{-- ERROR --}}
        @if(isset($error) && $error != '')
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endif


        <div class="row">


            <!-- ẢNH -->
            <div class="col-md-5 text-center mb-4">


                @php
                    $anh_hien_thi = $book_set->link_images;
                    if ($anh_hien_thi == '') {
                        $anh_hien_thi = 'https://via.placeholder.com/300x200?text=Khong+co+anh';
                    }
                @endphp


                <img src="{{ $anh_hien_thi }}"
                     class="thong-tin-anh img-fluid"
                     alt="Ảnh bộ sách">


                <div class="mt-3">
                    <span class="badge bg-secondary p-2">
                        ID Bộ sách: #{{ $book_set->set_id }}
                    </span>
                </div>
            </div>


            <!-- INFO -->
            <div class="col-md-7">


                <div class="alert alert-warning border-warning">
                    <h5>Bạn đang thực hiện xóa bộ sách:</h5>


                    <p class="display-6"
                       style="font-size: 1.5rem; font-weight: bold;">
                        {{ $book_set->name }}
                    </p>


                    <hr>


                    <ul class="mb-0">
                        <li>
                            Số lượng sách thành phần đang có:
                            <strong>{{ $book_count }} cuốn</strong>.
                        </li>


                        <li>
                            Giá bán niêm yết:
                            <strong>{{ number_format($book_set->price) }} đ</strong>.
                        </li>


                        <li class="text-danger fw-bold">
                            Dữ liệu này sẽ bị xóa hoàn toàn khỏi hệ thống!
                        </li>
                    </ul>
                </div>


                <!-- BUTTON -->
                <div class="card border-danger mt-4">
                    <div class="card-body bg-light">


                        <p class="text-center text-muted">
                            Bấm "Đồng ý xóa" để hoàn tất hoặc "Quay lại" để hủy bỏ thao tác này.
                        </p>


                        <form method="POST"
                              action="{{ route('admin.book_sets.destroy', $book_set->set_id) }}">
                            @csrf


                            <div class="row g-2">


                                <div class="col-6">
                                    <button type="submit"
                                            name="confirm"
                                            class="btn btn-danger btn-lg w-100 fw-bold">
                                        <i class="fas fa-check-circle"></i>
                                        ĐỒNG Ý XÓA
                                    </button>
                                </div>


                                <div class="col-6">
                                    <a href="{{ route('admin.book_sets.index') }}"
                                       class="btn btn-secondary btn-lg w-100 fw-bold">
                                        <i class="fas fa-arrow-left"></i>
                                        QUAY LẠI
                                    </a>
                                </div>


                            </div>
                        </form>


                    </div>
                </div>


            </div>
        </div>


    </div>
</div>


@endsection

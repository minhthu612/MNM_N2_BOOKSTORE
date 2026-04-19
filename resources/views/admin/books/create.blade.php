@extends('layouts.app')


@section('title', 'Thêm sách mới')


@section('content')


@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif


<div class="card">
    <div class="card-header bg-white border-bottom">
        <h4 class="mb-0">THÔNG TIN SÁCH</h4>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data" action="{{ route('admin.books.store') }}">
            @csrf


            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Tên sách *</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>


                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="description" rows="5"></textarea>
                    </div>


                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tác giả *</label>
                                <input type="text" class="form-control" name="author" required>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Danh mục *</label>
                                <select class="form-control" name="category_id" required>
                                    <option value="">-- Chọn --</option>


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
                        <label class="form-label">Link ảnh ngoài</label>
                        <input type="url" class="form-control" name="link_images">
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="row">
                        <div class="col-8">
                            <div class="mb-3">
                                <label class="form-label">Giá *</label>
                                <input type="number" class="form-control" name="price" min="0" required>
                            </div>
                        </div>


                        <div class="col-4">
                            <div class="mb-3">
                                <label class="form-label">Giảm %</label>
                                <input type="number" class="form-control" name="discount" min="0" max="100" value="0">
                            </div>
                        </div>
                    </div>


                    <div class="mb-3">
                        <label class="form-label">Số lượng nhập *</label>
                        <input type="number" class="form-control" name="stock" min="0" required>
                    </div>


                    <div class="mb-3">
                        <label class="form-label">Ảnh bìa</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                    </div>
                </div>
            </div>


            <div class="border-top pt-4 mt-4">
                <button type="submit" class="btn btn-primary">Lưu sách</button>


                <a href="{{ route('admin.books.index') }}" class="btn btn-secondary">
                    Hủy
                </a>
            </div>


        </form>
    </div>
</div>


@endsection

@extends('layouts.app')


@section('content')


<style>
    .khung-trang { background: #fff; padding: 30px; border-radius: 15px; border: 1px solid #eee; }
    .o-nhap { border-radius: 10px !important; padding: 12px; }
    .nut-hanh-dong { border-radius: 25px !important; padding: 10px 30px !important; font-weight: bold; }
    .thong-tin-sach { background: #f8f9fa; border-left: 5px solid #007bff; padding: 15px; border-radius: 8px; }
</style>


<div class="container">
    <div class="khung-trang shadow-sm">


        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-primary fw-bold mb-0">ĐIỀU CHỈNH SỐ LƯỢNG KHO</h4>
            <a href="{{ route('admin.inventory.history') }}" class="btn btn-outline-secondary rounded-pill px-3">Quay lại</a>
        </div>


        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm">
                {{ session('error') }}
            </div>
        @endif


        <div class="row">
            <div class="col-md-7">


                <form method="POST" action="">
                    @csrf


                    <div class="thong-tin-sach mb-4">
                        <div class="small text-muted text-uppercase">Sản phẩm điều chỉnh:</div>
                        <div class="fw-bold fs-5">{{ $inventory->title }}</div>
                        <div class="small text-secondary">Tác giả: {{ $inventory->author }}</div>
                    </div>


                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="fw-bold mb-2">Tồn kho hiện tại</label>
                            <input type="text" class="form-control o-nhap bg-white"
                                   value="{{ $inventory->stock }} cuốn" disabled>
                        </div>


                        <div class="col-6">
                            <label class="fw-bold mb-2">Mức tối thiểu</label>
                            <input type="text" class="form-control o-nhap bg-white"
                                   value="{{ $inventory->reorder_level }} cuốn" disabled>
                        </div>
                    </div>


                    <div class="mb-4">
                        <label class="fw-bold mb-2 text-danger">Số lượng thay đổi (±) *</label>
                        <div class="input-group">
                            <input type="number" name="adjustment"
                                   class="form-control o-nhap text-center fs-5 fw-bold"
                                   placeholder="0" required>
                            <span class="input-group-text bg-light px-4">Cuốn</span>
                        </div>
                    </div>


                    <div class="mb-4">
                        <label class="fw-bold mb-2">Ghi chú lý do</label>
                        <textarea name="note" class="form-control o-nhap" rows="3"></textarea>
                    </div>


                    <div class="pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-primary nut-hanh-dong shadow">
                            <i class="fas fa-save me-2"></i> CẬP NHẬT KHO
                        </button>


                        <a href="{{ route('admin.inventory.history') }}"
                           class="btn btn-light nut-hanh-dong border">HỦY BỎ</a>
                    </div>
                </form>
            </div>
        </div>


    </div>
</div>


@endsection

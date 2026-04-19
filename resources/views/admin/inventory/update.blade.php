@extends('layouts.app')

@section('title', 'Cập nhật tồn kho')

@section('content')
<style>
    .khung-trang { background: #fff; padding: 30px; border-radius: 15px; border: 1px solid #eee; }
    .o-nhap { border-radius: 10px !important; padding: 12px; }
    .nut-hanh-dong { border-radius: 25px !important; padding: 10px 30px !important; font-weight: bold; transition: all 0.3s; }
    .nut-hanh-dong:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .thong-tin-sach { background: #f8f9fa; border-left: 5px solid #007bff; padding: 15px; border-radius: 8px; }
    .bg-light-custom { background-color: #f8f9fa; border: 1px solid #eee; }
</style>

<div class="container-fluid">
    <div class="khung-trang shadow-sm">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-primary fw-bold mb-0 text-uppercase">
                <i class="fas fa-boxes me-2"></i>Điều chỉnh số lượng kho
            </h4>
            <a href="{{ route('admin.inventory.history') }}" class="btn btn-outline-secondary rounded-pill px-3 fw-bold">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        {{-- Error Message --}}
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm mb-4">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            </div>
        @endif

        <div class="row">
            {{-- Cột trái: Form điều chỉnh --}}
            <div class="col-md-7">
                <form method="POST" action="{{ route('admin.inventory.update', $inventory->inventory_id) }}">
                    @csrf

                    <div class="thong-tin-sach mb-4 shadow-sm">
                        <div class="small text-muted text-uppercase fw-bold">Sản phẩm điều chỉnh:</div>
                        <div class="fw-bold fs-5 text-dark">{{ $inventory->title }}</div>
                        <div class="small text-secondary">Tác giả: {{ $inventory->author }}</div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="fw-bold mb-2 text-dark">Tồn kho hiện tại</label>
                            <input type="text" class="form-control o-nhap bg-white text-center fw-bold" 
                                   value="{{ $inventory->stock }} cuốn" disabled>
                        </div>
                        <div class="col-6">
                            <label class="fw-bold mb-2 text-dark">Mức tối thiểu</label>
                            <input type="text" class="form-control o-nhap bg-white text-center fw-bold" 
                                   value="{{ $inventory->reorder_level }} cuốn" disabled>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2 text-danger text-uppercase small">Số lượng thay đổi (±) *</label>
                        <div class="input-group">
                            <input type="number" name="adjustment" 
                                   class="form-control o-nhap text-center fs-5 fw-bold border-danger" 
                                   placeholder="0" required autofocus>
                            <span class="input-group-text bg-danger text-white px-4 fw-bold">Cuốn</span>
                        </div>
                        <div class="mt-2 small text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Nhập số dương (VD: 10) để <b>nhập thêm</b>, số âm (VD: -5) để <b>xuất kho</b>.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2 text-dark">Ghi chú lý do</label>
                        <textarea name="note" class="form-control o-nhap" rows="3" 
                                  placeholder="Ví dụ: Nhập hàng bổ sung, Kiểm kho định kỳ tháng 4..."></textarea>
                    </div>

                    <div class="pt-4 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-primary nut-hanh-dong shadow">
                            <i class="fas fa-save me-2"></i> CẬP NHẬT KHO
                        </button>
                        <a href="{{ route('admin.inventory.history') }}" 
                           class="btn btn-light nut-hanh-dong border">HỦY BỎ</a>
                    </div>
                </form>
            </div>

            {{-- Cột phải: Quy tắc & Lịch sử --}}
            <div class="col-md-5">
                <div class="card border-0 bg-light-custom rounded-3 h-100 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-shield-alt me-2"></i>QUY TẮC CẬP NHẬT
                        </h6>
                        <hr>
                        <div class="mb-4">
                            <strong class="text-dark"><i class="fas fa-calculator me-2"></i>1. Cách tính số lượng:</strong>
                            <p class="small text-muted mt-1 ms-4">Số mới = Tồn hiện tại + Số lượng thay đổi.</p>
                        </div>

                        <div class="mb-4">
                            <strong class="text-dark"><i class="fas fa-sync-alt me-2"></i>2. Tự động chuyển trạng thái:</strong>
                            <ul class="small text-muted mt-2 ms-2" style="list-style: none;">
                                <li class="mb-2"><span class="badge bg-warning text-dark me-2">SẮP HẾT</span> Khi tồn < Mức tối thiểu.</li>
                                <li class="mb-2"><span class="badge bg-danger me-2">HẾT HÀNG</span> Khi tồn bằng hoặc nhỏ hơn 0.</li>
                                <li class="mb-2"><span class="badge bg-success me-2">CÒN HÀNG</span> Khi tồn >= Mức tối thiểu.</li>
                            </ul>
                        </div>

                        <div class="alert alert-info border-0 shadow-sm small mt-auto">
                            <i class="fas fa-history me-1"></i> <strong>Cập nhật gần nhất:</strong><br>
                            <div class="mt-1 ps-4 fw-bold">
                                {{ $inventory->last_updated ? \Carbon\Carbon::parse($inventory->last_updated)->format('d/m/Y - H:i') : 'Chưa có dữ liệu' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> {{-- End Row --}}
    </div>
</div>
@endsection
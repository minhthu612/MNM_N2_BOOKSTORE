@extends('layouts.app')

@section('title', 'Cập nhật đơn hàng #' . $order->order_id)

@section('content')
<style>
    .khung-cap-nhat { background: #fff; padding: 30px; border-radius: 15px; border: 1px solid #eee; }
    .o-nhap { border-radius: 10px !important; padding: 12px; }
    .nut-bam { border-radius: 25px !important; padding: 10px 30px !important; font-weight: bold; transition: all 0.3s; }
    .nut-bam:hover { transform: translateY(-2px); }
    .thong-tin-don { background: #f8f9fa; border-left: 5px solid #0d6efd; padding: 15px; border-radius: 8px; }
    
    /* Ghi chú lịch sử */
    .khung-ghi-chu { background: #fdfdfd; border-radius: 10px; border: 1px solid #f0f0f0; }
    
    /* Alert lưu ý kiểu mới cho đẹp */
    .alert-custom-note {
        background-color: #fff9db;
        border-left: 4px solid #fab005;
        color: #856404;
        border-radius: 8px;
        padding: 12px 15px;
    }
</style>

<div class="container">
    {{-- 1. GIAO DIỆN XÁC NHẬN --}}
    @if ($action != null)
        <div class="card border-danger shadow mb-4">
            <div class="card-body text-center py-4">
                <h4 class="text-danger fw-bold mb-3"><i class="fas fa-question-circle"></i> XÁC NHẬN</h4>
                <p class="fs-5">
                    @if($action == 'cancel')
                        Cảnh báo: Bạn đang yêu cầu <b>HỦY</b> đơn hàng này?
                    @else
                        Bạn có chắc chắn muốn thực hiện thao tác này?
                    @endif
                </p>
                <div class="d-flex justify-content-center gap-3 mt-4">
                    <a href="{{ route('admin.orders.update', $order->order_id) }}?action={{ $action }}&confirmed=1" 
                       class="btn btn-danger nut-bam shadow-sm">ĐỒNG Ý</a>
                    <a href="{{ route('admin.orders.show', $order->order_id) }}" 
                       class="btn btn-light nut-bam border">HỦY BỎ</a>
                </div>
            </div>
        </div>
    @else

    {{-- 2. GIAO DIỆN FORM CẬP NHẬT --}}
    <div class="khung-cap-nhat shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-primary fw-bold mb-0">
                <i class="fas fa-edit me-2"></i>CẬP NHẬT TRẠNG THÁI
            </h4>
            <a href="{{ route('admin.orders.show', $order->order_id) }}" class="btn btn-outline-secondary rounded-pill px-3 fw-bold small">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        <div class="row">
            {{-- Cột trái: Form nhập --}}
            <div class="col-md-7">
                <div class="thong-tin-don mb-4 shadow-sm">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Mã đơn hàng:</small>
                            <div class="fw-bold fs-5 text-primary">#{{ $order->order_id }}</div>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Khách hàng:</small>
                            <div class="fw-bold text-dark">{{ $order->fullname ?? $order->username }}</div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.orders.update', $order->order_id) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="fw-bold mb-2 text-dark">Trạng thái xử lý</label>
                        <select name="status" class="form-select o-nhap shadow-sm">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Đang đóng gói</option>
                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Đang giao hàng</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Đã giao thành công</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Đã hủy đơn</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2 text-dark">Mã vận đơn (Nếu có)</label>
                        <input type="text" name="tracking_number" class="form-control o-nhap shadow-sm" 
                               value="{{ $order->tracking_number }}" placeholder="Nhập mã từ đơn vị vận chuyển...">
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2 text-dark">Ghi chú nội bộ</label>
                        <textarea name="notes" class="form-control o-nhap shadow-sm" rows="3" 
                                  placeholder="Nhập lý do thay đổi hoặc lời nhắn cho khách..."></textarea>
                        <div class="mt-2 text-muted small fst-italic">
                            <i class="fas fa-info-circle me-1"></i> Nội dung này sẽ được lưu vào lịch sử đơn hàng.
                        </div>
                    </div>

                    <div class="pt-3 border-top">
                        <button type="submit" class="btn btn-primary nut-bam shadow w-100 py-3">
                            <i class="fas fa-save me-2"></i> LƯU THAY ĐỔI
                        </button>
                    </div>
                </form>
            </div>

            {{-- Cột phải: Lịch sử và Lưu ý --}}
            <div class="col-md-5">
                {{-- Khung lịch sử --}}
                <div class="card khung-ghi-chu border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-history me-2"></i>LỊCH SỬ GHI CHÚ</h6>
                        <hr class="mt-0">
                        <div class="small text-muted" style="white-space: pre-line; max-height: 250px; overflow-y: auto; line-height: 1.6;">
                            @if ($order->notes != '')
                                {{ $order->notes }}
                            @else
                                <span class="fst-italic">Chưa có ghi chú nào trước đó.</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                {{-- Khung lưu ý đã được đưa vào đây cho đẹp --}}
                <div class="alert-custom-note shadow-sm">
                    <div class="d-flex">
                        <i class="fas fa-lightbulb me-2 mt-1"></i>
                        <div>
                            <strong class="small d-block mb-1 text-uppercase">Mẹo nhỏ:</strong>
                            <span class="small">Khi chuyển sang <b>Đã giao</b>, hệ thống sẽ tự động chốt đơn và ghi nhận doanh thu thực tế.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
@extends('layouts.app')




@section('title', 'Chi tiết: ' . $user->username)




@section('content')




<style>
    .khung-trang { background: #fff; border-radius: 15px; border: 1px solid #eee; padding: 25px; }
    .avatar-tron {
        width: 100px; height: 100px; background: #e9ecef;
        border-radius: 50%; display: flex; align-items: center;
        justify-content: center; font-size: 40px; font-weight: bold; color: #007bff;
        margin: 0 auto 15px; border: 4px solid #fff; shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .the-thong-ke { border-radius: 12px; border: none; padding: 20px; color: #fff; height: 100%; }
    .nut-bam { border-radius: 20px !important; padding: 8px 20px; font-weight: bold; }
    .dong-hoat-dong { border-left: 3px solid #dee2e6; padding-left: 20px; position: relative; margin-bottom: 20px; }
    .dong-hoat-dong::before {
        content: ''; position: absolute; left: -9px; top: 0;
        width: 15px; height: 15px; border-radius: 50%; background: #007bff;
    }
</style>




<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0">HỒ SƠ NGƯỜI DÙNG</h4>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary nut-bam">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
        </a>
    </div>




    <div class="row">
        {{-- LEFT --}}
        <div class="col-md-4">
            <div class="khung-trang text-center mb-4 shadow-sm">
                <div class="avatar-tron shadow-sm">
                    {{ strtoupper(substr($user->username, 0, 1)) }}
                </div>




                <h4 class="fw-bold mb-1">{{ $user->fullname }}</h4>
                <p class="text-muted small">{{ '@' . $user->username }}</p>




                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-primary rounded-pill px-3">{{ $user->role }}</span>




                    @if ($user->status == 'Active')
                        <span class="badge bg-success rounded-pill px-3">Hoạt động</span>
                    @else
                        <span class="badge bg-danger rounded-pill px-3">Bị khóa</span>
                    @endif
                </div>




                <hr>




                <div class="text-start">
                    <div class="mb-3">
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">
                            Email liên hệ:
                        </small>
                        <span class="text-primary">{{ $user->email }}</span>
                    </div>




                    <div class="mb-3">
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">
                            Số điện thoại:
                        </small>
                        <span>
                            {{ $user->phone != '' ? $user->phone : 'Chưa cập nhật' }}
                        </span>
                    </div>




                    <div class="mb-3">
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">
                            Ngày sinh / Giới tính:
                        </small>
                        <span>
                            {{ $user->birthdate ? date('d/m/Y', strtotime($user->birthdate)) : 'N/A' }}
                            -
                            {{ $user->gender == 'male' ? 'Nam' : ($user->gender == 'female' ? 'Nữ' : 'Khác') }}
                        </span>
                    </div>
                </div>




                <div class="d-grid gap-2 mt-4">
                    <a href="{{ route('admin.users.edit', $user->user_id) }}" class="btn btn-warning nut-bam">
                        Sửa thông tin
                    </a>




                    <a href="{{ route('admin.users.delete', $user->user_id) }}" class="btn btn-outline-danger nut-bam">
                        Xóa tài khoản
                    </a>
                </div>
            </div>
        </div>




        {{-- RIGHT --}}
        <div class="col-md-8">




            {{-- STATS --}}
            <div class="row g-3 mb-4 text-center">
                <div class="col-md-4">
                    <div class="the-thong-ke bg-primary shadow-sm">
                        <h3 class="fw-bold mb-0">{{ $order_stats->total_orders ?? 0 }}</h3>
                        <small class="text-uppercase fw-bold" style="font-size: 0.7rem;">Đơn hàng</small>
                    </div>
                </div>




                <div class="col-md-4">
                    <div class="the-thong-ke bg-success shadow-sm">
                        <h3 class="fw-bold mb-0">
                            {{ number_format($order_stats->total_spent ?? 0) }}đ
                        </h3>
                        <small class="text-uppercase fw-bold" style="font-size: 0.7rem;">Tổng chi tiêu</small>
                    </div>
                </div>




                <div class="col-md-4">
                    <div class="the-thong-ke bg-info shadow-sm">
                        <h3 class="fw-bold mb-0">
                            {{ number_format($review_stats->avg_rating ?? 0, 1) }}★
                        </h3>
                        <small class="text-uppercase fw-bold" style="font-size: 0.7rem;">Đánh giá TB</small>
                    </div>
                </div>
            </div>




            {{-- ACTIVITIES --}}
            <div class="khung-trang shadow-sm">
                <h5 class="fw-bold mb-4">
                    <i class="fas fa-history me-2 text-primary"></i>HOẠT ĐỘNG GẦN ĐÂY
                </h5>




                @if (count($activities) > 0)
                    <div class="ms-2">
                        @foreach ($activities as $act)
                            <div class="dong-hoat-dong">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>




                                        @if ($act['type'] == 'order')
                                            <span class="fw-bold text-dark">
                                                Đặt đơn hàng #{{ $act['id']}}
                                            </span>




                                            <div class="small text-muted">
                                                Giá trị: {{ number_format($act['info']) }}đ
                                                - Trạng thái: {{ $act['status'] }}
                                            </div>




                                        @else
                                            <span class="fw-bold text-dark">
                                                Đánh giá sách: {{ $act['info'] }}
                                            </span>




                                            <div class="small text-warning">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    {{ $i <= $act['status'] ? '★' : '☆' }}
                                                @endfor
                                            </div>
                                        @endif




                                    </div>




                                    <small class="text-muted">
                                        {{ date('d/m/Y H:i', strtotime($act['created_at'])) }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-folder-open fa-3x mb-3"></i>
                        <p>Người dùng này chưa có hoạt động nào.</p>
                    </div>
                @endif
            </div>




        </div>
    </div>
</div>




@endsection

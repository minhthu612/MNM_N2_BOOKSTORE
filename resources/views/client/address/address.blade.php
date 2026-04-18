@extends('layouts.client')

@section('content')
<style>
    .card { border-radius: 16px; }
    .form-control, textarea { border-radius: 10px; }
    .btn { font-weight: 600; }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4">
                @if(session('error'))
                    <div class="alert alert-danger border-0 mb-4 shadow-sm">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    </div>
                @endif

                <h4 class="fw-bold mb-4 text-center">
                    <i class="fas fa-map-marked-alt text-primary me-2"></i>
                    {{ isset($address) ? 'Chỉnh sửa địa chỉ' : 'Thêm địa chỉ mới' }}
                </h4>

                <form method="POST" action="{{ isset($address) ? url('address/update/'.$address->address_id) : route('address.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Họ và tên *</label>
                        <input type="text" name="fullname" class="form-control" 
                               value="{{ $address->fullname ?? old('fullname') }}" placeholder="Nhập tên người nhận" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Số điện thoại *</label>
                        <input type="text" name="phone" class="form-control" 
                               value="{{ $address->phone ?? old('phone') }}" placeholder="Ví dụ: 0912345678" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Tỉnh/Thành *</label>
                            <input type="text" name="city" class="form-control" value="{{ $address->city ?? old('city') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Quận/Huyện *</label>
                            <input type="text" name="district" class="form-control" value="{{ $address->district ?? old('district') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Phường/Xã *</label>
                            <input type="text" name="ward" class="form-control" value="{{ $address->ward ?? old('ward') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Địa chỉ chi tiết *</label>
                        <textarea name="street" class="form-control" rows="2" required>{{ $address->street ?? old('street') }}</textarea>
                    </div>

                    @if(!isset($address))
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_default" id="is_default">
                            <label class="form-check-label fw-bold small" for="is_default">Đặt làm địa chỉ mặc định</label>
                        </div>
                    </div>
                    @endif

                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('checkout.index') }}" class="btn btn-light w-100 border rounded-pill py-2">Quay lại</a>
                        </div>
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2">
                                {{ isset($address) ? 'Cập nhật' : 'Lưu địa chỉ' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')


@section('content')


<style>
    .khung-cap-nhat { background: #fff; padding: 30px; border-radius: 15px; }
</style>


<div class="container">


<div class="khung-cap-nhat shadow-sm">


<h4 class="text-primary fw-bold">
    CẬP NHẬT ĐƠN HÀNG #{{ $order->order_id }}
</h4>


<form method="POST">
@csrf


<div class="mb-3">
    <label>Trạng thái</label>
    <select name="status" class="form-select">
        <option value="pending">Chờ xử lý</option>
        <option value="processing">Đang xử lý</option>
        <option value="shipped">Đang giao</option>
        <option value="delivered">Đã giao</option>
        <option value="cancelled">Đã hủy</option>
    </select>
</div>


<div class="mb-3">
    <label>Tracking</label>
    <input type="text" name="tracking_number" class="form-control">
</div>


<div class="mb-3">
    <label>Ghi chú</label>
    <textarea name="notes" class="form-control"></textarea>
</div>


<button class="btn btn-primary w-100">
    LƯU THAY ĐỔI
</button>


</form>


</div>
</div>


@endsection

@extends('layouts.app')
@section('title', 'Quản lý người dùng')
@section('content')


<style>
.the-bang { background: #fff; border-radius: 12px; }
.bang-du-lieu th { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; }
.form-control, .form-select { border-radius: 20px; }
.stat-box { border-radius: 15px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
.nut-hanh-dong { border-radius: 20px !important; padding: 5px 12px !important; margin: 0 2px; font-size: 0.8rem; }
.badge-tron { border-radius: 20px; padding: 5px 12px; font-size: 0.75rem; }
</style>


<div class="container-fluid">


<div class="row g-3 mb-4 text-center">
    <div class="col-md-3">
        <div class="card stat-box bg-primary text-white p-3">
            <h3 class="mb-0 fw-bold">{{ $stats->total_users }}</h3>
            <div class="small">Người dùng</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-box bg-dark text-white p-3">
            <h3 class="mb-0 fw-bold">{{ $stats->admins }}</h3>
            <div class="small">Quản trị viên</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-box bg-info text-white p-3">
            <h3 class="mb-0 fw-bold">{{ $stats->customers }}</h3>
            <div class="small">Khách hàng</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-box bg-success text-white p-3">
            <h3 class="mb-0 fw-bold">{{ $stats->active_count }}</h3>
            <div class="small">Đang hoạt động</div>
        </div>
    </div>
</div>


<div class="card the-bang border-0 shadow-sm">
<div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
<h5 class="mb-0 text-primary fw-bold"><i class="fas fa-users me-2"></i>DANH SÁCH THÀNH VIÊN</h5>
<a href="{{ route('admin.users.create') }}" class="btn btn-success rounded-pill px-4 shadow-sm">
<i class="fas fa-user-plus"></i> Thêm thành viên
</a>
</div>


<div class="card-body">


<form method="GET" class="row g-2 mb-4">
<div class="col-md-4">
<input type="text" class="form-control px-3" name="search" value="{{ request('search') }}">
</div>
<div class="col-md-2">
<select name="role" class="form-select">
<option value="">-- Vai trò --</option>
<option value="Admin">Admin</option>
<option value="Manager">Manager</option>
<option value="Customer">Customer</option>
</select>
</div>
<div class="col-md-2">
<select name="status" class="form-select">
<option value="">-- Trạng thái --</option>
<option value="Active">Hoạt động</option>
<option value="Inactive">Bị khóa</option>
</select>
</div>
<div class="col-md-2">
<button class="btn btn-primary w-100 rounded-pill">Lọc</button>
</div>
<div class="col-md-2">
<a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100 rounded-pill">Reset</a>
</div>
</form>


<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">
<thead class="table-light text-center">
<tr>
<th width="60">ID</th>
<th class="text-start">Tài khoản</th>
<th>Họ và tên</th>
<th>Vai trò</th>
<th>Trạng thái</th>
<th width="340">Thao tác</th>
</tr>
</thead>
<tbody>
@foreach($users as $u)
<tr class="text-center">
<td>#{{ $u->user_id }}</td>
<td class="text-start">
<div class="fw-bold">{{ $u->username }}</div>
<small class="text-muted">{{ $u->email }}</small>
</td>
<td>{{ $u->fullname }}</td>
<td>
@if($u->role == 'Admin')
<span class="badge bg-danger badge-tron">ADMIN</span>
@else
<span class="badge bg-info text-white badge-tron">{{ strtoupper($u->role) }}</span>
@endif
</td>
<td>
@if($u->status == 'Active')
<span class="badge bg-success badge-tron">HOẠT ĐỘNG</span>
@else
<span class="badge bg-secondary badge-tron">BỊ KHÓA</span>
@endif
</td>
<td>
<a href="{{ route('admin.users.show',$u->user_id) }}" class="btn btn-info text-white nut-hanh-dong"><i class="fas fa-eye"></i> Xem</a>
<a href="{{ route('admin.users.edit',$u->user_id) }}" class="btn btn-warning text-dark nut-hanh-dong"><i class="fas fa-edit"></i> Sửa</a>
@if($u->status == 'Active')
<a href="{{ route('admin.users.deactivate',$u->user_id) }}" class="btn btn-outline-danger nut-hanh-dong"><i class="fas fa-lock"></i> Khóa</a>
@else
<a href="{{ route('admin.users.activate',$u->user_id) }}" class="btn btn-outline-success nut-hanh-dong"><i class="fas fa-unlock"></i> Mở</a>
@endif
<a href="{{ route('admin.users.delete',$u->user_id) }}" class="btn btn-danger text-white nut-hanh-dong"><i class="fas fa-trash"></i> Xóa</a>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
<div class="mt-4 d-flex justify-content-center">
    {{ $users->appends(request()->all())->links() }}
</div>
</div>
</div>
</div>




@endsection

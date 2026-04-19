<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { width: 240px; height: 100vh; position: fixed; background: #343a40; color: white; z-index: 1000; }
        .sidebar h4 { font-weight: 800; letter-spacing: 1px; }
        .sidebar a { color: #c2c7d0; padding: 12px 20px; display: block; text-decoration: none; font-size: 15px; transition: all 0.3s; }
        .sidebar a:hover { background: #495057; color: #fff; padding-left: 25px; }
        .sidebar .active { background: #0d6efd; color: white !important; font-weight: bold; }
        
        .main-content { margin-left: 240px; padding: 25px; }

        /* KHUNG NAVBAR TRẮNG: Sửa bo góc và padding bự ra */
        .navbar-custom { 
            background: white; 
            padding: 15px 25px; /* Tăng khoảng cách cho thoáng */
            border-radius: 20px; /* Bo góc cực đại nhìn rất hiện đại */
            border: none; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); /* Đổ bóng nhẹ cho xịn */
        }

        /* TIÊU ĐỀ: Sửa font chữ bự và siêu đậm */
        .navbar-custom h1 { 
            font-size: 1.8rem !important; /* Chữ bự tổ chảng */
            font-weight: 850 !important; /* Độ đậm cực cao */
            color: #1a202c; 
            margin: 0; 
            letter-spacing: -1.5px; /* Sát chữ lại cho ngầu */
        }

        .admin-info { font-size: 14px; }
        .admin-info strong { display: block; font-size: 1.1rem; color: #333; }
        
        /* Role Badge giữ nguyên màu vàng nhưng tinh chỉnh bo góc */
        .role-badge {
            background-color: #ffc107;
            color: #000;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 6px;
            text-transform: uppercase;
            font-size: 10px;
            display: inline-block;
        }
    </style>
</head>

<body>

<div class="sidebar">
    <h4 class="text-center py-4 border-bottom">ADMIN</h4>

    <div class="text-center mb-4 mt-3">
        <strong>
            @if(Auth::check())
                Chào, {{ Auth::user()->fullname ?? Auth::user()->name }}
            @else
                Chào, Quản trị viên
            @endif
        </strong>
        <small class="d-block mt-1">
            <span class="role-badge">{{ Auth::user()->role ?? 'Admin' }}</span>
        </small>
    </div>

    <a href="{{ route('admin.books.index') }}" class="{{ Request::is('admin/books*') ? 'active' : '' }}">
        <i class="fas fa-book me-2"></i> Quản lý sách
    </a>
    <a href="{{ route('admin.book_sets.index') }}" class="{{ Request::is('admin/book-sets*') ? 'active' : '' }}">
        <i class="fas fa-layer-group me-2"></i> Bộ sách
    </a>
    <a href="{{ route('admin.categories.index') }}" class="{{ Request::is('admin/categories*') ? 'active' : '' }}">
        <i class="fas fa-list me-2"></i> Danh mục
    </a>
    <a href="{{ route('admin.users.index') }}" class="{{ Request::is('admin/users*') ? 'active' : '' }}">
        <i class="fas fa-users me-2"></i> Người dùng
    </a>
    <a href="{{ route('admin.orders.index') }}" class="{{ Request::is('admin/orders*') ? 'active' : '' }}">
        <i class="fas fa-file-invoice me-2"></i> Đơn hàng
    </a>
    <a href="{{ route('admin.inventory.index') }}" class="{{ Request::is('admin/inventory*') ? 'active' : '' }}">
        <i class="fas fa-warehouse me-2"></i> Kho hàng
    </a>
    <a href="{{ route('admin.reviews.index') }}" class="{{ Request::is('admin/reviews*') ? 'active' : '' }}">
        <i class="fas fa-star me-2"></i> Đánh giá
    </a>

    <div class="mt-auto p-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-danger w-100 rounded-pill fw-bold">
                <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
            </button>
        </form>
    </div>
</div>

<div class="main-content">

    <div class="navbar-custom mb-4">
        <h1 class="mb-0">@yield('title')</h1>

        <div class="admin-info text-end">
            <strong>Chào, {{ Auth::user()->fullname ?? Auth::user()->name ?? 'Admin' }}</strong>
            <div class="my-1">
                <span class="role-badge" style="font-size: 10px;">{{ Auth::user()->role ?? 'Admin' }}</span>
            </div>
            <small class="d-block text-muted">{{ Auth::user()->email ?? '' }}</small>
        </div>
    </div>

    @if(session('error')) 
        <div class="alert alert-danger border-0 shadow-sm rounded-3">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        </div> 
    @endif

    @if(session('success')) 
        <div class="alert alert-success border-0 shadow-sm rounded-3">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div> 
    @endif

    <div class="content-body">
        @yield('content')
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
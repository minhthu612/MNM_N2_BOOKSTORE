<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Store</title>

    <!-- Bootstrap + Icon -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding-top: 70px;
        }

        .navbar {
            background: linear-gradient(135deg,#667eea,#764ba2)!important;
        }

        .footer {
            background: linear-gradient(135deg,#667eea,#764ba2);
            color: white;
            margin-top: auto;
            padding: 30px 0;
        }
    </style>
</head>

<body>

<!-- ================= HEADER ================= -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
<div class="container">

    <a class="navbar-brand" href="/">
        <i class="fas fa-book"></i> Book Store
    </a>

    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">

        <!-- MENU -->
        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <a class="nav-link" href="/">
                    <i class="fas fa-home"></i> Trang chủ
                </a>
            </li>

            <!-- DANH MỤC -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-list"></i> Danh mục
                </a>

                <ul class="dropdown-menu">
                    @foreach(\Illuminate\Support\Facades\DB::table('categories')->get() as $cat)
                        <li>
                            <a class="dropdown-item"
                               href="/?category={{ $cat->category_id }}">
                                {{ $cat->category_name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="/?view=best_seller">
                    🔥 Bán chạy
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="/?view=new">
                    🆕 Mới
                </a>
            </li>
        </ul>

        <!-- SEARCH -->
        <form class="d-flex me-3" method="GET" action="/">
            <input class="form-control me-2" name="q" placeholder="Tìm sách...">
            <button class="btn btn-outline-light">
                🔍
            </button>
        </form>

        <!-- USER -->
        <ul class="navbar-nav">

        @auth
            <li class="nav-item">
                <a class="nav-link" href="#">
                    🛒 Giỏ hàng
                </a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    👤 {{ Auth::user()->fullname ?? Auth::user()->username }}
                </a>

                <ul class="dropdown-menu dropdown-menu-end">

                    @if(in_array(Auth::user()->role, ['Admin','Manager']))
                        <li>
                            <a class="dropdown-item" href="/admin">
                                ⚙️ Quản trị
                            </a>
                        </li>
                        <li><hr></li>
                    @endif

                    <li><a class="dropdown-item" href="/profile">Hồ sơ</a></li>
                    <li><a class="dropdown-item" href="#">Đơn hàng</a></li>
                    <li><a class="dropdown-item" href="#">Yêu thích</a></li>

                    <li><hr></li>

                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger">
                                Đăng xuất
                            </button>
                        </form>
                    </li>
                </ul>
            </li>

        @else
            <li class="nav-item">
                <a class="nav-link" href="{{ route('login') }}">
                    Đăng nhập
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('register') }}">
                    Đăng ký
                </a>
            </li>
        @endauth

        </ul>

    </div>
</div>
</nav>

<!-- ================= CONTENT ================= -->
<main class="flex-fill">
<div class="container mt-4">

    <!-- MESSAGE -->
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @yield('content')

</div>
</main>

<!-- ================= FOOTER ================= -->
<footer class="footer">
<div class="container">
    <div class="row">

        <div class="col-md-4">
            <h5>📚 Book Store</h5>
            <p>Chuyên cung cấp sách chất lượng.</p>
        </div>

        <div class="col-md-4">
            <h5>Liên kết</h5>
            <ul class="list-unstyled">
                <li><a href="/" class="text-light">Trang chủ</a></li>
                <li><a href="#" class="text-light">Giới thiệu</a></li>
                <li><a href="#" class="text-light">Liên hệ</a></li>
            </ul>
        </div>

        <div class="col-md-4">
            <h5>Liên hệ</h5>
            <p>📍 TP.HCM</p>
            <p>📞 0901 234 567</p>
        </div>

    </div>

    <hr>

    <div class="text-center">
        © {{ date('Y') }} Book Store
    </div>
</div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
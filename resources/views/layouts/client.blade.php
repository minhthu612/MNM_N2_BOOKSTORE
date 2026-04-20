<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Book Store')</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


    <style>
        html, body { height:100%; margin:0; }


        body {
            display: flex;
            flex-direction: column;
            padding-top: 70px;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }


        /* Navbar Gradient Tím */
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            box-shadow: 0 4px 12px rgba(102, 126, 234, .2);
            padding: 10px 0;
        }


        .navbar-brand, .nav-link { color: #fff !important; font-weight: 500; }
        .nav-link:hover { color: #ffdd57 !important; }


        .dropdown-item i {
            width: 20px;
            text-align: center;
            margin-right: 8px;
        }


        /* --- SEARCH CONTAINER (Y CHANG HÌNH MẪU) --- */
        .search-container {
            background: white;
            border-radius: 8px;
            padding: 3px;
            display: flex;
            align-items: center;
            width: 100%;
            min-width: 300px;
        }


        .search-container input {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            padding: 4px 12px;
            width: 100%;
            font-size: 14px;
            height: 28px !important;
        }


        .search-container button {
            background: #764ba2;
            color: white;
            border: none;
            border-radius: 6px;
            width: 38px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
        }


        .search-container button:hover {
            background: #5a368a;
        }


        /* Footer Gradient */
        .footer {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 35px 0;
            margin-top: auto;
            flex-shrink: 0;
        }


        /* --- PHẦN THÊM MỚI: CSS CHO BADGE GIỎ HÀNG --- */
        .cart-count-badge {
            font-size: 0.75rem;
            vertical-align: top;
            margin-left: -5px;
            border: 1px solid white;
            padding: 0.25em 0.5em;
        }


        /* Hiệu ứng rung icon khi nhận được sản phẩm */
        .shake-element {
            animation: shake-cart 0.5s ease-in-out;
        }


        @keyframes shake-cart {
            0% { transform: scale(1); }
            25% { transform: scale(1.2) rotate(15deg); }
            50% { transform: scale(1.2) rotate(-15deg); }
            75% { transform: scale(1.1) rotate(5deg); }
            100% { transform: scale(1); }
        }
    </style>
</head>


<body>


<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
<div class="container">


    <a class="navbar-brand" href="{{ url('/') }}">
        <i class="fas fa-book"></i> Book Store
    </a>


    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>


    <div class="collapse navbar-collapse" id="navbarNav">


        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/') }}">
                    <i class="fas fa-home"></i> Trang chủ
                </a>
            </li>


            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    <i class="fas fa-list"></i> Danh mục
                </a>
                <ul class="dropdown-menu">
                    @foreach(DB::table('categories')->orderBy('category_name')->get() as $cat)
                        <li>
                            <a class="dropdown-item" href="{{ url('/?category='.$cat->category_id) }}">
                                {{ $cat->category_name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>


            <li class="nav-item">
                <a class="nav-link" href="{{ url('/?view=best_seller') }}">
                    <i class="fas fa-fire"></i> Bán chạy
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link" href="{{ url('/?view=new') }}">
                    <i class="fas fa-newspaper"></i> Mới nhất
                </a>
            </li>
        </ul>


        <form action="{{ url('/search') }}" method="GET" class="d-flex mx-lg-3">
            <div class="search-container">
                <input type="text" name="q" placeholder="Tìm sách..." required value="{{ request('q') }}">
                <button type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>


        <ul class="navbar-nav">
            @auth
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('cart.index') }}">
                        <i class="fas fa-shopping-cart"></i> Giỏ hàng
                        {{-- THÊM LỚP ĐỊNH DANH cart-count-badge VÀ LOGIC ĐẾM SỐ LƯỢNG --}}
                        <span class="badge rounded-pill bg-danger cart-count-badge">
                            {{ DB::table('cart_items')
                                ->join('cart', 'cart_items.cart_id', '=', 'cart.cart_id')
                                ->where('cart.user_id', Auth::id())
                                ->count() }}
                        </span>
                    </a>
                </li>


                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i>
                        {{ Auth::user()->fullname ?: Auth::user()->username }}
                    </a>


                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        @if(in_array(Auth::user()->role, ['Admin', 'Manager']))
                            <li>
                                <a class="dropdown-item" href="{{ url('/admin') }}">
                                    <i class="fas fa-cog text-primary"></i> <strong>Quản trị hệ thống</strong>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                        @endif
                       
                        <li><a class="dropdown-item" href="{{ url('/profile') }}"><i class="fas fa-user-circle text-muted"></i> Hồ sơ</a></li>
                        <li><a class="dropdown-item" href="{{ route('orders.index') }}"><i class="fas fa-shopping-bag text-muted"></i> Đơn hàng</a></li>
                        <li><a class="dropdown-item" href="{{ route('wishlist.index') }}"><i class="fas fa-heart text-danger"></i> Yêu thích</a></li>
                        <li><hr class="dropdown-divider"></li>
                       
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt text-muted"></i> Đăng xuất
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">
                        <i class="fas fa-sign-in-alt"></i> Đăng nhập
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">
                        <i class="fas fa-user-plus"></i> Đăng ký
                    </a>
                </li>
            @endauth
        </ul>


    </div>
</div>
</nav>


<main style="flex: 1 0 auto;">
    <div class="container mt-4">


        {{-- 2. THANH TIÊU ĐỀ VÀ BỘ LỌC --}}
        @if(isset($category_name))
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm border">


                    {{-- TITLE --}}
                    <h4 class="mb-0 text-primary fw-bold">
                        <i class="fas fa-book-reader me-2"></i> {{ $category_name }}
                    </h4>


                    {{-- RIGHT BUTTONS --}}
                    <div class="d-flex align-items-center gap-2">
                        @if(($category_name == 'Sách giáo khoa') || (isset($set_id) && $set_id))
                            @if(isset($bookSets) && count($bookSets) > 0)
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle shadow-sm rounded-pill px-3"
                                        type="button"
                                        data-bs-toggle="dropdown">
                                    <i class="fas fa-layer-group me-1"></i> Bộ SGK theo lớp
                                </button>


                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <li>
                                        <a class="dropdown-item"
                                           href="?category={{ $category_id ?? '' }}">
                                            <i class="fas fa-th-list me-2 small text-muted"></i> Hiện tất cả sách lẻ
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    @foreach($bookSets as $set)
                                    <li>
                                        <a class="dropdown-item"
                                           href="?category={{ $category_id ?? '' }}&set_id={{ $set->set_id }}">
                                            {{ $set->name }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        @endif


                        <a href="{{ url('/') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            <i class="fas fa-sync-alt me-1 small"></i> Xóa lọc
                        </a>


                    </div>
                </div>
            </div>
        </div>
        @endif


        {{-- 3. NỘI DUNG CHÍNH --}}
        @yield('content')
       
    </div>
</main>


<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5><i class="fas fa-book"></i> Book Store</h5>
                <p>Chuyên cung cấp sách chất lượng với giá cả hợp lý. Đọc sách mỗi ngày, nâng cao tri thức.</p>
            </div>
            <div class="col-md-4">
                <h5>Liên kết nhanh</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ url('/') }}" class="text-light text-decoration-none">Trang chủ</a></li>
                    <li><a href="#" class="text-light text-decoration-none">Giới thiệu</a></li>
                    <li><a href="#" class="text-light text-decoration-none">Liên hệ</a></li>
                    <li><a href="#" class="text-light text-decoration-none">Chính sách</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Liên hệ</h5>
                <p><i class="fas fa-map-marker-alt me-2"></i> 123 Đường ABC, Quận XYZ, TP.HCM</p>
                <p><i class="fas fa-phone me-2"></i> 0901 234 567</p>
                <p><i class="fas fa-envelope me-2"></i> info@bookstore.vn</p>
            </div>
        </div>
        <hr class="bg-light opacity-25">
        <div class="text-center">
            <p class="mb-0">&copy; {{ date('Y') }} Book Store. All rights reserved.</p>
        </div>
    </div>
</footer>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


{{-- Để nhận script từ các trang con --}}
@yield('scripts')


</body>
</html>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$title}}</title>
    <link rel="stylesheet" href="{{asset('library/bootstrap.min.css')}}">

    <script src="{{asset('library/jquery.slim.min.js')}}"></script>
    <script src="{{asset('library/popper.min.js')}}"></script>
    <script src="{{asset('library/bootstrap.bundle.min.js')}}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="{{asset('library/jquery-3.7.1.js')}}" ></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 14px;
        
        }

        .container {
            max-width: 1200px; /* Chiều rộng tối đa của nội dung */
            margin: 0 auto; /* Căn giữa nội dung */
            padding: 0 15px;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding:5px 0;
            background-color: #122333;
            max-width:1000px;
            font-weight:bold;
            margin:0 auto;
        }


        .search-bar {
            flex: 1; /* Chiếm không gian còn lại */
            max-width: 500px;
            margin: 0 30px;
            
            position: relative;
        }

        .search-bar input {
            width: 100%;
            padding: 5px 10px;
            border: none;
            border-radius: 20px;
            background-color: white;
        }

        .auth-buttons .btn + .btn {
            margin-left: 10px;
        }
        .nav-item a
        {
            color: #fff!important;
        }
        .nav-item
        {
            padding:0 5px;
        }

        .search-btn
        {
            width:50px; 
            height: 30px;
            color:black; 
            background-color:white;
            border-radius:30px;
            border:none;
            position: absolute;
            right: 0;
        }
    </style>
</head>
<body>
    <header>
        <div style='text-align:center; max-width:1000px; margin:0 auto'>
            <img src="{{asset('images/banner.png')}}" width="1000px">
            <nav class="navbar navbar-light navbar-expand-sm">
                <div class='container-fluid p-0'>
                    <div class='col-6 p-0'>
                        <ul class="navbar-nav">
                            @foreach($categories as $category)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{url('laptop/theloai/'.$category->id)}}">{{$category->ten_danh_muc}}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="search-bar">
                        <form method="post" action="{{url('/timkiem')}}">
                            {{ csrf_field() }}
                            <input type="text" name="keyword" placeholder="Tìm kiếm laptop...">
                            <button class="search-btn"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </form>
                    </div>
                    <div class='col-2 p-0 d-flex'>
                        @auth
                            <div class="dropdown">
                                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                                {{ Auth::user()->name }}
                                </button>
                                <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{route('account')}}">Quản lý</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a class="dropdown-item" onclick="event.preventDefault();
                                                        this.closest('form').submit();">Đăng xuất</a>
                                </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}">
                                <button class='btn btn-sm btn-primary'>Đăng nhập</button>
                            </a>&nbsp;
                            <a href="{{ route('register') }}">
                                <button class='btn btn-sm btn-success'>Đăng ký</button>
                            </a>
                        @endauth
                </div>
            </nav>
        </div>
    </header>
    <main>
        {{$slot}}
    </main>

</body>
</html>
<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class DashboardController extends Controller
{
    public function index()
    {
        // chưa login
        if (!auth()->check()) {
            return redirect('/login');
        }


        // không phải admin hoặc manager
        if (auth()->user()->role != 'Admin' && auth()->user()->role != 'Manager') {
            return redirect('/')
                ->with('error', 'Bạn không có quyền truy cập khu vực này.');
        }


        // giống PHP: redirect vào books
        return redirect()->route('admin.books.index');
    }
}

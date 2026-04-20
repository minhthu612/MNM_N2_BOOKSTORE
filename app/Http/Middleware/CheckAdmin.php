<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Kiểm tra nếu chưa đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để vào trang này!');
        }

        // 2. Kiểm tra Role (Dựa trên logic file cũ: Phải là Admin hoặc Manager)
        $role = Auth::user()->role; 
        if ($role !== 'Admin' && $role !== 'Manager') {
            // Nếu là Customer thì đá về trang chủ
            return redirect('/')->with('error', 'Bạn không có quyền truy cập trang quản trị!');
        }

        return $next($request);
    }
}
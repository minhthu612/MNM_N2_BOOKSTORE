<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 📌 validate giống script gốc
        $request->validate([
            'username' => 'required',
            'email' => 'required|email',
            'fullname' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        // 📌 check trùng
        $exists = User::where('username', $request->username)
                    ->orWhere('email', $request->email)
                    ->exists();

        if ($exists) {
            return back()->withErrors([
                'register' => 'Tên đăng nhập hoặc email đã tồn tại.'
            ])->withInput();
        }

        // 📌 tạo user
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'fullname' => $request->fullname,
            // Sửa $request->PASSWORD thành $request->password (viết thường theo name của input)
            'password_hashed' => Hash::make($request->password), 
            'PASSWORD' => $request->password,
            'role' => 'Customer',
            'status' => 'Active',
        ]);
        // ✅ THÊM DÒNG NÀY: Đăng nhập ngay lập tức cho user vừa tạo
        Auth::login($user);

        // Giờ thì nhảy sang trang chủ với tư cách đã có tài khoản
        return redirect()->route('home')
            ->with('status', 'Đăng ký thành công!');
    }
}

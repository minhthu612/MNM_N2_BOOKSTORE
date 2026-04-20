<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    /**public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }
    */
public function store(Request $request): RedirectResponse
{
    $request->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    // 1. Tìm user theo username hoặc email
    $user = User::where('username', $request->username)
                ->orWhere('email', $request->username)
                ->first();

    if (!$user) {
        return back()->withErrors(['login' => 'Tên đăng nhập hoặc email không tồn tại.']);
    }

    $is_valid = false;

    // 🔐 KHÔNG dùng Hash::check nữa để tránh lỗi BcryptHasher
    // So sánh trực tiếp mật khẩu nhập vào với cột PASSWORD trần trong DB
    if (trim((string)$request->password) === trim((string)$user->PASSWORD)) {
        $is_valid = true;
    } 
    // Dự phòng: Nếu sau này ông có dùng mật khẩu mã hóa ở cột password_hashed
    elseif ($user->password_hashed) {
        try {
            if (Hash::check($request->password, $user->password_hashed)) {
                $is_valid = true;
            }
        } catch (\Exception $e) {
            // bỏ qua nếu hash lỗi
        }
    }

    if (!$is_valid) {
        return back()->withErrors(['login' => 'Mật khẩu không đúng.']);
    }

    // 2. Kiểm tra trạng thái Active
    if ($user->status !== 'Active') {
        return back()->withErrors(['login' => 'Tài khoản bị khóa hoặc chưa kích hoạt.']);
    }

    // ✅ Đăng nhập
    Auth::login($user);
    $request->session()->regenerate();

    // 🔁 Phân quyền
    if (in_array($user->role, ['Admin', 'Manager'])) {
        return redirect('/admin');
    }

    return redirect('/');
}
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
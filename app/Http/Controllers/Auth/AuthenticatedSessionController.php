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
        // 📌 validate giống script gốc
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // 📌 tìm user theo username hoặc email
        $user = User::where('username', $request->username)
                    ->orWhere('email', $request->username)
                    ->first();

        // ❌ không tồn tại
        if (!$user) {
            return back()->withErrors([
                'login' => 'Tên đăng nhập hoặc email không tồn tại.'
            ]);
        }

        $is_valid = false;

        // 🔐 1. password Laravel (bcrypt)
        if (Hash::check($request->password, $user->password_hashed)) {
            $is_valid = true;
        }
        elseif (md5($request->password) === $user->password_hashed) {
            $is_valid = true;

            // upgrade password
            $user->password_hashed = Hash::make($request->password);
            $user->save();
        }

        // ❌ sai mật khẩu
        if (!$is_valid) {
            return back()->withErrors([
                'login' => 'Mật khẩu không đúng.'
            ]);
        }

        // ❌ tài khoản bị khóa
        if ($user->status !== 'Active') {
            return back()->withErrors([
                'login' => 'Tài khoản bị khóa hoặc chưa kích hoạt.'
            ]);
        }

        // ✅ đăng nhập
        Auth::login($user);

        $request->session()->regenerate();

        // 🔁 phân quyền (giống script gốc)
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

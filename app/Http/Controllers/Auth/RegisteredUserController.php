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
            'password_hashed' => Hash::make($request->password),
            'role' => 'Customer',
            'status' => 'Active',
        ]);

        // ❌ KHÔNG auto login (giống script gốc)
        return redirect()->route('login')
            ->with('status', 'Đăng ký thành công! Bạn có thể đăng nhập.');
    }
}

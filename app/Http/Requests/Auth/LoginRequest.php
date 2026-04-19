<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginRequest extends FormRequest
{
    /**
     * Xác thực quyền truy cập request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Quy tắc validation: Khớp với name="username" và name="password" trong HTML.
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'], // Sửa từ 'email' thành 'username'
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Logic đăng nhập chính.
     */
public function authenticate(): void
{
    $this->ensureIsNotRateLimited();

    $loginValue = $this->input('username'); 
    $password = $this->input('password');

    $fieldType = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    // 🔥 SỬA CHỖ NÀY (bỏ Auth::attempt)
    $user = User::where($fieldType, $loginValue)->first();

    if (! $user || ! Hash::check($password, $user->password_hashed)) {
        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => trans('auth.failed'),
        ]);
    }

    // 🔥 LOGIN THỦ CÔNG
    Auth::login($user, $this->boolean('remember'));

    RateLimiter::clear($this->throttleKey());
}

    /**
     * Kiểm tra giới hạn số lần đăng nhập sai (chống Brute force).
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Tạo key định danh cho giới hạn lượt đăng nhập.
     */
    public function throttleKey(): string
    {
        // Khớp với input 'username'
        return Str::transliterate(Str::lower($this->input('username')).'|'.$this->ip());
    }
}
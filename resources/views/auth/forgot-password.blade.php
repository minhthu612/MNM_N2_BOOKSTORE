<x-guest-layout>
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">

        <h2 class="text-xl font-bold mb-4 text-center">
            🔑 Quên mật khẩu
        </h2>

        <p class="text-sm text-gray-600 mb-4 text-center">
            Nhập email để nhận link đặt lại mật khẩu
        </p>

        <!-- Thông báo -->
        @if (session('status'))
            <div class="mb-3 text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <!-- Lỗi -->
        @error('email')
            <div class="mb-3 text-red-600">{{ $message }}</div>
        @enderror

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <input type="email"
                   name="email"
                   class="w-full border p-2 mb-3"
                   placeholder="Nhập email"
                   required>

            <button class="w-full bg-blue-500 text-white p-2">
                Gửi link reset
            </button>
        </form>

        <div class="text-center mt-4 text-sm">
            <a href="{{ route('login') }}">Đăng nhập</a> |
            <a href="{{ route('register') }}">Đăng ký</a>
        </div>

    </div>
</x-guest-layout>
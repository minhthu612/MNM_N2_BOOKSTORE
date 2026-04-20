<x-guest-layout>
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">

        <h2 class="text-xl font-bold mb-4 text-center">
            🔒 Đặt lại mật khẩu
        </h2>

        <!-- Lỗi -->
        @foreach ($errors->all() as $error)
            <div class="text-red-600 mb-2">{{ $error }}</div>
        @endforeach

        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <input type="email" name="email"
                   value="{{ request()->email }}"
                   class="w-full border p-2 mb-3"
                   readonly>

            <input type="password" name="password"
                   placeholder="Mật khẩu mới"
                   class="w-full border p-2 mb-3"
                   required>

            <input type="password" name="password_confirmation"
                   placeholder="Xác nhận mật khẩu"
                   class="w-full border p-2 mb-3"
                   required>

            <button class="w-full bg-green-500 text-white p-2">
                Đặt lại mật khẩu
            </button>
        </form>
    </div>
</x-guest-layout>
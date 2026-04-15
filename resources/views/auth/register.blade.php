<x-guest-layout>

    <!-- Success -->
    @if (session('status'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            {{ session('status') }}
        </div>
    @endif

    <!-- Error -->
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            {{ $errors->first('register') ?? 'Có lỗi xảy ra' }}
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Username -->
        <div>
            <x-input-label value="Tên đăng nhập" />
            <x-text-input name="username" class="block mt-1 w-full" :value="old('username')" required />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label value="Email" />
            <x-text-input name="email" type="email" class="block mt-1 w-full" :value="old('email')" required />
        </div>

        <!-- Fullname -->
        <div class="mt-4">
            <x-input-label value="Họ và tên" />
            <x-text-input name="fullname" class="block mt-1 w-full" :value="old('fullname')" required />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label value="Mật khẩu" />
            <x-text-input type="password" name="password" class="block mt-1 w-full" required />
        </div>

        <!-- Confirm -->
        <div class="mt-4">
            <x-input-label value="Xác nhận mật khẩu" />
            <x-text-input type="password" name="password_confirmation" class="block mt-1 w-full" required />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a href="{{ route('login') }}" class="text-sm text-blue-600">
                Đã có tài khoản?
            </a>

            <x-primary-button class="ms-3">
                Đăng ký
            </x-primary-button>
        </div>
    </form>

</x-guest-layout>
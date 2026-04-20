<x-guest-layout>

    <!-- Message require -->
    @if(request('msg') == 'require')
        <div class="mb-4 p-3 bg-blue-100 text-blue-700 rounded">
            Bạn hãy đăng nhập để tiếp tục mua hàng 📚
        </div>
    @endif

    <!-- Error -->
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            {{ $errors->first('login') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Username / Email -->
        <div>
            <x-input-label for="username" value="Tên đăng nhập hoặc Email" />

            <x-text-input 
                id="username" 
                class="block mt-1 w-full" 
                type="text" 
                name="username" 
                :value="old('username')" 
                required 
                autofocus />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Mật khẩu" />

            <x-text-input 
                id="password" 
                class="block mt-1 w-full"
                type="password"
                name="password"
                required />
        </div>

        <!-- Remember -->
        <div class="block mt-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="remember">
                <span class="ms-2 text-sm text-gray-600">Ghi nhớ đăng nhập</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4 space-x-4">
            <a href="{{ route('register') }}" class="text-sm text-blue-600 hover:underline">
                Bạn chưa có tài khoản?
            </a>

            <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">
                Quên mật khẩu?
            </a>

            <x-primary-button>
                Đăng nhập
            </x-primary-button>
        </div>
    </form>

</x-guest-layout>
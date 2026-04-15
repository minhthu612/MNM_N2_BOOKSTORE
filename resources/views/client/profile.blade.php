@extends('layouts.client')

@section('content')

<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-xl font-bold mb-4">👤 Hồ sơ cá nhân</h2>

    {{-- success --}}
    @if(session('success'))
        <div class="text-green-600 mb-3">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="/profile">
        @csrf

        <div class="mb-3">
            <label>Tên đăng nhập</label>
            <input type="text" value="{{ $user->username }}" class="w-full border p-2" readonly>
        </div>

        <div class="mb-3">
            <label>Họ tên</label>
            <input type="text" name="fullname" value="{{ $user->fullname }}" class="w-full border p-2">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" value="{{ $user->email }}" class="w-full border p-2">
        </div>

        <button class="bg-blue-500 text-white px-4 py-2">
            Cập nhật
        </button>
    </form>

</div>

@endsection
<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // tab giống PHP gốc (?tab=info / password)
        $tab = $request->get('tab', 'info');

        return view('client.profile', compact('user', 'tab'));
    }

    // ===== UPDATE INFO =====
    public function update(Request $request)
    {
        $user = Auth::user();

        // check email trùng (giống code gốc)
        $check = User::where('email', $request->email)
            ->where('id', '!=', $user->id)
            ->first();

        if ($check) {
            return back()->with('error', 'Email này đã có người khác sử dụng rồi!');
        }

        $user->update([
            'name' => $request->fullname,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return redirect('/profile?tab=info')
            ->with('success', 'Đã lưu thay đổi thông tin cá nhân!');
    }

    // ===== CHANGE PASSWORD =====
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        // check mật khẩu cũ
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Mật khẩu hiện tại bạn nhập không đúng');
        }

        // check confirm
        if ($request->new_password != $request->confirm_password) {
            return back()->with('error', 'Hai lần nhập mật khẩu mới không khớp nhau');
        }

        // check độ dài
        if (strlen($request->new_password) < 6) {
            return back()->with('error', 'Mật khẩu mới phải từ 6 ký tự trở lên');
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect('/profile?tab=password')
            ->with('success', 'Chúc mừng! Bạn đã đổi mật khẩu thành công');
    }
}
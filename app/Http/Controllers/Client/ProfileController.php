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
        // Lấy tab từ URL, mặc định là info
        $tab = $request->get('tab', 'info');
        return view('client.profile', compact('user', 'tab'));
    }

    // ===== UPDATE INFO =====
    public function update(Request $request)
    {
        $user = Auth::user();

        // Check email trùng (trừ chính mình ra)
        $check = User::where('email', $request->email)
            ->where('user_id', '!=', $user->user_id)
            ->first();

        if ($check) {
            return back()->with('error', 'Email này đã có người khác sử dụng rồi!');
        }

        // Cập nhật đúng các cột trong DB của ông
        $user->update([
            'fullname' => $request->fullname,
            'email'    => $request->email,
            'phone'    => $request->phone,
        ]);

        return redirect()->to('/profile?tab=info')
            ->with('success', 'Đã lưu thay đổi thông tin cá nhân!');
    }

    // ===== CHANGE PASSWORD =====
    public function changePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check mật khẩu cũ (Laravel dùng hàm getAuthPassword đã định nghĩa ở Model)
        if (    !Hash::check($request->current_password, $user->password_hashed) && $request->current_password !== $user->PASSWORD) {
            return back()->with('error', 'Mật khẩu hiện tại bạn nhập không đúng');
        }

        // Check confirm
        if ($request->new_password != $request->confirm_password) {
            return back()->with('error', 'Hai lần nhập mật khẩu mới không khớp nhau');
        }

        // Check độ dài
        if (strlen($request->new_password) < 6) {
            return back()->with('error', 'Mật khẩu mới phải từ 6 ký tự trở lên');
        }

        // Update cả 2 cột mật khẩu cho chắc ăn như ông muốn
        // Trong hàm changePassword
        $user->password_hashed = Hash::make($request->new_password);
        $user->PASSWORD = $request->new_password;
        $user->save();

        return redirect()->to('/profile?tab=password')
            ->with('success', 'Chúc mừng! Bạn đã đổi mật khẩu thành công');
    }
}
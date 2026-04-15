<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // Hiển thị profile
    public function index()
    {
        $user = Auth::user();
        return view('client.profile', compact('user'));
    }

    // Cập nhật profile
    public function update(Request $request)
    {
        $request->validate([
            'fullname' => 'required',
            'email' => 'required|email',
        ]);

        $user = Auth::user();
        $user->fullname = $request->fullname;
        $user->email = $request->email;
        $user->save();

        return back()->with('success', 'Cập nhật thành công!');
    }
}
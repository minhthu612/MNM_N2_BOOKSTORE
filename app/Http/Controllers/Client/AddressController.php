<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Address;

class AddressController extends Controller
{
    // 1. GIAO DIỆN THÊM MỚI
    public function create()
    {
        return view('client.address.address'); // Trỏ vào resources/views/client/address.blade.php
    }

    // 2. LƯU ĐỊA CHỈ MỚI
    public function store(Request $r)
    {
        $user_id = Auth::id();
        $is_default = $r->has('is_default') ? 1 : 0;

        if ($is_default == 1) {
            DB::update("UPDATE addresses SET is_default = 0 WHERE user_id = ?", [$user_id]);
        }

        DB::insert("INSERT INTO addresses (user_id, fullname, phone, city, district, ward, street, is_default, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())", [
            $user_id, $r->fullname, $r->phone, $r->city, 
            $r->district, $r->ward, $r->street, $is_default
        ]);

        return redirect()->route('checkout.index')->with('success', 'Thêm địa chỉ mới thành công!');
    }

    // 3. GIAO DIỆN SỬA
    public function edit($id)
    {
        $user_id = Auth::id();
        $address = DB::selectOne("SELECT * FROM addresses WHERE address_id = ? AND user_id = ?", [$id, $user_id]);

        if (!$address) return redirect()->route('checkout.index');

        return view('client.address.address', compact('address'));
    }

    // 4. CẬP NHẬT (CÓ CHECK LOGIC 1 GIỜ)
    public function update(Request $r, $id)
    {
        $user_id = Auth::id();

        // 🔥 CHECK LOGIC 1 GIỜ
        $order = DB::table('orders')
            ->where('shipping_address', 'LIKE', "%$id%") 
            ->where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($order) {
            $minutes = now()->diffInMinutes($order->created_at);
            if ($minutes > 60) {
                return back()->with('error', 'Chỉ được chỉnh sửa địa chỉ trong vòng 1 giờ sau khi đặt hàng!');
            }
        }

        DB::update("UPDATE addresses SET fullname = ?, phone = ?, city = ?, district = ?, ward = ?, street = ? 
                    WHERE address_id = ? AND user_id = ?", [
            $r->fullname, $r->phone, $r->city, 
            $r->district, $r->ward, $r->street, $id, $user_id
        ]);

        return redirect()->route('checkout.index')->with('success', 'Cập nhật địa chỉ thành công!');
    }

    // 5. XÓA ĐỊA CHỈ
    public function destroy($id)
    {
        $user_id = Auth::id();

        $is_default = DB::table('addresses')->where('address_id', $id)->value('is_default');
        if ($is_default == 1) {
            return back()->with('error', 'Không thể xóa địa chỉ mặc định!');
        }

        $used = DB::table('orders')->where('shipping_address', 'LIKE', "%$id%")->exists();
        if ($used) {
            return back()->with('error', 'Địa chỉ đã được sử dụng trong đơn hàng, không thể xóa!');
        }

        DB::delete("DELETE FROM addresses WHERE address_id = ? AND user_id = ?", [$id, $user_id]);
        return redirect()->route('checkout.index')->with('success', 'Đã xóa địa chỉ!');
    }

    // 6. ĐẶT MẶC ĐỊNH
    public function setDefault($id)
    {
        $user_id = Auth::id();
        DB::update("UPDATE addresses SET is_default = 0 WHERE user_id = ?", [$user_id]);
        DB::update("UPDATE addresses SET is_default = 1 WHERE address_id = ? AND user_id = ?", [$id, $user_id]);
        return redirect()->route('checkout.index')->with('success', 'Đã đổi địa chỉ mặc định!');
    }
}
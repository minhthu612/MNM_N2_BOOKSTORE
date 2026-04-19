<?php


namespace App\Http\Controllers\Client;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class OrderController extends Controller
{
    // ========================
    // 1. DANH SÁCH ĐƠN HÀNG
    // ========================
    public function index()
    {
        $user_id = Auth::id();


        $orders = DB::select(
            "SELECT *
             FROM orders
             WHERE user_id = ?
             ORDER BY created_at DESC",
            [$user_id]
        );


        return view('client.orders.index', compact('orders'));
    }


    // ========================
    // 2. CHI TIẾT ĐƠN HÀNG
    // ========================
    public function show($id)
    {
        $user_id = Auth::id();


        // Lấy thẳng từ bảng orders, không cần JOIN sang users nữa
        // vì ta đã có fullname và phone trong orders rồi (nếu làm theo Cách 2)
        $order = DB::table('orders')
            ->where('order_id', $id)
            ->where('user_id', $user_id)
            ->first();


        if (!$order) {
            return redirect()->route('orders.index')->with('error', 'Không tìm thấy đơn hàng!');
        }


        $items = DB::select(
            "SELECT oi.*, b.title, b.link_images
            FROM order_items oi
            JOIN books b ON oi.book_id = b.book_id
            WHERE oi.order_id = ?",
            [$id]
        );


        // ========================
        // TIME LIMIT 1 HOUR (FIX CHUẨN)
        // ========================
        $expireAt = Carbon::parse($order->created_at)
        ->startOfSecond()
        ->addHour();
        $remainingSeconds = (int) now()->diffInSeconds($expireAt, false);
        $remainingSeconds = max(0, $remainingSeconds);


        return view('client.orders.detail', compact('order', 'items', 'remainingSeconds'));
    }


    // ========================
    // 3. EDIT ADDRESS FORM
    // ========================
    public function editAddress($id)
    {
        $user_id = Auth::id();


        $order = DB::selectOne(
            "SELECT o.*, u.fullname, u.phone
             FROM orders o
             JOIN users u ON o.user_id = u.user_id
             WHERE o.order_id = ? AND o.user_id = ?",
            [$id, $user_id]
        );


        if (!$order) {
            return redirect()->route('orders.index');
        }


        $expireAt = Carbon::parse($order->created_at)->addHour();
        $remainingSeconds = now()->diffInSeconds($expireAt, false);


        if ($remainingSeconds <= 0) {
            return redirect()
                ->route('orders.show', $id)
                ->with('error', 'Hết thời gian chỉnh sửa địa chỉ!');
        }


        return view('client.orders.edit_address', compact('order', 'remainingSeconds'));
    }


    // ========================
    // 4. UPDATE ADDRESS (ĐÃ FIX TRIỆT ĐỂ)
    // ========================
    public function updateAddress(Request $request, $id)
    {
        $user_id = Auth::id();


        $order = DB::table('orders')
            ->where('order_id', $id)
            ->where('user_id', $user_id)
            ->first();


        if (!$order) {
            return redirect()->route('orders.index')
                ->with('error', 'Không tìm thấy đơn hàng!');
        }


        // Check giới hạn 1 giờ
        $expireAt = \Carbon\Carbon::parse($order->created_at)->addHour();
        if (now()->greaterThan($expireAt)) {
            return redirect()->route('orders.show', $id)
                ->with('error', 'Hết thời gian chỉnh sửa địa chỉ!');
        }


        // 1. Thêm validate cho fullname và phone
        $request->validate([
            'fullname' => 'required|string|max:255',
            'phone'    => 'required|numeric|digits_between:10,11',
            'city'     => 'required',
            'district' => 'required',
            'ward'     => 'required',
            'street'   => 'required',
        ], [
            'phone.numeric' => 'Số điện thoại phải là chữ số.',
            'phone.digits_between' => 'Số điện thoại không hợp lệ (10-11 số).',
        ]);


        // Ghép địa chỉ
        $address = trim(
            $request->street . ', ' .
            $request->ward . ', ' .
            $request->district . ', ' .
            $request->city
        );


        // 2. Cập nhật đồng thời Địa chỉ, Tên và SĐT vào bảng orders
        DB::table('orders')
            ->where('order_id', $id)
            ->update([
                'shipping_address' => $address,
                'fullname'         => $request->fullname, // Cập nhật tên mới
                'phone'            => $request->phone,    // Cập nhật SĐT mới
            ]);


        return redirect()
            ->route('orders.show', $id)
            ->with('success', 'Cập nhật thông tin nhận hàng thành công!');
    }
}

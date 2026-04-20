<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderAddressUpdated;
use App\Mail\OrderCancelled;
use App\Models\Order; // Đảm bảo đã import Model
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

        $order = DB::selectOne(
            "SELECT o.*, u.phone as user_phone, u.fullname as user_fullname
            FROM orders o
            JOIN users u ON o.user_id = u.user_id
            WHERE o.order_id = ? AND o.user_id = ?",
            [$id, $user_id]
        );

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

        $expireAt = Carbon::parse($order->created_at)->addHour();
        $remainingSeconds = max(0, now()->diffInSeconds($expireAt, false));

        return view('client.orders.detail', compact('order', 'items', 'remainingSeconds'));
    }

    // ========================
    // 3. EDIT ADDRESS FORM
    // ========================
    public function editAddress($id)
    {
        $user_id = Auth::id();

        $order = DB::selectOne(
            "SELECT o.*, u.phone as user_phone, u.fullname as user_fullname
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
    // 4. UPDATE ADDRESS
    // ========================
    public function updateAddress(Request $request, $id)
    {
        $user_id = Auth::id();
        $order = DB::table('orders')->where('order_id', $id)->where('user_id', $user_id)->first();

        if (!$order) {
            return redirect()->route('orders.index')->with('error', 'Không tìm thấy đơn hàng!');
        }

        $expireAt = Carbon::parse($order->created_at)->addHour();
        if (now()->gt($expireAt)) {
            return redirect()->route('orders.show', $id)->with('error', 'Đã hết thời gian chỉnh sửa địa chỉ!');
        }

        $full_address = trim($request->street . ', ' . $request->ward . ', ' . $request->district . ', ' . $request->city);

        DB::table('orders')
            ->where('order_id', $id)
            ->update([
                'shipping_address' => $full_address,
                'fullname'         => $request->fullname,
                'phone'            => $request->phone,
                'updated_at'       => now()
            ]);

        try {
            $userEmail = Auth::user()->email;
            if ($userEmail) {
                $updatedOrder = DB::table('orders')->where('order_id', $id)->first();
                Mail::to($userEmail)->send(new OrderAddressUpdated($updatedOrder));
            }
        } catch (\Exception $e) {
            \Log::error("Gửi mail update thất bại: " . $e->getMessage());
        }

        return redirect()->route('orders.show', $id)->with('success', 'Cập nhật thông tin giao hàng thành công!');
    }

    // ========================
    // 5. HỦY ĐƠN HÀNG (BẢN FIX GỬI MAIL)
    // ========================
    public function cancel($id)
    {
        $user_id = Auth::id();

        // Sử dụng Model Order thay vì DB::table để có thể dùng load() quan hệ
        $order = Order::where('order_id', $id)
            ->where('user_id', $user_id)
            ->first();

        if (!$order) {
            return redirect()->route('orders.index')->with('error', 'Không tìm thấy đơn hàng!');
        }

        if ($order->status !== 'pending') {
            return redirect()->route('orders.show', $id)->with('error', 'Không thể hủy đơn hàng ở trạng thái này!');
        }

        // Thực hiện cập nhật trạng thái đơn hàng thành 'cancelled'
        $order->update([
            'status' => 'cancelled',
            'updated_at' => now()
        ]);

        // THỰC HIỆN GỬI MAIL THÔNG BÁO HỦY
        try {
            $userEmail = Auth::user()->email;
            if ($userEmail) {
                // QUAN TRỌNG: Nạp quan hệ để view emails.order không bị lỗi khi lặp qua orderDetails
                $order->load(['orderDetails.book', 'user']);
                
                Mail::to($userEmail)->send(new OrderCancelled($order));
            }
        } catch (\Exception $e) {
            \Log::error("Gửi mail hủy đơn #" . $id . " thất bại: " . $e->getMessage());
        }

        return redirect()->route('orders.show', $id)
            ->with('success', 'Đã hủy đơn hàng thành công và gửi mail xác nhận!');
    }
}
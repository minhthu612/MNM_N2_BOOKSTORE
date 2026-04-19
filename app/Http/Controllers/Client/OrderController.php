<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail; // 👈 THÊM DÒNG NÀY
use App\Mail\OrderAddressUpdated;    // 👈 THÊM DÒNG NÀY (Nhớ tạo file Mail trước)
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

        // Thêm u.phone và u.fullname vào câu SELECT để Blade có thể dùng
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

        $expireAt = \Carbon\Carbon::parse($order->created_at)->addHour();
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
    // 4. UPDATE ADDRESS (CẬP NHẬT ĐỊA CHỈ + GỬI MAIL)
    // ========================

public function updateAddress(Request $request, $id)
{
    $user_id = Auth::id();

    // 1. Kiểm tra đơn hàng chính chủ và lấy ra để check thời gian
    $order = DB::table('orders')->where('order_id', $id)->where('user_id', $user_id)->first();

    if (!$order) {
        return redirect()->route('orders.index')->with('error', 'Không tìm thấy đơn hàng!');
    }

    // 2. Kiểm tra giới hạn 1 giờ (Sửa lại logic so sánh cho chuẩn)
    $expireAt = \Carbon\Carbon::parse($order->created_at)->addHour();
    if (now()->gt($expireAt)) {
        return redirect()->route('orders.show', $id)->with('error', 'Đã hết thời gian chỉnh sửa địa chỉ!');
    }

    // 3. Ghép địa chỉ từ form
    $full_address = trim($request->street . ', ' . $request->ward . ', ' . $request->district . ', ' . $request->city);

    // 4. THỰC HIỆN UPDATE
    $updated = DB::table('orders')
        ->where('order_id', $id)
        ->update([
            'shipping_address' => $full_address,
            'fullname'         => $request->fullname,
            'phone'            => $request->phone,
            'updated_at'       => now()
        ]);

    // ============================================================
    // THÊM: GỬI MAIL THÔNG BÁO (Chèn vào đây trước khi Redirect)
    // ============================================================
    try {
        $userEmail = Auth::user()->email;
        if ($userEmail) {
            // Lấy data đã update để mail hiện đúng 123456789
            $updatedOrder = DB::table('orders')->where('order_id', $id)->first();
            \Mail::to($userEmail)->send(new \App\Mail\OrderAddressUpdated($updatedOrder));
        }
    } catch (\Exception $e) {
        \Log::error("Gửi mail update thất bại: " . $e->getMessage());
    }

    // 5. REDIRECT VỀ TRANG CHI TIẾT (Vẫn là code cũ của bạn đây)
    return redirect()->route('orders.show', $id)->with('success', 'Cập nhật thông tin giao hàng thành công!');
}

    // ========================
    // 5. HỦY ĐƠN HÀNG
    // ========================
    public function cancel($id)
    {
        $user_id = \Illuminate\Support\Facades\Auth::id();

        // Tìm đơn hàng và kiểm tra quyền sở hữu + trạng thái
        $order = \Illuminate\Support\Facades\DB::table('orders')
            ->where('order_id', $id)
            ->where('user_id', $user_id)
            ->first();

        if (!$order) {
            return redirect()->route('orders.index')
                ->with('error', 'Không tìm thấy đơn hàng!');
        }

        // Chỉ cho phép hủy khi đơn hàng đang ở trạng thái 'pending'
        if ($order->status !== 'pending') {
            return redirect()->route('orders.show', $id)
                ->with('error', 'Không thể hủy đơn hàng ở trạng thái này!');
        }

        // Thực hiện cập nhật trạng thái đơn hàng thành 'cancelled'
        \Illuminate\Support\Facades\DB::table('orders')
            ->where('order_id', $id)
            ->update([
                'status' => 'cancelled',
            ]);

        return redirect()->route('orders.show', $id)
            ->with('success', 'Đã hủy đơn hàng thành công!');
    }
}
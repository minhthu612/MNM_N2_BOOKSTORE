<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderCancelled; 
use App\Mail\OrderPlaced; 
use App\Models\Order;

class OrderController extends Controller
{
    // 1. DANH SÁCH ĐƠN HÀNG
    public function index()
    {
        $user_id = Auth::id();
        $orders = DB::select("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC", [$user_id]);

        return view('client.orders.index', compact('orders'));
    }

    // 2. CHI TIẾT ĐƠN HÀNG
    public function show($id)
    {
        $user_id = Auth::id();
        
        $order = DB::selectOne("SELECT o.*, u.fullname, u.phone 
                               FROM orders o 
                               JOIN users u ON o.user_id = u.user_id 
                               WHERE o.order_id = ? AND o.user_id = ? LIMIT 1", [$id, $user_id]);

        if (!$order) {
            return redirect()->route('orders.index')->with('error', 'Không tìm thấy đơn hàng!');
        }

        $items = DB::select("SELECT oi.*, b.title, b.link_images 
                            FROM order_items oi 
                            JOIN books b ON oi.book_id = b.book_id 
                            WHERE oi.order_id = ?", [$id]);

        return view('client.orders.detail', compact('order', 'items'));
    }

    // 3. HỦY ĐƠN HÀNG + GỬI MAIL
    public function cancel($id)
    {
        $user_id = Auth::id();

        // Kiểm tra đơn có phải của mình và đang chờ xử lý không
        $order_check = DB::selectOne("SELECT status FROM orders WHERE order_id = ? AND user_id = ? AND status = 'pending'", [$id, $user_id]);

        if ($order_check) {
            DB::beginTransaction();
            try {
                // Cập nhật trạng thái
                DB::update("UPDATE orders SET status = 'cancelled' WHERE order_id = ?", [$id]);

                // Lấy danh sách item để hoàn kho
                $items = DB::select("SELECT book_id, quantity FROM order_items WHERE order_id = ?", [$id]);

                foreach ($items as $item) {
                    // Cộng lại kho
                    DB::update("UPDATE inventory SET stock = stock + ? WHERE book_id = ?", [$item->quantity, $item->book_id]);
                    // Trừ lại số lượng đã bán
                    DB::update("UPDATE books SET sold_quantity = sold_quantity - ? WHERE book_id = ?", [$item->quantity, $item->book_id]);
                }

                DB::commit();

                // 🔥 GỬI MAIL THÔNG BÁO HỦY ĐƠN
                try {
                    // LẤY DỮ LIỆU MỚI NHẤT KÈM RELATIONSHIP ĐỂ MAIL HIỆN ĐỦ SẢN PHẨM
                    $order = Order::with(['orderDetails.book'])->find($id);
                    
                    if ($order && Auth::user()->email) {
                        // Gọi đúng class OrderCancelled
                        Mail::to(Auth::user()->email)->send(new OrderCancelled($order));
                    }
                } catch (\Exception $mailEx) {
                    \Log::error("Lỗi gửi mail hủy đơn #" . $id . ": " . $mailEx->getMessage());
                }

                return redirect()->route('orders.index')->with('success', "Đã hủy đơn hàng #$id thành công!");
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Có lỗi xảy ra khi hủy đơn.');
            }
        }

        return back()->with('error', 'Đơn hàng này không thể hủy.');
    }
}
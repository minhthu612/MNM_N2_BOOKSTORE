<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    // ================= INDEX =================
    public function index(Request $request)
    {
        $user_id = auth()->id();

        if (!$user_id) {
            return redirect('/login');
        }

        // ================= XỬ LÝ COUPON TỪ DATABASE =================
        $discount_amount = session('discount_amount', 0);
        $applied_coupon = session('applied_coupon', null);
        
        if ($request->has('apply_coupon')) {
            $coupon_code = trim($request->coupon_code);
            
            if (!empty($coupon_code)) {
                // Tìm coupon trong database - SỬA status thành 'active'
                $coupon = DB::table('coupons')
                    ->where('code', $coupon_code)
                    ->where('status', 'active')  // ← SỬA: 'active' thay vì 1
                    ->first();
                
                if ($coupon) {
                    // Xóa thông báo lỗi cũ trước
                    session()->forget('error');
                    
                    // Tính toán discount dựa trên loại (phần trăm hay số tiền)
                    $discount_value = $coupon->discount;
                    
                    // Lấy tổng tiền giỏ hàng để tính nếu là phần trăm
                    $cart = DB::table('cart')->where('user_id', $user_id)->first();
                    if ($cart) {
                        $cart_list_temp = DB::table('cart_items as ci')
                            ->join('books as b', 'ci.book_id', '=', 'b.book_id')
                            ->where('ci.cart_id', $cart->cart_id)
                            ->select('ci.quantity', 'b.price')
                            ->get();
                        
                        $subtotal_temp = 0;
                        foreach ($cart_list_temp as $item) {
                            $subtotal_temp += $item->price * $item->quantity;
                        }
                        
                        // Nếu discount > 100 thì coi là số tiền, nếu <= 100 thì coi là phần trăm
                        if ($discount_value <= 100) {
                            // Giảm theo phần trăm
                            $discount_amount_calc = ($subtotal_temp * $discount_value) / 100;
                        } else {
                            // Giảm theo số tiền
                            $discount_amount_calc = $discount_value;
                        }
                        
                        // Đảm bảo không giảm quá tổng tiền
                        if ($discount_amount_calc > $subtotal_temp) {
                            $discount_amount_calc = $subtotal_temp;
                        }
                    } else {
                        $discount_amount_calc = ($discount_value <= 100) ? 0 : $discount_value;
                    }
                    
                    // Lưu thông tin coupon vào session
                    session([
                        'discount_amount' => $discount_amount_calc,
                        'applied_coupon' => [
                            'code' => $coupon->code,
                            'discount' => $coupon->discount,
                            'discount_type' => ($coupon->discount <= 100) ? 'percent' : 'fixed',
                            'id' => $coupon->coupon_id
                        ]
                    ]);
                    $discount_amount = $discount_amount_calc;
                    $applied_coupon = session('applied_coupon');
                    
                    return redirect()->route('cart.index')->with('success', 'Áp dụng mã giảm giá thành công!');
                } else {
                    // Xóa session coupon và thông báo cũ
                    session()->forget(['discount_amount', 'applied_coupon', 'success']);
                    
                    return redirect()->route('cart.index')->with('error', 'Mã giảm giá không hợp lệ hoặc đã hết hạn!');
                }
            }
        }

        // Giữ nguyên logic lấy giỏ hàng cũ
        $cart = DB::table('cart')->where('user_id', $user_id)->first();

        if (!$cart) {
            return view('client.cart.index', [
                'cart_list' => collect(),
                'discount_amount' => 0,
                'applied_coupon' => null,
                'subtotal' => 0,
                'total' => 0
            ]);
        }

        $cart_list = DB::table('cart_items as ci')
            ->join('books as b', 'ci.book_id', '=', 'b.book_id')
            ->where('ci.cart_id', $cart->cart_id)
            ->select('ci.cart_item_id', 'ci.book_id', 'ci.quantity', 'b.title', 'b.price', 'b.link_images')
            ->get();

        // Tính tổng tiền
        $subtotal = 0;
        foreach ($cart_list as $item) {
            $subtotal += $item->price * $item->quantity;
        }
        
        // Áp dụng giảm giá
        $total = $subtotal - $discount_amount;
        if ($total < 0) $total = 0;

        return view('client.cart.index', compact('cart_list', 'subtotal', 'discount_amount', 'applied_coupon', 'total'));
    }

    // ================= THÊM METHOD XÓA COUPON =================
    public function removeCoupon()
    {
        session()->forget(['discount_amount', 'applied_coupon']);
        return redirect()->route('cart.index')->with('success', 'Đã xóa mã giảm giá!');
    }

    // ================= ADD (SÁCH LẺ) =================
    public function add(Request $request)
    {
        $user_id = auth()->id();
        if (!$user_id) return redirect('/login');

        $book_id = (int)($request->book_id ?? 0);
        $qty_to_add = (int)($request->quantity ?? 1);

        if ($book_id <= 0) return back();

        // Lấy hoặc tạo cart_id
        $cart = DB::table('cart')->where('user_id', $user_id)->first();
        $cart_id = $cart ? $cart->cart_id : DB::table('cart')->insertGetId(['user_id' => $user_id]);

        // Kiểm tra xem sách này có trong giỏ chưa
        $existing = DB::table('cart_items')
            ->where('cart_id', $cart_id)
            ->where('book_id', $book_id)
            ->first();

        if ($existing) {
            DB::table('cart_items')
                ->where('cart_item_id', $existing->cart_item_id)
                ->update(['quantity' => $existing->quantity + $qty_to_add]);
        } else {
            DB::table('cart_items')->insert([
                'cart_id' => $cart_id,
                'book_id' => $book_id,
                'quantity' => $qty_to_add
            ]);
        }

        // Xóa coupon khi thêm sản phẩm mới (tùy chọn)
        session()->forget(['discount_amount', 'applied_coupon']);

        return redirect()->route('cart.index')->with('success', 'Đã thêm vào giỏ hàng!');
    }

    // ================= ADD SET (BỘ SÁCH) =================
    public function addSet(Request $request)
    {
        $user_id = auth()->id();
        $set_id = (int) $request->set_id;
        $set_qty = (int)($request->quantity ?? 1);

        if (!$user_id || !$set_id) return redirect('/');

        $cart = DB::table('cart')->where('user_id', $user_id)->first();
        $cart_id = $cart ? $cart->cart_id : DB::table('cart')->insertGetId(['user_id' => $user_id]);

        $items = DB::table('book_set_items')->where('set_id', $set_id)->get();

        foreach ($items as $item) {
            $existing = DB::table('cart_items')
                ->where('cart_id', $cart_id)
                ->where('book_id', $item->book_id)
                ->first();

            $total_qty_item = $item->quantity * $set_qty;

            if ($existing) {
                DB::table('cart_items')
                    ->where('cart_item_id', $existing->cart_item_id)
                    ->update(['quantity' => $existing->quantity + $total_qty_item]);
            } else {
                DB::table('cart_items')->insert([
                    'cart_id' => $cart_id,
                    'book_id' => $item->book_id,
                    'quantity' => $total_qty_item
                ]);
            }
        }

        // Xóa coupon khi thêm bộ sách (tùy chọn)
        session()->forget(['discount_amount', 'applied_coupon']);

        return redirect()->route('cart.index')->with('success', 'Đã thêm bộ sách vào giỏ!');
    }

    // ================= UPDATE & DELETE =================
    public function update(Request $request)
    {
        $cart_item_id = (int) $request->cart_item_id;
        $quantity = (int) $request->quantity;
        if ($quantity < 1) return back()->with('error', 'Số lượng tối thiểu là 1');

        DB::table('cart_items')->where('cart_item_id', $cart_item_id)->update(['quantity' => $quantity]);
        
        // Xóa coupon khi cập nhật số lượng (tùy chọn)
        session()->forget(['discount_amount', 'applied_coupon']);
        
        return back()->with('success', 'Cập nhật thành công!');
    }

    public function delete(Request $request)
    {
        $id = (int) $request->id;
        DB::table('cart_items')->where('cart_item_id', $id)->delete();
        
        // Xóa coupon khi xóa sản phẩm (tùy chọn)
        session()->forget(['discount_amount', 'applied_coupon']);
        
        return back()->with('success', 'Đã xóa sản phẩm!');
    }
}
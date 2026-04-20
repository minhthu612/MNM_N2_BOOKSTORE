<?php


namespace App\Http\Controllers\Client;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPlaced;
use App\Models\Order;


class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $user_id = Auth::id();
       
        // 1. Lấy dữ liệu giỏ hàng từ DB
        $cart_items = DB::table('cart_items as ci')
            ->join('books as b', 'ci.book_id', '=', 'b.book_id')
            ->join('cart as c', 'ci.cart_id', '=', 'c.cart_id')
            ->where('c.user_id', $user_id)
            ->select('ci.*', 'b.title', 'b.price')
            ->get();


        if ($cart_items->isEmpty()) {
            return redirect()->route('cart.index');
        }


        $subtotal = 0;
        foreach($cart_items as $item) {
            $subtotal += ($item->price * $item->quantity);
        }


        // Logic coupon
        $shipping_fee = 30000;
        $discount_val = session('discount_amount', 0);
        $final_total = max(0, $subtotal - $discount_val + $shipping_fee);


        // 2. Lấy danh sách địa chỉ
        $addresses = DB::table('addresses')->where('user_id', $user_id)->orderBy('is_default', 'desc')->get();
       
        // SỬA Ở ĐÂY: Lấy selected_id từ link "Chọn địa chỉ này"
        $selected_address_id = $request->query('selected_id', 0);
       
        if($selected_address_id == 0) {
            $default = $addresses->where('is_default', 1)->first();
            $selected_address_id = $default ? $default->address_id : ($addresses->first()->address_id ?? 0);
        }


        $current_addr = $addresses->where('address_id', $selected_address_id)->first();
        $selected_address_text = $current_addr ? ($current_addr->street.', '.$current_addr->ward.', '.$current_addr->district.', '.$current_addr->city) : '';


        // SỬA Ở ĐÂY: Trỏ đúng vào resources/views/client/checkout.blade.php (theo route của ông)
        return view('client.checkout.index', compact(
            'cart_items', 'subtotal', 'discount_val', 'shipping_fee',
            'final_total', 'addresses', 'selected_address_id', 'selected_address_text', 'current_addr'
        ));
    }


        public function store(Request $request)
        {
            $user_id = Auth::id();

            // RÀNG BUỘC DỮ LIỆU BẮT BUỘC
            $request->validate([
                'address' => 'required',
                'payment_method' => 'required',
                'fullname' => 'required',
                'phone' => 'required',
            ], [
                'address.required' => 'Vui lòng chọn hoặc thêm địa chỉ nhận hàng.',
                'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
                'fullname.required' => 'Địa chỉ của bạn thiếu họ tên người nhận.',
                'phone.required' => 'Địa chỉ của bạn thiếu số điện thoại.',
            ]);

       
        $cart_items = DB::table('cart_items as ci')
            ->join('books as b', 'ci.book_id', '=', 'b.book_id')
            ->join('cart as c', 'ci.cart_id', '=', 'c.cart_id')
            ->where('c.user_id', $user_id)
            ->select('ci.*', 'b.price')
            ->get();


        if ($cart_items->isEmpty()) return redirect()->route('cart.index');


        $subtotal = 0;
        foreach($cart_items as $item) { $subtotal += ($item->price * $item->quantity); }
       
        $shipping_fee = 30000;
        $discount = session('discount_amount', 0);
        $final_total = max(0, $subtotal - $discount + $shipping_fee);


        DB::beginTransaction();
        try {
            // LƯU ĐƠN HÀNG
            // Trong hàm store của CheckoutController.php
                $order_id = DB::table('orders')->insertGetId([
                    'user_id'          => $user_id,
                    'total_amount'     => $final_total,
                    'status'           => 'pending',
                    'shipping_address' => $request->address,
                   
                    // --- THÊM 2 DÒNG NÀY ---
                    'fullname'         => $request->fullname, // Lấy từ ô nhập Họ tên ở trang Checkout
                    'phone'            => $request->phone,    // Lấy từ ô nhập Số điện thoại ở trang Checkout
                    // -----------------------


                    'payment_method'   => $request->payment_method,
                    'shipping_fee'     => $shipping_fee,
                    'discount_amount'  => $discount,
                    'coupon_code'      => session('coupon_code_used'),
                    'notes'            => $request->notes,
                    'created_at'       => now(),
                ]);
            // Lưu chi tiết & trừ kho
            foreach ($cart_items as $item) {
                DB::table('order_items')->insert([
                    'order_id' => $order_id,
                    'book_id'  => $item->book_id,
                    'quantity' => $item->quantity,
                    'price'    => $item->price
                ]);
                DB::table('inventory')->where('book_id', $item->book_id)->decrement('stock', $item->quantity);
                DB::table('books')->where('book_id', $item->book_id)->increment('sold_quantity', $item->quantity);
            }


            // Xóa giỏ hàng
            $cart = DB::table('cart')->where('user_id', $user_id)->first();
            DB::table('cart_items')->where('cart_id', $cart->cart_id)->delete();
            session()->forget(['discount_amount', 'coupon_code_used']);


            DB::commit();
           
            // 🔥 GỬI EMAIL SAU KHI ĐẶT HÀNG THÀNH CÔNG
            try {
                // Lấy thông tin order kèm theo quan hệ để hiển thị trong email
                $order = Order::with(['orderDetails.book'])->find($order_id);
                if ($order && Auth::user()->email) {
                    Mail::to(Auth::user()->email)->send(new OrderPlaced($order));
                }
            } catch (\Exception $mailEx) {
                // Nếu lỗi mail (do config sai) thì log lại nhưng vẫn cho khách đi tiếp
                \Log::error("Gửi mail thất bại: " . $mailEx->getMessage());
            }


            return redirect()->route('checkout.success', ['id' => $order_id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }


    public function success($id) // $id này lấy từ Route {id}
    {
        // Phải truyền đúng tên biến 'order_id' ra View
        $order_id = $id;
       
        // Đảm bảo file tại resources/views/client/checkout/success.blade.php
        return view('client.checkout.success', compact('order_id'));
    }
}

<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
    // ================= INDEX =================
    public function index(Request $request)
    {
        $search = $request->search ?? '';
        $status = $request->status ?? '';
        $date_from = $request->date_from ?? '';
        $date_to = $request->date_to ?? '';


        $query = DB::table('orders as o')
            ->leftJoin('users as u', 'o.user_id', '=', 'u.user_id');


        if ($search != '') {
            $query->where(function ($q) use ($search) {
                $q->where('o.order_id', 'like', "%$search%")
                  ->orWhere('u.fullname', 'like', "%$search%")
                  ->orWhere('u.username', 'like', "%$search%")
                  ->orWhere('o.tracking_number', 'like', "%$search%");
            });
        }


        if ($status != '') {
            $query->where('o.status', $status);
        }


        if ($date_from != '') {
            $query->whereDate('o.created_at', '>=', $date_from);
        }


        if ($date_to != '') {
            $query->whereDate('o.created_at', '<=', $date_to);
        }


        $orders = $query
            ->select(
                'o.*',
                'u.username',
                'u.fullname',
                DB::raw('(SELECT SUM(quantity) FROM order_items WHERE order_id = o.order_id) as total_qty')
            )
            ->orderBy('o.created_at', 'asc')
            ->paginate(10);


        $stats = DB::table('orders')
            ->selectRaw("
                COUNT(*) as total_orders,
                SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending_count,
                SUM(total_amount) as total_revenue
            ")
            ->first();


        return view('admin.orders.index', compact('orders', 'stats', 'search', 'status', 'date_from', 'date_to'));
    }


    // ================= DETAIL =================
    public function show($id)
    {
        $order = DB::table('orders as o')
            ->leftJoin('users as u', 'o.user_id', '=', 'u.user_id')
            ->where('o.order_id', $id)
            ->select('o.*', 'u.username', 'u.fullname', 'u.email', 'u.phone')
            ->first();


        if (!$order) {
            return redirect()->route('admin.orders.index')->with('error', 'Không tìm thấy đơn hàng');
        }


        $items = DB::table('order_items as oi')
            ->leftJoin('books as b', 'oi.book_id', '=', 'b.book_id')
            ->where('oi.order_id', $id)
            ->select('oi.*', 'b.title', 'b.link_images')
            ->get();


        return view('admin.orders.detail', compact('order', 'items'));
    }


    // ================= UPDATE STATUS =================
   
public function updateStatus(Request $request, $id)
{
    $order = DB::table('orders')->where('order_id', $id)->first();


    if (!$order) {
        return redirect()->route('admin.orders.index')
            ->with('error', 'Đơn hàng không tồn tại');
    }


    $action = $request->get('action');


    // ================= XỬ LÝ ACTION NHANH (GET) =================
    if ($action) {


        // Nếu đã xác nhận
        if ($request->has('confirmed')) {


            $new_status = '';
            $data = [];


            if ($action == 'ship') {
                $new_status = 'shipped';
            } elseif ($action == 'deliver') {
                $new_status = 'delivered';
                $data['delivered_at'] = now();
            } elseif ($action == 'cancel') {
                $new_status = 'cancelled';
            }


            if ($new_status != '') {
                $data['status'] = $new_status;


                DB::table('orders')
                    ->where('order_id', $id)
                    ->update($data);


                return redirect()->route('admin.orders.show', $id)
                    ->with('success', 'Cập nhật trạng thái thành công');
            }
        }


        // HIỂN THỊ TRANG XÁC NHẬN
        return view('admin.orders.update_status', [
            'order' => $order,
            'action' => $action
        ]);
    }


    // ================= XỬ LÝ FORM (POST) =================
    if ($request->isMethod('post')) {
        $status = $request->status;
        $track = $request->tracking_number;
        $notes = $request->notes;


        $data = [
            'status' => $status
        ];


        if ($status == 'shipped') {
            $data['tracking_number'] = $track;
        }


        if ($status == 'delivered') {
            $data['delivered_at'] = now();
        }


        if ($notes) {
            $time = now()->format('d/m/Y H:i');
            $data['notes'] = $order->notes . "\n[" . $time . " ADMIN]: " . $notes;
        }


        DB::table('orders')
            ->where('order_id', $id)
            ->update($data);


        return redirect()->route('admin.orders.show', $id)
            ->with('success', 'Đã cập nhật đơn hàng thành công');
    }


    // ================= LOAD VIEW =================
    return view('admin.orders.update_status', [
        'order' => $order,
        'action' => null
    ]);
}
}

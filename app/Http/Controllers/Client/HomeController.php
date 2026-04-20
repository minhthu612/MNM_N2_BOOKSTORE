<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // ===== LẤY PARAM =====
        $category_id = $request->category;
        $view_type   = $request->view;
        $set_id      = $request->set_id;

        $category_name = "Tất cả sách";

        // ===== XỬ LÝ TITLE =====
        if ($view_type == 'best_seller') {
            $category_name = "Sách bán chạy";
        } elseif ($view_type == 'new') {
            $category_name = "Sách mới";
        } elseif ($category_id && $category_id != 'all') {
            $cat = DB::table('categories')
                    ->where('category_id', $category_id)
                    ->first();

            if ($cat) {
                $category_name = $cat->category_name;
            }
        }

        if ($set_id) {
            $set = DB::table('book_sets')
                    ->where('set_id', $set_id)
                    ->first();

            if ($set) {
                $category_name = $set->name;
            }
        }

        // Lọc danh sách Book Sets (Bộ sách) ở sidebar chẳng hạn
        // Chỉ lấy những bộ không bị DELETED và còn hàng
        $bookSets = DB::table('book_sets')
                    ->where('stock_status', '!=', 'DELETED')
                    ->where('stock_status', '!=', 'OUT_OF_STOCK') // Lọc theo status bạn đưa trong DB
                    ->orderBy('set_id')
                    ->get();

        // ===== QUERY =====
        if ($set_id) {
            // Trường hợp xem theo Bộ Sách
            $books = DB::table('book_sets')
                ->where('set_id', $set_id)
                ->where('stock_status', '!=', 'OUT_OF_STOCK') 
                ->selectRaw("set_id as book_id, name as title, link_images, price, discount, sold_quantity, 'set' as loai")
                ->paginate(12)
                ->appends($request->query());
        } else {
            // Trường hợp xem Sách lẻ (Join với bảng inventory để biết stock)
            $query = DB::table('books')
                ->join('inventory', 'books.book_id', '=', 'inventory.book_id')
                ->select('books.*', 'inventory.stock', 'inventory.stock_status');

            // 👉 ĐIỀU KIỆN QUAN TRỌNG: Ẩn sách hết hàng hoặc bị ẩn
            $query->where('inventory.stock', '>', 0)
                  ->where('inventory.stock_status', '!=', 'HIDDEN')
                  ->where('inventory.stock_status', '!=', 'OUT_OF_STOCK');

            if ($category_id && $category_id != 'all') {
                $query->where('books.category_id', $category_id);
            }

            if ($view_type == 'new') {
                $query->orderBy('books.created_at', 'desc');
            } else {
                $query->orderBy('books.sold_quantity', 'desc');
            }

            $books = $query->paginate(12)->appends($request->query());
        }

        // ==========================================
        // 👉 THÊM: LẤY DANH SÁCH ID ĐÃ YÊU THÍCH
        // ==========================================
        $wishlist_ids = [];
        if (Auth::check()) {
            $wishlist_ids = DB::table('wishlist')
                ->where('user_id', Auth::id())
                ->pluck('book_id')
                ->toArray();
        }

        return view('client.home', compact(
            'books',
            'category_name',
            'category_id',
            'set_id',
            'bookSets',
            'wishlist_ids' // 👈 Truyền mảng này qua View
        ));
    }
}
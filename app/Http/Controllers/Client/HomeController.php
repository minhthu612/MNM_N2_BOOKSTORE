<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller; 

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

        // 👉 THÊM: tên bộ sách
        if ($set_id) {
            $set = DB::table('book_sets')
                    ->where('set_id', $set_id)
                    ->first();

            if ($set) {
                $category_name = $set->name;
            }
        }

        // 👉 THÊM: lấy list bộ SGK (dropdown)
        $bookSets = DB::table('book_sets')
                    ->where('stock_status', '!=', 'DELETED')
                    ->orderBy('set_id')
                    ->get();

        // ===== QUERY =====
        if ($set_id) {
            $books = DB::table('book_sets')
                ->where('set_id', $set_id)
                ->selectRaw("set_id as book_id, name as title, link_images, price, discount, sold_quantity, 'set' as loai")
                ->paginate(12)
                ->appends($request->query()); // 👈 giữ query khi phân trang
        } else {
            $query = DB::table('books');

            if ($category_id && $category_id != 'all') {
                $query->where('category_id', $category_id);
            }

            // ===== SORT =====
            if ($view_type == 'new') {
                $query->orderBy('created_at', 'desc');
            } else {
                $query->orderBy('sold_quantity', 'desc');
            }

            $books = $query->paginate(12)
                           ->appends($request->query()); // 👈 giữ query
        }

        return view('client.home', compact(
            'books',
            'category_name',
            'category_id',
            'set_id',
            'bookSets'
        ));
    }
}
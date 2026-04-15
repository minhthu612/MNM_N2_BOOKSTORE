<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // lấy filter
        $category_id = $request->category;
        $view_type   = $request->view;
        $set_id      = $request->set_id;

        $category_name = "Tất cả sách";

        // tên danh mục
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

        // query chính
        if ($set_id) {
            $books = DB::table('book_sets')
                ->where('set_id', $set_id)
                ->paginate(18);
        } else {

            $query = DB::table('books');

            if ($category_id && $category_id != 'all') {
                $query->where('category_id', $category_id);
            }

            if ($view_type == 'new') {
                $query->orderBy('created_at', 'desc');
            } else {
                $query->orderBy('sold_quantity', 'desc');
            }

            $books = $query->paginate(18);
        }

        return view('client.home', compact(
            'books',
            'category_name'
        ));
    }
}
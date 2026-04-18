<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    // 1. TRANG DANH SÁCH YÊU THÍCH
    public function index()
    {
        $user_id = Auth::id();

        // Query trần LEFT JOIN giống hệt bản cũ của ông
        $sql = "SELECT w.wishlist_id, 
                       b.book_id as b_id, b.title as b_title, b.link_images as b_img, b.price as b_price, b.discount as b_disc,
                       bs.set_id as s_id, bs.name as s_title, bs.link_images as s_img, bs.price as s_price, bs.discount as s_disc
                FROM wishlist w
                LEFT JOIN books b ON w.book_id = b.book_id
                LEFT JOIN book_sets bs ON w.book_id = bs.set_id
                WHERE w.user_id = ?
                ORDER BY w.wishlist_id DESC";

        $list_fav = DB::select($sql, [$user_id]);

        return view('client.wishlist.index', compact('list_fav'));
    }

    // 2. THÊM VÀO YÊU THÍCH
    public function add(Request $request)
    {
        $user_id = Auth::id();
        $book_id = $request->query('book_id');

        if (!$book_id) return back();

        // Kiểm tra xem đã có chưa (Query trần)
        $exists = DB::selectOne("SELECT wishlist_id FROM wishlist WHERE user_id = ? AND book_id = ?", [$user_id, $book_id]);

        if (!$exists) {
            DB::insert("INSERT INTO wishlist (user_id, book_id) VALUES (?, ?)", [$user_id, $book_id]);
            return back()->with('success', 'Đã thêm vào danh sách yêu thích!');
        }

        return back()->with('info', 'Sách này đã có trong danh sách yêu thích.');
    }

    // 3. XÓA KHỎI YÊU THÍCH
    public function destroy($id)
    {
        $user_id = Auth::id();
        
        // Xóa trần kèm check user_id cho an toàn
        DB::delete("DELETE FROM wishlist WHERE wishlist_id = ? AND user_id = ?", [$id, $user_id]);

        return redirect()->route('wishlist.index')->with('success', 'Đã bỏ sản phẩm ra khỏi danh sách yêu thích!');
    }
}
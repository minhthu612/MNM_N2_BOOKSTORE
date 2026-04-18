<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\BookSet; // ✅ thêm model nhưng KHÔNG phá code cũ

class BookSetController extends Controller
{
    public function detail($id)
    {
        if (!$id) {
            return redirect('/');
        }

        // ===== VIEW (ghi lượt xem) =====
        DB::table('book_views')->insert([
            'user_id' => Auth::id(),
            'book_id' => $id, // ⚠️ đang dùng chung với book
            'viewed_at' => now()
        ]);

        // ===== LẤY SET (dùng model nhẹ) =====
        $set = BookSet::where('set_id', $id)->first();

        if (!$set) {
            return redirect('/');
        }

        // ===== LẤY STOCK (giữ logic cũ) =====
        $stock = DB::table('inventory')
            ->where('book_id', $id)
            ->value('stock');

        $set->stock = $stock;

        // ===== COUNT VIEW =====
        $total_views = DB::table('book_views')
            ->where('book_id', $id)
            ->count();

        // ===== LIST ITEM =====
        $list_items = DB::table('books as b')
            ->join('book_set_items as bsi', 'b.book_id', '=', 'bsi.book_id')
            ->where('bsi.set_id', $id)
            ->get();

        // ===== RATING (THÊM để giống book detail) =====
        $rating = DB::table('reviews')
            ->where('book_id', $id) // ⚠️ dùng chung với book
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total_reviews')
            ->first();

        $avg_rating = round($rating->avg_rating ?? 0, 1);
        $total_reviews = $rating->total_reviews ?? 0;

        // ===== REVIEWS =====
        $list_reviews = DB::table('reviews as r')
            ->join('users as u', 'r.user_id', '=', 'u.user_id')
            ->where('r.book_id', $id)
            ->orderBy('r.created_at', 'desc')
            ->select('r.*', 'u.fullname', 'u.username')
            ->get();

        return view('client.book_sets.detail', compact(
            'set',
            'total_views',
            'list_items',
            'list_reviews',
            'avg_rating',      // ✅ thêm
            'total_reviews'    // ✅ thêm
        ));
    }
}
<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    // =========================
    // 1. LIST / CATEGORY
    // =========================
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $books = $query->orderBy('book_id', 'desc')->paginate(12);

        // Lấy danh sách ID đã yêu thích để hiện tim đậm/rỗng ở trang danh sách
        $wishlist_ids = [];
        if (Auth::check()) {
            $wishlist_ids = DB::table('wishlist')
                ->where('user_id', Auth::id())
                ->pluck('book_id')
                ->toArray();
        }

        return view('client.books.index', compact('books', 'wishlist_ids'));
    }

    // =========================
    // 2. DETAIL BOOK
    // =========================
    public function show($id)
    {
        $book = DB::table('books as b')
            ->leftJoin('categories as c', 'b.category_id', '=', 'c.category_id')
            ->leftJoin('inventory as i', 'b.book_id', '=', 'i.book_id')
            ->where('b.book_id', $id)
            ->select('b.*', 'c.category_name', 'i.stock')
            ->first();

        if (!$book) {
            abort(404);
        }

        // VIEW COUNT
        DB::table('book_views')->insert([
            'user_id' => auth()->id() ?? null,
            'book_id' => $id,
            'viewed_at' => now()
        ]);

        $total_views = DB::table('book_views')->where('book_id', $id)->count();

        // RATING
        $rating = DB::table('reviews')
            ->where('book_id', $id)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total_reviews')
            ->first();

        $avg_rating = round($rating->avg_rating ?? 0, 1);
        $total_reviews = $rating->total_reviews ?? 0;

        // LIST REVIEWS
        $list_reviews = DB::table('reviews as r')
            ->join('users as u', 'r.user_id', '=', 'u.user_id')
            ->where('r.book_id', $id)
            ->select('r.*', 'u.fullname', 'u.username')
            ->orderBy('r.created_at', 'desc')
            ->get();

        // WISHLIST CHECK
        $wishlist_ids = [];
        if (Auth::check()) {
            $wishlist_ids = DB::table('wishlist')
                ->where('user_id', Auth::id())
                ->pluck('book_id')
                ->toArray();
        }

        return view('client.books.detail', compact(
            'book',
            'avg_rating',
            'total_reviews',
            'total_views',
            'list_reviews',
            'wishlist_ids'
        ));
    }
}
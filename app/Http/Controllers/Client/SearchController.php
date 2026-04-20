<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->q;

        if (!$keyword) {
            return view('client.search', [
                'books' => collect(),
                'keyword' => '',
                'wishlist_ids' => []
            ]);
        }

        $books = Book::leftJoin('inventory as i', 'books.book_id', '=', 'i.book_id')
            ->where(function($query) use ($keyword) {
                $query->where('books.title', 'like', "%{$keyword}%")
                      ->orWhere('books.author', 'like', "%{$keyword}%");
            })
            ->select('books.*', 'i.stock')
            ->orderBy('books.sold_quantity', 'desc')
            ->paginate(12)
            ->appends(['q' => $keyword]); // Giữ lại từ khóa khi chuyển trang phân trang

        // Lấy danh sách ID đã yêu thích để hiện tim đậm/rỗng
        $wishlist_ids = [];
        if (Auth::check()) {
            $wishlist_ids = DB::table('wishlist')
                ->where('user_id', Auth::id())
                ->pluck('book_id')
                ->toArray();
        }

        return view('client.search', compact('books', 'keyword', 'wishlist_ids'));
    }
}
<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;

class SearchController extends Controller
{
public function index(Request $request)
{
    // ✅ LẤY ĐÚNG BIẾN q
    $keyword = $request->q;

    // nếu rỗng thì trả về view luôn
    if (!$keyword) {
        return view('client.search', [
            'books' => collect(), // rỗng
            'keyword' => ''
        ]);
    }

    $books = Book::leftJoin('inventory as i', 'books.book_id', '=', 'i.book_id')
        ->where(function($query) use ($keyword) {
            $query->where('books.title', 'like', "%{$keyword}%")
                  ->orWhere('books.author', 'like', "%{$keyword}%");
        })
        ->select('books.*', 'i.stock')
        ->orderBy('books.sold_quantity', 'desc')
        ->paginate(12);

    return view('client.search', compact('books', 'keyword'));
}
}
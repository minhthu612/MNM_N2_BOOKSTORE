<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;

class SearchController extends Controller
{
    public function index(Request $request)
    {
    $keyword = $request->keyword; // 👈 PHẢI LÀ keyword

    $books = Book::where('title', 'like', "%$keyword%")
        ->orWhere('author', 'like', "%$keyword%")
        ->paginate(12);


        $books = Book::leftJoin('inventory as i', 'books.book_id', '=', 'i.book_id')
            ->where('books.title', 'like', "%{$keyword}%")
            ->orWhere('books.author', 'like', "%{$keyword}%")
            ->select('books.*', 'i.stock')
            ->orderBy('books.sold_quantity', 'desc')
            ->paginate(12);

        return view('client.search', compact('books', 'keyword'));
    }
}
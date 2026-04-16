<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Book;

class HomeController extends Controller
{
    public function index()
    {
        $books = Book::orderBy('book_id', 'desc')->paginate(12);

        return view('client.home', [
            'books' => $books,
            'category_name' => 'Trang chủ'
        ]);
    }
}
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\BookController; 
use App\Http\Controllers\Client\SearchController;

// ================= HOME =================
Route::get('/', [HomeController::class, 'index'])->name('home');

// ================= AUTH =================
require __DIR__.'/auth.php';

// ================= PROFILE (CẦN LOGIN) =================
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'index']);
    Route::post('/profile/update', [ProfileController::class, 'update']);
    Route::post('/profile/password', [ProfileController::class, 'changePassword']);
});

// ================= BOOK (PUBLIC - KHÔNG LOGIN) =================
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{id}', [BookController::class, 'show']);



Route::get('/search', [SearchController::class, 'index'])->name('search');
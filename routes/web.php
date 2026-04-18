<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\BookController;
use App\Http\Controllers\Client\BookSetController; 
use App\Http\Controllers\Client\SearchController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\OrderController;
use App\Http\Controllers\Client\AddressController;
use App\Http\Controllers\Client\WishlistController;


// ================= HOME =================
Route::get('/', [HomeController::class, 'index'])->name('home');

// ================= AUTH =================
require __DIR__.'/auth.php';

// ================= PROFILE (CẦN LOGIN) =================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update']);
    Route::post('/profile/password', [ProfileController::class, 'changePassword']);
});

// ================= BOOK (PUBLIC - KHÔNG LOGIN) =================
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{id}', [BookController::class, 'show']);

// ================= BOOK_SETS (PUBLIC - KHÔNG LOGIN) =================
Route::get('/book-set/{id}', [BookSetController::class, 'detail']);

// ================= SEARCH (PUBLIC - KHÔNG LOGIN) =================
Route::get('/search', [SearchController::class, 'index']);


// ================= CART =================
Route::middleware('auth')->prefix('client/cart')->group(function () {

    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::get('/remove-coupon', [CartController::class, 'removeCoupon'])->name('cart.remove_coupon'); // 👈 THÊM DÒNG NÀY
    Route::post('/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/delete', [CartController::class, 'delete'])->name('cart.delete');
    Route::post('/add-set', [CartController::class, 'addSet'])->name('cart.addSet');
});

// ================= CHECKOUT =================
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{id}', [CheckoutController::class, 'success'])->name('checkout.success'); 
});

// ================= ORDER =================
Route::middleware('auth')->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

// ================= ADDRESS =================
Route::middleware('auth')->prefix('address')->group(function () {
    Route::get('/create', [AddressController::class, 'create'])->name('address.create');
    Route::post('/store', [AddressController::class, 'store'])->name('address.store');
    Route::get('/edit/{id}', [AddressController::class, 'edit'])->name('address.edit');
    Route::post('/update/{id}', [AddressController::class, 'update'])->name('address.update');
    Route::post('/delete/{id}', [AddressController::class, 'destroy'])->name('address.delete');
    Route::post('/set-default/{id}', [AddressController::class, 'setDefault'])->name('address.default');
});

// ================= WISHLIST =================
Route::middleware('auth')->prefix('wishlist')->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::get('/add', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::post('/delete/{id}', [WishlistController::class, 'destroy'])->name('wishlist.delete');
});
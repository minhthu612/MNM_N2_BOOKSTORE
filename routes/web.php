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
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BookSetController as AdminBookSetController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\InventoryController;


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


//ADMIN
// ================= ADMIN DASHBOARD =================
Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ✅ ĐÚNG: chỉ 'books'
    Route::prefix('books')->name('books.')->group(function () {


        Route::get('/', [AdminBookController::class, 'index'])->name('index');


        Route::get('/create', [AdminBookController::class, 'create'])->name('create');
        Route::post('/create', [AdminBookController::class, 'store'])->name('store');


        Route::get('/edit/{id}', [AdminBookController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}', [AdminBookController::class, 'update'])->name('update');


        Route::get('/delete/{id}', [AdminBookController::class, 'delete'])->name('delete');
        Route::post('/delete/{id}', [AdminBookController::class, 'destroy'])->name('destroy');
    });


});


// ================= ADMIN BOOK SETS =================
Route::prefix('admin/book_sets')->name('admin.book_sets.')->group(function () {

    Route::get('/', [AdminBookSetController::class, 'index'])->name('index');

    Route::get('/create', [AdminBookSetController::class, 'create'])->name('create');
    Route::post('/store', [AdminBookSetController::class, 'store'])->name('store');

    Route::get('/edit/{id}', [AdminBookSetController::class, 'edit'])->name('edit');
    Route::post('/update/{id}', [AdminBookSetController::class, 'update'])->name('update');

    Route::get('/delete/{id}', [AdminBookSetController::class, 'delete'])->name('delete');
    Route::post('/destroy/{id}', [AdminBookSetController::class, 'destroy'])->name('destroy');

    Route::get('/items/{id}', [AdminBookSetController::class, 'items'])->name('items');
    Route::post('/items/{id}', [AdminBookSetController::class, 'itemsAction'])->name('items.action');
});


// ================= ADMIN CATEGORIES =================
Route::prefix('admin/categories')->name('admin.categories.')->group(function () {


    // Danh sách
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::post('/', [CategoryController::class, 'index']); // xử lý delete_selected


    // Thêm
    Route::get('/create', [CategoryController::class, 'create'])->name('create');
    Route::post('/create', [CategoryController::class, 'store'])->name('store');


    // Sửa
    Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('edit');
    Route::post('/edit/{id}', [CategoryController::class, 'update'])->name('update');


    // Xóa (2 bước giống PHP)
    Route::get('/delete/{id}', [CategoryController::class, 'delete'])->name('delete');
    Route::post('/delete/{id}', [CategoryController::class, 'destroy'])->name('destroy');


});


// ================= ADMIN ORDERS =================
Route::prefix('admin/orders')->name('admin.orders.')->group(function () {

    Route::get('/', [AdminOrderController::class, 'index'])->name('index');

    Route::get('/{id}', [AdminOrderController::class, 'show'])->name('show');

    Route::match(['get','post'], '/update/{id}', [AdminOrderController::class, 'updateStatus'])
        ->name('update');
});


// ================= ADMIN USERS =================
Route::prefix('admin/users')->name('admin.users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');


    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/create', [UserController::class, 'store'])->name('store');


    Route::get('/detail/{id}', [UserController::class, 'show'])->name('show');


    Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
    Route::post('/edit/{id}', [UserController::class, 'update'])->name('update');


    Route::get('/delete/{id}', [UserController::class, 'delete'])->name('delete');
    Route::post('/delete/{id}', [UserController::class, 'destroy'])->name('destroy');


    Route::get('/activate/{id}', [UserController::class, 'activate'])->name('activate');
    Route::get('/deactivate/{id}', [UserController::class, 'deactivate'])->name('deactivate');


   
    Route::post('/reset-password/{id}', [UserController::class, 'updatePassword'])->name('updatePassword');
    Route::get('/reset-password/{id}', [UserController::class, 'resetPassword'])->name('reset_password');
});


// ================= ADMIN REVIEWS =================
Route::prefix('admin/reviews')->name('admin.reviews.')->group(function () {


    Route::get('/', [ReviewController::class, 'index'])->name('index');


    Route::get('/edit/{id}', [ReviewController::class, 'edit'])->name('edit');
    Route::post('/edit/{id}', [ReviewController::class, 'update'])->name('update');


    Route::get('/delete/{id}', [ReviewController::class, 'delete'])->name('delete');
    Route::post('/delete/{id}', [ReviewController::class, 'destroy'])->name('destroy');


});


// ================= ADMIN INVENTORY =================
Route::prefix('admin/inventory')->name('admin.inventory.')->group(function () {


    Route::get('/', [InventoryController::class,'index'])->name('index');


    Route::get('/history', [InventoryController::class,'history'])->name('history');


    Route::get('/edit/{id}', [InventoryController::class, 'edit'])
            ->name('edit');
    Route::get('/update/{id}', [InventoryController::class, 'edit'])
            ->name('updateForm');


    Route::post('/update/{id}', [InventoryController::class, 'update'])
            ->name('update');


});

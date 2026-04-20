<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\{
    HomeController, ProfileController, BookController, 
    BookSetController, SearchController, CartController, 
    CheckoutController, OrderController, AddressController, WishlistController
};
use App\Http\Controllers\Admin\{
    BookController as AdminBookController, DashboardController, 
    BookSetController as AdminBookSetController, CategoryController, 
    OrderController as AdminOrderController, UserController, 
    ReviewController, InventoryController
};

// ================= CLIENT ROUTES =================

Route::get('/', [HomeController::class, 'index'])->name('home');

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update']);
    Route::post('/profile/password', [ProfileController::class, 'changePassword']);

    // Cart
    Route::prefix('client/cart')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('cart.index');
        Route::get('/remove-coupon', [CartController::class, 'removeCoupon'])->name('cart.remove_coupon');
        Route::post('/add', [CartController::class, 'add'])->name('cart.add');
        Route::post('/update', [CartController::class, 'update'])->name('cart.update');
        Route::post('/delete', [CartController::class, 'delete'])->name('cart.delete');
        Route::post('/add-set', [CartController::class, 'addSet'])->name('cart.addSet');
    });

    // Checkout & Order
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{id}', [CheckoutController::class, 'success'])->name('checkout.success'); 

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/orders/{id}/edit-address', [OrderController::class, 'editAddress'])->name('orders.edit_address');
    Route::post('/orders/{id}/update-address', [OrderController::class, 'updateAddress'])->name('orders.update_address');

    // Address
    Route::prefix('address')->group(function () {
        Route::get('/create', [AddressController::class, 'create'])->name('address.create');
        Route::post('/store', [AddressController::class, 'store'])->name('address.store');
        Route::get('/edit/{id}', [AddressController::class, 'edit'])->name('address.edit');
        Route::post('/update/{id}', [AddressController::class, 'update'])->name('address.update');
        Route::post('/delete/{id}', [AddressController::class, 'destroy'])->name('address.delete');
        Route::post('/set-default/{id}', [AddressController::class, 'setDefault'])->name('address.default');
    });

    // Wishlist
    Route::prefix('wishlist')->group(function () {
        Route::get('/', [WishlistController::class, 'index'])->name('wishlist.index');
        Route::get('/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle'); 
        Route::post('/delete/{id}', [WishlistController::class, 'destroy'])->name('wishlist.delete');
    });
});

// Public Book Routes
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{id}', [BookController::class, 'show']);
Route::get('/book-set/{id}', [BookSetController::class, 'detail']);
Route::get('/search', [SearchController::class, 'index']);


// ================= ADMIN ROUTES =================
// Tui bọc thêm Middleware CheckAdmin ở đây để bảo mật nè
Route::middleware([\App\Http\Middleware\CheckAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Books
    Route::prefix('books')->name('books.')->group(function () {
        Route::get('/', [AdminBookController::class, 'index'])->name('index');
        Route::get('/create', [AdminBookController::class, 'create'])->name('create');
        Route::post('/create', [AdminBookController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [AdminBookController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}', [AdminBookController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [AdminBookController::class, 'delete'])->name('delete');
        Route::post('/delete/{id}', [AdminBookController::class, 'destroy'])->name('destroy');
    });

    // Book Sets
    Route::prefix('book_sets')->name('book_sets.')->group(function () {
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

    // Categories
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::post('/', [CategoryController::class, 'index']); 
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/create', [CategoryController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}', [CategoryController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [CategoryController::class, 'delete'])->name('delete');
        Route::post('/delete/{id}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminOrderController::class, 'show'])->name('show');
        Route::match(['get','post'], '/update/{id}', [AdminOrderController::class, 'updateStatus'])->name('update');
    });

    // Users
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/create', [UserController::class, 'store'])->name('store');
        Route::get('/detail/{id}', [UserController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}', [UserController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [UserController::class, 'delete'])->name('delete');
        Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('/activate/{id}', [UserController::class, 'activate'])->name('activate');
        Route::get('/deactivate/{id}', [UserController::class, 'deactivate'])->name('deactivate');
        Route::match(['get', 'post'], '/reset-password/{id}', [UserController::class, 'resetPassword'])->name('resetPassword');
    });

    // Reviews
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [ReviewController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [ReviewController::class, 'edit'])->name('edit');
        Route::post('/edit/{id}', [ReviewController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [ReviewController::class, 'delete'])->name('delete');
        Route::post('/delete/{id}', [ReviewController::class, 'destroy'])->name('destroy');
    });

    // Inventory
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/history', [InventoryController::class, 'history'])->name('history');
        Route::get('/edit/{id}', [InventoryController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [InventoryController::class, 'update'])->name('update');
    });
});
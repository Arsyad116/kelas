<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

// Main Routes
Route::get('/', [HomeController::class, 'index']);
Route::get('/produk', [\App\Http\Controllers\ProductController::class, 'index']);
Route::get('/tentang', [\App\Http\Controllers\AboutController::class, 'index']);
Route::get('/kontak', [\App\Http\Controllers\ContactController::class, 'index']);
Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index']);
Route::get('/pembayaran', [\App\Http\Controllers\PaymentController::class, 'index']);
Route::get('/medsos', [\App\Http\Controllers\SocialMediaController::class, 'index']);

// Authentication Routes
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);
Route::get('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout']);

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::match(['get', 'post'], '/', [\App\Http\Controllers\Admin\AdminController::class, 'index'])->name('dashboard');
    Route::get('/get_product', [\App\Http\Controllers\Admin\AdminController::class, 'getProduct'])->name('get_product');

    // Products
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ProductController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ProductController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ProductController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\ProductController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\ProductController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('destroy');
    });

    // Banners
    Route::prefix('banners')->name('banners.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BannerController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\BannerController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\BannerController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\BannerController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\BannerController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\BannerController::class, 'destroy'])->name('destroy');
    });

    // Orders
    Route::match(['get', 'post'], '/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders');

    // Customers
    Route::match(['get', 'post'], '/customers', [\App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customers');
});

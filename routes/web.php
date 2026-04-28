<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\RedeemController;
use Illuminate\Support\Facades\Route;

// Halaman Utama
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Halaman Dashboard (Setelah Login)
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Menu routes (public)
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{product}', [MenuController::class, 'show'])->name('menu.show');

// Protected cart routes (require authentication)
Route::middleware(['auth', 'verified'])->group(function () {
    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/{cartItem}/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/{cartItem}/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

    // Order / Checkout
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/order', [OrderController::class, 'store'])->name('order.store');
    Route::get('/order/{order}/receipt', [OrderController::class, 'receipt'])->name('order.receipt');
    Route::get('/orders', [OrderController::class, 'history'])->name('order.history');
    Route::get('/order/{order}', [OrderController::class, 'show'])->name('order.show');

    // Poin & Daily Claim
    Route::get('/redeem', [RedeemController::class, 'index'])->name('redeem.index');
    Route::post('/daily-claim', [RedeemController::class, 'claimDaily'])->name('daily.claim');
});

// Admin Routes (Hanya untuk is_admin = true)
Route::middleware(['auth', 'verified', 'App\Http\Middleware\IsAdmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', AdminProductController::class);
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
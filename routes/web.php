<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\AchievementController;
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

    // Referral routes
    Route::get('/referral', [ReferralController::class, 'index'])->name('referral.index');
    Route::post('/referral/apply', [ReferralController::class, 'apply'])->name('referral.apply');
    Route::post('/referral/generate-code', [ReferralController::class, 'generateCode'])->name('referral.generate');
    Route::get('/referral/stats', [ReferralController::class, 'stats'])->name('referral.stats');

    // Achievement routes
    Route::get('/achievements', [AchievementController::class, 'index'])->name('achievement.index');
    Route::get('/achievement/{achievement}', [AchievementController::class, 'show'])->name('achievement.show');
    Route::get('/achievements/list', [AchievementController::class, 'list'])->name('achievement.list');
    Route::post('/achievements/check', [AchievementController::class, 'checkAndAward'])->name('achievement.check');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
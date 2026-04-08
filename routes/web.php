<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


    Route::get('/menu', [MenuController::class, 'index'])->name('menu');

    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::patch('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');

    Route::get('/checkout', [PaymentController::class, 'showCheckout'])->name('checkout');
    Route::post('/checkout', [PaymentController::class, 'checkout'])->name('checkout.store');
    Route::get('/receipt/{id}', [PaymentController::class, 'receipt'])->name('receipt');

require __DIR__.'/settings.php';

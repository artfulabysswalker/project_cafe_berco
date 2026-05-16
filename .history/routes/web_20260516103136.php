<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StaffLoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Customer\MenuController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PasswordResetRequestController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\RedeemController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\ReviewController;

Route::get('/', [DashboardController::class, 'index']);
Route::get('/staff-login', function () {
    return view('admin.login');
})->name('staff.login.form');

Route::post('/staff-login', [StaffLoginController::class, 'login'])
    ->name('staff.login');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');






Route::get(
    '/admin/reset-request',
    [PasswordResetRequestController::class, 'create']
)->name('admin.reset.request');


Route::post(
    '/admin/reset-request',
    [PasswordResetRequestController::class, 'store']
)->name('admin.reset.store');


Route::get(
    '/admin/requests',
    [PasswordResetRequestController::class, 'index']
)->name('admin.requests');


Route::put(
    '/admin/requests/{id_user}/reset',
    [PasswordResetRequestController::class, 'resetDefault']
)->name('admin.requests.reset');



// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('control.dashboard');

// Staff
Route::get('/admin/staff', [StaffController::class, 'index'])
    ->name('admin.staffoption.index');
Route::get('/admin/staff/delete/{id}', [StaffController::class, 'delete']);
Route::put('/admin/staff/{id_user}/role', [StaffController::class, 'updateRole'])
    ->name('admin.staff.role');
// Orders
Route::get('/admin/orders', [OrderController::class, 'index'])
    ->name('admin.orders');

Route::put('/admin/orders/{id_order}/complete', [OrderController::class, 'complete'])
    ->name('admin.orders.complete');

Route::put('/admin/orders/{id_order}/cancel', [OrderController::class, 'cancel'])
    ->name('admin.orders.cancel');
Route::prefix('admin')->group(function () {

    Route::get('/receipt/view/{id}', [ReceiptController::class, 'view'])
        ->name('admin.receipt.view');

    Route::get('/receipt/print/{id}', [ReceiptController::class, 'print'])
        ->name('admin.receipt.print');

    Route::get('/receipt/pdf/{id}', [ReceiptController::class, 'pdf'])
        ->name('admin.receipt.pdf');

});
//Menus

Route::get('/admin/menu', [MenuController::class, 'index'])->name('admin.menu');

Route::get('/admin/menu/create', [MenuController::class, 'create'])->name('admin.menu.create');
Route::post('/admin/menu', [MenuController::class, 'store'])->name('admin.menu.store');
Route::get('/admin/menu/{id}', [MenuController::class, 'show'])->name('admin.menu.show');
Route::get('/admin/menu/{id}/edit', [MenuController::class, 'edit'])->name('admin.menu.edit');
Route::put('/admin/menu/{id}', [MenuController::class, 'update'])->name('admin.menu.update');
Route::delete('/admin/menu/{id}', [MenuController::class, 'destroy'])->name('admin.menu.delete');

//setting
Route::get('/admin/settings/password', [SettingsController::class, 'editPassword'])->name('admin.password.edit');
Route::post('/admin/settings/password', [SettingsController::class, 'updatePassword'])->name('admin.password.update');

// History
Route::get(
    '/admin/history',
    [OrderController::class, 'history']
)
    ->name('admin.history');


Route::get('/staff/reset-request', function () {
    return view('admin.reset');
});



Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/receipt-settings', [ReceiptController::class, 'edit'])
        ->name('receipt.edit');

    Route::post('/receipt-settings', [ReceiptController::class, 'update'])
        ->name('receipt.update');

    Route::get('/receipt/print/{id}', [ReceiptController::class, 'print'])
        ->name('receipt.print');

    Route::get('/receipt/pdf/{id}', [ReceiptController::class, 'pdf'])
        ->name('receipt.pdf');

});

    Route::get('/stats', [StatsController::class, 'index'])->name('admin.stats');


Route::get('/', function () {
    return view('customerviews.welcome');
});

Route::get('/home', function () {
    return view('Customerviews.index');
})->name('home');





// Public Menu Routes
Route::get('/menu', [MenuController::class, 'customerIndex'])
    ->name('menu.index');

Route::get('/menu/{menu}', [MenuController::class, 'showProduct'])
    ->name('menu.show');
// Loyalty Pages
Route::view('/daily-quest', 'CustomerViews.daily-quest')->name('daily-quest');
Route::view('/rewards', 'CustomerViews.rewards')->name('rewards');


// Protected Customer Routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/{cartItem}/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/{cartItem}/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');


    // Orders
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/order', [OrderController::class, 'store'])->name('order.store');
    Route::get('/order/{order}/receipt', [OrderController::class, 'receipt'])->name('order.receipt');
    Route::get('/orders', [OrderController::class, 'history'])->name('order.history');
    Route::get('/order/{order}', [OrderController::class, 'show'])->name('order.show');


    // Redeem / Loyalty
    Route::get('/redeem', [RedeemController::class, 'index'])->name('redeem.index');
    Route::post('/daily-claim', [RedeemController::class, 'claimDaily'])->name('daily.claim');
    Route::post('/redeem/{reward}', [RedeemController::class, 'redeem'])->name('redeem.redeem');
    Route::get('/redeem/receipt/{redemption}', [RedeemController::class, 'receipt'])->name('redeem.receipt');
    Route::get('/redeem/history', [RedeemController::class, 'history'])->name('redeem.history');
    Route::get('/redeem/leaderboard', [RedeemController::class, 'leaderboard'])->name('redeem.leaderboard');


    // Reviews
Route::post('/menu/{menu}/reviews', [ReviewController::class, 'store'])
    ->name('reviews.store');
});




require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
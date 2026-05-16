<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
use App\Http\Controllers\CostumerController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('customerviews.welcome');
});

Route::get('/menu', [MenuController::class, 'customerIndex'])
    ->name('menu.index');

Route::get('/menu/{menu}', [MenuController::class, 'showProduct'])
    ->name('menu.show');


/*
|--------------------------------------------------------------------------
| GUEST ONLY
|--------------------------------------------------------------------------
*/

Route::middleware(['guest'])->group(function () {

    // Customer Login
    Route::get('/testlogin', function () {
        return view('Customerviews.pages.auth.login');
    })->name('testlogin');

    Route::post('/login-user', [CostumerController::class, 'login'])
        ->name('login.user');

    // Customer Register
    Route::get('/testregister', function () {
        return view('Customerviews.pages.auth.register');
    })->name('testregister');

    Route::post('/register-user', [CostumerController::class, 'register'])
        ->name('register.user');

    // Staff Login
    Route::get('/staff-login', function () {
        return view('admin.login');
    })->name('staff.login.form');

    Route::post('/staff-login', [StaffLoginController::class, 'login'])
        ->name('staff.login');

});


/*
|--------------------------------------------------------------------------
| AUTH USERS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::post('/logout', function () {

        Auth::logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();

        return redirect('/testlogin');

    })->name('logout');

});


/*
|--------------------------------------------------------------------------
| CUSTOMER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['customer'])->group(function () {

    Route::get('/home', function () {
        return view('customerviews.home');
    })->name('home');

    // Loyalty
    Route::view('/daily-quest', 'CustomerViews.daily-quest')
        ->name('daily-quest');

    Route::view('/rewards', 'CustomerViews.rewards')
        ->name('rewards');

    // Redeem
    Route::get('/redeem', [RedeemController::class, 'index'])
        ->name('redeem.index');

    Route::post('/daily-claim', [RedeemController::class, 'claimDaily'])
        ->name('daily.claim');

    Route::post('/redeem/{reward}', [RedeemController::class, 'redeem'])
        ->name('redeem.redeem');

    Route::get('/redeem/receipt/{redemption}', [RedeemController::class, 'receipt'])
        ->name('redeem.receipt');

    Route::get('/redeem/history', [RedeemController::class, 'history'])
        ->name('redeem.history');

    Route::get('/redeem/leaderboard', [RedeemController::class, 'leaderboard'])
        ->name('redeem.leaderboard');

    // Orders
    Route::get('/orders', [OrderController::class, 'history'])
        ->name('order.history');

    Route::get('/order/{order}', [OrderController::class, 'show'])
        ->name('order.show');

    Route::get('/order/{order}/receipt', [OrderController::class, 'receipt'])
        ->name('order.receipt');

    // Reviews
    Route::post('/menu/{menu}/reviews', [ReviewController::class, 'store'])
        ->name('reviews.store');

});


/*
|--------------------------------------------------------------------------
| GUEST MODE
|--------------------------------------------------------------------------
*/

Route::middleware(['guest.mode'])->group(function () {

    // Cart
    Route::get('/cart', [CartController::class, 'index'])
        ->name('cart.index');

    Route::post('/cart/add', [CartController::class, 'add'])
        ->name('cart.add');

    Route::post('/cart/{cartItem}/update', [CartController::class, 'update'])
        ->name('cart.update');

    Route::post('/cart/{cartItem}/remove', [CartController::class, 'remove'])
        ->name('cart.remove');

    Route::post('/cart/clear', [CartController::class, 'clear'])
        ->name('cart.clear');

    Route::get('/cart/count', [CartController::class, 'count'])
        ->name('cart.count');

    // Checkout
    Route::get('/checkout', [OrderController::class, 'checkout'])
        ->name('checkout');

    Route::post('/order', [OrderController::class, 'store'])
        ->name('order.store');

});


/*
|--------------------------------------------------------------------------
| ADMIN + STAFF
|--------------------------------------------------------------------------
*/

Route::middleware(['admin.staff'])->prefix('admin')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('control.dashboard');

    // Staff
    Route::get('/staff', [StaffController::class, 'index'])
        ->name('admin.staffoption.index');

    Route::get('/staff/delete/{id}', [StaffController::class, 'delete']);

    Route::put('/staff/{id_user}/role', [StaffController::class, 'updateRole'])
        ->name('admin.staff.role');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])
        ->name('admin.orders');

    Route::put('/orders/{id_order}/complete', [OrderController::class, 'complete'])
        ->name('admin.orders.complete');

    Route::put('/orders/{id_order}/cancel', [OrderController::class, 'cancel'])
        ->name('admin.orders.cancel');

    // History
    Route::get('/history', [OrderController::class, 'history'])
        ->name('admin.history');

    // Menu CRUD
    Route::get('/menu', [MenuController::class, 'index'])
        ->name('admin.menu');

    Route::get('/menu/create', [MenuController::class, 'create'])
        ->name('admin.menu.create');

    Route::post('/menu', [MenuController::class, 'store'])
        ->name('admin.menu.store');

    Route::get('/menu/{id}', [MenuController::class, 'show'])
        ->name('admin.menu.show');

    Route::get('/menu/{id}/edit', [MenuController::class, 'edit'])
        ->name('admin.menu.edit');

    Route::put('/menu/{id}', [MenuController::class, 'update'])
        ->name('admin.menu.update');

    Route::delete('/menu/{id}', [MenuController::class, 'destroy'])
        ->name('admin.menu.delete');

    // Receipt
    Route::get('/receipt/view/{id}', [ReceiptController::class, 'view'])
        ->name('admin.receipt.view');

    Route::get('/receipt/print/{id}', [ReceiptController::class, 'print'])
        ->name('admin.receipt.print');

    Route::get('/receipt/pdf/{id}', [ReceiptController::class, 'pdf'])
        ->name('admin.receipt.pdf');

    // Receipt Settings
    Route::get('/receipt-settings', [ReceiptController::class, 'edit'])
        ->name('admin.receipt.edit');

    Route::post('/receipt-settings', [ReceiptController::class, 'update'])
        ->name('admin.receipt.update');

    // Settings
    Route::get('/settings/password', [SettingsController::class, 'editPassword'])
        ->name('admin.password.edit');

    Route::post('/settings/password', [SettingsController::class, 'updatePassword'])
        ->name('admin.password.update');

    // Stats
    Route::get('/stats', [StatsController::class, 'index'])
        ->name('admin.stats');

    // Reset Request
    Route::get('/reset-request', [PasswordResetRequestController::class, 'create'])
        ->name('admin.reset.request');

    Route::post('/reset-request', [PasswordResetRequestController::class, 'store'])
        ->name('admin.reset.store');

    Route::get('/requests', [PasswordResetRequestController::class, 'index'])
        ->name('admin.requests');

    Route::put('/requests/{id_user}/reset', [PasswordResetRequestController::class, 'resetDefault'])
        ->name('admin.requests.reset');

});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
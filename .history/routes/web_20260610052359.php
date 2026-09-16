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
use App\Http\Controllers\Customer\FavoriteController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\AchievementController;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Controllers\AuthController

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// Xendit Test Endpoint (no session/db required)
Route::get('/xendit/test', function () {
    try {
        \Xendit\Configuration::setXenditKey(config('services.xendit.secret_key'));

        return response()->json([
            'status' => 'success',
            'message' => '✅ Xendit SDK berhasil diinisialisasi!',
            'config' => [
                'api_key_set' => !empty(config('services.xendit.secret_key')),
                'public_key_set' => !empty(config('services.xendit.public_key')),
                'environment' => config('services.xendit.environment', 'development'),
            ],
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => '❌ Error: ' . $e->getMessage(),
        ], 500);
    }
})->name('xendit.test')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();

        // If admin or staff, redirect to admin dashboard
        if ($user->isAdmin() || $user->isStaff()) {
            return redirect()->route('dashboard');
        }

        // Otherwise redirect to menu
        return redirect()->route('menu.index');
    }

    return view('Customerviews.welcome');
});

Route::middleware(['restore.guest'])->group(function () {

    Route::get('/menu', [MenuController::class, 'customerIndex'])
        ->name('menu.index');

    Route::get('/menu/{menu}', [MenuController::class, 'showProduct'])
        ->name('menu.show');

Route::post('/password/request', [App\Http\Controllers\AuthController::class, 'passwordRequestText'])
    ->name('password.request.text');
    /*
    |--------------------------------------------------------------------------
    | GUEST ONLY
    |--------------------------------------------------------------------------
    */

    Route::post('/guest-login', function () {
        // Ensure a guest role exists and use its id to satisfy FK
        $role = Role::firstOrCreate([
            'role_name' => 'Guest',
        ]);

        $guest = User::firstOrCreate(
            ['is_guest' => true],
            [
                'name' => 'Guest User',
                'username' => 'guest',
                'email' => 'guest@local.test',
                'password' => Hash::make(Str::random(16)),
                'id_role' => $role->id_role,
                'is_guest' => true,
            ]
        );

        Auth::login($guest);

        session([
            'is_guest' => true,
            'guest_name' => 'Guest',
        ]);

        return redirect()->route('menu.index');
    })->name('guest.login');

});

Route::middleware(['guest'])->group(function () {

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

    Route::post('/logout', function (Illuminate\Http\Request $request) {

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');

    })->name('logout');

});


/*
|--------------------------------------------------------------------------
| CUSTOMER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['customer'])->group(function () {

    Route::get('/home', function () {
        return view('Customerviews.home');
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

    Route::get('/redeem/receipt/{redemption}', [RedeemController::class, 'receipt'])
        ->name('redeem.receipt');

    Route::get('/redeem/history', [RedeemController::class, 'history'])
        ->name('redeem.history');

    Route::get('/redeem/leaderboard', [RedeemController::class, 'leaderboard'])
        ->name('redeem.leaderboard');

    Route::post('/redeem/{reward}', [RedeemController::class, 'redeem'])
        ->name('redeem.redeem');

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

    Route::post('/favorites/toggle', [FavoriteController::class, 'toggle'])
        ->name('favorites.toggle');

    // Achievements
    Route::get('/achievements', [AchievementController::class, 'index'])
        ->name('achievements.index');

    Route::get('/achievements/list', [AchievementController::class, 'list'])
        ->name('achievements.list');

    Route::get('/achievement/{achievement}', [AchievementController::class, 'show'])
        ->name('achievement.show');

    // Referral
    Route::get('/referral', [ReferralController::class, 'index'])
        ->name('referral.index');

    Route::post('/referral/apply', [ReferralController::class, 'apply'])
        ->name('referral.apply');

    Route::get('/referral/generate-code', [ReferralController::class, 'generateCode'])
        ->name('referral.generateCode');

    Route::get('/referral/stats', [ReferralController::class, 'stats'])
        ->name('referral.stats');

    // My Vouchers
    Route::get('/my-vouchers', [VoucherController::class, 'myVouchers'])
        ->name('vouchers.myVouchers');

    // Playlists
    Route::get('/playlists', [PlaylistController::class, 'index'])
        ->name('playlists.index');

    Route::get('/playlists/create', [PlaylistController::class, 'create'])
        ->name('playlists.create');

    Route::post('/playlists', [PlaylistController::class, 'store'])
        ->name('playlists.store');

    Route::get('/playlists/top-voted', [PlaylistController::class, 'topVoted'])
        ->name('playlists.topVoted');

    Route::get('/playlists/current-playing', [PlaylistController::class, 'currentPlaying'])
        ->name('playlists.currentPlaying');

    Route::get('/playlists/{playlist}', [PlaylistController::class, 'show'])
        ->name('playlists.show');

    Route::get('/playlists/{playlist}/edit', [PlaylistController::class, 'edit'])
        ->name('playlists.edit');

    Route::put('/playlists/{playlist}', [PlaylistController::class, 'update'])
        ->name('playlists.update');

    Route::delete('/playlists/{playlist}', [PlaylistController::class, 'destroy'])
        ->name('playlists.destroy');

    Route::post('/playlists/{playlist}/vote', [PlaylistController::class, 'vote'])
        ->name('playlists.vote');

});


/*
|--------------------------------------------------------------------------
| GUEST MODE
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Cart
    Route::get('/cart', [CartController::class, 'index'])
        ->name('cart.index');

    Route::post('/cart/add', [CartController::class, 'add'])
        ->name('cart.add');

    Route::post('/cart/clear', [CartController::class, 'clear'])
        ->name('cart.clear');

    Route::get('/cart/count', [CartController::class, 'count'])
        ->name('cart.count');

    Route::patch('/cart/{cartItem}/update', [CartController::class, 'update'])
        ->name('cart.update');

    Route::delete('/cart/{cartItem}/remove', [CartController::class, 'remove'])
        ->name('cart.remove');

    // Checkout
    Route::get('/checkout', [OrderController::class, 'checkout'])
        ->name('checkout');

    // Get available discounts (API endpoint)
    Route::get('/api/discounts/available', [\App\Http\Controllers\Admin\DiscountSchemeController::class, 'getAvailable'])
        ->name('api.discounts.available');

    Route::post('/order', [OrderController::class, 'store'])
        ->name('order.store');

    // Xendit Payment Routes
    Route::prefix('xendit')->group(function () {
        Route::get('/payment/{order}/redirect', [\App\Http\Controllers\XenditPaymentController::class, 'redirectToPayment'])
            ->name('xendit.payment.redirect');

        Route::post('/payment/{order}/invoice', [\App\Http\Controllers\XenditPaymentController::class, 'createInvoice'])
            ->name('xendit.payment.create');

        Route::get('/payment/{order}/status', [\App\Http\Controllers\XenditPaymentController::class, 'checkStatus'])
            ->name('xendit.payment.status');

        // Success and Failed redirect from Xendit
        Route::get('/payment/success/{order}', [\App\Http\Controllers\XenditPaymentController::class, 'success'])
            ->name('payment.success');

        Route::get('/payment/failed/{order}', [\App\Http\Controllers\XenditPaymentController::class, 'failed'])
            ->name('payment.failed');

        // Xendit Callback
        Route::post('/payment/callback', [\App\Http\Controllers\XenditPaymentController::class, 'callback'])
            ->name('xendit.payment.callback')
            ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

        // QRIS Payment Routes
        Route::prefix('qris')->group(function () {
            Route::get('/payment/{order}', [\App\Http\Controllers\QrisPaymentController::class, 'show'])
                ->name('xendit.qris.show');

            Route::get('/payment/{order}/redirect', [\App\Http\Controllers\QrisPaymentController::class, 'redirectToPayment'])
                ->name('xendit.qris.redirect');

            Route::post('/payment/{order}/invoice', [\App\Http\Controllers\QrisPaymentController::class, 'createInvoice'])
                ->name('xendit.qris.create');

            Route::get('/payment/{order}/status', [\App\Http\Controllers\QrisPaymentController::class, 'checkStatus'])
                ->name('xendit.qris.status');

            // Success and Failed redirect from Xendit
            Route::get('/payment/success/{order}', [\App\Http\Controllers\QrisPaymentController::class, 'success'])
                ->name('xendit.qris.success');

            Route::get('/payment/failed/{order}', [\App\Http\Controllers\QrisPaymentController::class, 'failed'])
                ->name('xendit.qris.failed');

            // QRIS Callback
            Route::post('/payment/callback', [\App\Http\Controllers\QrisPaymentController::class, 'callback'])
                ->name('xendit.qris.callback')
                ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
        });
    });

});


/*
|--------------------------------------------------------------------------
| ADMIN + STAFF
|--------------------------------------------------------------------------
*/

Route::middleware(['admin.staff'])->prefix('admin')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Staff (Admin only)
    Route::middleware(['is_admin'])->group(function () {
        Route::get('/staff', [StaffController::class, 'index'])
            ->name('admin.staffoption.index');

        Route::get('/staff/create', [StaffController::class, 'create'])
            ->name('admin.staffoption.create');

        Route::post('/staff', [StaffController::class, 'store'])
            ->name('admin.staff.store');

        Route::get('/staff/{id_user}/edit', [StaffController::class, 'edit'])
            ->name('admin.staffoption.edit');

        Route::put('/staff/{id_user}', [StaffController::class, 'update'])
            ->name('admin.staff.update');

        Route::delete('/staff/{id}', [StaffController::class, 'destroy'])
            ->name('admin.staff.destroy');

        Route::put('/staff/{id_user}/role', [StaffController::class, 'updateRole'])
            ->name('admin.staff.role');
    });

    // Orders

    Route::get('/admin/orders', [OrderController::class, 'index'])
        ->name('orders.index');

    Route::patch('/admin/orders/{id_order}/complete', [OrderController::class, 'complete'])
        ->name('order.complete');

    Route::patch('/admin/orders/{id_order}/cancel', [OrderController::class, 'cancel'])
        ->name('order.cancel');

    Route::post('/order/{id_order}/finish', [OrderController::class, 'finishOrder'])
        ->name('order.finish');

        //password
      

Route::put('/password/update', [StaffController::class, 'updateOwnPassword'])
    ->name('admin.password.update');

Route::get('/staff/{id}/password', [StaffController::class, 'editPassword'])
    ->name('admin.staff.password.edit');

Route::put('/staff/{id}/password', [StaffController::class, 'updatePassword'])
    ->name('admin.staff.password.update');
 
    // History
    Route::get('/history', [OrderController::class, 'historyAdmin'])
        ->name('admin.history');

Route::get('/order-history/{id}/receipt', [ReceiptController::class, 'viewHistory'])
    ->name('admin.history.receipt');
    // Menu CRUD
    Route::get('/menu/{id}/discount', [MenuController::class, 'discountForm'])
    ->name('admin.menu.discount');

Route::post('/menu/{id}/discount', [MenuController::class, 'setDiscount'])
    ->name('admin.menu.discount.store');
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

    // Vouchers CRUD
    Route::get('/vouchers', [VoucherController::class, 'index'])
        ->name('vouchers.index');

    Route::get('/vouchers/create', [VoucherController::class, 'create'])
        ->name('vouchers.create');

    Route::post('/vouchers', [VoucherController::class, 'store'])
        ->name('vouchers.store');

    Route::get('/vouchers/{voucher}/edit', [VoucherController::class, 'edit'])
        ->name('vouchers.edit');

    Route::put('/vouchers/{voucher}', [VoucherController::class, 'update'])
        ->name('vouchers.update');

    Route::delete('/vouchers/{voucher}', [VoucherController::class, 'destroy'])
        ->name('vouchers.destroy');

    Route::get('/vouchers/{voucher}/distribute-form', [VoucherController::class, 'distributeForm'])
        ->name('vouchers.distributeForm');

    Route::post('/vouchers/{voucher}/distribute', [VoucherController::class, 'distribute'])
        ->name('vouchers.distribute');

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

    // Tax Configuration
    Route::prefix('tax')->name('admin.tax.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\TaxConfigurationController::class, 'index'])
            ->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\TaxConfigurationController::class, 'create'])
            ->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\TaxConfigurationController::class, 'store'])
            ->name('store');
        Route::get('/{tax}/edit', [\App\Http\Controllers\Admin\TaxConfigurationController::class, 'edit'])
            ->name('edit');
        Route::put('/{tax}', [\App\Http\Controllers\Admin\TaxConfigurationController::class, 'update'])
            ->name('update');
        Route::delete('/{tax}', [\App\Http\Controllers\Admin\TaxConfigurationController::class, 'destroy'])
            ->name('destroy');
        Route::post('/{tax}/set-active', [\App\Http\Controllers\Admin\TaxConfigurationController::class, 'setActive'])
            ->name('setActive');
    });

    // Discount Schemes
    Route::prefix('discount')->name('admin.discount.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DiscountSchemeController::class, 'index'])
            ->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\DiscountSchemeController::class, 'create'])
            ->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\DiscountSchemeController::class, 'store'])
            ->name('store');
        Route::get('/{discount}/edit', [\App\Http\Controllers\Admin\DiscountSchemeController::class, 'edit'])
            ->name('edit');
        Route::put('/{discount}', [\App\Http\Controllers\Admin\DiscountSchemeController::class, 'update'])
            ->name('update');
        Route::delete('/{discount}', [\App\Http\Controllers\Admin\DiscountSchemeController::class, 'destroy'])
            ->name('destroy');
    });

    // Penjualan Hari Ini
    Route::get('/sales-today', function () {
        return view('admin.sales-today');
    })->name('admin.sales.today');

    // Penjualan Produk & Analytics
    Route::get('/product-analytics', function () {
        return view('admin.product-sales-analytics');
    })->name('admin.product.analytics');

    // Konfigurasi Pajak & Diskon
    Route::get('/config/tax-discount', function () {
        return view('admin.tax-discount-config');
    })->name('admin.config.tax-discount');

    // Analytics & Reports (Owner only)
    Route::middleware(['is_admin'])->prefix('analytics')->name('admin.analytics.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AnalyticsController::class, 'dashboard'])
            ->name('dashboard');
        Route::get('/products', [\App\Http\Controllers\Admin\AnalyticsController::class, 'productReport'])
            ->name('products');
        Route::get('/export', [\App\Http\Controllers\Admin\AnalyticsController::class, 'exportCsv'])
            ->name('export');
    });

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

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

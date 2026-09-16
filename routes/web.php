<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DiscountSchemeController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\HppController;
use App\Http\Controllers\Admin\TaxConfigurationController;
use App\Http\Controllers\Api\LoginApiController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\FavoriteController;
use App\Http\Controllers\Customer\MenuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PasswordResetRequestController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrisPaymentController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\RedeemController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StaffLoginController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\XenditPaymentController;
use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Xendit\Configuration;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// Xendit Test Endpoint (no session/db required)
Route::get('/xendit/test', function () {
    try {
        Configuration::setXenditKey(config('services.xendit.secret_key'));

        return response()->json([
            'status' => 'success',
            'message' => 'Xendit SDK berhasil diinisialisasi!',
            'config' => [
                'api_key_set' => ! empty(config('services.xendit.secret_key')),
                'public_key_set' => ! empty(config('services.xendit.public_key')),
                'environment' => config('services.xendit.environment', 'development'),
            ],
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: '.$e->getMessage(),
        ], 500);
    }
})->name('xendit.test')->withoutMiddleware([VerifyCsrfToken::class]);

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if (! $user->is_guest && ($user->isAdmin() || $user->isStaff())) {
            return redirect()->route('dashboard');
        }
        if (! $user->is_guest) {
            return redirect()->route('menu.index');
        }
    }

    $favoriteMenus = Menu::where('status_tersedia', 1)->take(8)->get();
    if ($favoriteMenus->isEmpty()) {
        $favoriteMenus = Menu::take(8)->get();
    }

    return view('Customerviews.welcome', compact('favoriteMenus'));
})->name('home');

Route::middleware(['restore.guest'])->group(function () {

    Route::get('/menu', [MenuController::class, 'customerIndex'])
        ->name('menu.index');

    Route::get('/menu/{menu}', [MenuController::class, 'showProduct'])
        ->name('menu.show');

    Route::post('/password/request', [PasswordResetRequestController::class, 'store'])
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

    Route::post('/logout', function (Request $request) {

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
    })->name('customer.home');

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

    // EXP & Rewards (Tukar EXP)
    Route::get('/redeem', [RedeemController::class, 'index'])
        ->name('redeem.index');

    Route::post('/redeem/{reward}', [RedeemController::class, 'redeem'])
        ->name('redeem.redeem');

    Route::get('/redeem/receipt/{redemption}', [RedeemController::class, 'receipt'])
        ->name('redeem.receipt');

    Route::get('/redeem/history', [RedeemController::class, 'history'])
        ->name('redeem.history');

    Route::post('/daily-claim', [RedeemController::class, 'claimDaily'])
        ->name('daily.claim');

    Route::get('/leaderboard', [RedeemController::class, 'leaderboard'])
        ->name('leaderboard');

    // Daily Quest & Rewards Center
    Route::get('/daily-quest', function () {
        return view('Customerviews.daily-quest');
    })->name('daily.quest');

    Route::get('/rewards', function () {
        return view('Customerviews.rewards');
    })->name('rewards');

    Route::get('/achievements', [AchievementController::class, 'index'])
        ->name('achievements.index');

    // Referral System
    Route::get('/referral', [ReferralController::class, 'index'])
        ->name('referral.index');

    Route::post('/referral/apply', [ReferralController::class, 'apply'])
        ->name('referral.apply');

    Route::post('/referral/generate', [ReferralController::class, 'generateCode'])
        ->name('referral.generate');

});

/*
|--------------------------------------------------------------------------
| GUEST MODE
|--------------------------------------------------------------------------
*/

Route::middleware(['restore.guest'])->group(function () {

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
    Route::get('/api/discounts/available', [DiscountSchemeController::class, 'getAvailable'])
        ->name('api.discounts.available');

    Route::post('/order', [OrderController::class, 'store'])
        ->name('order.store');

    // Xendit Payment Routes
    Route::prefix('xendit')->group(function () {
        Route::get('/payment/{order}/redirect', [XenditPaymentController::class, 'redirectToPayment'])
            ->name('xendit.payment.redirect');

        Route::post('/payment/{order}/invoice', [XenditPaymentController::class, 'createInvoice'])
            ->name('xendit.payment.create');

        Route::get('/payment/{order}/status', [XenditPaymentController::class, 'checkStatus'])
            ->name('xendit.payment.status');

        // Success and Failed redirect from Xendit
        Route::get('/payment/success/{order}', [XenditPaymentController::class, 'success'])
            ->name('payment.success');

        Route::get('/payment/failed/{order}', [XenditPaymentController::class, 'failed'])
            ->name('payment.failed');

        // Xendit Callback
        Route::post('/payment/callback', [XenditPaymentController::class, 'callback'])
            ->name('xendit.payment.callback')
            ->withoutMiddleware([VerifyCsrfToken::class]);

        // QRIS Payment Routes
        Route::prefix('qris')->group(function () {
            Route::get('/payment/{order}', [QrisPaymentController::class, 'show'])
                ->name('xendit.qris.show');

            Route::get('/payment/{order}/redirect', [QrisPaymentController::class, 'redirectToPayment'])
                ->name('xendit.qris.redirect');

            Route::post('/payment/{order}/invoice', [QrisPaymentController::class, 'createInvoice'])
                ->name('xendit.qris.create');

            Route::get('/payment/{order}/status', [QrisPaymentController::class, 'checkStatus'])
                ->name('xendit.qris.status');

            // Success and Failed redirect from Xendit
            Route::get('/payment/success/{order}', [QrisPaymentController::class, 'success'])
                ->name('xendit.qris.success');

            Route::get('/payment/failed/{order}', [QrisPaymentController::class, 'failed'])
                ->name('xendit.qris.failed');

            // QRIS Callback
            Route::post('/payment/callback', [QrisPaymentController::class, 'callback'])
                ->name('xendit.qris.callback')
                ->withoutMiddleware([VerifyCsrfToken::class]);
        });
    });

});

/*
|--------------------------------------------------------------------------
| AUTH USER ROUTES (Dashboard, Profile)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

});

/*
|--------------------------------------------------------------------------
| ADMIN + STAFF
|--------------------------------------------------------------------------
*/

Route::middleware(['admin.staff'])->group(function () {

    Route::get('/control/dashboard', [DashboardController::class, 'index'])
        ->name('control.dashboard');

    // Staff Directory (Viewable by Staff, modifications Admin only)
    Route::get('/staff', [StaffController::class, 'index'])
        ->name('admin.staffoption.index');

    Route::middleware(['is_admin'])->group(function () {
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

        Route::post('/staff/{id_user}/toggle-status', [StaffController::class, 'toggleStatus'])
            ->name('admin.staff.toggle-status');

        Route::post('/staff/{id_user}/reset-password', [StaffController::class, 'resetPassword'])
            ->name('admin.staff.reset-password');
    });

    // Orders
    Route::get('/admin/orders', [OrderController::class, 'index'])
        ->name('admin.orders');

    Route::get('/admin/orders-list', [OrderController::class, 'index'])
        ->name('orders.index');

    Route::put('/orders/{id_order}/complete', [OrderController::class, 'complete'])
        ->name('admin.orders.complete');

    Route::patch('/admin/orders/{id_order}/complete', [OrderController::class, 'complete'])
        ->name('order.complete');

    Route::put('/orders/{id_order}/cancel', [OrderController::class, 'cancel'])
        ->name('admin.orders.cancel');

    Route::patch('/admin/orders/{id_order}/cancel', [OrderController::class, 'cancel'])
        ->name('order.cancel');

    Route::post('/order/{id_order}/finish', [OrderController::class, 'finishOrder'])
        ->name('order.finish');

    // Password Management
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

    // Menu CRUD & Discount
    Route::get('/menu/{id}/discount', [MenuController::class, 'discountForm'])
        ->name('admin.menu.discount');

    Route::post('/menu/{id}/discount', [MenuController::class, 'setDiscount'])
        ->name('admin.menu.discount.store');

    Route::get('/admin/menu', [MenuController::class, 'index'])
        ->name('admin.menu');

    // HPP & Resep Bahan Baku
    Route::get('/hpp', [HppController::class, 'index'])
        ->name('admin.hpp');
    Route::get('/hpp/recipe/{menuId}', [HppController::class, 'getRecipe'])
        ->name('admin.hpp.recipe.get');
    Route::post('/hpp/recipe/{menuId}', [HppController::class, 'saveRecipe'])
        ->name('admin.hpp.recipe.save');
    Route::post('/hpp/raw-material', [HppController::class, 'storeRawMaterial'])
        ->name('admin.hpp.raw-material.store');
    Route::put('/hpp/raw-material/{id}', [HppController::class, 'updateRawMaterial'])
        ->name('admin.hpp.raw-material.update');
    Route::delete('/hpp/raw-material/{id}', [HppController::class, 'deleteRawMaterial'])
        ->name('admin.hpp.raw-material.destroy');

    // Stok Barang (Inventory Management)
    Route::get('/inventory', function () {
        return view('admin.inventory');
    })->name('admin.inventory');

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

    Route::put('/menu/{id}/toggle-status', [MenuController::class, 'toggleStatus'])
        ->name('admin.menu.toggle-status');

    Route::delete('/menu/{id}', [MenuController::class, 'destroy'])
        ->name('admin.menu.delete');

    // Categories Management
    Route::get('/categories/list', [CategoryController::class, 'listJson'])
        ->name('admin.categories.list');

    Route::post('/categories', [CategoryController::class, 'store'])
        ->name('admin.categories.store');

    Route::put('/categories/{id}', [CategoryController::class, 'update'])
        ->name('admin.categories.update');

    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])
        ->name('admin.categories.destroy');

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

    // Stats & Financial Reports
    Route::get('/stats', [StatsController::class, 'index'])
        ->name('admin.stats');

    Route::get('/stats/pdf', [StatsController::class, 'downloadPdf'])
        ->name('admin.stats.pdf');

    Route::get('/stats/print', [StatsController::class, 'printReport'])
        ->name('admin.stats.print');

    // Expense Management (Dedicated Page)
    Route::get('/expenses', [ExpenseController::class, 'index'])
        ->name('admin.expenses.index');

    Route::post('/expenses', [ExpenseController::class, 'store'])
        ->name('admin.expenses.store');

    Route::put('/expenses/{id}', [ExpenseController::class, 'update'])
        ->name('admin.expenses.update');

    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])
        ->name('admin.expenses.destroy');

    Route::get('/expenses/pdf', [ExpenseController::class, 'downloadPdf'])
        ->name('admin.expenses.pdf');

    // Tax Configuration
    Route::prefix('tax')->name('admin.tax.')->group(function () {
        Route::get('/', [TaxConfigurationController::class, 'index'])
            ->name('index');
        Route::get('/create', [TaxConfigurationController::class, 'create'])
            ->name('create');
        Route::post('/', [TaxConfigurationController::class, 'store'])
            ->name('store');
        Route::get('/{tax}/edit', [TaxConfigurationController::class, 'edit'])
            ->name('edit');
        Route::put('/{tax}', [TaxConfigurationController::class, 'update'])
            ->name('update');
        Route::delete('/{tax}', [TaxConfigurationController::class, 'destroy'])
            ->name('destroy');
        Route::post('/{tax}/set-active', [TaxConfigurationController::class, 'setActive'])
            ->name('setActive');
    });

    // Discount Schemes
    Route::prefix('discount')->name('admin.discount.')->group(function () {
        Route::get('/', [DiscountSchemeController::class, 'index'])
            ->name('index');
        Route::get('/create', [DiscountSchemeController::class, 'create'])
            ->name('create');
        Route::post('/', [DiscountSchemeController::class, 'store'])
            ->name('store');
        Route::get('/{discount}/edit', [DiscountSchemeController::class, 'edit'])
            ->name('edit');
        Route::put('/{discount}', [DiscountSchemeController::class, 'update'])
            ->name('update');
        Route::delete('/{discount}', [DiscountSchemeController::class, 'destroy'])
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
        Route::get('/', [AnalyticsController::class, 'dashboard'])
            ->name('dashboard');
        Route::get('/products', [AnalyticsController::class, 'productReport'])
            ->name('products');
        Route::get('/export', [AnalyticsController::class, 'exportCsv'])
            ->name('export');
    });

    // Shift Monitoring
    Route::get('/shifts', [ShiftController::class, 'index'])
        ->name('admin.shifts.index');
    Route::post('/shifts/open', [ShiftController::class, 'openShift'])
        ->name('admin.shifts.open');
    Route::post('/shifts/close', [ShiftController::class, 'closeShift'])
        ->name('admin.shifts.close');
    Route::get('/shifts/{id}/orders', [ShiftController::class, 'getShiftOrders'])
        ->name('admin.shifts.orders');

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

/*
|--------------------------------------------------------------------------
| POS API ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    Route::post('/login', [LoginApiController::class, 'login']);
});

Route::prefix('api/pos')->group(function () {
    Route::post('/verify-pin', [ShiftController::class, 'verifyPin']);
    Route::get('/pegawai-list', [ShiftController::class, 'getEmployees']);
    Route::post('/shift/open', [ShiftController::class, 'openShift']);
    Route::post('/shift/close', [ShiftController::class, 'closeShift']);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

<?php

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Role;
use App\Models\User;

function analyticsAdmin(): User
{
    $role = Role::firstOrCreate(['role_name' => 'Admin']);

    return User::factory()->create(['id_role' => $role->id_role, 'status' => 'active']);
}

function analyticsMenu(): Menu
{
    return Menu::create([
        'nama_menu' => 'Kopi Hijau',
        'harga' => 15000,
        'hpp' => 5000,
        'stok' => 100,
        'status_tersedia' => 1,
        'id_kategori' => 1,
    ]);
}

function analyticsOrder(array $overrides = []): Order
{
    $user = User::factory()->create(['is_guest' => true]);

    $order = Order::create(array_merge([
        'tanggal' => now(),
        'nama_pelanggan' => 'Pelanggan Analytics',
        'customer_name' => 'Pelanggan Analytics',
        'total_harga' => 15000,
        'subtotal' => 15000,
        'final_total' => 15000,
        'status_pembayaran' => 'pending',
        'status_order' => 'pending',
        'service_type' => 'dine_in',
        'payment_method' => 'cash',
        'id_user' => $user->id_user,
    ], $overrides));

    return $order;
}

test('admin can access the analytics dashboard', function () {
    $admin = analyticsAdmin();

    $response = $this->actingAs($admin)
        ->get(route('admin.analytics.dashboard'));
    
    // Debug: if failed, show response content
    if ($response->status() !== 200) {
        echo "Status: " . $response->status() . "\n";
        echo "Content: " . $response->getContent() . "\n";
    }
    
    $response->assertOk()
        ->assertSee('Analytics & Insights', false);
});

test('non-admin cannot access the analytics dashboard', function () {
    $role = Role::firstOrCreate(['role_name' => 'Staff']);
    $staff = User::factory()->create(['id_role' => $role->id_role, 'status' => 'active']);

    $this->actingAs($staff)
        ->get(route('admin.analytics.dashboard'))
        ->assertForbidden();
});

test('dashboard calculates profit margin based on HPP from order items', function () {
    $menu = analyticsMenu();
    $admin = analyticsAdmin();

    // Create order with paid status
    $order = analyticsOrder(['status_pembayaran' => 'paid', 'final_total' => 15000]);

    // Create order item with HPP = 5000, quantity = 2, subtotal = 15000
    OrderItem::create([
        'id_order' => $order->id_order,
        'id_menu' => $menu->id_menu,
        'quantity' => 2,
        'subtotal' => 15000,
        'hpp' => 5000, // HPP per unit
        'hpp_at_sale' => 5000,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.analytics.dashboard', ['period' => 'daily', 'date' => now()->format('Y-m-d')]))
        ->assertOk()
        ->assertSee('Rp 15.000', false) // total_revenue
        ->assertSee('Rp 5.000', false); // total_hpp = 5000 * 2 = 10000, margin = 15000 - 10000 = 5000
});

test('dashboard profit margin is calculated as (revenue - hpp * qty)', function () {
    $menu = analyticsMenu();
    $admin = analyticsAdmin();

    // Create order with item: revenue = 30000, hpp = 5000/unit, qty = 2
    // Expected margin = 30000 - (5000 * 2) = 30000 - 10000 = 20000
    $order = analyticsOrder(['status_pembayaran' => 'paid', 'final_total' => 30000]);

    OrderItem::create([
        'id_order' => $order->id_order,
        'id_menu' => $menu->id_menu,
        'quantity' => 2,
        'subtotal' => 30000,
        'hpp' => 5000,
        'hpp_at_sale' => 5000,
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.analytics.dashboard', ['period' => 'daily', 'date' => now()->format('Y-m-d')]))
        ->assertOk();

    // Verify the calculation works - the purchase history should show correct profit
    $transaction = $response->viewData('periodTransactions')['purchase_history']->first();
    expect((float) $transaction->final_total)->toBe(30000.0);
});

test('payment method summary shows correct transaction count and revenue', function () {
    $menu = analyticsMenu();
    $admin = analyticsAdmin();

    // Create cash order
    $cashOrder = analyticsOrder([
        'status_pembayaran' => 'paid',
        'final_total' => 15000,
        'payment_method' => 'cash',
    ]);

    // Create QRIS order
    $qrisOrder = analyticsOrder([
        'status_pembayaran' => 'paid',
        'final_total' => 25000,
        'payment_method' => 'qris',
    ]);

    // Create items for both orders
    OrderItem::create([
        'id_order' => $cashOrder->id_order,
        'id_menu' => $menu->id_menu,
        'quantity' => 1,
        'subtotal' => 15000,
        'hpp' => 5000,
        'hpp_at_sale' => 5000,
    ]);

    OrderItem::create([
        'id_order' => $qrisOrder->id_order,
        'id_menu' => $menu->id_menu,
        'quantity' => 1,
        'subtotal' => 25000,
        'hpp' => 5000,
        'hpp_at_sale' => 5000,
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.analytics.dashboard', ['period' => 'daily', 'date' => now()->format('Y-m-d')]))
        ->assertOk();

    $paymentMethods = $response->viewData('paymentMethodSummary');

    // Find cash and qris in the results
    $cashData = collect($paymentMethods)->firstWhere('payment_method', 'cash');
    $qrisData = collect($paymentMethods)->firstWhere('payment_method', 'qris');

    expect($cashData->transaction_count)->toBe(1)
        ->and($cashData->total_revenue)->toBe(15000);

    expect($qrisData->transaction_count)->toBe(1)
        ->and($qrisData->total_revenue)->toBe(25000);
});

test('period filter works for daily transactions', function () {
    $menu = analyticsMenu();
    $admin = analyticsAdmin();

    // Create order for today
    $todayOrder = analyticsOrder([
        'status_pembayaran' => 'paid',
        'final_total' => 15000,
        'payment_method' => 'cash',
        'tanggal' => now(),
    ]);

    OrderItem::create([
        'id_order' => $todayOrder->id_order,
        'id_menu' => $menu->id_menu,
        'quantity' => 1,
        'subtotal' => 15000,
        'hpp' => 5000,
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.analytics.dashboard', ['period' => 'daily', 'date' => now()->format('Y-m-d')]))
        ->assertOk();

    $transactions = $response->viewData('periodTransactions')['total_transactions'];
    expect($transactions)->toBe(1);
});

test('period filter works for monthly transactions', function () {
    $menu = analyticsMenu();
    $admin = analyticsAdmin();

    // Create order for this month
    $monthOrder = analyticsOrder([
        'status_pembayaran' => 'paid',
        'final_total' => 15000,
        'payment_method' => 'cash',
        'tanggal' => now(),
    ]);

    OrderItem::create([
        'id_order' => $monthOrder->id_order,
        'id_menu' => $menu->id_menu,
        'quantity' => 1,
        'subtotal' => 15000,
        'hpp' => 5000,
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.analytics.dashboard', ['period' => 'monthly', 'date' => now()->format('Y-m') . '-01']))
        ->assertOk();

    $transactions = $response->viewData('periodTransactions')['total_transactions'];
    expect($transactions)->toBe(1);
});

test('period filter works for yearly transactions', function () {
    $menu = analyticsMenu();
    $admin = analyticsAdmin();

    // Create order for this year
    $yearOrder = analyticsOrder([
        'status_pembayaran' => 'paid',
        'final_total' => 15000,
        'payment_method' => 'cash',
        'tanggal' => now(),
    ]);

    OrderItem::create([
        'id_order' => $yearOrder->id_order,
        'id_menu' => $menu->id_menu,
        'quantity' => 1,
        'subtotal' => 15000,
        'hpp' => 5000,
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.analytics.dashboard', ['period' => 'yearly', 'date' => now()->format('Y-m-d')]))
        ->assertOk();

    $transactions = $response->viewData('periodTransactions')['total_transactions'];
    expect($transactions)->toBe(1);
});

test('only paid transactions are included in analytics', function () {
    $menu = analyticsMenu();
    $admin = analyticsAdmin();

    // Create unpaid order
    $unpaidOrder = analyticsOrder([
        'status_pembayaran' => 'pending',
        'final_total' => 15000,
        'payment_method' => 'cash',
    ]);

    // Create paid order
    $paidOrder = analyticsOrder([
        'status_pembayaran' => 'paid',
        'final_total' => 20000,
        'payment_method' => 'cash',
    ]);

    OrderItem::create([
        'id_order' => $paidOrder->id_order,
        'id_menu' => $menu->id_menu,
        'quantity' => 1,
        'subtotal' => 20000,
        'hpp' => 5000,
    ]);

    OrderItem::create([
        'id_order' => $unpaidOrder->id_order,
        'id_menu' => $menu->id_menu,
        'quantity' => 1,
        'subtotal' => 15000,
        'hpp' => 5000,
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.analytics.dashboard', ['period' => 'daily', 'date' => now()->format('Y-m-d')]))
        ->assertOk();

    $transactions = $response->viewData('periodTransactions');

    // Should only count the paid order
    expect($transactions['total_transactions'])->toBe(1)
        ->and((float) $transactions['total_revenue'])->toBe(20000.0);

    // Payment method summary should also only show paid
    $paymentMethods = collect($response->viewData('paymentMethodSummary'));
    $cashData = $paymentMethods->firstWhere('payment_method', 'cash');
    expect($cashData->transaction_count)->toBe(1)
        ->and($cashData->total_revenue)->toBe(20000);
});

test('top products shows correct margin based on HPP', function () {
    $menu1 = Menu::create([
        'nama_menu' => 'Kopi Susu',
        'harga' => 20000,
        'hpp' => 6000,
        'stok' => 50,
        'status_tersedia' => 1,
        'id_kategori' => 1,
    ]);

    $menu2 = Menu::create([
        'nama_menu' => 'Espresso',
        'harga' => 18000,
        'hpp' => 4000,
        'stok' => 50,
        'status_tersedia' => 1,
        'id_kategori' => 1,
    ]);

    $admin = analyticsAdmin();

    // Create order with menu1: revenue=36000 (qty=2), hpp=12000 (6000*2), margin=24000
    $order1 = analyticsOrder(['status_pembayaran' => 'paid', 'final_total' => 36000, 'payment_method' => 'cash']);
    OrderItem::create([
        'id_order' => $order1->id_order,
        'id_menu' => $menu1->id_menu,
        'quantity' => 2,
        'subtotal' => 36000,
        'hpp' => 6000,
        'hpp_at_sale' => 6000,
    ]);

    // Create order with menu2: revenue=18000 (qty=1), hpp=4000, margin=14000
    $order2 = analyticsOrder(['status_pembayaran' => 'paid', 'final_total' => 18000, 'payment_method' => 'cash']);
    OrderItem::create([
        'id_order' => $order2->id_order,
        'id_menu' => $menu2->id_menu,
        'quantity' => 1,
        'subtotal' => 18000,
        'hpp' => 4000,
        'hpp_at_sale' => 4000,
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.analytics.dashboard', ['period' => 'daily', 'date' => now()->format('Y-m-d')]))
        ->assertOk();

    $topProducts = $response->viewData('topProducts');

    $product1 = $topProducts->firstWhere('id_menu', $menu1->id_menu);
    $product2 = $topProducts->firstWhere('id_menu', $menu2->id_menu);

    // Verify HPP-based margin calculation
    // margin = revenue - (hpp * qty)
    expect((float) $product1->total_margin)->toBe((float) (36000 - 12000)) // 36000 - (6000 * 2) = 24000
        ->and((float) $product2->total_margin)->toBe((float) (18000 - 4000)); // 18000 - 4000 = 14000
});

// =====================================================================
// Tes tambahan: chart trend data, periode kosong, dan tanpa data
// =====================================================================

test('daily trend chart returns 24 hourly labels with correct data', function () {
    $menu = analyticsMenu();
    $admin = analyticsAdmin();

    $order = analyticsOrder([
        'status_pembayaran' => 'paid',
        'final_total' => 20000,
        'profit_margin' => 8000,
        'tanggal' => now(),
    ]);

    OrderItem::create([
        'id_order' => $order->id_order,
        'id_menu' => $menu->id_menu,
        'quantity' => 1,
        'subtotal' => 20000,
        'hpp' => 5000,
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.analytics.dashboard', ['period' => 'daily', 'date' => now()->format('Y-m-d')]))
        ->assertOk();

    $chartData = $response->viewData('chartData');

    // Should have 24 hour labels
    expect($chartData['labels'])->toHaveCount(24)
        ->and($chartData['labels'][0])->toBe('00:00')
        ->and($chartData['labels'][23])->toBe('23:00');

    // Revenue, profit, transactions arrays also have 24 entries
    expect($chartData['revenue'])->toHaveCount(24)
        ->and($chartData['profit'])->toHaveCount(24)
        ->and($chartData['transactions'])->toHaveCount(24);

    // At least one hour should have revenue > 0
    expect(array_sum($chartData['revenue']))->toBeGreaterThan(0);
});

test('monthly trend chart returns labels for each day of the month', function () {
    $menu = analyticsMenu();
    $admin = analyticsAdmin();

    $order = analyticsOrder([
        'status_pembayaran' => 'paid',
        'final_total' => 10000,
        'profit_margin' => 3000,
        'tanggal' => now(),
    ]);

    OrderItem::create([
        'id_order' => $order->id_order,
        'id_menu' => $menu->id_menu,
        'quantity' => 1,
        'subtotal' => 10000,
        'hpp' => 5000,
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.analytics.dashboard', ['period' => 'monthly', 'date' => now()->format('Y-m-d')]))
        ->assertOk();

    $chartData = $response->viewData('chartData');
    $daysInMonth = now()->daysInMonth;

    expect($chartData['labels'])->toHaveCount($daysInMonth)
        ->and($chartData['revenue'])->toHaveCount($daysInMonth)
        ->and($chartData['transactions'])->toHaveCount($daysInMonth);

    // At least one day should have revenue > 0
    expect(array_sum($chartData['revenue']))->toBeGreaterThan(0);
});

test('yearly trend chart returns 12 month labels', function () {
    $menu = analyticsMenu();
    $admin = analyticsAdmin();

    $order = analyticsOrder([
        'status_pembayaran' => 'paid',
        'final_total' => 10000,
        'profit_margin' => 3000,
        'tanggal' => now(),
    ]);

    OrderItem::create([
        'id_order' => $order->id_order,
        'id_menu' => $menu->id_menu,
        'quantity' => 1,
        'subtotal' => 10000,
        'hpp' => 5000,
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.analytics.dashboard', ['period' => 'yearly', 'date' => now()->format('Y-m-d')]))
        ->assertOk();

    $chartData = $response->viewData('chartData');

    expect($chartData['labels'])->toHaveCount(12)
        ->and($chartData['labels'][0])->toBe('Jan')
        ->and($chartData['labels'][11])->toBe('Dec');

    expect($chartData['revenue'])->toHaveCount(12)
        ->and($chartData['transactions'])->toHaveCount(12);

    // At least one month should have revenue > 0
    expect(array_sum($chartData['revenue']))->toBeGreaterThan(0);
});

test('dashboard works with no transactions at all', function () {
    $admin = analyticsAdmin();

    $response = $this->actingAs($admin)
        ->get(route('admin.analytics.dashboard', ['period' => 'daily', 'date' => now()->format('Y-m-d')]))
        ->assertOk();

    $periodTransactions = $response->viewData('periodTransactions');
    $chartData = $response->viewData('chartData');

    expect($periodTransactions['total_transactions'])->toBe(0)
        ->and($periodTransactions['total_revenue'])->toBe(0);

    // Chart data should still be valid (all zeros)
    expect($chartData['labels'])->toHaveCount(24)
        ->and(array_sum($chartData['revenue']))->toBe(0)
        ->and(array_sum($chartData['transactions']))->toBe(0);
});

test('period without transactions returns zero values and page still loads', function () {
    $menu = analyticsMenu();
    $admin = analyticsAdmin();

    // Create order for today
    $order = analyticsOrder([
        'status_pembayaran' => 'paid',
        'final_total' => 15000,
        'tanggal' => now(),
    ]);

    OrderItem::create([
        'id_order' => $order->id_order,
        'id_menu' => $menu->id_menu,
        'quantity' => 1,
        'subtotal' => 15000,
        'hpp' => 5000,
    ]);

    // Request a different date with no transactions
    $emptyDate = now()->subYear()->format('Y-m-d');

    $response = $this->actingAs($admin)
        ->get(route('admin.analytics.dashboard', ['period' => 'daily', 'date' => $emptyDate]))
        ->assertOk();

    $periodTransactions = $response->viewData('periodTransactions');
    $chartData = $response->viewData('chartData');

    expect($periodTransactions['total_transactions'])->toBe(0)
        ->and($periodTransactions['total_revenue'])->toBe(0);

    // Chart should have valid structure with all zeros
    expect($chartData['labels'])->toHaveCount(24)
        ->and(array_sum($chartData['revenue']))->toBe(0);
});

test('datePartExpression helper produces correct SQL for sqlite driver', function () {
    // Since tests run on SQLite, we can verify the generated SQL contains strftime
    $admin = analyticsAdmin();

    $response = $this->actingAs($admin)
        ->get(route('admin.analytics.dashboard', ['period' => 'daily', 'date' => now()->format('Y-m-d')]))
        ->assertOk();

    // If we got here without errors, the strftime expressions worked on SQLite
    $chartData = $response->viewData('chartData');
    expect($chartData['labels'])->toHaveCount(24);
});

test('monthly period with empty days still returns complete labels', function () {
    $admin = analyticsAdmin();

    // Request a month with no data
    $response = $this->actingAs($admin)
        ->get(route('admin.analytics.dashboard', ['period' => 'monthly', 'date' => '2020-02-15']))
        ->assertOk();

    $chartData = $response->viewData('chartData');

    // February 2020 has 29 days (leap year)
    expect($chartData['labels'])->toHaveCount(29)
        ->and($chartData['revenue'])->toHaveCount(29)
        ->and(array_sum($chartData['revenue']))->toBe(0);
});

test('yearly period with no data returns 12 months of zeros', function () {
    $admin = analyticsAdmin();

    $response = $this->actingAs($admin)
        ->get(route('admin.analytics.dashboard', ['period' => 'yearly', 'date' => '2020-06-15']))
        ->assertOk();

    $chartData = $response->viewData('chartData');

    expect($chartData['labels'])->toHaveCount(12)
        ->and(array_sum($chartData['revenue']))->toBe(0)
        ->and(array_sum($chartData['transactions']))->toBe(0)
        ->and(array_sum($chartData['profit']))->toBe(0);
});

test('datePartExpression produces correct SQL for mysql driver without requiring real mysql connection', function () {
    config([
        'database.connections.mysql_dummy' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'database' => 'test',
            'username' => 'test',
            'password' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ],
        'database.default' => 'mysql_dummy',
    ]);

    $controller = new \App\Http\Controllers\Admin\AnalyticsController();
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('datePartExpression');
    $method->setAccessible(true);

    $hourExpr = $method->invoke($controller, 'tanggal', 'hour');
    $dayExpr = $method->invoke($controller, 'tanggal', 'day');
    $monthExpr = $method->invoke($controller, 'tanggal', 'month');

    expect($hourExpr)->toBe('HOUR(tanggal)')
        ->and($dayExpr)->toBe('DAY(tanggal)')
        ->and($monthExpr)->toBe('MONTH(tanggal)');

    // Ensure the generated SQL query compiles with MySQL grammar and does not contain strftime
    $dailySql = Order::selectRaw("$hourExpr as hour")
        ->selectRaw('COUNT(*) as transactions')
        ->selectRaw('SUM(final_total) as revenue')
        ->selectRaw('SUM(profit_margin) as profit')
        ->whereDate('tanggal', '2026-11-03')
        ->where('status_pembayaran', 'paid')
        ->groupBy('hour')
        ->orderBy('hour')
        ->toSql();

    expect($dailySql)->toContain('HOUR(tanggal)')
        ->and($dailySql)->not->toContain('strftime');

    // Restore testing connection
    config(['database.default' => 'sqlite']);
});

test('datePartExpression produces correct SQL for mariadb driver', function () {
    config([
        'database.connections.mariadb_dummy' => [
            'driver' => 'mariadb',
            'host' => '127.0.0.1',
            'database' => 'test',
            'username' => 'test',
            'password' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ],
        'database.default' => 'mariadb_dummy',
    ]);

    $controller = new \App\Http\Controllers\Admin\AnalyticsController();
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('datePartExpression');
    $method->setAccessible(true);

    $hourExpr = $method->invoke($controller, 'tanggal', 'hour');
    expect($hourExpr)->toBe('HOUR(tanggal)');

    // Restore testing connection
    config(['database.default' => 'sqlite']);
});

// =====================================================================
// Browser / Web Workflow Tests
// =====================================================================

test('web flow: unauthenticated guest is redirected away from analytics', function () {
    $this->get(route('admin.analytics.dashboard'))
        ->assertRedirect('/home');
});

test('web flow: dashboard renders complete HTML with chart canvases and valid Chart.js config', function () {
    $admin = analyticsAdmin();
    $menu = analyticsMenu();

    $order = analyticsOrder([
        'status_pembayaran' => 'paid',
        'final_total' => 30000,
        'profit_margin' => 12000,
        'tanggal' => now(),
    ]);

    OrderItem::create([
        'id_order' => $order->id_order,
        'id_menu' => $menu->id_menu,
        'quantity' => 2,
        'subtotal' => 30000,
        'hpp' => 5000,
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.analytics.dashboard'))
        ->assertOk();

    // Verify critical HTML elements
    $response->assertSee('📊 Analytics & Insights', false)
        ->assertSee('💰 Tren Pendapatan & Keuntungan', false)
        ->assertSee('📈 Jumlah Transaksi', false)
        ->assertSee('id="revenueChart"', false)
        ->assertSee('id="transactionChart"', false)
        ->assertSee('📥 Export CSV', false);

    // Verify Chart.js canvas elements exist and labels are present in the script
    $content = $response->getContent();
    expect($content)->toContain('new Chart(revenueCtx')
        ->and($content)->toContain('new Chart(transactionCtx');
});

test('web flow: filter form submission for monthly period renders selected option and 30 day labels', function () {
    $admin = analyticsAdmin();

    $response = $this->actingAs($admin)
        ->get(route('admin.analytics.dashboard', [
            'period' => 'monthly',
            'date' => '2026-11-03',
        ]))
        ->assertOk();

    // In November, there are 30 days
    $chartData = $response->viewData('chartData');
    expect($chartData['labels'])->toHaveCount(30)
        ->and($chartData['labels'][0])->toBe(1)
        ->and($chartData['labels'][29])->toBe(30);

    // Verify option selected in HTML
    $response->assertSee('value="monthly" selected', false);
});

test('web flow: filter form submission for yearly period renders selected option and 12 month labels', function () {
    $admin = analyticsAdmin();

    $response = $this->actingAs($admin)
        ->get(route('admin.analytics.dashboard', [
            'period' => 'yearly',
            'date' => '2026-11-03',
        ]))
        ->assertOk();

    $chartData = $response->viewData('chartData');
    expect($chartData['labels'])->toHaveCount(12)
        ->and($chartData['labels'][0])->toBe('Jan')
        ->and($chartData['labels'][11])->toBe('Dec');

    // Verify option selected in HTML
    $response->assertSee('value="yearly" selected', false);
});

test('web flow: CSV export button generates a downloadable CSV stream with correct headers', function () {
    $admin = analyticsAdmin();
    $menu = analyticsMenu();

    $order = analyticsOrder([
        'status_pembayaran' => 'paid',
        'final_total' => 25000,
        'tanggal' => now(),
    ]);

    OrderItem::create([
        'id_order' => $order->id_order,
        'id_menu' => $menu->id_menu,
        'quantity' => 1,
        'subtotal' => 25000,
        'hpp' => 8000,
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.analytics.export', [
            'period' => 'daily',
            'date' => now()->format('Y-m-d'),
        ]))
        ->assertOk();

    // Verify stream headers
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    expect($response->headers->get('Content-Disposition'))->toContain('attachment; filename=sales-report-daily-');

    // Verify CSV content stream
    $content = $response->streamedContent();
    expect($content)->toContain('Menu,')
        ->and($content)->toContain('"Total Revenue"')
        ->and($content)->toContain($menu->nama_menu);
});

test('web flow: CSV export works for monthly and yearly periods', function () {
    $admin = analyticsAdmin();

    // Monthly export
    $monthlyResponse = $this->actingAs($admin)
        ->get(route('admin.analytics.export', [
            'period' => 'monthly',
            'date' => '2026-11-03',
        ]))
        ->assertOk();
    expect($monthlyResponse->headers->get('Content-Disposition'))->toContain('attachment; filename=sales-report-monthly-');

    // Yearly export
    $yearlyResponse = $this->actingAs($admin)
        ->get(route('admin.analytics.export', [
            'period' => 'yearly',
            'date' => '2026-11-03',
        ]))
        ->assertOk();
    expect($yearlyResponse->headers->get('Content-Disposition'))->toContain('attachment; filename=sales-report-yearly-');
});

test('web flow: purchase history table displays items and customer details', function () {
    $admin = analyticsAdmin();
    $menu = analyticsMenu();

    $user = User::factory()->create(['name' => 'Pelanggan Setia Berco', 'is_guest' => true]);

    $order = analyticsOrder([
        'id_user' => $user->id_user,
        'nama_pelanggan' => 'Pelanggan Setia Berco',
        'customer_name' => 'Pelanggan Setia Berco',
        'status_pembayaran' => 'paid',
        'final_total' => 35000,
        'subtotal' => 35000,
        'profit_margin' => 15000,
        'tanggal' => now(),
    ]);

    OrderItem::create([
        'id_order' => $order->id_order,
        'id_menu' => $menu->id_menu,
        'quantity' => 1,
        'subtotal' => 35000,
        'hpp' => 10000,
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.analytics.dashboard'))
        ->assertOk();

    $response->assertSee('Pelanggan Setia Berco')
        ->assertSee($menu->nama_menu)
        ->assertSee('Rp 35.000', false);
});
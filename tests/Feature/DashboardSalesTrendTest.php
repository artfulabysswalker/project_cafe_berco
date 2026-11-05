<?php

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Role;
use App\Models\User;

function trendAdminUser(): User
{
    $role = Role::firstOrCreate(['role_name' => 'Admin']);

    return User::factory()->create(['id_role' => $role->id_role, 'status' => 'active']);
}

function trendMenuItem(): Menu
{
    return Menu::create([
        'nama_menu' => 'Kopi Berco',
        'harga' => 20000,
        'hpp' => 5000,
        'stok' => 50,
        'status_tersedia' => 1,
        'id_kategori' => 1,
    ]);
}

test('dashboard loads 7 days sales trend by default and only includes completed or paid orders', function () {
    $admin = trendAdminUser();
    $menu = trendMenuItem();

    // Create a paid order for today
    $todayOrder = Order::create([
        'tanggal' => now(),
        'nama_pelanggan' => 'Pembeli Hari Ini',
        'total_harga' => 40000,
        'final_total' => 40000,
        'profit_margin' => 30000,
        'status_pembayaran' => 'paid',
        'status_order' => 'completed',
        'payment_method' => 'cash',
        'id_user' => $admin->id_user,
    ]);

    OrderItem::create([
        'id_order' => $todayOrder->id_order,
        'id_menu' => $menu->id_menu,
        'quantity' => 2,
        'subtotal' => 40000,
        'hpp' => 5000,
    ]);

    // Create a cancelled unpaid order for today (must be ignored in trend)
    Order::create([
        'tanggal' => now(),
        'nama_pelanggan' => 'Pembeli Batal',
        'total_harga' => 50000,
        'final_total' => 50000,
        'status_pembayaran' => 'pending',
        'status_order' => 'cancelled',
        'payment_method' => 'cash',
        'id_user' => $admin->id_user,
    ]);

    $response = $this->actingAs($admin)->get(route('control.dashboard'));
    $response->assertOk();

    $dates = $response->viewData('sevenDaysDates');
    $revenue = $response->viewData('sevenDaysRevenue');
    $orders = $response->viewData('sevenDaysOrders');

    expect($dates)->toHaveCount(7)
        ->and($revenue)->toHaveCount(7)
        ->and($orders)->toHaveCount(7)
        // Today's revenue must be exactly 40000, ignoring the cancelled 50000
        ->and($revenue->last())->toBe(40000)
        ->and($orders->last())->toBe(1);
});

test('dashboard supports 30_days range filter', function () {
    $admin = trendAdminUser();

    $response = $this->actingAs($admin)->get(route('control.dashboard', ['range' => '30_days']));
    $response->assertOk();

    $dates = $response->viewData('sevenDaysDates');
    $range = $response->viewData('trendRange');

    expect($range)->toBe('30_days')
        ->and($dates)->toHaveCount(30);
});

test('dashboard supports recent active range anchor when past orders exist', function () {
    $admin = trendAdminUser();
    $pastDate = now()->subDays(15);

    Order::create([
        'tanggal' => $pastDate,
        'nama_pelanggan' => 'Pelanggan Lampau',
        'total_harga' => 25000,
        'final_total' => 25000,
        'status_pembayaran' => 'paid',
        'status_order' => 'completed',
        'payment_method' => 'cash',
        'id_user' => $admin->id_user,
    ]);

    $response = $this->actingAs($admin)->get(route('control.dashboard', ['range' => 'recent']));
    $response->assertOk();

    $range = $response->viewData('trendRange');
    $revenue = $response->viewData('sevenDaysRevenue');

    expect($range)->toBe('recent')
        ->and($revenue->sum())->toBe(25000);
});

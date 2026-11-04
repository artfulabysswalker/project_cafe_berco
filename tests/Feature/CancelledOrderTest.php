<?php

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Role;
use App\Models\User;

function cancelledOrderStaff(): User
{
    $role = Role::firstOrCreate(['role_name' => 'Staff']);

    return User::factory()->create(['id_role' => $role->id_role, 'status' => 'active']);
}

function cancelledOrder(array $overrides = []): Order
{
    $user = User::factory()->create(['is_guest' => true]);

    return Order::create(array_merge([
        'tanggal' => now(),
        'nama_pelanggan' => 'Pelanggan Batal',
        'customer_name' => 'Pelanggan Batal',
        'total_harga' => 25000,
        'subtotal' => 25000,
        'final_total' => 25000,
        'status_pembayaran' => 'pending',
        'status_order' => 'cancelled',
        'service_type' => 'dine_in',
        'payment_method' => 'cash',
        'id_user' => $user->id_user,
    ], $overrides));
}

test('a cancelled order is hidden from the cashier list and polling', function () {
    $staff = cancelledOrderStaff();
    $cancelled = cancelledOrder();
    $active = cancelledOrder(['status_order' => 'pending']);

    $this->actingAs($staff)
        ->get(route('admin.orders'))
        ->assertOk()
        ->assertSee($active->public_code)
        ->assertDontSee($cancelled->public_code);

    $this->actingAs($staff)
        ->get(route('admin.orders.poll', ['after_id' => 0]))
        ->assertOk()
        ->assertJsonFragment(['id' => $active->id_order])
        ->assertJsonMissing(['id' => $cancelled->id_order]);
});

test('a cancelled order is hidden from the live order feed', function () {
    $staff = cancelledOrderStaff();
    $cancelled = cancelledOrder();
    $active = cancelledOrder(['status_order' => 'pending']);

    $this->actingAs($staff)
        ->get(route('admin.orders.live', ['after_id' => 0]))
        ->assertOk()
        ->assertJsonFragment(['id_order' => $active->id_order])
        ->assertJsonMissing(['id_order' => $cancelled->id_order]);
});

test('a cancelled order cannot be settled through the cash confirmation endpoint', function () {
    $staff = cancelledOrderStaff();
    $order = cancelledOrder();

    $this->actingAs($staff)
        ->from(route('admin.orders'))
        ->post(route('admin.orders.confirm', $order->id_order), ['uang_diterima' => 25000])
        ->assertRedirect(route('admin.orders'))
        ->assertSessionHasErrors('payment_method');

    $order->refresh();
    expect($order->isCancelled())->toBeTrue()
        ->and($order->isPaid())->toBeFalse()
        ->and($order->status_order)->toBe('cancelled')
        ->and($order->status_pembayaran)->toBe('pending')
        ->and($order->uang_diterima)->toBeNull();
});

test('a midtrans settlement cannot revive a cancelled order', function () {
    $order = cancelledOrder(['payment_method' => 'qris', 'total_harga' => 25000]);
    $log = qrisPaymentLogFor($order);

    $this->postJson(route('midtrans.webhook'), midtransNotificationFor($log))
        ->assertOk();

    $order->refresh();
    expect($order->status_order)->toBe('cancelled')
        ->and($order->isPaid())->toBeFalse()
        ->and($order->payment_status)->toBeNull();
});

test('a cancelled order cannot be finished or completed', function () {
    $staff = cancelledOrderStaff();
    $order = cancelledOrder([
        'status_pembayaran' => 'paid',
        'status_order' => 'cancelled',
    ]);

    $this->actingAs($staff)
        ->post(route('order.finish', $order->id_order))
        ->assertRedirect();

    expect($order->fresh()->status_order)->toBe('cancelled');

    $this->actingAs($staff)
        ->put(route('admin.orders.complete', $order->id_order))
        ->assertRedirect();

    expect($order->fresh()->status_order)->toBe('cancelled');
});

test('a cancelled order never appears in the kitchen queue', function () {
    $staff = cancelledOrderStaff();
    $order = cancelledOrder([
        'status_pembayaran' => 'paid',
        'status_order' => 'processing',
    ]);
    $order->update(['status_order' => 'cancelled']);

    $menu = Menu::create([
        'nama_menu' => 'Menu Dapur Uji',
        'harga' => 25000,
        'stok' => 10,
        'status_tersedia' => 1,
        'id_kategori' => Category::firstOrCreate(['nama_kategori' => 'Uji Dapur'])->id,
    ]);
    OrderItem::create([
        'id_order' => $order->id_order,
        'id_menu' => $menu->id_menu,
        'quantity' => 1,
        'subtotal' => 25000,
    ]);

    $this->actingAs($staff)
        ->get(route('admin.kitchen'))
        ->assertOk()
        ->assertDontSee($order->public_code);
});

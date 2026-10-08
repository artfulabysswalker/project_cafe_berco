<?php

use App\Events\OrderCreated;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $this->adminRole = Role::firstOrCreate(['role_name' => 'Admin']);
    $this->staffRole = Role::firstOrCreate(['role_name' => 'Staff']);
    $this->customerRole = Role::firstOrCreate(['role_name' => 'Customer']);

    $this->staffUser = User::factory()->create([
        'id_role' => $this->staffRole->id_role,
        'status' => 'active',
    ]);

    $this->customerUser = User::factory()->create([
        'id_role' => $this->customerRole->id_role,
        'status' => 'active',
    ]);
});

test('order created event has correct broadcast payload and channel', function () {
    $category = Category::create([
        'nama_kategori' => 'Coffee',
    ]);

    $menu = Menu::create([
        'nama_menu' => 'Espresso Hot',
        'harga' => 25000,
        'stok' => 50,
        'id_kategori' => $category->id_kategori,
        'status_tersedia' => 1,
    ]);

    $order = Order::create([
        'tanggal' => now(),
        'nama_pelanggan' => 'Andi Table 03',
        'total_harga' => 50000,
        'subtotal' => 50000,
        'status_pembayaran' => 'pending',
        'service_type' => 'dine_in',
        'payment_method' => 'cash',
        'notes' => 'Meja 03 - tanpa gula',
        'status_order' => 'pending',
        'id_user' => $this->customerUser->id_user,
    ]);

    OrderItem::create([
        'id_order' => $order->id_order,
        'id_menu' => $menu->id_menu,
        'quantity' => 2,
        'subtotal' => 50000,
        'temperature' => 'hot',
        'note' => 'tanpa gula',
    ]);

    $event = new OrderCreated($order);

    // Assert Channel
    $channels = $event->broadcastOn();
    expect($channels)->toHaveCount(1);
    expect($channels[0]->name)->toBe('private-cashier.orders');

    // Assert Payload
    $payload = $event->broadcastWith();
    expect($payload['id_order'])->toBe($order->id_order)
        ->and($payload['order_code'])->toBe('#ORD-'.$order->id_order)
        ->and($payload['table_number'])->toBe('Meja 03')
        ->and($payload['customer_name'])->toBe('Andi Table 03')
        ->and($payload['total_amount'])->toBe(50000.0)
        ->and($payload['items'])->toHaveCount(1)
        ->and($payload['items'][0]['name'])->toContain('Espresso Hot')
        ->and($payload['items'][0]['quantity'])->toBe(2);
});

test('live orders endpoint returns new orders for staff and cashier', function () {
    $order = Order::create([
        'tanggal' => now(),
        'nama_pelanggan' => 'Budi Meja 05',
        'total_harga' => 35000,
        'subtotal' => 35000,
        'status_pembayaran' => 'pending',
        'service_type' => 'dine_in',
        'payment_method' => 'qris',
        'notes' => 'Meja 05',
        'status_order' => 'pending',
        'id_user' => $this->customerUser->id_user,
    ]);

    $this->actingAs($this->staffUser);

    $response = $this->getJson(route('admin.orders.live', ['after_id' => 0]));

    $response->assertOk()
        ->assertJsonStructure([
            'success',
            'orders',
            'max_id',
            'total_pending',
        ])
        ->assertJsonFragment([
            'id_order' => $order->id_order,
            'table_number' => 'Meja 05',
        ]);
});

test('live orders endpoint is forbidden for customer users', function () {
    $this->actingAs($this->customerUser);

    $response = $this->getJson(route('admin.orders.live'));

    // Customer Middleware or AdminMiddleware redirects/aborts unauthorized
    expect(in_array($response->status(), [403, 302]))->toBeTrue();
});

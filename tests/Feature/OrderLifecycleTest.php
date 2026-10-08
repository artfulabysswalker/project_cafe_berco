<?php

use App\Models\Category;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\QrisTransaction;
use App\Models\Role;
use App\Models\User;

function lifecycleMenu(): Menu
{
    return Menu::create([
        'nama_menu' => 'Mie Goreng Level',
        'harga' => 18000,
        'stok' => 60,
        'status_tersedia' => 1,
        'id_kategori' => Category::firstOrCreate(['nama_kategori' => 'Mie'])->id,
    ]);
}

function lifecycleGuest(string $name = 'Pelanggan Tes'): User
{
    return User::factory()->create(['name' => $name, 'is_guest' => true]);
}

function lifecycleOrder(array $overrides = []): Order
{
    $user = lifecycleGuest();

    return Order::create(array_merge([
        'tanggal' => now(),
        'nama_pelanggan' => 'Pelanggan Tes',
        'customer_name' => 'Pelanggan Tes',
        'total_harga' => 18000,
        'subtotal' => 18000,
        'final_total' => 18000,
        'status_pembayaran' => 'pending',
        'status_order' => 'pending',
        'service_type' => 'dine_in',
        'payment_method' => 'cash',
        'id_user' => $user->id_user,
    ], $overrides));
}

function lifecycleStaff(): User
{
    $role = Role::firstOrCreate(['role_name' => 'Staff']);

    return User::factory()->create(['id_role' => $role->id_role]);
}

test('cash order is created pending payment with a public tagihan code', function () {
    $menu = lifecycleMenu();

    $this->postJson(route('cart.add'), ['product_id' => $menu->id_menu, 'quantity' => 2])->assertOk();

    $response = $this->postJson(route('order.store'), [
        'service_type' => 'dine_in',
        'payment_method' => 'cash',
        'customer_name' => 'Budi',
    ])->assertOk();

    $order = Order::latest('id_order')->firstOrFail();

    $this->assertSame('pending', $order->status_pembayaran);
    $this->assertSame('pending', $order->status_order);
    $this->assertNotNull($order->order_code);
    $this->assertSame('BT-'.str_pad((string) $order->id_order, 4, '0', STR_PAD_LEFT), $order->order_code);

    $data = $response->json();
    $this->assertSame(route('order.waiting', $order->id_order), $data['redirect']);
});

test('per-item notes from the cart are stored on the order item', function () {
    $menu = lifecycleMenu();

    $this->postJson(route('cart.add'), [
        'product_id' => $menu->id_menu,
        'quantity' => 1,
        'notes' => 'Level pedas 5, tambah topping',
    ])->assertOk();

    $item = auth()->user()->cartItems()->first();
    $this->assertSame('Level pedas 5, tambah topping', $item->notes);

    $this->postJson(route('order.store'), [
        'service_type' => 'dine_in',
        'payment_method' => 'cash',
        'customer_name' => 'Budi',
    ])->assertOk();

    $order = Order::latest('id_order')->firstOrFail();
    $this->assertSame('Level pedas 5, tambah topping', $order->items()->first()->notes);
});

test('the waiting page shows the tagihan code and belongs to the owner only', function () {
    $order = lifecycleOrder();

    // Pemilik (guest yang sama)
    $this->actingAs($order->user)
        ->get(route('order.waiting', $order->id_order))
        ->assertOk()
        ->assertSee($order->public_code)
        ->assertSee('Bayar di Kasir');

    // Orang lain tidak boleh melihatnya
    $other = lifecycleGuest('Orang Lain');
    $this->actingAs($other)
        ->get(route('order.waiting', $order->id_order))
        ->assertForbidden();
});

test('the tracking page shows the status timeline and polls json status', function () {
    $order = lifecycleOrder([
        'status_pembayaran' => 'paid',
        'status_order' => 'processing',
        'paid_at' => now(),
    ]);

    $this->actingAs($order->user)
        ->get(route('order.show', $order->id_order))
        ->assertOk()
        ->assertSee('Diproses Dapur')
        ->assertSee($order->public_code);

    $this->actingAs($order->user)
        ->get(route('order.status', $order->id_order))
        ->assertOk()
        ->assertJson([
            'order_code' => $order->public_code,
            'status_order' => 'processing',
            'paid' => true,
        ]);
});

test('tracking page is forbidden for another guest', function () {
    $order = lifecycleOrder();

    $this->actingAs(lifecycleGuest('Orang Lain'))
        ->get(route('order.show', $order->id_order))
        ->assertForbidden();
});

test('cashier can confirm a cash order: paid + processing then opens the thermal receipt', function () {
    $staff = lifecycleStaff();
    $order = lifecycleOrder();

    $this->actingAs($staff)
        ->post(route('admin.orders.confirm', $order->id_order))
        ->assertRedirect(route('admin.receipt.print', $order->id_order));

    $order->refresh();
    $this->assertTrue($order->isPaid());
    $this->assertSame('processing', $order->status_order);
    $this->assertNotNull($order->paid_at);
    $this->assertSame($staff->name, $order->cashier_name);
});

test('admin can see kitchen orders grouped per station and print kitchen tickets', function () {
    $admin = (function () {
        $role = Role::firstOrCreate(['role_name' => 'Admin']);

        return User::factory()->create(['id_role' => $role->id_role]);
    })();

    $meja = Meja::factory()->create(['nama_meja' => 'Meja 03']);
    $order = lifecycleOrder([
        'status_pembayaran' => 'paid',
        'status_order' => 'processing',
        'table_id' => $meja->id_meja,
    ]);

    $menu = lifecycleMenu();
    OrderItem::create([
        'id_order' => $order->id_order,
        'id_menu' => $menu->id_menu,
        'quantity' => 2,
        'notes' => 'Level pedas 5',
        'subtotal' => 36000,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.kitchen'))
        ->assertOk()
        ->assertSee('Dapur Mie')
        ->assertSee($order->public_code);

    $this->actingAs($admin)
        ->get(route('admin.orders.tickets', $order->id_order))
        ->assertOk()
        ->assertSee('Dapur Mie')
        ->assertSee('Mie Goreng Level')
        ->assertSee('Level pedas 5');
});

test('kitchen can mark an order ready and finish it', function () {
    $admin = (function () {
        $role = Role::firstOrCreate(['role_name' => 'Admin']);

        return User::factory()->create(['id_role' => $role->id_role]);
    })();

    $order = lifecycleOrder([
        'status_pembayaran' => 'paid',
        'status_order' => 'processing',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.orders.ready', $order->id_order))
        ->assertRedirect();

    $this->assertSame('ready', $order->fresh()->status_order);

    $this->actingAs($admin)
        ->post(route('order.finish', $order->id_order))
        ->assertRedirect();

    $this->assertSame('completed', $order->fresh()->status_order);
    $this->assertTrue($order->fresh()->isPaid());
});

test('polling returns only new pending orders after the given id', function () {
    $admin = lifecycleStaff();
    $first = lifecycleOrder();
    $second = lifecycleOrder();
    // Sudah lunas -> tidak ikut polling.
    lifecycleOrder(['status_pembayaran' => 'paid', 'status_order' => 'processing']);

    $this->actingAs($admin)
        ->get(route('admin.orders.poll', ['after_id' => $first->id_order]))
        ->assertOk()
        ->assertJson(['count' => 1])
        ->assertJsonFragment(['id' => $second->id_order])
        ->assertJsonMissing(['id' => $first->id_order]);
});

test('manual order create keeps notes and order items relation', function () {
    $order = lifecycleOrder();
    $menu = lifecycleMenu();

    $order->items()->create([
        'id_menu' => $menu->id_menu,
        'quantity' => 1,
        'notes' => 'Tanpa es',
        'subtotal' => 18000,
    ]);

    $this->assertSame('Tanpa es', $order->fresh()->items()->first()->notes);
});

test('qris settlement marks the order paid and pushes it to the kitchen', function () {
    $order = lifecycleOrder(['payment_method' => 'qris']);

    $qris = QrisTransaction::create([
        'id_order' => $order->id_order,
        'qris_code' => 'QRIS-'.uniqid(),
        'transaction_id' => 'txn-'.uniqid(),
        'amount' => 18000,
        'status' => 'pending',
    ]);

    $qris->markAsPaid();

    $order->refresh();
    $this->assertTrue($order->isPaid());
    $this->assertSame('processing', $order->status_order);
    $this->assertNotNull($order->paid_at);
});

test('cash order is not listed in kitchen until kasir confirms payment', function () {
    $admin = lifecycleStaff();
    $pending = lifecycleOrder(); // cash, belum dibayar
    $paid = lifecycleOrder([
        'status_pembayaran' => 'paid',
        'status_order' => 'processing',
    ]);

    $menu = lifecycleMenu();
    $paid->items()->create([
        'id_menu' => $menu->id_menu,
        'quantity' => 1,
        'subtotal' => 18000,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.kitchen'))
        ->assertOk()
        ->assertSee($paid->public_code)
        ->assertDontSee($pending->public_code);
});

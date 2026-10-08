<?php

use App\Models\Meja;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

beforeEach(function () {
    Storage::fake('public');
    config()->set('qrcode.disk', 'public');
});

function orderTokenAdmin(): User
{
    $role = Role::firstOrCreate(['role_name' => 'Admin']);

    return User::factory()->create(['id_role' => $role->id_role]);
}

function orderTokenMenu(): Menu
{
    return Menu::create([
        'nama_menu' => 'Kopi Order Token',
        'harga' => 15000,
        'stok' => 50,
        'status_tersedia' => 1,
    ]);
}

function orderTokenFor(Meja $table, ?int $expiresInHours = null): string
{
    return app(QrCodeService::class)->generateForTable($table->id_meja, $expiresInHours)->token;
}

function orderForTable(Meja $table, string $customer): Order
{
    return Order::create([
        'tanggal' => now(),
        'nama_pelanggan' => $customer,
        'customer_name' => $customer,
        'table_id' => $table->id_meja,
        'total_harga' => 50000,
        'id_user' => User::factory()->create()->id_user,
    ]);
}

test('a valid token opens the order page and locks the table into the session', function () {
    $meja = Meja::factory()->create(['nama_meja' => 'Meja 09']);
    $token = orderTokenFor($meja);

    $response = $this->get('/order?token='.$token)
        ->assertOk()
        ->assertSee('Meja 09')
        ->assertSee(route('menu.index'))
        ->assertSee('Berlaku sampai');

    $response->assertSessionHas('order_table_token', $token);
    $response->assertSessionHas('order_table', 'Meja 09');
    $response->assertSessionHas('order_table_id', $meja->id_meja);
});

test('a missing token shows a friendly error page without opening the menu', function () {
    $this->get('/order')
        ->assertNotFound()
        ->assertSee('silakan hubungi kasir')
        ->assertDontSee('Lihat Menu');

    expect(session()->has('order_table_token'))->toBeFalse();
});

test('an unknown token is rejected with the same friendly error', function () {
    $this->get('/order?token='.Str::uuid())
        ->assertNotFound()
        ->assertSee('silakan hubungi kasir');
});

test('a revoked qr token cannot open the order page', function () {
    $meja = Meja::factory()->create();
    $token = orderTokenFor($meja);

    app(QrCodeService::class)->revoke($token);

    $this->get('/order?token='.$token)
        ->assertNotFound()
        ->assertSee('silakan hubungi kasir');
});

test('an expired qr token cannot open the order page', function () {
    $meja = Meja::factory()->create();
    $token = orderTokenFor($meja, 1);

    $this->travel(2)->hours();

    $this->get('/order?token='.$token)
        ->assertNotFound()
        ->assertSee('silakan hubungi kasir');
});

test('an inactive table cannot be ordered even with a valid qr token', function () {
    $meja = Meja::factory()->create();
    $token = orderTokenFor($meja);

    $meja->update(['is_active' => false]);

    $this->get('/order?token='.$token)
        ->assertNotFound()
        ->assertSee('tidak aktif');
});

test('checkout requires a customer name', function () {
    $menu = orderTokenMenu();

    $this->postJson(route('cart.add'), ['product_id' => $menu->id_menu, 'quantity' => 1])->assertOk();

    $this->postJson(route('order.store'), [
        'service_type' => 'dine_in',
        'payment_method' => 'cash',
    ])->assertStatus(422)->assertJsonValidationErrors('customer_name');

    expect(Order::count())->toBe(0);
});

test('the customer can order without a phone and the table comes from the token', function () {
    $meja = Meja::factory()->create(['nama_meja' => 'Meja 05']);
    $token = orderTokenFor($meja);

    $this->get('/order?token='.$token)->assertOk();

    $menu = orderTokenMenu();
    $this->postJson(route('cart.add'), ['product_id' => $menu->id_menu, 'quantity' => 1])->assertOk();

    $this->postJson(route('order.store'), [
        'service_type' => 'dine_in',
        'payment_method' => 'cash',
        'customer_name' => 'Siti Aminah',
    ])->assertOk();

    $order = Order::sole();

    expect($order->customer_name)->toBe('Siti Aminah')
        ->and($order->customer_phone)->toBeNull()
        ->and($order->table_id)->toBe($meja->id_meja);
});

test('a phone number in the wrong format is rejected', function () {
    $menu = orderTokenMenu();

    $this->postJson(route('cart.add'), ['product_id' => $menu->id_menu, 'quantity' => 1])->assertOk();

    $this->postJson(route('order.store'), [
        'service_type' => 'dine_in',
        'payment_method' => 'cash',
        'customer_name' => 'Budi',
        'customer_phone' => 'bukan-nomor',
    ])->assertStatus(422)->assertJsonValidationErrors('customer_phone');

    expect(Order::count())->toBe(0);
});

test('form input cannot override the table that comes from the qr token', function () {
    $mejaA = Meja::factory()->create(['nama_meja' => 'Meja 01']);
    $mejaB = Meja::factory()->create(['nama_meja' => 'Meja 02']);
    $token = orderTokenFor($mejaA);

    $this->get('/order?token='.$token)->assertOk();

    $menu = orderTokenMenu();
    $this->postJson(route('cart.add'), ['product_id' => $menu->id_menu, 'quantity' => 1])->assertOk();

    $this->postJson(route('order.store'), [
        'service_type' => 'dine_in',
        'payment_method' => 'cash',
        'customer_name' => 'Penipu Meja',
        'table_id' => $mejaB->nama_meja,
        'meja' => $mejaB->nama_meja,
    ])->assertOk();

    expect(Order::sole()->table_id)->toBe($mejaA->id_meja);
});

test('admin can filter pending orders by table', function () {
    $admin = orderTokenAdmin();
    $mejaA = Meja::factory()->create();
    $mejaB = Meja::factory()->create();
    orderForTable($mejaA, 'Andi Wirawan');
    orderForTable($mejaB, 'Budi Santoso');

    $this->actingAs($admin)
        ->get(route('admin.orders', ['meja' => $mejaA->id_meja]))
        ->assertOk()
        ->assertSee('Andi Wirawan')
        ->assertDontSee('Budi Santoso');

    $this->get(route('admin.orders', ['meja' => 'all']))
        ->assertOk()
        ->assertSee('Andi Wirawan')
        ->assertSee('Budi Santoso');
});

test('admin can filter pending orders by qr token', function () {
    $admin = orderTokenAdmin();
    $mejaA = Meja::factory()->create();
    $mejaB = Meja::factory()->create();
    $tokenA = orderTokenFor($mejaA);
    orderTokenFor($mejaB);
    orderForTable($mejaA, 'Andi Wirawan');
    orderForTable($mejaB, 'Budi Santoso');

    $this->actingAs($admin)
        ->get(route('admin.orders', ['q' => $tokenA]))
        ->assertOk()
        ->assertSee('Andi Wirawan')
        ->assertDontSee('Budi Santoso');

    $this->get(route('admin.orders', ['q' => $mejaB->nama_meja]))
        ->assertOk()
        ->assertSee('Budi Santoso')
        ->assertDontSee('Andi Wirawan');
});

test('the per-table admin page lists only that tables orders', function () {
    $admin = orderTokenAdmin();
    $mejaA = Meja::factory()->create();
    $mejaB = Meja::factory()->create();
    orderForTable($mejaA, 'Andi Wirawan');
    orderForTable($mejaB, 'Budi Santoso');

    $this->actingAs($admin)
        ->get(route('admin.tables.orders', $mejaA->id_meja))
        ->assertOk()
        ->assertSee($mejaA->nama_meja)
        ->assertSee('Andi Wirawan')
        ->assertDontSee('Budi Santoso');
});

test('admin can deactivate and reactivate a table', function () {
    $admin = orderTokenAdmin();
    $meja = Meja::factory()->create();
    $token = orderTokenFor($meja);

    $this->actingAs($admin)
        ->patch(route('admin.tables.toggle-status', $meja->id_meja))
        ->assertRedirect();

    expect($meja->fresh()->is_active)->toBeFalse();

    $this->get('/order?token='.$token)->assertNotFound();

    $this->actingAs($admin)
        ->patch(route('admin.tables.toggle-status', $meja->id_meja))
        ->assertRedirect();

    expect($meja->fresh()->is_active)->toBeTrue();

    $this->get('/order?token='.$token)->assertOk();
});

test('admin can create a new table', function () {
    $admin = orderTokenAdmin();

    $this->actingAs($admin)
        ->post(route('admin.tables.store'), [
            'nama_meja' => 'Meja Baru 77',
            'kapasitas' => 4,
            'area' => 'Indoor',
        ])
        ->assertRedirect(route('admin.tables.index'));

    $meja = Meja::where('nama_meja', 'Meja Baru 77')->first();

    expect($meja)->not->toBeNull()
        ->and($meja->is_active)->toBeTrue()
        ->and($meja->kapasitas)->toBe(4);
});

test('the printable qr page shows the table name', function () {
    $admin = orderTokenAdmin();
    $meja = Meja::factory()->create(['nama_meja' => 'Meja Cetak']);
    orderTokenFor($meja);

    $this->actingAs($admin)
        ->get(route('admin.tables.qrcode.print', $meja->id_meja))
        ->assertOk()
        ->assertSee('Meja Cetak')
        ->assertSee('window.print()', false);
});

test('token probing on the order page is rate limited', function () {
    $throttled = false;

    foreach (range(1, 45) as $i) {
        $status = $this->get('/order?token=missing-'.$i)->getStatusCode();

        if ($status === 429) {
            $throttled = true;
            break;
        }

        if ($status !== 404) {
            $this->fail('Request #'.$i.' returned unexpected status '.$status);
        }
    }

    expect($throttled)->toBeTrue();
});

<?php

use App\Models\Category;
use App\Models\DiscountScheme;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;

function checkoutPricingMenu(int $price = 40000): Menu
{
    return Menu::create([
        'nama_menu' => 'Paket Hemat Uji',
        'harga' => $price,
        'stok' => 50,
        'status_tersedia' => 1,
        'id_kategori' => Category::firstOrCreate(['nama_kategori' => 'Uji'])->id,
    ]);
}

function checkoutPricingScheme(string $code, string $type, float $value, float $min = 0): DiscountScheme
{
    return DiscountScheme::create([
        'code' => $code,
        'name' => 'Promo Uji '.$code,
        'discount_type' => $type,
        'discount_value' => $value,
        'min_purchase' => $min,
        'is_active' => true,
        'valid_from' => now()->subDay(),
        'valid_until' => now()->addDay(),
        'id_user' => User::factory()->create()->id_user,
    ]);
}

function checkoutPricingStaff(): User
{
    $role = Role::firstOrCreate(['role_name' => 'Staff']);

    return User::factory()->create(['id_role' => $role->id_role, 'status' => 'active']);
}

test('checkout, stored order, and cashier charge all agree under the morning discount', function () {
    // 5% diskon pagi berlaku pada jam 06:00-11:00 (waktu server).
    $this->travelTo(now()->setTime(8, 0));

    $menu = checkoutPricingMenu(40000);
    $this->postJson(route('cart.add'), ['product_id' => $menu->id_menu, 'quantity' => 1])->assertOk();

    // Halaman checkout memakai nilai server: subtotal 40.000 - diskon 2.000 = 38.000.
    $this->get(route('checkout'))
        ->assertOk()
        ->assertSee('Rp 38.000')
        ->assertSee('Rp -2.000');

    // Nilai total/diskon palsu dari browser harus diabaikan total.
    $this->postJson(route('order.store'), [
        'service_type' => 'dine_in',
        'payment_method' => 'cash',
        'customer_name' => 'Pelanggan Uji',
        'total' => 1,
        'total_harga' => 1,
        'final_total' => 1,
        'discount' => 999999,
        'discount_amount' => 999999,
    ])->assertOk();

    $order = Order::latest('id_order')->firstOrFail();

    expect((int) $order->subtotal)->toBe(40000)
        ->and((int) $order->discount_amount)->toBe(2000)
        ->and((int) $order->tax_amount)->toBe(0)
        ->and((int) $order->total_harga)->toBe(38000)
        ->and((int) $order->final_total)->toBe(38000);

    // Kasir menagih angka yang sama dengan yang tersimpan (38.000).
    $staff = checkoutPricingStaff();
    $this->actingAs($staff)
        ->post(route('admin.orders.confirm', $order->id_order), ['uang_diterima' => 38000])
        ->assertRedirect(route('admin.receipt.print', $order->id_order));

    $order->refresh();
    expect($order->isPaid())->toBeTrue()
        ->and((int) $order->kembalian)->toBe(0);
});

test('an applied promo is charged once and never stacked with the morning discount', function () {
    $this->travelTo(now()->setTime(8, 0));

    $scheme = checkoutPricingScheme('UJI10', 'percentage', 10);
    $menu = checkoutPricingMenu(40000);

    $this->postJson(route('cart.add'), ['product_id' => $menu->id_menu, 'quantity' => 1])->assertOk();
    $this->postJson(route('cart.promo.apply'), ['code' => 'UJI10'])->assertOk();

    // 10% dari 40.000 = 4.000. Diskon pagi 2.000 TIDAK boleh ditambahkan lagi.
    $this->get(route('checkout'))->assertOk()->assertSee('Rp 36.000');

    $this->postJson(route('order.store'), [
        'service_type' => 'dine_in',
        'payment_method' => 'cash',
        'customer_name' => 'Pelanggan Uji',
    ])->assertOk();

    $order = Order::latest('id_order')->firstOrFail();

    expect((int) $order->discount_amount)->toBe(4000)
        ->and((int) $order->total_harga)->toBe(36000)
        ->and((int) $order->id_discount_scheme)->toBe($scheme->id_discount_scheme);
});

test('a promo that becomes invalid after being applied is ignored and never charged', function () {
    // Jam sore: diskon pagi tidak berlaku, jadi hanya promo yang diuji.
    $this->travelTo(now()->setTime(14, 0));

    $scheme = checkoutPricingScheme('UJIEXP', 'percentage', 25);
    $menu = checkoutPricingMenu(40000);

    $this->postJson(route('cart.add'), ['product_id' => $menu->id_menu, 'quantity' => 1])->assertOk();
    $this->postJson(route('cart.promo.apply'), ['code' => 'UJIEXP'])->assertOk();

    // Promo dimatikan setelah dipasang di keranjang.
    $scheme->update(['is_active' => false]);

    $this->get(route('checkout'))->assertOk()->assertSee('Rp 40.000');

    $this->postJson(route('order.store'), [
        'service_type' => 'dine_in',
        'payment_method' => 'cash',
        'customer_name' => 'Pelanggan Uji',
    ])->assertOk();

    $order = Order::latest('id_order')->firstOrFail();

    expect((int) $order->discount_amount)->toBe(0)
        ->and((int) $order->total_harga)->toBe(40000)
        ->and($order->id_discount_scheme)->toBeNull();
});

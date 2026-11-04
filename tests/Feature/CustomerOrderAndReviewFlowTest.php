<?php

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\Role;
use App\Models\User;

test('full customer workflow: browse menu, add to cart, order, and submit rating/review', function () {
    // 1. Prepare roles and users
    $customerRole = Role::firstOrCreate(['role_name' => 'customer']);
    $customer = User::factory()->create([
        'name' => 'Dewi Lestari',
        'id_role' => $customerRole->id_role,
        'status' => 'active',
        'is_guest' => false,
    ]);

    // 2. Prepare Category and Menu item
    $category = Category::create([
        'nama_kategori' => 'Specialty Coffee',
        'slug' => 'specialty-coffee',
    ]);

    $menu = Menu::create([
        'nama_menu' => 'Kopi Susu Gula Aren Berco',
        'id_kategori' => $category->id,
        'harga' => 18000,
        'hpp' => 7000,
        'stok' => 50,
        'status_tersedia' => 1,
        'has_temperature_option' => true,
        'temperature_options' => [
            ['type' => 'Ice', 'price' => 18000, 'hpp' => 7000],
            ['type' => 'Hot', 'price' => 18000, 'hpp' => 7000],
        ],
    ]);

    // 3. Step 1: Customer visits /menu
    $menuResponse = $this->actingAs($customer)
        ->get(route('menu.index'))
        ->assertOk()
        ->assertSee('Daftar Menu Berco')
        ->assertSee('Kopi Susu Gula Aren Berco')
        ->assertSee('Rating & Review Menu', false)
        ->assertSee('Tulis Ulasan & Rating', false);

    // 4. Step 2: Customer adds item to cart
    $cartResponse = $this->actingAs($customer)
        ->post(route('cart.add'), [
            'menu_id' => $menu->id_menu,
            'quantity' => 2,
            'temperature' => 'Ice',
            'note' => 'Less sugar please',
        ]);

    $cartResponse->assertOk()
        ->assertJson([
            'success' => true,
        ]);

    // 5. Step 3: Customer views cart
    $viewCartResponse = $this->actingAs($customer)
        ->get(route('cart.index'))
        ->assertOk()
        ->assertSee('Kopi Susu Gula Aren Berco');

    // 6. Step 4: Customer checks out / places an order
    $order = Order::create([
        'tanggal' => now(),
        'nama_pelanggan' => $customer->name,
        'customer_name' => $customer->name,
        'total_harga' => 36000,
        'subtotal' => 36000,
        'final_total' => 36000,
        'status_pembayaran' => 'paid',
        'status_order' => 'completed',
        'service_type' => 'dine_in',
        'payment_method' => 'cash',
        'id_user' => $customer->id_user,
    ]);

    OrderItem::create([
        'id_order' => $order->id_order,
        'id_menu' => $menu->id_menu,
        'quantity' => 2,
        'subtotal' => 36000,
        'temperature' => 'Ice',
        'note' => 'Less sugar please',
        'hpp' => 7000,
    ]);

    // 7. Step 5: Customer views receipt
    $receiptResponse = $this->actingAs($customer)
        ->get(route('order.receipt', $order->id_order))
        ->assertOk()
        ->assertSee('Pesanan Berhasil!')
        ->assertSee('Kopi Susu Gula Aren Berco')
        ->assertSee('Rp 36.000', false);

    // 8. Step 6: Customer submits rating and review for the ordered menu
    $reviewResponse = $this->actingAs($customer)
        ->post(route('reviews.store', $menu->id_menu), [
            'rating' => 5,
            'comment' => 'Kopi gula arennya luar biasa sedap dan pas manisnya!',
        ]);

    $reviewResponse->assertRedirect()
        ->assertSessionHas('success', 'Review berhasil ditambahkan!');

    // Verify review persisted in database
    $this->assertDatabaseHas('reviews', [
        'user_id' => $customer->id_user,
        'menu_id' => $menu->id_menu,
        'rating' => 5,
        'comment' => 'Kopi gula arennya luar biasa sedap dan pas manisnya!',
    ]);

    // 9. Step 7: Customer revisits /menu and verifies review is visible in the UI
    $revisitMenuResponse = $this->actingAs($customer)
        ->get(route('menu.index'))
        ->assertOk()
        ->assertSee('Dewi Lestari')
        ->assertSee('Kopi gula arennya luar biasa sedap dan pas manisnya!')
        ->assertSee('Total <strong>1</strong> Ulasan', false);
});

test('guest cannot submit review and is redirected to login', function () {
    $menu = Menu::create([
        'nama_menu' => 'Americano',
        'id_kategori' => 1,
        'harga' => 15000,
        'hpp' => 5000,
        'stok' => 50,
        'status_tersedia' => 1,
    ]);

    $this->post(route('reviews.store', $menu->id_menu), [
        'rating' => 5,
        'comment' => 'Harus login',
    ])->assertRedirect('/');
});

test('review validation requires rating between 1 and 5', function () {
    $customerRole = Role::firstOrCreate(['role_name' => 'customer']);
    $customer = User::factory()->create([
        'id_role' => $customerRole->id_role,
        'status' => 'active',
    ]);

    $menu = Menu::create([
        'nama_menu' => 'Americano',
        'id_kategori' => 1,
        'harga' => 15000,
        'hpp' => 5000,
        'stok' => 50,
        'status_tersedia' => 1,
    ]);

    // Rating 0 should fail
    $this->actingAs($customer)
        ->post(route('reviews.store', $menu->id_menu), [
            'rating' => 0,
        ])
        ->assertSessionHasErrors(['rating']);

    // Rating 6 should fail
    $this->actingAs($customer)
        ->post(route('reviews.store', $menu->id_menu), [
            'rating' => 6,
        ])
        ->assertSessionHasErrors(['rating']);

    // Rating string should fail
    $this->actingAs($customer)
        ->post(route('reviews.store', $menu->id_menu), [
            'rating' => 'bintang_lima',
        ])
        ->assertSessionHasErrors(['rating']);
});

test('customer can update their previous review for the same menu item', function () {
    $customerRole = Role::firstOrCreate(['role_name' => 'customer']);
    $customer = User::factory()->create([
        'name' => 'Budi',
        'id_role' => $customerRole->id_role,
        'status' => 'active',
    ]);

    $menu = Menu::create([
        'nama_menu' => 'V60 Pour Over',
        'id_kategori' => 1,
        'harga' => 20000,
        'hpp' => 8000,
        'stok' => 50,
        'status_tersedia' => 1,
    ]);

    // Initial review: 4 stars
    $this->actingAs($customer)
        ->post(route('reviews.store', $menu->id_menu), [
            'rating' => 4,
            'comment' => 'Cukup enak',
        ])
        ->assertRedirect();

    expect(Review::count())->toBe(1);

    // Update review: 5 stars
    $this->actingAs($customer)
        ->post(route('reviews.store', $menu->id_menu), [
            'rating' => 5,
            'comment' => 'Setelah coba lagi, ternyata luar biasa enak!',
        ])
        ->assertRedirect();

    // Still only 1 record (updated in place)
    expect(Review::count())->toBe(1);

    $updated = Review::first();
    expect($updated->rating)->toBe(5)
        ->and($updated->comment)->toBe('Setelah coba lagi, ternyata luar biasa enak!');
});

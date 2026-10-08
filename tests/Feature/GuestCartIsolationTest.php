<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use App\Services\GuestSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use Tests\TestCase;

/**
 * Setiap meja memindai QR dari perangkatnya sendiri, jadi keranjang antar
 * perangkat tidak boleh saling tertimpa.
 */
class GuestCartIsolationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Simulasikan perangkat baru: sesi kosong dan guard auth dilepas,
     * supaya request berikutnya benar-benar memakai user guest baru.
     */
    protected function newDevice(): void
    {
        $this->flushSession();
        $this->app['auth']->forgetGuards();
    }

    protected function makeMenu(): Menu
    {
        return Menu::create([
            'nama_menu' => 'Kopi Susu Test',
            'harga' => 20000,
            'stok' => 50,
            'status_tersedia' => 1,
        ]);
    }

    public function test_each_session_gets_its_own_guest_user()
    {
        $service = app(GuestSessionService::class);

        $requestA = Request::create('/menu');
        $requestA->setLaravelSession(new Store('device-a', new ArraySessionHandler(120)));

        $requestB = Request::create('/menu');
        $requestB->setLaravelSession(new Store('device-b', new ArraySessionHandler(120)));

        $guestA = $service->resolve($requestA);
        $guestB = $service->resolve($requestB);

        $this->assertTrue($guestA->is_guest);
        $this->assertTrue($guestB->is_guest);
        $this->assertNotSame($guestA->id_user, $guestB->id_user);
    }

    public function test_same_session_keeps_reusing_the_same_guest_user()
    {
        $service = app(GuestSessionService::class);

        $request = Request::create('/menu');
        $request->setLaravelSession(new Store('device-a', new ArraySessionHandler(120)));

        $first = $service->resolve($request);
        $second = $service->resolve($request);

        $this->assertSame($first->id_user, $second->id_user);
        $this->assertSame(1, User::where('is_guest', true)->count());
    }

    public function test_visiting_the_menu_creates_only_one_guest_user_per_session()
    {
        $this->get('/menu')->assertOk();
        $this->get('/menu')->assertOk();
        $this->get('/cart')->assertOk();

        $this->assertSame(1, User::where('is_guest', true)->count());
    }

    public function test_cart_from_one_device_is_not_visible_from_another()
    {
        $menu = $this->makeMenu();

        // Perangkat A: Norse add to cart
        $this->postJson(route('cart.add'), ['product_id' => $menu->id_menu, 'quantity' => 2])
            ->assertOk();

        $guestA = auth()->id();
        $this->assertSame(2, CartItem::where('user_id', $guestA)->sum('quantity'));

        // Perangkat B: sesi baru, harus punya keranjang kosong
        $this->newDevice();

        $this->get(route('cart.index'))->assertOk();

        $guestB = auth()->id();
        $this->assertNotSame($guestA, $guestB);
        $this->assertSame(0, CartItem::where('user_id', $guestB)->count());

        // Keranjang perangkat A tidak hilang
        $this->assertSame(1, CartItem::where('user_id', $guestA)->count());
    }

    public function test_guests_from_two_tables_keep_separate_carts()
    {
        $menuA = $this->makeMenu();
        $menuB = Menu::create([
            'nama_menu' => 'Matcha Test',
            'harga' => 25000,
            'stok' => 20,
            'status_tersedia' => 1,
        ]);

        // Meja 1
        $this->postJson(route('cart.add'), ['product_id' => $menuA->id_menu, 'quantity' => 1])->assertOk();
        $guestAtTableOne = auth()->id();

        // Meja 2, perangkat lain
        $this->newDevice();
        $this->get(route('menu.index', ['meja' => 'Meja 2']))->assertOk();
        $this->postJson(route('cart.add'), ['product_id' => $menuB->id_menu, 'quantity' => 3])->assertOk();
        $guestAtTableTwo = auth()->id();

        $this->assertNotSame($guestAtTableOne, $guestAtTableTwo);

        // Tiap perangkat hanya melihat pesanannya sendiri
        $this->assertSame(1, CartItem::where('user_id', $guestAtTableOne)->sum('quantity'));
        $this->assertSame(3, CartItem::where('user_id', $guestAtTableTwo)->sum('quantity'));
    }

    public function test_orders_from_two_devices_are_linked_to_the_right_guest()
    {
        $menu = $this->makeMenu();

        $payload = [
            'service_type' => 'dine_in',
            'payment_method' => 'cash',
            'customer_phone' => '081234567890',
        ];

        // Perangkat A memesan
        $this->postJson(route('cart.add'), ['product_id' => $menu->id_menu, 'quantity' => 1])->assertOk();
        $guestA = auth()->id();
        $this->postJson(route('order.store'), array_merge($payload, ['customer_name' => 'Budi']))->assertOk();

        // Perangkat B memesan
        $this->newDevice();
        $this->postJson(route('cart.add'), ['product_id' => $menu->id_menu, 'quantity' => 1])->assertOk();
        $guestB = auth()->id();
        $this->postJson(route('order.store'), array_merge($payload, ['customer_name' => 'Sari']))->assertOk();

        $orders = Order::orderBy('id_order')->get();

        $this->assertCount(2, $orders);
        $this->assertSame($guestA, $orders[0]->id_user);
        $this->assertSame('Budi', $orders[0]->customer_name);
        $this->assertSame($guestB, $orders[1]->id_user);
        $this->assertSame('Sari', $orders[1]->customer_name);

        // Tiap pelanggan hanya melihat pesanannya sendiri
        $this->assertSame(1, User::find($guestA)->orders()->count());
        $this->assertSame(1, User::find($guestB)->orders()->count());
    }

    public function test_guest_login_route_reuses_the_session_guest()
    {
        $this->get('/menu')->assertOk();
        $first = auth()->id();

        $this->post(route('guest.login'))->assertRedirect(route('menu.index'));

        $this->assertSame($first, auth()->id());
        $this->assertSame(1, User::where('is_guest', true)->count());
    }

    public function test_orders_require_customer_name()
    {
        $menu = $this->makeMenu();

        $this->postJson(route('cart.add'), ['product_id' => $menu->id_menu, 'quantity' => 1])->assertOk();

        $this->postJson(route('order.store'), [
            'service_type' => 'dine_in',
            'payment_method' => 'cash',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['customer_name']);

        $this->assertSame(0, Order::count());
    }

    public function test_orders_accept_missing_or_valid_phone_only()
    {
        $menu = $this->makeMenu();

        $this->postJson(route('cart.add'), ['product_id' => $menu->id_menu, 'quantity' => 1])->assertOk();

        // Telepon bersifat opsional — tanpa nomor pun pesanan tetap valid.
        $this->postJson(route('order.store'), [
            'service_type' => 'dine_in',
            'payment_method' => 'cash',
            'customer_name' => 'Budi',
        ])->assertOk();

        $this->assertSame(1, Order::count());

        $this->postJson(route('cart.add'), ['product_id' => $menu->id_menu, 'quantity' => 1])->assertOk();

        // Format telepon divalidasi hanya bila diisi.
        $this->postJson(route('order.store'), [
            'service_type' => 'dine_in',
            'payment_method' => 'cash',
            'customer_name' => 'Budi',
            'customer_phone' => 'bukan-nomor',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['customer_phone']);

        $this->assertSame(1, Order::count());
    }

    public function test_payment_method_rejects_values_outside_cash_and_qris()
    {
        $menu = $this->makeMenu();

        $this->postJson(route('cart.add'), ['product_id' => $menu->id_menu, 'quantity' => 1])->assertOk();

        $this->postJson(route('order.store'), [
            'service_type' => 'dine_in',
            'payment_method' => 'debit',
            'customer_name' => 'Budi',
            'customer_phone' => '081234567890',
        ])->assertStatus(422)->assertJsonValidationErrors('payment_method');

        $this->assertSame(0, Order::count());
    }

    public function test_store_saves_the_table_from_the_menu_url()
    {
        $meja = Meja::factory()->create(['nama_meja' => '05']);
        $menu = $this->makeMenu();

        $this->get(route('menu.index', ['meja' => '05']))->assertOk();
        $this->postJson(route('cart.add'), ['product_id' => $menu->id_menu, 'quantity' => 1])->assertOk();

        $this->postJson(route('order.store'), [
            'service_type' => 'dine_in',
            'payment_method' => 'cash',
            'customer_name' => 'Budi',
            'customer_phone' => '081234567890',
        ])->assertOk();

        $order = Order::latest('id_order')->first();

        $this->assertSame($meja->id_meja, $order->table_id);
        $this->assertSame('05', $order->table->nama_meja);
        $this->assertSame('Budi', $order->customer_name);
        $this->assertSame('081234567890', $order->customer_phone);
    }
}

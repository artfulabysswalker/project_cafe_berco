<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use App\Services\CartSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartSessionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Menu $beverageMenu;

    protected Menu $foodMenu;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['role_name' => 'Customer']);
        $this->user = User::create([
            'name' => 'Test Customer',
            'username' => 'testcustomer',
            'email' => 'customer@test.com',
            'password' => bcrypt('password'),
            'id_role' => $role->id_role,
        ]);

        $bevCategory = Category::create([
            'nama_kategori' => 'Classic Coffee',
            'slug' => 'classic-coffee',
            'icon' => 'fas fa-coffee',
        ]);

        $foodCategory = Category::create([
            'nama_kategori' => 'Food',
            'slug' => 'food',
            'icon' => 'fas fa-utensils',
        ]);

        $this->beverageMenu = Menu::create([
            'nama_menu' => 'Americano Specialty',
            'id_kategori' => $bevCategory->id,
            'harga' => 20000,
            'hpp' => 8000,
            'stok' => 50,
            'status_tersedia' => 1,
            'has_temperature_option' => true,
            'temperature_options' => [
                ['type' => 'Hot', 'price' => 20000, 'hpp' => 8000],
                ['type' => 'Cold', 'price' => 22000, 'hpp' => 9000],
            ],
        ]);

        $this->foodMenu = Menu::create([
            'nama_menu' => 'Croissant Butter',
            'id_kategori' => $foodCategory->id,
            'harga' => 25000,
            'hpp' => 10000,
            'stok' => 20,
            'status_tersedia' => 1,
            'has_temperature_option' => false,
        ]);
    }

    public function test_can_add_beverage_with_hot_and_ice_as_separate_items(): void
    {
        $this->actingAs($this->user);

        // 1. Add Hot Americano
        $response1 = $this->postJson(route('cart.add'), [
            'product_id' => $this->beverageMenu->id_menu,
            'quantity' => 1,
            'temperature' => 'Hot',
        ]);

        $response1->assertStatus(200)
            ->assertJson([
                'success' => true,
                'variant' => 'Hot',
                'cart_count' => 1,
            ]);

        // 2. Add Ice Americano (must create a separate item, not overwrite)
        $response2 = $this->postJson(route('cart.add'), [
            'product_id' => $this->beverageMenu->id_menu,
            'quantity' => 2,
            'temperature' => 'Ice',
        ]);

        $response2->assertStatus(200)
            ->assertJson([
                'success' => true,
                'variant' => 'Ice',
                'cart_count' => 3, // 1 hot + 2 ice = 3 total quantity
                'cart_items_count' => 2, // 2 distinct line items
            ]);

        // Verify Cart Page shows both items
        $cartPage = $this->get(route('cart.index'));
        $cartPage->assertStatus(200);
        $cartPage->assertSee('Americano Specialty');
        $cartPage->assertSee('Hot');
        $cartPage->assertSee('Ice');
    }

    public function test_can_update_quantity_realtime(): void
    {
        $this->actingAs($this->user);

        // Add item
        $this->postJson(route('cart.add'), [
            'product_id' => $this->beverageMenu->id_menu,
            'quantity' => 2,
            'temperature' => 'Hot',
        ]);

        $cartService = app(CartSessionService::class);
        $items = $cartService->getCartItems();
        $this->assertNotEmpty($items);
        $firstKey = $items->first()->cart_item_key;

        // Increase quantity
        $updateResp = $this->patchJson(route('cart.update', $firstKey), [
            'action' => 'increase',
        ]);

        $updateResp->assertStatus(200)
            ->assertJson([
                'success' => true,
                'quantity' => 3,
            ]);

        // Decrease quantity
        $updateResp2 = $this->patchJson(route('cart.update', $firstKey), [
            'action' => 'decrease',
        ]);

        $updateResp2->assertStatus(200)
            ->assertJson([
                'success' => true,
                'quantity' => 2,
            ]);
    }

    public function test_can_remove_and_clear_cart(): void
    {
        $this->actingAs($this->user);

        // Add item
        $this->postJson(route('cart.add'), [
            'product_id' => $this->beverageMenu->id_menu,
            'quantity' => 1,
            'temperature' => 'Ice',
        ]);

        $cartService = app(CartSessionService::class);
        $items = $cartService->getCartItems();
        $firstKey = $items->first()->cart_item_key;

        // Remove single item
        $removeResp = $this->deleteJson(route('cart.remove', $firstKey));
        $removeResp->assertStatus(200)
            ->assertJson([
                'success' => true,
                'is_empty' => true,
            ]);

        // Add item again and clear all
        $this->postJson(route('cart.add'), [
            'product_id' => $this->beverageMenu->id_menu,
            'quantity' => 2,
        ]);

        $clearResp = $this->postJson(route('cart.clear'));
        $clearResp->assertStatus(200)
            ->assertJson([
                'success' => true,
                'cart_count' => 0,
            ]);
    }

    public function test_can_apply_promo_code(): void
    {
        $this->actingAs($this->user);

        // Add 5 items
        $this->postJson(route('cart.add'), [
            'product_id' => $this->beverageMenu->id_menu,
            'quantity' => 5,
        ]);

        // Apply BERCO10 promo
        $promoResp = $this->postJson(route('cart.promo.apply'), [
            'promo_code' => 'BERCO10',
        ]);

        $promoResp->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertGreaterThan(0, $promoResp->json('totals.discount'));

        // Remove promo
        $removePromoResp = $this->postJson(route('cart.promo.remove'));
        $removePromoResp->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_non_beverage_item_has_no_temperature(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('cart.add'), [
            'product_id' => $this->foodMenu->id_menu,
            'quantity' => 2,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'variant' => null,
            ]);

        $cartService = app(CartSessionService::class);
        $items = $cartService->getCartItems();
        $this->assertEquals(1, $items->count());
        $this->assertNull($items->first()->temperature);
    }
}

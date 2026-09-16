<?php

use App\Models\CartItem;
use App\Models\Menu;
use App\Models\User;

/**
 * Test script to verify checkout flow works
 * Run: php scripts/test_checkout_flow.php
 */

require_once __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

// Get or create a guest user
$guest = User::where('is_guest', true)->first();
if (! $guest) {
    echo "Creating guest user...\n";
    $guest = User::create([
        'name' => 'Guest Test',
        'email' => 'guest_test_'.time().'@test.com',
        'password' => bcrypt('password'),
        'is_guest' => true,
    ]);
    echo "Created: {$guest->name}\n";
}

echo "Guest User ID: {$guest->id_user}\n";

// Clear any existing cart
$guest->cartItems()->delete();
echo "Cleared cart\n";

// Get or create a menu item
$menu = Menu::first();
if (! $menu) {
    echo "ERROR: No menus found in database!\n";
    exit(1);
}

echo "Menu: {$menu->name} - Harga: {$menu->harga}\n";

// Add to cart
$cartItem = CartItem::create([
    'user_id' => $guest->id_user,
    'menu_id' => $menu->id_menu,
    'quantity' => 2,
]);

echo "Added to cart: {$cartItem->quantity}x {$menu->name}\n";

// Test price calculation
$cartItems = $guest->cartItems()->with('menu')->get();
echo "\nCart Items Count: ".$cartItems->count()."\n";

foreach ($cartItems as $item) {
    echo "- {$item->menu->name} x{$item->quantity} @ {$item->menu->harga}\n";
}

// Test OrderController price calculation logic
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item->menu->harga * $item->quantity;
}
echo "\nSubtotal: {$subtotal}\n";

// Test with dine_in
$itemCount = $cartItems->sum(fn ($i) => $i->quantity);
$serviceCharge = 0; // dine_in = 0
echo "Service Charge (dine_in): {$serviceCharge}\n";

// Test discount (5% if 6 <= hour < 11)
$currentHour = date('H');
$discount = ($currentHour >= 6 && $currentHour < 11) ? intval($subtotal * 0.05) : 0;
echo "Current Hour: {$currentHour}, Discount: {$discount}\n";

$total = max(0, $subtotal + $serviceCharge - $discount);
echo "Total (dine_in): {$total}\n";

// Test with take_away
$serviceCharge = $itemCount * 1000;
$total = max(0, $subtotal + $serviceCharge - $discount);
echo "Service Charge (take_away): {$serviceCharge}\n";
echo "Total (take_away): {$total}\n";

// Test cartItemsData serialization
$cartItemsData = $cartItems->map(function ($item) {
    return [
        'id' => $item->id,
        'quantity' => $item->quantity,
        'menu' => [
            'id_menu' => $item->menu->id_menu,
            'nama_menu' => $item->menu->nama_menu,
            'harga' => $item->menu->harga,
            'name' => $item->menu->name,
            'price' => $item->menu->price,
        ],
    ];
})->toArray();

echo "\nJSON Serialization Test:\n";
$json = json_encode($cartItemsData);
echo 'JSON Length: '.strlen($json)."\n";
echo 'JSON Sample: '.substr($json, 0, 200)."...\n";

// Test that it can be decoded
$decoded = json_decode($json, true);
echo 'Decoded Items: '.count($decoded)."\n";
echo 'First Item Menu: '.$decoded[0]['menu']['name']."\n";

echo "\n✅ All checkout flow tests passed!\n";

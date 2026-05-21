<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Menu;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

try {
    $user = User::first();
    if (!$user) {
        echo "No users found, aborting smoke test\n";
        exit(1);
    }

    $product = Menu::first();
    if (!$product) {
        echo "No products found (Menu), aborting smoke test\n";
        exit(1);
    }

    echo "Using user id={$user->id}, product id={$product->id}\n";

    // Create a temporary cart item
    $cart = CartItem::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    echo "Cart item created id={$cart->id}\n";

    // Simulate checkout logic
    $cartItems = $user->cartItems()->with('menu')->get();
    $subtotal = $cartItems->sum(function ($item) { return $item->menu->harga * $item->quantity; });
    $tax = 0;
    $total = $subtotal + $tax;

    $order = Order::create([
        'order_number' => uniqid('ORD'),
        'user_id' => $user->id,
        'status' => 'completed',
        'service_type' => 'dine_in',
        'payment_method' => 'cash',
        'subtotal' => $subtotal,
        'tax' => $tax,
        'total' => $total,
        'notes' => 'Smoke test',
        'completed_at' => now(),
    ]);

    foreach ($cartItems as $ci) {
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $ci->menu_id,
            'quantity' => $ci->quantity,
            'price' => $ci->menu->harga,
            'subtotal' => $ci->menu->harga * $ci->quantity,
        ]);
    }

    echo "Order created id={$order->id}, items_count={$order->items()->count()}\n";

    // Cleanup: remove created order and items
    $order->items()->delete();
    $order->delete();
    $cart->delete();

    echo "Smoke order test passed and cleaned up.\n";
    exit(0);
} catch (\Exception $e) {
    echo "Smoke test failed: " . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
    exit(2);
}

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Show checkout page
     */
    public function checkout()
    {
        $user = auth()->user();
        $cartItems = $user->cartItems()->with('menu')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang Anda kosong');
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->menu->harga * $item->quantity;
        });

        $total = $subtotal;

        return view('Customerviews.checkout', compact('cartItems', 'subtotal', 'total'));
    }

    /**
     * Process order (payment)
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_type' => 'required|in:dine_in,take_away',
            'payment_method' => 'required|in:cash,debit,credit',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        $cartItems = $user->cartItems()->with('menu')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang Anda kosong',
            ], 422);
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->menu->harga * $item->quantity;
        });

        $tax = 0; // tax logic can be added if needed
        $total = $subtotal + $tax;

        try {
            $order = Order::create([
                'order_number' => uniqid('ORD'),
                'user_id' => $user->id_user, // Menggunakan id_user sebagai primary key User
                'status' => 'completed',
                'service_type' => $request->service_type,
                'payment_method' => $request->payment_method,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'notes' => $request->notes,
                'completed_at' => now(),
            ]);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id_order, // Menggunakan id_order sebagai primary key Order
                    'product_id' => $cartItem->menu_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->menu->harga,
                    'subtotal' => $cartItem->menu->harga * $cartItem->quantity,
                ]);
            }

            $user->cartItems()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat',
                'order_id' => $order->id,
                'redirect' => route('order.receipt', $order),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pesanan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show order receipt
     */
    public function receipt(Order $order)
    {
        if ($order->id_user !== auth()->id()) {
            return redirect()->route('home')
                ->with('error', 'Anda tidak memiliki akses ke pesanan ini');
        }

        $order->load('items.menu');

        return view('Customerviews.receipt', compact('order'));
    }

    /**
     * Get order history
     */
    public function history()
    {
        $orders = auth()->user()->orders()
            ->latest()
            ->paginate(10);

        return view('Customerviews.order-history', compact('orders'));
    }

    /**
     * Get order details via AJAX
     */
    public function show(Order $order)
    {
        if ($order->id_user !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $order->load('items.menu');

        return response()->json($order);
    }
}

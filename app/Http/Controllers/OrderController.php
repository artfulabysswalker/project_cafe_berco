<?php

namespace App\\Http\\Controllers;

use App\\Models\\Order;
use App\\Models\\OrderItem;
use App\\Models\\CartItem;
use Illuminate\\Http\\Request;
use Carbon\\Carbon;

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

        $tax = $subtotal * 0.1;
        $total = $subtotal + $tax;

        return view('Customerviews.checkout', compact('cartItems', 'subtotal', 'tax', 'total'));
    }

    /**
     * Show order history
     */
    public function history()
    {
        $user = auth()->user();
        $orders = $user->orders()
            ->with('items.menu')
            ->latest()
            ->paginate(10);

        return view('Customerviews.order.history', compact('orders'));
    }

    /**
     * Show single order detail
     */
    public function show(Order $order)
    {
        if ($order->id_user !== auth()->user()->id_user && !auth()->user()->is_admin) {
            return redirect()->route('home')->with('error', 'Unauthorized');
        }

        $order->load('items.menu', 'user');

        return view('Customerviews.receipt', compact('order'));
    }

    /**
     * Process order (payment)
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'payment_method' => 'required|in:cash,debit,credit',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        $cartItems = $user->cartItems()->with('menu')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang Anda kosong',
            ], 400);
        }

        try {
            $total = $cartItems->sum(function ($item) {
                return $item->menu->harga * $item->quantity;
            });

            $order = Order::create([
                'tanggal' => Carbon::now(),
                'nama_pelanggan' => $request->customer_name,
                'total_harga' => $total,
                'status_pembayaran' => 'pending',
                'status_order' => 'pending',
                'id_user' => $user->id_user,
            ]);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'id_order' => $order->id_order,
                    'id_menu' => $cartItem->menu_id,
                    'quantity' => $cartItem->quantity,
                    'subtotal' => $cartItem->menu->harga * $cartItem->quantity,
                ]);
            }

            CartItem::where('user_id', $user->id_user)->delete();

            return response()->json([
                'success' => true,
                'order_id' => $order->id_order,
                'redirect_url' => route('order.receipt', $order->id_order),
            ]);
        } catch (\\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show receipt/invoice
     */
    public function receipt(Order $order)
    {
        if ($order->id_user !== auth()->user()->id_user && !auth()->user()->is_admin) {
            return redirect()->route('home')->with('error', 'Unauthorized');
        }

        $order->load('items.menu', 'user');

        return view('Customerviews.orders.receipt', compact('order'));
    }
}
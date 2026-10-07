<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use App\Services\CartSessionService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Show checkout page
     */
    /**
     * Show checkout page
     */
    public function checkout()
    {
        $user = auth()->user();
        $cartItems = app(CartSessionService::class)->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang Anda kosong');
        }

        $priceInfo = $this->calculateOrderPrice($cartItems, 'dine_in');

        $cartItemsArray = $cartItems->map(function ($item) {
            return [
                'id' => $item->id,
                'cart_item_key' => $item->cart_item_key,
                'quantity' => $item->quantity,
                'temperature' => $item->temperature,
                'note' => $item->note,
                'unit_price' => $item->price,
                'subtotal' => $item->subtotal,
                'menu' => [
                    'id_menu' => $item->menu->id_menu,
                    'nama_menu' => $item->menu->nama_menu,
                    'harga' => $item->price,
                    'name' => $item->menu->name,
                    'price' => $item->price,
                ],
            ];
        })->toArray();

        return view('Customerviews.checkout', [
            'cartItems' => $cartItems,
            'cartItemsData' => $cartItemsArray,
            'subtotal' => $priceInfo['subtotal'],
            'serviceCharge' => $priceInfo['service_charge'],
            'discount' => $priceInfo['discount'],
            'total' => $priceInfo['total'],
            'morningDiscount' => $priceInfo['discount'] > 0,
        ]);
    }

    private function calculateOrderPrice($cartItems, $serviceType, $dateTime = null)
    {
        $dateTime = $dateTime ?? now();
        $subtotal = $cartItems->sum(function ($item) {
            $price = isset($item->price) ? $item->price : (isset($item->unit_price) ? $item->unit_price : ($item->menu ? $item->menu->getPriceForTemperature($item->temperature ?? null) : 0));

            return $price * $item->quantity;
        });
        $serviceCharge = 0;

        if ($serviceType === 'take_away') {
            $itemCount = $cartItems->sum('quantity');
            $serviceCharge = $itemCount * 1000;
        }

        $discount = $this->calculateMorningDiscount($subtotal, $dateTime);
        $total = max(0, $subtotal + $serviceCharge - $discount);

        return [
            'subtotal' => $subtotal,
            'service_charge' => $serviceCharge,
            'discount' => $discount,
            'total' => $total,
        ];
    }

    private function calculateMorningDiscount($amount, $dateTime)
    {
        $hour = $dateTime->hour;
        if ($hour >= 6 && $hour < 11) {
            return round($amount * 0.05);
        }

        return 0;
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $cartItems = app(CartSessionService::class)->getCartItems();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang belanja Anda kosong.',
            ], 400);
        }

        $validated = $request->validate([
            'service_type' => 'required|in:dine_in,take_away',
            'payment_method' => 'required|in:cash,qris,debit,credit',
            'notes' => 'nullable|string|max:500',
        ]);

        // 1. Stock validation before creating order
        foreach ($cartItems as $item) {
            $menu = $item->menu;
            if (! $menu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Salah satu produk di keranjang tidak valid.',
                ], 400);
            }

            if ($menu->stok < $item->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok menu "'.$menu->nama_menu.'" tidak mencukupi (Sisa stok: '.$menu->stok.' unit).',
                ], 400);
            }
        }

        // 2. Calculations
        $priceInfo = $this->calculateOrderPrice($cartItems, $validated['service_type']);
        $subtotal = $priceInfo['subtotal'];
        $serviceCharge = $priceInfo['service_charge'];
        $discount = $priceInfo['discount'];
        $total = $priceInfo['total'];

        $totalHpp = $cartItems->sum(function ($item) {
            $hpp = $item->menu ? $item->menu->getHppForTemperature($item->temperature ?? null) : 0;

            return $hpp * $item->quantity;
        });
        $profitMargin = max(0, $total - $totalHpp);

        // 3. Create Order
        $order = Order::create([
            'tanggal' => now(),
            'nama_pelanggan' => $user->name,
            'total_harga' => $total,
            'subtotal' => $subtotal,
            'service_charge' => $serviceCharge,
            'discount_amount' => $discount,
            'final_total' => $total,
            'cost_of_goods' => $totalHpp,
            'profit_margin' => $profitMargin,
            'status_pembayaran' => $validated['payment_method'] === 'cash' ? 'paid' : 'pending',
            'service_type' => $validated['service_type'],
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'] ?? null,
            'status_order' => 'pending',
            'id_user' => $user->id_user,
        ]);

        // 4. Create OrderItems with temperature and note, record historical HPP, and deduct stock
        foreach ($cartItems as $item) {
            $menu = $item->menu;
            $itemUnitPrice = $menu->getPriceForTemperature($item->temperature ?? null);
            $itemHpp = $menu->getHppForTemperature($item->temperature ?? null);

            OrderItem::create([
                'id_order' => $order->id_order,
                'id_menu' => $menu->id_menu,
                'quantity' => $item->quantity,
                'subtotal' => $itemUnitPrice * $item->quantity,
                'hpp' => $itemHpp,
                'hpp_at_sale' => $itemHpp,
                'temperature' => $item->temperature ?? null,
                'note' => $item->note ?? null,
            ]);

            // Deduct stock automatically
            $menu->decrementStock($item->quantity);
        }

        // 5. Clear cart session and database
        app(CartSessionService::class)->clearCart();

        // 6. Broadcast real-time event to cashier tablet
        event(new OrderCreated($order));

        // 7. Return response
        $redirectUrl = ($validated['payment_method'] === 'qris')
            ? route('xendit.qris.redirect', $order->id_order)
            : route('order.receipt', $order->id_order);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat!',
            'order_id' => $order->id_order,
            'redirect' => $redirectUrl,
        ]);
    }

    public function receipt(Order $order)
    {
        $order->load('items.menu');

        return view('Customerviews.receipt', compact('order'));
    }

    public function history()
    {
        $orders = auth()->user()->orders()->with('items.menu')->latest()->paginate(10);

        return view('Customerviews.order-history', compact('orders'));
    }

    /**
     * Admin: Unified Sales History & Reporting
     */
    public function historyAdmin(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();
        $query = Order::with(['user', 'items.menu']);

        if (! $isAdmin) {
            $query->where('id_user', $user->id_user);
        }

        // 1. Date Filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay(),
            ]);
        } elseif ($request->query('range') === 'today') {
            $query->whereDate('tanggal', Carbon::today());
        }

        // 2. Staff Filter
        if ($request->filled('staff') && $request->staff !== 'all') {
            $query->where(function ($q) use ($request) {
                $q->where('cashier_name', $request->staff)
                    ->orWhereHas('user', function ($sq) use ($request) {
                        $sq->where('name', $request->staff);
                    });
            });
        }

        // 3. Payment Method Filter
        if ($request->filled('payment_method') && $request->payment_method !== 'all') {
            $query->where('payment_method', $request->payment_method);
        }

        // 4. Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status_order', $request->status);
        }

        $historyOrders = $query->latest('tanggal')->paginate(15)->withQueryString();

        // Calculate Header Stats
        $statsQuery = clone $query;
        $totalRevenue = $statsQuery->where('status_order', 'completed')->sum('total_harga');
        $totalOrders = $query->count();

        // Employee list for dropdown
        $staffList = User::whereHas('role', function ($q) {
            $q->whereIn('role_name', ['Admin', 'Staff', 'pegawai']);
        })->get(['name']);

        return view('admin.history', compact('historyOrders', 'totalOrders', 'totalRevenue', 'staffList'));
    }

    public function complete($id_order)
    {
        $order = Order::findOrFail($id_order);
        $order->update(['status_pembayaran' => 'paid', 'status_order' => 'completed']);

        return back()->with('success', 'Pesanan selesai');
    }

    public function cancel($id_order)
    {
        $order = Order::findOrFail($id_order);
        $order->update(['status_order' => 'cancelled']);

        return back()->with('success', 'Pesanan dibatalkan');
    }

    public function index()
    {
        $orders = Order::where(function ($q) {
            $q->where('status_pembayaran', 'pending')
                ->orWhere('status_order', 'pending');
        })
            ->with(['user', 'items.menu'])
            ->latest()
            ->paginate(15);

        return view('admin.orders', compact('orders'));
    }

    /**
     * API for tablet cashier real-time polling fallback
     */
    public function liveOrders(Request $request)
    {
        $lastId = (int) $request->query('after_id', 0);

        $newOrders = Order::where('id_order', '>', $lastId)
            ->where(function ($q) {
                $q->where('status_pembayaran', 'pending')
                    ->orWhere('status_order', 'pending');
            })
            ->with(['user', 'items.menu'])
            ->latest('id_order')
            ->take(10)
            ->get();

        $formatted = $newOrders->map(function ($order) {
            $tableNumber = 'Meja -';
            if (preg_match('/meja\s*([0-9a-zA-Z_-]+)/i', $order->notes ?? '', $matches)) {
                $tableNumber = 'Meja '.strtoupper($matches[1]);
            } elseif ($order->service_type === 'dine_in') {
                $tableNumber = 'Dine-In (Meja)';
            } else {
                $tableNumber = 'Take Away';
            }

            $itemsSummary = $order->items->map(function ($item) {
                $temp = $item->temperature ? ' ('.ucfirst($item->temperature).')' : '';

                return [
                    'name' => ($item->menu->nama_menu ?? 'Menu').$temp,
                    'quantity' => $item->quantity,
                    'subtotal' => (float) $item->subtotal,
                    'note' => $item->note ?? null,
                ];
            })->toArray();

            return [
                'id_order' => $order->id_order,
                'order_code' => '#ORD-'.$order->id_order,
                'table_number' => $tableNumber,
                'customer_name' => $order->nama_pelanggan ?? ($order->user->name ?? 'Pelanggan Walk-In'),
                'service_type' => $order->service_type ?? 'dine_in',
                'payment_method' => strtoupper($order->payment_method ?? 'CASH'),
                'payment_status' => strtolower($order->status_pembayaran ?? 'pending'),
                'status_order' => strtolower($order->status_order ?? 'pending'),
                'total_amount' => (float) $order->total_harga,
                'total_formatted' => 'Rp '.number_format($order->total_harga ?? 0, 0, ',', '.'),
                'notes' => $order->notes,
                'items' => $itemsSummary,
                'items_count' => $order->items->sum('quantity'),
                'created_at_human' => $order->tanggal
                    ? $order->tanggal->format('H:i')
                    : ($order->created_at ? $order->created_at->format('H:i') : now()->format('H:i')),
                'receipt_url' => route('admin.receipt.view', $order->id_order),
                'complete_url' => route('admin.orders.complete', $order->id_order),
            ];
        });

        $maxId = $newOrders->max('id_order') ?? $lastId;
        $totalPending = Order::where('status_order', 'pending')->orWhere('status_pembayaran', 'pending')->count();

        return response()->json([
            'success' => true,
            'orders' => $formatted,
            'max_id' => $maxId,
            'total_pending' => $totalPending,
        ]);
    }
}

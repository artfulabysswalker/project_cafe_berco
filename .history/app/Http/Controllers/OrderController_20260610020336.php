<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderHistory
use App\Models\Payment;
use App\Services\QrisPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        // Default calculation (will be recalculated in JavaScript)
        $tax = $subtotal * 0.05;  // 5% tax for dine in
        $total = $subtotal + $tax;

        return view('Customerviews.checkout', compact('cartItems', 'subtotal', 'tax', 'total'));
    }

    /**
     * Calculate order price based on service type
     */
    private function calculateOrderPrice($cartItems, $serviceType)
    {
        if ($serviceType === 'take_away') {
            // Take away: add 1000 per item
            $itemCount = $cartItems->sum('quantity');
            $subtotal = $cartItems->sum(function ($item) {
                return $item->menu->harga * $item->quantity;
            });
            $tax = $itemCount * 1000;
            $total = $subtotal + $tax;

            return [
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
            ];
        } else {
            // Dine in: add 5% tax
            $subtotal = $cartItems->sum(function ($item) {
                return $item->menu->harga * $item->quantity;
            });
            $tax = $subtotal * 0.05;
            $total = $subtotal + $tax;

            return [
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
            ];
        }
    }

    /**
     * Process order (payment)
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_type' => 'required|in:dine_in,take_away',
            'payment_method' => 'required|in:cash,qris',
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

        // Calculate price based on service type
        $priceInfo = $this->calculateOrderPrice($cartItems, $request->service_type);

        try {
            $order = Order::create([
                'tanggal' => now(),
                'nama_pelanggan' => $user->name,
                'total_harga' => $priceInfo['total'],
                'subtotal' => $priceInfo['subtotal'],
                'tax_amount' => $priceInfo['tax'],
                'status_pembayaran' => 'pending',
                'service_type' => $request->service_type,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
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

            $user->cartItems()->delete();

            // Handle payment methods
            if ($request->payment_method === 'qris') {
                // Auto-generate QRIS invoice and redirect directly to Xendit
                try {
                    $qrisService = new QrisPaymentService();
                    $invoiceResult = $qrisService->createQrisInvoice($order);

                    // Redirect directly to Xendit checkout page (no intermediate QRIS page)
                    $redirectRoute = $invoiceResult['invoice_url'];

                    \Log::info('OrderController Auto-Generate QRIS Invoice & Redirect to Xendit', [
                        'order_id' => $order->id_order,
                        'invoice_id' => $invoiceResult['invoice_id'],
                        'amount' => $invoiceResult['amount'],
                        'xendit_url' => $redirectRoute,
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Pesanan berhasil dibuat, redirecting ke Xendit...',
                        'order_id' => $order->id_order,
                        'redirect' => $redirectRoute,
                        'invoice' => $invoiceResult,
                    ]);
                } catch (\Exception $e) {
                    \Log::error('OrderController QRIS Invoice Creation Failed', [
                        'order_id' => $order->id_order,
                        'error' => $e->getMessage(),
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal membuat QRIS invoice: ' . $e->getMessage(),
                    ], 500);
                }
            } else {
                // For cash payment, stay pending for admin to manually confirm
                return response()->json([
                    'success' => true,
                    'message' => 'Pesanan berhasil dibuat. Menunggu konfirmasi admin...',
                    'order_id' => $order->id_order,
                    'redirect' => route('order.receipt', $order),
                ]);
            }
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
        if ($order->id_user !== auth()->user()->id_user) {
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
            ->with('items.menu')
            ->latest()
            ->paginate(10);

        return view('Customerviews.order-history', compact('orders'));
    }

    /**
     * Get order details via AJAX
     */
    public function show(Order $order)
    {
        if ($order->id_user !== auth()->user()->id_user) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $order->load('items.menu');

        return response()->json($order);
    }

    /**
     * Admin: Get all orders (pending)
     */
    public function index()
    {
 $orders = auth()->user()->orders()
    ->with('items.menu')
    ->latest()
    ->paginate(40);

        return view('admin.orders', compact('orders'));
    }
    /**
     * Admin: Get order history (completed)
     */
    public function historyAdmin()
    {
        $historyOrders = Order::where('status_pembayaran', 'paid')
            ->with('user')
            ->latest()
            ->paginate(10);

        // Calculate stats
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status_pembayaran', 'paid')->sum('total_harga');
        $completedOrders = Order::where('status_order', 'completed')->count();
        $pendingOrders = Order::where('status_order', '!=', 'completed')->count();
        $completionRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100) : 0;

        return view('admin.history', compact('historyOrders', 'totalOrders', 'totalRevenue', 'completedOrders', 'pendingOrders', 'completionRate'));
    }

    /**
     * Admin: Mark order as completed
     */
    public function complete($id_order)
    {
        $order = Order::where('id_order', $id_order)->firstOrFail();

        $order->update([
            'status_pembayaran' => 'paid',
            'status_order' => 'completed',
        ]);

        return back()->with('success', 'Pesanan berhasil ditandai sebagai selesai');
    }

    /**
     * Admin: Cancel order
     */
    public function cancel($id_order)
    {
        $order = Order::where('id_order', $id_order)->firstOrFail();

        $order->update([
            'status_pembayaran' => 'pending',
            'status_order' => 'cancelled',
        ]);

        return back()->with('success', 'Pesanan berhasil dibatalkan');
    }


public function finishOrder($id_order)
{
    $order = Order::where('id_order', $id_order)->firstOrFail();

    DB::transaction(function () use ($order) {

        // 1. Save to history (MODEL WAY)
        OrderHistory::create([
            'id_order' => $order->id_order,
            'id_user' => $order->id_user,
            'nama_pelanggan' => $order->nama_pelanggan,
            'total_harga' => $order->total_harga,
            'payment_method' => $order->payment_method,
            'service_type' => $order->service_type,
            'status_order' => 'completed',
            'status_pembayaran' => $order->status_pembayaran,
            'notes' => $order->notes,
        ]);

        // 2. Delete from active orders
        $order->delete();
    });

    return back()->with('success', 'Order moved to history');
}
}

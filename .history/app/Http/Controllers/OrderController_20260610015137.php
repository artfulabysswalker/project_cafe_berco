<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
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

        $tax = $subtotal * 0.05;
        $total = $subtotal + $tax;

        return view('Customerviews.checkout', compact('cartItems', 'subtotal', 'tax', 'total'));
    }

    /**
     * Calculate order price
     */
    private function calculateOrderPrice($cartItems, $serviceType)
    {
        $subtotal = $cartItems->sum(function ($item) {
            return $item->menu->harga * $item->quantity;
        });

        if ($serviceType === 'take_away') {
            $tax = $cartItems->sum('quantity') * 1000;
        } else {
            $tax = $subtotal * 0.05;
        }

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax,
        ];
    }

    /**
     * Store order
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

            if ($request->payment_method === 'qris') {
                $qrisService = new QrisPaymentService();
                $invoiceResult = $qrisService->createQrisInvoice($order);

                return response()->json([
                    'success' => true,
                    'message' => 'Redirecting to payment...',
                    'redirect' => $invoiceResult['invoice_url'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dibuat',
                'order_id' => $order->id_order,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * FINISH ORDER → MOVE TO HISTORY + DELETE
     */
    public function finishOrder($id_order)
    {
        $order = Order::where('id_order', $id_order)->firstOrFail();

        DB::transaction(function () use ($order) {

            DB::table('order_histories')->insert([
                'id_order' => $order->id_order,
                'id_user' => $order->id_user,
                'nama_pelanggan' => $order->nama_pelanggan,
                'total_harga' => $order->total_harga,
                'payment_method' => $order->payment_method,
                'service_type' => $order->service_type,
                'status_order' => 'completed',
                'status_pembayaran' => $order->status_pembayaran,
                'notes' => $order->notes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $order->delete();
        });

        return back()->with('success', 'Order moved to history');
    }

    /**
     * ADMIN - complete (no move)
     */
    public function complete($id_order)
    {
        $order = Order::where('id_order', $id_order)->firstOrFail();

        $order->update([
            'status_pembayaran' => 'paid',
            'status_order' => 'completed',
        ]);

        return back()->with('success', 'Completed');
    }

    /**
     * ADMIN - cancel
     */
    public function cancel($id_order)
    {
        $orderd->update([
            'status_pembayaran' => 'pending',
            'status_order' => 'cancelled',
        ]);

        return back()->with('success', 'Cancelled');
    }
}
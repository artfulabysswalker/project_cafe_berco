<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Referral;
use App\Models\UserAchievement;
use App\Models\Achievement;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Show checkout page
     */
    public function checkout()
    {
        $user = auth()->user();
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang Anda kosong');
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $tax = $subtotal * 0.1; // 10% tax
        $total = $subtotal + $tax;

        return view('checkout', compact('cartItems', 'subtotal', 'tax', 'total'));
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
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang Anda kosong',
            ], 422);
        }

        // Calculate totals
        $subtotal = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $tax = $subtotal * 0.1;
        $total = $subtotal + $tax;

        try {
            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => Order::generateOrderNumber(),
                'status' => 'completed',
                'service_type' => $request->service_type,
                'payment_method' => $request->payment_method,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'notes' => $request->notes,
                'completed_at' => now(),
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                    'subtotal' => $cartItem->product->price * $cartItem->quantity,
                ]);
            }

            // Handle referral completion on first order
            if ($user->referred_by) {
                $isFirstOrder = $user->orders()->count() === 1;
                if ($isFirstOrder) {
                    $referral = Referral::where('referee_id', $user->id)
                        ->where('status', 'pending')
                        ->first();

                    if ($referral) {
                        $referral->markAsCompleted($order);
                    }
                }
            }

            // Check and award achievements
            $this->checkAndAwardAchievements($user);

            // Clear cart
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
        // Check if user is the owner
        if ($order->user_id !== auth()->id()) {
            return redirect()->route('home')
                ->with('error', 'Anda tidak memiliki akses ke pesanan ini');
        }

        $order->load('items.product');

        return view('receipt', compact('order'));
    }

    /**
     * Get order history
     */
    public function history()
    {
        $orders = auth()->user()->orders()
            ->latest()
            ->paginate(10);

        return view('order-history', compact('orders'));
    }

    /**
     * Get order details via AJAX
     */
    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $order->load('items.product');

        return response()->json($order);
    }

    /**
     * Check and award achievements for user
     */
    private function checkAndAwardAchievements($user)
    {
        $achievements = Achievement::where('is_active', true)->get();

        foreach ($achievements as $achievement) {
            // Skip if already earned
            if ($user->achievements()->where('achievement_id', $achievement->id)->exists()) {
                continue;
            }

            $isEarned = match ($achievement->type) {
                'orders_count' => $user->getCompletedOrdersCount() >= $achievement->threshold,
                'total_spent' => $user->getTotalSpent() >= $achievement->threshold,
                'referrals_count' => $user->referralsMade()
                    ->where('status', 'completed')
                    ->count() >= $achievement->threshold,
                default => false,
            };

            if ($isEarned) {
                // Award achievement
                UserAchievement::create([
                    'user_id' => $user->id,
                    'achievement_id' => $achievement->id,
                    'earned_at' => now(),
                ]);

                // Add reward to user's referral balance
                $user->increment('referral_balance', $achievement->reward_amount);
            }
        }
    }
}

<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\CartSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartSessionService $cartService;

    public function __construct(CartSessionService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Show shopping cart page
     */
    public function index()
    {
        $cartItems = $this->cartService->getCartItems();
        $totals = $this->cartService->getTotals();
        $total = $totals['grand_total'];

        return view('Customerviews.cart', compact('cartItems', 'totals', 'total'));
    }

    /**
     * Add menu item with temperature variant to cart
     */
    public function add(Request $request): JsonResponse
    {
        $productId = $request->input('product_id') ?? $request->input('menu_id') ?? $request->input('id');
        $temperature = $request->input('temperature');
        $quantity = (int) ($request->input('quantity') ?? 1);
        $note = $request->input('note') ?? $request->input('notes');

        if (! $productId) {
            return response()->json([
                'success' => false,
                'message' => 'Menu tidak valid atau tidak dipilih.',
            ], 422);
        }

        $result = $this->cartService->addItem((int) $productId, $quantity, $temperature, $note);

        $status = $result['success'] ? 200 : 400;

        return response()->json($result, $status);
    }

    /**
     * Update quantity of an item
     */
    public function update(Request $request, string $cartItem): JsonResponse
    {
        $action = $request->input('action');
        $quantity = $request->has('quantity') ? (int) $request->input('quantity') : null;

        $result = $this->cartService->updateQuantity($cartItem, $quantity, $action);

        $status = $result['success'] ? 200 : 400;

        return response()->json($result, $status);
    }

    /**
     * Update note of an item
     */
    public function updateNote(Request $request, string $cartItem): JsonResponse
    {
        $note = $request->input('note') ?? $request->input('notes');
        $result = $this->cartService->updateItemNote($cartItem, $note);

        return response()->json($result);
    }

    /**
     * Remove single item from cart
     */
    public function remove(string $cartItem): JsonResponse
    {
        $result = $this->cartService->removeItem($cartItem);

        $status = $result['success'] ? 200 : 400;

        return response()->json($result, $status);
    }

    /**
     * Clear all items in cart
     */
    public function clear(Request $request)
    {
        $result = $this->cartService->clearCart();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        return redirect()->route('cart.index')->with('success', 'Keranjang belanja telah dikosongkan.');
    }

    /**
     * Apply Promo Code
     */
    public function applyPromo(Request $request): JsonResponse
    {
        $code = $request->input('code') ?? $request->input('promo_code');
        $result = $this->cartService->applyPromo((string) $code);

        $status = $result['success'] ? 200 : 400;

        return response()->json($result, $status);
    }

    /**
     * Remove applied promo code
     */
    public function removePromo(): JsonResponse
    {
        $result = $this->cartService->removePromo();

        return response()->json($result);
    }

    /**
     * Get real-time cart item count for badge
     */
    public function count(): JsonResponse
    {
        $totals = $this->cartService->getTotals();

        return response()->json([
            'count' => $totals['total_quantity'],
            'items_count' => $totals['items_count'],
            'subtotal' => $totals['subtotal'],
            'grand_total' => $totals['grand_total'],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Menu;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Show shopping cart
     */
    public function index()
    {
        $user = auth()->user();

        $cartItems = $user->cartItems()
            ->with('menu')
            ->get();

        $total = $cartItems->sum(function ($item) {
            return $item->menu->harga * $item->quantity;
        });

        return view('CustomerViews.cart', compact('cartItems', 'total'));
    }

    /**
     * Add item to cart
     */
    public function add(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id_menu',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = auth()->user();

        $cartItem = $user->cartItems()
            ->where('menu_id', $request->menu_id)
            ->first();

        if ($cartItem) {

            // Update quantity
            $cartItem->quantity += $request->quantity;
            $cartItem->save();

        } else {

            // Create new cart item
            CartItem::create([
                'user_id' => $user->id,
                'menu_id' => $request->menu_id,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Produk ditambahkan ke keranjang',
            'cart_count' => $user->cartItems()->sum('quantity'),
        ]);
    }

    /**
     * Update quantity
     */
    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $cartItem->update([
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Keranjang diperbarui',
        ]);
    }

    /**
     * Remove item
     */
    public function remove(CartItem $cartItem)
    {
        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk dihapus dari keranjang',
            'cart_count' => auth()->user()->cartItems()->sum('quantity'),
        ]);
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        auth()->user()->cartItems()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Keranjang dikosongkan',
        ]);
    }

    /**
     * Cart item count
     */
    public function count()
    {
        $count = auth()->user()
            ->cartItems()
            ->sum('quantity');

        return response()->json([
            'count' => $count
        ]);
    }
}
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

        return view('Customerviews.cart', compact('cartItems', 'total'));
    }

    /**
     * Add item to cart
     */
    public function add(Request $request)
    {
        $productId = $request->input('product_id') ?? $request->input('menu_id') ?? $request->input('id');

        $request->merge(['product_id' => $productId]);

        $request->validate([
            'product_id' => 'required|exists:menus,id_menu',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $quantity = (int) ($request->input('quantity') ?? 1);
        $user = auth()->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi belanja belum aktif. Silakan muat ulang halaman.',
            ], 401);
        }

        $menu = Menu::where('id_menu', $productId)->firstOrFail();

        $cartItem = $user->cartItems()
            ->where('menu_id', $productId)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'user_id' => $user->id_user ?? $user->id,
                'menu_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        $totalCount = (int) $user->cartItems()->sum('quantity');

        return response()->json([
            'success' => true,
            'status' => 'success',
            'message' => 'Menu "'.$menu->nama_menu.'" berhasil ditambahkan ke keranjang!',
            'product_name' => $menu->nama_menu,
            'product_price' => $menu->harga,
            'cart_count' => $totalCount,
            'count' => $totalCount,
        ]);
    }

    /**
     * Update quantity
     */
    public function update(Request $request, CartItem $cartItem)
    {
        // Handle form submission with action parameter (increase/decrease)
        if ($request->has('action')) {
            $action = $request->input('action');
            $newQuantity = $cartItem->quantity;

            if ($action === 'increase') {
                $newQuantity++;
            } elseif ($action === 'decrease' && $newQuantity > 1) {
                $newQuantity--;
            }

            $cartItem->update([
                'quantity' => $newQuantity,
            ]);
        } else {
            // Handle JSON request with quantity parameter
            $request->validate([
                'quantity' => 'required|integer|min:1|max:100',
            ]);

            $cartItem->update([
                'quantity' => $request->quantity,
            ]);
        }

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
            'count' => $count,
        ]);
    }
}

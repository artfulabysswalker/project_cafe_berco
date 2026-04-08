<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Menu;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::where('user_id', auth()->id())->get();
        return view('cart.index', compact('cart'));
    }

    public function add($id)
    {
        $menu = Menu::findOrFail($id);

        if (!$menu->status_tersedia) {
            return back()->with('error', 'Menu tidak tersedia');
        }

        // Cek apakah menu sudah ada di cart
        $existingCart = Cart::where('user_id', auth()->id())
                           ->where('menu_id', $id)
                           ->first();

        if ($existingCart) {
            $existingCart->increment('qty');
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'menu_id' => $id,
                'qty' => 1
            ]);
        }

        return back()->with('success', 'Menu ditambahkan ke cart');
    }

    public function remove($id)
    {
        Cart::where('user_id', auth()->id())
            ->where('id', $id)
            ->delete();

        return back()->with('success', 'Item dihapus dari cart');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'qty' => 'required|integer|min:1'
        ]);

        Cart::where('user_id', auth()->id())
            ->where('id', $id)
            ->update(['qty' => $request->qty]);

        return back()->with('success', 'Quantity diperbarui');
    }
}

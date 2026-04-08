<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use App\Models\OrderItem;

class PaymentController extends Controller
{
    public function showCheckout()
    {
        $cart = Cart::where('user_id', auth()->id())->with('menu')->get();
        $total = $cart->sum(function($item) {
            return $item->qty * $item->menu->harga;
        });

        return view('checkout', compact('cart', 'total'));
    }

    public function checkout(Request $request)
    {
        $cart = Cart::where('user_id', auth()->id())->with('menu')->get();

        if ($cart->isEmpty()) {
            return back()->with('error', 'Cart kosong');
        }

        $total = $cart->sum(function($item){
            return $item->qty * $item->menu->harga;
        });

        $order = Order::create([
            'tanggal' => now(),
            'nama_pelanggan' => auth()->user()->name,
            'total_harga' => $total,
            'status_pembayaran' => 'Sudah',
            'id_user' => auth()->id()
        ]);

        // Buat order items dari cart
        foreach ($cart as $item) {
            OrderItem::create([
                'id_order' => $order->id_order,
                'id_menu' => $item->menu_id,
                'qty' => $item->qty,
                'harga' => $item->menu->harga
            ]);
        }

        Cart::where('user_id', auth()->id())->delete();

        return redirect()->route('receipt', $order->id_order)->with('success', 'Pembayaran berhasil');
    }

    public function receipt($id)
    {
        $order = Order::with('orderItems.menu')->findOrFail($id);

        if ($order->id_user !== auth()->id()) {
            abort(403);
        }

        return view('receipt', compact('order'));
    }
}
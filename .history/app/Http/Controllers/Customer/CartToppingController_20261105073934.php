<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Topping;
use App\Models\ToppingSize;
use Illuminate\Http\Request;

class CartToppingController extends Controller
{
    public function available(Request $request)
    {
        $menuId = $request->query('menu_id');

        $toppings = Topping::with('sizes')
            ->whereHas('menus', function ($query) use ($menuId) {
                $query->where('menus.id_menu', $menuId);
            })
            ->where('status_tersedia', true)
            ->get();

        return response()->json($toppings);
    }

    public function update(Request $request, string $cartItem)
    {
        $request->validate([
            'size_ids' => ['nullable', 'array'],
            'size_ids.*' => ['integer', 'distinct', 'exists:topping_sizes,id'],
        ]);

        $cart = session()->get('cart', []);

        if (!isset($cart[$cartItem])) {
            return response()->json([
                'message' => 'Cart item tidak ditemukan.'
            ], 404);
        }

        $menuId = $cart[$cartItem]['id_menu']
            ?? $cart[$cartItem]['menu_id']
            ?? null;

        if (!$menuId) {
            return response()->json([
                'message' => 'Menu ID tidak ditemukan pada cart.'
            ], 422);
        }

        $selectedSizes = ToppingSize::with('topping')
            ->whereIn('id', $request->input('size_ids', []))
            ->whereHas('topping', function ($query) {
                $query->where('status_tersedia', true);
            })
            ->get();

        // Verify that every selected topping is allowed for this menu.
        foreach ($selectedSizes as $size) {
            $allowed = $size->topping->menus()
                ->where('menus.id_menu', $menuId)
                ->exists();

            if (!$allowed) {
                return response()->json([
                    'message' => 'Topping tidak tersedia untuk menu ini.'
                ], 422);
            }
        }

        $cart[$cartItem]['toppings'] = $selectedSizes->map(function ($size) {
            return [
                'id_topping' => $size->id_topping,
                'id_topping_size' => $size->id,
                'nama_topping' => $size->topping->nama_topping,
                'size' => $size->size,
                'quantity' => $size->quantity,
                'unit' => $size->unit,
                'harga' => (float) $size->harga,
            ];
        })->values()->toArray();

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'toppings' => $cart[$cartItem]['toppings'],
            'message' => 'Topping berhasil diperbarui.'
        ]);
    }
}
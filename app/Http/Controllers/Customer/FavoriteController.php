<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:menus,id_menu',
        ]);

        $user = Auth::user();

        $favorite = Favorite::where('user_id', $user->id_user)
            ->where('menu_id', $request->product_id)
            ->first();

        if ($favorite) {
            $favorite->delete();

            return response()->json([
                'success' => true,
                'favorited' => false,
                'message' => 'Favorite removed.',
            ]);
        }

        Favorite::create([
            'user_id' => $user->id_user,
            'menu_id' => $request->product_id,
        ]);

        return response()->json([
            'success' => true,
            'favorited' => true,
            'message' => 'Favorite added.',
        ]);
    }
}

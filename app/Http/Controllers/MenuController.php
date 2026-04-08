<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Cart;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $menus = Menu::filter($request)->paginate(8);

        $cartCount = auth()->check()
            ? Cart::where('user_id', auth()->id())->count()
            : 0;

        $categories = [];

        $menuImages = [
            'Espresso' => 'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?q=80&w=400',
            'Cappuccino' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?q=80&w=400',
            'Latte' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=400',
            'Americano' => 'https://images.unsplash.com/photo-1511920170033-f8396924c348?q=80&w=400',
            'Mocha' => 'https://images.unsplash.com/photo-1521305916504-4a1121188589?q=80&w=400',
            'Croissant' => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?q=80&w=400',
            'Sandwich' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=400',
            'Cake Coklat' => 'https://source.unsplash.com/featured/400x300/?chocolate-cake',
        ];

        return view('menu.index', compact('menus', 'categories', 'cartCount', 'menuImages'));
    }
}
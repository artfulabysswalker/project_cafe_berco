<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display menu page
     */
    public function index(Request $request)
    {
        $query = Product::query()->where('available', true);

        // Filter by category
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by price range
        if ($request->filled('price')) {
            if ($request->price === 'low') {
                $query->where('price', '<', 15000);
            } elseif ($request->price === 'high') {
                $query->where('price', '>=', 15000);
            }
        }

        $products = $query->latest()->paginate(12);
        $categories = ['kopi', 'non-kopi', 'ice-blend', 'snack', 'dessert', 'makanan'];

        return view('menu', compact('products', 'categories'));
    }

    /**
     * Get product details via AJAX
     */
    public function show(Product $product)
    {
        return response()->json($product);
    }
}

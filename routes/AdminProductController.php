<?php

// Pindahkan file ini dari folder routes ke 
// app/Http/Controllers/Admin/AdminProductController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
            'available' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($request->name);
        Product::create($validated);

        return back()->with('success', 'Menu baru berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'available' => 'required|boolean',
        ]);

        $product->update($validated);
        return back()->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Menu berhasil dihapus.');
    }
}
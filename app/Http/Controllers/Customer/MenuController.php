<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ADMIN METHODS
    |--------------------------------------------------------------------------
    */

    // Admin Menu List with Category & Search Filters
    public function index(Request $request)
    {
        $categories = Category::withCount('menus')->get();
        $selectedCategory = $request->query('category');
        $search = $request->query('search');

        $query = Menu::with(['categoryRelation', 'recipes.rawMaterial'])->latest();

        if ($request->filled('category') && $selectedCategory !== 'all') {
            $query->where('id_kategori', $selectedCategory);
        }

        if ($request->filled('search')) {
            $query->where('nama_menu', 'like', '%'.$search.'%');
        }

        $menus = $query->get();

        return view('admin.menu', compact('menus', 'categories', 'selectedCategory', 'search'));
    }

    // Admin Create Page
    public function create()
    {
        $categories = Category::all();

        return view('admin.menu.create', compact('categories'));
    }

    // Store Menu
    public function store(Request $request)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'id_kategori' => 'nullable',
            'new_kategori' => 'nullable|string|max:100',
            'harga' => 'required|numeric|min:0',
            'hpp' => 'nullable|numeric|min:0',
            'stok' => 'nullable|integer|min:0',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'rating' => 'nullable|numeric|min:0|max:5',
        ]);

        // Handle inline new category
        $idKategori = $request->id_kategori;
        if ($request->filled('new_kategori')) {
            $cat = Category::firstOrCreate([
                'nama_kategori' => trim($request->new_kategori),
            ], [
                'slug' => str()->slug($request->new_kategori),
                'icon' => 'fas fa-tag',
            ]);
            $idKategori = $cat->id;
        }

        $path = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('menu', 'public');
        }

        Menu::create([
            'nama_menu' => $request->nama_menu,
            'id_kategori' => $idKategori,
            'harga' => $request->harga,
            'hpp' => $request->hpp ?? 0,
            'stok' => $request->stok ?? 0,
            'status_tersedia' => $request->boolean('status_tersedia', true),
            'foto' => $path,
            'rating' => $request->rating ?? 0,
            'deskripsi' => $request->deskripsi ?? '',
        ]);

        return redirect()->route('admin.menu')
            ->with('success', 'Produk menu "'.$request->nama_menu.'" berhasil ditambahkan!');
    }

    // Admin Show
    public function show($id)
    {
        $menu = Menu::with('categoryRelation')->where('id_menu', $id)->firstOrFail();

        return view('admin.menu.show', compact('menu'));
    }

    // Admin Edit
    public function edit($id)
    {
        $menu = Menu::where('id_menu', $id)->firstOrFail();
        $categories = Category::all();

        return view('admin.menu.edit', compact('menu', 'categories'));
    }

    // Admin Update
    public function update(Request $request, $id)
    {
        $menu = Menu::where('id_menu', $id)->firstOrFail();

        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'id_kategori' => 'nullable',
            'new_kategori' => 'nullable|string|max:100',
            'harga' => 'required|numeric|min:0',
            'hpp' => 'nullable|numeric|min:0',
            'stok' => 'nullable|integer|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Handle inline new category
        $idKategori = $request->id_kategori;
        if ($request->filled('new_kategori')) {
            $cat = Category::firstOrCreate([
                'nama_kategori' => trim($request->new_kategori),
            ], [
                'slug' => str()->slug($request->new_kategori),
                'icon' => 'fas fa-tag',
            ]);
            $idKategori = $cat->id;
        }

        // Replace image
        if ($request->hasFile('foto')) {
            if ($menu->foto) {
                Storage::disk('public')->delete($menu->foto);
            }
            $path = $request->file('foto')->store('menu', 'public');
            $menu->foto = $path;
        }

        $menu->update([
            'nama_menu' => $request->nama_menu,
            'id_kategori' => $idKategori,
            'harga' => $request->harga,
            'hpp' => $request->has('hpp') ? ($request->hpp ?? 0) : ($menu->hpp ?? 0),
            'stok' => $request->stok ?? 0,
            'status_tersedia' => $request->has('status_tersedia') ? true : false,
            'rating' => $request->rating ?? $menu->rating,
            'deskripsi' => $request->deskripsi ?? '',
            'foto' => $menu->foto,
        ]);

        return redirect()->route('admin.menu')
            ->with('success', 'Menu "'.$menu->nama_menu.'" berhasil diperbarui!');
    }

    // Quick Status Toggle
    public function toggleStatus($id)
    {
        $menu = Menu::where('id_menu', $id)->firstOrFail();
        $menu->status_tersedia = ! $menu->status_tersedia;
        $menu->save();

        return redirect()->back()->with('success', 'Status menu "'.$menu->nama_menu.'" diubah menjadi '.($menu->status_tersedia ? 'Tersedia/Aktif' : 'Habis/Nonaktif'));
    }

    // Admin Delete
    public function destroy($id)
    {
        $menu = Menu::where('id_menu', $id)->firstOrFail();
        $name = $menu->nama_menu;

        if ($menu->foto) {
            Storage::disk('public')->delete($menu->foto);
        }

        $menu->delete();

        return redirect()->route('admin.menu')->with('success', 'Menu "'.$name.'" berhasil dihapus!');
    }

    /*
    |--------------------------------------------------------------------------
    | CUSTOMER METHODS
    |--------------------------------------------------------------------------
    */

    // Customer Menu Page (Grouped Catalog by Category & Filterable Tabs)
    public function customerIndex(Request $request)
    {
        $selectedCategory = $request->query('category');
        $search = $request->query('search');
        $price = $request->query('price');

        // All categories for tab navigation with live product counts
        $allCategories = Category::withCount(['products' => function ($q) {
            $q->where('status_tersedia', true);
        }])->get();

        // Base category query with eager loaded active products
        $categoriesQuery = Category::query();

        // Filter specific category if selected and not 'all'
        if ($request->filled('category') && $selectedCategory !== 'all') {
            $categoriesQuery->where(function ($q) use ($selectedCategory) {
                if (is_numeric($selectedCategory)) {
                    $q->where('id', $selectedCategory);
                } else {
                    $q->where('slug', $selectedCategory)
                        ->orWhere('nama_kategori', $selectedCategory);
                }
            });
        }

        // Eager load products with active status and search / price constraints
        $categoriesQuery->with(['products' => function ($q) use ($search, $price) {
            $q->where('status_tersedia', true);

            // Search filter by product name, description, flavor, or sub-variants
            if (! empty($search)) {
                $q->where(function ($sq) use ($search) {
                    $sq->where('nama_menu', 'like', '%'.$search.'%')
                        ->orWhere('deskripsi', 'like', '%'.$search.'%')
                        ->orWhere('sub_variants', 'like', '%'.$search.'%')
                        ->orWhere('flavor_options', 'like', '%'.$search.'%');
                });
            }

            // Price filter
            if (! empty($price)) {
                if ($price === 'low') {
                    $q->where('harga', '<', 15000);
                } elseif ($price === 'high') {
                    $q->where('harga', '>=', 15000);
                }
            }

            $q->orderByRaw("CASE 
                WHEN series = 'Black' THEN 1
                WHEN series = 'White' THEN 2
                WHEN series = 'Kopi Susu Flavored' THEN 3
                WHEN series = 'Signature' THEN 4
                WHEN series = 'Summer' THEN 5
                WHEN series = 'Sparkling Drink' THEN 6
                WHEN series = 'Tea' THEN 7
                WHEN series = 'Milkbase' THEN 8
                WHEN series = 'Matcha' THEN 9
                WHEN series = 'Lokal Pride' THEN 10
                ELSE 11 
            END ASC")->orderBy('harga', 'asc');
        }]);

        $categories = $categoriesQuery->get();

        // Calculate total products visible
        $totalProductsCount = $categories->sum(fn ($cat) => $cat->products->count());

        $favoriteIds = auth()->check()
            ? auth()->user()->favorites()->pluck('menu_id')->toArray()
            : [];

        $totalReviews = Review::count();
        $recentReviews = Review::with(['user', 'product'])->latest()->take(4)->get();

        return view('Customerviews.menu', compact(
            'categories',
            'allCategories',
            'selectedCategory',
            'search',
            'price',
            'totalProductsCount',
            'favoriteIds',
            'totalReviews',
            'recentReviews'
        ));
    }

    // Customer AJAX Detail
    public function showProduct(Menu $menu)
    {
        return response()->json($menu);
    }
}

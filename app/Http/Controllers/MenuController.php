<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | ADMIN METHODS
    |--------------------------------------------------------------------------
    */

    // Admin Menu List
    public function index()
    {
        $menus = Menu::all();

        return view('admin.menu', compact('menus'));
    }

    // Admin Create Page
    public function create()
    {
        return view('admin.menu.create');
    }

    // Store Menu
    public function store(Request $request)
    {
        $request->validate([
            'nama_menu' => 'required',
            'harga' => 'required|numeric',
            'deskripsi' => 'required',
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'rating' => 'required|numeric|min:0|max:5',
        ]);

        $path = $request->file('foto')->store('menu', 'public');

        Menu::create([
            'nama_menu' => $request->nama_menu,
            'harga' => $request->harga,
            'status_tersedia' => true,
            'foto' => $path,
            'rating' => $request->rating,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('admin.menu')
            ->with('success', 'Menu created successfully');
    }

    // Admin Show
    public function show($id)
    {
        $menu = Menu::where('id_menu', $id)->firstOrFail();

        return view('admin.menu.show', compact('menu'));
    }

    // Admin Edit
    public function edit($id)
    {
        $menu = Menu::where('id_menu', $id)->firstOrFail();

        return view('admin.menu.edit', compact('menu'));
    }

    // Admin Update
    public function update(Request $request, $id)
    {
        $menu = Menu::where('id_menu', $id)->firstOrFail();

        $request->validate([
            'nama_menu' => 'required',
            'harga' => 'required|numeric',
            'rating' => 'required|numeric|min:0|max:5',
            'deskripsi' => 'required',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

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
            'harga' => $request->harga,
            'status_tersedia' => $request->has('status_tersedia'),
            'rating' => $request->rating,
            'deskripsi' => $request->deskripsi,
            'foto' => $menu->foto,
        ]);

        return redirect()->route('admin.menu')
            ->with('success', 'Menu updated');
    }

    // Admin Delete
    public function destroy($id)
    {
        $menu = Menu::where('id_menu', $id)->firstOrFail();

        $menu->delete();

        return redirect()->route('admin.menu');
    }



    /*
    |--------------------------------------------------------------------------
    | CUSTOMER METHODS
    |--------------------------------------------------------------------------
    */

    // Customer Menu Page
    public function customerIndex(Request $request)
    {
        $query = Menu::query()
            ->where('status_tersedia', true);

        // Search
        if ($request->filled('search')) {

            $query->where(
                'nama_menu',
                'like',
                '%' . $request->search . '%'
            );
        }

        // Price Filter
        if ($request->filled('price')) {

            if ($request->price === 'low') {

                $query->where('harga', '<', 15000);

            } elseif ($request->price === 'high') {

                $query->where('harga', '>=', 15000);
            }
        }

        $menus = $query
            ->latest()
            ->paginate(12);

        return view('CustomerViews.menu', compact('menus'));
    }

    // Customer AJAX Detail
    public function showProduct(Menu $menu)
    {
        return response()->json($menu);
    }
}
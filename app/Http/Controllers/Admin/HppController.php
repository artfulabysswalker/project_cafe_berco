<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\ProductRecipe;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HppController extends Controller
{
    /**
     * Tampilan Utama HPP Menu & Master Bahan Baku
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $categoryFilter = $request->query('category');

        $query = Menu::with(['categoryRelation', 'recipes.rawMaterial'])->latest('id_menu');

        if ($request->filled('search')) {
            $query->where('nama_menu', 'like', '%'.$search.'%');
        }

        if ($request->filled('category') && $categoryFilter !== 'all') {
            $query->where('id_kategori', $categoryFilter);
        }

        $menus = $query->get();
        $rawMaterials = RawMaterial::latest()->get();
        $categories = Category::all();

        // 1. Metric Calculations
        $totalMenus = $menus->count();
        $menusWithRecipe = $menus->filter(fn ($m) => $m->recipes->count() > 0)->count();
        $totalRawMaterials = $rawMaterials->count();

        $avgMarginPct = $totalMenus > 0
            ? round($menus->avg(fn ($m) => $m->profit_percentage ?? 0), 1)
            : 0;

        return view('admin.hpp.index', compact(
            'menus',
            'rawMaterials',
            'categories',
            'totalMenus',
            'menusWithRecipe',
            'totalRawMaterials',
            'avgMarginPct',
            'search',
            'categoryFilter'
        ));
    }

    /**
     * Get JSON Recipe for a Menu Item
     */
    public function getRecipe($menuId)
    {
        $menu = Menu::with('recipes.rawMaterial')->findOrFail($menuId);
        $rawMaterials = RawMaterial::all();

        return response()->json([
            'status' => 'success',
            'menu' => [
                'id_menu' => $menu->id_menu,
                'nama_menu' => $menu->nama_menu,
                'harga' => (float) $menu->harga,
                'hpp' => (float) ($menu->hpp ?? 0),
            ],
            'recipes' => $menu->recipes->map(function ($r) {
                return [
                    'raw_material_id' => $r->raw_material_id,
                    'name' => $r->rawMaterial?->name,
                    'unit' => $r->rawMaterial?->unit,
                    'purchase_price' => (float) ($r->rawMaterial?->purchase_price ?? 0),
                    'quantity_used' => (float) $r->quantity_used,
                    'cost' => (float) $r->cost,
                ];
            }),
            'available_materials' => $rawMaterials->map(function ($m) {
                return [
                    'id' => $m->id,
                    'name' => $m->name,
                    'unit' => $m->unit,
                    'purchase_price' => (float) $m->purchase_price,
                    'stock_quantity' => (float) $m->stock_quantity,
                ];
            }),
        ]);
    }

    /**
     * Simpan / Perbarui Resep Menu dan Hitung HPP Otomatis
     */
    public function saveRecipe(Request $request, $menuId)
    {
        $menu = Menu::findOrFail($menuId);

        $request->validate([
            'ingredients' => 'nullable|array',
            'ingredients.*.raw_material_id' => 'required|exists:raw_materials,id',
            'ingredients.*.quantity_used' => 'required|numeric|min:0.001',
            'manual_hpp' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Hapus resep lama
            $menu->recipes()->delete();

            $totalCalculatedHpp = 0;
            $ingredients = $request->input('ingredients', []);

            if (! empty($ingredients)) {
                foreach ($ingredients as $item) {
                    $rawMat = RawMaterial::find($item['raw_material_id']);
                    if ($rawMat) {
                        ProductRecipe::create([
                            'menu_id' => $menu->id_menu,
                            'raw_material_id' => $rawMat->id,
                            'quantity_used' => $item['quantity_used'],
                        ]);

                        $totalCalculatedHpp += (float) ($item['quantity_used'] * $rawMat->purchase_price);
                    }
                }
                $menu->hpp = round($totalCalculatedHpp, 2);
            } elseif ($request->filled('manual_hpp')) {
                // Fallback nilai manual jika resep tidak diisi
                $menu->hpp = (float) $request->manual_hpp;
            }

            $menu->save();
            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Resep dan HPP untuk "'.$menu->nama_menu.'" berhasil disimpan!',
                    'new_hpp' => $menu->hpp,
                    'profit_margin' => $menu->profit_margin,
                    'profit_pct' => $menu->profit_percentage,
                ]);
            }

            return redirect()->route('admin.hpp')->with('success', 'Resep dan kalkulasi HPP untuk "'.$menu->nama_menu.'" berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Gagal menyimpan resep: '.$e->getMessage());
        }
    }

    /**
     * Simpan Bahan Baku Baru
     */
    public function storeRawMaterial(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'purchase_price' => 'required|numeric|min:0',
            'stock_quantity' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        RawMaterial::create([
            'name' => $request->name,
            'unit' => $request->unit,
            'purchase_price' => $request->purchase_price,
            'stock_quantity' => $request->stock_quantity ?? 0,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.hpp')->with('success', 'Bahan baku "'.$request->name.'" berhasil ditambahkan!');
    }

    /**
     * Update Bahan Baku & Recalculate Affected Recipes
     */
    public function updateRawMaterial(Request $request, $id)
    {
        $material = RawMaterial::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'purchase_price' => 'required|numeric|min:0',
            'stock_quantity' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $material->update([
            'name' => $request->name,
            'unit' => $request->unit,
            'purchase_price' => $request->purchase_price,
            'stock_quantity' => $request->stock_quantity ?? 0,
            'notes' => $request->notes,
        ]);

        // Recalculate all menus that use this raw material
        $affectedRecipes = ProductRecipe::where('raw_material_id', $material->id)->pluck('menu_id')->unique();
        foreach ($affectedRecipes as $menuId) {
            $menu = Menu::find($menuId);
            if ($menu) {
                $menu->updateHppFromRecipe();
            }
        }

        return redirect()->route('admin.hpp')->with('success', 'Bahan baku "'.$material->name.'" diperbarui & HPP menu terkait disinkronkan!');
    }

    /**
     * Hapus Bahan Baku
     */
    public function deleteRawMaterial($id)
    {
        $material = RawMaterial::findOrFail($id);
        $name = $material->name;
        $material->delete();

        return redirect()->route('admin.hpp')->with('success', 'Bahan baku "'.$name.'" berhasil dihapus!');
    }
}

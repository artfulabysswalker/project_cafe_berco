<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display category listing
     */
    public function index()
    {
        $categories = Category::withCount('menus')->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Store a newly created category (Supports AJAX and Standard Form)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:categories,nama_kategori',
            'icon' => 'nullable|string|max:50',
        ]);

        $category = Category::create([
            'nama_kategori' => trim($request->nama_kategori),
            'slug' => str()->slug($request->nama_kategori),
            'icon' => $request->icon ?: 'fas fa-tag',
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan!',
                'category' => $category,
            ]);
        }

        return redirect()->back()->with('success', 'Kategori ' . $category->nama_kategori . ' berhasil dibuat!');
    }

    /**
     * Get categories list as JSON
     */
    public function listJson()
    {
        $categories = Category::withCount('menus')->get();
        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    /**
     * Update an existing category
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:categories,nama_kategori,' . $category->id,
            'icon' => 'nullable|string|max:50',
        ]);

        $category->update([
            'nama_kategori' => trim($request->nama_kategori),
            'slug' => str()->slug($request->nama_kategori),
            'icon' => $request->icon ?: $category->icon ?: 'fas fa-tag',
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diperbarui!',
                'category' => $category,
            ]);
        }

        return redirect()->back()->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Delete a category
     */
    public function destroy(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->menus()->update(['id_kategori' => null]);
        $category->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus!',
            ]);
        }

        return redirect()->back()->with('success', 'Kategori berhasil dihapus');
    }
}

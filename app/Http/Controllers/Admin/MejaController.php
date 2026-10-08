<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meja;
use Illuminate\Http\Request;

/**
 * CRUD meja (tanpa menyentuh kolom status enum lama).
 * Flag aktif/nonaktif memakai kolom `is_active` — meja nonaktif
 * tidak bisa dipakai memesan lewat QR Code.
 */
class MejaController extends Controller
{
    public function create()
    {
        return view('admin.tables.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_meja' => 'required|string|max:50|unique:mejas,nama_meja',
            'kapasitas' => 'nullable|integer|min:1|max:100',
            'area' => 'nullable|string|max:100',
        ]);

        $validated['is_active'] = true;

        Meja::create($validated);

        return redirect()->route('admin.tables.index')
            ->with('success', '"'.$validated['nama_meja'].'" berhasil ditambahkan');
    }

    public function edit($tableId)
    {
        $table = Meja::findOrFail($tableId);

        return view('admin.tables.edit', compact('table'));
    }

    public function update(Request $request, $tableId)
    {
        $table = Meja::findOrFail($tableId);

        $validated = $request->validate([
            'nama_meja' => 'required|string|max:50|unique:mejas,nama_meja,'.$table->id_meja.',id_meja',
            'kapasitas' => 'nullable|integer|min:1|max:100',
            'area' => 'nullable|string|max:100',
        ]);

        $table->update($validated);

        return redirect()->route('admin.tables.index')
            ->with('success', '"'.$table->nama_meja.'" berhasil diperbarui');
    }

    public function toggleStatus($tableId)
    {
        $table = Meja::findOrFail($tableId);

        $table->update(['is_active' => ! $table->is_active]);

        $state = $table->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', '"'.$table->nama_meja.'" berhasil '.$state);
    }
}

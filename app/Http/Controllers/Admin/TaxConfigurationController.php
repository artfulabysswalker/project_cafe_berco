<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaxConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaxConfigurationController extends Controller
{
    /**
     * Display list of tax configurations
     */
    public function index()
    {
        $taxConfigs = TaxConfiguration::with('user')->latest()->paginate(10);
        $activeConfig = TaxConfiguration::getActiveConfiguration();
        
        return view('admin.tax.index', compact('taxConfigs', 'activeConfig'));
    }

    /**
     * Show form to create new tax configuration
     */
    public function create()
    {
        return view('admin.tax.create');
    }

    /**
     * Store new tax configuration
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'effective_from' => 'nullable|date_format:Y-m-d H:i',
            'effective_until' => 'nullable|date_format:Y-m-d H:i|after:effective_from',
        ]);

        $validated['id_user'] = Auth::id();
        
        // If setting as active, deactivate others
        if ($validated['is_active'] ?? false) {
            TaxConfiguration::where('is_active', true)->update(['is_active' => false]);
        }

        TaxConfiguration::create($validated);

        return redirect()->route('admin.tax.index')
            ->with('success', 'Konfigurasi pajak berhasil dibuat');
    }

    /**
     * Show edit form
     */
    public function edit(TaxConfiguration $tax)
    {
        return view('admin.tax.edit', compact('tax'));
    }

    /**
     * Update tax configuration
     */
    public function update(Request $request, TaxConfiguration $tax)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'effective_from' => 'nullable|date_format:Y-m-d H:i',
            'effective_until' => 'nullable|date_format:Y-m-d H:i|after:effective_from',
        ]);

        // If setting as active, deactivate others
        if ($validated['is_active'] ?? false) {
            TaxConfiguration::where('is_active', true)
                ->where('id_tax_config', '!=', $tax->id_tax_config)
                ->update(['is_active' => false]);
        }

        $tax->update($validated);

        return redirect()->route('admin.tax.index')
            ->with('success', 'Konfigurasi pajak berhasil diperbarui');
    }

    /**
     * Delete tax configuration
     */
    public function destroy(TaxConfiguration $tax)
    {
        $tax->delete();
        
        return redirect()->route('admin.tax.index')
            ->with('success', 'Konfigurasi pajak berhasil dihapus');
    }

    /**
     * Set as active
     */
    public function setActive(TaxConfiguration $tax)
    {
        TaxConfiguration::where('is_active', true)->update(['is_active' => false]);
        $tax->update(['is_active' => true]);

        return back()->with('success', 'Konfigurasi pajak diaktifkan');
    }
}

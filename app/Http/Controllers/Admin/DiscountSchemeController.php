<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountScheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscountSchemeController extends Controller
{
    /**
     * Display list of discount schemes
     */
    public function index()
    {
        $schemes = DiscountScheme::with('user')->latest()->paginate(10);
        
        return view('admin.discount.index', compact('schemes'));
    }

    /**
     * Show form to create new discount scheme
     */
    public function create()
    {
        return view('admin.discount.create');
    }

    /**
     * Store new discount scheme
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:discount_schemes',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'valid_from' => 'required|date_format:Y-m-d H:i',
            'valid_until' => 'required|date_format:Y-m-d H:i|after:valid_from',
        ]);

        $validated['id_user'] = Auth::id();

        DiscountScheme::create($validated);

        return redirect()->route('admin.discount.index')
            ->with('success', 'Skema diskon berhasil dibuat');
    }

    /**
     * Show edit form
     */
    public function edit(DiscountScheme $discount)
    {
        return view('admin.discount.edit', compact('discount'));
    }

    /**
     * Update discount scheme
     */
    public function update(Request $request, DiscountScheme $discount)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:discount_schemes,code,' . $discount->id_discount_scheme . ',id_discount_scheme',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'valid_from' => 'required|date_format:Y-m-d H:i',
            'valid_until' => 'required|date_format:Y-m-d H:i|after:valid_from',
        ]);

        $discount->update($validated);

        return redirect()->route('admin.discount.index')
            ->with('success', 'Skema diskon berhasil diperbarui');
    }

    /**
     * Delete discount scheme
     */
    public function destroy(DiscountScheme $discount)
    {
        $discount->delete();
        
        return redirect()->route('admin.discount.index')
            ->with('success', 'Skema diskon berhasil dihapus');
    }

    /**
     * Get available discount schemes (for API or front-end)
     */
    public function getAvailable(Request $request)
    {
        $subtotal = $request->query('subtotal', 0);
        $schemes = DiscountScheme::getValidSchemes($subtotal);

        return response()->json([
            'data' => $schemes->map(function ($scheme) use ($subtotal) {
                return [
                    'id' => $scheme->id_discount_scheme,
                    'code' => $scheme->code,
                    'name' => $scheme->name,
                    'discount_value' => $scheme->discount_value,
                    'discount_type' => $scheme->discount_type,
                    'discount_amount' => $scheme->calculateDiscount($subtotal),
                    'description' => $scheme->description,
                ];
            })->values(),
        ]);
    }
}

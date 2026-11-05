<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topping;
use App\Models\ToppingSize;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ToppingController extends Controller
{
    public function index()
    {
        $toppings = Topping::with('sizes')
            ->withCount('menus')
            ->latest()
            ->paginate(10);

        return view('admin.toppings.index', compact('toppings'));
    }

    public function create()
    {
        $menus = Menu::all();

        return view('admin.toppings.create', compact('menus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_available' => ['nullable', 'boolean'],
            'menu_ids' => ['nullable', 'array'],
            'menu_ids.*' => ['integer', 'exists:menus,id_menu'],
            'sizes' => ['required', 'array'],
            'sizes.small' => ['required', 'array'],
            'sizes.medium' => ['required', 'array'],
            'sizes.large' => ['required', 'array'],
        ]);

        foreach (['small', 'medium', 'large'] as $size) {
            $request->validate([
                "sizes.$size.quantity" => ['required', 'numeric', 'min:0.01'],
                "sizes.$size.unit" => ['required', Rule::in(['g', 'ml'])],
                "sizes.$size.price" => ['required', 'numeric', 'min:0'],
            ]);
        }

        DB::transaction(function () use ($request) {
            $topping = Topping::create([
                'name' => $request->name,
                'image' => $request->hasFile('image')
                    ? $request->file('image')->store('toppings', 'public')
                    : null,
                'is_available' => $request->boolean('is_available'),
            ]);

            foreach (['small', 'medium', 'large'] as $size) {
                $topping->sizes()->create([
                    'size' => $size,
                    'quantity' => $request->input("sizes.$size.quantity"),
                    'unit' => $request->input("sizes.$size.unit"),
                    'price' => $request->input("sizes.$size.price"),
                ]);
            }

            $topping->menus()->sync($request->input('menu_ids', []));
        });

        return redirect()
            ->route('admin.toppings.index')
            ->with('success', 'Topping created successfully.');
    }

    public function edit(Topping $topping)
    {
        $topping->load('sizes', 'menus');
        $menus = Menu::all();

        return view('admin.toppings.edit', compact('topping', 'menus'));
    }

    public function update(Request $request, Topping $topping)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_available' => ['nullable', 'boolean'],
            'menu_ids' => ['nullable', 'array'],
            'menu_ids.*' => ['integer', 'exists:menus,id_menu'],
        ]);

        foreach (['small', 'medium', 'large'] as $size) {
            $request->validate([
                "sizes.$size.quantity" => ['required', 'numeric', 'min:0.01'],
                "sizes.$size.unit" => ['required', Rule::in(['g', 'ml'])],
                "sizes.$size.price" => ['required', 'numeric', 'min:0'],
            ]);
        }

        DB::transaction(function () use ($request, $topping) {
            $image = $topping->image;

            if ($request->hasFile('image')) {
                if ($image) {
                    Storage::disk('public')->delete($image);
                }

                $image = $request->file('image')->store('toppings', 'public');
            }

            $topping->update([
                'name' => $request->name,
                'image' => $image,
                'is_available' => $request->boolean('is_available'),
            ]);

            foreach (['small', 'medium', 'large'] as $size) {
                $topping->sizes()->updateOrCreate(
                    ['size' => $size],
                    [
                        'quantity' => $request->input("sizes.$size.quantity"),
                        'unit' => $request->input("sizes.$size.unit"),
                        'price' => $request->input("sizes.$size.price"),
                    ]
                );
            }

            $topping->menus()->sync($request->input('menu_ids', []));
        });

        return redirect()
            ->route('admin.toppings.index')
            ->with('success', 'Topping updated successfully.');
    }

    public function destroy(Topping $topping)
    {
        if ($topping->image) {
            Storage::disk('public')->delete($topping->image);
        }

        $topping->delete();

        return redirect()
            ->route('admin.toppings.index')
            ->with('success', 'Topping deleted successfully.');
    }

    public function toggleAvailability(Topping $topping)
    {
        $topping->update([
            'is_available' => !$topping->is_available,
        ]);

        return back()->with('success', 'Topping availability updated.');
    }
}

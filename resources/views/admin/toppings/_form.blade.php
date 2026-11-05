```blade
@php
    $sizes = [
        'small' => 'Small',
        'medium' => 'Medium',
        'large' => 'Large',
    ];

    $currentSizes = old('sizes', isset($topping)
        ? $topping->sizes->keyBy('size')->map(fn ($s) => [
            'quantity' => $s->quantity,
            'unit' => $s->unit,
            'price' => $s->price,
        ])->toArray()
        : []);

    $selectedMenus = old('menu_ids', isset($topping)
        ? $topping->menus->pluck('id_menu')->toArray()
        : []);
@endphp

<div class="space-y-6">

    {{-- Basic Information --}}
    <div class="bg-white border border-stone-200 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-stone-200">
            <h3 class="text-sm font-semibold text-stone-900">Basic Information</h3>
            <p class="text-xs text-stone-500 mt-1">Name, image, and availability.</p>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-xs font-mono text-stone-600 mb-2">
                    TOPPING NAME *
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $topping->name ?? '') }}"
                    required
                    class="w-full rounded-lg border border-stone-300 px-3 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-100 outline-none"
                    placeholder="e.g. Extra Cheese"
                >
                @error('name')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="image" class="block text-xs font-mono text-stone-600 mb-2">
                    TOPPING IMAGE
                </label>
                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/png,image/jpeg,image/webp"
                    class="w-full rounded-lg border border-stone-300 bg-white text-sm file:mr-4 file:border-0 file:bg-stone-100 file:px-4 file:py-2.5 file:text-xs file:font-mono file:text-stone-700 hover:file:bg-stone-200"
                >

                @if(isset($topping) && $topping->image)
                    <div class="mt-3 flex items-center gap-3">
                        <img
                            src="{{ asset('storage/' . $topping->image) }}"
                            alt="{{ $topping->name }}"
                            class="h-16 w-16 rounded-lg border border-stone-200 object-cover"
                        >
                        <span class="text-xs text-stone-500">Current image</span>
                    </div>
                @endif

                @error('image')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input
                        type="checkbox"
                        name="is_available"
                        value="1"
                        @checked(old('is_available', $topping->is_available ?? true))
                        class="h-4 w-4 rounded border-stone-300 text-orange-600 focus:ring-orange-500"
                    >
                    <span>
                        <span class="block text-sm font-medium text-stone-800">Available for sale</span>
                        <span class="block text-xs text-stone-500 mt-1">Customers can select this topping when enabled.</span>
                    </span>
                </label>
            </div>
        </div>
    </div>

    {{-- Size and Pricing --}}
    <div class="bg-white border border-stone-200 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-stone-200">
            <h3 class="text-sm font-semibold text-stone-900">Size & Pricing</h3>
            <p class="text-xs text-stone-500 mt-1">Set the quantity, unit, and additional price for each size.</p>
        </div>

        <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
            @foreach($sizes as $key => $label)
                <div class="border border-stone-200 rounded-xl p-4 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-stone-800">{{ $label }}</h4>
                        <span class="text-[10px] font-mono text-stone-400 uppercase">{{ $key }}</span>
                    </div>

                    <div>
                        <label class="block text-xs font-mono text-stone-500 mb-2">
                            QUANTITY *
                        </label>
                        <input
                            type="number"
                            name="sizes[{{ $key }}][quantity]"
                            value="{{ $currentSizes[$key]['quantity'] ?? '' }}"
                            min="0.01"
                            step="0.01"
                            required
                            class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-100 outline-none"
                            placeholder="e.g. 30"
                        >
                        @error("sizes.$key.quantity")
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-mono text-stone-500 mb-2">
                            UNIT *
                        </label>
                        <select
                            name="sizes[{{ $key }}][unit]"
                            required
                            class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-100 outline-none"
                        >
                            <option value="g" @selected(($currentSizes[$key]['unit'] ?? 'g') === 'g')>Gram (g)</option>
                            <option value="ml" @selected(($currentSizes[$key]['unit'] ?? 'g') === 'ml')>Milliliter (ml)</option>
                        </select>
                        @error("sizes.$key.unit")
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-mono text-stone-500 mb-2">
                            EXTRA PRICE (RP) *
                        </label>
                        <input
                            type="number"
                            name="sizes[{{ $key }}][price]"
                            value="{{ $currentSizes[$key]['price'] ?? '0' }}"
                            min="0"
                            step="1"
                            required
                            class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-100 outline-none"
                            placeholder="0"
                        >
                        @error("sizes.$key.price")
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Menu Assignment --}}
    <div class="bg-white border border-stone-200 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-stone-200">
            <h3 class="text-sm font-semibold text-stone-900">Available for Menus</h3>
            <p class="text-xs text-stone-500 mt-1">Choose which menu items can use this topping.</p>
        </div>

        <div class="p-6">
            @if($menus->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($menus as $menu)
                        <label class="flex items-center gap-3 p-3 border border-stone-200 rounded-lg cursor-pointer hover:bg-stone-50 transition-colors">
                            <input
                                type="checkbox"
                                name="menu_ids[]"
                                value="{{ $menu->id_menu }}"
                                @checked(in_array($menu->id_menu, $selectedMenus))
                                class="h-4 w-4 rounded border-stone-300 text-orange-600 focus:ring-orange-500"
                            >
                            <span class="text-sm text-stone-700">{{ $menu->nama_menu }}</span>
                        </label>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-stone-500">No menu items found. Add menu items before assigning toppings.</p>
            @endif

            @error('menu_ids')
                <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
            @enderror
            @error('menu_ids.*')
                <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex items-center justify-end gap-3">
        <a
            href="{{ route('admin.toppings.index') }}"
            class="px-5 py-2.5 rounded-lg border border-stone-300 text-xs font-mono text-stone-700 hover:bg-stone-100 transition-colors"
        >
            CANCEL
        </a>

        <button
            type="submit"
            class="px-5 py-2.5 rounded-lg bg-[#C27835] text-white text-xs font-mono hover:bg-[#A96228] transition-colors shadow-sm"
        >
            <i class="fas fa-save mr-2"></i>{{ strtoupper($buttonText) }}
        </button>
    </div>

</div>
```
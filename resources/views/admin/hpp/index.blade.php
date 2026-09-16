@extends('dashboard')

@section('page-title', 'HPP & Resep Bahan Baku')
@section('breadcrumb', 'Cost of Goods Sold & Recipes')

@section('content')
<div class="space-y-6">

    {{-- Flash Alerts --}}
    @if(session('success'))
        <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-xs font-mono flex items-center justify-between shadow-2xs">
            <div class="flex items-center space-x-2">
                <i class="fas fa-circle-check text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg text-xs font-mono flex items-center justify-between shadow-2xs">
            <div class="flex items-center space-x-2">
                <i class="fas fa-circle-exclamation text-rose-600"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900">&times;</button>
        </div>
    @endif

    {{-- 1. KPI SUMMARY METRIC CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Average Margin --}}
        <div class="bg-white border border-neutral-200 rounded-xl p-4 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-mono uppercase tracking-wider text-neutral-500 font-medium">RATA-RATA MARGIN</span>
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            </div>
            <div class="flex items-baseline space-x-2">
                <h3 class="text-2xl font-mono font-semibold text-neutral-900 tracking-tight">{{ $avgMarginPct }}%</h3>
                <span class="text-xs text-neutral-500 font-mono">Gross Profit</span>
            </div>
            <p class="text-[11px] text-neutral-500 font-mono">Dari {{ $totalMenus }} total menu terdaftar</p>
        </div>

        {{-- Card 2: Menus with Recipe --}}
        <div class="bg-white border border-neutral-200 rounded-xl p-4 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-mono uppercase tracking-wider text-neutral-500 font-medium">RESEP OTOMATIS</span>
                <span class="text-[10px] font-mono text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200">Aktif</span>
            </div>
            <div class="flex items-baseline space-x-2">
                <h3 class="text-2xl font-mono font-semibold text-neutral-900 tracking-tight">{{ $menusWithRecipe }}</h3>
                <span class="text-xs text-neutral-500 font-mono">/ {{ $totalMenus }} menu</span>
            </div>
            <p class="text-[11px] text-neutral-500 font-mono">HPP terhitung otomatis dari bahan</p>
        </div>

        {{-- Card 3: Total Raw Materials --}}
        <div class="bg-white border border-neutral-200 rounded-xl p-4 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-mono uppercase tracking-wider text-neutral-500 font-medium">MASTER BAHAN BAKU</span>
                <span class="text-[10px] font-mono text-sky-700 bg-sky-50 px-1.5 py-0.2 rounded border border-sky-200">Database</span>
            </div>
            <div class="flex items-baseline space-x-2">
                <h3 class="text-2xl font-mono font-semibold text-neutral-900 tracking-tight">{{ $totalRawMaterials }}</h3>
                <span class="text-xs text-neutral-500 font-mono">item bahan</span>
            </div>
            <p class="text-[11px] text-neutral-500 font-mono">Kopi, susu, sirup, cup, dll.</p>
        </div>

        {{-- Card 4: Action Quick Add --}}
        <div class="bg-neutral-900 border border-neutral-800 rounded-xl p-4 shadow-xs flex flex-col justify-between space-y-2 text-white">
            <div>
                <span class="text-[11px] font-mono uppercase tracking-wider text-neutral-400 font-medium block">KONTROL CEPAT</span>
                <p class="text-xs text-neutral-300 mt-1">Tambah bahan baku baru ke sistem untuk dipakai dalam resep.</p>
            </div>
            <button type="button" onclick="openRawMaterialModal()" class="w-full text-xs font-mono font-medium bg-[#C27835] hover:bg-[#A05C22] text-white py-2 rounded-md transition-colors flex items-center justify-center space-x-1.5 shadow-2xs">
                <i class="fas fa-plus text-[10px]"></i>
                <span>+ Tambah Bahan Baku</span>
            </button>
        </div>
    </div>

    {{-- 2. MAIN WORKSPACE CARD --}}
    <div class="bg-white border border-neutral-200 rounded-xl shadow-xs overflow-hidden">
        
        {{-- Section Switcher Tabs & Search Toolbar --}}
        <div class="p-5 border-b border-neutral-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            {{-- Tabs --}}
            <div class="flex items-center space-x-2 bg-neutral-100 p-1 rounded-lg border border-neutral-200 self-start">
                <button type="button" id="tabBtnMenuHpp" onclick="switchMainTab('menu-hpp')" class="px-3.5 py-1.5 rounded-md font-mono text-xs font-semibold bg-white text-neutral-900 shadow-2xs transition-colors">
                    HPP & Resep Menu ({{ $menus->count() }})
                </button>
                <button type="button" id="tabBtnRawMat" onclick="switchMainTab('raw-mat')" class="px-3.5 py-1.5 rounded-md font-mono text-xs font-medium text-neutral-600 hover:text-neutral-900 transition-colors">
                    Master Bahan Baku ({{ $rawMaterials->count() }})
                </button>
            </div>

            {{-- Search & Actions --}}
            <div class="flex items-center space-x-3 w-full md:w-auto">
                <div class="relative flex-1 md:w-64">
                    <input 
                        type="text" 
                        id="hppSearchInput" 
                        placeholder="Cari menu / bahan..." 
                        onkeyup="filterHppTable()"
                        class="w-full bg-neutral-50 border border-neutral-200 rounded-md pl-8 pr-3 py-1.5 text-xs text-neutral-900 placeholder:text-neutral-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-stone-800 focus:border-stone-800 transition-colors"
                    />
                    <i class="fas fa-magnifying-glass text-neutral-400 text-xs absolute left-2.5 top-2.5"></i>
                </div>

                <button type="button" id="btnHeaderAction" onclick="openRawMaterialModal()" class="hidden text-xs font-mono font-medium bg-[#18181B] hover:bg-black text-white px-3.5 py-1.5 rounded-md transition-colors shadow-2xs shrink-0 inline-flex items-center space-x-1.5">
                    <i class="fas fa-plus text-[10px]"></i>
                    <span>Bahan Baru</span>
                </button>
            </div>
        </div>

        {{-- TAB 1: MENU HPP TABLE --}}
        <div id="sectionMenuHpp" class="overflow-x-auto">
            <table class="w-full text-left text-xs" id="tableMenuHpp">
                <thead class="bg-neutral-50 border-b border-neutral-200 text-neutral-500 text-[10px] font-mono uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Nama Produk Menu</th>
                        <th class="px-5 py-3 font-semibold">Harga Jual</th>
                        <th class="px-5 py-3 font-semibold">Total HPP (Modal)</th>
                        <th class="px-5 py-3 font-semibold">Laba Kotor (Rp)</th>
                        <th class="px-5 py-3 font-semibold text-center">Margin (%)</th>
                        <th class="px-5 py-3 font-semibold">Komposisi Bahan</th>
                        <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 font-sans">
                    @forelse($menus as $menu)
                        @php
                            $hasRecipe = $menu->recipes->count() > 0;
                            $marginRp = max(0, ($menu->harga ?? 0) - ($menu->hpp ?? 0));
                            $marginPct = $menu->profit_percentage ?? 0;
                        @endphp
                        <tr class="menu-row hover:bg-neutral-50/75 transition-colors">
                            {{-- Menu Name & Category --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-md bg-stone-100 border border-neutral-200 flex items-center justify-center text-xs shrink-0 overflow-hidden">
                                        @if(!empty($menu->foto))
                                            <img src="/storage/{{ $menu->foto }}" alt="{{ $menu->nama_menu }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-mug-hot text-neutral-400"></i>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-neutral-900 truncate leading-snug">{{ $menu->nama_menu }}</p>
                                        <p class="text-[11px] font-mono text-neutral-500">{{ $menu->categoryRelation?->nama_kategori ?? 'Specialty' }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Selling Price --}}
                            <td class="px-5 py-3.5 font-mono text-neutral-900 font-medium">
                                Rp {{ number_format($menu->harga, 0, ',', '.') }}
                            </td>

                            {{-- Total HPP --}}
                            <td class="px-5 py-3.5 font-mono">
                                <div class="flex items-center space-x-1.5">
                                    <span class="font-semibold text-neutral-900">Rp {{ number_format($menu->hpp ?? 0, 0, ',', '.') }}</span>
                                    @if($hasRecipe)
                                        <span class="text-[9px] font-mono text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200" title="Terhitung otomatis dari bahan resep">Auto</span>
                                    @else
                                        <span class="text-[9px] font-mono text-neutral-500 bg-neutral-100 px-1.5 py-0.2 rounded border border-neutral-200" title="Estimasi manual">Manual</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Gross Profit (Rp) --}}
                            <td class="px-5 py-3.5 font-mono text-emerald-700 font-semibold">
                                Rp {{ number_format($marginRp, 0, ',', '.') }}
                            </td>

                            {{-- Gross Margin (%) --}}
                            <td class="px-5 py-3.5 text-center font-mono">
                                @if($marginPct >= 65)
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-semibold">
                                        {{ $marginPct }}%
                                    </span>
                                @elseif($marginPct >= 40)
                                    <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 text-[11px] font-semibold">
                                        {{ $marginPct }}%
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200 text-[11px] font-semibold">
                                        {{ $marginPct }}%
                                    </span>
                                @endif
                            </td>

                            {{-- Recipe Composition Summary --}}
                            <td class="px-5 py-3.5 text-xs text-neutral-600 font-mono max-w-sm">
                                @if($hasRecipe)
                                    @php
                                        $compositionList = $menu->recipes->map(function($r) {
                                            $qty = (float) $r->quantity_used;
                                            $unit = $r->rawMaterial?->unit ?? '';
                                            $name = $r->rawMaterial?->name ?? 'Bahan';
                                            return "{$name} ({$qty} {$unit})";
                                        })->join(', ');
                                    @endphp
                                    <div class="space-y-1">
                                        <div class="flex items-center space-x-1 text-[11px] font-semibold text-neutral-800">
                                            <i class="fas fa-cubes-stacked text-amber-600 text-[10px]"></i>
                                            <span>{{ $menu->recipes->count() }} Bahan:</span>
                                        </div>
                                        <p class="text-[11px] text-neutral-600 leading-snug line-clamp-2" title="{{ $compositionList }}">
                                            {{ $compositionList }}
                                        </p>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono bg-neutral-100 text-neutral-400 border border-neutral-200">
                                        <i class="fas fa-circle-exclamation text-[9px] mr-1"></i> Belum diset resep
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-3.5 text-right font-mono">
                                <button 
                                    type="button" 
                                    onclick="openRecipeModal({{ $menu->id_menu }})"
                                    class="text-[11px] font-medium bg-neutral-100 hover:bg-neutral-900 hover:text-white text-neutral-800 border border-neutral-200 px-3 py-1.5 rounded transition-colors inline-flex items-center space-x-1.5 shadow-2xs"
                                >
                                    <i class="fas fa-sliders text-[10px]"></i>
                                    <span>Atur Resep</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-xs font-mono text-neutral-400">
                                Tidak ada data menu yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- TAB 2: MASTER RAW MATERIALS TABLE --}}
        <div id="sectionRawMat" class="hidden overflow-x-auto">
            <table class="w-full text-left text-xs" id="tableRawMat">
                <thead class="bg-neutral-50 border-b border-neutral-200 text-neutral-500 text-[10px] font-mono uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Nama Bahan Baku</th>
                        <th class="px-5 py-3 font-semibold">Satuan Unit</th>
                        <th class="px-5 py-3 font-semibold">Harga Beli per Unit</th>
                        <th class="px-5 py-3 font-semibold">Stok Saat Ini</th>
                        <th class="px-5 py-3 font-semibold">Catatan / Keterangan</th>
                        <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 font-sans">
                    @forelse($rawMaterials as $mat)
                        <tr class="mat-row hover:bg-neutral-50/75 transition-colors">
                            <td class="px-5 py-3.5 font-semibold text-neutral-900 font-mono">
                                {{ $mat->name }}
                            </td>
                            <td class="px-5 py-3.5 font-mono">
                                <span class="px-2 py-0.5 rounded bg-neutral-100 text-neutral-700 border border-neutral-200 text-[11px]">
                                    {{ $mat->unit }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 font-mono text-neutral-900 font-medium">
                                Rp {{ number_format($mat->purchase_price, 2, ',', '.') }} / {{ $mat->unit }}
                            </td>
                            <td class="px-5 py-3.5 font-mono">
                                <span class="{{ $mat->stock_quantity <= 10 ? 'text-amber-700 font-semibold' : 'text-neutral-700' }}">
                                    {{ (float) $mat->stock_quantity }} {{ $mat->unit }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-neutral-500 text-xs">
                                {{ $mat->notes ?: '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-mono space-x-1">
                                <button 
                                    type="button" 
                                    onclick="openEditRawMaterialModal({{ json_encode($mat) }})"
                                    class="text-[11px] text-neutral-700 hover:text-neutral-900 border border-neutral-200 px-2.5 py-1 rounded bg-white hover:bg-neutral-50"
                                    title="Edit Bahan"
                                >
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.hpp.raw-material.destroy', $mat->id) }}" class="inline-block m-0" onsubmit="return confirm('Hapus bahan {{ addslashes($mat->name) }}? Menu yang menggunakan bahan ini akan disinkronkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[11px] text-rose-600 hover:text-rose-800 border border-rose-200 px-2.5 py-1 rounded bg-rose-50 hover:bg-rose-100" title="Hapus Bahan">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-xs font-mono text-neutral-400">
                                Belum ada bahan baku terdaftar. Klik "+ Tambah Bahan Baku" untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

{{-- ========================================================================= --}}
{{-- MODAL ATUR RESEP & KALKULATOR HPP REALTIME --}}
{{-- ========================================================================= --}}
<div id="recipeModal" class="hidden fixed inset-0 z-50 bg-black/45 backdrop-blur-2xs flex items-center justify-center p-4">
    <div class="bg-white border border-neutral-200 rounded-xl shadow-xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]" onclick="event.stopPropagation()">
        
        {{-- Modal Header --}}
        <div class="px-6 py-4 border-b border-neutral-200 flex items-center justify-between bg-neutral-50/70 shrink-0">
            <div>
                <span class="text-[10px] font-mono text-neutral-500 uppercase tracking-widest block">KOMPOSISI RESEP PRODUK</span>
                <h3 class="text-sm font-semibold text-neutral-900 font-mono" id="recipeModalMenuName">Loading...</h3>
            </div>
            <button type="button" onclick="closeRecipeModal()" class="text-neutral-400 hover:text-neutral-700 text-base">&times;</button>
        </div>

        {{-- Modal Form Body --}}
        <form id="recipeForm" method="POST" action="" class="flex-1 flex flex-col overflow-hidden m-0">
            @csrf
            <div class="p-6 overflow-y-auto space-y-5 flex-1 text-xs">
                
                {{-- Price & Summary Bar --}}
                <div class="grid grid-cols-3 gap-3 p-3.5 bg-neutral-50 border border-neutral-200 rounded-lg text-xs font-mono">
                    <div>
                        <span class="text-neutral-500 text-[10px] block">HARGA JUAL</span>
                        <strong class="text-neutral-900 text-sm" id="recipeModalSellingPrice">Rp 0</strong>
                    </div>
                    <div>
                        <span class="text-neutral-500 text-[10px] block">TOTAL HPP RESEP</span>
                        <strong class="text-[#C27835] text-sm" id="recipeModalCalculatedHpp">Rp 0</strong>
                    </div>
                    <div>
                        <span class="text-neutral-500 text-[10px] block">ESTIMASI MARGIN</span>
                        <strong class="text-emerald-700 text-sm" id="recipeModalCalculatedMargin">Rp 0 (0%)</strong>
                    </div>
                </div>

                {{-- Ingredients Repeater Section --}}
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="font-semibold text-neutral-900 uppercase tracking-wider text-[11px] font-mono">Bahan Baku & Takaran per Porsi</label>
                        <button type="button" onclick="addRecipeIngredientRow()" class="text-xs font-mono text-[#C27835] hover:text-[#A05C22] font-semibold inline-flex items-center space-x-1">
                            <i class="fas fa-plus text-[10px]"></i>
                            <span>+ Tambah Bahan</span>
                        </button>
                    </div>

                    <div id="ingredientRowsContainer" class="space-y-2">
                        {{-- Dynamic ingredient rows will be injected here --}}
                    </div>
                </div>

                {{-- Manual Fallback Note --}}
                <div class="border-t border-neutral-100 pt-3 flex items-center justify-between text-[11px] text-neutral-500 font-mono">
                    <span>* HPP otomatis dihitung dari: [Takaran &times; Harga Beli Bahan]</span>
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-3.5 border-t border-neutral-200 bg-neutral-50/70 flex items-center justify-between shrink-0 font-mono text-xs">
                <button type="button" onclick="closeRecipeModal()" class="px-3.5 py-1.5 rounded border border-neutral-200 bg-white text-neutral-600 hover:bg-neutral-50">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded bg-stone-900 text-white hover:bg-black font-semibold shadow-2xs">
                    Simpan Resep & Perbarui HPP
                </button>
            </div>
        </form>

    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL TAMBAH / EDIT BAHAN BAKU --}}
{{-- ========================================================================= --}}
<div id="rawMaterialModal" class="hidden fixed inset-0 z-50 bg-black/45 backdrop-blur-2xs flex items-center justify-center p-4">
    <div class="bg-white border border-neutral-200 rounded-xl shadow-xl w-full max-w-md overflow-hidden" onclick="event.stopPropagation()">
        
        <div class="px-6 py-4 border-b border-neutral-200 flex items-center justify-between bg-neutral-50/70">
            <h3 class="text-xs font-mono uppercase tracking-wider font-semibold text-neutral-900" id="rawMaterialModalTitle">
                Tambah Bahan Baku Baru
            </h3>
            <button type="button" onclick="closeRawMaterialModal()" class="text-neutral-400 hover:text-neutral-700 text-base">&times;</button>
        </div>

        <form id="rawMaterialForm" method="POST" action="{{ route('admin.hpp.raw-material.store') }}" class="m-0">
            @csrf
            <div id="rawMaterialMethodField"></div>

            <div class="p-6 space-y-4 text-xs">
                <div class="space-y-1.5">
                    <label class="font-medium text-neutral-700">Nama Bahan Baku</label>
                    <input type="text" name="name" id="matInputName" required placeholder="Contoh: Kopi Arabika House Blend, Susu UHT 1L, Cup 14oz" class="w-full bg-white border border-neutral-200 rounded-md px-3 py-1.5 text-xs text-neutral-900 focus:outline-none focus:ring-1 focus:ring-stone-800">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="font-medium text-neutral-700">Satuan Unit</label>
                        <select name="unit" id="matInputUnit" required class="w-full bg-white border border-neutral-200 rounded-md px-3 py-1.5 text-xs text-neutral-900 focus:outline-none focus:ring-1 focus:ring-stone-800">
                            <option value="gr">Gram (gr)</option>
                            <option value="ml">Mililiter (ml)</option>
                            <option value="pcs">Pieces (pcs)</option>
                            <option value="shot">Shot (shot)</option>
                            <option value="pump">Pump (pump)</option>
                            <option value="porsi">Porsi (porsi)</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="font-medium text-neutral-700">Harga per Satuan (Rp)</label>
                        <input type="number" step="0.01" name="purchase_price" id="matInputPrice" required placeholder="Contoh: 150 (artinya Rp 150/gr)" class="w-full bg-white border border-neutral-200 rounded-md px-3 py-1.5 text-xs text-neutral-900 focus:outline-none focus:ring-1 focus:ring-stone-800 font-mono">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="font-medium text-neutral-700">Stok Awal (Opsional)</label>
                    <input type="number" step="0.01" name="stock_quantity" id="matInputStock" placeholder="0" class="w-full bg-white border border-neutral-200 rounded-md px-3 py-1.5 text-xs text-neutral-900 focus:outline-none focus:ring-1 focus:ring-stone-800 font-mono">
                </div>

                <div class="space-y-1.5">
                    <label class="font-medium text-neutral-700">Catatan / Keterangan</label>
                    <textarea name="notes" id="matInputNotes" rows="2" placeholder="Contoh: Beli per pack 1kg seharga Rp 150.000 (Rp 150/gr)" class="w-full bg-white border border-neutral-200 rounded-md px-3 py-1.5 text-xs text-neutral-900 focus:outline-none focus:ring-1 focus:ring-stone-800"></textarea>
                </div>
            </div>

            <div class="px-6 py-3 border-t border-neutral-200 bg-neutral-50 flex justify-end space-x-2 text-xs font-mono">
                <button type="button" onclick="closeRawMaterialModal()" class="px-3.5 py-1.5 rounded border border-neutral-200 bg-white text-neutral-600 hover:bg-neutral-50">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 rounded bg-stone-900 text-white hover:bg-black font-semibold">
                    Simpan Bahan
                </button>
            </div>
        </form>

    </div>
</div>

<script>
let currentActiveMainTab = 'menu-hpp';
let availableMaterialsData = [];
let currentEditingMenuPrice = 0;

function switchMainTab(tab) {
    currentActiveMainTab = tab;
    const btnMenu = document.getElementById('tabBtnMenuHpp');
    const btnRaw = document.getElementById('tabBtnRawMat');
    const secMenu = document.getElementById('sectionMenuHpp');
    const secRaw = document.getElementById('sectionRawMat');
    const btnAction = document.getElementById('btnHeaderAction');

    if (tab === 'menu-hpp') {
        btnMenu.className = 'px-3.5 py-1.5 rounded-md font-mono text-xs font-semibold bg-white text-neutral-900 shadow-2xs transition-colors';
        btnRaw.className = 'px-3.5 py-1.5 rounded-md font-mono text-xs font-medium text-neutral-600 hover:text-neutral-900 transition-colors';
        secMenu.classList.remove('hidden');
        secRaw.classList.add('hidden');
        btnAction.classList.add('hidden');
    } else {
        btnRaw.className = 'px-3.5 py-1.5 rounded-md font-mono text-xs font-semibold bg-white text-neutral-900 shadow-2xs transition-colors';
        btnMenu.className = 'px-3.5 py-1.5 rounded-md font-mono text-xs font-medium text-neutral-600 hover:text-neutral-900 transition-colors';
        secRaw.classList.remove('hidden');
        secMenu.classList.add('hidden');
        btnAction.classList.remove('hidden');
    }
}

function filterHppTable() {
    const q = document.getElementById('hppSearchInput').value.toLowerCase();
    
    if (currentActiveMainTab === 'menu-hpp') {
        document.querySelectorAll('#tableMenuHpp tbody .menu-row').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
        });
    } else {
        document.querySelectorAll('#tableRawMat tbody .mat-row').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
        });
    }
}

// -------------------------------------------------------------
// RECIPE MODAL & DYNAMIC REPEATER
// -------------------------------------------------------------
function openRecipeModal(menuId) {
    const modal = document.getElementById('recipeModal');
    const form = document.getElementById('recipeForm');
    const container = document.getElementById('ingredientRowsContainer');
    
    form.action = `/admin/hpp/recipe/${menuId}`;
    container.innerHTML = '<div class="text-center py-4 text-neutral-400 font-mono">Memuat resep...</div>';
    modal.classList.remove('hidden');

    fetch(`/admin/hpp/recipe/${menuId}`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('recipeModalMenuName').innerText = data.menu.nama_menu;
                currentEditingMenuPrice = data.menu.harga;
                document.getElementById('recipeModalSellingPrice').innerText = 'Rp ' + Number(data.menu.harga).toLocaleString('id-ID');
                
                availableMaterialsData = data.available_materials || [];
                container.innerHTML = '';

                if (data.recipes && data.recipes.length > 0) {
                    data.recipes.forEach(r => {
                        addRecipeIngredientRow(r.raw_material_id, r.quantity_used);
                    });
                } else {
                    addRecipeIngredientRow(); // Add empty initial row
                }

                recalculateRecipeHppRealtime();
            }
        })
        .catch(() => {
            container.innerHTML = '<div class="text-center py-4 text-rose-600 font-mono">Gagal memuat resep.</div>';
        });
}

function closeRecipeModal() {
    document.getElementById('recipeModal').classList.add('hidden');
}

function addRecipeIngredientRow(selectedId = '', quantity = 1) {
    const container = document.getElementById('ingredientRowsContainer');
    const rowIndex = container.children.length;

    let optionsHtml = '<option value="">-- Pilih Bahan Baku --</option>';
    availableMaterialsData.forEach(mat => {
        const isSelected = mat.id == selectedId ? 'selected' : '';
        optionsHtml += `<option value="${mat.id}" data-price="${mat.purchase_price}" data-unit="${mat.unit}" ${isSelected}>${mat.name} (Rp ${Number(mat.purchase_price).toLocaleString('id-ID')} / ${mat.unit})</option>`;
    });

    const rowDiv = document.createElement('div');
    rowDiv.className = 'ingredient-row flex items-center space-x-2 bg-neutral-50 p-2.5 rounded-lg border border-neutral-200';
    rowDiv.innerHTML = `
        <div class="flex-1">
            <select name="ingredients[${rowIndex}][raw_material_id]" required onchange="recalculateRecipeHppRealtime()" class="select-material w-full bg-white border border-neutral-200 rounded-md px-2.5 py-1.5 text-xs text-neutral-900 focus:outline-none focus:ring-1 focus:ring-stone-800">
                ${optionsHtml}
            </select>
        </div>
        <div class="w-32 flex items-center space-x-1">
            <input type="number" step="0.001" min="0.001" name="ingredients[${rowIndex}][quantity_used]" value="${quantity}" required oninput="recalculateRecipeHppRealtime()" placeholder="Takaran" class="input-qty w-full bg-white border border-neutral-200 rounded-md px-2 py-1.5 text-xs text-neutral-900 text-right font-mono focus:outline-none focus:ring-1 focus:ring-stone-800">
            <span class="unit-label text-[10px] font-mono text-neutral-500 w-8">-</span>
        </div>
        <div class="w-24 text-right font-mono font-semibold text-neutral-800 text-xs cost-label">
            Rp 0
        </div>
        <button type="button" onclick="this.closest('.ingredient-row').remove(); recalculateRecipeHppRealtime();" class="p-1.5 text-neutral-400 hover:text-rose-600 rounded transition-colors" title="Hapus Bahan">
            <i class="fas fa-trash-can text-xs"></i>
        </button>
    `;

    container.appendChild(rowDiv);
    recalculateRecipeHppRealtime();
}

function recalculateRecipeHppRealtime() {
    const rows = document.querySelectorAll('#ingredientRowsContainer .ingredient-row');
    let totalHpp = 0;

    rows.forEach(row => {
        const select = row.querySelector('.select-material');
        const inputQty = row.querySelector('.input-qty');
        const unitLabel = row.querySelector('.unit-label');
        const costLabel = row.querySelector('.cost-label');

        const selectedOption = select.selectedOptions[0];
        const pricePerUnit = selectedOption ? parseFloat(selectedOption.getAttribute('data-price') || 0) : 0;
        const unit = selectedOption ? (selectedOption.getAttribute('data-unit') || '-') : '-';
        const qty = parseFloat(inputQty.value || 0);

        unitLabel.innerText = unit;
        const itemCost = qty * pricePerUnit;
        costLabel.innerText = 'Rp ' + Math.round(itemCost).toLocaleString('id-ID');
        totalHpp += itemCost;
    });

    totalHpp = Math.round(totalHpp);
    const grossProfit = Math.max(0, currentEditingMenuPrice - totalHpp);
    const marginPct = currentEditingMenuPrice > 0 ? Math.round((grossProfit / currentEditingMenuPrice) * 100) : 0;

    document.getElementById('recipeModalCalculatedHpp').innerText = 'Rp ' + totalHpp.toLocaleString('id-ID');
    document.getElementById('recipeModalCalculatedMargin').innerText = 'Rp ' + grossProfit.toLocaleString('id-ID') + ' (' + marginPct + '%)';
}

// -------------------------------------------------------------
// RAW MATERIAL MODAL
// -------------------------------------------------------------
function openRawMaterialModal() {
    const modal = document.getElementById('rawMaterialModal');
    const form = document.getElementById('rawMaterialForm');
    document.getElementById('rawMaterialModalTitle').innerText = 'Tambah Bahan Baku Baru';
    document.getElementById('rawMaterialMethodField').innerHTML = '';
    form.action = "{{ route('admin.hpp.raw-material.store') }}";
    form.reset();
    modal.classList.remove('hidden');
}

function openEditRawMaterialModal(mat) {
    const modal = document.getElementById('rawMaterialModal');
    const form = document.getElementById('rawMaterialForm');
    document.getElementById('rawMaterialModalTitle').innerText = 'Edit Bahan: ' + mat.name;
    document.getElementById('rawMaterialMethodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    form.action = `/admin/hpp/raw-material/${mat.id}`;

    document.getElementById('matInputName').value = mat.name;
    document.getElementById('matInputUnit').value = mat.unit;
    document.getElementById('matInputPrice').value = mat.purchase_price;
    document.getElementById('matInputStock').value = mat.stock_quantity;
    document.getElementById('matInputNotes').value = mat.notes || '';

    modal.classList.remove('hidden');
}

function closeRawMaterialModal() {
    document.getElementById('rawMaterialModal').classList.add('hidden');
}
</script>
@endsection

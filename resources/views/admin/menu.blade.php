@extends('dashboard')

@section('page-title', 'Daftar Menu Produk')
@section('breadcrumb', 'Manajemen Katalog Menu')

@section('content')
<div class="product-page-container px-4 py-2">

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="mb-4 flex items-center p-4 text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-100 shadow-sm transition-all" role="alert">
            <i class="fas fa-check-circle mr-3 text-lg text-emerald-600"></i>
            <div class="text-sm font-semibold">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 flex items-center p-4 text-rose-800 bg-rose-50 rounded-xl border border-rose-100 shadow-sm transition-all" role="alert">
            <i class="fas fa-exclamation-circle mr-3 text-lg text-rose-600"></i>
            <div class="text-sm font-semibold">{{ session('error') }}</div>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="mb-4 p-4 text-rose-800 bg-rose-50 rounded-xl border border-rose-100 shadow-sm" role="alert">
            <div class="flex items-center gap-2 mb-1 font-bold text-sm">
                <i class="fas fa-triangle-exclamation"></i> Terdapat kesalahan input:
            </div>
            <ul class="text-xs list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Page Header & Filter Bar --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-6 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold text-stone-800">Daftar Menu Produk</h2>
                <span class="text-stone-600 text-xs font-semibold bg-stone-100 border border-stone-200 px-2.5 py-1 rounded-full">
                    Total {{ $menus->count() }} menu
                </span>
            </div>
            <p class="text-stone-500 text-xs mt-1">Kelola katalog produk, harga jual, HPP, serta pantau persediaan stok real-time.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            {{-- Search Bar --}}
            <form method="GET" action="{{ route('admin.menu') }}" class="relative group">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 group-focus-within:text-[#C87D38] transition-colors text-xs"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama menu..."
                    class="pl-9 pr-3 py-2 bg-white border border-stone-200 rounded-lg text-xs focus:ring-2 focus:ring-[#C87D38]/20 focus:border-[#C87D38] outline-none transition-all w-48 md:w-56 shadow-sm">
            </form>

            {{-- Category Filter --}}
            @if(isset($categories))
            <select onchange="window.location.href=this.value" class="bg-white border border-stone-200 rounded-lg text-xs py-2 px-3 outline-none focus:border-[#C87D38] shadow-sm cursor-pointer text-stone-700">
                <option value="{{ route('admin.menu') }}">Semua Kategori ({{ $categories->sum('menus_count') }})</option>
                @foreach($categories as $cat)
                    <option value="{{ route('admin.menu', ['category' => $cat->id]) }}" {{ ($selectedCategory ?? '') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nama_kategori }} ({{ $cat->menus_count }})
                    </option>
                @endforeach
            </select>
            @endif

            {{-- Kelola Kategori Button --}}
            <button type="button" onclick="openCategoryModal()" class="bg-stone-100 hover:bg-stone-200 text-stone-700 border border-stone-200 px-3.5 py-2 rounded-lg text-xs font-semibold shadow-sm transition-all flex items-center gap-1.5 cursor-pointer">
                <i class="fas fa-tags text-[#C87D38]"></i> Kelola Kategori
            </button>

            {{-- Tambah Menu Button --}}
            <button type="button" onclick="openCreateMenuModal()" class="bg-[#C87D38] hover:bg-[#A8642A] text-white px-4 py-2 rounded-lg text-xs font-semibold shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                <i class="fas fa-plus"></i> Tambah Menu
            </button>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-stone-50 border-b border-stone-200">
                    <tr>
                        <th class="px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wider w-12 text-center">No</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wider">Informasi Produk</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wider">Harga Pokok (HPP)</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Harga Jual</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wider text-center">Margin Laba</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wider text-center">Stok & Status</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wider text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($menus as $index => $menu)
                        <tr class="hover:bg-stone-50/60 transition-colors group">
                            <td class="px-5 py-3.5 text-xs text-stone-400 font-medium text-center">{{ $index + 1 }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-11 h-11 rounded-lg overflow-hidden bg-stone-100 border border-stone-200 flex-shrink-0 flex items-center justify-center shadow-xs">
                                        @if($menu->foto)
                                            <img src="/storage/{{ $menu->foto }}" alt="{{ $menu->nama_menu }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="flex flex-col items-center">
                                                <i class="fas fa-mug-hot text-stone-300 text-xs"></i>
                                                <span class="text-[8px] font-bold text-stone-400 uppercase mt-0.5">{{ substr($menu->nama_menu, 0, 2) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex flex-col max-w-xs">
                                        <span class="text-sm font-bold text-[#1C1917] leading-snug">{{ $menu->nama_menu }}</span>
                                        <span class="text-[11px] text-stone-400 mt-0.5 truncate">{{ $menu->deskripsi ?: 'Variasi Standar' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 uppercase tracking-wide">
                                    <i class="fas fa-tag text-[9px] mr-1 opacity-70"></i>
                                    {{ $menu->categoryRelation?->nama_kategori ?? ($menu->kategori ?? 'UMUM') }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex flex-col">
                                    <span class="text-xs text-stone-800 font-bold">Rp {{ number_format($menu->hpp ?? 0, 0, ',', '.') }}</span>
                                    @if($menu->recipes && $menu->recipes->count() > 0)
                                        @php
                                            $recipeTooltip = $menu->recipes->map(fn($r) => ($r->rawMaterial?->name ?? 'Bahan') . ' (' . (float)$r->quantity_used . ' ' . ($r->rawMaterial?->unit ?? '') . ')')->join(', ');
                                        @endphp
                                        <span class="text-[10px] text-amber-700 font-mono flex items-center gap-1 mt-0.5" title="{{ $recipeTooltip }}">
                                            <i class="fas fa-cubes-stacked text-[9px]"></i> {{ $menu->recipes->count() }} Bahan
                                        </span>
                                    @else
                                        <span class="text-[10px] text-stone-400 italic">Belum diset resep</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <span class="text-sm font-bold text-stone-800">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @php
                                    $profit = max(0, ($menu->harga ?? 0) - ($menu->hpp ?? 0));
                                    $profitPct = ($menu->harga > 0) ? round(($profit / $menu->harga) * 100) : 0;
                                @endphp
                                <span class="text-xs font-semibold text-emerald-600 block">+Rp {{ number_format($profit, 0, ',', '.') }}</span>
                                <span class="text-[10px] text-stone-400">({{ $profitPct }}%)</span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="text-xs font-bold {{ ($menu->stok ?? 0) <= 5 ? 'text-rose-600' : (($menu->stok ?? 0) <= 15 ? 'text-amber-600' : 'text-stone-700') }}">
                                        {{ $menu->stok ?? 0 }} Unit
                                    </span>
                                    @if(!$menu->status_tersedia || ($menu->stok ?? 0) <= 0)
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 text-rose-600 border border-rose-200 uppercase">
                                            <i class="fas fa-ban text-[8px] mr-0.5"></i> Habis
                                        </span>
                                    @elseif(($menu->stok ?? 0) <= 10)
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase">
                                            <i class="fas fa-triangle-exclamation text-[8px] mr-0.5"></i> Menipis
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200 uppercase">
                                            <i class="fas fa-check text-[8px] mr-0.5"></i> Tersedia
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-center gap-1.5 opacity-80 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('admin.menu.edit', $menu->id_menu) }}" class="p-1.5 text-stone-500 hover:text-[#C87D38] hover:bg-amber-50 rounded-lg transition-all" title="Edit Menu">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.menu.delete', $menu->id_menu) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu {{ $menu->nama_menu }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-stone-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="Hapus Menu">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-stone-50 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-mug-hot text-stone-300 text-2xl"></i>
                                    </div>
                                    <h3 class="text-stone-800 font-bold text-sm">Menu Tidak Ditemukan</h3>
                                    <p class="text-stone-500 text-xs max-w-xs mx-auto mt-1">Silakan coba kata kunci pencarian lain atau tambahkan produk menu baru.</p>
                                    <button onclick="openCreateMenuModal()" class="mt-4 bg-[#C87D38] text-white px-4 py-1.5 rounded-lg text-xs font-semibold shadow-sm hover:bg-[#A8642A] transition-all">
                                        + Tambah Menu Baru
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL 1: TAMBAH MENU PRODUK --}}
<div id="createMenuModal" class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/60 backdrop-blur-xs hidden p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border border-stone-100 animate-fadeIn">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between p-5 border-b border-stone-100 bg-stone-50/50 rounded-t-2xl">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-[#C87D38] flex items-center justify-center font-bold">
                    <i class="fas fa-plus"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-stone-800">Tambah Menu Produk Baru</h3>
                    <p class="text-xs text-stone-500">Lengkapi data produk, harga jual, HPP, dan stok awal</p>
                </div>
            </div>
            <button type="button" onclick="closeCreateMenuModal()" class="w-8 h-8 rounded-lg text-stone-400 hover:text-stone-600 hover:bg-stone-100 flex items-center justify-center transition-all cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        {{-- Modal Body Form --}}
        <form method="POST" action="{{ route('admin.menu.store') }}" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf

            {{-- 1. Nama Menu --}}
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1" for="modal_nama_menu">
                    Nama Menu / Produk <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="modal_nama_menu" name="nama_menu" required placeholder="Contoh: Kopi Susu Gula Aren, Croissant..."
                    class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-xs text-stone-800 focus:bg-white focus:ring-2 focus:ring-[#C87D38]/20 focus:border-[#C87D38] outline-none transition-all">
            </div>

            {{-- 2. Kategori Dropdown + Inline Add --}}
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-bold text-stone-700" for="modal_id_kategori">
                        Kategori <span class="text-rose-500">*</span>
                    </label>
                    <button type="button" onclick="openCategoryModal()" class="text-[11px] text-[#C87D38] font-bold hover:underline cursor-pointer">
                        + Tambah / Kelola Kategori
                    </button>
                </div>
                <select id="modal_id_kategori" name="id_kategori" required
                    class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-xs text-stone-800 focus:bg-white focus:ring-2 focus:ring-[#C87D38]/20 focus:border-[#C87D38] outline-none transition-all">
                    <option value="" disabled selected>-- Pilih Kategori --</option>
                    @if(isset($categories))
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            {{-- 3. Pricing (Harga Jual) --}}
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1" for="modal_harga">
                    Harga Jual (Rp) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-stone-400">Rp</span>
                    <input type="number" id="modal_harga" name="harga" required min="0" placeholder="25000"
                        class="w-full pl-10 pr-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-xs text-stone-800 focus:bg-white focus:ring-2 focus:ring-[#C87D38]/20 focus:border-[#C87D38] outline-none transition-all">
                </div>
                <p class="text-[11px] text-stone-500 mt-1.5 flex items-center gap-1.5">
                    <i class="fas fa-info-circle text-[#B45309]"></i> HPP modal produk akan dihitung otomatis dari resep bahan baku di menu <a href="{{ route('admin.hpp') }}" target="_blank" class="text-[#B45309] font-medium underline">HPP & Resep</a>.
                </p>
            </div>

            {{-- 4. Stok Awal & Status --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1" for="modal_stok">
                        Stok Awal (Unit/Porsi) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="modal_stok" name="stok" value="50" min="0" required
                        class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-xs text-stone-800 focus:bg-white focus:ring-2 focus:ring-[#C87D38]/20 focus:border-[#C87D38] outline-none transition-all">
                </div>

                <div class="flex flex-col justify-end">
                    <label class="flex items-center gap-2.5 p-2.5 bg-stone-50 border border-stone-200 rounded-xl cursor-pointer hover:bg-stone-100/50 transition-all">
                        <input type="checkbox" name="status_tersedia" value="1" checked class="w-4 h-4 text-[#C87D38] rounded border-stone-300 focus:ring-[#C87D38]">
                        <span class="text-xs font-bold text-stone-700">Tersedia untuk Dijual</span>
                    </label>
                </div>
            </div>

            {{-- 5. Deskripsi / Variasi --}}
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1" for="modal_deskripsi">
                    Deskripsi / Variasi Singkat
                </label>
                <textarea id="modal_deskripsi" name="deskripsi" rows="2" placeholder="Contoh: Menggunakan biji arabika pilihan dan gula aren murni..."
                    class="w-full px-3.5 py-2 bg-stone-50 border border-stone-200 rounded-xl text-xs text-stone-800 focus:bg-white focus:ring-2 focus:ring-[#C87D38]/20 focus:border-[#C87D38] outline-none transition-all"></textarea>
            </div>

            {{-- 6. Upload Foto Produk --}}
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1" for="modal_foto">
                    Foto Produk / Gambar
                </label>
                <input type="file" id="modal_foto" name="foto" accept="image/*"
                    class="w-full text-xs text-stone-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-[#C87D38] hover:file:bg-amber-100 cursor-pointer">
            </div>

            {{-- Modal Actions --}}
            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-stone-100">
                <button type="button" onclick="closeCreateMenuModal()" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-semibold transition-all cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-[#C87D38] hover:bg-[#A8642A] text-white rounded-xl text-xs font-bold shadow-md transition-all cursor-pointer flex items-center gap-1.5">
                    <i class="fas fa-check"></i> Simpan Menu
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 2: KELOLA KATEGORI DINAMIS --}}
<div id="categoryModal" class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/60 backdrop-blur-xs hidden p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto border border-stone-100 animate-fadeIn">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between p-5 border-b border-stone-100 bg-stone-50/50 rounded-t-2xl">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-[#C87D38] flex items-center justify-center font-bold">
                    <i class="fas fa-tags"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-stone-800">Kelola Kategori</h3>
                    <p class="text-xs text-stone-500">Tambah dan atur kategori menu secara dinamis</p>
                </div>
            </div>
            <button type="button" onclick="closeCategoryModal()" class="w-8 h-8 rounded-lg text-stone-400 hover:text-stone-600 hover:bg-stone-100 flex items-center justify-center transition-all cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <div class="p-5 space-y-4">
            {{-- Form Tambah Kategori Baru --}}
            <form method="POST" action="{{ route('admin.categories.store') }}" class="flex items-center gap-2">
                @csrf
                <input type="text" name="nama_kategori" required placeholder="Nama Kategori Baru..."
                    class="flex-1 px-3.5 py-2 bg-stone-50 border border-stone-200 rounded-xl text-xs text-stone-800 focus:bg-white focus:ring-2 focus:ring-[#C87D38]/20 focus:border-[#C87D38] outline-none transition-all">
                <button type="submit" class="px-3.5 py-2 bg-[#C87D38] hover:bg-[#A8642A] text-white rounded-xl text-xs font-bold shadow-xs transition-all flex items-center gap-1 cursor-pointer">
                    <i class="fas fa-plus"></i> Tambah
                </button>
            </form>

            {{-- List Kategori --}}
            <div class="border border-stone-200 rounded-xl overflow-hidden divide-y divide-stone-100">
                <div class="px-3.5 py-2 bg-stone-50 text-[11px] font-bold text-stone-500 uppercase tracking-wider flex justify-between">
                    <span>Daftar Kategori</span>
                    <span>Jumlah Menu</span>
                </div>
                <div class="max-h-60 overflow-y-auto divide-y divide-stone-100">
                    @if(isset($categories))
                        @forelse($categories as $cat)
                            <div class="px-3.5 py-2.5 flex items-center justify-between text-xs hover:bg-stone-50 transition-colors">
                                <span class="font-semibold text-stone-700">{{ $cat->nama_kategori }}</span>
                                <div class="flex items-center gap-3">
                                    <span class="text-[11px] font-bold text-stone-400 bg-stone-100 px-2 py-0.5 rounded-full">
                                        {{ $cat->menus_count ?? (method_exists($cat, 'menus') ? $cat->menus()->count() : 0) }}
                                    </span>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $cat->id) }}" class="inline" onsubmit="return confirm('Hapus kategori {{ $cat->nama_kategori }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-stone-300 hover:text-rose-600 transition-colors cursor-pointer" title="Hapus Kategori">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="px-3.5 py-4 text-center text-xs text-stone-400">
                                Belum ada kategori
                            </div>
                        @endforelse
                    @endif
                </div>
            </div>
        </div>

        <div class="p-4 border-t border-stone-100 text-right bg-stone-50/50 rounded-b-2xl">
            <button type="button" onclick="closeCategoryModal()" class="px-4 py-1.5 bg-stone-200 hover:bg-stone-300 text-stone-700 rounded-lg text-xs font-semibold transition-all cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function openCreateMenuModal() {
        document.getElementById('createMenuModal').classList.remove('hidden');
    }

    function closeCreateMenuModal() {
        document.getElementById('createMenuModal').classList.add('hidden');
    }

    function openCategoryModal() {
        document.getElementById('categoryModal').classList.remove('hidden');
    }

    function closeCategoryModal() {
        document.getElementById('categoryModal').classList.add('hidden');
    }

    // Close modal when clicking on backdrop
    window.addEventListener('click', function(e) {
        const createModal = document.getElementById('createMenuModal');
        const catModal = document.getElementById('categoryModal');
        if (e.target === createModal) closeCreateMenuModal();
        if (e.target === catModal) closeCategoryModal();
    });
</script>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.97); }
        to { opacity: 1; transform: scale(1); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.15s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>
@endsection

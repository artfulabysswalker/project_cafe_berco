<div class="space-y-6">

    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center justify-between shadow-2xs">
            <div class="flex items-center space-x-2">
                <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                <span class="font-medium">{{ session('message') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- 1. KPI Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Item --}}
        <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-mono font-semibold uppercase tracking-wider text-stone-500">Total SKU Barang</span>
                <div class="w-7 h-7 rounded-lg bg-stone-100 flex items-center justify-center text-stone-600 text-xs">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
            </div>
            <div class="text-2xl font-bold font-mono text-stone-900">{{ $totalItems }}</div>
            <div class="text-[11px] text-stone-400 mt-1 font-mono">Bahan Baku & Kemasan</div>
        </div>

        {{-- Stok Aman --}}
        <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-mono font-semibold uppercase tracking-wider text-emerald-700">Stok Aman</span>
                <div class="w-7 h-7 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 text-xs">
                    <i class="fas fa-shield-check"></i>
                </div>
            </div>
            <div class="text-2xl font-bold font-mono text-emerald-600">{{ $safeCount }}</div>
            <div class="text-[11px] text-emerald-600/70 mt-1 font-mono">> Batas Minimum Stok</div>
        </div>

        {{-- Stok Menipis --}}
        <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-mono font-semibold uppercase tracking-wider text-amber-700">Stok Menipis</span>
                <div class="w-7 h-7 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 text-xs">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="text-2xl font-bold font-mono text-amber-600">{{ $warningCount }}</div>
            <div class="text-[11px] text-amber-600/70 mt-1 font-mono">Mendekati Batas Min</div>
        </div>

        {{-- Stok Kritis --}}
        <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-mono font-semibold uppercase tracking-wider text-red-700">Stok Kritis</span>
                <div class="w-7 h-7 rounded-lg bg-red-50 border border-red-200 flex items-center justify-center text-red-600 text-xs">
                    <i class="fas fa-circle-exclamation animate-pulse"></i>
                </div>
            </div>
            <div class="text-2xl font-bold font-mono text-red-600">{{ $criticalCount }}</div>
            <div class="text-[11px] text-red-600/70 mt-1 font-mono">Perlu Restock Segera!</div>
        </div>
    </div>

    {{-- 2. Filter Bar & Action Header --}}
    <div class="bg-white rounded-xl border border-stone-200 p-4 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1">
            {{-- Search Bar --}}
            <div class="relative flex-1 max-w-sm">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari SKU, nama barang, atau kategori..." 
                    class="w-full bg-stone-50 border border-stone-200 rounded-lg pl-8 pr-3 py-2 text-xs font-mono focus:outline-none focus:ring-1 focus:ring-[#C27835]"
                />
                <i class="fas fa-magnifying-glass text-stone-400 text-xs absolute left-2.5 top-2.5"></i>
            </div>

            {{-- Category Filter --}}
            <select wire:model.live="selectedCategory" class="text-xs font-mono bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#C27835]">
                <option value="all">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>

            {{-- Status Filter --}}
            <select wire:model.live="selectedStatus" class="text-xs font-mono bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#C27835]">
                <option value="all">Semua Status</option>
                <option value="aman">🟢 Stok Aman</option>
                <option value="menipis">🟡 Stok Menipis</option>
                <option value="kritis">🔴 Stok Kritis</option>
            </select>
        </div>

        {{-- Add Item Button --}}
        <button 
            type="button" 
            wire:click="openAddModal"
            class="bg-[#C27835] hover:bg-[#A05C22] text-white text-xs font-mono font-semibold px-4 py-2 rounded-lg transition-colors shadow-2xs flex items-center justify-center space-x-1.5 shrink-0">
            <i class="fas fa-plus text-[10px]"></i>
            <span>Tambah Item Stok</span>
        </button>
    </div>

    {{-- 3. Inventory Table --}}
    <div class="bg-white border border-stone-200 rounded-xl shadow-2xs overflow-hidden">
        <div class="px-5 py-3.5 border-b border-stone-200 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-[#C27835]"></span>
                <h3 class="text-xs font-mono uppercase tracking-wider font-bold text-stone-900">
                    Daftar Stok Bahan Baku & Perlengkapan Cafe
                </h3>
            </div>
            <span class="text-xs font-mono text-stone-500 bg-stone-100 px-2.5 py-0.5 rounded border border-stone-200">
                Menampilkan {{ $items->count() }} dari {{ $items->total() }} Item
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-mono">
                <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 text-[10px] uppercase">
                    <tr>
                        <th class="px-4 py-3">Kode SKU</th>
                        <th class="px-4 py-3">Nama Barang / Bahan</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3 text-right">Stok Saat Ini</th>
                        <th class="px-4 py-3 text-right">Min. Stok</th>
                        <th class="px-4 py-3 text-center">Status Stok</th>
                        <th class="px-4 py-3 text-center">Ubah Stok Cepat</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-stone-50/70 transition-colors">
                            {{-- Kode SKU --}}
                            <td class="px-4 py-3.5 font-bold text-stone-800">
                                <span class="px-2 py-0.5 rounded bg-stone-100 border border-stone-200 text-stone-700">
                                    {{ $item->item_code }}
                                </span>
                            </td>

                            {{-- Nama Barang --}}
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-stone-900">{{ $item->item_name }}</div>
                            </td>

                            {{-- Kategori --}}
                            <td class="px-4 py-3.5">
                                <span class="text-stone-600 bg-stone-100/80 px-2 py-0.5 rounded text-[11px]">
                                    {{ $item->category }}
                                </span>
                            </td>

                            {{-- Stok Saat Ini --}}
                            <td class="px-4 py-3.5 text-right font-bold {{ $item->status == 'kritis' ? 'text-red-600 font-extrabold' : ($item->status == 'menipis' ? 'text-amber-600' : 'text-stone-900') }}">
                                {{ number_format($item->current_stock, 0, ',', '.') }} {{ $item->unit }}
                            </td>

                            {{-- Batas Minimum --}}
                            <td class="px-4 py-3.5 text-right text-stone-500">
                                {{ number_format($item->min_stock, 0, ',', '.') }} {{ $item->unit }}
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-4 py-3.5 text-center">
                                @if($item->status == 'aman')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center space-x-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Aman</span>
                                    </span>
                                @elseif($item->status == 'menipis')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 inline-flex items-center space-x-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        <span>Menipis</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200 inline-flex items-center space-x-1 animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        <span>Kritis</span>
                                    </span>
                                @endif
                            </td>

                            {{-- Quick Adjust (+ / -) --}}
                            <td class="px-4 py-3.5 text-center">
                                <div class="inline-flex items-center rounded-lg border border-stone-200 bg-stone-50 p-0.5 space-x-1">
                                    <button 
                                        type="button" 
                                        wire:click="adjustStock({{ $item->id }}, -1)"
                                        class="w-6 h-6 rounded bg-white hover:bg-red-50 hover:text-red-600 text-stone-600 flex items-center justify-center text-xs font-bold border border-stone-200 shadow-2xs transition"
                                        title="Kurang 1">
                                        -
                                    </button>
                                    <button 
                                        type="button" 
                                        wire:click="adjustStock({{ $item->id }}, 1)"
                                        class="w-6 h-6 rounded bg-white hover:bg-emerald-50 hover:text-emerald-600 text-stone-600 flex items-center justify-center text-xs font-bold border border-stone-200 shadow-2xs transition"
                                        title="Tambah 1">
                                        +
                                    </button>
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3.5 text-right space-x-1">
                                <button 
                                    type="button" 
                                    wire:click="openEditModal({{ $item->id }})"
                                    class="p-1.5 rounded text-stone-600 hover:text-stone-900 hover:bg-stone-100 border border-stone-200 transition"
                                    title="Edit Item">
                                    <i class="fas fa-pencil text-xs"></i>
                                </button>
                                <button 
                                    type="button" 
                                    wire:click="deleteItem({{ $item->id }})"
                                    wire:confirm="Yakin ingin menghapus item '{{ $item->item_name }}' dari stok?"
                                    class="p-1.5 rounded text-red-600 hover:text-red-800 hover:bg-red-50 border border-red-200 transition"
                                    title="Hapus Item">
                                    <i class="fas fa-trash-can text-xs"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-stone-400 font-mono text-xs">
                                Tidak ada data stok barang yang cocok dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="px-5 py-3 border-t border-stone-200 bg-stone-50">
                {{ $items->links() }}
            </div>
        @endif
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL TAMBAH / EDIT BARANG STOK                         --}}
    {{-- ======================================================== --}}
    @if($isModalOpen)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full border border-stone-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-stone-200 flex items-center justify-between bg-stone-50">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-lg bg-[#C27835] text-white flex items-center justify-center text-xs">
                            <i class="fas fa-boxes-stacked"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-mono uppercase tracking-wider font-bold text-stone-900">
                                {{ $isEditMode ? 'Edit Item Stok' : 'Tambah Item Stok Baru' }}
                            </h3>
                            <p class="text-[11px] text-stone-500">Kelola inventori bahan & perlengkapan</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeModal" class="text-stone-400 hover:text-stone-600">
                        <i class="fas fa-xmark text-sm"></i>
                    </button>
                </div>

                <form wire:submit="saveItem" class="p-5 space-y-4">
                    {{-- Item Code & Name --}}
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-1">
                            <label class="block text-[11px] font-mono text-stone-700 mb-1 font-semibold uppercase">Kode SKU</label>
                            <input type="text" wire:model="item_code" placeholder="ING-001" class="w-full text-xs font-mono bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#C27835]">
                            @error('item_code') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-span-2">
                            <label class="block text-[11px] font-mono text-stone-700 mb-1 font-semibold uppercase">Nama Barang / Bahan</label>
                            <input type="text" wire:model="item_name" placeholder="Contoh: Biji Kopi Arabica" class="w-full text-xs font-mono bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#C27835]">
                            @error('item_name') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Category & Unit --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-mono text-stone-700 mb-1 font-semibold uppercase">Kategori</label>
                            <input type="text" wire:model="category" placeholder="Bahan Baku / Kemasan" class="w-full text-xs font-mono bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#C27835]">
                            @error('category') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-mono text-stone-700 mb-1 font-semibold uppercase">Satuan (Unit)</label>
                            <select wire:model="unit" class="w-full text-xs font-mono bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#C27835]">
                                <option value="Gram">Gram</option>
                                <option value="Kg">Kg</option>
                                <option value="Liter">Liter</option>
                                <option value="Ml">Ml</option>
                                <option value="Botol">Botol</option>
                                <option value="Pcs">Pcs</option>
                                <option value="Pack">Pack</option>
                                <option value="Box">Box</option>
                            </select>
                            @error('unit') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Current Stock & Min Stock --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-mono text-stone-700 mb-1 font-semibold uppercase">Stok Tersedia</label>
                            <input type="number" wire:model="current_stock" step="0.1" min="0" class="w-full text-sm font-mono font-bold bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#C27835]">
                            @error('current_stock') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-mono text-stone-700 mb-1 font-semibold uppercase">Batas Minimum</label>
                            <input type="number" wire:model="min_stock" step="0.1" min="0" class="w-full text-sm font-mono font-bold bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#C27835]">
                            @error('min_stock') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="pt-2 flex items-center space-x-2">
                        <button type="button" wire:click="closeModal" class="w-1/2 py-2.5 rounded-lg border border-stone-200 text-stone-600 font-mono text-xs hover:bg-stone-50 transition">
                            Batal
                        </button>
                        <button type="submit" class="w-1/2 py-2.5 rounded-lg bg-[#C27835] hover:bg-[#A05C22] text-white font-mono text-xs font-bold transition shadow-sm">
                            {{ $isEditMode ? 'Simpan Perubahan' : 'Tambah ke Stok' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>

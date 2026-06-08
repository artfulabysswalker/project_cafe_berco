<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-neutral-900 dark:text-white">⚙️ Konfigurasi Pajak & Diskon</h2>
            <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">Kelola konfigurasi pajak dan skema diskon untuk transaksi</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex gap-4 border-b border-neutral-200 dark:border-neutral-700">
        <button wire:click="$set('mode', 'view')" 
            @class(['px-4 py-2 text-sm font-semibold border-b-2 transition',
                'border-blue-600 text-blue-600' => $mode === 'view',
                'border-transparent text-neutral-600 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-white' => $mode !== 'view'
            ])>
            📋 Konfigurasi
        </button>
    </div>

    @if($mode === 'view')
        <!-- TAX CONFIGURATION SECTION -->
        <div class="space-y-6">
            <div class="rounded-lg border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-neutral-900 dark:text-white">💰 Konfigurasi Pajak (PB1)</h3>
                            <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">Atur persentase pajak untuk transaksi</p>
                        </div>
                        <button wire:click="$set('mode', 'edit_tax')" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800">
                            ✨ Tambah Pajak
                        </button>
                    </div>
                </div>

                @if($activeTaxConfig)
                <div class="border-b border-neutral-200 bg-blue-50 px-6 py-4 dark:border-neutral-700 dark:bg-blue-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-900 dark:text-blue-100">🔴 Pajak Aktif Saat Ini:</p>
                            <p class="mt-1 text-lg font-bold text-blue-900 dark:text-blue-100">{{ $activeTaxConfig->name }} - {{ $activeTaxConfig->tax_percentage }}%</p>
                        </div>
                        <span class="rounded-full bg-green-100 px-4 py-2 text-sm font-semibold text-green-700 dark:bg-green-900 dark:text-green-200">✓ Aktif</span>
                    </div>
                </div>
                @endif

                <div class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($taxConfigurations as $tax)
                    <div class="px-6 py-4 hover:bg-neutral-50 dark:hover:bg-neutral-700">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h4 class="font-semibold text-neutral-900 dark:text-white">{{ $tax['name'] }}</h4>
                                <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">{{ $tax['description'] }}</p>
                                <div class="mt-2 flex items-center gap-4">
                                    <span class="inline-block rounded-lg bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-700 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $tax['percentage'] }}%
                                    </span>
                                    @if($tax['is_active'])
                                    <span class="inline-block rounded-lg bg-green-100 px-3 py-1 text-sm font-semibold text-green-700 dark:bg-green-900 dark:text-green-200">
                                        ✓ Aktif
                                    </span>
                                    @else
                                    <span class="inline-block rounded-lg bg-gray-100 px-3 py-1 text-sm font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                        Tidak Aktif
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex gap-2">
                                @if(!$tax['is_active'])
                                <button wire:click="activateTax({{ $tax['id'] }})" 
                                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800">
                                    Aktifkan
                                </button>
                                @endif
                                <button wire:click="editTax({{ $tax['id'] }})" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700 dark:bg-amber-700 dark:hover:bg-amber-800">
                                    Edit
                                </button>
                                <button wire:click="deleteTax({{ $tax['id'] }})" wire:confirm="Hapus pajak ini?" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-neutral-500 dark:text-neutral-400">
                        📭 Belum ada konfigurasi pajak
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- DISCOUNT SCHEME SECTION -->
            <div class="rounded-lg border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-neutral-900 dark:text-white">🏷️ Skema Diskon</h3>
                            <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">Atur skema dan kode diskon untuk pelanggan</p>
                        </div>
                        <button wire:click="$set('mode', 'edit_discount')" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800">
                            ✨ Tambah Diskon
                        </button>
                    </div>
                </div>

                <div class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($discountSchemes as $discount)
                    <div class="px-6 py-4 hover:bg-neutral-50 dark:hover:bg-neutral-700">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-semibold text-neutral-900 dark:text-white">{{ $discount['name'] }}</h4>
                                    <code class="rounded bg-neutral-100 px-2 py-1 text-xs font-mono text-neutral-900 dark:bg-neutral-700 dark:text-white">
                                        {{ $discount['code'] }}
                                    </code>
                                </div>
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    <span class="inline-block rounded-lg bg-purple-100 px-3 py-1 text-sm font-semibold text-purple-700 dark:bg-purple-900 dark:text-purple-200">
                                        @if($discount['type'] === 'percentage')
                                            {{ $discount['value'] }}%
                                        @else
                                            Rp {{ number_format($discount['value'], 0, ',', '.') }}
                                        @endif
                                    </span>
                                    @if($discount['min_purchase'])
                                    <span class="inline-block rounded-lg bg-yellow-100 px-3 py-1 text-sm text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200">
                                        Min: Rp {{ number_format($discount['min_purchase'], 0, ',', '.') }}
                                    </span>
                                    @endif
                                    @if($discount['is_active'])
                                    <span class="inline-block rounded-lg bg-green-100 px-3 py-1 text-sm font-semibold text-green-700 dark:bg-green-900 dark:text-green-200">
                                        ✓ Aktif
                                    </span>
                                    @else
                                    <span class="inline-block rounded-lg bg-gray-100 px-3 py-1 text-sm font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                        Tidak Aktif
                                    </span>
                                    @endif
                                </div>
                                <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                                    Berlaku: {{ $discount['valid_from'] }} - {{ $discount['valid_until'] }}
                                </p>
                            </div>
                            <div class="flex gap-2">
                                @if($discount['is_active'])
                                <button wire:click="deactivateDiscount({{ $discount['id'] }})" class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-800">
                                    Nonaktifkan
                                </button>
                                @else
                                <button wire:click="activateDiscount({{ $discount['id'] }})" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800">
                                    Aktifkan
                                </button>
                                @endif
                                <button wire:click="editDiscount({{ $discount['id'] }})" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700 dark:bg-amber-700 dark:hover:bg-amber-800">
                                    Edit
                                </button>
                                <button wire:click="deleteDiscount({{ $discount['id'] }})" wire:confirm="Hapus skema diskon ini?" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-neutral-500 dark:text-neutral-400">
                        📭 Belum ada skema diskon
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    @elseif($mode === 'edit_tax')
        <!-- TAX FORM -->
        <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <h3 class="mb-6 text-lg font-bold text-neutral-900 dark:text-white">
                {{ $taxId ? '✏️ Edit Pajak' : '➕ Tambah Pajak Baru' }}
            </h3>

            <form wire:submit.prevent="saveTax" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Nama Pajak</label>
                    <input type="text" wire:model="taxName" placeholder="contoh: PB1 Umum"
                        class="mt-2 w-full rounded-lg border border-neutral-300 bg-white px-4 py-2 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white">
                    @error('taxName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Persentase Pajak (%)</label>
                    <input type="number" step="0.01" wire:model="taxPercentage" placeholder="contoh: 10"
                        class="mt-2 w-full rounded-lg border border-neutral-300 bg-white px-4 py-2 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white">
                    @error('taxPercentage') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Deskripsi</label>
                    <textarea wire:model="taxDescription" rows="3" placeholder="Jelaskan tentang pajak ini..."
                        class="mt-2 w-full rounded-lg border border-neutral-300 bg-white px-4 py-2 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white"></textarea>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2 font-semibold text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800">
                        💾 Simpan
                    </button>
                    <button type="button" wire:click="resetTaxFields()" class="rounded-lg border border-neutral-300 px-6 py-2 font-semibold text-neutral-700 hover:bg-neutral-50 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-700">
                        Batal
                    </button>
                </div>
            </form>
        </div>

    @elseif($mode === 'edit_discount')
        <!-- DISCOUNT FORM -->
        <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <h3 class="mb-6 text-lg font-bold text-neutral-900 dark:text-white">
                {{ $discountId ? '✏️ Edit Skema Diskon' : '➕ Tambah Skema Diskon Baru' }}
            </h3>

            <form wire:submit.prevent="saveDiscount" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Kode Diskon</label>
                        <input type="text" wire:model="discountCode" placeholder="contoh: DISKON10"
                            class="mt-2 w-full rounded-lg border border-neutral-300 bg-white px-4 py-2 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white">
                        @error('discountCode') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Nama Diskon</label>
                        <input type="text" wire:model="discountName" placeholder="contoh: Diskon 10%"
                            class="mt-2 w-full rounded-lg border border-neutral-300 bg-white px-4 py-2 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white">
                        @error('discountName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Tipe Diskon</label>
                        <select wire:model="discountType"
                            class="mt-2 w-full rounded-lg border border-neutral-300 bg-white px-4 py-2 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white">
                            <option value="percentage">Persentase (%)</option>
                            <option value="fixed">Nominal (Rp)</option>
                        </select>
                        @error('discountType') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                            Nilai Diskon {{ $discountType === 'percentage' ? '(%)' : '(Rp)' }}
                        </label>
                        <input type="number" step="0.01" wire:model="discountValue" placeholder="contoh: 10"
                            class="mt-2 w-full rounded-lg border border-neutral-300 bg-white px-4 py-2 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white">
                        @error('discountValue') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Minimum Pembelian (Rp)</label>
                        <input type="number" wire:model="discountMinPurchase" placeholder="0 = Tanpa minimum"
                            class="mt-2 w-full rounded-lg border border-neutral-300 bg-white px-4 py-2 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Max Diskon (Rp)</label>
                        <input type="number" wire:model="discountMaxDiscount" placeholder="0 = Tanpa batas"
                            class="mt-2 w-full rounded-lg border border-neutral-300 bg-white px-4 py-2 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Max Penggunaan</label>
                        <input type="number" wire:model="discountMaxUses" placeholder="0 = Unlimited"
                            class="mt-2 w-full rounded-lg border border-neutral-300 bg-white px-4 py-2 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white">
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2 font-semibold text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800">
                        💾 Simpan
                    </button>
                    <button type="button" wire:click="resetDiscountFields()" class="rounded-lg border border-neutral-300 px-6 py-2 font-semibold text-neutral-700 hover:bg-neutral-50 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-700">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>

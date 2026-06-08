@extends('dashboard')

@section('content')
<div class="mx-auto max-w-2xl py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">➕ Tambah Skema Diskon</h1>
            <p class="mt-2 text-neutral-600 dark:text-neutral-400">Buat skema diskon promosi baru</p>
        </div>

        <form method="POST" action="{{ route('admin.discount.store') }}" class="space-y-6 rounded-lg border border-neutral-200 bg-white p-8 dark:border-neutral-700 dark:bg-neutral-900">
            @csrf

            <!-- Kode Diskon -->
            <div>
                <label for="code" class="block text-sm font-medium text-neutral-900 dark:text-white">Kode Diskon *</label>
                <input type="text" id="code" name="code" value="{{ old('code') }}" placeholder="Contoh: PAGI_SPESIAL" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 uppercase dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('code') border-red-500 @enderror">
                @error('code')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama Skema -->
            <div>
                <label for="name" class="block text-sm font-medium text-neutral-900 dark:text-white">Nama Skema *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Promo Pagi Spesial" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="description" class="block text-sm font-medium text-neutral-900 dark:text-white">Deskripsi</label>
                <textarea id="description" name="description" rows="3" placeholder="Jelaskan detail promo ini..." class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tipe & Nilai Diskon -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="discount_type" class="block text-sm font-medium text-neutral-900 dark:text-white">Tipe Diskon *</label>
                    <select id="discount_type" name="discount_type" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('discount_type') border-red-500 @enderror">
                        <option value="">-- Pilih Tipe --</option>
                        <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                        <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                    </select>
                    @error('discount_type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="discount_value" class="block text-sm font-medium text-neutral-900 dark:text-white">Nilai Diskon *</label>
                    <input type="number" id="discount_value" name="discount_value" value="{{ old('discount_value') }}" min="0" step="0.01" placeholder="15 atau 20000" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('discount_value') border-red-500 @enderror">
                    @error('discount_value')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Min Pembelian & Max Diskon -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="min_purchase" class="block text-sm font-medium text-neutral-900 dark:text-white">Min. Pembelian (Rp)</label>
                    <input type="number" id="min_purchase" name="min_purchase" value="{{ old('min_purchase') }}" min="0" step="0.01" placeholder="50000" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('min_purchase') border-red-500 @enderror">
                    @error('min_purchase')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_discount" class="block text-sm font-medium text-neutral-900 dark:text-white">Max Diskon (Rp) - jika %</label>
                    <input type="number" id="max_discount" name="max_discount" value="{{ old('max_discount') }}" min="0" step="0.01" placeholder="30000" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('max_discount') border-red-500 @enderror">
                    @error('max_discount')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Max Uses -->
            <div>
                <label for="max_uses" class="block text-sm font-medium text-neutral-900 dark:text-white">Maksimal Penggunaan</label>
                <input type="number" id="max_uses" name="max_uses" value="{{ old('max_uses') }}" min="1" placeholder="100" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('max_uses') border-red-500 @enderror">
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Kosongkan untuk unlimited</p>
                @error('max_uses')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Periode -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="valid_from" class="block text-sm font-medium text-neutral-900 dark:text-white">Berlaku Dari *</label>
                    <input type="datetime-local" id="valid_from" name="valid_from" value="{{ old('valid_from') }}" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('valid_from') border-red-500 @enderror">
                    @error('valid_from')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="valid_until" class="block text-sm font-medium text-neutral-900 dark:text-white">Berlaku Hingga *</label>
                    <input type="datetime-local" id="valid_until" name="valid_until" value="{{ old('valid_until') }}" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('valid_until') border-red-500 @enderror">
                    @error('valid_until')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Status Aktif -->
            <div class="flex items-center">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }} class="h-4 w-4 rounded border-neutral-300 dark:bg-neutral-800">
                <label for="is_active" class="ml-3 text-sm font-medium text-neutral-900 dark:text-white">Aktifkan skema diskon ini</label>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-6">
                <button type="submit" class="flex-1 rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white hover:bg-blue-700">
                    ✓ Simpan Skema
                </button>
                <a href="{{ route('admin.discount.index') }}" class="flex-1 rounded-lg border border-neutral-300 px-6 py-3 text-center font-semibold text-neutral-900 hover:bg-neutral-100 dark:border-neutral-600 dark:text-white dark:hover:bg-neutral-800">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-6">
                <button type="submit" class="flex-1 rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white hover:bg-blue-700">
                    ✓ Simpan Skema
                </button>
                <a href="{{ route('admin.discount.index') }}" class="flex-1 rounded-lg border border-neutral-300 px-6 py-3 text-center font-semibold text-neutral-900 hover:bg-neutral-100 dark:border-neutral-600 dark:text-white dark:hover:bg-neutral-800">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-layouts::app>

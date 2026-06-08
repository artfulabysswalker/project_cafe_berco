@extends('dashboard')

@section('content')
<div class="mx-auto max-w-2xl py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">➕ Tambah Konfigurasi Pajak</h1>
            <p class="mt-2 text-neutral-600 dark:text-neutral-400">Buat konfigurasi pajak baru untuk transaksi cafe</p>
        </div>

        <form method="POST" action="{{ route('admin.tax.store') }}" class="space-y-6 rounded-lg border border-neutral-200 bg-white p-8 dark:border-neutral-700 dark:bg-neutral-900">
            @csrf

            <!-- Nama Konfigurasi -->
            <div>
                <label for="name" class="block text-sm font-medium text-neutral-900 dark:text-white">Nama Konfigurasi *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: PB1 - Pajak 10%" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Persentase Pajak -->
            <div>
                <label for="tax_percentage" class="block text-sm font-medium text-neutral-900 dark:text-white">Persentase Pajak (%) *</label>
                <input type="number" id="tax_percentage" name="tax_percentage" value="{{ old('tax_percentage') }}" min="0" max="100" step="0.01" placeholder="10" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('tax_percentage') border-red-500 @enderror">
                @error('tax_percentage')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="description" class="block text-sm font-medium text-neutral-900 dark:text-white">Deskripsi</label>
                <textarea id="description" name="description" rows="4" placeholder="Jelaskan tujuan dan detail konfigurasi pajak ini..." class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status Aktif -->
            <div class="flex items-center">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }} class="h-4 w-4 rounded border-neutral-300 dark:bg-neutral-800">
                <label for="is_active" class="ml-3 text-sm font-medium text-neutral-900 dark:text-white">Aktifkan sebagai konfigurasi default</label>
            </div>

            <!-- Tanggal Berlaku -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="effective_from" class="block text-sm font-medium text-neutral-900 dark:text-white">Berlaku Dari</label>
                    <input type="datetime-local" id="effective_from" name="effective_from" value="{{ old('effective_from') }}" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('effective_from') border-red-500 @enderror">
                    @error('effective_from')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="effective_until" class="block text-sm font-medium text-neutral-900 dark:text-white">Berlaku Hingga</label>
                    <input type="datetime-local" id="effective_until" name="effective_until" value="{{ old('effective_until') }}" class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white @error('effective_until') border-red-500 @enderror">
                    @error('effective_until')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-6">
                <button type="submit" class="flex-1 rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white hover:bg-blue-700">
                    ✓ Simpan Konfigurasi
                </button>
                <a href="{{ route('admin.tax.index') }}" class="flex-1 rounded-lg border border-neutral-300 px-6 py-3 text-center font-semibold text-neutral-900 hover:bg-neutral-100 dark:border-neutral-600 dark:text-white dark:hover:bg-neutral-800">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

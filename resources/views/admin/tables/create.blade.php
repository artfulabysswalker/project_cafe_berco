@extends('dashboard')

@section('content')
<div class="mx-auto max-w-2xl space-y-8 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">Tambah Meja Baru</h1>
            <p class="mt-2 text-neutral-600 dark:text-neutral-400">Meja baru langsung aktif dan siap digenerate QR Code-nya</p>
        </div>
        <a href="{{ route('admin.tables.index') }}" class="rounded-lg border border-neutral-300 bg-white px-4 py-2 font-semibold text-neutral-700 hover:bg-neutral-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700">
            ← Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="rounded-lg border-l-4 border-red-500 bg-red-50 p-4 text-red-800 dark:bg-red-900/20 dark:text-red-200">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.tables.store') }}" class="space-y-5 rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
        @csrf

        <div>
            <label for="nama_meja" class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                Nama Meja <span class="text-red-500">*</span>
            </label>
            <input type="text"
                   name="nama_meja"
                   id="nama_meja"
                   value="{{ old('nama_meja') }}"
                   required
                   maxlength="50"
                   placeholder="Contoh: Meja 05"
                   class="w-full rounded-lg border border-neutral-300 px-4 py-2 text-neutral-900 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
            <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">Harus unik. Nama ini yang tampil di halaman order pelanggan.</p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="kapasitas" class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Kapasitas (orang)</label>
                <input type="number"
                       name="kapasitas"
                       id="kapasitas"
                       value="{{ old('kapasitas') }}"
                       min="1"
                       max="100"
                       placeholder="4"
                       class="w-full rounded-lg border border-neutral-300 px-4 py-2 text-neutral-900 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
            </div>

            <div>
                <label for="area" class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Area</label>
                <input type="text"
                       name="area"
                       id="area"
                       value="{{ old('area') }}"
                       maxlength="100"
                       placeholder="Contoh: Indoor / Outdoor"
                       class="w-full rounded-lg border border-neutral-300 px-4 py-2 text-neutral-900 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="rounded-lg bg-[#C27835] px-6 py-3 font-semibold text-white hover:bg-[#a8642a]">
                Simpan Meja
            </button>
            <a href="{{ route('admin.tables.index') }}" class="rounded-lg border border-neutral-300 bg-white px-6 py-3 font-semibold text-neutral-700 hover:bg-neutral-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection

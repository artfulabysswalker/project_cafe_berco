@extends('dashboard')

@section('content')
<div class="mx-auto max-w-2xl space-y-8 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">Edit {{ $table->nama_meja }}</h1>
            <p class="mt-2 text-neutral-600 dark:text-neutral-400">ID Meja: {{ $table->id_meja }}</p>
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

    <form method="POST" action="{{ route('admin.tables.update', $table->id_meja) }}" class="space-y-5 rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
        @csrf
        @method('PUT')

        <div>
            <label for="nama_meja" class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                Nama Meja <span class="text-red-500">*</span>
            </label>
            <input type="text"
                   name="nama_meja"
                   id="nama_meja"
                   value="{{ old('nama_meja', $table->nama_meja) }}"
                   required
                   maxlength="50"
                   class="w-full rounded-lg border border-neutral-300 px-4 py-2 text-neutral-900 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="kapasitas" class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Kapasitas (orang)</label>
                <input type="number"
                       name="kapasitas"
                       id="kapasitas"
                       value="{{ old('kapasitas', $table->kapasitas) }}"
                       min="1"
                       max="100"
                       class="w-full rounded-lg border border-neutral-300 px-4 py-2 text-neutral-900 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
            </div>

            <div>
                <label for="area" class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Area</label>
                <input type="text"
                       name="area"
                       id="area"
                       value="{{ old('area', $table->area) }}"
                       maxlength="100"
                       class="w-full rounded-lg border border-neutral-300 px-4 py-2 text-neutral-900 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
            </div>
        </div>

        <div class="flex items-center justify-between rounded-lg bg-neutral-50 px-4 py-3 dark:bg-neutral-800">
            <div>
                <p class="text-sm font-medium text-neutral-800 dark:text-neutral-200">Status Meja</p>
                <p class="text-xs text-neutral-500 dark:text-neutral-400">Meja nonaktif tidak bisa dipesan lewat QR Code</p>
            </div>
            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $table->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200' }}">
                {{ $table->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="rounded-lg bg-[#C27835] px-6 py-3 font-semibold text-white hover:bg-[#a8642a]">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.tables.index') }}" class="rounded-lg border border-neutral-300 bg-white px-6 py-3 font-semibold text-neutral-700 hover:bg-neutral-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection

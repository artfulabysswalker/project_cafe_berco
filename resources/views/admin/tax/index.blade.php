@extends('dashboard')

@section('content')
<div class="mx-auto max-w-6xl space-y-8 py-8">
        <!-- Header -->
        <div class="flex items-center justify-between rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <div>
                <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">📋 Konfigurasi Pajak PB1</h1>
                <p class="mt-2 text-neutral-600 dark:text-neutral-400">Kelola pengaturan pajak untuk semua transaksi</p>
            </div>
            <a href="{{ route('admin.tax.create') }}" class="inline-block rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white hover:bg-blue-700">
                ➕ Tambah Konfigurasi
            </a>
        </div>

        <!-- Active Configuration Alert -->
        @if($activeConfig)
        <div class="rounded-lg border-l-4 border-green-500 bg-green-50 p-4 dark:bg-green-900/20">
            <div class="flex">
                <div class="flex-shrink-0">
                    <span class="text-2xl">✅</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">
                        Konfigurasi Aktif: <strong>{{ $activeConfig->name }}</strong>
                    </p>
                    <p class="mt-2 text-sm text-green-700 dark:text-green-300">
                        Tarif Pajak: <strong>{{ $activeConfig->tax_percentage }}%</strong>
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Table -->
        <div class="overflow-x-auto rounded-lg border border-neutral-200 dark:border-neutral-700">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-neutral-200 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800">
                        <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Nama Konfigurasi</th>
                        <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Tarif Pajak</th>
                        <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Status</th>
                        <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Berlaku Dari</th>
                        <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Dibuat Oleh</th>
                        <th class="px-6 py-4 text-center font-semibold text-neutral-900 dark:text-white">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($taxConfigs as $config)
                    <tr class="border-b border-neutral-200 dark:border-neutral-700">
                        <td class="px-6 py-4 text-neutral-900 dark:text-white">
                            <div>
                                <p class="font-medium">{{ $config->name }}</p>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $config->description }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-700 dark:bg-blue-900 dark:text-blue-200">
                                {{ $config->tax_percentage }}%
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($config->is_active)
                                <span class="rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-700 dark:bg-green-900 dark:text-green-200">Aktif ✓</span>
                            @else
                                <span class="rounded-full bg-neutral-100 px-3 py-1 text-sm font-semibold text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300">Tidak Aktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-600 dark:text-neutral-400">
                            {{ $config->effective_from ? $config->effective_from->format('d M Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-600 dark:text-neutral-400">
                            {{ $config->user->name }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.tax.edit', $config) }}" class="rounded bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-400">Edit</a>
                                @if(!$config->is_active)
                                    <form method="POST" action="{{ route('admin.tax.setActive', $config) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="rounded bg-green-100 px-3 py-1 text-xs font-semibold text-green-700 hover:bg-green-200 dark:bg-green-900 dark:text-green-400">Aktifkan</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.tax.destroy', $config) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 hover:bg-red-200 dark:bg-red-900 dark:text-red-400">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-neutral-500 dark:text-neutral-400">
                            Belum ada konfigurasi pajak. <a href="{{ route('admin.tax.create') }}" class="text-blue-600 hover:underline">Buat yang baru</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $taxConfigs->links() }}
        </div>
    </div>
</x-layouts::app>

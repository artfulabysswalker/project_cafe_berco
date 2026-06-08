@extends('dashboard')

@section('content')
<div class="mx-auto max-w-6xl space-y-8 py-8">
        <!-- Header -->
        <div class="flex items-center justify-between rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <div>
                <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">🎉 Kelola Skema Diskon</h1>
                <p class="mt-2 text-neutral-600 dark:text-neutral-400">Buat dan kelola semua skema diskon promosi</p>
            </div>
            <a href="{{ route('admin.discount.create') }}" class="inline-block rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white hover:bg-blue-700">
                ➕ Tambah Skema Diskon
            </a>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-lg border border-neutral-200 dark:border-neutral-700">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-neutral-200 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800">
                        <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Kode</th>
                        <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Nama Skema</th>
                        <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Diskon</th>
                        <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Min. Pembelian</th>
                        <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Status</th>
                        <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Periode</th>
                        <th class="px-6 py-4 text-center font-semibold text-neutral-900 dark:text-white">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schemes as $scheme)
                    <tr class="border-b border-neutral-200 dark:border-neutral-700">
                        <td class="px-6 py-4">
                            <span class="rounded-full bg-purple-100 px-3 py-1 text-sm font-semibold text-purple-700 dark:bg-purple-900 dark:text-purple-200">
                                {{ $scheme->code }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-neutral-900 dark:text-white">
                            <div>
                                <p class="font-medium">{{ $scheme->name }}</p>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $scheme->description }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($scheme->discount_type === 'percentage')
                                <span class="rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-700 dark:bg-green-900 dark:text-green-200">
                                    {{ $scheme->discount_value }}%
                                </span>
                            @else
                                <span class="rounded-full bg-orange-100 px-3 py-1 text-sm font-semibold text-orange-700 dark:bg-orange-900 dark:text-orange-200">
                                    Rp {{ number_format($scheme->discount_value, 0, ',', '.') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-600 dark:text-neutral-400">
                            {{ $scheme->min_purchase ? 'Rp ' . number_format($scheme->min_purchase, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($scheme->is_active)
                                <span class="rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-700 dark:bg-green-900 dark:text-green-200">Aktif ✓</span>
                            @else
                                <span class="rounded-full bg-neutral-100 px-3 py-1 text-sm font-semibold text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300">Tidak Aktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-600 dark:text-neutral-400">
                            <div>
                                <p>Dari: {{ $scheme->valid_from->format('d/m/y') }}</p>
                                <p>Hingga: {{ $scheme->valid_until->format('d/m/y') }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.discount.edit', $scheme) }}" class="rounded bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-400">Edit</a>
                                <form method="POST" action="{{ route('admin.discount.destroy', $scheme) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 hover:bg-red-200 dark:bg-red-900 dark:text-red-400">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-neutral-500 dark:text-neutral-400">
                            Belum ada skema diskon. <a href="{{ route('admin.discount.create') }}" class="text-blue-600 hover:underline">Buat yang baru</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $schemes->links() }}
        </div>
    </div>
</div>
@endsection

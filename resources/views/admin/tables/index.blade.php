@extends('dashboard')

@section('content')
<div class="mx-auto max-w-7xl space-y-8 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">Manajemen Meja & QR Code</h1>
            <p class="mt-2 text-neutral-600 dark:text-neutral-400">Generate, revoke, dan download QR Code untuk setiap meja</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.tables.create') }}" class="rounded-lg bg-[#C27835] px-4 py-2 font-semibold text-white hover:bg-[#a8642a]">
                + Tambah Meja
            </a>
            <a href="{{ route('admin.tables.index') }}" class="inline-block rounded-lg border border-neutral-300 bg-white px-4 py-2 font-semibold text-neutral-700 hover:bg-neutral-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700">
                Refresh
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg border-l-4 border-green-500 bg-green-50 p-4 text-green-800 dark:bg-green-900/20 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-lg border-l-4 border-red-500 bg-red-50 p-4 text-red-800 dark:bg-red-900/20 dark:text-red-200">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="overflow-x-auto rounded-lg border border-neutral-200 dark:border-neutral-700">
        <table class="w-full">
            <thead>
                <tr class="border-b border-neutral-200 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800">
                    <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Meja</th>
                    <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Kapasitas</th>
                    <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Area</th>
                    <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Status Aktif</th>
                    <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Status Meja</th>
                    <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">QR Aktif</th>
                    <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Token QR</th>
                    <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Expired</th>
                    <th class="px-6 py-4 text-center font-semibold text-neutral-900 dark:text-white">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mejas as $meja)
                @php
                    $activeQr = $meja->qrCodes->firstWhere('is_active', true);
                    if ($activeQr && $activeQr->isExpired()) {
                        $activeQr = null;
                    }
                @endphp
                <tr class="border-b border-neutral-200 dark:border-neutral-700">
                    <td class="px-6 py-4 text-neutral-900 dark:text-white font-medium">{{ $meja->nama_meja }}</td>
                    <td class="px-6 py-4 text-neutral-700 dark:text-neutral-300">{{ $meja->kapasitas ?? '-' }}</td>
                    <td class="px-6 py-4 text-neutral-700 dark:text-neutral-300">{{ $meja->area ?? '-' }}</td>
                    <td class="px-6 py-4">
                        @if($meja->is_active)
                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700 dark:bg-green-900 dark:text-green-200">Aktif</span>
                        @else
                            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-900 dark:text-red-200">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900 dark:text-blue-200">{{ $meja->status ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($activeQr)
                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700 dark:bg-green-900 dark:text-green-200">Aktif</span>
                        @else
                            <span class="rounded-full bg-neutral-100 px-3 py-1 text-xs font-semibold text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300">Tidak Aktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs">
                        @if($activeQr)
                            <span class="font-mono text-neutral-600 dark:text-neutral-300" title="{{ $activeQr->token }}">{{ \Illuminate\Support\Str::limit($activeQr->token, 14) }}</span>
                        @else
                            <span class="text-neutral-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-neutral-700 dark:text-neutral-300 text-xs">
                        @if($activeQr && $activeQr->expired_at)
                            {{ $activeQr->expired_at->format('d/m/Y H:i') }}
                        @elseif($activeQr)
                            <span class="text-green-600 dark:text-green-400">Tidak ada expiry</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex flex-wrap items-center justify-center gap-2">
                            <a href="{{ route('admin.tables.qrcode', $meja->id_meja) }}" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">
                                Kelola QR
                            </a>
                            <a href="{{ route('admin.tables.orders', $meja->id_meja) }}" class="rounded-lg border border-neutral-300 bg-white px-3 py-1.5 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300" title="Pesanan meja ini">
                                Pesanan
                            </a>
                            <a href="{{ route('admin.tables.edit', $meja->id_meja) }}" class="rounded-lg border border-neutral-300 bg-white px-3 py-1.5 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300" title="Edit meja">
                                Edit
                            </a>
                            @if($activeQr)
                                <a href="{{ route('admin.tables.qrcode.print', $meja->id_meja) }}" target="_blank" class="rounded-lg border border-neutral-300 bg-white px-3 py-1.5 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300" title="Cetak QR">
                                    Cetak
                                </a>
                            @endif
                            <form method="POST" action="{{ route('admin.tables.toggle-status', $meja->id_meja) }}" style="display:inline;" onsubmit="return confirm('{{ $meja->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $meja->nama_meja }}? Meja nonaktif tidak bisa dipakai pesan lewat QR.')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-semibold {{ $meja->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                    {{ $meja->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-8 text-center text-neutral-500 dark:text-neutral-400">
                        Belum ada data meja. Buat seeder Meja terlebih dahulu.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $mejas->links() }}
    </div>
</div>
@endsection

@extends('dashboard')

@section('content')
<div class="mx-auto max-w-6xl space-y-8 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">Pesanan {{ $table->nama_meja }}</h1>
            <p class="mt-2 text-neutral-600 dark:text-neutral-400">
                ID Meja: {{ $table->id_meja }}
                @if($table->area) · Area: {{ $table->area }} @endif
                @if($table->kapasitas) · Kapasitas: {{ $table->kapasitas }} @endif
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.tables.qrcode', $table->id_meja) }}" class="rounded-lg border border-neutral-300 bg-white px-4 py-2 font-semibold text-neutral-700 hover:bg-neutral-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700">
                Kelola QR
            </a>
            <a href="{{ route('admin.tables.index') }}" class="rounded-lg border border-neutral-300 bg-white px-4 py-2 font-semibold text-neutral-700 hover:bg-neutral-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700">
                ← Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="rounded-lg border-l-4 border-red-500 bg-red-50 p-4 text-red-800 dark:bg-red-900/20 dark:text-red-200">
            {{ $errors->first() }}
        </div>
    @endif

    @if($orders->isEmpty())
        <div class="rounded-lg border-2 border-dashed border-neutral-300 bg-neutral-50 p-12 text-center dark:border-neutral-700 dark:bg-neutral-800/50">
            <div class="text-5xl mb-4">📭</div>
            <h3 class="text-xl font-semibold text-neutral-800 mb-2 dark:text-neutral-200">Belum Ada Pesanan</h3>
            <p class="text-neutral-500 dark:text-neutral-400">Belum ada pesanan yang tercatat untuk {{ $table->nama_meja }}</p>
        </div>
    @else
        <div class="overflow-x-auto rounded-lg border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-neutral-200 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800">
                        <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">ID</th>
                        <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Pelanggan</th>
                        <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Pesanan</th>
                        <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Total</th>
                        <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Status</th>
                        <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Tanggal</th>
                        <th class="px-6 py-4 text-center font-semibold text-neutral-900 dark:text-white">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr class="border-b border-neutral-200 hover:bg-neutral-50 dark:border-neutral-700 dark:hover:bg-neutral-800">
                            <td class="px-6 py-4 font-semibold text-neutral-900 dark:text-white">#{{ $order->id_order }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-sm text-neutral-800 dark:text-neutral-200">
                                        {{ $order->customer_name ?: ($order->user->name ?? $order->nama_pelanggan) }}
                                    </span>
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400 font-mono">
                                        {{ $order->customer_phone ?: '-' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <ul class="max-w-[240px] space-y-0.5 text-xs text-neutral-700 dark:text-neutral-300">
                                    @forelse($order->items as $item)
                                        <li>
                                            <span class="font-semibold">{{ $item->quantity }}x</span>
                                            {{ $item->menu?->nama_menu ?? 'Item' }}
                                        </li>
                                    @empty
                                        <li class="text-neutral-400">-</li>
                                    @endforelse
                                </ul>
                            </td>
                            <td class="px-6 py-4 font-semibold text-neutral-900 dark:text-white">
                                Rp {{ number_format($order->final_total ?? $order->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                @if(in_array($order->status_pembayaran, ['Paid', 'paid']))
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700 dark:bg-green-900 dark:text-green-200">Lunas</span>
                                @else
                                    <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200">Pending</span>
                                @endif
                                <span class="mt-1 inline-block rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900 dark:text-blue-200">
                                    {{ ucfirst($order->status_order) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-neutral-600 dark:text-neutral-400">
                                {{ $order->tanggal->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('order.receipt', $order) }}"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 text-blue-600 transition-colors hover:bg-blue-200"
                                   title="Lihat Kwitansi">
                                    👀
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="rounded-lg border border-neutral-200 bg-neutral-50 px-6 py-4 text-sm text-neutral-600 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400">
            <strong class="text-neutral-900 dark:text-white">Total:</strong> {{ $orders->total() }} pesanan untuk {{ $table->nama_meja }}
        </div>

        @if($orders->hasPages())
            <div class="flex justify-center">
                {{ $orders->links() }}
            </div>
        @endif
    @endif
</div>
@endsection

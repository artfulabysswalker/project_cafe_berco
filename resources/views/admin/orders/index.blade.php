@extends('dashboard')

@section('content')

@include('admin.partials.order-alert')

<div class="px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <span class="text-4xl">⏳</span>
            <h1 class="text-3xl font-bold text-amber-900">Pesanan Menunggu</h1>
        </div>
        <p class="text-gray-600 ml-14">Kelola pesanan yang belum diselesaikan</p>
    </div>

    <!-- Filter per meja / token QR -->
    <form method="GET" action="{{ route('admin.orders') }}" class="mb-6 flex flex-wrap items-end gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div class="min-w-[180px]">
            <label for="filter-meja" class="mb-1 block text-xs font-semibold text-gray-600">Meja</label>
            <select name="meja" id="filter-meja" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-amber-500 focus:outline-none">
                <option value="all">Semua Meja</option>
                @foreach($tables as $tableOption)
                    <option value="{{ $tableOption->id_meja }}" @selected((string) request('meja') === (string) $tableOption->id_meja)>
                        {{ $tableOption->nama_meja }}{{ $tableOption->is_active ? '' : ' (Nonaktif)' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="min-w-[240px] flex-1">
            <label for="filter-q" class="mb-1 block text-xs font-semibold text-gray-600">Cari Token QR / Nama Meja</label>
            <input type="text"
                   name="q"
                   id="filter-q"
                   value="{{ request('q') }}"
                   placeholder="Contoh: 05 / Meja 05 / token QR..."
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-amber-500 focus:outline-none">
        </div>

        <div class="flex gap-2">
            <button type="submit" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700 transition-colors">
                Terapkan
            </button>
            <a href="{{ route('admin.orders') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                Reset
            </a>
        </div>
    </form>

    @if(request('meja') !== null && request('meja') !== 'all' || request('q'))
        @php
            $filteredTable = $tables->firstWhere('id_meja', (int) request('meja'));
        @endphp
        <p class="mb-4 text-sm text-gray-600">
            Menampilkan pesanan
            @if($filteredTable) untuk <strong>{{ $filteredTable->nama_meja }}</strong> @endif
            @if(request('q')) dengan pencarian "<strong>{{ request('q') }}</strong>" @endif
            — {{ $orders->total() }} data ditemukan.
        </p>
    @endif

    @if($orders->isEmpty())
        <!-- Empty State -->
        <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg border-2 border-dashed border-amber-300 p-12 text-center">
            <div class="text-5xl mb-4">📭</div>
            <h3 class="text-xl font-semibold text-amber-900 mb-2">Tidak Ada Pesanan Menunggu</h3>
            <p class="text-amber-700">Semua pesanan telah diproses!</p>
        </div>
    @else
        <!-- Orders Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-amber-100 to-amber-50 border-b-2 border-amber-200">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-amber-900">ID Pesanan</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-amber-900">Data Pelanggan</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-amber-900">Pesanan</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-amber-900">Total</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-amber-900">Status Pembayaran</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-amber-900">Status Pesanan</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-amber-900">Tanggal</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-amber-900">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr class="border-b border-gray-200 hover:bg-amber-50 transition-colors">
                                <td class="px-6 py-4">
                                    <strong class="text-amber-700 text-lg">#{{ $order->id_order }}</strong>
                                    <div class="text-[11px] text-gray-400 font-mono">{{ $order->public_code }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-0.5">
                                        <span class="text-[11px] text-gray-500">
                                            Meja: <strong class="text-gray-800">{{ $order->table?->nama_meja ?? '-' }}</strong>
                                        </span>
                                        <span class="text-[11px] text-gray-500">
                                            Nama: <strong class="text-gray-800">{{ $order->customer_name ?: ($order->user->name ?? $order->nama_pelanggan) }}</strong>
                                        </span>
                                        <span class="text-[11px] text-gray-500">
                                            No. Telp: <strong class="text-gray-800 font-mono">{{ $order->customer_phone ?: '-' }}</strong>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <ul class="text-xs text-gray-700 space-y-0.5 max-w-[220px]">
                                        @forelse($order->items as $item)
                                            <li>
                                                <span class="font-semibold">{{ $item->quantity }}x</span>
                                                {{ $item->menu?->nama_menu ?? 'Item' }}
                                                @if($item->notes)
                                                    <span class="block text-[10px] text-amber-700">📝 {{ $item->notes }}</span>
                                                @endif
                                            </li>
                                        @empty
                                            <li class="text-gray-400">-</li>
                                        @endforelse
                                    </ul>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-800">
                                    Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($order->status_pembayaran === 'Paid' || $order->status_pembayaran === 'paid')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                            ✓ Lunas
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">
                                            ⏳ Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                                        ⚙ {{ ucfirst($order->status_order) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 text-sm">
                                    {{ $order->tanggal->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-center">
<div class="flex justify-center items-center gap-2">
                                        <!-- Konfirmasi & Cetak (bayar di kasir) -->
                                        @if(! $order->isPaid() && $order->payment_method === 'cash')
                                            <form method="POST" action="{{ route('admin.orders.confirm', $order->id_order) }}"
                                                  style="display:inline;" onsubmit="return confirm('Konfirmasi pembayaran #{{ $order->id_order }} dari {{ $order->customer_name ?: $order->nama_pelanggan }}?')">
                                                @csrf
                                                <button type="submit"
                                                        class="inline-flex items-center justify-center px-3 h-9 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-colors text-xs font-semibold"
                                                        title="Konfirmasi pembayaran lalu cetak struk">
                                                    💵 Konfirmasi &amp; Cetak
                                                </button>
                                            </form>
                                        @endif

                                        <!-- View Receipt -->
                                        <a href="{{ route('admin.receipt.view', $order->id_order) }}"
                                           class="inline-flex items-center justify-center w-9 h-9 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors"
                                           title="Lihat Kwitansi">
                                            👀
                                        </a>

                                        <!-- Complete Order -->
                                        <form method="POST" action="{{ route('admin.orders.complete', $order->id_order) }}" 
                                              style="display:inline;" onsubmit="return confirm('Tandai pesanan ini sebagai selesai?')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" 
                                                    class="inline-flex items-center justify-center w-9 h-9 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-colors" 
                                                    title="Tandai Selesai">
                                                ✔
                                            </button>
                                        </form>

                                        <!-- Cancel Order -->
                                        <form method="POST" action="{{ route('admin.orders.cancel', $order->id_order) }}" 
                                              style="display:inline;" onsubmit="return confirm('Batalkan pesanan ini?')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" 
                                                    class="inline-flex items-center justify-center w-9 h-9 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors" 
                                                    title="Batalkan">
                                                ✖
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            <div class="bg-gradient-to-r from-amber-50 to-amber-100 px-6 py-4 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    <strong class="text-amber-900">Total:</strong> {{ count($orders) }} pesanan menunggu
                </p>
            </div>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="mt-6 flex justify-center">
                <div class="pagination-custom">
                    {{ $orders->links() }}
                </div>
            </div>
        @endif
    @endif
</div>

<style>
    .pagination-custom :deep(.pagination) {
        gap: 0.5rem;
        justify-content: center;
    }
    
    .pagination-custom :deep(.pagination li a),
    .pagination-custom :deep(.pagination li span) {
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        transition: all 0.3s ease;
    }
    
    .pagination-custom :deep(.pagination li a:hover) {
        background-color: #fef3c7;
        border-color: #d97706;
    }
    
    .pagination-custom :deep(.pagination li.active span) {
        background-color: #b45309;
        color: white;
        border-color: #b45309;
    }
</style>

@endsection
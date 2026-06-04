@extends('dashboard')

@section('content')

<div class="px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <span class="text-4xl">⏳</span>
            <h1 class="text-3xl font-bold text-amber-900">Pesanan Menunggu</h1>
        </div>
        <p class="text-gray-600 ml-14">Kelola pesanan yang belum diselesaikan</p>
    </div>

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
                            <th class="px-6 py-4 text-left text-sm font-semibold text-amber-900">Pelanggan</th>
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
                                </td>
                                <td class="px-6 py-4 text-gray-800 font-medium">
                                    {{ $order->user->name ?? $order->nama_pelanggan }}
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
                                        <!-- View Receipt -->
                                        <a href="{{ route('order.receipt', $order) }}" 
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
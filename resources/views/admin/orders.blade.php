@extends('dashboard')

@section('page-title', 'Pesanan Aktif')
@section('breadcrumb', 'Orders Management')

@section('content')
<div class="space-y-5">

    {{-- Header with Search & New Order Action --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="relative w-72">
                <input 
                    type="text" 
                    id="orderSearchInput" 
                    placeholder="Cari ID order, nama..." 
                    onkeyup="filterOrders()"
                    class="w-full bg-white border border-stone-200 rounded-md pl-8 pr-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#C27835] focus:border-[#C27835]"
                />
                <i class="fas fa-magnifying-glass text-stone-400 text-xs absolute left-2.5 top-2.5"></i>
            </div>
            <select id="orderStatusFilter" onchange="filterOrders()" class="bg-white border border-stone-200 rounded-md px-3 py-1.5 text-xs text-stone-700 focus:outline-none focus:ring-1 focus:ring-[#C27835]">
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="completed">Selesai</option>
                <option value="cancelled">Dibatalkan</option>
            </select>
        </div>

        <a href="{{ route('checkout') }}" class="text-xs font-mono font-medium bg-[#18181B] text-white px-4 py-2 rounded-md hover:bg-black transition-colors shadow-2xs inline-flex items-center space-x-2 shrink-0">
            <i class="fas fa-plus text-[10px]"></i>
            <span>Buat Pesanan Kasir</span>
        </a>
    </div>

    {{-- Orders Table Card --}}
    <div class="bg-white border border-stone-200 rounded-lg shadow-2xs overflow-hidden">
        <div class="px-5 py-3.5 border-b border-stone-200 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-[#C27835]"></span>
                <h3 class="text-xs font-mono uppercase tracking-wider font-semibold text-stone-800">Antrean Pesanan Masuk</h3>
            </div>
            <span class="text-xs font-mono text-stone-500 bg-stone-100 px-2.5 py-0.5 rounded border border-stone-200">{{ count($orders ?? []) }} Tiket</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-mono" id="ordersTable">
                <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 text-[10px] uppercase">
                    <tr>
                        <th class="px-4 py-2.5">Order ID</th>
                        <th class="px-4 py-2.5">Pelanggan</th>
                        <th class="px-4 py-2.5">Tipe Layanan</th>
                        <th class="px-4 py-2.5">Total Bayar</th>
                        <th class="px-4 py-2.5">Metode</th>
                        <th class="px-4 py-2.5">Status Bayar</th>
                        <th class="px-4 py-2.5">Status Order</th>
                        <th class="px-4 py-2.5">Waktu</th>
                        <th class="px-4 py-2.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($orders ?? [] as $order)
                        @php
                            $paymentStatus = strtolower($order->status_pembayaran ?? 'pending');
                            $orderStatus = strtolower($order->status_order ?? 'pending');
                        @endphp
                        <tr class="order-row hover:bg-stone-50/70 transition-colors" data-status="{{ $orderStatus }}">
                            <td class="px-4 py-3 font-semibold text-stone-900">#ORD-{{ $order->id_order }}</td>
                            <td class="px-4 py-3 font-sans">
                                <div class="font-medium text-stone-900">{{ $order->nama_pelanggan ?? 'Tamu Walk-in' }}</div>
                                <div class="text-[11px] text-stone-500">{{ $order->user?->email ?? 'Direct Counter' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @if(($order->service_type ?? '') === 'take_away')
                                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-stone-100 text-stone-700 border border-stone-200">TAKE AWAY</span>
                                @else
                                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-stone-100 text-stone-700 border border-stone-200">DINE IN</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-semibold text-stone-900">
                                Rp {{ number_format($order->total_harga ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-stone-600 uppercase">{{ $order->payment_method ?? 'CASH' }}</td>
                            <td class="px-4 py-3">
                                @if(in_array($paymentStatus, ['paid', 'sudah']))
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px]">LUNAS</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 text-[10px]">BELUM BAYAR</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($orderStatus === 'completed')
                                    <span class="px-2 py-0.5 rounded bg-stone-100 text-stone-700 border border-stone-200 text-[10px]">SELESAI</span>
                                @elseif($orderStatus === 'cancelled')
                                    <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-200 text-[10px]">BATAL</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200 text-[10px]">DIPROSES</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-stone-500 text-[11px]">
                                {{ $order->tanggal ? $order->tanggal->locale('id')->isoFormat('HH:mm') : ($order->created_at ? $order->created_at->format('H:i') : '-') }}
                            </td>
                            <td class="px-4 py-3 text-right space-x-1.5">
                                <a href="{{ route('admin.receipt.view', $order->id_order) }}" class="inline-block text-stone-600 hover:text-stone-900 p-1 border border-stone-200 rounded hover:bg-stone-100" title="Lihat Struk">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                @if(($order->status_order ?? 'pending') !== 'completed')
                                    <form method="POST" action="{{ route('admin.orders.complete', $order->id_order) }}" class="inline-block m-0">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="p-1 text-emerald-700 border border-emerald-300 rounded hover:bg-emerald-50 cursor-pointer" title="Selesaikan Pesanan" onclick="return confirm('Tandai pesanan #{{ $order->id_order }} selesai?')">
                                            <i class="fas fa-check text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-stone-400">
                                Belum ada pesanan aktif saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterOrders() {
    const searchVal = document.getElementById('orderSearchInput').value.toLowerCase();
    const statusVal = document.getElementById('orderStatusFilter').value.toLowerCase();
    const rows = document.querySelectorAll('#ordersTable tbody .order-row');

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        const status = row.getAttribute('data-status') || '';
        const matchSearch = text.includes(searchVal);
        const matchStatus = !statusVal || status === statusVal;

        if (matchSearch && matchStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
@endsection
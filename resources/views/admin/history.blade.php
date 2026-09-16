@extends('dashboard')

@section('page-title', 'Riwayat & Laporan Penjualan')
@section('breadcrumb', 'Manajemen Transaksi')

@section('content')
<div class="history-page-container px-4 py-2">

    {{-- Page Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-stone-800">Riwayat & Laporan Penjualan</h2>
            <p class="text-stone-500 text-sm mt-1">
                Total Pendapatan Valid: <span class="font-bold text-emerald-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                • {{ $totalOrders }} Transaksi tercatat
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.stats.pdf', array_merge(request()->all(), ['range' => request('range', 'custom')])) }}"
               class="bg-[#C87D38] hover:bg-[#A8642A] text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-md transition-all flex items-center gap-2">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
            <button class="bg-stone-100 hover:bg-stone-200 text-stone-700 px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 border border-stone-200">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
        </div>
    </div>

    {{-- Unified Filter Toolbar --}}
    <div class="bg-white p-5 rounded-xl shadow-sm border border-stone-200 mb-6">
        <form method="GET" action="{{ route('admin.history') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            {{-- Range Quick Picker (Optional if needed, but we use manual range) --}}
            <div class="md:col-span-3 flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-stone-400 uppercase tracking-wider">Rentang Tanggal</label>
                <div class="flex items-center gap-2">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full bg-stone-50 border border-stone-200 rounded-lg text-xs py-2 px-3 outline-none focus:ring-2 focus:ring-[#C87D38]/10 focus:border-[#C87D38]">
                    <span class="text-stone-300">-</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full bg-stone-50 border border-stone-200 rounded-lg text-xs py-2 px-3 outline-none focus:ring-2 focus:ring-[#C87D38]/10 focus:border-[#C87D38]">
                </div>
            </div>

            {{-- Filter Staff --}}
            <div class="md:col-span-2 flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-stone-400 uppercase tracking-wider">Staff / Kasir</label>
                <select name="staff" class="w-full bg-stone-50 border border-stone-200 rounded-lg text-xs py-2 px-3 outline-none focus:border-[#C87D38] cursor-pointer">
                    <option value="all">Semua Pegawai</option>
                    @foreach($staffList as $staff)
                        <option value="{{ $staff->name }}" {{ request('staff') == $staff->name ? 'selected' : '' }}>{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Payment --}}
            <div class="md:col-span-2 flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-stone-400 uppercase tracking-wider">Metode Bayar</label>
                <select name="payment_method" class="w-full bg-stone-50 border border-stone-200 rounded-lg text-xs py-2 px-3 outline-none focus:border-[#C87D38] cursor-pointer">
                    <option value="all">Semua Metode</option>
                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Tunai / Cash</option>
                    <option value="qris" {{ request('payment_method') == 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                </select>
            </div>

            {{-- Filter Status --}}
            <div class="md:col-span-2 flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-stone-400 uppercase tracking-wider">Status</label>
                <select name="status" class="w-full bg-stone-50 border border-stone-200 rounded-lg text-xs py-2 px-3 outline-none focus:border-[#C87D38] cursor-pointer">
                    <option value="all">Semua Status</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan (Void)</option>
                </select>
            </div>

            <div class="md:col-span-3 flex gap-2">
                <button type="submit" class="bg-stone-800 hover:bg-black text-white px-6 py-2 rounded-lg text-sm font-bold shadow-md transition-all flex-1 uppercase tracking-wide">
                    Terapkan
                </button>
                <a href="{{ route('admin.history') }}" class="bg-stone-100 hover:bg-stone-200 text-stone-600 px-4 py-2 rounded-lg transition-all border border-stone-200 flex items-center justify-center" title="Reset Filters">
                    <i class="fas fa-undo text-sm"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-stone-50 border-b border-stone-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase tracking-wider">No / ID Nota</th>
                        <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase tracking-wider">Waktu Transaksi</th>
                        <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase tracking-wider">Kasir Bertugas</th>
                        <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase tracking-wider">Daftar Item Menu</th>
                        <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase tracking-wider text-center">Metode</th>
                        <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Total Nominal</th>
                        <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($historyOrders as $index => $order)
                        <tr class="hover:bg-stone-50/50 transition-colors group {{ $order->status_order === 'cancelled' ? 'bg-rose-50/20' : '' }}">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-[#C87D38]">#ORD-{{ $order->id_order }}</span>
                                    @if($order->status_order === 'cancelled')
                                        <span class="text-[9px] font-black text-rose-500 uppercase tracking-tighter">CANCELLED / VOID</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-stone-600 font-medium">
                                <div class="text-sm">{{ $order->tanggal ? $order->tanggal->format('d/m/Y') : '-' }}</div>
                                <div class="text-[11px] text-stone-400 font-normal">{{ $order->tanggal ? $order->tanggal->format('H:i') : '-' }} WIB</div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $cashier = $order->cashier_name ?: ($order->user?->name ?: 'Staff');
                                    $badgeColor = match($cashier) {
                                        'Robin' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                        'Dery' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                        'Nikita' => 'bg-pink-50 text-pink-700 border-pink-100',
                                        default => 'bg-stone-50 text-stone-600 border-stone-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $badgeColor }}">
                                    {{ $cashier }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-[11px] text-stone-600 leading-relaxed line-clamp-2 max-w-[240px]">
                                    @foreach($order->items as $item)
                                        <span class="font-bold">{{ $item->quantity }}x</span> {{ $item->menu?->nama_menu ?? 'Item' }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $method = strtoupper($order->payment_method ?: 'CASH');
                                    $methodIcon = match($method) {
                                        'QRIS' => 'fa-qrcode text-blue-500',
                                        'TRANSFER' => 'fa-bank text-purple-500',
                                        default => 'fa-money-bill-wave text-stone-400'
                                    };
                                @endphp
                                <div class="flex flex-col items-center gap-0.5">
                                    <i class="fas {{ $methodIcon }} text-xs"></i>
                                    <span class="text-[10px] font-bold text-stone-500">{{ $method }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold {{ $order->status_order === 'cancelled' ? 'text-stone-300 line-through' : 'text-stone-800' }}">
                                    Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.receipt.view', $order->id_order) }}" class="w-8 h-8 flex items-center justify-center text-stone-400 hover:text-[#C87D38] hover:bg-amber-50 rounded-full transition-all" title="Detail">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.receipt.print', $order->id_order) }}" target="_blank" class="w-8 h-8 flex items-center justify-center text-stone-400 hover:text-stone-900 hover:bg-stone-100 rounded-full transition-all" title="Cetak Struk">
                                        <i class="fas fa-print text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-24 text-center">
                                <div class="flex flex-col items-center opacity-30">
                                    <i class="fas fa-file-invoice text-5xl mb-4 text-stone-300"></i>
                                    <p class="text-sm font-bold text-stone-400 uppercase tracking-widest">Tidak ada riwayat ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($historyOrders->hasPages())
            <div class="px-6 py-5 bg-stone-50 border-t border-stone-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-stone-500 font-medium">Menampilkan {{ $historyOrders->firstItem() }} - {{ $historyOrders->lastItem() }} dari {{ $historyOrders->total() }} transaksi</span>
                    <div class="pagination-clean">
                        {{ $historyOrders->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    /* Pagination Polishing */
    .pagination-clean nav svg { width: 14px; height: 14px; }
    .pagination-clean .relative.inline-flex { border-radius: 8px; overflow: hidden; border-color: #E5E7EB; }
</style>
@endsection

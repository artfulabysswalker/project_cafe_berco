@extends('dashboard')

@section('page-title', 'Overview & POS Analytics')
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- 1. SUMMARY METRICS CARDS (4 High-Density Cards) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Card 1: Total Omzet --}}
        <div class="bg-white border border-stone-200 rounded-lg p-4 shadow-2xs space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-mono uppercase tracking-wider text-stone-500 font-medium">TOTAL OMZET</span>
                <span class="text-[11px] font-mono text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Live</span>
            </div>
            <div>
                <h3 class="text-2xl font-mono font-semibold text-stone-900 tracking-tight">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </h3>
                <div class="flex items-center space-x-2 text-xs font-mono text-stone-500 mt-1">
                    <span>Cash: <strong class="text-stone-700">Rp {{ number_format($cashRevenue ?? 0, 0, ',', '.') }}</strong></span>
                    <span>•</span>
                    <span>QRIS: <strong class="text-stone-700">Rp {{ number_format($qrisRevenue ?? 0, 0, ',', '.') }}</strong></span>
                </div>
            </div>
        </div>

        {{-- Card 2: Total HPP & Laba Kotor --}}
        <div class="bg-white border border-stone-200 rounded-lg p-4 shadow-2xs space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-mono uppercase tracking-wider text-stone-500 font-medium">TOTAL HPP (MODAL)</span>
                <span class="text-[11px] font-mono text-stone-600 bg-stone-100 px-2 py-0.5 rounded border border-stone-200">COGS</span>
            </div>
            <div>
                <h3 class="text-2xl font-mono font-semibold text-stone-900 tracking-tight">
                    Rp {{ number_format($cogsTotal, 0, ',', '.') }}
                </h3>
                <p class="text-xs font-mono text-stone-500 mt-1">
                    Laba Kotor: <strong class="text-stone-700">Rp {{ number_format($grossProfit, 0, ',', '.') }}</strong>
                </p>
            </div>
        </div>

        {{-- Card 3: Estimasi Laba Bersih & Margin --}}
        <div class="bg-white border border-stone-200 rounded-lg p-4 shadow-2xs space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-mono uppercase tracking-wider text-stone-500 font-medium">ESTIMASI LABA BERSIH</span>
                <span class="text-[11px] font-mono text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">Margin: {{ $profitMarginPct ?? 0 }}%</span>
            </div>
            <div>
                <h3 class="text-2xl font-mono font-semibold text-emerald-700 tracking-tight">
                    Rp {{ number_format($netProfit, 0, ',', '.') }}
                </h3>
                <p class="text-xs font-mono text-stone-500 mt-1">
                    Rata-rata Margin Toko: <strong class="text-stone-700">{{ $profitMarginPct ?? 0 }}%</strong>
                </p>
            </div>
        </div>

        {{-- Card 4: Total Pesanan --}}
        <div class="bg-white border border-stone-200 rounded-lg p-4 shadow-2xs space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-mono uppercase tracking-wider text-stone-500 font-medium">VOLUME TRANSAKSI</span>
                @if($pendingOrdersCount > 0)
                    <span class="text-[11px] font-mono text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">{{ $pendingOrdersCount }} Pending</span>
                @else
                    <span class="text-[11px] font-mono text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Selesai</span>
                @endif
            </div>
            <div>
                <h3 class="text-2xl font-mono font-semibold text-stone-900 tracking-tight">
                    {{ $totalOrders }} <span class="text-sm font-normal text-stone-500">Order</span>
                </h3>
                <p class="text-xs font-mono text-stone-500 mt-1">
                    Cash: <strong class="text-stone-700">{{ $cashCount ?? 0 }}</strong> | QRIS: <strong class="text-stone-700">{{ $qrisCount ?? 0 }}</strong>
                </p>
            </div>
        </div>

    </div>

    {{-- 2. CHARTS SECTION (Sales Trend & Payment Breakdown) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        
        {{-- Left: 7-Day Trend Chart --}}
        <div class="lg:col-span-8 bg-white border border-stone-200 rounded-lg p-5 shadow-2xs space-y-4">
            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#C27835]"></span>
                    <h3 class="text-xs font-mono uppercase tracking-wider font-semibold text-stone-800">Tren Penjualan 7 Hari Terakhir</h3>
                </div>
                <div class="flex items-center space-x-4 text-xs font-mono text-stone-500">
                    <span class="flex items-center space-x-1.5"><span class="w-2 h-2 rounded bg-[#C27835]"></span><span>Omzet (Rp)</span></span>
                    <span class="flex items-center space-x-1.5"><span class="w-2 h-2 rounded bg-sky-500"></span><span>Pesanan (Trx)</span></span>
                </div>
            </div>
            <div class="h-64 relative">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        {{-- Right: Payment Methods Donut --}}
        <div class="lg:col-span-4 bg-white border border-stone-200 rounded-lg p-5 shadow-2xs space-y-4 flex flex-col justify-between">
            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                <h3 class="text-xs font-mono uppercase tracking-wider font-semibold text-stone-800">Kanal Pembayaran</h3>
                <span class="text-[11px] font-mono text-stone-400">Distribusi</span>
            </div>
            <div class="h-44 relative flex items-center justify-center">
                <canvas id="paymentMethodsChart"></canvas>
            </div>
            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-stone-100 text-xs font-mono">
                @foreach($paymentLabels as $index => $label)
                    <div class="bg-stone-50 border border-stone-100 p-2 rounded flex justify-between">
                        <span class="text-stone-600 truncate">{{ $label }}</span>
                        <strong class="text-stone-900">{{ $paymentCounts[$index] ?? 0 }}</strong>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- 3. TWO-COLUMN: TOP SELLING & STOCK STATUS --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        
        {{-- Top Selling Products --}}
        <div class="lg:col-span-6 bg-white border border-stone-200 rounded-lg shadow-2xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-stone-200 flex items-center justify-between">
                <h3 class="text-xs font-mono uppercase tracking-wider font-semibold text-stone-800">5 Menu Terlaris</h3>
                <a href="{{ route('admin.menu') }}" class="text-xs font-mono text-[#C27835] hover:underline">Kelola Menu →</a>
            </div>
            <div class="divide-y divide-stone-100">
                @php $maxSold = max(1, collect($topProducts)->max('total_sold') ?? 1); @endphp
                @forelse($topProducts as $index => $product)
                    <div class="px-5 py-3 flex items-center justify-between hover:bg-stone-50/70 transition-colors">
                        <div class="flex items-center space-x-3 min-w-0">
                            <span class="w-5 font-mono text-xs text-stone-400 font-semibold">{{ $index + 1 }}.</span>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-stone-900 truncate">{{ $product->nama_menu }}</p>
                                <p class="text-[11px] font-mono text-stone-500">Rp {{ number_format($product->harga, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="text-right font-mono">
                            <span class="text-xs font-semibold text-stone-900">{{ $product->total_sold }}</span>
                            <span class="text-[10px] text-stone-500 block">Terjual</span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs font-mono text-stone-400">Belum ada data penjualan menu.</div>
                @endforelse
            </div>
        </div>

        {{-- Stock & Menu Status Monitoring --}}
        <div class="lg:col-span-6 bg-white border border-stone-200 rounded-lg shadow-2xs overflow-hidden flex flex-col justify-between">
            <div class="px-5 py-3.5 border-b border-stone-200 flex items-center justify-between">
                <h3 class="text-xs font-mono uppercase tracking-wider font-semibold text-stone-800">Monitoring Ketersediaan</h3>
                <a href="{{ route('admin.menu.create') }}" class="text-xs font-mono bg-stone-900 text-white px-2.5 py-1 rounded hover:bg-stone-800 transition-colors">+ Tambah Menu</a>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-3 gap-2 text-center font-mono">
                    <div class="bg-stone-50 p-2.5 rounded border border-stone-200">
                        <span class="block text-[10px] text-stone-500 uppercase">Total Menu</span>
                        <span class="text-lg font-semibold text-stone-900">{{ $totalMenus }}</span>
                    </div>
                    <div class="bg-emerald-50/60 p-2.5 rounded border border-emerald-200">
                        <span class="block text-[10px] text-emerald-700 uppercase">Siap Saji</span>
                        <span class="text-lg font-semibold text-emerald-800">{{ $availableMenus }}</span>
                    </div>
                    <div class="bg-amber-50/60 p-2.5 rounded border border-amber-200">
                        <span class="block text-[10px] text-amber-700 uppercase">Stok Habis</span>
                        <span class="text-lg font-semibold text-amber-800">{{ $outOfStockMenus->count() }}</span>
                    </div>
                </div>

                <div>
                    @if($outOfStockMenus->count() > 0)
                        <div class="space-y-1.5 max-h-40 overflow-y-auto pr-1">
                            @foreach($outOfStockMenus as $outMenu)
                                <div class="flex items-center justify-between p-2 rounded bg-amber-50/50 border border-amber-200 text-xs">
                                    <div class="font-medium text-amber-900 truncate">{{ $outMenu->nama_menu }}</div>
                                    <div class="flex items-center space-x-2 font-mono">
                                        <span class="text-[10px] text-amber-800 bg-amber-100 px-1.5 py-0.5 rounded">Habis</span>
                                        <a href="{{ route('admin.menu.edit', $outMenu->id_menu) }}" class="text-stone-500 hover:text-stone-900"><i class="fas fa-pen text-[10px]"></i></a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-3 bg-stone-50 border border-stone-200 rounded text-xs text-stone-600 flex items-center space-x-2">
                            <i class="fas fa-check-circle text-emerald-600"></i>
                            <span>Semua menu dalam kondisi tersedia & siap dipesan.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- 4. RECENT ORDERS HIGH-DENSITY TABLE --}}
    <div class="bg-white border border-stone-200 rounded-lg shadow-2xs overflow-hidden">
        <div class="px-5 py-3.5 border-b border-stone-200 flex items-center justify-between">
            <h3 class="text-xs font-mono uppercase tracking-wider font-semibold text-stone-800">5 Transaksi Terakhir</h3>
            <a href="{{ route('admin.orders') }}" class="text-xs font-mono text-[#C27835] hover:underline">Semua Pesanan →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-mono">
                <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 text-[10px] uppercase">
                    <tr>
                        <th class="px-5 py-2.5">Order ID</th>
                        <th class="px-5 py-2.5">Pelanggan</th>
                        <th class="px-5 py-2.5">Total Harga</th>
                        <th class="px-5 py-2.5">Metode Bayar</th>
                        <th class="px-5 py-2.5">Status</th>
                        <th class="px-5 py-2.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-stone-50/70 transition-colors">
                            <td class="px-5 py-3 font-semibold text-stone-900">#ORD-{{ $order->id_order }}</td>
                            <td class="px-5 py-3 font-sans text-stone-800">{{ $order->user?->name ?? 'Tamu Walk-in' }}</td>
                            <td class="px-5 py-3 font-semibold text-stone-900">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-stone-600 uppercase">{{ $order->payment_method ?? 'Cash' }}</td>
                            <td class="px-5 py-3">
                                @if(in_array($order->status_pembayaran, ['paid', 'Sudah']) || $order->status_order === 'completed')
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px]">LUNAS</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 text-[10px]">PENDING</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.receipt.view', $order->id_order) }}" class="text-xs text-stone-600 hover:text-stone-900 underline underline-offset-2">Struk</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-6 text-center text-stone-400">Belum ada transaksi hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Chart.js Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Sales Trend Chart
    const trendCtx = document.getElementById('salesTrendChart');
    if (trendCtx) {
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($sevenDaysDates) !!},
                datasets: [
                    {
                        label: 'Omzet Penjualan (Rp)',
                        data: {!! json_encode($sevenDaysRevenue) !!},
                        borderColor: '#C27835',
                        backgroundColor: 'rgba(194, 120, 53, 0.08)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.25,
                        pointRadius: 4,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Jumlah Pesanan',
                        data: {!! json_encode($sevenDaysOrders) !!},
                        borderColor: '#0284c7',
                        backgroundColor: '#0284c7',
                        type: 'bar',
                        borderRadius: 4,
                        barThickness: 16,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 10,
                        callbacks: {
                            label: function(ctx) {
                                if (ctx.dataset.yAxisID === 'y') {
                                    return 'Omzet: Rp ' + ctx.parsed.y.toLocaleString('id-ID');
                                }
                                return 'Pesanan: ' + ctx.parsed.y + ' trx';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'JetBrains Mono', size: 11 }, color: '#78716c' }
                    },
                    y: {
                        type: 'linear',
                        position: 'left',
                        grid: { color: '#f5f5f4' },
                        ticks: {
                            font: { family: 'JetBrains Mono', size: 10 },
                            color: '#78716c',
                            callback: function(val) {
                                return 'Rp ' + (val >= 1000 ? (val/1000).toFixed(0) + 'k' : val);
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: {
                            font: { family: 'JetBrains Mono', size: 10 },
                            color: '#78716c',
                            stepSize: 1,
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // 2. Payment Donut Chart
    const paymentCtx = document.getElementById('paymentMethodsChart');
    if (paymentCtx) {
        new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($paymentLabels) !!},
                datasets: [{
                    data: {!! json_encode($paymentCounts) !!},
                    backgroundColor: [
                        '#C27835',
                        '#059669',
                        '#0284c7',
                        '#d97706',
                        '#475569'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '72%'
            }
        });
    }
});
</script>
@endsection

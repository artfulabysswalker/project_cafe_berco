<x-layouts::app :title="__('Analytics & Insights - Admin')">
    <div class="mx-auto max-w-7xl py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">📊 Analytics & Insights</h1>
                    <p class="mt-2 text-neutral-600 dark:text-neutral-400">Analisis performa penjualan dan produk Anda</p>
                </div>
                <a href="{{ route('admin.analytics.export', ['period' => $period, 'date' => $date]) }}" class="rounded-lg bg-green-600 px-4 py-2 font-semibold text-white hover:bg-green-700">
                    📥 Export CSV
                </a>
            </div>
        </div>

        <!-- Period Selector -->
        <div class="mb-8 flex gap-4">
            <form method="GET" action="{{ route('admin.analytics.dashboard') }}" class="flex items-center gap-4">
                <div>
                    <select name="period" class="rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
                        <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>Harian</option>
                        <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                        <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                    </select>
                </div>
                <div>
                    <input type="date" name="date" value="{{ $date }}" class="rounded-lg border border-neutral-300 px-4 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
                </div>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">
                    Filter
                </button>
            </form>
        </div>

        <!-- Sales Summary -->
        @if($periodTransactions['date_range']['label'])
        <div class="mb-8 rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div class="text-center">
                    <p class="text-sm text-neutral-600 dark:text-neutral-400">Transaksi</p>
                    <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">{{ $periodTransactions['total_transactions'] }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-neutral-600 dark:text-neutral-400">Total Pendapatan</p>
                    <p class="mt-2 text-2xl lg:text-3xl font-bold text-green-600 whitespace-nowrap">Rp {{ number_format($periodTransactions['total_revenue'], 0, ',', '.') }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-neutral-600 dark:text-neutral-400">Keuntungan</p>
                    <p class="mt-2 text-2xl lg:text-3xl font-bold text-blue-600 whitespace-nowrap">Rp {{ number_format($periodTransactions['total_profit'], 0, ',', '.') }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-neutral-600 dark:text-neutral-400">Avg per Transaksi</p>
                    <p class="mt-2 text-2xl lg:text-3xl font-bold text-orange-600 whitespace-nowrap">Rp {{ number_format($periodTransactions['avg_transaction'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Chart Section -->
        <div class="mb-8 grid grid-cols-1 gap-8 md:grid-cols-2">
            <!-- Revenue & Profit Chart -->
            <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <div class="flex items-center justify-between border-b border-neutral-100 pb-3 dark:border-neutral-800">
                    <h2 class="text-xl font-bold text-neutral-900 dark:text-white">💰 Tren Pendapatan & Keuntungan</h2>
                    <span class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Rupiah (Rp)</span>
                </div>
                <div class="relative mt-4 h-72 w-full">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Transactions Chart -->
            <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <div class="flex items-center justify-between border-b border-neutral-100 pb-3 dark:border-neutral-800">
                    <h2 class="text-xl font-bold text-neutral-900 dark:text-white">📈 Jumlah Transaksi</h2>
                    <span class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Frekuensi (Trx)</span>
                </div>
                <div class="relative mt-4 h-72 w-full">
                    <canvas id="transactionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Products Section -->
        <div class="mb-8 rounded-lg border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <h2 class="text-xl font-bold text-neutral-900 dark:text-white">🏆 Produk Terlaris</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-neutral-200 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800">
                            <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Peringkat</th>
                            <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Nama Menu</th>
                            <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Terjual</th>
                            <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Pesanan</th>
                            <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Total Revenue</th>
                            <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Margin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $product)
                        <tr class="border-b border-neutral-200 dark:border-neutral-700">
                            <td class="px-6 py-4">
                                @if($product->rank <= 3)
                                    <span class="text-2xl">{{ $product->rank == 1 ? '🥇' : ($product->rank == 2 ? '🥈' : '🥉') }}</span>
                                @else
                                    <span class="font-semibold text-neutral-900 dark:text-white">#{{ $product->rank }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-neutral-900 dark:text-white">{{ $product->menu_name }}</td>
                            <td class="px-6 py-4 text-neutral-900 dark:text-white">{{ $product->total_sold }} unit</td>
                            <td class="px-6 py-4 text-neutral-900 dark:text-white">{{ $product->times_ordered }}x</td>
                            <td class="px-6 py-4">
                                <span class="inline-block whitespace-nowrap rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-700 dark:bg-green-900 dark:text-green-200">
                                    Rp {{ number_format($product->total_revenue, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block whitespace-nowrap rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-700 dark:bg-blue-900 dark:text-blue-200">
                                    Rp {{ number_format($product->total_margin, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-neutral-500 dark:text-neutral-400">
                                Belum ada data penjualan produk
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Method Summary -->
        @if(!empty($paymentMethodSummary))
        <div class="mb-8 rounded-lg border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <h2 class="text-xl font-bold text-neutral-900 dark:text-white">💳 Rekap Metode Pembayaran</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-neutral-200 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800">
                            <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Metode Pembayaran</th>
                            <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Jumlah Transaksi</th>
                            <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Total Omzet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paymentMethodSummary as $method)
                        <tr class="border-b border-neutral-200 dark:border-neutral-700">
                            <td class="px-6 py-4 font-medium text-neutral-900 dark:text-white">{{ $method->payment_method_label ?? $method->payment_method }}</td>
                            <td class="px-6 py-4 text-neutral-900 dark:text-white">{{ $method->transaction_count }} transaksi</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-700 dark:bg-green-900 dark:text-green-200">
                                    Rp {{ number_format($method->total_revenue, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Purchase History -->
        <div class="rounded-lg border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <h2 class="text-xl font-bold text-neutral-900 dark:text-white">🛒 History Pembelian {{ $periodTransactions['date_range']['label'] ?? 'Hari Ini' }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-neutral-200 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800">
                            <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Waktu</th>
                            <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Pelanggan</th>
                            <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Produk</th>
                            <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Subtotal</th>
                            <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Total</th>
                            <th class="px-6 py-4 text-left font-semibold text-neutral-900 dark:text-white">Keuntungan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($periodTransactions['purchase_history'] as $order)
                        <tr class="border-b border-neutral-200 dark:border-neutral-700">
                            <td class="px-6 py-4 text-sm text-neutral-600 dark:text-neutral-400">
                                {{ $order->tanggal->format('H:i') }}
                            </td>
                            <td class="px-6 py-4 font-medium text-neutral-900 dark:text-white">
                                {{ $order->user->name ?? $order->nama_pelanggan }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="space-y-1">
                                    @foreach($order->items as $item)
                                        <p class="text-neutral-900 dark:text-white">{{ $item->menu->nama_menu }} x{{ $item->quantity }}</p>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-neutral-900 dark:text-white">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-green-100 px-3 py-1 font-semibold text-green-700 dark:bg-green-900 dark:text-green-200">
                                    Rp {{ number_format($order->final_total, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-blue-100 px-3 py-1 font-semibold text-blue-700 dark:bg-blue-900 dark:text-blue-200">
                                    Rp {{ number_format($order->profit_margin, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-neutral-500 dark:text-neutral-400">
                                Belum ada transaksi untuk periode ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function initAnalyticsCharts() {
            if (typeof Chart === 'undefined') return;

            const isDark = document.documentElement.classList.contains('dark') || window.matchMedia('(prefers-color-scheme: dark)').matches;
            const textColor = isDark ? '#f4f4f5' : '#18181b';
            const mutedColor = isDark ? '#e4e4e7' : '#3f3f46';
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.12)' : 'rgba(0, 0, 0, 0.08)';

            // Revenue & Profit Chart
            const revenueCanvas = document.getElementById('revenueChart');
            if (revenueCanvas) {
                const existingRev = Chart.getChart(revenueCanvas);
                if (existingRev) existingRev.destroy();

                const revenueCtx = revenueCanvas.getContext('2d');
                new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: @json($chartData['labels']),
                        datasets: [
                            {
                                label: 'Pendapatan',
                                data: @json($chartData['revenue']),
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.15)',
                                borderWidth: 2.5,
                                pointRadius: 0,
                                pointHoverRadius: 6,
                                pointHitRadius: 10,
                                pointBackgroundColor: '#10b981',
                                pointBorderColor: '#ffffff',
                                tension: 0.35,
                                fill: true,
                            },
                            {
                                label: 'Keuntungan',
                                data: @json($chartData['profit']),
                                borderColor: '#38bdf8',
                                borderDash: [5, 4],
                                backgroundColor: 'rgba(56, 189, 248, 0.1)',
                                borderWidth: 2.5,
                                pointRadius: 0,
                                pointHoverRadius: 6,
                                pointHitRadius: 10,
                                pointBackgroundColor: '#38bdf8',
                                pointBorderColor: '#ffffff',
                                tension: 0.35,
                                fill: true,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: { top: 12, right: 12, bottom: 4, left: 4 }
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    color: textColor,
                                    font: { size: 12, weight: '600' },
                                    padding: 16,
                                    usePointStyle: true,
                                    boxWidth: 8,
                                }
                            },
                            tooltip: {
                                backgroundColor: isDark ? 'rgba(24, 24, 27, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                                titleColor: isDark ? '#ffffff' : '#09090b',
                                bodyColor: isDark ? '#f4f4f5' : '#27272a',
                                borderColor: isDark ? '#3f3f46' : '#e4e4e7',
                                borderWidth: 1,
                                padding: 10,
                                boxPadding: 4,
                                usePointStyle: true,
                                callbacks: {
                                    label: function(ctx) {
                                        const val = Number(ctx.parsed.y || 0);
                                        return ' ' + ctx.dataset.label + ': Rp ' + val.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    color: gridColor,
                                    drawBorder: false,
                                },
                                ticks: {
                                    color: mutedColor,
                                    font: { size: 11, weight: '500' },
                                    maxRotation: 45,
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: gridColor,
                                    drawBorder: false,
                                },
                                ticks: {
                                    color: mutedColor,
                                    font: { size: 11, weight: '500' },
                                    callback: function(value) {
                                        if (value >= 1000000) {
                                            return 'Rp ' + (value / 1000000).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' jt';
                                        }
                                        if (value >= 1000) {
                                            return 'Rp ' + (value / 1000).toLocaleString('id-ID', { maximumFractionDigits: 0 }) + ' rb';
                                        }
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Transactions Chart
            const transactionCanvas = document.getElementById('transactionChart');
            if (transactionCanvas) {
                const existingTrx = Chart.getChart(transactionCanvas);
                if (existingTrx) existingTrx.destroy();

                const transactionCtx = transactionCanvas.getContext('2d');
                new Chart(transactionCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($chartData['labels']),
                        datasets: [{
                            label: 'Jumlah Transaksi',
                            data: @json($chartData['transactions']),
                            backgroundColor: '#a855f7',
                            hoverBackgroundColor: '#c084fc',
                            borderColor: '#9333ea',
                            borderWidth: 1,
                            borderRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: { top: 16, right: 12, bottom: 4, left: 4 }
                        },
                        plugins: {
                            legend: {
                                display: false,
                            },
                            tooltip: {
                                backgroundColor: isDark ? 'rgba(24, 24, 27, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                                titleColor: isDark ? '#ffffff' : '#09090b',
                                bodyColor: isDark ? '#f4f4f5' : '#27272a',
                                borderColor: isDark ? '#3f3f46' : '#e4e4e7',
                                borderWidth: 1,
                                padding: 10,
                                callbacks: {
                                    label: function(ctx) {
                                        return ' ' + ctx.parsed.y + ' transaksi';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false,
                                },
                                ticks: {
                                    color: mutedColor,
                                    font: { size: 11, weight: '500' },
                                    autoSkip: false,
                                    maxRotation: 45,
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grace: '15%',
                                grid: {
                                    color: gridColor,
                                    drawBorder: false,
                                },
                                ticks: {
                                    color: mutedColor,
                                    font: { size: 11, weight: '500' },
                                    stepSize: 1,
                                    precision: 0,
                                    callback: function(value) {
                                        if (Number.isInteger(value)) {
                                            return value + ' trx';
                                        }
                                        return '';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAnalyticsCharts);
        } else {
            initAnalyticsCharts();
        }
        document.addEventListener('livewire:navigated', initAnalyticsCharts);
    </script>
</x-layouts::app>
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

        <!-- Today's Sales Summary -->
        <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-600 dark:text-neutral-400">Transaksi Hari Ini</p>
                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">{{ $todaySales['total_transactions'] }}</p>
                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">📅 {{ $todaySales['date'] }}</p>
            </div>

            <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-600 dark:text-neutral-400">Total Pendapatan</p>
                <p class="mt-2 text-3xl font-bold text-green-600">Rp {{ number_format($todaySales['total_revenue'], 0, ',', '.') }}</p>
                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">Rata-rata: Rp {{ number_format($todaySales['avg_transaction'], 0, ',', '.') }}</p>
            </div>

            <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-600 dark:text-neutral-400">Keuntungan</p>
                <p class="mt-2 text-3xl font-bold text-blue-600">Rp {{ number_format($todaySales['total_profit'], 0, ',', '.') }}</p>
                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">Profit Margin</p>
            </div>

            <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-600 dark:text-neutral-400">Total Pajak</p>
                <p class="mt-2 text-3xl font-bold text-orange-600">Rp {{ number_format($todaySales['total_tax'], 0, ',', '.') }}</p>
                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">PB1 & Charges</p>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="mb-8 grid grid-cols-1 gap-8 md:grid-cols-2">
            <!-- Revenue & Profit Chart -->
            <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <h2 class="text-xl font-bold text-neutral-900 dark:text-white">💰 Tren Pendapatan & Keuntungan</h2>
                <canvas id="revenueChart" class="mt-4"></canvas>
            </div>

            <!-- Transactions Chart -->
            <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <h2 class="text-xl font-bold text-neutral-900 dark:text-white">📈 Jumlah Transaksi</h2>
                <canvas id="transactionChart" class="mt-4"></canvas>
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
                                <span class="rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-700 dark:bg-green-900 dark:text-green-200">
                                    Rp {{ number_format($product->total_revenue, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-700 dark:bg-blue-900 dark:text-blue-200">
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

        <!-- Purchase History -->
        <div class="rounded-lg border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <h2 class="text-xl font-bold text-neutral-900 dark:text-white">🛒 History Pembelian Hari Ini</h2>
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
                        @forelse($todaySales['purchase_history'] as $order)
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
                                Belum ada transaksi hari ini
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
        // Revenue & Profit Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: [
                    {
                        label: 'Pendapatan',
                        data: @json($chartData['revenue']),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true,
                    },
                    {
                        label: 'Keuntungan',
                        data: @json($chartData['profit']),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        // Transactions Chart
        const transactionCtx = document.getElementById('transactionChart').getContext('2d');
        new Chart(transactionCtx, {
            type: 'bar',
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: @json($chartData['transactions']),
                    backgroundColor: '#8b5cf6',
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                    }
                }
            }
        });
    </script>
</x-layouts::app>

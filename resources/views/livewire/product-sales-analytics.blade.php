<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-neutral-900 dark:text-white">📈 Penjualan Produk & Analytics</h2>
            <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">Analisis performa penjualan dan produk terlaris</p>
        </div>
    </div>

    <!-- Period Filter -->
    <div class="rounded-lg border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="flex flex-wrap items-center gap-4">
            <div>
                <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Periode:</label>
                <div class="mt-2 flex gap-2">
                    <button wire:click="updatePeriod('daily')" 
                        @class(['px-4 py-2 rounded-lg text-sm font-semibold transition', 
                            'bg-blue-600 text-white' => $period === 'daily',
                            'bg-neutral-200 text-neutral-900 hover:bg-neutral-300 dark:bg-neutral-700 dark:text-white dark:hover:bg-neutral-600' => $period !== 'daily'
                        ])>
                        📅 Harian
                    </button>
                    <button wire:click="updatePeriod('monthly')"
                        @class(['px-4 py-2 rounded-lg text-sm font-semibold transition',
                            'bg-blue-600 text-white' => $period === 'monthly',
                            'bg-neutral-200 text-neutral-900 hover:bg-neutral-300 dark:bg-neutral-700 dark:text-white dark:hover:bg-neutral-600' => $period !== 'monthly'
                        ])>
                        📆 Bulanan
                    </button>
                    <button wire:click="updatePeriod('yearly')"
                        @class(['px-4 py-2 rounded-lg text-sm font-semibold transition',
                            'bg-blue-600 text-white' => $period === 'yearly',
                            'bg-neutral-200 text-neutral-900 hover:bg-neutral-300 dark:bg-neutral-700 dark:text-white dark:hover:bg-neutral-600' => $period !== 'yearly'
                        ])>
                        📊 Tahunan
                    </button>
                </div>
            </div>

            @if($period !== 'yearly')
            <div>
                <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Tanggal:</label>
                <input type="date" wire:model.live="date" class="mt-2 rounded-lg border border-neutral-300 bg-white px-4 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
            </div>
            @endif
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="rounded-lg border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <p class="text-sm text-neutral-600 dark:text-neutral-400">Total Produk Terjual</p>
            <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">{{ $summary['total_quantity'] ?? 0 }}</p>
            <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">unit</p>
        </div>

        <div class="rounded-lg border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <p class="text-sm text-neutral-600 dark:text-neutral-400">Total Pendapatan</p>
            <p class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400">Rp {{ number_format($summary['total_revenue'] ?? 0, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">dari penjualan</p>
        </div>

        <div class="rounded-lg border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <p class="text-sm text-neutral-600 dark:text-neutral-400">Total Pesanan</p>
            <p class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $summary['total_orders'] ?? 0 }}</p>
            <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">transaksi</p>
        </div>

        <div class="rounded-lg border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <p class="text-sm text-neutral-600 dark:text-neutral-400">Rata-rata per Pesanan</p>
            <p class="mt-2 text-2xl font-bold text-purple-600 dark:text-purple-400">
                {{ $summary['avg_items_per_order'] ?? 0 }} item
            </p>
            <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">Rp {{ number_format($summary['avg_revenue_per_order'] ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Top Products -->
    <div class="rounded-lg border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h3 class="text-lg font-bold text-neutral-900 dark:text-white">🏆 Produk Terlaris</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-neutral-200 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-700">
                        <th class="px-6 py-3 text-left text-sm font-semibold text-neutral-900 dark:text-white">Peringkat</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-neutral-900 dark:text-white">Nama Menu</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-neutral-900 dark:text-white">Terjual</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-neutral-900 dark:text-white">Pesanan</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-neutral-900 dark:text-white">Rata-rata/Pesanan</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-neutral-900 dark:text-white">Total Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($topProducts as $product)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700">
                        <td class="px-6 py-4">
                            @if($product['rank'] <= 3)
                                <span class="text-2xl">{{ $product['rank'] == 1 ? '🥇' : ($product['rank'] == 2 ? '🥈' : '🥉') }}</span>
                            @else
                                <span class="font-semibold text-neutral-900 dark:text-white">#{{ $product['rank'] }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-medium text-neutral-900 dark:text-white">{{ $product['menu_name'] }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-neutral-900 dark:text-white">{{ $product['total_sold'] }} unit</td>
                        <td class="px-6 py-4 text-right text-neutral-900 dark:text-white">{{ $product['times_ordered'] }}x</td>
                        <td class="px-6 py-4 text-right text-neutral-900 dark:text-white">{{ $product['avg_sold_per_order'] }} unit</td>
                        <td class="px-6 py-4 text-right">
                            <span class="rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-700 dark:bg-green-900 dark:text-green-200">
                                Rp {{ number_format($product['total_revenue'], 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-neutral-500 dark:text-neutral-400">
                            📭 Belum ada data penjualan produk
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- All Products -->
    <div class="rounded-lg border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h3 class="text-lg font-bold text-neutral-900 dark:text-white">📊 Semua Produk Penjualan</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-neutral-200 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-700">
                        <th class="px-6 py-3 text-left text-sm font-semibold text-neutral-900 dark:text-white">Produk</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-neutral-900 dark:text-white">Jumlah Terjual</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-neutral-900 dark:text-white">Pesanan</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-neutral-900 dark:text-white">Harga Rata-rata</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-neutral-900 dark:text-white">Total Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($products as $product)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700">
                        <td class="px-6 py-4 font-medium text-neutral-900 dark:text-white">{{ $product['menu_name'] }}</td>
                        <td class="px-6 py-4 text-right text-neutral-900 dark:text-white">{{ $product['quantity'] }} unit</td>
                        <td class="px-6 py-4 text-right text-neutral-900 dark:text-white">{{ $product['orders'] }}x</td>
                        <td class="px-6 py-4 text-right text-neutral-900 dark:text-white">Rp {{ number_format($product['avg_price'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right">
                            <span class="rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-700 dark:bg-blue-900 dark:text-blue-200">
                                Rp {{ number_format($product['revenue'], 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-neutral-500 dark:text-neutral-400">
                            📭 Belum ada data penjualan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-neutral-900 dark:text-white">📊 Penjualan Hari Ini</h2>
            <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">Ringkasan performa penjualan harian Anda</p>
        </div>
        <div class="flex items-center gap-2">
            <input type="date" wire:model.live="date" class="rounded-lg border border-neutral-300 bg-white px-4 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
            <button wire:click="loadTodaysSales()" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800">
                🔄 Refresh
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
        <!-- Total Transactions -->
        <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Jumlah Transaksi</p>
                    <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">{{ $todaySales['total_transactions'] ?? 0 }}</p>
                    <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">📅 {{ $todaySales['date_display'] ?? '' }}</p>
                </div>
                <div class="rounded-lg bg-blue-100 p-3 dark:bg-blue-900">
                    <i class="text-2xl text-blue-600 dark:text-blue-300 fas fa-receipt"></i>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Total Pendapatan</p>
                    <p class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400">Rp {{ number_format($todaySales['total_revenue'] ?? 0, 0, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">Rata-rata: Rp {{ number_format($todaySales['avg_transaction'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg bg-green-100 p-3 dark:bg-green-900">
                    <i class="text-2xl text-green-600 dark:text-green-300 fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>

        <!-- Total Profit -->
        <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Keuntungan</p>
                    <p class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($todaySales['total_profit'] ?? 0, 0, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">Profit Margin</p>
                </div>
                <div class="rounded-lg bg-blue-100 p-3 dark:bg-blue-900">
                    <i class="text-2xl text-blue-600 dark:text-blue-300 fas fa-chart-pie"></i>
                </div>
            </div>
        </div>

        <!-- Total Charge (Tax) -->
        <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Charge (PB1)</p>
                    <p class="mt-2 text-3xl font-bold text-orange-600 dark:text-orange-400">Rp {{ number_format($todaySales['total_charge'] ?? 0, 0, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">Total Pajak & Charge</p>
                </div>
                <div class="rounded-lg bg-orange-100 p-3 dark:bg-orange-900">
                    <i class="text-2xl text-orange-600 dark:text-orange-300 fas fa-percent"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase History -->
    <div class="rounded-lg border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h3 class="text-lg font-bold text-neutral-900 dark:text-white">🛒 History Pembelian</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-neutral-200 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-700">
                        <th class="px-6 py-3 text-left text-sm font-semibold text-neutral-900 dark:text-white">Waktu</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-neutral-900 dark:text-white">Pelanggan</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-neutral-900 dark:text-white">Produk</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-neutral-900 dark:text-white">Subtotal</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-neutral-900 dark:text-white">Tax</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-neutral-900 dark:text-white">Diskon</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-neutral-900 dark:text-white">Total</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-neutral-900 dark:text-white">Profit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($purchaseHistory as $order)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700">
                        <td class="px-6 py-3 text-sm text-neutral-900 dark:text-white">{{ $order['time'] }}</td>
                        <td class="px-6 py-3 text-sm font-medium text-neutral-900 dark:text-white">{{ $order['customer'] }}</td>
                        <td class="px-6 py-3 text-sm text-neutral-600 dark:text-neutral-300">{{ Str::limit($order['items'], 30) }}</td>
                        <td class="px-6 py-3 text-right text-sm text-neutral-900 dark:text-white">Rp {{ number_format($order['subtotal'], 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-right text-sm text-orange-600 dark:text-orange-400">Rp {{ number_format($order['tax'], 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-right text-sm text-red-600 dark:text-red-400">Rp {{ number_format($order['discount'], 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-right text-sm font-semibold text-green-600 dark:text-green-400">Rp {{ number_format($order['total'], 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-right text-sm font-semibold text-blue-600 dark:text-blue-400">Rp {{ number_format($order['profit'], 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-neutral-500 dark:text-neutral-400">
                            📭 Belum ada transaksi untuk tanggal ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

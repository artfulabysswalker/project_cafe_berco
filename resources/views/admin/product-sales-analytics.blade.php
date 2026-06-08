<x-layouts::app :title="__('Penjualan Produk & Analytics - Admin')">
    <div class="mx-auto max-w-7xl py-8">
        <!-- Navigation Breadcrumb -->
        <div class="mb-6 flex items-center justify-between">
            <nav class="flex items-center gap-2 text-sm text-neutral-600 dark:text-neutral-400">
                <a href="{{ route('control.dashboard') }}" class="hover:text-neutral-900 dark:hover:text-white">Admin</a>
                <span>/</span>
                <span class="text-neutral-900 dark:text-white">Penjualan Produk</span>
            </nav>
        </div>

        <!-- Livewire Component -->
        @livewire('product-sales-analytics')
    </div>

    @push('scripts')
    <!-- Chart.js for charts if needed -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush
</x-layouts::app>

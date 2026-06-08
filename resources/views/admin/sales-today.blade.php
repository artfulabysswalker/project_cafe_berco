<x-layouts::app :title="__('Penjualan Hari Ini - Admin')">
    <div class="mx-auto max-w-7xl py-8">
        <!-- Navigation Breadcrumb -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <nav class="flex items-center gap-2 text-sm text-neutral-600 dark:text-neutral-400">
                    <a href="{{ route('control.dashboard') }}" class="hover:text-neutral-900 dark:hover:text-white">Admin</a>
                    <span>/</span>
                    <span class="text-neutral-900 dark:text-white">Penjualan Hari Ini</span>
                </nav>
            </div>
        </div>

        <!-- Livewire Component -->
        @livewire('sales-today')
    </div>

    @push('scripts')
    <script>
        // Optional: Add any JavaScript if needed
    </script>
    @endpush
</x-layouts::app>

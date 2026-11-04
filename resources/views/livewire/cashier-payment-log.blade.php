<div class="space-y-4" wire:poll.5s="poll">
    <style>
        @keyframes qris-log-in {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .qris-log-row { animation: qris-log-in .5s ease-out; }
    </style>

    {{-- Listener websocket (opsional): hanya aktif bila Laravel Echo tersedia.
         Polling di atas tetap menjadi cadangan utama. --}}
    <script>
        (() => {
            // Cegah pemasangan listener ganda bila script dievaluasi ulang.
            if (window.__bercoQrisEchoInit) return;
            window.__bercoQrisEchoInit = true;

            const bind = () => {
                if (window.__bercoQrisEchoBound) return;
                if (typeof window.Echo === 'undefined') return;

                window.__bercoQrisEchoBound = true;

                try {
                    window.Echo.private('cashier.dashboard')
                        .listen('PaymentReceived', () => window.Livewire.dispatch('qris-payment-received'))
                        .listen('.PaymentReceived', () => window.Livewire.dispatch('qris-payment-received'));
                } catch (e) {
                    window.__bercoQrisEchoBound = false;
                    console.warn('Echo listener initialization warning:', e);
                }
            };

            bind();
            document.addEventListener('livewire:initialized', bind);
            document.addEventListener('DOMContentLoaded', bind);
        })();
    </script>

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h3 class="text-sm font-bold font-mono uppercase tracking-wider text-stone-800">
                Log Transfer QRIS
            </h3>
            <p class="text-[11px] text-stone-400 mt-0.5">
                Pembayaran masuk tercatat otomatis dari Midtrans
            </p>
        </div>
        <div class="text-right">
            <p class="text-[10px] font-mono uppercase tracking-wider text-stone-400">Total Masuk Hari Ini</p>
            <p class="text-lg font-bold font-mono text-emerald-600">
                Rp {{ number_format((float) $paidToday, 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="flex flex-wrap items-center gap-2">
        {{-- Dropdown status --}}
        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
            <button
                type="button"
                @click="open = ! open"
                class="inline-flex items-center gap-2 rounded-lg border border-stone-200 bg-white px-3 py-2 text-xs font-semibold text-stone-600 hover:bg-stone-50 transition"
            >
                {{ $statuses[$status] ?? 'Semua Status' }}
                <i class="fas fa-chevron-down text-[10px] text-stone-400" :class="open ? 'rotate-180' : ''"></i>
            </button>
            <div
                x-show="open"
                x-transition.origin.top
                x-cloak
                class="absolute left-0 z-20 mt-1 w-48 overflow-hidden rounded-lg border border-stone-200 bg-white shadow-lg"
            >
                @foreach ($statuses as $value => $label)
                    <button
                        type="button"
                        wire:click="$set('status', '{{ $value }}')"
                        @click="open = false"
                        class="block w-full px-3 py-2 text-left text-xs transition {{ $status === $value ? 'bg-stone-100 font-semibold text-stone-900' : 'text-stone-600 hover:bg-stone-50' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Filter tanggal --}}
        <input
            type="date"
            wire:model.live="date"
            class="rounded-lg border-stone-200 text-xs text-stone-600 focus:border-terracotta focus:ring-terracotta/30"
        >
        @if ($date || $status !== 'all')
            <button
                type="button"
                wire:click="$set('status', 'all'); $set('date', null)"
                class="text-xs font-semibold text-terracotta hover:text-terracotta-dark transition"
            >
                Reset filter
            </button>
        @endif
    </div>

    {{-- Tabel log --}}
    <div class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-2xs">
        <table class="min-w-full divide-y divide-stone-100 text-xs">
            <thead class="bg-stone-50">
                <tr class="text-left text-[10px] uppercase tracking-wider text-stone-400">
                    <th class="px-4 py-3 font-semibold">Waktu</th>
                    <th class="px-4 py-3 font-semibold">Pesanan</th>
                    <th class="px-4 py-3 font-semibold text-right">Total</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                    <th class="px-4 py-3 font-semibold">Transaksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($logs as $log)
                    @php $isNew = in_array((int) $log->id, $highlightedIds, true); @endphp
                    <tr
                        wire:key="qris-log-{{ $log->id }}"
                        class="{{ $isNew ? 'qris-log-row bg-amber-50/70' : '' }} {{ $log->isPaid() ? 'bg-emerald-50/40' : '' }} hover:bg-stone-50 transition-colors"
                    >
                        <td class="px-4 py-3 font-mono text-stone-500 whitespace-nowrap">
                            {{ $log->created_at?->format('d/m H:i:s') }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-mono font-semibold text-stone-800">
                                {{ $log->order?->public_code ?? '-' }}
                            </span>
                            <span class="block text-[11px] text-stone-400">
                                {{ $log->order?->customer_name ?? $log->order?->nama_pelanggan ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-mono font-semibold text-stone-800 whitespace-nowrap">
                            Rp {{ number_format($log->gross_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-semibold {{ $log->statusBadgeClass() }}">
                                {{ $log->statusLabel() }}
                            </span>
                            <span class="block text-[10px] text-stone-400 mt-1">
                                {{ $log->paid_at?->format('d/m H:i:s') ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-mono text-[11px] text-stone-500 break-all">
                            {{ $log->transaction_id ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-stone-400">
                            <i class="fas fa-inbox mb-2 block text-xl"></i>
                            Belum ada transaksi QRIS.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="text-stone-400">
        {{ $logs->links() }}
    </div>
</div>

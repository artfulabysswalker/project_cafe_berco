@extends('Customerviews.layouts.web')

@section('title', 'Lacak Pesanan - Berco Cafe')

@section('content')
<div class="min-h-[calc(100vh-4rem)] bg-stone-100">
    <main class="container" style="max-width: 620px; margin: 0 auto; padding: 40px 16px;">
        <div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
            <div class="px-6 py-8 text-center border-b border-stone-100 bg-stone-50">
                <h1 class="text-2xl font-serif font-semibold text-stone-900">
                    Status Pesanan
                </h1>
                <p class="mt-1 font-mono text-lg font-bold text-amber-700">{{ $order->public_code }}</p>
                <p class="mt-1 text-sm text-stone-500">
                    Total <strong>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</strong>
                    @if($order->table) · {{ $order->table->nama_meja }} @endif
                </p>

                <div id="live-pill" class="mt-4 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-sm font-semibold {{ $order->status_order === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($order->status_order === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-800') }}">
                    <span id="live-dot" class="h-2 w-2 rounded-full {{ $order->status_order === 'cancelled' ? 'bg-red-500' : 'bg-emerald-500' }}"></span>
                    <span id="live-label">{{ $order->statusLabel() }}</span>
                </div>
            </div>

            <div class="px-6 py-8">
                @if($order->status_order === 'cancelled')
                    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-4 text-center text-sm text-red-700">
                        Pesanan ini dibatalkan. Silakan hubungi kasir jika ini tidak disengaja.
                    </div>
                @else
                    @php
                        $steps = $order->statusSteps();
                        $currentKey = in_array($order->status_order, ['pending', 'confirmed'], true)
                            ? 'pending'
                            : $order->status_order;
                        $currentIndex = collect($steps)->search(fn ($s) => $s['key'] === $currentKey);
                        $currentIndex = $currentIndex === false ? 0 : $currentIndex;
                    @endphp

                    <ol id="timeline" class="relative space-y-8">
                        @foreach($steps as $i => $step)
                            @php $done = $i <= $currentIndex; @endphp
                            <li class="relative flex gap-4">
                                @if(!$loop->last)
                                    <span class="absolute left-[15px] top-9 bottom-[-26px] w-0.5 {{ $done && !$loop->last ? 'bg-emerald-500' : 'bg-stone-200' }}" aria-hidden="true"></span>
                                @endif
                                <span class="relative z-10 flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-base font-bold {{ $done ? 'bg-emerald-600 text-white' : 'bg-stone-200 text-stone-400' }}">
                                    {!! $done ? '<i class="fas fa-check"></i>' : $i + 1 !!}
                                </span>
                                <div class="pt-0.5">
                                    <p class="font-semibold {{ $done ? 'text-stone-900' : 'text-stone-400' }}">{{ $step['label'] }}</p>
                                    <p class="text-xs text-stone-500">{{ $step['desc'] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif

                <div class="mt-8 rounded-lg border border-stone-200 bg-stone-50 px-4 py-3">
                    <h2 class="text-sm font-semibold text-stone-700">Detail Pesanan</h2>
                    <ul class="mt-2 divide-y divide-stone-100 text-sm text-stone-600">
                        @foreach($order->items as $item)
                            <li class="flex items-start justify-between gap-3 py-2">
                                <div>
                                    <span class="font-semibold text-stone-800">{{ $item->quantity }}x</span>
                                    {{ $item->menu?->nama_menu ?? 'Item' }}
                                    @if($item->notes)
                                        <div class="mt-0.5 text-xs text-amber-700">📝 {{ $item->notes }}</div>
                                    @endif
                                </div>
                                <span class="shrink-0">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="mt-6">
                    <a href="{{ route('menu.index', $order->table ? ['meja' => $order->table->nama_meja] : []) }}"
                       class="block w-full rounded-lg border border-stone-200 bg-white px-4 py-3 text-center text-sm font-semibold text-stone-600 hover:bg-stone-50 transition-colors">
                        <i class="fas fa-utensils mr-1"></i> Pesan Lagi
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
(function () {
    const statusUrl = @json(route('order.status', $order->id_order));
    const stepsOrder = @json(collect($order->statusSteps())->pluck('key')->values());

    function refresh() {
        fetch(statusUrl, { headers: { 'Accept': 'application/json' } })
            .then(r => r.ok ? r.json() : null)
            .then(data => {
                if (!data) return;
                const labelEl = document.getElementById('live-label');
                const pillEl = document.getElementById('live-pill');
                const dotEl = document.getElementById('live-dot');

                labelEl.textContent = data.status_label;

                if (data.status_order === 'cancelled') {
                    pillEl.className = 'mt-4 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-sm font-semibold bg-red-100 text-red-700';
                    dotEl.className = 'h-2 w-2 rounded-full bg-red-500';
                    return;
                }

                if (data.status_order === 'completed') {
                    pillEl.className = 'mt-4 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-sm font-semibold bg-emerald-100 text-emerald-700';
                    dotEl.className = 'h-2 w-2 rounded-full bg-emerald-500';
                    return;
                }

                const idx = stepsOrder.indexOf(['pending', 'confirmed'].includes(data.status_order) ? 'pending' : data.status_order);
                document.querySelectorAll('#timeline li').forEach((li, i) => {
                    const circle = li.querySelector('span.relative');
                    const line = li.querySelector('span.absolute');
                    const isDone = idx >= i;
                    circle.className = 'relative z-10 flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-base font-bold ' + (isDone ? 'bg-emerald-600 text-white' : 'bg-stone-200 text-stone-400');
                    circle.innerHTML = isDone ? '<i class="fas fa-check"></i>' : (i + 1);
                    if (line) { line.classList.toggle('bg-emerald-500', isDone); line.classList.toggle('bg-stone-200', !isDone); }
                });
            })
            .catch(() => {});
    }

    setInterval(refresh, 5000);
})();
</script>
@endsection
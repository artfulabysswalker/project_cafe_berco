@extends('dashboard')

@section('page-title', 'Kitchen Display')
@section('breadcrumb', 'KDS')

@section('content')

<div class="px-6 py-8">
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <span class="text-4xl">👨‍🍳</span>
            <h1 class="text-3xl font-bold text-amber-900">Kitchen Display</h1>
        </div>
        <p class="text-gray-600">Pesanan terbayar yang sedang di dapur / diantar — dikelompokkan per stasiun produksi.</p>
    </div>

    @if(empty($groups))
        <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg border-2 border-dashed border-amber-300 p-12 text-center">
            <div class="text-5xl mb-4">🍳</div>
            <h3 class="text-xl font-semibold text-amber-900 mb-2">Dapur Sedang Tenang</h3>
            <p class="text-amber-700">Tidak ada pesanan yang sedang diproses saat ini.</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-6">
        @foreach($groups as $group)
            <div class="rounded-xl border border-amber-200 bg-white shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-amber-100 to-amber-50 px-4 py-3 border-b border-amber-200 flex items-center justify-between">
                    <h2 class="font-semibold text-amber-900 text-sm">🍳 {{ $group['station'] }}</h2>
                    <span class="text-xs bg-white rounded-full px-3 py-1 text-amber-700 border border-amber-200">
                        {{ count($group['entries']) }} antrean
                    </span>
                </div>

                <div class="divide-y divide-amber-100">
                    @foreach($group['entries'] as $entry)
                        @php $order = $entry['order']; @endphp
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <span class="font-mono font-bold text-amber-700">{{ $order->public_code }}</span>
                                <span class="text-xs text-gray-500">
                                    {{ $order->tanggal->format('H:i') }}
                                    @if($order->table) · {{ $order->table->nama_meja }} @endif
                                </span>
                            </div>

                            <ul class="mt-2 space-y-1">
                                @foreach($entry['items'] as $item)
                                    <li class="text-sm text-gray-700">
                                        <span class="font-semibold">{{ $item->quantity }}x</span>
                                        {{ $item->menu?->nama_menu ?? 'Item' }}
                                        @if($item->notes)
                                            <span class="block mt-0.5 text-xs text-amber-700">📝 {{ $item->notes }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>

                            <div class="mt-3 flex items-center gap-2">
                                @if($order->status_order === 'processing')
                                    <form method="POST" action="{{ route('admin.orders.ready', $order->id_order) }}" class="flex-1">
                                        @csrf
                                        <button class="w-full rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">
                                            ✓ Tandai Siap
                                        </button>
                                    </form>
                                @elseif($order->status_order === 'ready')
                                    <form method="POST" action="{{ route('order.finish', $order->id_order) }}" class="flex-1">
                                        @csrf
                                        <button class="w-full rounded-lg bg-amber-600 px-3 py-2 text-xs font-semibold text-white hover:bg-amber-700">
                                            🎯 Tandai Selesai / Antar
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('admin.orders.tickets', $order->id_order) }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-1 rounded-lg border border-amber-300 px-3 py-2 text-xs font-semibold text-amber-700 hover:bg-amber-50">
                                    🖨 Tiket
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
    // Auto-refresh KDS setiap 8 detik supaya dapur selalu melihat pesanan baru.
    setInterval(() => { window.location.reload(); }, 8000);
</script>
@endpush

@endsection
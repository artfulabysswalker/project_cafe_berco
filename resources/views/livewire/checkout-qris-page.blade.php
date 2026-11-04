<div class="space-y-5" @if (! $this->isPaid) wire:poll.5s="pollStatus" @endif>
    {{-- Header --}}
    <div class="text-center">
        <p class="text-xs uppercase tracking-widest text-ink-muted">Pembayaran QRIS</p>
        <h1 class="mt-1 font-serif text-2xl font-semibold text-ink">Berco Cafe</h1>
        <p class="mt-1 font-mono text-sm font-bold text-terracotta">
            {{ $order->public_code }}
        </p>
    </div>

    @if ($errorMessage)
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold">QRIS gagal dibuat</p>
            <p class="mt-1 text-xs leading-relaxed">{{ $errorMessage }}</p>
            <button
                type="button"
                wire:click="regenerateQr"
                wire:loading.attr="disabled"
                class="mt-3 rounded-lg bg-red-600 px-4 py-2 text-xs font-semibold text-white hover:bg-red-700 transition disabled:opacity-60"
            >
                Coba Lagi
            </button>
        </div>
    @endif

    @if ($this->isPaid)
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-6 py-8 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-600 text-white text-2xl shadow-lg">
                <i class="fas fa-check"></i>
            </div>
            <h2 class="mt-4 font-serif text-xl font-semibold text-emerald-800">Pembayaran Berhasil</h2>
            <p class="mt-1 text-sm text-emerald-700">
                Terima kasih! Pesanan Anda sedang disiapkan.
            </p>
            <a
                href="{{ route('order.show', $order->id_order) }}"
                class="mt-5 inline-block rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition"
            >
                Lacak Pesanan
            </a>
        </div>
    @elseif ($this->isExpired)
        <div class="rounded-xl border border-stone-200 bg-white px-6 py-8 text-center shadow-sm">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-stone-100 text-stone-500 text-2xl">
                <i class="fas fa-clock"></i>
            </div>
            <h2 class="mt-4 font-serif text-xl font-semibold text-stone-800">QRIS Kedaluwarsa</h2>
            <p class="mt-1 text-sm text-stone-500">Silakan buat kode QR baru untuk membayar.</p>
            <button
                type="button"
                wire:click="regenerateQr"
                wire:loading.attr="disabled"
                class="mt-5 rounded-lg bg-terracotta px-5 py-2.5 text-sm font-semibold text-white hover:bg-terracotta-dark transition disabled:opacity-60"
            >
                Buat QR Baru
            </button>
        </div>
    @elseif ($paymentLog && $paymentLog->qr_code_url)
        <div
            x-data="{
                seconds: {{ $expiresInSeconds }},
                timer: null,
                start() {
                    if (this.seconds === 0) return;
                    this.timer = setInterval(() => {
                        this.seconds = Math.max(0, this.seconds - 1);
                        if (this.seconds === 0) clearInterval(this.timer);
                    }, 1000);
                },
                get countdown() {
                    const m = String(Math.floor(this.seconds / 60)).padStart(2, '0');
                    const s = String(this.seconds % 60).padStart(2, '0');
                    return m + ':' + s;
                }
            }"
            x-init="start()"
            class="rounded-xl border border-stone-200 bg-white p-6 shadow-sm text-center"
        >
            <p class="text-xs uppercase tracking-widest text-ink-muted">Total Tagihan</p>
            <p class="mt-1 font-mono text-3xl font-bold text-ink">
                Rp {{ number_format($this->total, 0, ',', '.') }}
            </p>

            <div class="mt-5 inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                <span class="relative flex h-2 w-2">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex h-2 w-2 rounded-full bg-amber-500"></span>
                </span>
                {{ $paymentLog->statusLabel() }}
            </div>

            <div class="mt-5 flex justify-center">
                <div class="rounded-xl border border-stone-200 bg-white p-3 shadow-sm">
                    <img
                        src="{{ $paymentLog->qr_code_url }}"
                        alt="QRIS Berco Cafe"
                        class="h-64 w-64 object-contain"
                        wire:key="qris-{{ $paymentLog->id }}"
                    >
                </div>
            </div>

            <p class="mt-5 text-xs text-ink-muted">Berlaku lagi dalam</p>
            <p
                class="font-mono text-2xl font-bold"
                :class="seconds > 60 ? 'text-ink' : 'text-red-600'"
                x-text="countdown"
            >{{ sprintf('%02d:%02d', intdiv($expiresInSeconds, 60), $expiresInSeconds % 60) }}</p>

            <button
                type="button"
                wire:click="refreshStatus"
                wire:loading.attr="disabled"
                class="mt-4 rounded-lg border border-stone-300 px-5 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-50 transition disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="refreshStatus">Cek Status Pembayaran</span>
                <span wire:loading wire:target="refreshStatus">Mengecek…</span>
            </button>

            <ol class="mt-6 space-y-2 text-left text-xs leading-relaxed text-ink-muted">
                <li>1. Buka aplikasi e-wallet / mobile banking Anda.</li>
                <li>2. Pilih fitur <strong>Scan QR</strong> / QRIS.</li>
                <li>3. Arahkan kamera ke kode QR di atas.</li>
                <li>4. Pastikan nominal sesuai, lalu konfirmasi pembayaran.</li>
            </ol>
        </div>
    @endif

    <div class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-xs text-ink-muted">
        <p class="font-semibold text-stone-600">Rincian Pesanan</p>
        <ul class="mt-2 space-y-1">
            @foreach ($order->orderItems as $item)
                <li class="flex justify-between gap-3">
                    <span>{{ $item->menu?->nama_menu }} × {{ $item->quantity }}</span>
                    <span class="font-mono">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</span>
                </li>
            @endforeach
            @if ((float) $order->service_charge > 0)
                <li class="flex justify-between gap-3">
                    <span>Biaya Take-Away</span>
                    <span class="font-mono">Rp {{ number_format((float) $order->service_charge, 0, ',', '.') }}</span>
                </li>
            @endif
            @if ((float) $order->discount_amount > 0)
                <li class="flex justify-between gap-3 text-emerald-700">
                    <span>Diskon</span>
                    <span class="font-mono">-Rp {{ number_format((float) $order->discount_amount, 0, ',', '.') }}</span>
                </li>
            @endif
            <li class="flex justify-between gap-3 border-t border-stone-200 pt-2 font-semibold text-stone-700">
                <span>Total</span>
                <span class="font-mono">Rp {{ number_format($this->total, 0, ',', '.') }}</span>
            </li>
        </ul>
    </div>
</div>

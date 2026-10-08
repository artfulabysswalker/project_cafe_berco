@extends('dashboard')

@section('content')
<div class="mx-auto max-w-4xl space-y-8 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">QR Code — {{ $table->nama_meja }}</h1>
            <p class="mt-2 text-neutral-600 dark:text-neutral-400">
                ID Meja: {{ $table->id_meja }} · Kapasitas: {{ $table->kapasitas ?? '-' }} · Area: {{ $table->area ?? '-' }}
            </p>
        </div>
        <a href="{{ route('admin.tables.index') }}" class="rounded-lg border border-neutral-300 bg-white px-4 py-2 font-semibold text-neutral-700 hover:bg-neutral-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700">
            ← Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-lg border-l-4 border-green-500 bg-green-50 p-4 text-green-800 dark:bg-green-900/20 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-lg border-l-4 border-red-500 bg-red-50 p-4 text-red-800 dark:bg-red-900/20 dark:text-red-200">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Payload (untuk verifikasi keamanan) -->
    @if($qr)
        <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <h2 class="mb-1 text-lg font-semibold text-neutral-900 dark:text-white">Isi QR (Payload)</h2>
            <p class="mb-4 text-xs text-neutral-500 dark:text-neutral-400">
                Yang dipindai customer adalah {{ config('qrcode.encode_as_signed_url') ? 'signed URL (HMAC + expiry)' : 'payload terenkripsi (AES)' }} —
                ID meja tidak pernah muncul polos dan tidak bisa diedit tanpa membatalkan signature.
            </p>

            <div class="rounded-lg bg-neutral-100 p-4 dark:bg-neutral-800">
                <code class="block break-all font-mono text-xs text-neutral-800 dark:text-neutral-200" id="qr-payload">{{ $payload }}</code>
            </div>

            <div class="mt-3 flex flex-wrap gap-2">
                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('qr-payload').textContent)" class="rounded-lg border border-neutral-300 bg-white px-3 py-1.5 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                    Salin Payload
                </button>
                <a href="{{ $scanUrl }}" target="_blank" class="rounded-lg border border-neutral-300 bg-white px-3 py-1.5 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                    Buka Halaman Scan
                </a>
            </div>
        </div>
    @endif

    <div class="grid gap-6 md:grid-cols-2">
        <!-- QR Preview -->
        <div class="rounded-lg border border-neutral-200 bg-white p-6 text-center dark:border-neutral-700 dark:bg-neutral-900">
            <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-white">Preview QR Code</h2>

            @if($qr && !$qr->isExpired())
                @if($imageExists)
                    <img src="{{ route('admin.tables.qrcode.image', $table->id_meja) }}" alt="QR Code {{ $table->nama_meja }}" class="mx-auto h-64 w-64 rounded-lg border border-neutral-200 bg-white p-2 dark:border-neutral-600">
                    <p class="mt-3 text-xs text-neutral-500 dark:text-neutral-400">Token: <span class="font-mono">{{ $qr->token }}</span></p>
                @else
                    <div class="mx-auto flex h-64 w-64 flex-col items-center justify-center rounded-lg border border-dashed border-neutral-300 bg-neutral-50 text-neutral-400 dark:border-neutral-600 dark:bg-neutral-800">
                        <span class="text-4xl">⚠️</span>
                        <p class="mt-2 text-sm">File QR hilang</p>
                        <p class="text-xs">Silakan regenerate</p>
                    </div>
                @endif
            @else
                <div class="mx-auto flex h-64 w-64 flex-col items-center justify-center rounded-lg border border-dashed border-neutral-300 bg-neutral-50 text-neutral-400 dark:border-neutral-600 dark:bg-neutral-800">
                    <span class="text-4xl">📭</span>
                    <p class="mt-2 text-sm">QR belum ada / expired</p>
                </div>
            @endif
        </div>

        <!-- Details & Actions -->
        <div class="space-y-4">
            <!-- Status -->
            <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-white">Status QR</h2>

                @if(! $qr)
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Belum ada QR Code yang pernah digenerate untuk meja ini.</p>
                @else
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-neutral-500 dark:text-neutral-400">Status</dt>
                            <dd>
                                @if($qr->isExpired())
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-900 dark:text-red-200">Expired</span>
                                @elseif($qr->is_active)
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700 dark:bg-green-900 dark:text-green-200">Aktif</span>
                                @else
                                    <span class="rounded-full bg-neutral-100 px-3 py-1 text-xs font-semibold text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300">Revoked</span>
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-500 dark:text-neutral-400">Version</dt>
                            <dd class="font-mono text-neutral-900 dark:text-white">{{ $qr->version }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-500 dark:text-neutral-400">Token</dt>
                            <dd class="font-mono text-xs text-neutral-900 dark:text-white">{{ $qr->token }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-500 dark:text-neutral-400">Dibuat</dt>
                            <dd class="text-neutral-900 dark:text-white">{{ $qr->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-500 dark:text-neutral-400">Expired At</dt>
                            <dd class="text-neutral-900 dark:text-white">
                                {{ $qr->expired_at?->format('d/m/Y H:i') ?? 'Tidak ada expiry' }}
                            </dd>
                        </div>
                        @if($qr->isExpired())
                            <div class="flex justify-between">
                                <dt class="text-neutral-500 dark:text-neutral-400">Sisa Waktu</dt>
                                <dd class="text-red-600 dark:text-red-400 font-semibold">Expired</dd>
                            </div>
                        @elseif($qr->expired_at)
                            <div class="flex justify-between">
                                <dt class="text-neutral-500 dark:text-neutral-400">Sisa Waktu</dt>
                                <dd class="text-green-600 dark:text-green-400 font-semibold">{{ now()->diffForHumans($qr->expired_at, true) }}</dd>
                            </div>
                        @endif
                    </dl>
                @endif
            </div>

            <!-- Generate -->
            <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-white">Generate / Regenerate</h2>
                <form method="POST" action="{{ route('admin.tables.qrcode.generate', $table->id_meja) }}">
                    @csrf
                    <label for="expires_in_hours" class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Masa berlaku (jam)</label>
                    <input type="number" name="expires_in_hours" id="expires_in_hours" value="{{ old('expires_in_hours', config('qrcode.default_expires_in_hours', 24)) }}" min="0" max="720" class="w-full rounded-lg border border-neutral-300 px-4 py-2 text-neutral-900 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
                    <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">0 = tanpa expiry (permanen). Default: {{ config('qrcode.default_expires_in_hours', 24) }} jam</p>
                    <button type="submit" class="mt-4 w-full rounded-lg bg-blue-600 px-4 py-3 font-semibold text-white hover:bg-blue-700">
                        {{ $qr ? 'Regenerate (Revoke Lama)' : 'Generate QR Code' }}
                    </button>
                </form>
            </div>

            <!-- Revoke & Download -->
            @if($qr)
                <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                    <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-white">Aksi Lainnya</h2>
                    <div class="space-y-3">
                        <a href="{{ route('admin.tables.qrcode.download', $table->id_meja) }}" class="block w-full rounded-lg bg-green-600 px-4 py-3 text-center font-semibold text-white hover:bg-green-700">
                            ⬇ Download PNG (untuk dicetak)
                        </a>

                        @if($qr->is_active && ! $qr->isExpired())
                            <form method="POST" action="{{ route('admin.tables.qrcode.revoke', $table->id_meja) }}" onsubmit="return confirm('Yakin ingin revoke QR Code ini?')">
                                @csrf
                                <button type="submit" class="w-full rounded-lg bg-red-600 px-4 py-3 font-semibold text-white hover:bg-red-700">
                                    Revoke QR Code
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

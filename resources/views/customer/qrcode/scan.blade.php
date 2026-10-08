<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meja {{ $table?->nama_meja }} — Cafe Berco</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-50">
    <div class="mx-auto max-w-md px-4 py-12">
        <div class="rounded-lg border border-stone-200 bg-white p-6 text-center shadow-sm">
            @if($table)
                <p class="text-sm text-stone-500">Selamat datang di</p>
                <h1 class="mt-1 text-3xl font-bold text-stone-900">Cafe Berco</h1>
                <p class="mt-4 text-lg text-stone-700">Meja <span class="font-bold">{{ $table->nama_meja }}</span></p>
                @if($table->kapasitas)
                    <p class="text-sm text-stone-500">Kapasitas {{ $table->kapasitas }} orang</p>
                @endif
                @if($qr->expired_at)
                    <p class="mt-3 text-xs text-stone-400">Berlaku sampai {{ $qr->expired_at->format('d/m/Y H:i') }}</p>
                @endif

                <a href="{{ route('menu.index', ['meja' => $table->nama_meja]) }}" class="mt-6 block w-full rounded-lg bg-[#C27835] px-4 py-3 font-semibold text-white hover:bg-[#a8622b]">
                    Lihat Menu & Pesan
                </a>
            @else
                <p class="text-lg font-semibold text-red-600">Meja tidak ditemukan</p>
            @endif
        </div>
    </div>
</body>
</html>

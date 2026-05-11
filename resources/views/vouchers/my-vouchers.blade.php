@php
    $user = auth()->user();
@endphp

<x-layouts::app :title="__('Voucher Saya')">
    <div class="mx-auto max-w-6xl space-y-8 py-8">
        <!-- Header Section -->
        <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">💳 Voucher & Promo Saya</h1>
            <p class="mt-2 text-neutral-600 dark:text-neutral-400">Kumpulan voucher eksklusif yang sudah Anda dapatkan. Gunakan sebelum tanggal expired!</p>
        </div>

        @if($vouchers->isEmpty())
            <div class="rounded-lg border border-neutral-200 bg-white p-12 text-center dark:border-neutral-700 dark:bg-neutral-900">
                <div class="mb-4 text-5xl">🎟️</div>
                <p class="text-lg font-medium text-neutral-900 dark:text-white">Belum Ada Voucher</p>
                <p class="mt-2 text-neutral-600 dark:text-neutral-400">Anda belum memiliki voucher. Lakukan pembelian atau tunggu penawaran khusus dari kami!</p>
                <a href="{{ route('menu.index') }}" class="mt-4 inline-block rounded-lg bg-blue-600 px-6 py-2 text-white hover:bg-blue-700">
                    🛍️ Lihat Menu
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($vouchers as $voucher)
                    <div class="group rounded-lg border-2 border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100 p-6 dark:border-blue-900 dark:from-blue-950 dark:to-blue-900">
                        <!-- Voucher Header -->
                        <div class="mb-4 flex items-start justify-between">
                            <div>
                                <p class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $voucher->type === 'comeback' ? '🎉 COMEBACK' : ($voucher->type === 'welcome' ? '👋 WELCOME' : ($voucher->type === 'referral' ? '🤝 REFERRAL' : '🎊 PROMO')) }}</p>
                                <h3 class="text-lg font-bold text-neutral-900 dark:text-white">{{ $voucher->name }}</h3>
                            </div>
                        </div>

                        <!-- Discount Display -->
                        <div class="mb-4 rounded-lg bg-white p-4 dark:bg-neutral-800">
                            <div class="text-center">
                                @if($voucher->discount_percentage)
                                    <p class="text-4xl font-bold text-blue-600 dark:text-blue-400">{{ $voucher->discount_percentage }}%</p>
                                    <p class="text-xs text-neutral-600 dark:text-neutral-400">
                                        @if($voucher->max_discount)
                                            Maks Rp {{ number_format($voucher->max_discount, 0, ',', '.') }}
                                        @endif
                                    </p>
                                @else
                                    <p class="text-4xl font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($voucher->discount_amount, 0, ',', '.') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Voucher Details -->
                        <div class="mb-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-neutral-600 dark:text-neutral-400">Kode Voucher:</span>
                                <code class="rounded bg-neutral-200 px-2 py-1 font-mono text-sm font-bold dark:bg-neutral-700">{{ $voucher->code }}</code>
                            </div>
                            @if($voucher->min_purchase > 0)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-neutral-600 dark:text-neutral-400">Min. Pembelian:</span>
                                    <span class="text-sm font-semibold text-neutral-900 dark:text-white">Rp {{ number_format($voucher->min_purchase, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-neutral-600 dark:text-neutral-400">Berlaku Hingga:</span>
                                <span class="text-sm font-semibold text-neutral-900 dark:text-white">{{ $voucher->valid_until->format('d M Y') }}</span>
                            </div>
                        </div>

                        <!-- Description -->
                        @if($voucher->description)
                            <p class="mb-4 text-xs text-neutral-600 dark:text-neutral-400">{{ $voucher->description }}</p>
                        @endif

                        <!-- Warning if expiring soon -->
                        @if($voucher->valid_until->diffInDays(now()) <= 7)
                            <div class="mb-4 rounded-lg bg-red-100 p-2 text-center dark:bg-red-900">
                                <p class="text-xs font-semibold text-red-600 dark:text-red-400">
                                    ⏰ Berakhir dalam {{ $voucher->valid_until->diffInDays(now()) }} hari
                                </p>
                            </div>
                        @endif

                        <!-- Use Button -->
                        <a href="{{ route('menu.index') }}" class="block w-full rounded-lg bg-blue-600 py-2 text-center font-semibold text-white transition hover:bg-blue-700">
                            Gunakan Sekarang
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts::app>

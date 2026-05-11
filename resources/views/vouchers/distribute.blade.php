<x-layouts::app :title="__('Distribusi Voucher ke Pelanggan Tidak Aktif - Admin')">
    <div class="mx-auto max-w-2xl py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">📤 Distribusi Voucher</h1>
            <p class="mt-2 text-neutral-600 dark:text-neutral-400">Kirim voucher ke pelanggan yang tidak aktif</p>
        </div>

        <div class="space-y-6">
            <!-- Voucher Info -->
            <div class="rounded-lg border-2 border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100 p-6 dark:border-blue-900 dark:from-blue-950 dark:to-blue-900">
                <h2 class="text-lg font-bold text-neutral-900 dark:text-white">{{ $voucher->name }}</h2>
                <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">{{ $voucher->description }}</p>
                
                <div class="mt-4 grid grid-cols-2 gap-4 md:grid-cols-4">
                    <div>
                        <p class="text-xs text-neutral-600 dark:text-neutral-400">Kode</p>
                        <p class="font-mono font-bold text-blue-600 dark:text-blue-400">{{ $voucher->code }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-neutral-600 dark:text-neutral-400">Diskon</p>
                        <p class="font-bold text-neutral-900 dark:text-white">
                            @if($voucher->discount_percentage)
                                {{ $voucher->discount_percentage }}%
                            @else
                                Rp {{ number_format($voucher->discount_amount, 0, ',', '.') }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-neutral-600 dark:text-neutral-400">Berlaku Hingga</p>
                        <p class="font-bold text-neutral-900 dark:text-white">{{ $voucher->valid_until->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-neutral-600 dark:text-neutral-400">Tipe</p>
                        <p class="font-bold text-neutral-900 dark:text-white">{{ ucfirst($voucher->type) }}</p>
                    </div>
                </div>
            </div>

            <!-- Distribution Form -->
            <form method="POST" action="{{ route('admin.voucher.distribute', $voucher) }}" class="space-y-6 rounded-lg border border-neutral-200 bg-white p-8 dark:border-neutral-700 dark:bg-neutral-900">
                @csrf

                <!-- Info Box -->
                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-900 dark:bg-blue-950">
                    <p class="text-sm text-blue-600 dark:text-blue-400">
                        <strong>ℹ️ Info:</strong> Saat ini ada <strong>{{ $inactiveUsersCount }}</strong> pelanggan yang tidak aktif selama 30 hari atau lebih.
                    </p>
                </div>

                <!-- Inactive Days -->
                <div>
                    <label for="days" class="block text-sm font-medium text-neutral-900 dark:text-white">Pelanggan Tidak Aktif Selama (hari) *</label>
                    <div class="mt-2 flex items-center gap-4">
                        <input type="range" id="days" name="days" min="1" max="365" value="30" class="flex-1" oninput="document.getElementById('daysValue').textContent = this.value">
                        <span class="text-lg font-bold text-blue-600 dark:text-blue-400">
                            <span id="daysValue">30</span> hari
                        </span>
                    </div>
                    <p class="mt-2 text-xs text-neutral-600 dark:text-neutral-400">Voucher akan dikirim ke pelanggan yang tidak aktif selama minimal jumlah hari yang dipilih.</p>
                </div>

                <!-- Send Notification -->
                <div>
                    <label for="send_notification" class="flex items-center gap-3">
                        <input type="checkbox" id="send_notification" name="send_notification" value="1" checked class="h-4 w-4 rounded border-neutral-300">
                        <span class="text-sm font-medium text-neutral-900 dark:text-white">Kirim Notifikasi Email ke Pelanggan</span>
                    </label>
                    <p class="mt-2 text-xs text-neutral-600 dark:text-neutral-400">Pelanggan akan menerima email berisi kode voucher dan cara penggunaannya.</p>
                </div>

                <!-- Preview -->
                <div class="rounded-lg bg-neutral-50 p-4 dark:bg-neutral-800">
                    <p class="mb-3 text-sm font-semibold text-neutral-900 dark:text-white">📧 Preview Email:</p>
                    <div class="space-y-2 border-l-4 border-blue-500 bg-white p-4 dark:bg-neutral-900">
                        <p class="text-xs"><strong>Subject:</strong> 🎉 Promo Eksklusif Menanti Anda! - {{ $voucher->name }}</p>
                        <p class="mt-3 text-xs text-neutral-600 dark:text-neutral-400">Halo [Nama Pelanggan]!</p>
                        <p class="text-xs text-neutral-600 dark:text-neutral-400">Kami merindukan Anda! Sebagai pelanggan setia, kami memberikan voucher promo eksklusif...</p>
                        <p class="mt-2 text-xs text-neutral-600 dark:text-neutral-400"><strong>Kode Voucher:</strong> {{ $voucher->code }}</p>
                    </div>
                </div>

                <!-- Warning -->
                <div class="rounded-lg border border-orange-200 bg-orange-50 p-4 dark:border-orange-900 dark:bg-orange-950">
                    <p class="text-sm text-orange-600 dark:text-orange-400">
                        <strong>⚠️ Perhatian:</strong> Pelanggan yang sudah memiliki voucher ini tidak akan menerima duplikat. Email akan dikirim secara asynchronous.
                    </p>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 rounded-lg bg-green-600 px-6 py-3 font-semibold text-white hover:bg-green-700">
                        ✓ Distribusi Voucher
                    </button>
                    <a href="{{ route('admin.vouchers.index') }}" class="flex-1 rounded-lg border border-neutral-300 px-6 py-3 text-center font-semibold text-neutral-900 hover:bg-neutral-100 dark:border-neutral-600 dark:text-white dark:hover:bg-neutral-800">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>

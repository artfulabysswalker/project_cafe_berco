<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <!-- Welcome Header -->
        <div class="bg-gradient-to-r from-[#78350F] to-[#92400E] text-white rounded-xl p-8 shadow-lg">
            <h1 class="text-4xl font-bold mb-2">Selamat Datang, {{ Auth::user()->name }}! 👋</h1>
            <p class="text-lg text-orange-100">Apa yang ingin Anda pesan hari ini?</p>
        </div>

        <!-- Quick Action Cards -->
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <!-- Order Menu Card -->
            <a href="{{ route('menu.index') }}" class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-gradient-to-br from-orange-50 to-amber-50 hover:shadow-lg hover:scale-105 transition-all cursor-pointer p-6 flex flex-col justify-between">
                <div>
                    <div class="text-4xl mb-2">🍽️</div>
                    <h3 class="font-bold text-xl text-[#78350F]">Pesan Menu</h3>
                    <p class="text-sm text-[#92400E] mt-1">Jelajahi produk kami</p>
                </div>
            </a>

            <!-- My Orders Card -->
            <a href="{{ route('order.history') }}" class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-gradient-to-br from-blue-50 to-cyan-50 hover:shadow-lg hover:scale-105 transition-all cursor-pointer p-6 flex flex-col justify-between">
                <div>
                    <div class="text-4xl mb-2">📦</div>
                    <h3 class="font-bold text-xl text-blue-900">Pesanan Saya</h3>
                    <p class="text-sm text-blue-700 mt-1">Lihat riwayat pesanan</p>
                </div>
            </a>

            <!-- My Cart Card -->
            <a href="{{ route('cart.index') }}" class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-gradient-to-br from-green-50 to-emerald-50 hover:shadow-lg hover:scale-105 transition-all cursor-pointer p-6 flex flex-col justify-between">
                <div>
                    <div class="text-4xl mb-2">🛒</div>
                    <h3 class="font-bold text-xl text-green-900">Keranjang</h3>
                    <p class="text-sm text-green-700 mt-1">Lihat keranjang Anda</p>
                </div>
            </a>
        </div>

        <!-- Rewards & Programs Section -->
        <div class="grid auto-rows-min gap-4 md:grid-cols-2">
            <!-- Referral Card -->
            <a href="{{ route('referral.index') }}" class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-gradient-to-br from-purple-50 to-pink-50 hover:shadow-lg transition-all cursor-pointer p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-3xl mb-2">🎁</div>
                        <h3 class="font-bold text-xl text-purple-900">Program Referral</h3>
                        <p class="text-sm text-purple-700 mt-2">Ajak teman & dapatkan bonus!</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-purple-900">{{ Auth::user()->referral_balance ? 'Rp ' . number_format(Auth::user()->referral_balance) : 'Rp 0' }}</div>
                        <p class="text-xs text-purple-600">Saldo Anda</p>
                    </div>
                </div>
            </a>

            <!-- Achievement Card -->
            <a href="{{ route('achievement.index') }}" class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-gradient-to-br from-yellow-50 to-orange-50 hover:shadow-lg transition-all cursor-pointer p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-3xl mb-2">🏆</div>
                        <h3 class="font-bold text-xl text-orange-900">Pencapaian</h3>
                        <p class="text-sm text-orange-700 mt-2">Raih badge & reward!</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</x-layouts::app>

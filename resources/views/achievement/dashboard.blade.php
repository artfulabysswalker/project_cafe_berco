@php
    $user = auth()->user();
@endphp

<x-layouts::app :title="__('Achievements')">
    <div class="mx-auto max-w-6xl space-y-8 py-8">
        <!-- Header Section -->
        <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">Pencapaian Anda</h1>
            <p class="mt-2 text-neutral-600 dark:text-neutral-400">Raih semua badge dan dapatkan reward eksklusif!</p>
            
            <!-- Progress Bar -->
            <div class="mt-6">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Progress</span>
                    <span class="text-sm font-bold text-neutral-900 dark:text-white">
                        {{ $earnedCount }} / {{ $totalCount }} Badge
                    </span>
                </div>
                <div class="mt-2 h-3 w-full overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-700">
                    <div 
                        class="h-full bg-gradient-to-r from-blue-500 to-blue-600 transition-all duration-300"
                        style="width: {{ ($earnedCount / $totalCount) * 100 }}%"
                    ></div>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-600 dark:text-neutral-400">Total Orders</p>
                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">
                    {{ $user->getCompletedOrdersCount() }}
                </p>
            </div>
            <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-600 dark:text-neutral-400">Total Spent</p>
                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">
                    Rp {{ number_format($user->getTotalSpent(), 0, ',', '.') }}
                </p>
            </div>
            <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-600 dark:text-neutral-400">Successful Referrals</p>
                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">
                    {{ $user->referralsMade()->where('status', 'completed')->count() }}
                </p>
            </div>
            <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-600 dark:text-neutral-400">Badges Earned</p>
                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">
                    {{ $earnedCount }}
                </p>
            </div>
        </div>

        <!-- Achievements Grid -->
        <div>
            <h2 class="mb-6 text-2xl font-bold text-neutral-900 dark:text-white">Semua Pencapaian</h2>
            
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($achievements as $item)
                    @php
                        $achievement = $item['achievement'];
                        $earned = $item['earned'];
                        $earnedAt = $item['earned_at'];
                    @endphp
                    
                    <div class="group relative overflow-hidden rounded-lg border-2 transition-all duration-300
                        @if($earned)
                            border-yellow-400 bg-gradient-to-br from-yellow-50 to-amber-50 dark:border-yellow-500 dark:from-yellow-950 dark:to-amber-950
                        @else
                            border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900
                        @endif
                    ">
                        <!-- Earned Badge -->
                        @if($earned)
                            <div class="absolute right-0 top-0 -mr-2 -mt-2 h-16 w-16 rounded-full bg-yellow-400 opacity-20"></div>
                        @endif

                        <div class="relative p-6">
                            <!-- Icon & Header -->
                            <div class="flex items-start justify-between">
                                <div class="text-5xl">{{ $achievement->icon }}</div>
                                @if($earned)
                                    <span class="inline-block rounded-full bg-yellow-400 px-3 py-1 text-xs font-bold text-yellow-900 dark:bg-yellow-500 dark:text-yellow-950">
                                        ✓ Earned
                                    </span>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="mt-4">
                                <h3 class="text-lg font-bold text-neutral-900 dark:text-white">
                                    {{ $achievement->name }}
                                </h3>
                                <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">
                                    {{ $achievement->description }}
                                </p>

                                <!-- Threshold Info -->
                                <div class="mt-4 rounded-lg bg-neutral-100 p-3 dark:bg-neutral-800">
                                    <p class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">
                                        @switch($achievement->type)
                                            @case('orders_count')
                                                Target: {{ $achievement->threshold }} orders
                                                @break
                                            @case('total_spent')
                                                Target: Rp {{ number_format($achievement->threshold, 0, ',', '.') }}
                                                @break
                                            @case('referrals_count')
                                                Target: {{ $achievement->threshold }} referrals
                                                @break
                                        @endswitch
                                    </p>
                                </div>

                                <!-- Reward -->
                                <div class="mt-3 flex items-center gap-2">
                                    <span class="text-xs text-neutral-600 dark:text-neutral-400">Reward:</span>
                                    <span class="font-bold text-green-600 dark:text-green-400">
                                        +Rp {{ number_format($achievement->reward_amount, 0, ',', '.') }}
                                    </span>
                                </div>

                                <!-- Earned Date -->
                                @if($earned)
                                    <p class="mt-3 text-xs text-neutral-500 dark:text-neutral-500">
                                        Diraih: {{ $earnedAt->format('d M Y') }}
                                    </p>
                                @endif
                            </div>

                            <!-- Lock Icon for Not Earned -->
                            @if(!$earned)
                                <div class="absolute inset-0 flex items-center justify-center bg-white/20 opacity-0 transition-opacity duration-300 group-hover:opacity-100 dark:bg-black/20">
                                    <span class="text-4xl">🔒</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Rewards Summary -->
        <div class="rounded-lg border border-neutral-200 bg-gradient-to-br from-blue-50 to-indigo-50 p-6 dark:border-neutral-700 dark:from-blue-950 dark:to-indigo-950">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-bold text-blue-900 dark:text-blue-100">🎁 Total Rewards Earned</h3>
                    <p class="mt-2 text-sm text-blue-700 dark:text-blue-200">
                        Dari semua pencapaian yang sudah Anda raih:
                    </p>
                </div>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                    Rp {{ number_format(
                        $achievements
                            ->filter(fn($item) => $item['earned'])
                            ->sum(fn($item) => $item['achievement']->reward_amount),
                        0,
                        ',',
                        '.'
                    ) }}
                </p>
            </div>
        </div>

        <!-- Tips Section -->
        <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <h3 class="text-lg font-bold text-neutral-900 dark:text-white">💡 Tips Mendapatkan Badge</h3>
            <ul class="mt-4 space-y-2 text-neutral-600 dark:text-neutral-400">
                <li>✓ Lakukan pembelian secara rutin untuk membuka badge "Pemula" dan "Pelanggan Setia"</li>
                <li>✓ Tingkatkan total pembelian Anda untuk membuka badge "Penggemar Kopi"</li>
                <li>✓ Ajak teman Anda melalui referral untuk membuka badge "Duta Kafe"</li>
                <li>✓ Terus berbelanja untuk mencapai badge tertinggi "Atlet Espresso"</li>
            </ul>
        </div>
    </div>
</x-layouts::app>

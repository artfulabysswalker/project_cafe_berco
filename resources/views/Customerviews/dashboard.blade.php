<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-6">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl p-6 text-white">
            <h1 class="text-2xl font-bold mb-2">Welcome back, {{ auth()->user()->name }}! 🎉</h1>
            <p class="text-amber-100">Ready to earn more points and enjoy great coffee?</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid auto-rows-min gap-6 md:grid-cols-3">
            <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex flex-col">
                        <h3 class="text-lg font-semibold mb-1 opacity-90">Total Points</h3>
                        <p class="text-3xl font-bold">{{ auth()->user()->points ?? 0 }}</p>
                        <p class="text-sm opacity-80">Loyalty points earned</p>
                    </div>
                    <div class="text-6xl opacity-20">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-green-500 to-green-600 p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex flex-col">
                        <h3 class="text-lg font-semibold mb-1 opacity-90">Total Orders</h3>
                        <p class="text-3xl font-bold">{{ auth()->user()->orders->count() }}</p>
                        <p class="text-sm opacity-80">Orders placed</p>
                    </div>
                    <div class="text-6xl opacity-20">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex flex-col">
                        <h3 class="text-lg font-semibold mb-1 opacity-90">Total Reviews</h3>
                        <p class="text-3xl font-bold">{{ auth()->user()->reviews->count() }}</p>
                        <p class="text-sm opacity-80">Reviews submitted</p>
                    </div>
                    <div class="text-6xl opacity-20">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid auto-rows-min gap-6 md:grid-cols-2">
            <a href="{{ route('daily-quest') }}" class="relative overflow-hidden rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div class="flex flex-col">
                        <h3 class="text-lg font-semibold mb-1">Daily Quest</h3>
                        <p class="text-sm opacity-90">Selesaikan tantangan harian untuk dapat badge dan reward</p>
                    </div>
                    <div class="text-4xl opacity-20">
                        <i class="fas fa-trophy"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('rewards') }}" class="relative overflow-hidden rounded-xl bg-gradient-to-br from-pink-500 to-rose-500 p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div class="flex flex-col">
                        <h3 class="text-lg font-semibold mb-1">Rewards Center</h3>
                        <p class="text-sm opacity-90">Tukar poinmu dengan diskon dan merchandise menarik</p>
                    </div>
                    <div class="text-4xl opacity-20">
                        <i class="fas fa-gift"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Recent Activity -->
        <div class="relative overflow-hidden rounded-xl bg-white dark:bg-gray-800 shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Recent Activity</h3>
                <a href="{{ route('order.history') }}" class="text-amber-500 hover:text-orange-700 font-medium">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="space-y-4">
                @forelse(auth()->user()->orders->take(5) as $order)
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 rounded-lg hover:shadow-md transition-shadow">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center text-white font-bold">
                                #{{ $order->id }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-white">Order #{{ $order->id }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $order->created_at->format('M d, Y \a\t H:i') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-lg text-gray-800 dark:text-white">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                            <div class="flex items-center text-green-600 font-medium">
                                <i class="fas fa-plus-circle mr-1"></i>
                                +{{ $order->points_earned }} points
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-coffee text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-600 dark:text-gray-400 mb-2">No orders yet</p>
                        <p class="text-sm text-gray-500">Start ordering to earn points and unlock rewards!</p>
                        <a href="{{ route('menu.index') }}" class="inline-block mt-4 bg-gradient-to-r from-amber-500 to-orange-600 text-white px-6 py-2 rounded-lg font-medium hover:shadow-lg transition-shadow">
                            <i class="fas fa-utensils mr-2"></i>Order Now
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Points Progress -->
        @if(auth()->user()->points > 0)
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 text-white">
            <h3 class="text-lg font-bold mb-4">Points Progress</h3>
            <div class="flex items-center justify-between mb-2">
                <span>Current Points: {{ auth()->user()->points }}</span>
                <span>Next Reward: {{ ceil(auth()->user()->points / 100) * 100 + 100 }}</span>
            </div>
            <div class="w-full bg-white/20 rounded-full h-3">
                <div class="bg-white h-3 rounded-full transition-all duration-500" style="width: {{ min(100, (auth()->user()->points % 100) * 100 / 100) }}%"></div>
            </div>
            <p class="text-sm mt-2 opacity-90">Earn {{ 100 - (auth()->user()->points % 100) }} more points for your next reward!</p>
        </div>
        @endif
    </div>
</x-layouts::app>

@extends('dashboard')

@section('content')

<div class="mb-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-amber-900">Statistics Dashboard</h1>
        <p class="text-gray-600 mt-1">Monitor business performance and analytics</p>
    </div>

    <!-- Time Range Tabs -->
    @php
        $range = request('range', 'today');
    @endphp

    <div class="flex gap-2 mb-6 flex-wrap">
        <a href="?range=today" class="px-4 py-2 rounded-lg transition-all font-medium {{ $range === 'today' ? 'bg-amber-600 text-white shadow-md' : 'bg-white text-amber-700 border border-amber-300 hover:bg-amber-50' }}">
            <i class="fas fa-calendar-day mr-2"></i>Today
        </a>
        <a href="?range=week" class="px-4 py-2 rounded-lg transition-all font-medium {{ $range === 'week' ? 'bg-amber-600 text-white shadow-md' : 'bg-white text-amber-700 border border-amber-300 hover:bg-amber-50' }}">
            <i class="fas fa-calendar-week mr-2"></i>This Week
        </a>
        <a href="?range=month" class="px-4 py-2 rounded-lg transition-all font-medium {{ $range === 'month' ? 'bg-amber-600 text-white shadow-md' : 'bg-white text-amber-700 border border-amber-300 hover:bg-amber-50' }}">
            <i class="fas fa-calendar-alt mr-2"></i>This Month
        </a>
        <a href="?range=all" class="px-4 py-2 rounded-lg transition-all font-medium {{ $range === 'all' ? 'bg-amber-600 text-white shadow-md' : 'bg-white text-amber-700 border border-amber-300 hover:bg-amber-50' }}">
            <i class="fas fa-infinity mr-2"></i>All Time
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-amber-500 hover:shadow-lg transition-shadow">
            <div class="flex justify-between items-start mb-2">
                <h3 class="text-gray-600 text-sm font-medium">Total Revenue</h3>
                <div class="p-2 bg-amber-100 rounded-lg">
                    <i class="fas fa-coins text-amber-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-amber-700">Rp {{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-2">
                <span class="text-green-600 font-semibold">↑ 12%</span> from last period
            </p>
        </div>

        <!-- Total Orders -->
        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-blue-500 hover:shadow-lg transition-shadow">
            <div class="flex justify-between items-start mb-2">
                <h3 class="text-gray-600 text-sm font-medium">Total Orders</h3>
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-shopping-cart text-blue-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-blue-700">{{ $stats['total_orders'] ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-2">
                <span class="text-green-600 font-semibold">↑ 8%</span> from last period
            </p>
        </div>

        <!-- Completed Orders -->
        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-green-500 hover:shadow-lg transition-shadow">
            <div class="flex justify-between items-start mb-2">
                <h3 class="text-gray-600 text-sm font-medium">Completed Orders</h3>
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-green-700">{{ $stats['completed_orders'] ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-2">
                {{ round((($stats['completed_orders'] ?? 0) / max($stats['total_orders'] ?? 1, 1)) * 100) }}% completion rate
            </p>
        </div>

        <!-- Average Order Value -->
        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-purple-500 hover:shadow-lg transition-shadow">
            <div class="flex justify-between items-start mb-2">
                <h3 class="text-gray-600 text-sm font-medium">Avg. Order Value</h3>
                <div class="p-2 bg-purple-100 rounded-lg">
                    <i class="fas fa-chart-line text-purple-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-purple-700">Rp {{ number_format(($stats['total_revenue'] ?? 0) / max($stats['total_orders'] ?? 1, 1), 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-2">
                Per transaction average
            </p>
        </div>
    </div>

    <!-- Additional Stats Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Popular Items -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-fire text-orange-500"></i>
                Popular Items
            </h2>
            <div class="space-y-3">
                @for($i = 1; $i <= 5; $i++)
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100 last:border-b-0">
                        <span class="text-gray-700">Item #{{ $i }}</span>
                        <div class="flex items-center gap-3">
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="bg-amber-500 h-2 rounded-full" style="width: {{ 100 - ($i * 15) }}%"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-700 w-10">{{ 100 - ($i * 15) }}%</span>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-credit-card text-green-500"></i>
                Payment Methods
            </h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                    <span class="text-gray-700">Cash</span>
                    <span class="font-semibold text-gray-800">{{ $stats['cash_payments'] ?? 0 }} (45%)</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                    <span class="text-gray-700">Debit Card</span>
                    <span class="font-semibold text-gray-800">{{ $stats['debit_payments'] ?? 0 }} (35%)</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                    <span class="text-gray-700">Credit Card</span>
                    <span class="font-semibold text-gray-800">{{ $stats['credit_payments'] ?? 0 }} (20%)</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
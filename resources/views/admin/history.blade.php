@extends('dashboard')

@section('content')

<div class="mb-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-amber-900">Order History</h1>
        <p class="text-gray-600 mt-1">View and manage completed orders</p>
    </div>

    <!-- Search & Filter -->
    <form method="GET" action="{{ route('admin.history') }}" class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search (ID or Customer)</label>
                <input type="text" name="search" placeholder="Order ID or customer name..." 
                       value="{{ request('search') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                <input type="date" name="date" value="{{ request('date') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">From</label>
                <input type="datetime-local" name="from" value="{{ request('from') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">To</label>
                <input type="datetime-local" name="to" value="{{ request('to') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500">
            </div>
        </div>
        <div class="mt-4 flex gap-3">
            <button type="submit" class="px-6 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2">
                <i class="fas fa-search"></i>
                <span>Search</span>
            </button>
            <a href="{{ route('admin.history') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Reset
            </a>
        </div>
    </form>

    <!-- Orders List -->
    <div class="space-y-4">
        @forelse($historyOrders as $order)
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-all overflow-hidden border-l-4 border-amber-500">
                <div class="p-6">
                    <!-- Order Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-amber-700">Order #{{ $order->id_order }}</h3>
                            <p class="text-gray-600 text-sm">{{ $order->tanggal->format('d F Y \\a\\t H:i') }}</p>
                        </div>
                        <div class="flex gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                  :class="$order->status_order === 'completed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'">
                                {{ ucfirst($order->status_order) }}
                            </span>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                  :class="$order->status_pembayaran === 'paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                                {{ ucfirst($order->status_pembayaran) }}
                            </span>
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 pb-4 border-b border-gray-200">
                        <div>
                            <p class="text-gray-600 text-sm">Customer</p>
                            <p class="font-semibold text-gray-800">{{ $order->nama_pelanggan }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Total Amount</p>
                            <p class="font-bold text-amber-700 text-lg">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Items</p>
                            <p class="font-semibold text-gray-800">{{ count($order->items ?? []) }} item(s)</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 flex-wrap">
                        <a href="{{ route('order.receipt', $order->id_order) }}" 
                           class="px-4 py-2 bg-amber-100 text-amber-700 rounded-lg hover:bg-amber-200 transition-colors flex items-center gap-2 text-sm">
                            <i class="fas fa-eye"></i>
                            View Receipt
                        </a>
                        <a href="{{ route('admin.receipt.print', $order->id_order) }}" target="_blank"
                           class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors flex items-center gap-2 text-sm">
                            <i class="fas fa-print"></i>
                            Print
                        </a>
                        <a href="{{ route('admin.receipt.pdf', $order->id_order) }}"
                           class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors flex items-center gap-2 text-sm">
                            <i class="fas fa-file-pdf"></i>
                            Download PDF
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="fas fa-history text-gray-300 text-4xl mb-4"></i>
                <p class="text-gray-500 text-lg">No order history found</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(method_exists($historyOrders, 'links'))
        <div class="mt-8">
            {{ $historyOrders->links() }}
        </div>
    @endif
</div>

@endsection
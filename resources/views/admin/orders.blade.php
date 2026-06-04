@extends('dashboard')

@section('content')

<div class="mb-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-amber-900">Active Orders</h1>
            <p class="text-gray-600 mt-1">Monitor and manage current orders</p>
        </div>
        <a href="{{ route('order.create') }}" 
           class="bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white px-6 py-2 rounded-lg transition-all shadow-md hover:shadow-lg flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>New Order</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6 flex gap-4 flex-wrap">
        <input type="search" placeholder="Search order ID or customer..." class="flex-1 min-w-64 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500">
        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-amber-100 to-amber-50 border-b border-amber-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-amber-900">Order ID</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-amber-900">Customer</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-amber-900">Items</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-amber-900">Total</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-amber-900">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-amber-900">Payment</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-amber-900">Date</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-amber-900">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders ?? [] as $order)
                        <tr class="border-b border-gray-200 hover:bg-yellow-50 transition-colors">
                            <td class="px-6 py-4 font-semibold text-amber-700">#{{ $order->id_order ?? '1001' }}</td>
                            <td class="px-6 py-4 text-gray-800">{{ $order->nama_pelanggan ?? 'John Doe' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ count($order->items ?? []) }} items</td>
                            <td class="px-6 py-4 font-medium text-gray-800">Rp {{ number_format($order->total_harga ?? 50000, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                      :class="($order->status_order ?? 'pending') === 'pending' ? 'bg-yellow-100 text-yellow-700' : (($order->status_order ?? 'pending') === 'completed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700')">
                                    {{ ucfirst($order->status_order ?? 'Pending') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                      :class="($order->status_pembayaran ?? 'pending') === 'paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                                    {{ ucfirst($order->status_pembayaran ?? 'Pending') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-sm">{{ $order->tanggal?->format('d/m/Y H:i') ?? '02/06/2026 11:31' }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('order.show', $order->id_order ?? 1) }}" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('order.edit', $order->id_order ?? 1) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('order.receipt', $order->id_order ?? 1) }}" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Receipt">
                                        <i class="fas fa-receipt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <i class="fas fa-inbox text-gray-300 text-4xl mb-4"></i>
                                <p class="text-gray-500 text-lg">No orders found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex justify-between items-center">
        <p class="text-gray-600">Showing {{ count($orders ?? []) }} orders</p>
        <div class="flex gap-2">
            <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Previous</button>
            <button class="px-4 py-2 bg-amber-600 text-white rounded-lg">1</button>
            <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">2</button>
            <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Next</button>
        </div>
    </div>
</div>

@endsection
@extends('dashboard')

@section('content')

<div class="mb-8">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-amber-900">Active Orders</h1>
            <p class="text-gray-600 mt-1">Monitor and manage current orders</p>
        </div>

        <a href="#"
           class="bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white px-6 py-2 rounded-lg transition-all shadow-md hover:shadow-lg flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>New Order</span>
        </a>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-gradient-to-r from-amber-100 to-amber-50 border-b border-amber-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-amber-900">Order ID</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-amber-900">Customer</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-amber-900">Service</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-amber-900">Payment Method</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-amber-900">Total</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-amber-900">Order Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-amber-900">Payment Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-amber-900">Date</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($orders as $order)

                        <tr class="border-b border-gray-200 hover:bg-yellow-50 transition-colors">

                            <!-- ID -->
                            <td class="px-6 py-4 font-semibold text-amber-700">
                                #{{ $order->id_order }}
                            </td>

                            <!-- Customer -->
                            <td class="px-6 py-4 text-gray-800">
                                {{ $order->nama_pelanggan }}
                            </td>

                            <!-- Service -->
                            <td class="px-6 py-4 text-gray-700">
                                {{ ucfirst(str_replace('_', ' ', $order->service_type)) }}
                            </td>

                            <!-- Payment Method -->
                            <td class="px-6 py-4 text-gray-700">
                                {{ ucfirst($order->payment_method) }}
                            </td>

                            <!-- Total -->
                            <td class="px-6 py-4 font-medium text-gray-800">
                                Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                            </td>

                            <!-- Order Status -->
                            <td class="px-6 py-4">

                                @if($order->status_order == 'pending')

                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                        Pending
                                    </span>

                                @elseif($order->status_order == 'completed')

                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        Completed
                                    </span>

                                @else

                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                        Cancelled
                                    </span>

                                @endif

                            </td>

                            <!-- Payment Status -->
                            <td class="px-6 py-4">

                                @if($order->status_pembayaran == 'paid')

                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        Paid
                                    </span>

                                @else

                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                        Pending
                                    </span>

                                @endif

                            </td>

                            <!-- Date -->
                            <td class="px-6 py-4 text-gray-600 text-sm">
                                {{ $order->tanggal->format('d/m/Y H:i') }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">

                                <i class="fas fa-inbox text-gray-300 text-4xl mb-4"></i>

                                <p class="text-gray-500 text-lg">
                                    No orders found
                                </p>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>
    </div>

</div>

@endsection
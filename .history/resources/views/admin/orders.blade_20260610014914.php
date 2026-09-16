@extends('dashboard')

@section('content')

    <div class="mb-8">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-amber-900">Active Orders</h1>
                <p class="text-gray-600 mt-1">Monitor and manage current orders</p>
            </div>
        </div>

        {{-- Orders Table --}}
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

                        @forelse($orders as $order)

                            <tr class="border-b border-gray-200 hover:bg-yellow-50 transition-colors">

                                {{-- Order ID --}}
                                <td class="px-6 py-4 font-semibold text-amber-700">
                                    #{{ $order->id_order }}
                                </td>

                                {{-- Customer --}}
                                <td class="px-6 py-4 text-gray-800">
                                    {{ $order->nama_pelanggan }}
                                </td>

                                {{-- Items --}}
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $order->items->count() }} items
                                </td>

                                {{-- Total --}}
                                <td class="px-6 py-4 font-medium text-gray-800">
                                    Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                </td>

                                {{-- Order Status --}}
                                <td class="px-6 py-4">

                                    <span class="px-3 py-1 rounded-full text-xs font-semibold

                                            @if($order->status_order == 'pending')
                                                bg-yellow-100 text-yellow-700
                                            @elseif($order->status_order == 'completed')
                                                bg-green-100 text-green-700
                                            @elseif($order->status_order == 'cancelled')
                                                bg-red-100 text-red-700
                                            @endif
                                        ">

                                        {{ ucfirst($order->status_order) }}

                                    </span>

                                </td>

                                {{-- Payment Status --}}
                                <td class="px-6 py-4">

                                    <span class="px-3 py-1 rounded-full text-xs font-semibold

                                            @if($order->status_pembayaran == 'paid')
                                                bg-green-100 text-green-700
                                            @else
                                                bg-red-100 text-red-700
                                            @endif
                                        ">

                                        {{ ucfirst($order->status_pembayaran) }}

                                    </span>

                                </td>

                                {{-- Date --}}
                                <td class="px-6 py-4 text-gray-600 text-sm">
                                    {{ $order->tanggal->format('d/m/Y H:i') }}
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-center">

                                    <div class="flex justify-center gap-2">

                                        {{-- View --}}
                                        <a href="{{ route('order.show', $order->id_order) }}"
                                            class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">

                                            <i class="fas fa-eye"></i>

                                        </a>

                                        {{-- Complete --}}
                                        <form action="{{ route('order.finish', $order->id_order) }}" method="POST">
                                            @csrf

                                            <button type="submit"
                                                class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                                title="Finish & Move to History">

                                                <i class="fas fa-check"></i>
                                            </button>

                                        </form>

    
                                        {{-- Cancel --}}
                                        <form action="{{ route('order.cancel', $order->id_order) }}" method="POST">

                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">

                                                <i class="fas fa-times"></i>

                                            </button>

                                        </form>

                                    </div>

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

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $orders->links() }}
        </div>

    </div>

@endsection
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 py-12 px-4">
    <div class="max-w-md mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Payment Test</h1>
            <p class="text-gray-600 mt-2">Mock Payment Gateway for Testing</p>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <!-- Order Info -->
            <div class="border-b pb-4 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Order Details</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Order ID:</span>
                        <span class="font-medium text-gray-900">#{{ $order->id_order }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Customer:</span>
                        <span class="font-medium text-gray-900">{{ $order->nama_pelanggan }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Method:</span>
                        <span class="font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Amount:</span>
                        <span class="font-semibold text-xl text-orange-600">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="bg-blue-50 border border-blue-200 rounded p-4 mb-6">
                <p class="text-sm text-blue-800">
                    <strong>ℹ️ Testing Mode:</strong> This is a mock payment page for development testing. 
                    Select an action below to simulate payment response.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <button onclick="completePayment()" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <span>✓</span> Complete Payment (Success)
                </button>

                <button onclick="failPayment()" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <span>✕</span> Failed Payment
                </button>

                <a href="{{ route('cart.index') }}" class="w-full block text-center bg-gray-300 hover:bg-gray-400 text-gray-900 font-semibold py-3 rounded-lg transition duration-200">
                    ← Back to Cart
                </a>
            </div>

            <!-- Test Info -->
            <div class="mt-6 p-4 bg-gray-100 rounded text-xs text-gray-600">
                <strong>Token:</strong> {{ substr($payment->snap_token, 0, 20) }}...
            </div>
        </div>

        <!-- Footer Info -->
        <div class="mt-6 text-center text-xs text-gray-500">
            <p>🧪 This is a mock payment page for testing purposes only</p>
        </div>
    </div>
</div>

<script>
function completePayment() {
    const orderId = {{ $order->id_order }};
    
    fetch(`/payment/${orderId}/mock-complete`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Payment Success! Order has been confirmed.');
            window.location.href = data.redirect || '/orders';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error processing payment');
    });
}

function failPayment() {
    alert('❌ Payment failed (test mode)');
    window.location.href = '{{ route("cart.index") }}';
}
</script>
@endsection

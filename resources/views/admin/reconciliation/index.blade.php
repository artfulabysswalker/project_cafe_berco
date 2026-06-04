@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">📊 QRIS Reconciliation Dashboard</h1>
        <p class="text-gray-600">Kelola dan monitoring rekonsiliasi pembayaran QRIS</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Transactions</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalTransactions }}</p>
                </div>
                <i class="fas fa-receipt text-4xl text-blue-500 opacity-20"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Amount</p>
                    <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalAmount, 0, ',', '.') }}</p>
                </div>
                <i class="fas fa-money-bill text-4xl text-green-500 opacity-20"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Matched</p>
                    <p class="text-2xl font-bold text-green-600">{{ $matchedCount }}</p>
                </div>
                <i class="fas fa-check-circle text-4xl text-green-500 opacity-20"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Mismatched</p>
                    <p class="text-2xl font-bold text-red-600">{{ $mismatchedCount }}</p>
                </div>
                <i class="fas fa-exclamation-circle text-4xl text-red-500 opacity-20"></i>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="matched" {{ request('status') === 'matched' ? 'selected' : '' }}>Matched</option>
                    <option value="mismatched" {{ request('status') === 'mismatched' ? 'selected' : '' }}>Mismatched</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    🔍 Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Reconciliation Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Order ID</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Amount</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Bank Amount</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Difference</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reconciliations as $reconciliation)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-3">
                        <a href="{{ route('order.receipt', $reconciliation->qrisTransaction->order) }}" class="text-blue-600 hover:underline">
                            #{{ $reconciliation->qrisTransaction->id_order }}
                        </a>
                    </td>
                    <td class="px-6 py-3 font-semibold">Rp {{ number_format($reconciliation->system_amount, 0, ',', '.') }}</td>
                    <td class="px-6 py-3">
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            @if($reconciliation->reconciliation_status === 'matched')
                                bg-green-100 text-green-800
                            @elseif($reconciliation->reconciliation_status === 'mismatched')
                                bg-red-100 text-red-800
                            @elseif($reconciliation->reconciliation_status === 'resolved')
                                bg-blue-100 text-blue-800
                            @else
                                bg-yellow-100 text-yellow-800
                            @endif
                        ">
                            {{ ucfirst($reconciliation->reconciliation_status) }}
                        </span>
                    </td>
                    <td class="px-6 py-3">
                        {{ $reconciliation->bank_amount ? 'Rp ' . number_format($reconciliation->bank_amount, 0, ',', '.') : '-' }}
                    </td>
                    <td class="px-6 py-3">
                        @if($reconciliation->amount_difference > 0)
                            <span class="text-red-600 font-semibold">Rp {{ number_format($reconciliation->amount_difference, 0, ',', '.') }}</span>
                        @else
                            <span class="text-green-600">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-600">
                        {{ $reconciliation->created_at->format('d M Y H:i') }}
                    </td>
                    <td class="px-6 py-3">
                        <button onclick="viewDetails('{{ $reconciliation->id_reconciliation }}')" class="text-blue-600 hover:underline text-sm">
                            View
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        No reconciliation records found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($reconciliations->hasPages())
    <div class="mt-6">
        {{ $reconciliations->links() }}
    </div>
    @endif
</div>

<!-- Detail Modal -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4 p-6">
        <h2 class="text-xl font-bold mb-4">Reconciliation Details</h2>
        <div id="detailContent" class="space-y-3"></div>
        <button onclick="closeModal()" class="mt-6 w-full bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400">
            Close
        </button>
    </div>
</div>

<script>
function viewDetails(reconciliationId) {
    // In a real scenario, fetch details from server
    // For now, just show a placeholder
    const modal = document.getElementById('detailModal');
    const content = document.getElementById('detailContent');
    content.innerHTML = `<p>Loading details for reconciliation #${reconciliationId}...</p>`;
    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('detailModal').classList.add('hidden');
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>

<style>
    /* Add any custom styles here */
</style>
@endsection

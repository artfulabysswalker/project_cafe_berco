@extends('dashboard')

@section('page-title', 'Riwayat Pesanan')
@section('breadcrumb', 'Order History')

@section('content')
    <form method="GET" action="{{ route('admin.history') }}"
        style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:15px;">

        <!-- Search -->
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customer / order ID"
            style="padding:8px;border:1px solid #ddd;border-radius:6px;">

        <!-- Status Filter -->
        <select name="status" style="padding:8px;border:1px solid #ddd;border-radius:6px;">
            <option value="">All Status</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            <option value="exclude_cancelled" {{ request('status') == 'exclude_cancelled' ? 'selected' : '' }}>
                Hide Cancelled
            </option>
        </select>

        <!-- Date Filter -->
        <input type="date" name="date" value="{{ request('date') }}"
            style="padding:8px;border:1px solid #ddd;border-radius:6px;">

        <button type="submit" class="act-btn primary">
            Filter
        </button>

        @if(request()->anyFilled(['search', 'status', 'date']))
            <a href="{{ route('admin.history') }}" class="act-btn">
                Reset
            </a>
        @endif

    </form>

    </form>
    <!-- Stats Grid -->
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-shopping-cart"></i> Total Pesanan</div>
            <div class="stat-val">{{ $totalOrders ?? 0 }}</div>
            <div class="stat-diff up">↑ 12% minggu ini</div>
        </div>
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-cash"></i> Pendapatan</div>
            <div class="stat-val" style="font-size:15px">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</div>
            <div class="stat-diff up">↑ 8% minggu ini</div>
        </div>
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-check"></i> Selesai</div>
            <div class="stat-val">{{ $completedOrders ?? 0 }}</div>
            <div class="stat-diff up">{{ $completionRate ?? 0 }}% completion</div>
        </div>
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-clock"></i> Pending</div>
            <div class="stat-val">{{ $pendingOrders ?? 0 }}</div>
            <div class="stat-diff dn">Butuh perhatian</div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-history"></i> Riwayat Pesanan (Selesai)</div>
            <div class="filter-row">
                <div class="filter-chip active">Semua</div>
                <div class="filter-chip">Hari ini</div>
                <div class="filter-chip">Minggu ini</div>
                <button class="act-btn"><i class="ti ti-download"></i> Export</button>
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Pembayaran</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historyOrders as $order)
                        <tr>
                            <td><span class="order-id">#{{ $order->id_order }}</span></td>
                            <td>
                                <div class="cust-row">
                                    <div class="cust-av">{{ substr($order->nama_pelanggan ?? 'GU', 0, 2) }}</div>
                                    {{ $order->nama_pelanggan ?? 'Guest' }}
                                </div>
                            </td>
                            <td style="font-weight:500">Rp {{ number_format($order->total_harga ?? 0, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $paymentStatus = strtolower($order->status_pembayaran ?? 'pending');
                                    $badgeClass = $paymentStatus === 'paid' ? 'badge-paid' : 'badge-pending';
                                    $badgeText = $paymentStatus === 'paid' ? 'Paid' : 'Pending';
                                    $dotClass = $paymentStatus === 'paid' ? 'dot-paid' : 'dot-done';
                                @endphp
                                <span class="badge {{ $badgeClass }}"><span
                                        class="dot {{ $dotClass }}"></span>{{ $badgeText }}</span>
                            </td>
                            <td>
                                @php
                                    $orderStatus = strtolower($order->status_order ?? 'pending');
                                    $statusText = $orderStatus === 'completed' ? 'Selesai' : ucfirst($orderStatus);
                                @endphp
                                <span class="badge badge-done"><span class="dot dot-done"></span>{{ $statusText }}</span>
                            </td>
                            <td style="color:var(--color-text-secondary)">
                                {{ $order->tanggal ? $order->tanggal->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td>
                                <div style="display:flex;gap:5px">
                                    <a href="{{ route('order.receipt', $order->id_order) }}" class="act-btn">
                                        <i class="ti ti-eye"></i> Detail
                                    </a>
                                    <a href="{{ route('admin.receipt.print', $order->id_order) }}" target="_blank"
                                        class="act-btn primary">
                                        <i class="ti ti-printer"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:40px;">
                                <i class="ti ti-history" style="font-size:32px;color:#ccc;"></i>
                                <p style="color:#999;margin-top:10px;">Tidak ada riwayat pesanan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if(method_exists($historyOrders, 'links'))
        <div style="margin-top:20px;">
            {{ $historyOrders->links() }}
        </div>
    @endif

@endsection
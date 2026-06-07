@extends('dashboard')

@section('page-title', 'Dashboard')
@section('breadcrumb', 'Home')

@section('content')

<!-- Stats Grid -->
<div class="stat-grid" style="margin-bottom:16px">
    <div class="stat-card">
        <div class="stat-icon" style="background:#FAEEDA"><i class="ti ti-shopping-bag" style="color:#D4752C" aria-hidden="true"></i></div>
        <div class="stat-label">Total Pesanan</div>
        <div class="stat-val">{{ $totalOrders ?? 24 }}</div>
        <div class="stat-diff up">↑ 12% minggu ini</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#EAF3DE"><i class="ti ti-cash" style="color:#27500A" aria-hidden="true"></i></div>
        <div class="stat-label">Pendapatan</div>
        <div class="stat-val" style="font-size:16px">Rp {{ number_format($totalRevenue ?? 396000, 0, ',', '.') }}</div>
        <div class="stat-diff up">↑ 8% minggu ini</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#E1F5EE"><i class="ti ti-users" style="color:#085041" aria-hidden="true"></i></div>
        <div class="stat-label">Pelanggan</div>
        <div class="stat-val">{{ $totalCustomers ?? 18 }}</div>
        <div class="stat-diff up">↑ 5 baru hari ini</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#FCEBEB"><i class="ti ti-clock" style="color:#791F1F" aria-hidden="true"></i></div>
        <div class="stat-label">Pending</div>
        <div class="stat-val">{{ $pendingOrders ?? 2 }}</div>
        <div class="stat-diff dn">Butuh perhatian</div>
    </div>
</div>

<!-- Two Column Layout -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
    <!-- Recent Orders -->
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-shopping-bag"></i> Pesanan Terbaru</div>
        </div>
        <table class="tbl">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders ?? [] as $order)
                    <tr>
                        <td><span class="order-id">#{{ $order->id_order }}</span></td>
                        <td>{{ $order->nama_pelanggan ?? 'Guest' }}</td>
                        <td style="font-weight:500">Rp {{ number_format($order->total_harga ?? 0, 0, ',', '.') }}</td>
                        <td><span class="badge badge-process"><span class="dot dot-b"></span>Proses</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:20px;color:#999;">Tidak ada pesanan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Top Menu -->
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-star"></i> Menu Terlaris</div>
        </div>
        <div style="padding:10px 14px">
            <div class="top-menu-row">
                <div class="top-menu-name">Cappuccino</div>
                <div class="top-menu-bar"><div class="top-menu-fill" style="width:90%"></div></div>
                <div class="top-menu-val">45x</div>
            </div>
            <div class="top-menu-row">
                <div class="top-menu-name">Latte</div>
                <div class="top-menu-bar"><div class="top-menu-fill" style="width:70%"></div></div>
                <div class="top-menu-val">35x</div>
            </div>
            <div class="top-menu-row">
                <div class="top-menu-name">Ice Blended</div>
                <div class="top-menu-bar"><div class="top-menu-fill" style="width:55%"></div></div>
                <div class="top-menu-val">28x</div>
            </div>
            <div class="top-menu-row">
                <div class="top-menu-name">Espresso</div>
                <div class="top-menu-bar"><div class="top-menu-fill" style="width:40%"></div></div>
                <div class="top-menu-val">20x</div>
            </div>
            <div class="top-menu-row">
                <div class="top-menu-name">Croissant</div>
                <div class="top-menu-bar"><div class="top-menu-fill" style="width:30%"></div></div>
                <div class="top-menu-val">15x</div>
            </div>
        </div>
    </div>
</div>

<!-- Activity Section -->
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-clock"></i> Aktivitas Terkini</div>
    </div>
    <div style="padding:10px 14px">
        <div style="display:flex;gap:8px;margin-bottom:10px;align-items:flex-start">
            <div style="width:8px;height:8px;border-radius:50%;background:#D4752C;margin-top:5px;flex-shrink:0"></div>
            <div>
                <div style="font-size:11px;color:var(--color-text-primary)"><strong>Pesanan #3 selesai</strong></div>
                <div style="font-size:10px;color:var(--color-text-secondary)">5 menit yang lalu</div>
            </div>
        </div>
        <div style="display:flex;gap:8px;margin-bottom:10px;align-items:flex-start">
            <div style="width:8px;height:8px;border-radius:50%;background:#27500A;margin-top:5px;flex-shrink:0"></div>
            <div>
                <div style="font-size:11px;color:var(--color-text-primary)"><strong>Pembayaran diterima</strong> dari Rp 16.500</div>
                <div style="font-size:10px;color:var(--color-text-secondary)">12 menit yang lalu</div>
            </div>
        </div>
        <div style="display:flex;gap:8px;align-items:flex-start">
            <div style="width:8px;height:8px;border-radius:50%;background:#378ADD;margin-top:5px;flex-shrink:0"></div>
            <div>
                <div style="font-size:11px;color:var(--color-text-primary)"><strong>Pesanan baru</strong> dari Customer One</div>
                <div style="font-size:10px;color:var(--color-text-secondary)">25 menit yang lalu</div>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }
    .stat-card {
        background: var(--color-background-primary);
        border: 0.5px solid var(--color-border-tertiary);
        border-radius: 9px;
        padding: 12px 14px;
    }
    .stat-icon {
        width: 32px;
        height: 32px;
        border-radius: 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 8px;
    }
    .stat-label {
        font-size: 11px;
        color: var(--color-text-secondary);
        margin-bottom: 4px;
    }
    .stat-val {
        font-size: 20px;
        font-weight: 600;
        color: var(--color-text-primary);
    }
    .stat-diff {
        font-size: 10px;
        margin-top: 4px;
    }
    .stat-diff.up {
        color: #27500A;
    }
    .stat-diff.dn {
        color: #A32D2D;
    }
    .card {
        background: var(--color-background-primary);
        border: 0.5px solid var(--color-border-tertiary);
        border-radius: 9px;
        overflow: hidden;
    }
    .card-header {
        padding: 12px 14px;
        border-bottom: 0.5px solid var(--color-border-tertiary);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .card-title {
        font-size: 13px;
        font-weight: 500;
        color: var(--color-text-primary);
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .card-title i {
        color: #D4752C;
    }
    .tbl {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }
    .tbl th {
        padding: 8px 12px;
        text-align: left;
        color: var(--color-text-secondary);
        font-weight: 500;
        font-size: 10px;
        border-bottom: 0.5px solid var(--color-border-tertiary);
        background: var(--color-background-secondary);
        text-transform: uppercase;
    }
    .tbl td {
        padding: 10px 12px;
        border-bottom: 0.5px solid var(--color-border-tertiary);
        color: var(--color-text-primary);
    }
    .tbl tr:hover td {
        background: #FDF8F4;
    }
    .order-id {
        font-weight: 600;
        color: #D4752C;
    }
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 500;
    }
    .badge-process {
        background: #E6F1FB;
        color: #0C447C;
    }
    .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }
    .dot-b {
        background: #378ADD;
    }
    .top-menu-row {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 0;
        border-bottom: 0.5px solid var(--color-border-tertiary);
    }
    .top-menu-row:last-child {
        border-bottom: none;
    }
    .top-menu-name {
        flex: 1;
        font-size: 11px;
        color: var(--color-text-primary);
    }
    .top-menu-bar {
        flex: 2;
        height: 6px;
        border-radius: 3px;
        background: var(--color-background-secondary);
        overflow: hidden;
    }
    .top-menu-fill {
        height: 100%;
        border-radius: 3px;
        background: #D4752C;
    }
    .top-menu-val {
        font-size: 10px;
        color: var(--color-text-secondary);
        min-width: 32px;
        text-align: right;
    }
</style>

@endsection

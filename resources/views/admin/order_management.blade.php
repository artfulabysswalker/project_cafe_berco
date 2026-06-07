@extends('dashboard')

@section('page-title', 'Manajemen Pesanan')
@section('breadcrumb', 'Orders')

@section('content')

<!-- Order Status Filter -->
<div style="display:flex;gap:6px;margin-bottom:14px;flex-wrap:wrap;align-items:center">
    <div class="filter-chip active">Semua (142)</div>
    <div class="filter-chip">Baru (8)</div>
    <div class="filter-chip">Dikemas (5)</div>
    <div class="filter-chip">Siap (3)</div>
    <div class="filter-chip">Selesai (126)</div>
    <div style="margin-left:auto;display:flex;gap:6px">
        <button class="act-btn"><i class="ti ti-download"></i> Export</button>
        <button class="act-btn primary"><i class="ti ti-printer"></i> Print</button>
    </div>
</div>

<!-- Order Queue Status -->
<div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-bottom:14px">
    <div class="queue-card">
        <div class="queue-icon" style="background:#FFE8CC;color:#7D3D1F">📝</div>
        <div style="font-size:11px;color:var(--color-text-secondary)">Pesanan Baru</div>
        <div style="font-size:18px;font-weight:600">8</div>
        <div style="font-size:9px;color:#D4752C">↑ 3 dalam 10 menit terakhir</div>
    </div>
    <div class="queue-card">
        <div class="queue-icon" style="background:#FFF4CC;color:#B39200">🔄</div>
        <div style="font-size:11px;color:var(--color-text-secondary)">Sedang Dikemas</div>
        <div style="font-size:18px;font-weight:600">5</div>
        <div style="font-size:9px;color:#B39200">Est. selesai 5 menit</div>
    </div>
    <div class="queue-card">
        <div class="queue-icon" style="background:#E1F5EE;color:#085041">✓</div>
        <div style="font-size:11px;color:var(--color-text-secondary)">Siap Diambil</div>
        <div style="font-size:18px;font-weight:600">3</div>
        <div style="font-size:9px;color:#1D9E75">Menunggu pelanggan</div>
    </div>
    <div class="queue-card">
        <div class="queue-icon" style="background:#E8F2FF;color:#003D99">🎯</div>
        <div style="font-size:11px;color:var(--color-text-secondary)">Kesuksesan Hari Ini</div>
        <div style="font-size:18px;font-weight:600">98%</div>
        <div style="font-size:9px;color:#003D99">126 dari 128 selesai</div>
    </div>
</div>

<!-- Active Orders Table -->
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-shopping-bag"></i> Pesanan Aktif</div>
    </div>
    <table class="tbl">
        <thead>
            <tr>
                <th>No. Pesanan</th>
                <th>Pelanggan</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status Order</th>
                <th>Pembayaran</th>
                <th>Waktu Order</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr class="highlight">
                <td style="font-weight:600">#147</td>
                <td>Customer One</td>
                <td>
                    <div style="font-size:10px">
                        • Cappuccino x2<br/>
                        • Latte x1
                    </div>
                </td>
                <td style="font-weight:600">Rp 49.5k</td>
                <td><span class="badge badge-new">Baru</span></td>
                <td><span class="badge badge-done">Lunas</span></td>
                <td style="font-size:10px">05/06 11:52</td>
                <td>
                    <button class="order-action-btn">👁 Lihat</button>
                </td>
            </tr>
            <tr class="highlight">
                <td style="font-weight:600">#146</td>
                <td>Guest User</td>
                <td>
                    <div style="font-size:10px">
                        • Ice Blended x3
                    </div>
                </td>
                <td style="font-weight:600">Rp 67.5k</td>
                <td><span class="badge badge-packing">Dikemas</span></td>
                <td><span class="badge badge-done">Lunas</span></td>
                <td style="font-size:10px">05/06 11:45</td>
                <td>
                    <button class="order-action-btn">✓ Selesai</button>
                </td>
            </tr>
            <tr class="highlight">
                <td style="font-weight:600">#145</td>
                <td>John Doe</td>
                <td>
                    <div style="font-size:10px">
                        • Cappuccino x1<br/>
                        • Snack x2
                    </div>
                </td>
                <td style="font-weight:600">Rp 65k</td>
                <td><span class="badge badge-ready">Siap</span></td>
                <td><span class="badge badge-done">Lunas</span></td>
                <td style="font-size:10px">05/06 11:38</td>
                <td>
                    <button class="order-action-btn">🔔 Ingatkan</button>
                </td>
            </tr>
            <tr>
                <td style="font-weight:600">#144</td>
                <td>Jane Smith</td>
                <td>
                    <div style="font-size:10px">
                        • Latte x1<br/>
                        • Pastry x1
                    </div>
                </td>
                <td style="font-weight:600">Rp 42k</td>
                <td><span class="badge badge-done">Selesai</span></td>
                <td><span class="badge badge-done">Lunas</span></td>
                <td style="font-size:10px">05/06 11:20</td>
                <td>
                    <button class="order-action-btn">📊 Detail</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Performance Metrics -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px">
    <!-- Average Order Value -->
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-chart-bar"></i> AOV Harian</div>
        </div>
        <div style="padding:14px">
            <div style="display:flex;justify-content:space-between;align-items:flex-end;height:80px">
                <div class="bar" style="height:40%;background:#D4752C"></div>
                <div class="bar" style="height:65%;background:#D4752C"></div>
                <div class="bar" style="height:50%;background:#D4752C"></div>
                <div class="bar" style="height:75%;background:#D4752C"></div>
                <div class="bar" style="height:90%;background:#D4752C"></div>
                <div class="bar" style="height:85%;background:#D4752C"></div>
                <div class="bar" style="height:95%;background:#D4752C"></div>
            </div>
            <div style="font-size:10px;color:var(--color-text-secondary);margin-top:8px">
                Rata-rata hari ini: <strong>Rp 52.3k</strong> per pesanan
            </div>
        </div>
    </div>

    <!-- Order Completion Time -->
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-clock"></i> Waktu Persiapan</div>
        </div>
        <div style="padding:14px">
            <div class="time-stat">
                <span>Tercepat:</span>
                <strong>2 menit</strong>
            </div>
            <div class="time-stat">
                <span>Rata-rata:</span>
                <strong>7.5 menit</strong>
            </div>
            <div class="time-stat">
                <span>Terlama:</span>
                <strong>18 menit</strong>
            </div>
            <div style="background:var(--color-background-secondary);border-radius:4px;padding:8px;margin-top:8px;font-size:10px">
                <span style="color:var(--color-text-secondary)">Target SLA: 10 menit</span><br/>
                <span style="color:#1D9E75;font-weight:600">✓ 92% pesanan sesuai target</span>
            </div>
        </div>
    </div>
</div>

<style>
    .filter-chip {
        font-size: 11px;
        padding: 5px 12px;
        border-radius: 20px;
        border: 0.5px solid var(--color-border-tertiary);
        cursor: pointer;
        color: var(--color-text-secondary);
        background: transparent;
    }
    .filter-chip.active {
        background: #D4752C;
        color: #fff;
    }
    .act-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 12px;
        border-radius: 7px;
        background: transparent;
        border: 0.5px solid var(--color-border-tertiary);
        color: var(--color-text-secondary);
        cursor: pointer;
        font-size: 11px;
        font-weight: 600;
    }
    .act-btn.primary {
        background: #D4752C;
        color: #fff;
        border-color: #D4752C;
    }
    .queue-card {
        background: var(--color-background-primary);
        border: 0.5px solid var(--color-border-tertiary);
        border-radius: 9px;
        padding: 12px;
    }
    .queue-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        margin-bottom: 8px;
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
        font-weight: 600;
        font-size: 10px;
        border-bottom: 0.5px solid var(--color-border-tertiary);
        background: var(--color-background-secondary);
    }
    .tbl td {
        padding: 10px 12px;
        border-bottom: 0.5px solid var(--color-border-tertiary);
        color: var(--color-text-primary);
    }
    .tbl tr.highlight:hover td {
        background: #FDF8F4;
    }
    .badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 9px;
        font-weight: 600;
    }
    .badge-new {
        background: #FAEEDA;
        color: #633806;
    }
    .badge-packing {
        background: #FFF4CC;
        color: #7D3D1F;
    }
    .badge-ready {
        background: #E1F5EE;
        color: #085041;
    }
    .badge-done {
        background: #EAF3DE;
        color: #27500A;
    }
    .order-action-btn {
        padding: 4px 8px;
        border-radius: 4px;
        border: 0.5px solid var(--color-border-tertiary);
        background: transparent;
        cursor: pointer;
        font-size: 10px;
        font-weight: 600;
        color: var(--color-text-secondary);
    }
    .order-action-btn:hover {
        background: var(--color-background-secondary);
    }
    .bar {
        flex: 1;
        border-radius: 4px 4px 0 0;
        opacity: 0.7;
    }
    .time-stat {
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
        font-size: 11px;
        border-bottom: 0.5px solid var(--color-border-tertiary);
    }
</style>

@endsection

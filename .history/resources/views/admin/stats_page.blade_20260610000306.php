@extends('dashboard')

@section('page-title', 'Statistik & Analitik')
@section('breadcrumb', 'Statistics')

@section('content')

<!-- Filter Period -->
<div style="display:flex;gap:6px;margin-bottom:14px;flex-wrap:wrap">
    <div class="filter-chip active">Minggu ini</div>
    <div class="filter-chip">Bulan ini</div>
    <div class="filter-chip">3 Bulan</div>
    <div class="filter-chip">Tahun ini</div>
</div>

<!-- Stats Grid -->
<div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-bottom:14px">
    <div class="stat-card">
        <div class="stat-label">Total Pendapatan</div>
        <div class="stat-val" style="font-size:18px">Rp 396k</div>
        <div class="stat-diff up">↑ 8% vs minggu lalu</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Pesanan</div>
        <div class="stat-val">24</div>
        <div class="stat-diff up">↑ 12% vs minggu lalu</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Rata-rata/hari</div>
        <div class="stat-val" style="font-size:16px">Rp 56.5k</div>
        <div class="stat-diff up">↑ 5% vs minggu lalu</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pelanggan Unik</div>
        <div class="stat-val">18</div>
        <div class="stat-diff up">↑ 3 baru</div>
    </div>
</div>

<!-- Charts Row -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
    <!-- Revenue Chart -->
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-chart-bar"></i> Pendapatan Harian</div>
        </div>
        <div style="padding:14px">
            <div class="chart-bar-wrap">
                <div class="bar" style="height:60px"></div>
                <div class="bar" style="height:85px"></div>
                <div class="bar" style="height:35px"></div>
                <div class="bar" style="height:100px"></div>
                <div class="bar" style="height:68px"></div>
                <div class="bar" style="height:95px"></div>
                <div class="bar" style="height:48px"></div>
            </div>
            <div style="display:flex;gap:8px;margin-top:10px">
                <div class="bar-label">Sen</div>
                <div class="bar-label">Sel</div>
                <div class="bar-label">Rab</div>
                <div class="bar-label">Kam</div>
                <div class="bar-label">Jum</div>
                <div class="bar-label">Sab</div>
                <div class="bar-label">Min</div>
            </div>
        </div>
    </div>

    <!-- Top Menu Performance -->
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-star"></i> Performa Menu</div>
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
        </div>
    </div>
</div>

<!-- Customer Statistics -->
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-users"></i> Statistik Pelanggan Terbaik</div>
    </div>
    <table class="tbl">
        <thead>
            <tr>
                <th>Pelanggan</th>
                <th>Total Pesanan</th>
                <th>Total Belanja</th>
                <th>Terakhir Order</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Customer One</td>
                <td style="font-weight:600">5</td>
                <td>Rp 82.500</td>
                <td>2 jam yang lalu</td>
                <td><span class="badge badge-done"><span class="dot dot-t"></span>Aktif</span></td>
            </tr>
            <tr>
                <td>Guest User</td>
                <td style="font-weight:600">3</td>
                <td>Rp 49.500</td>
                <td>5 jam yang lalu</td>
                <td><span class="badge badge-done"><span class="dot dot-t"></span>Aktif</span></td>
            </tr>
            <tr>
                <td>John Doe</td>
                <td style="font-weight:600">8</td>
                <td>Rp 132.000</td>
                <td>1 hari yang lalu</td>
                <td><span class="badge badge-done"><span class="dot dot-t"></span>Aktif</span></td>
            </tr>
            <tr>
                <td>Jane Smith</td>
                <td style="font-weight:600">2</td>
                <td>Rp 33.000</td>
                <td>3 hari yang lalu</td>
                <td><span class="badge badge-pending"><span class="dot dot-y"></span>Tidak Aktif</span></td>
            </tr>
        </tbody>
    </table>
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
    .stat-card {
        background: var(--color-background-primary);
        border: 0.5px solid var(--color-border-tertiary);
        border-radius: 9px;
        padding: 12px 14px;
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
    .chart-bar-wrap {
        display: flex;
        align-items: flex-end;
        gap: 6px;
        height: 100px;
        padding: 0 4px;
    }
    .bar {
        flex: 1;
        border-radius: 4px 4px 0 0;
        background: #D4752C;
        opacity: 0.8;
    }
    .bar-label {
        flex: 1;
        font-size: 10px;
        color: var(--color-text-secondary);
        text-align: center;
    }
    .top-menu-row {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 0;
        border-bottom: 0.5px solid var(--color-border-tertiary);
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
    .tbl tr:hover td {
        background: #FDF8F4;
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
    .badge-done {
        background: #E1F5EE;
        color: #085041;
    }
    .badge-pending {
        background: #FAEEDA;
        color: #633806;
    }
    .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }
    .dot-t {
        background: #1D9E75;
    }
    .dot-y {
        background: #EF9F27;
    }
</style>

@endsection

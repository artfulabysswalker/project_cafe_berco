@extends('dashboard')

@section('page-title', 'Voucher & Promo')
@section('breadcrumb', 'Vouchers')

@section('content')

<!-- Header -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;flex-wrap:wrap;gap:10px">
    <div style="display:flex;gap:6px;flex-wrap:wrap">
        <div class="filter-chip active">Semua</div>
        <div class="filter-chip">Aktif</div>
        <div class="filter-chip">Kadaluarsa</div>
    </div>
    <button class="add-btn"><i class="ti ti-plus"></i> Tambah Voucher</button>
</div>

<!-- Voucher Grid -->
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:12px">
    <!-- Voucher Card 1 -->
    <div class="vouch-card">
        <div class="vouch-badge">ACTIVE</div>
        <div class="vouch-code">COFFEE2024</div>
        <div style="font-size:11px;color:var(--color-text-secondary);margin-top:2px">Diskon 20% Kopi</div>
        <div style="border-top:0.5px solid var(--color-border-tertiary);margin-top:8px;padding-top:8px">
            <div class="vouch-row">
                <span>Diskon</span>
                <span style="font-weight:600">20%</span>
            </div>
            <div class="vouch-row">
                <span>Min. Pembelian</span>
                <span>Rp 50k</span>
            </div>
            <div class="vouch-row">
                <span>Digunakan</span>
                <span style="font-weight:600">45x</span>
            </div>
            <div class="vouch-row">
                <span>Kadaluarsa</span>
                <span>31 Des 2024</span>
            </div>
        </div>
        <div style="display:flex;gap:6px;margin-top:10px">
            <button class="vouch-btn edit"><i class="ti ti-edit"></i> Edit</button>
            <button class="vouch-btn delete"><i class="ti ti-trash"></i> Hapus</button>
        </div>
    </div>

    <!-- Voucher Card 2 -->
    <div class="vouch-card">
        <div class="vouch-badge" style="background:#FFE8E8;color:#791F1F">EXPIRED</div>
        <div class="vouch-code">PROMO2024</div>
        <div style="font-size:11px;color:var(--color-text-secondary);margin-top:2px">Diskon 15% Semua Menu</div>
        <div style="border-top:0.5px solid var(--color-border-tertiary);margin-top:8px;padding-top:8px">
            <div class="vouch-row">
                <span>Diskon</span>
                <span style="font-weight:600">15%</span>
            </div>
            <div class="vouch-row">
                <span>Min. Pembelian</span>
                <span>Rp 30k</span>
            </div>
            <div class="vouch-row">
                <span>Digunakan</span>
                <span style="font-weight:600">120x</span>
            </div>
            <div class="vouch-row">
                <span>Kadaluarsa</span>
                <span>31 Mei 2024</span>
            </div>
        </div>
        <div style="display:flex;gap:6px;margin-top:10px">
            <button class="vouch-btn edit"><i class="ti ti-edit"></i> Edit</button>
            <button class="vouch-btn delete"><i class="ti ti-trash"></i> Hapus</button>
        </div>
    </div>

    <!-- Voucher Card 3 -->
    <div class="vouch-card">
        <div class="vouch-badge" style="background:#E1F5EE;color:#085041">COMING</div>
        <div class="vouch-code">SUMMER2024</div>
        <div style="font-size:11px;color:var(--color-text-secondary);margin-top:2px">Diskon 25% Ice Blended</div>
        <div style="border-top:0.5px solid var(--color-border-tertiary);margin-top:8px;padding-top:8px">
            <div class="vouch-row">
                <span>Diskon</span>
                <span style="font-weight:600">25%</span>
            </div>
            <div class="vouch-row">
                <span>Min. Pembelian</span>
                <span>Rp 40k</span>
            </div>
            <div class="vouch-row">
                <span>Digunakan</span>
                <span style="font-weight:600">0x</span>
            </div>
            <div class="vouch-row">
                <span>Mulai</span>
                <span>01 Jun 2024</span>
            </div>
        </div>
        <div style="display:flex;gap:6px;margin-top:10px">
            <button class="vouch-btn edit"><i class="ti ti-edit"></i> Edit</button>
            <button class="vouch-btn delete"><i class="ti ti-trash"></i> Hapus</button>
        </div>
    </div>

    <!-- Voucher Card 4 -->
    <div class="vouch-card">
        <div class="vouch-badge">ACTIVE</div>
        <div class="vouch-code">LOYAL2024</div>
        <div style="font-size:11px;color:var(--color-text-secondary);margin-top:2px">Voucher Member Loyal</div>
        <div style="border-top:0.5px solid var(--color-border-tertiary);margin-top:8px;padding-top:8px">
            <div class="vouch-row">
                <span>Diskon</span>
                <span style="font-weight:600">Rp 10k</span>
            </div>
            <div class="vouch-row">
                <span>Min. Pembelian</span>
                <span>Rp 100k</span>
            </div>
            <div class="vouch-row">
                <span>Digunakan</span>
                <span style="font-weight:600">23x</span>
            </div>
            <div class="vouch-row">
                <span>Kadaluarsa</span>
                <span>31 Des 2024</span>
            </div>
        </div>
        <div style="display:flex;gap:6px;margin-top:10px">
            <button class="vouch-btn edit"><i class="ti ti-edit"></i> Edit</button>
            <button class="vouch-btn delete"><i class="ti ti-trash"></i> Hapus</button>
        </div>
    </div>
</div>

<!-- Voucher Usage Statistics -->
<div style="margin-top:14px">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-chart-bar"></i> Statistik Penggunaan Voucher</div>
        </div>
        <table class="tbl">
            <thead>
                <tr>
                    <th>Kode Voucher</th>
                    <th>Deskripsi</th>
                    <th>Total Penggunaan</th>
                    <th>Total Diskon</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight:600">COFFEE2024</td>
                    <td>Diskon 20% Kopi</td>
                    <td>45x</td>
                    <td>Rp 180k</td>
                    <td><span class="badge badge-done"><span class="dot dot-t"></span>Aktif</span></td>
                </tr>
                <tr>
                    <td style="font-weight:600">PROMO2024</td>
                    <td>Diskon 15% Semua Menu</td>
                    <td>120x</td>
                    <td>Rp 540k</td>
                    <td><span class="badge badge-done"><span class="dot dot-t"></span>Aktif</span></td>
                </tr>
                <tr>
                    <td style="font-weight:600">LOYAL2024</td>
                    <td>Voucher Member Loyal</td>
                    <td>23x</td>
                    <td>Rp 230k</td>
                    <td><span class="badge badge-done"><span class="dot dot-t"></span>Aktif</span></td>
                </tr>
            </tbody>
        </table>
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
    .add-btn {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 6px 12px;
        border-radius: 7px;
        background: #D4752C;
        color: #fff;
        border: none;
        cursor: pointer;
        font-size: 11px;
        font-weight: 600;
    }
    .add-btn:hover {
        background: #c26620;
    }
    .vouch-card {
        background: var(--color-background-primary);
        border: 0.5px solid var(--color-border-tertiary);
        border-radius: 9px;
        padding: 12px;
        position: relative;
    }
    .vouch-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 8px;
        font-weight: 700;
        background: #E1F5EE;
        color: #085041;
        letter-spacing: 0.03em;
    }
    .vouch-code {
        font-size: 14px;
        font-weight: 700;
        color: var(--color-text-primary);
        margin-top: 8px;
        font-family: 'Courier New', monospace;
    }
    .vouch-row {
        display: flex;
        justify-content: space-between;
        font-size: 10px;
        color: var(--color-text-secondary);
        margin-bottom: 4px;
    }
    .vouch-btn {
        flex: 1;
        padding: 6px 8px;
        border-radius: 6px;
        font-size: 10px;
        border: 0.5px solid var(--color-border-tertiary);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 3px;
        background: transparent;
    }
    .vouch-btn.edit {
        color: #0066cc;
        background: #E8F2FF;
        border-color: #B3D9FF;
    }
    .vouch-btn.delete {
        color: #791F1F;
        background: #FCEBEB;
        border-color: #F09595;
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
        background: #E1F5EE;
        color: #085041;
    }
    .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
        background: #1D9E75;
    }
    .badge-done {
        background: #E1F5EE;
        color: #085041;
    }
    .dot-t {
        background: #1D9E75;
    }
</style>

@endsection

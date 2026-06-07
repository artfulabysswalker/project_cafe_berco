@extends('dashboard')

@section('page-title', 'Permintaan & Pesan')
@section('breadcrumb', 'Requests')

@section('content')

<!-- Filter -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;flex-wrap:wrap;gap:10px">
    <div style="display:flex;gap:6px;flex-wrap:wrap">
        <div class="filter-chip active">Semua</div>
        <div class="filter-chip">Belum Dibalas</div>
        <div class="filter-chip">Selesai</div>
    </div>
    <span style="font-size:12px;color:var(--color-text-secondary)">2 permintaan baru</span>
</div>

<!-- Request Cards -->
<div>
    <!-- Request Card 1 -->
    <div class="req-card">
        <div class="req-top">
            <div class="req-user">
                <div class="cust-av" style="width:32px;height:32px;font-size:12px">CO</div>
                <div>
                    <div style="font-size:12px;font-weight:600;color:var(--color-text-primary)">Customer One</div>
                    <div style="font-size:10px;color:var(--color-text-secondary)">2 jam yang lalu</div>
                </div>
            </div>
            <span class="badge badge-pending"><span class="dot dot-y"></span>Baru</span>
        </div>
        <div class="req-msg">Halo admin, saya ingin request menu matcha latte untuk ditambahkan ke menu. Sudah banyak pelanggan yang menanyakan menu ini. Mohon dipertimbangkan ya, terima kasih!</div>
        <div class="req-actions">
            <button class="act-btn success"><i class="ti ti-check"></i> Tandai Selesai</button>
            <button class="act-btn primary"><i class="ti ti-message"></i> Balas</button>
            <button class="act-btn danger"><i class="ti ti-trash"></i> Hapus</button>
        </div>
    </div>

    <!-- Request Card 2 -->
    <div class="req-card">
        <div class="req-top">
            <div class="req-user">
                <div class="cust-av" style="width:32px;height:32px;font-size:12px;background:#E6F1FB;color:#0C447C">GU</div>
                <div>
                    <div style="font-size:12px;font-weight:600;color:var(--color-text-primary)">Guest User</div>
                    <div style="font-size:10px;color:var(--color-text-secondary)">1 jam yang lalu</div>
                </div>
            </div>
            <span class="badge badge-pending"><span class="dot dot-y"></span>Baru</span>
        </div>
        <div class="req-msg">Pak admin, apakah ada promo untuk pelanggan yang ulang tahun? Kebetulan minggu depan ulang tahun saya, dan saya sering pesan di sini. Terima kasih sebelumnya.</div>
        <div class="req-actions">
            <button class="act-btn success"><i class="ti ti-check"></i> Tandai Selesai</button>
            <button class="act-btn primary"><i class="ti ti-message"></i> Balas</button>
            <button class="act-btn danger"><i class="ti ti-trash"></i> Hapus</button>
        </div>
    </div>

    <!-- Request Card 3 (Completed) -->
    <div class="req-card" style="opacity:0.7">
        <div class="req-top">
            <div class="req-user">
                <div class="cust-av" style="width:32px;height:32px;font-size:12px;background:#EAF3DE;color:#27500A">JD</div>
                <div>
                    <div style="font-size:12px;font-weight:600;color:var(--color-text-primary)">John Doe</div>
                    <div style="font-size:10px;color:var(--color-text-secondary)">3 hari yang lalu</div>
                </div>
            </div>
            <span class="badge badge-done"><span class="dot dot-t"></span>Selesai</span>
        </div>
        <div class="req-msg">Tolong tambahkan opsi ukuran large untuk minuman. Saya dan teman-teman suka memesan minuman ukuran besar.</div>
        <div class="req-actions">
            <button class="act-btn danger"><i class="ti ti-trash"></i> Hapus</button>
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
    .req-card {
        background: var(--color-background-primary);
        border: 0.5px solid var(--color-border-tertiary);
        border-radius: 9px;
        padding: 12px;
        margin-bottom: 10px;
    }
    .req-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .req-user {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .cust-av {
        border-radius: 50%;
        background: #FAEEDA;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #633806;
        font-weight: 600;
        flex-shrink: 0;
    }
    .req-msg {
        font-size: 12px;
        color: var(--color-text-secondary);
        line-height: 1.5;
        margin-bottom: 10px;
    }
    .req-actions {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }
    .act-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 11px;
        border: 0.5px solid var(--color-border-tertiary);
        cursor: pointer;
        color: var(--color-text-secondary);
        background: transparent;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .act-btn:hover {
        background: var(--color-background-secondary);
    }
    .act-btn.primary {
        background: #D4752C;
        color: #fff;
        border-color: #D4752C;
    }
    .act-btn.success {
        background: #EAF3DE;
        color: #27500A;
        border-color: #C0DD97;
    }
    .act-btn.danger {
        background: #FCEBEB;
        color: #791F1F;
        border-color: #F09595;
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

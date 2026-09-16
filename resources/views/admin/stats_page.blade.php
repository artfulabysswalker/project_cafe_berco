@extends('dashboard')

@section('page-title', 'Statistik & Laporan Keuangan Shift')
@section('breadcrumb', 'Analytics & Shift Reports')

@section('content')
<div class="stats-financial-page">

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="alert-success-custom">
            <i class="fas fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    {{-- 1. Action & Filter Toolbar --}}
    <div class="report-toolbar-card">
        <form method="GET" action="{{ route('admin.stats') }}" id="filterReportForm" class="toolbar-filter-grid">
            
            {{-- Staff / Kasir Selector (Admin & Individual Kasir) --}}
            <div class="filter-item-group">
                <label class="filter-item-lbl"><i class="fas fa-user-tie text-amber"></i> Akun Kasir / Staff</label>
                <select name="staff_id" class="filter-select" onchange="document.getElementById('filterReportForm').submit()">
                    @if($isAdmin)
                        <option value="all" {{ $selectedStaffId === 'all' ? 'selected' : '' }}>-- Semua Staff & Admin --</option>
                    @endif
                    @foreach($staffList as $staff)
                        <option value="{{ $staff->id_user }}" {{ $selectedStaffId == $staff->id_user ? 'selected' : '' }}>
                            {{ $staff->name }} ({{ $staff->role?->role_name ?? 'Staff' }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Shift Selector --}}
            <div class="filter-item-group">
                <label class="filter-item-lbl"><i class="fas fa-business-time text-blue"></i> Pilihan Shift Operasional</label>
                <select name="shift" class="filter-select" onchange="document.getElementById('filterReportForm').submit()">
                    <option value="all" {{ $selectedShift === 'all' ? 'selected' : '' }}>Semua Jam Buka Cafe (10:00 - 22:30)</option>
                    <option value="shift1" {{ $selectedShift === 'shift1' ? 'selected' : '' }}>Shift 1 Siang (10:00 - 16:00 / 1 Staff)</option>
                    <option value="shift2" {{ $selectedShift === 'shift2' ? 'selected' : '' }}>Shift 2 Sore (16:00 - 22:30 / 2 Staff)</option>
                    <option value="shift_izin" {{ $selectedShift === 'shift_izin' ? 'selected' : '' }}>Shift Khusus (1 Staff Izin / Buka 16:00 - 22:30)</option>
                </select>
            </div>

            {{-- Period Filter Chips & Dates --}}
            <div class="filter-item-group">
                <label class="filter-item-lbl"><i class="fas fa-calendar-alt text-purple"></i> Rentang Periode</label>
                <div class="period-tabs-inline">
                    <a href="{{ route('admin.stats', array_merge(request()->query(), ['range' => 'today'])) }}" class="tab-chip {{ $range === 'today' ? 'active' : '' }}">Hari Ini</a>
                    <a href="{{ route('admin.stats', array_merge(request()->query(), ['range' => 'yesterday'])) }}" class="tab-chip {{ $range === 'yesterday' ? 'active' : '' }}">Kemarin</a>
                    <a href="{{ route('admin.stats', array_merge(request()->query(), ['range' => 'week'])) }}" class="tab-chip {{ $range === 'week' ? 'active' : '' }}">Minggu Ini</a>
                    <a href="{{ route('admin.stats', array_merge(request()->query(), ['range' => 'month'])) }}" class="tab-chip {{ $range === 'month' ? 'active' : '' }}">Bulan Ini</a>
                </div>
            </div>

            {{-- Custom Date Range --}}
            <div class="filter-item-group date-picker-group">
                <label class="filter-item-lbl"><i class="fas fa-calendar-day text-green"></i> Kustom Tanggal</label>
                <div class="custom-date-row">
                    <input type="hidden" name="range" value="custom">
                    <input type="date" name="start_date" value="{{ $startDate }}" class="date-input">
                    <span style="color:#94a3b8; font-size:12px;">s/d</span>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="date-input">
                    <button type="submit" class="btn-filter-date" title="Terapkan Filter Tanggal">
                        <i class="fas fa-magnifying-glass"></i>
                    </button>
                </div>
            </div>

        </form>

        {{-- Right Action Buttons --}}
        <div class="toolbar-actions-footer">
            <div class="active-badge-indicator">
                <i class="fas fa-id-badge text-amber"></i> Laporan Aktif: 
                <strong>{{ $selectedStaff ? $selectedStaff->name : 'Semua Staff & Admin' }}</strong>
                <span class="shift-indicator">({{ $selectedShift === 'shift1' ? 'Shift 1 Siang (10:00 - 16:00)' : ($selectedShift === 'shift2' ? 'Shift 2 Sore (16:00 - 22:30)' : ($selectedShift === 'shift_izin' ? 'Shift 1 Izin (Buka 16:00 - 22:30)' : 'Semua Jam Buka (10:00 - 22:30)')) }})</span>
            </div>

            <div class="btn-action-cluster">
                <a href="{{ route('admin.expenses.index') }}" class="btn-action-outline" title="Kelola Pengeluaran">
                    <i class="fas fa-file-invoice-dollar text-red"></i> Menu Pengeluaran
                </a>

                <a href="{{ route('admin.stats.print', request()->query()) }}" target="_blank" class="btn-action-outline" title="Cetak Langsung">
                    <i class="fas fa-print"></i> Cetak Struk
                </a>

                <a href="{{ route('admin.stats.pdf', request()->query()) }}" class="btn-primary-custom" title="Unduh File PDF Laporan">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </a>
            </div>
        </div>
    </div>

    {{-- 2. Financial Metrics Summary Cards --}}
    <div class="metrics-grid">
        {{-- Card 1: Total Omzet Penjualan --}}
        <div class="metric-card bg-white">
            <div class="metric-icon-wrap bg-amber-soft text-amber">
                <i class="fas fa-cash-register"></i>
            </div>
            <div class="metric-content">
                <span class="metric-lbl">Total Omzet ({{ $countTotal ?? 0 }} Pembeli)</span>
                <h3 class="metric-val">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</h3>
                <div class="metric-sub-info">
                    <span>Cash: <strong>Rp {{ number_format($totalCash, 0, ',', '.') }} ({{ $countCash ?? 0 }} Pembeli)</strong></span>
                    <span>•</span>
                    <span>QRIS: <strong>Rp {{ number_format($totalQris, 0, ',', '.') }} ({{ $countQris ?? 0 }} Pembeli)</strong></span>
                </div>
            </div>
        </div>

        {{-- Card 2: Total HPP & Laba Kotor --}}
        <div class="metric-card bg-white">
            <div class="metric-icon-wrap bg-purple-soft text-purple">
                <i class="fas fa-boxes-packing"></i>
            </div>
            <div class="metric-content">
                <span class="metric-lbl">Total HPP & Laba Kotor</span>
                <h3 class="metric-val text-purple">Rp {{ number_format($totalHpp, 0, ',', '.') }}</h3>
                <div class="metric-sub-info">
                    <span>Laba Kotor: <strong class="text-green">Rp {{ number_format($labaKotor, 0, ',', '.') }}</strong></span>
                    <span>•</span>
                    <span>Margin: <strong>{{ $marginLabaKotor ?? 0 }}%</strong></span>
                </div>
            </div>
        </div>

        {{-- Card 3: Total Pengeluaran --}}
        <div class="metric-card bg-white">
            <div class="metric-icon-wrap bg-red-soft text-red">
                <i class="fas fa-arrow-trend-down"></i>
            </div>
            <div class="metric-content">
                <span class="metric-lbl">Pengeluaran Operasional</span>
                <h3 class="metric-val text-red">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</h3>
                <div class="metric-sub-info">
                    <span>{{ $expenses->count() }} Catatan Pengeluaran Shift</span>
                </div>
            </div>
        </div>

        {{-- Card 4: Pendapatan Bersih (Net Profit) --}}
        <div class="metric-card bg-white border-green-glow">
            <div class="metric-icon-wrap bg-green-soft text-green">
                <i class="fas fa-sack-dollar"></i>
            </div>
            <div class="metric-content">
                <span class="metric-lbl">Pendapatan Bersih (Net Profit)</span>
                <h3 class="metric-val text-green">Rp {{ number_format($labaBersih, 0, ',', '.') }}</h3>
                <div class="metric-sub-info">
                    <span class="text-green font-bold">Laba Kotor - Pengeluaran</span>
                </div>
            </div>
        </div>
    </div>

    {{-- 2.5. Section: Top 5 Menu Paling Menghasilkan Keuntungan (Laba Tertinggi) --}}
    <div class="card table-report-card" style="margin-bottom: 25px;">
        <div class="card-header flex-between" style="background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%); border-bottom: 1px solid #fde68a;">
            <div class="card-title" style="color: #92400e; font-weight: 800;">
                <i class="fas fa-trophy text-amber"></i> Top 5 Menu Paling Menghasilkan Keuntungan (Laba Tertinggi)
            </div>
            <span class="badge-total-category" style="background:#fef3c7; color:#b45309; border:1px solid #fcd34d;">
                Rata-rata Margin Toko: <strong>{{ $marginLabaKotor ?? 0 }}%</strong>
            </span>
        </div>
        <div class="table-responsive">
            <table class="tbl custom-report-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">Rank</th>
                        <th>Menu / Produk</th>
                        <th style="text-align: center;">Porsi Terjual</th>
                        <th style="text-align: right;">Total Omzet</th>
                        <th style="text-align: right;">Total HPP (Modal)</th>
                        <th style="text-align: right;">Keuntungan (Laba Kotor)</th>
                        <th style="text-align: center;">Margin (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProfitableMenus as $idx => $menuProfit)
                        @php
                            $marginMenuPct = ($menuProfit->total_revenue > 0) 
                                ? round(($menuProfit->total_profit / $menuProfit->total_revenue) * 100, 1) 
                                : 0;
                        @endphp
                        <tr>
                            <td>
                                @if($idx === 0)
                                    <span class="badge" style="background:#fef08a; color:#854d0e; font-weight:800; border-radius:50%; width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center;">🥇</span>
                                @elseif($idx === 1)
                                    <span class="badge" style="background:#e2e8f0; color:#475569; font-weight:800; border-radius:50%; width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center;">🥈</span>
                                @elseif($idx === 2)
                                    <span class="badge" style="background:#fed7aa; color:#9a3412; font-weight:800; border-radius:50%; width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center;">🥉</span>
                                @else
                                    <span style="font-weight:700; color:#64748b; padding-left:8px;">#{{ $idx + 1 }}</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    @if($menuProfit->foto)
                                        <img src="{{ asset('storage/' . $menuProfit->foto) }}" alt="{{ $menuProfit->nama_menu }}" style="width:36px; height:36px; border-radius:8px; object-fit:cover; border:1px solid #e2e8f0;">
                                    @else
                                        <div style="width:36px; height:36px; border-radius:8px; background:#fef3c7; color:#b45309; display:flex; align-items:center; justify-content:center; font-size:16px;">☕</div>
                                    @endif
                                    <div>
                                        <strong style="color:#1e293b; font-size:13px;">{{ $menuProfit->nama_menu }}</strong>
                                        <div style="font-size:11px; color:#64748b;">Harga Jual: Rp {{ number_format($menuProfit->harga, 0, ',', '.') }} | HPP: Rp {{ number_format($menuProfit->hpp ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span class="qty-badge" style="font-size:12px; font-weight:700; background:#f1f5f9; padding:3px 8px; border-radius:6px;">{{ $menuProfit->total_qty }} Unit</span>
                            </td>
                            <td style="text-align: right; color:#334155;">
                                Rp {{ number_format($menuProfit->total_revenue, 0, ',', '.') }}
                            </td>
                            <td style="text-align: right; color:#7c3aed;">
                                Rp {{ number_format($menuProfit->total_cogs, 0, ',', '.') }}
                            </td>
                            <td style="text-align: right;">
                                <strong style="color:#15803d; font-size:13px;">+Rp {{ number_format($menuProfit->total_profit, 0, ',', '.') }}</strong>
                            </td>
                            <td style="text-align: center;">
                                <span style="background:#dcfce7; color:#166534; padding:3px 8px; border-radius:12px; font-weight:700; font-size:11px;">
                                    {{ $marginMenuPct }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state-row">Belum ada data transaksi menu pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 3. Table 1: Laporan Penjualan F&B - Tunai (Cash) --}}
    <div class="card table-report-card">
        <div class="card-header flex-between">
            <div class="card-title">
                <i class="fas fa-money-bill-wave text-amber"></i> Laporan F&B - Tunai (Cash)
            </div>
            <span class="badge-total-category cash">Total Cash: Rp {{ number_format($totalCash, 0, ',', '.') }} ({{ $countCash ?? 0 }} Pembeli)</span>
        </div>
        <div class="table-responsive">
            <table class="tbl custom-report-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Waktu Transaksi</th>
                        <th>Pelanggan</th>
                        <th>Detail Pesanan (Menu & Sisa Stok)</th>
                        <th>Kasir / Shift</th>
                        <th>Metode</th>
                        <th style="text-align: right;">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cashOrders as $index => $order)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="order-time">{{ $order->tanggal ? $order->tanggal->format('d/m/Y H:i') : ($order->created_at ? $order->created_at->format('d/m/Y H:i') : '-') }}</span>
                            </td>
                            <td>
                                <strong class="cust-name">{{ $order->nama_pelanggan ?: 'Pelanggan Umum' }}</strong>
                            </td>
                            <td>
                                <ul class="product-item-list">
                                    @foreach($order->items as $item)
                                        <li>
                                            <span class="prod-bullet">•</span> <strong>{{ $item->menu?->nama_menu ?? 'Menu' }}</strong> <span class="qty-badge">(x{{ $item->quantity }})</span>
                                            @if(isset($item->menu?->stok))
                                                <span class="stock-sub">- Sisa Stok: {{ $item->menu->stok }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <span class="cashier-name"><i class="fas fa-user-circle"></i> {{ $order->user?->name ?: 'Gina (Kasir)' }}</span>
                            </td>
                            <td>
                                <span class="badge-method cash">CASH</span>
                            </td>
                            <td style="text-align: right;">
                                <strong class="order-amount">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state-row">Tidak ada transaksi Cash untuk filter akun/shift ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="tfoot-highlight cash">
                        <td colspan="6" style="text-align: right; font-weight: 800;">TOTAL PENDAPATAN F&B CASH ({{ $countCash ?? 0 }} Transaksi / Pembeli):</td>
                        <td style="text-align: right; font-weight: 800;">Rp {{ number_format($totalCash, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- 4. Table 2: Laporan Penjualan F&B - QRIS --}}
    <div class="card table-report-card">
        <div class="card-header flex-between">
            <div class="card-title">
                <i class="fas fa-qrcode text-blue"></i> Laporan F&B - QRIS
            </div>
            <span class="badge-total-category qris">Total QRIS: Rp {{ number_format($totalQris, 0, ',', '.') }} ({{ $countQris ?? 0 }} Pembeli)</span>
        </div>
        <div class="table-responsive">
            <table class="tbl custom-report-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Waktu Transaksi</th>
                        <th>Pelanggan</th>
                        <th>Detail Pesanan (Menu & Sisa Stok)</th>
                        <th>Kasir / Shift</th>
                        <th>Metode</th>
                        <th style="text-align: right;">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($qrisOrders as $index => $order)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="order-time">{{ $order->tanggal ? $order->tanggal->format('d/m/Y H:i') : ($order->created_at ? $order->created_at->format('d/m/Y H:i') : '-') }}</span>
                            </td>
                            <td>
                                <strong class="cust-name">{{ $order->nama_pelanggan ?: 'Pelanggan Umum' }}</strong>
                            </td>
                            <td>
                                <ul class="product-item-list">
                                    @foreach($order->items as $item)
                                        <li>
                                            <span class="prod-bullet">•</span> <strong>{{ $item->menu?->nama_menu ?? 'Menu' }}</strong> <span class="qty-badge">(x{{ $item->quantity }})</span>
                                            @if(isset($item->menu?->stok))
                                                <span class="stock-sub">- Sisa Stok: {{ $item->menu->stok }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <span class="cashier-name"><i class="fas fa-user-circle"></i> {{ $order->user?->name ?: 'Gina (Kasir)' }}</span>
                            </td>
                            <td>
                                <span class="badge-method qris">QRIS</span>
                            </td>
                            <td style="text-align: right;">
                                <strong class="order-amount">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state-row">Tidak ada transaksi QRIS untuk filter akun/shift ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="tfoot-highlight qris">
                        <td colspan="6" style="text-align: right; font-weight: 800;">TOTAL PENDAPATAN F&B QRIS ({{ $countQris ?? 0 }} Transaksi / Pembeli):</td>
                        <td style="text-align: right; font-weight: 800;">Rp {{ number_format($totalQris, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- 5. Table 3: Laporan Pengeluaran Operasional --}}
    <div class="card table-report-card">
        <div class="card-header flex-between">
            <div class="card-title">
                <i class="fas fa-file-invoice-dollar text-red"></i> Laporan Pengeluaran Shift
            </div>
            <a href="{{ route('admin.expenses.index') }}" class="btn-action-outline" style="font-size: 12px; padding: 6px 12px;">
                <i class="fas fa-external-link-alt text-amber"></i> Buka Menu Pengeluaran
            </a>
        </div>
        <div class="table-responsive">
            <table class="tbl custom-report-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Waktu / Tanggal</th>
                        <th>Deskripsi & Keperluan Pengeluaran</th>
                        <th>Operator / Kasir</th>
                        <th style="text-align: right;">Nominal (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $index => $expense)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="order-time">{{ $expense->tanggal ? $expense->tanggal->format('d/m/Y H:i') : '-' }}</span>
                            </td>
                            <td>
                                <strong class="expense-desc">{{ $expense->deskripsi }}</strong>
                            </td>
                            <td>
                                <span class="cashier-name">{{ $expense->operator ?: 'Kasir' }}</span>
                            </td>
                            <td style="text-align: right;">
                                <strong class="expense-amount">Rp {{ number_format($expense->nominal, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state-row">Belum ada catatan pengeluaran operasional untuk shift ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="tfoot-highlight expense">
                        <td colspan="4" style="text-align: right; font-weight: 800;">TOTAL PENGELUARAN:</td>
                        <td style="text-align: right; font-weight: 800; color: #dc2626;">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- 6. Net Profit Calculation Summary Box --}}
    <div class="card net-profit-statement-card">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-scale-balanced text-green"></i> Ringkasan Laba Rugi & Pendapatan Bersih (Net Profit)
            </div>
        </div>
        <div class="statement-body">
            <div class="statement-row">
                <div class="statement-label">
                    <i class="fas fa-cart-shopping text-amber"></i> Total Omzet Penjualan ({{ $countTotal ?? 0 }} Transaksi: {{ $countCash ?? 0 }} Cash, {{ $countQris ?? 0 }} QRIS)
                </div>
                <div class="statement-val text-dark">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</div>
            </div>

            <div class="statement-row">
                <div class="statement-label">
                    <i class="fas fa-boxes-stacked text-purple"></i> Total HPP (Harga Pokok Penjualan Bahan Baku)
                </div>
                <div class="statement-val text-red">- Rp {{ number_format($totalHpp, 0, ',', '.') }}</div>
            </div>

            <div class="statement-row subtotal-row">
                <div class="statement-label">
                    <strong>Estimasi Laba Kotor (Omzet - HPP)</strong>
                </div>
                <div class="statement-val text-green font-bold">Rp {{ number_format($labaKotor, 0, ',', '.') }}</div>
            </div>

            <div class="statement-row">
                <div class="statement-label">
                    <i class="fas fa-receipt text-red"></i> Total Biaya Pengeluaran Operasional
                </div>
                <div class="statement-val text-red">- Rp {{ number_format($totalExpenses, 0, ',', '.') }}</div>
            </div>

            <div class="statement-row final-net-profit">
                <div class="statement-label">
                    <i class="fas fa-trophy"></i> PENDAPATAN BERSIH (NET PROFIT SHIFT)
                </div>
                <div class="statement-val">Rp {{ number_format($labaBersih, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

</div>

<style>
    .stats-financial-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
        max-width: 100%;
    }

    .alert-success-custom {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
        padding: 14px 20px;
        border-radius: 16px;
        font-weight: 700;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* 1. Toolbar */
    .report-toolbar-card {
        background: #ffffff;
        border: 1px solid rgba(107, 63, 31, 0.1);
        border-radius: 20px;
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        gap: 18px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }

    .toolbar-filter-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        align-items: flex-end;
    }

    .filter-item-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .filter-item-lbl {
        font-size: 11.5px;
        font-weight: 700;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .filter-select {
        width: 100%;
        padding: 9px 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-size: 12.5px;
        font-weight: 600;
        color: #1e293b;
        outline: none;
        transition: border-color 0.15s ease;
    }

    .filter-select:focus {
        border-color: #D4752C;
    }

    .period-tabs-inline {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
    }

    .tab-chip {
        display: inline-flex;
        align-items: center;
        padding: 8px 11px;
        border-radius: 10px;
        font-size: 11.5px;
        font-weight: 700;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #64748b;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .tab-chip:hover {
        background: #fef3c7;
        color: #b45309;
    }

    .tab-chip.active {
        background: linear-gradient(135deg, #D4752C 0%, #B45309 100%);
        color: #ffffff;
        border-color: #D4752C;
        box-shadow: 0 4px 12px rgba(212, 117, 44, 0.25);
    }

    .custom-date-row {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .date-input {
        width: 100%;
        padding: 7px 10px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 11.5px;
        font-weight: 600;
        color: #334155;
        outline: none;
    }

    .btn-filter-date {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #475569;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .btn-filter-date:hover {
        background: #D4752C;
        color: #ffffff;
    }

    .toolbar-actions-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #f1f5f9;
        padding-top: 14px;
        flex-wrap: wrap;
        gap: 14px;
    }

    .active-badge-indicator {
        font-size: 13px;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .shift-indicator {
        color: #D4752C;
        font-weight: 700;
    }

    .btn-action-cluster {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-action-outline {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        border-radius: 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .btn-action-outline:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    .btn-primary-custom {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #D4752C 0%, #B45309 100%);
        color: #ffffff;
        padding: 10px 20px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(212, 117, 44, 0.25);
    }

    .btn-primary-custom:hover {
        box-shadow: 0 6px 18px rgba(212, 117, 44, 0.35);
    }

    /* 2. Metrics Grid */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
    }

    .metric-card {
        border: 1px solid rgba(107, 63, 31, 0.1);
        border-radius: 18px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }

    .border-green-glow {
        border: 1.5px solid #a7f3d0 !important;
        background: #f0fdf4 !important;
    }

    .metric-icon-wrap {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .bg-amber-soft { background: #fef3c7; }
    .bg-purple-soft { background: #f3e8ff; }
    .bg-red-soft { background: #fee2e2; }
    .bg-green-soft { background: #dcfce7; }

    .text-amber { color: #D4752C; }
    .text-purple { color: #9333ea; }
    .text-red { color: #dc2626; }
    .text-green { color: #059669; }
    .text-blue { color: #2563eb; }

    .metric-content {
        display: flex;
        flex-direction: column;
    }

    .metric-lbl {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .metric-val {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        line-height: 1.2;
    }

    .metric-sub-info {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* 3. Table Reports */
    .table-report-card {
        background: #ffffff;
        border: 1px solid rgba(107, 63, 31, 0.1);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,0.04);
    }

    .card-header {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
    }

    .flex-between {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title {
        font-size: 15px;
        font-weight: 800;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .badge-total-category {
        font-size: 12px;
        font-weight: 800;
        padding: 5px 14px;
        border-radius: 999px;
    }

    .badge-total-category.cash { background: #fef3c7; color: #b45309; }
    .badge-total-category.qris { background: #eff6ff; color: #1d4ed8; }

    .btn-add-expense-inline {
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fca5a5;
        padding: 6px 14px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-add-expense-inline:hover {
        background: #dc2626;
        color: #ffffff;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .custom-report-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .custom-report-table th {
        padding: 14px 20px;
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }

    .custom-report-table td {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .custom-report-table tr:hover td {
        background: #fffaf5;
    }

    .order-time {
        color: #64748b;
        font-size: 12px;
        white-space: nowrap;
    }

    .cust-name {
        color: #0f172a;
        font-size: 13px;
    }

    .product-item-list {
        margin: 0;
        padding: 0;
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .prod-bullet { color: #D4752C; }
    .qty-badge { color: #0284c7; font-weight: 700; }
    .stock-sub { color: #94a3b8; font-size: 11px; margin-left: 4px; }

    .cashier-name {
        color: #475569;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-method {
        font-size: 10px;
        font-weight: 800;
        padding: 4px 8px;
        border-radius: 6px;
    }

    .badge-method.cash { background: #fef3c7; color: #b45309; }
    .badge-method.qris { background: #eff6ff; color: #1d4ed8; }

    .order-amount {
        color: #0f172a;
        font-size: 13px;
        font-weight: 800;
        white-space: nowrap;
    }

    .tfoot-highlight {
        background: #fffbeb;
        font-size: 13px;
    }

    .tfoot-highlight.cash td { color: #92400e; padding: 14px 20px; }
    .tfoot-highlight.qris td { color: #1e40af; background: #eff6ff; padding: 14px 20px; }
    .tfoot-highlight.expense td { color: #991b1b; background: #fef2f2; padding: 14px 20px; }

    .empty-state-row {
        text-align: center;
        padding: 28px 20px;
        color: #94a3b8;
        font-style: italic;
    }

    .btn-delete-expense {
        background: #fee2e2;
        color: #dc2626;
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 8px;
        cursor: pointer;
    }

    .btn-delete-expense:hover {
        background: #dc2626;
        color: #ffffff;
    }

    .badge-category-expense {
        font-size: 11px;
        font-weight: 700;
        background: #f1f5f9;
        color: #475569;
        padding: 4px 10px;
        border-radius: 8px;
    }

    /* 6. Statement Box */
    .net-profit-statement-card {
        background: #ffffff;
        border: 2px solid #a7f3d0;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 6px 30px rgba(0,0,0,0.04);
    }

    .statement-body {
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .statement-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 14px;
    }

    .statement-label {
        font-weight: 600;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .statement-val {
        font-size: 15px;
        font-weight: 800;
    }

    .subtotal-row {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .final-net-profit {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: #ffffff;
        padding: 16px 20px;
        border-radius: 14px;
        font-size: 16px;
        font-weight: 800;
        box-shadow: 0 4px 16px rgba(5, 150, 105, 0.3);
    }

    .final-net-profit .statement-label {
        color: #ffffff;
    }

    .final-net-profit .statement-val {
        font-size: 20px;
        color: #ffffff;
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(4px);
    }

    .modal-card {
        background: #ffffff;
        border-radius: 20px;
        width: 100%;
        max-width: 480px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        overflow: hidden;
    }

    .modal-header {
        padding: 18px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-title {
        font-size: 16px;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-close-modal {
        background: none;
        border: none;
        font-size: 24px;
        color: #94a3b8;
        cursor: pointer;
    }

    .modal-body {
        padding: 22px 24px;
    }

    .form-lbl {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 6px;
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        font-size: 13px;
        outline: none;
        box-sizing: border-box;
    }

    .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background: #f8fafc;
    }

    .btn-cancel {
        padding: 10px 20px;
        border-radius: 12px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 22px;
        border-radius: 12px;
        background: linear-gradient(135deg, #D4752C 0%, #B45309 100%);
        color: #ffffff;
        font-size: 13px;
        font-weight: 800;
        border: none;
        cursor: pointer;
    }

    @media (max-width: 1100px) {
        .toolbar-filter-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .metrics-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .toolbar-filter-grid {
            grid-template-columns: 1fr;
        }
        .metrics-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

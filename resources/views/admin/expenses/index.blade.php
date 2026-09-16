@extends('dashboard')

@section('page-title', 'Manajemen Pengeluaran Cafe')
@section('breadcrumb', 'Laporan Pengeluaran')

@section('content')
<div class="expenses-page-wrap">

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="alert-success-custom">
            <i class="fas fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    {{-- 1. Summary Cards --}}
    <div class="expense-metrics-grid">
        <div class="expense-stat-box">
            <div class="stat-icon-wrap bg-amber-soft text-amber">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div>
                <span class="stat-box-lbl">Pengeluaran Hari Ini</span>
                <h4 class="stat-box-val">Rp {{ number_format($todayExpenses, 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="expense-stat-box">
            <div class="stat-icon-wrap bg-blue-soft text-blue">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div>
                <span class="stat-box-lbl">Pengeluaran Minggu Ini</span>
                <h4 class="stat-box-val">Rp {{ number_format($thisWeekExpenses, 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="expense-stat-box">
            <div class="stat-icon-wrap bg-purple-soft text-purple">
                <i class="fas fa-calendar-days"></i>
            </div>
            <div>
                <span class="stat-box-lbl">Pengeluaran Bulan Ini</span>
                <h4 class="stat-box-val">Rp {{ number_format($thisMonthExpenses, 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="expense-stat-box border-red-glow">
            <div class="stat-icon-wrap bg-red-soft text-red">
                <i class="fas fa-receipt"></i>
            </div>
            <div>
                <span class="stat-box-lbl">Total Sesuai Filter</span>
                <h4 class="stat-box-val text-red">Rp {{ number_format($filteredTotal, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    {{-- 2. Filter & Action Toolbar --}}
    <div class="card expense-toolbar-card">
        <form method="GET" action="{{ route('admin.expenses.index') }}" id="filterExpenseForm" class="toolbar-filter-grid">
            
            {{-- Staff Selector --}}
            <div class="filter-item-group">
                <label class="filter-item-lbl"><i class="fas fa-user-tie text-amber"></i> Akun Kasir / Staff</label>
                <select name="staff_id" class="filter-select" onchange="document.getElementById('filterExpenseForm').submit()">
                    @if($isAdmin)
                        <option value="all" {{ ($selectedStaffId ?? 'all') === 'all' ? 'selected' : '' }}>-- Semua Staff & Admin --</option>
                    @endif
                    @foreach($staffList as $staff)
                        <option value="{{ $staff->id_user }}" {{ ($selectedStaffId ?? '') == $staff->id_user ? 'selected' : '' }}>
                            {{ $staff->name }} ({{ $staff->role?->role_name ?? 'Staff' }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Shift Selector --}}
            <div class="filter-item-group">
                <label class="filter-item-lbl"><i class="fas fa-business-time text-blue"></i> Pilihan Shift</label>
                <select name="shift" class="filter-select" onchange="document.getElementById('filterExpenseForm').submit()">
                    <option value="all" {{ ($selectedShift ?? 'all') === 'all' ? 'selected' : '' }}>Semua Jam Buka (10:00 - 22:30)</option>
                    <option value="shift1" {{ ($selectedShift ?? '') === 'shift1' ? 'selected' : '' }}>Shift 1 Siang (10:00 - 16:00 / 1 Staff)</option>
                    <option value="shift2" {{ ($selectedShift ?? '') === 'shift2' ? 'selected' : '' }}>Shift 2 Sore (16:00 - 22:30 / 2 Staff)</option>
                    <option value="shift_izin" {{ ($selectedShift ?? '') === 'shift_izin' ? 'selected' : '' }}>Shift Khusus (1 Staff Izin / 16:00 - 22:30)</option>
                </select>
            </div>

            {{-- Period Chips --}}
            <div class="filter-item-group">
                <label class="filter-item-lbl"><i class="fas fa-calendar-alt text-purple"></i> Periode</label>
                <div class="period-tabs-inline">
                    <a href="{{ route('admin.expenses.index', array_merge(request()->query(), ['range' => 'today'])) }}" class="tab-chip {{ ($range ?? '') === 'today' ? 'active' : '' }}">Hari Ini</a>
                    <a href="{{ route('admin.expenses.index', array_merge(request()->query(), ['range' => 'yesterday'])) }}" class="tab-chip {{ ($range ?? '') === 'yesterday' ? 'active' : '' }}">Kemarin</a>
                    <a href="{{ route('admin.expenses.index', array_merge(request()->query(), ['range' => 'week'])) }}" class="tab-chip {{ ($range ?? '') === 'week' ? 'active' : '' }}">Minggu Ini</a>
                    <a href="{{ route('admin.expenses.index', array_merge(request()->query(), ['range' => 'month'])) }}" class="tab-chip {{ ($range ?? '') === 'month' ? 'active' : '' }}">Bulan Ini</a>
                    <a href="{{ route('admin.expenses.index', array_merge(request()->query(), ['range' => 'all'])) }}" class="tab-chip {{ ($range ?? '') === 'all' ? 'active' : '' }}">Semua</a>
                </div>
            </div>

            {{-- Date Range --}}
            <div class="filter-item-group date-picker-group">
                <label class="filter-item-lbl"><i class="fas fa-calendar-day text-green"></i> Kustom Tanggal</label>
                <div class="custom-date-row">
                    <input type="hidden" name="range" value="custom">
                    <input type="date" name="start_date" value="{{ $startDate ?? date('Y-m-d') }}" class="date-input">
                    <span style="color:#94a3b8; font-size:12px;">s/d</span>
                    <input type="date" name="end_date" value="{{ $endDate ?? date('Y-m-d') }}" class="date-input">
                    <button type="submit" class="btn-filter-date" title="Terapkan Filter">
                        <i class="fas fa-magnifying-glass"></i>
                    </button>
                </div>
            </div>

        </form>

        <div class="toolbar-actions-footer">
            <div class="active-badge-indicator">
                <i class="fas fa-filter text-amber"></i> Laporan: 
                <strong>{{ $selectedStaff ? $selectedStaff->name : 'Semua Staff & Admin' }}</strong>
                <span class="shift-indicator">({{ ($selectedShift ?? '') === 'shift1' ? 'Shift 1 Siang' : (($selectedShift ?? '') === 'shift2' ? 'Shift 2 Sore' : (($selectedShift ?? '') === 'shift_izin' ? 'Shift 1 Izin' : 'Semua Shift')) }})</span>
            </div>

            <div class="btn-action-cluster">
                <button type="button" class="btn-primary-custom" onclick="openCreateModal()">
                    <i class="fas fa-plus"></i> Catat Pengeluaran Baru
                </button>

                <a href="{{ route('admin.expenses.pdf', request()->query()) }}" class="btn-action-outline" title="Unduh PDF Laporan Pengeluaran">
                    <i class="fas fa-file-pdf text-red"></i> Download PDF
                </a>
            </div>
        </div>
    </div>

    {{-- 3. Expenses Table --}}
    <div class="card expenses-table-card">
        <div class="card-header flex-between">
            <div class="card-title">
                <i class="fas fa-receipt text-red"></i> Rincian Pengeluaran Operasional ({{ $expenses->count() }})
            </div>
            <span class="record-badge">Total: Rp {{ number_format($filteredTotal, 0, ',', '.') }}</span>
        </div>

        <div class="table-responsive">
            <table class="tbl custom-expense-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Waktu & Tanggal</th>
                        <th>Deskripsi / Keperluan Pengeluaran</th>
                        <th>Kasir / Operator Shift</th>
                        <th style="text-align: right;">Nominal (Rp)</th>
                        <th style="text-align: center; width: 100px;">Aksi</th>
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
                                <span class="cashier-name"><i class="fas fa-user-circle"></i> {{ $expense->operator ?: ($expense->user?->name ?? 'Kasir') }}</span>
                            </td>
                            <td style="text-align: right;">
                                <strong class="expense-amount">Rp {{ number_format($expense->nominal, 0, ',', '.') }}</strong>
                            </td>
                            <td style="text-align: center;">
                                <div class="btn-group-actions">
                                    <button type="button" class="btn-action edit" onclick="openEditModal({{ $expense->id }}, '{{ addslashes($expense->deskripsi) }}', {{ (int)$expense->nominal }}, '{{ $expense->tanggal ? $expense->tanggal->format('Y-m-d\TH:i') : '' }}', '{{ addslashes($expense->operator) }}')" title="Edit">
                                        <i class="fas fa-pen-to-square"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.expenses.destroy', $expense->id) }}" style="display:inline;" onsubmit="return confirm('Hapus catatan pengeluaran ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action delete" title="Hapus">
                                            <i class="fas fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state-row">Belum ada catatan pengeluaran untuk filter yang dipilih.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="tfoot-highlight">
                        <td colspan="4" style="text-align: right; font-weight: 800;">TOTAL PENGELUARAN:</td>
                        <td style="text-align: right; font-weight: 800; color: #dc2626;">Rp {{ number_format($filteredTotal, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

{{-- MODAL TAMBAH PENGELUARAN --}}
<div id="createExpenseModal" class="modal-overlay" style="display: none;">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-receipt text-red"></i> Catat Pengeluaran Baru</h3>
            <button type="button" class="btn-close-modal" onclick="closeCreateModal()">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.expenses.store') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-lbl">Tanggal & Waktu <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="tanggal" value="{{ date('Y-m-d\TH:i') }}" class="form-control" required>
                </div>

                <div class="form-group" style="margin-top: 14px;">
                    <label class="form-lbl">Deskripsi / Keperluan Pengeluaran <span class="text-danger">*</span></label>
                    <input type="text" name="deskripsi" class="form-control" placeholder="Contoh: Beli Es Batu Kristal 2 Karung, Gas LPG, Cup..." required>
                </div>

                <div class="form-group" style="margin-top: 14px;">
                    <label class="form-lbl">Nominal Pengeluaran (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="nominal" class="form-control" placeholder="25000" min="0" required>
                </div>

                <div class="form-group" style="margin-top: 14px;">
                    <label class="form-lbl">Nama Kasir / Operator Shift</label>
                    <input type="text" name="operator" value="{{ auth()->user()?->name ? auth()->user()->name . ' (Kasir)' : 'Robin (Kasir Shift 1)' }}" class="form-control" placeholder="Nama pencatat">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeCreateModal()">Batal</button>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-check"></i> Simpan Pengeluaran
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT PENGELUARAN --}}
<div id="editExpenseModal" class="modal-overlay" style="display: none;">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-pen-to-square text-amber"></i> Edit Catatan Pengeluaran</h3>
            <button type="button" class="btn-close-modal" onclick="closeEditModal()">&times;</button>
        </div>
        <form method="POST" id="editExpenseForm" action="">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-lbl">Tanggal & Waktu <span class="text-danger">*</span></label>
                    <input type="datetime-local" id="edit_tanggal" name="tanggal" class="form-control" required>
                </div>

                <div class="form-group" style="margin-top: 14px;">
                    <label class="form-lbl">Deskripsi / Keperluan Pengeluaran <span class="text-danger">*</span></label>
                    <input type="text" id="edit_deskripsi" name="deskripsi" class="form-control" required>
                </div>

                <div class="form-group" style="margin-top: 14px;">
                    <label class="form-lbl">Nominal Pengeluaran (Rp) <span class="text-danger">*</span></label>
                    <input type="number" id="edit_nominal" name="nominal" class="form-control" min="0" required>
                </div>

                <div class="form-group" style="margin-top: 14px;">
                    <label class="form-lbl">Nama Kasir / Operator Shift</label>
                    <input type="text" id="edit_operator" name="operator" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-check"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .expenses-page-wrap {
        display: flex;
        flex-direction: column;
        gap: 22px;
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

    /* Metrics Grid */
    .expense-metrics-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
    }

    .expense-stat-box {
        background: #ffffff;
        border: 1px solid rgba(107, 63, 31, 0.1);
        border-radius: 18px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }

    .border-red-glow {
        border: 1.5px solid #fca5a5 !important;
        background: #fff5f5 !important;
    }

    .stat-icon-wrap {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .bg-amber-soft { background: #fef3c7; }
    .bg-blue-soft { background: #eff6ff; }
    .bg-purple-soft { background: #f3e8ff; }
    .bg-red-soft { background: #fee2e2; }

    .text-amber { color: #D4752C; }
    .text-blue { color: #2563eb; }
    .text-purple { color: #9333ea; }
    .text-red { color: #dc2626; }

    .stat-box-lbl {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
        display: block;
        margin-bottom: 2px;
    }

    .stat-box-val {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        line-height: 1.2;
    }

    /* Toolbar */
    .expense-toolbar-card {
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
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(212, 117, 44, 0.25);
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
        text-decoration: none;
    }

    /* Table */
    .expenses-table-card {
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

    .record-badge {
        font-size: 12px;
        font-weight: 800;
        color: #dc2626;
        background: #fee2e2;
        padding: 5px 14px;
        border-radius: 999px;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .custom-expense-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .custom-expense-table th {
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

    .custom-expense-table td {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .custom-expense-table tr:hover td {
        background: #fffaf5;
    }

    .expense-desc {
        color: #0f172a;
        font-size: 14px;
    }

    .cashier-name {
        color: #475569;
        font-size: 12px;
        font-weight: 600;
    }

    .expense-amount {
        color: #dc2626;
        font-size: 14px;
        font-weight: 800;
        white-space: nowrap;
    }

    .tfoot-highlight {
        background: #fef2f2;
        font-size: 13px;
    }

    .tfoot-highlight td {
        padding: 16px 20px;
    }

    .btn-group-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        font-size: 13px;
    }

    .btn-action.edit {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-action.edit:hover {
        background: #D4752C;
        color: #ffffff;
    }

    .btn-action.delete {
        background: #fee2e2;
        color: #dc2626;
    }

    .btn-action.delete:hover {
        background: #dc2626;
        color: #ffffff;
    }

    .empty-state-row {
        text-align: center;
        padding: 36px 20px;
        color: #94a3b8;
        font-style: italic;
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
        .expense-metrics-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .toolbar-filter-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .expense-metrics-grid {
            grid-template-columns: 1fr;
        }
        .toolbar-filter-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
function openCreateModal() {
    document.getElementById('createExpenseModal').style.display = 'flex';
}
function closeCreateModal() {
    document.getElementById('createExpenseModal').style.display = 'none';
}
function openEditModal(id, desc, nominal, tanggal, operator) {
    document.getElementById('editExpenseForm').action = '/admin/expenses/' + id;
    document.getElementById('edit_deskripsi').value = desc;
    document.getElementById('edit_nominal').value = nominal;
    document.getElementById('edit_tanggal').value = tanggal;
    document.getElementById('edit_operator').value = operator;
    document.getElementById('editExpenseModal').style.display = 'flex';
}
function closeEditModal() {
    document.getElementById('editExpenseModal').style.display = 'none';
}
</script>
@endsection

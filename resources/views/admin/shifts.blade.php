@extends('dashboard')

@section('page-title', 'Monitoring Shift Kasir')
@section('breadcrumb', 'Operasional & Rekapitulasi Multi-Kasir')

@section('content')
<div class="space-y-6">

    {{-- Flash Alerts --}}
    @if(session('success'))
        <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center justify-between shadow-2xs">
            <div class="flex items-center space-x-2">
                <i class="fas fa-circle-check text-emerald-600 text-sm"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-xs flex items-center justify-between shadow-2xs">
            <div class="flex items-center space-x-2">
                <i class="fas fa-triangle-exclamation text-red-600 text-sm"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- 1. Sesi Shift Saya (Khusus Kasir Login) --}}
    @if(auth()->user() && !auth()->user()->isAdmin())
        <div class="bg-gradient-to-r from-stone-900 to-stone-800 text-white p-5 rounded-xl border border-stone-700 shadow-md">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center space-x-3.5">
                    <div class="w-10 h-10 rounded-lg bg-[#C27835] flex items-center justify-center text-white text-base">
                        <i class="fas fa-cash-register"></i>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <h2 class="text-sm font-semibold tracking-wide">Sesi Kasir: {{ auth()->user()->name }}</h2>
                            @if($myActiveShift)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-medium bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 flex items-center space-x-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                                    <span>SHIFT AKTIF (#{{ $myActiveShift->id_shift }})</span>
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-medium bg-stone-700 text-stone-300">
                                    BELUM BUKA SHIFT
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-stone-400 mt-0.5">
                            @if($myActiveShift)
                                Tipe: <strong class="text-stone-200">{{ $myActiveShift->shift_type == 'shift_1' ? 'Shift 1 (Pagi: 07:00 - 15:00)' : 'Shift 2 (Sore: 15:00 - 23:00)' }}</strong> | Dibuka sejak: {{ $myActiveShift->opened_at->format('H:i') }} WIB
                            @else
                                Anda belum membuka shift untuk sesi transaksi hari ini.
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    @if($myActiveShift)
                        <div class="text-right hidden sm:block">
                            <span class="text-[10px] font-mono text-stone-400 block uppercase">Uang Laci Kasir</span>
                            <span class="text-sm font-mono font-bold text-amber-400">Rp {{ number_format($myActiveShift->starting_cash + $myActiveShift->cash_sales, 0, ',', '.') }}</span>
                        </div>
                        <button onclick="openCloseShiftModal({{ $myActiveShift->id_shift }}, '{{ auth()->user()->name }}', {{ $myActiveShift->starting_cash }}, {{ $myActiveShift->cash_sales }})" class="bg-red-600 hover:bg-red-700 text-white text-xs font-mono px-4 py-2 rounded-lg transition-colors shadow-sm flex items-center space-x-1.5">
                            <i class="fas fa-lock text-[11px]"></i>
                            <span>Tutup Shift Saya</span>
                        </button>
                    @else
                        <button onclick="openOpenShiftModal({{ auth()->user()->id_user }})" class="bg-[#C27835] hover:bg-[#A05C22] text-white text-xs font-mono font-medium px-4 py-2 rounded-lg transition-colors shadow-sm flex items-center space-x-1.5">
                            <i class="fas fa-key text-[11px]"></i>
                            <span>Buka Shift Saya</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- 2. Ringkasan Operasional Hari Ini (Shift 1 vs Shift 2 Multi-Active) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Card Shift 1 (Pagi) --}}
        <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-2xs relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-mono font-semibold uppercase tracking-wider text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                    <i class="fas fa-sun text-amber-500 me-1"></i> Shift 1 (07:00 - 15:00)
                </span>
                <span class="text-[10px] font-mono text-stone-500">1 Kasir</span>
            </div>
            <div class="mt-2">
                <div class="text-xs text-stone-500 font-medium">Omzet Shift 1 Hari Ini</div>
                <div class="text-base font-bold font-mono text-stone-900 mt-0.5">Rp {{ number_format($shift1Stats['total_omzet'], 0, ',', '.') }}</div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px] font-mono text-stone-600">
                <span>Tunai: Rp {{ number_format($shift1Stats['total_cash'], 0, ',', '.') }}</span>
                <span>QRIS: Rp {{ number_format($shift1Stats['total_qris'], 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Card Shift 2 (Sore - Multi Active) --}}
        <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-2xs relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-mono font-semibold uppercase tracking-wider text-purple-700 bg-purple-50 px-2 py-0.5 rounded border border-purple-200">
                    <i class="fas fa-moon text-purple-600 me-1"></i> Shift 2 (15:00 - 23:00)
                </span>
                <span class="text-[10px] font-mono text-emerald-600 font-semibold bg-emerald-50 px-1.5 py-0.2 rounded">
                    {{ $shift2Stats['active_count'] }} Kasir Aktif
                </span>
            </div>
            <div class="mt-2">
                <div class="text-xs text-stone-500 font-medium">Omzet Shift 2 (Gabungan)</div>
                <div class="text-base font-bold font-mono text-stone-900 mt-0.5">Rp {{ number_format($shift2Stats['total_omzet'], 0, ',', '.') }}</div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px] font-mono text-stone-600">
                <span>Tunai: Rp {{ number_format($shift2Stats['total_cash'], 0, ',', '.') }}</span>
                <span>QRIS: Rp {{ number_format($shift2Stats['total_qris'], 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Card Total Kas Masuk Hari Ini --}}
        <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-mono font-semibold uppercase tracking-wider text-stone-500">
                    <i class="fas fa-money-bill-wave text-emerald-600 me-1"></i> Total Cash Masuk
                </span>
                <span class="text-[10px] font-mono text-stone-400">Hari Ini</span>
            </div>
            <div class="mt-2">
                <div class="text-xs text-stone-500 font-medium">Seluruh Laci Kasir</div>
                <div class="text-base font-bold font-mono text-emerald-600 mt-0.5">
                    Rp {{ number_format($shift1Stats['total_cash'] + $shift2Stats['total_cash'], 0, ',', '.') }}
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-stone-100 text-[11px] font-mono text-stone-500">
                Total QRIS: Rp {{ number_format($shift1Stats['total_qris'] + $shift2Stats['total_qris'], 0, ',', '.') }}
            </div>
        </div>

        {{-- Card Kasir Aktif Berjalan --}}
        <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-mono font-semibold uppercase tracking-wider text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                    <i class="fas fa-user-clock text-amber-600 me-1"></i> Kasir Bertugas
                </span>
                <span class="text-[10px] font-mono text-stone-500">{{ $activeShifts->count() }} Orang</span>
            </div>
            <div class="mt-2">
                <div class="text-xs text-stone-500 font-medium">Staf Sedang Buka Kasir</div>
                <div class="text-xs font-semibold text-stone-800 mt-1 flex flex-wrap gap-1">
                    @forelse($activeShifts as $ashift)
                        <span class="px-2 py-0.5 rounded bg-stone-100 border border-stone-200 font-mono text-[11px]">
                            {{ $ashift->user->name }} ({{ $ashift->shift_type == 'shift_1' ? 'Shift 1' : 'Shift 2' }})
                        </span>
                    @empty
                        <span class="text-stone-400 text-xs italic">Tidak ada shift yang aktif</span>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    {{-- 3. Live Active Shift Stations (Visual Multi-Kasir Nikita & Dery) --}}
    @if($activeShifts->count() > 0)
        <div class="bg-white rounded-xl border border-stone-200 p-5 shadow-2xs">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h3 class="text-xs font-mono uppercase tracking-wider font-bold text-stone-900">
                        Station Kasir Aktif Real-Time (Multi-Shift)
                    </h3>
                </div>
                <span class="text-[11px] font-mono text-stone-500">Isolasi Drawer & Transaksi Terproteksi</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($activeShifts as $active)
                    <div class="p-4 rounded-xl border {{ $active->shift_type == 'shift_2' ? 'border-purple-200 bg-purple-50/30' : 'border-blue-200 bg-blue-50/30' }} shadow-2xs transition-all hover:shadow-xs">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-lg {{ $active->shift_type == 'shift_2' ? 'bg-purple-600' : 'bg-blue-600' }} text-white flex items-center justify-center font-bold text-sm">
                                    {{ strtoupper(substr($active->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-stone-900">{{ $active->user->name }}</h4>
                                    <div class="flex items-center space-x-1.5 mt-0.5">
                                        <span class="text-[10px] font-mono px-1.5 py-0.2 rounded {{ $active->shift_type == 'shift_2' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $active->shift_type == 'shift_1' ? 'Shift 1 (Pagi)' : 'Shift 2 (Sore)' }}
                                        </span>
                                        <span class="text-[10px] font-mono text-stone-500">ID #{{ $active->id_shift }}</span>
                                    </div>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono bg-emerald-100 text-emerald-700 font-semibold border border-emerald-200">
                                OPEN
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mt-4 p-2.5 rounded-lg bg-white border border-stone-200/80 text-xs font-mono">
                            <div>
                                <span class="text-[10px] text-stone-400 block">Modal Laci Awal</span>
                                <strong class="text-stone-800">Rp {{ number_format($active->starting_cash, 0, ',', '.') }}</strong>
                            </div>
                            <div>
                                <span class="text-[10px] text-stone-400 block">Cash Masuk</span>
                                <strong class="text-emerald-600">Rp {{ number_format($active->cash_sales, 0, ',', '.') }}</strong>
                            </div>
                            <div class="mt-1">
                                <span class="text-[10px] text-stone-400 block">Total di Laci</span>
                                <strong class="text-amber-600">Rp {{ number_format($active->starting_cash + $active->cash_sales, 0, ',', '.') }}</strong>
                            </div>
                            <div class="mt-1">
                                <span class="text-[10px] text-stone-400 block">Pesanan Diproses</span>
                                <strong class="text-stone-800">{{ $active->orders->count() }} Order</strong>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center justify-between gap-2">
                            <button onclick="viewShiftOrders({{ $active->id_shift }})" class="flex-1 text-[11px] font-mono py-1.5 px-2.5 rounded bg-stone-100 hover:bg-stone-200 text-stone-700 border border-stone-200 text-center transition-colors">
                                <i class="fas fa-receipt text-[10px] me-1"></i> Rincian Pesanan
                            </button>
                            <button onclick="openCloseShiftModal({{ $active->id_shift }}, '{{ $active->user->name }}', {{ $active->starting_cash }}, {{ $active->cash_sales }})" class="flex-1 text-[11px] font-mono py-1.5 px-2.5 rounded bg-red-600 hover:bg-red-700 text-white text-center transition-colors">
                                <i class="fas fa-lock text-[10px] me-1"></i> Tutup Kasir
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- 4. Filter & Action Header --}}
    <div class="bg-white rounded-xl border border-stone-200 p-4 shadow-2xs">
        <form action="{{ route('admin.shifts.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <div>
                <label class="block text-[11px] font-mono text-stone-600 mb-1 font-semibold uppercase">Kasir</label>
                <select name="nama_pegawai" class="w-full text-xs font-mono bg-stone-50 border border-stone-200 rounded-lg px-2.5 py-2 focus:outline-none focus:ring-1 focus:ring-[#C27835]">
                    <option value="Semua">Semua Kasir</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->name }}" {{ request('nama_pegawai') == $emp->name ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-mono text-stone-600 mb-1 font-semibold uppercase">Tipe Shift</label>
                <select name="shift_type" class="w-full text-xs font-mono bg-stone-50 border border-stone-200 rounded-lg px-2.5 py-2 focus:outline-none focus:ring-1 focus:ring-[#C27835]">
                    <option value="all">Semua Tipe Shift</option>
                    <option value="shift_1" {{ request('shift_type') == 'shift_1' ? 'selected' : '' }}>Shift 1 (Pagi: 07:00 - 15:00)</option>
                    <option value="shift_2" {{ request('shift_type') == 'shift_2' ? 'selected' : '' }}>Shift 2 (Sore: 15:00 - 23:00)</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-mono text-stone-600 mb-1 font-semibold uppercase">Status</label>
                <select name="status" class="w-full text-xs font-mono bg-stone-50 border border-stone-200 rounded-lg px-2.5 py-2 focus:outline-none focus:ring-1 focus:ring-[#C27835]">
                    <option value="all">Semua Status</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open (Aktif)</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed (Ditutup)</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-mono text-stone-600 mb-1 font-semibold uppercase">Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="w-full text-xs font-mono bg-stone-50 border border-stone-200 rounded-lg px-2.5 py-2 focus:outline-none focus:ring-1 focus:ring-[#C27835]">
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="w-full bg-stone-800 hover:bg-stone-900 text-white text-xs font-mono font-medium py-2 rounded-lg transition-colors">
                    <i class="fas fa-filter text-[10px] me-1"></i> Filter
                </button>
                <button type="button" onclick="openOpenShiftModal()" class="w-full bg-[#C27835] hover:bg-[#A05C22] text-white text-xs font-mono font-medium py-2 rounded-lg transition-colors flex items-center justify-center space-x-1 shrink-0">
                    <i class="fas fa-plus text-[10px]"></i>
                    <span>Buka Shift</span>
                </button>
            </div>
        </form>
    </div>

    {{-- 5. Tabel Rekapitulasi Shift --}}
    <div class="bg-white border border-stone-200 rounded-xl shadow-2xs overflow-hidden">
        <div class="px-5 py-3.5 border-b border-stone-200 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-[#C27835]"></span>
                <h3 class="text-xs font-mono uppercase tracking-wider font-bold text-stone-900">
                    Riwayat & Sesi Shift Kasir
                </h3>
            </div>
            <span class="text-xs font-mono text-stone-500 bg-stone-100 px-2.5 py-0.5 rounded border border-stone-200">
                Total {{ $shifts->total() }} Sesi
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-mono">
                <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 text-[10px] uppercase">
                    <tr>
                        <th class="px-4 py-3">Kasir & Sesi</th>
                        <th class="px-4 py-3">Waktu Operasional</th>
                        <th class="px-4 py-3">Modal Awal</th>
                        <th class="px-4 py-3 text-right">Omzet Tunai</th>
                        <th class="px-4 py-3 text-right">Omzet QRIS</th>
                        <th class="px-4 py-3 text-right">Kas Aktual</th>
                        <th class="px-4 py-3 text-center">Selisih</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($shifts as $shift)
                        <tr class="hover:bg-stone-50/70 transition-colors">
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-stone-900">{{ $shift->user ? $shift->user->name : $shift->nama_pegawai }}</div>
                                <div class="flex items-center space-x-1.5 mt-0.5">
                                    <span class="text-[10px] px-1.5 py-0.2 rounded font-semibold {{ $shift->shift_type == 'shift_1' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-purple-50 text-purple-700 border border-purple-200' }}">
                                        {{ $shift->shift_type == 'shift_1' ? 'Shift 1 (Pagi)' : 'Shift 2 (Sore)' }}
                                    </span>
                                    <span class="text-stone-400 text-[10px]">#{{ $shift->id_shift }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-stone-600">
                                <div class="flex items-center space-x-1 text-emerald-700">
                                    <i class="fas fa-sign-in-alt text-[10px]"></i>
                                    <span>{{ $shift->opened_at ? $shift->opened_at->format('d M, H:i') : '-' }}</span>
                                </div>
                                @if($shift->closed_at)
                                    <div class="flex items-center space-x-1 text-red-600 mt-0.5">
                                        <i class="fas fa-sign-out-alt text-[10px]"></i>
                                        <span>{{ $shift->closed_at->format('d M, H:i') }}</span>
                                    </div>
                                @else
                                    <div class="text-[10px] text-emerald-600 font-semibold mt-0.5 animate-pulse">
                                        ● Sedang Berjalan
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-stone-800">
                                Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5 text-right font-bold text-emerald-600">
                                Rp {{ number_format($shift->cash_sales, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5 text-right text-stone-500">
                                Rp {{ number_format($shift->non_cash_sales, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5 text-right font-bold text-stone-900">
                                @if($shift->actual_cash !== null)
                                    Rp {{ number_format($shift->actual_cash, 0, ',', '.') }}
                                @else
                                    <span class="text-stone-400 font-normal">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if($shift->difference !== null)
                                    @if($shift->difference == 0)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Pas
                                        </span>
                                    @elseif($shift->difference < 0)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-red-50 text-red-700 border border-red-200" title="Minus Kas">
                                            -Rp {{ number_format(abs($shift->difference), 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200" title="Surplus Kas">
                                            +Rp {{ number_format($shift->difference, 0, ',', '.') }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-stone-400 text-[10px]">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if($shift->status == 'open')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-700 border border-emerald-500/30 flex items-center justify-center space-x-1 mx-auto w-max">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>OPEN</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-stone-100 text-stone-600 border border-stone-200">
                                        CLOSED
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-1">
                                <button onclick="viewShiftOrders({{ $shift->id_shift }})" class="p-1.5 rounded text-stone-600 hover:text-stone-900 hover:bg-stone-100 border border-stone-200 transition-colors" title="Lihat Daftar Transaksi Shift">
                                    <i class="fas fa-receipt text-xs"></i>
                                </button>
                                @if($shift->status == 'open')
                                    <button onclick="openCloseShiftModal({{ $shift->id_shift }}, '{{ $shift->user ? $shift->user->name : $shift->nama_pegawai }}', {{ $shift->starting_cash }}, {{ $shift->cash_sales }})" class="p-1.5 rounded bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 transition-colors" title="Tutup Shift & Rekonsiliasi">
                                        <i class="fas fa-lock text-xs"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-stone-400 font-mono text-xs">
                                Tidak ada catatan shift yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($shifts->hasPages())
            <div class="px-5 py-3 border-t border-stone-200 bg-stone-50">
                {{ $shifts->links() }}
            </div>
        @endif
    </div>

</div>

{{-- ======================================================== --}}
{{-- MODAL 1: BUKA SHIFT KASIR                               --}}
{{-- ======================================================== --}}
<div id="openShiftModal" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full border border-stone-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150">
        <div class="p-5 border-b border-stone-200 flex items-center justify-between bg-stone-50">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#C27835] text-white flex items-center justify-center text-xs">
                    <i class="fas fa-key"></i>
                </div>
                <div>
                    <h3 class="text-xs font-mono uppercase tracking-wider font-bold text-stone-900">Buka Shift Kasir Baru</h3>
                    <p class="text-[11px] text-stone-500">Inisialisasi laci kasir & sesi transaksi</p>
                </div>
            </div>
            <button onclick="closeModal('openShiftModal')" class="text-stone-400 hover:text-stone-600">
                <i class="fas fa-xmark text-sm"></i>
            </button>
        </div>

        <form action="{{ route('admin.shifts.open') }}" method="POST" class="p-5 space-y-4">
            @csrf
            <div>
                <label class="block text-[11px] font-mono text-stone-700 mb-1 font-semibold uppercase">Pilih Kasir</label>
                <select name="id_user" id="open_id_user" required class="w-full text-xs font-mono bg-stone-50 border border-stone-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-[#C27835]">
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id_user }}" {{ auth()->id() == $emp->id_user ? 'selected' : '' }}>
                            {{ $emp->name }} ({{ $emp->username }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-mono text-stone-700 mb-1 font-semibold uppercase">Pola Shift Operasional</label>
                <select name="shift_type" class="w-full text-xs font-mono bg-stone-50 border border-stone-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-[#C27835]">
                    <option value="shift_1">Shift 1 (Pagi: 07:00 - 15:00 / 1 Kasir)</option>
                    <option value="shift_2" {{ now()->hour >= 15 ? 'selected' : '' }}>Shift 2 (Sore: 15:00 - 23:00 / Multi-Kasir Aktif)</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-mono text-stone-700 mb-1 font-semibold uppercase">Modal Kas Awal di Laci (Rp)</label>
                <input type="number" name="modal_awal" value="200000" min="0" step="1000" required class="w-full text-sm font-mono font-bold bg-stone-50 border border-stone-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-[#C27835]">
                <span class="text-[10px] text-stone-400 mt-1 block">Uang pecahan kecil untuk kembalian pelanggan di laci kasir.</span>
            </div>

            <div>
                <label class="block text-[11px] font-mono text-stone-700 mb-1 font-semibold uppercase">Catatan / Posisi Station</label>
                <input type="text" name="notes" placeholder="Contoh: Laci Kasir POS-A / Bar Depan" class="w-full text-xs font-mono bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#C27835]">
            </div>

            <div class="pt-2 flex items-center space-x-2">
                <button type="button" onclick="closeModal('openShiftModal')" class="w-1/2 py-2.5 rounded-lg border border-stone-200 text-stone-600 font-mono text-xs hover:bg-stone-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="w-1/2 py-2.5 rounded-lg bg-[#C27835] hover:bg-[#A05C22] text-white font-mono text-xs font-bold transition-colors shadow-sm">
                    Konfirmasi Buka Shift
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ======================================================== --}}
{{-- MODAL 2: TUTUP SHIFT & REKONSILIASI KAS                 --}}
{{-- ======================================================== --}}
<div id="closeShiftModal" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full border border-stone-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150">
        <div class="p-5 border-b border-stone-200 flex items-center justify-between bg-stone-50">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-red-600 text-white flex items-center justify-center text-xs">
                    <i class="fas fa-lock"></i>
                </div>
                <div>
                    <h3 class="text-xs font-mono uppercase tracking-wider font-bold text-stone-900">Tutup Shift & Rekonsiliasi</h3>
                    <p class="text-[11px] text-stone-500" id="closeModalSubtitle">Hitung kas fisik akhir di laci</p>
                </div>
            </div>
            <button onclick="closeModal('closeShiftModal')" class="text-stone-400 hover:text-stone-600">
                <i class="fas fa-xmark text-sm"></i>
            </button>
        </div>

        <form action="{{ route('admin.shifts.close') }}" method="POST" class="p-5 space-y-4">
            @csrf
            <input type="hidden" name="id_shift" id="close_id_shift">

            <div class="bg-stone-50 p-3.5 rounded-xl border border-stone-200 space-y-2 text-xs font-mono">
                <div class="flex items-center justify-between">
                    <span class="text-stone-500">Modal Kas Awal:</span>
                    <strong class="text-stone-800" id="close_starting_cash_display">Rp 0</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-stone-500">Penjualan Tunai (Cash):</span>
                    <strong class="text-emerald-600" id="close_cash_sales_display">Rp 0</strong>
                </div>
                <div class="pt-2 border-t border-stone-200 flex items-center justify-between font-bold">
                    <span class="text-stone-700">Total Kas Seharusnya:</span>
                    <span class="text-stone-900 text-sm" id="close_expected_cash_display">Rp 0</span>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-mono text-stone-700 mb-1 font-semibold uppercase">Kas Fisik Akhir di Laci (Rp)</label>
                <input type="number" name="kas_akhir_aktual" id="close_actual_cash_input" oninput="calculateDifferenceLive()" min="0" step="1000" required class="w-full text-base font-mono font-bold bg-white border border-stone-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500">
                <span class="text-[10px] text-stone-400 mt-1 block">Hitung seluruh uang tunai fisik yang ada di dalam laci saat ini.</span>
            </div>

            {{-- Live Difference Result --}}
            <div id="liveDiffBox" class="p-3 rounded-lg border text-xs font-mono flex items-center justify-between bg-stone-100 border-stone-200 text-stone-600">
                <span>Status Rekonsiliasi:</span>
                <strong id="liveDiffText">Masukkan Kas Fisik</strong>
            </div>

            <div>
                <label class="block text-[11px] font-mono text-stone-700 mb-1 font-semibold uppercase">Catatan Hand-over (Opsional)</label>
                <input type="text" name="notes" placeholder="Contoh: Kas pas, uang disimpan ke brankas" class="w-full text-xs font-mono bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-stone-400">
            </div>

            <div class="pt-2 flex items-center space-x-2">
                <button type="button" onclick="closeModal('closeShiftModal')" class="w-1/2 py-2.5 rounded-lg border border-stone-200 text-stone-600 font-mono text-xs hover:bg-stone-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="w-1/2 py-2.5 rounded-lg bg-red-600 hover:bg-red-700 text-white font-mono text-xs font-bold transition-colors shadow-sm">
                    Konfirmasi Tutup Shift
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ======================================================== --}}
{{-- MODAL 3: RINCIAN ORDER KHUSUS SHIFT (ISOLASI DATA)      --}}
{{-- ======================================================== --}}
<div id="shiftOrdersModal" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full border border-stone-200 shadow-2xl overflow-hidden max-h-[90vh] flex flex-col animate-in fade-in zoom-in duration-150">
        <div class="p-5 border-b border-stone-200 flex items-center justify-between bg-stone-50 shrink-0">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-stone-800 text-white flex items-center justify-center text-xs">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <h3 class="text-xs font-mono uppercase tracking-wider font-bold text-stone-900" id="ordersModalTitle">Detail Transaksi Shift</h3>
                    <p class="text-[11px] text-stone-500" id="ordersModalSubtitle">Memuat data transaksi...</p>
                </div>
            </div>
            <button onclick="closeModal('shiftOrdersModal')" class="text-stone-400 hover:text-stone-600">
                <i class="fas fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="p-5 overflow-y-auto flex-1 space-y-4">
            {{-- Shift Metadata Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs font-mono bg-stone-50 p-3 rounded-xl border border-stone-200" id="ordersShiftMeta">
                <!-- Injected by JS -->
            </div>

            {{-- Table Orders --}}
            <div class="border border-stone-200 rounded-lg overflow-hidden">
                <table class="w-full text-left text-xs font-mono">
                    <thead class="bg-stone-100 text-stone-500 text-[10px] uppercase">
                        <tr>
                            <th class="px-3 py-2">Order ID</th>
                            <th class="px-3 py-2">Pelanggan</th>
                            <th class="px-3 py-2">Items</th>
                            <th class="px-3 py-2">Metode</th>
                            <th class="px-3 py-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody" class="divide-y divide-stone-100">
                        <!-- Injected by JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <div class="p-4 border-t border-stone-200 bg-stone-50 flex items-center justify-between shrink-0">
            <div class="text-xs font-mono text-stone-600">
                Total Transaksi: <strong id="modalTotalOrders" class="text-stone-900">0</strong> | Total Omzet: <strong id="modalTotalOmzet" class="text-emerald-600">Rp 0</strong>
            </div>
            <button type="button" onclick="closeModal('shiftOrdersModal')" class="px-4 py-2 rounded-lg bg-stone-800 text-white font-mono text-xs hover:bg-stone-900 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentExpectedCash = 0;

function openOpenShiftModal(userId = null) {
    if (userId) {
        document.getElementById('open_id_user').value = userId;
    }
    document.getElementById('openShiftModal').classList.remove('hidden');
}

function openCloseShiftModal(idShift, cashierName, startingCash, cashSales) {
    document.getElementById('close_id_shift').value = idShift;
    document.getElementById('closeModalSubtitle').innerText = 'Kasir: ' + cashierName + ' (Shift #' + idShift + ')';
    
    currentExpectedCash = parseFloat(startingCash) + parseFloat(cashSales);
    
    document.getElementById('close_starting_cash_display').innerText = 'Rp ' + Number(startingCash).toLocaleString('id-ID');
    document.getElementById('close_cash_sales_display').innerText = 'Rp ' + Number(cashSales).toLocaleString('id-ID');
    document.getElementById('close_expected_cash_display').innerText = 'Rp ' + Number(currentExpectedCash).toLocaleString('id-ID');
    
    document.getElementById('close_actual_cash_input').value = currentExpectedCash;
    calculateDifferenceLive();
    
    document.getElementById('closeShiftModal').classList.remove('hidden');
}

function calculateDifferenceLive() {
    const inputVal = parseFloat(document.getElementById('close_actual_cash_input').value) || 0;
    const diff = inputVal - currentExpectedCash;
    const box = document.getElementById('liveDiffBox');
    const text = document.getElementById('liveDiffText');

    if (diff === 0) {
        box.className = 'p-3 rounded-lg border text-xs font-mono flex items-center justify-between bg-emerald-50 border-emerald-200 text-emerald-800';
        text.innerHTML = '<i class="fas fa-check-circle me-1"></i> Kas Pas (Balance)';
    } else if (diff < 0) {
        box.className = 'p-3 rounded-lg border text-xs font-mono flex items-center justify-between bg-red-50 border-red-200 text-red-800';
        text.innerHTML = '<i class="fas fa-triangle-exclamation me-1"></i> Selisih Kurang: Rp ' + Number(Math.abs(diff)).toLocaleString('id-ID');
    } else {
        box.className = 'p-3 rounded-lg border text-xs font-mono flex items-center justify-between bg-blue-50 border-blue-200 text-blue-800';
        text.innerHTML = '<i class="fas fa-plus-circle me-1"></i> Surplus Kas: +Rp ' + Number(diff).toLocaleString('id-ID');
    }
}

function viewShiftOrders(shiftId) {
    document.getElementById('ordersModalTitle').innerText = 'Detail Transaksi Shift #' + shiftId;
    document.getElementById('ordersModalSubtitle').innerText = 'Memuat data...';
    document.getElementById('ordersTableBody').innerHTML = '<tr><td colspan="5" class="text-center py-6 text-stone-400">Memuat transaksi shift...</td></tr>';
    document.getElementById('shiftOrdersModal').classList.remove('hidden');

    fetch('/shifts/' + shiftId + '/orders')
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                closeModal('shiftOrdersModal');
                return;
            }

            const s = data.shift;
            document.getElementById('ordersModalSubtitle').innerText = s.kasir + ' | ' + s.shift_type + ' | Status: ' + s.status.toUpperCase();
            
            document.getElementById('ordersShiftMeta').innerHTML = `
                <div><span class="text-[10px] text-stone-400 block">Modal Awal</span><strong>${s.formatted_starting_cash}</strong></div>
                <div><span class="text-[10px] text-stone-400 block">Omzet Cash</span><strong class="text-emerald-600">${s.formatted_cash_sales}</strong></div>
                <div><span class="text-[10px] text-stone-400 block">Omzet QRIS</span><strong class="text-stone-700">${s.formatted_non_cash_sales}</strong></div>
                <div><span class="text-[10px] text-stone-400 block">Kasir</span><strong>${s.kasir}</strong></div>
            `;

            document.getElementById('modalTotalOrders').innerText = data.total_orders + ' Tiket';
            document.getElementById('modalTotalOmzet').innerText = data.total_omzet;

            let rows = '';
            if (data.orders.length === 0) {
                rows = '<tr><td colspan="5" class="text-center py-6 text-stone-400">Belum ada transaksi pada shift ini.</td></tr>';
            } else {
                data.orders.forEach(o => {
                    rows += `
                        <tr class="hover:bg-stone-50">
                            <td class="px-3 py-2.5 font-bold text-stone-800">#${o.id_order}<div class="text-[10px] text-stone-400">${o.tanggal}</div></td>
                            <td class="px-3 py-2.5">${o.nama_pelanggan}</td>
                            <td class="px-3 py-2.5 text-[11px] text-stone-600 max-w-xs truncate" title="${o.items_detail}">${o.items_detail}</td>
                            <td class="px-3 py-2.5"><span class="px-1.5 py-0.2 rounded text-[10px] ${o.payment_method === 'CASH' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-purple-50 text-purple-700 border border-purple-200'}">${o.payment_method}</span></td>
                            <td class="px-3 py-2.5 text-right font-bold text-stone-900">${o.formatted_total}</td>
                        </tr>
                    `;
                });
            }
            document.getElementById('ordersTableBody').innerHTML = rows;
        })
        .catch(err => {
            document.getElementById('ordersTableBody').innerHTML = '<tr><td colspan="5" class="text-center py-6 text-red-500">Gagal memuat data transaksi.</td></tr>';
        });
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}
</script>
@endpush

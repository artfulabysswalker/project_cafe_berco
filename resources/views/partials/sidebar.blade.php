<aside class="w-64 bg-[#18181B] flex flex-col justify-between border-r border-stone-800 shrink-0 h-screen select-none">
    <div class="flex flex-col flex-1 overflow-hidden">
        
        <!-- 1. BRAND / STATION HEADER -->
        <div class="h-16 px-5 flex items-center border-b border-stone-800/90 shrink-0 bg-[#141416]">
            <a href="{{ route('control.dashboard') }}" class="flex items-center space-x-3 group">
                <div class="w-8 h-8 rounded-md bg-[#C27835] flex items-center justify-center text-white font-mono font-bold text-xs tracking-wider shadow-2xs group-hover:bg-[#A05C22] transition-colors">
                    CB
                </div>
                <div class="leading-tight">
                    <span class="text-xs font-semibold text-white tracking-wider uppercase font-mono block">BERCO CAFE</span>
                    <span class="text-[10px] font-mono text-stone-400 block tracking-normal">POS & MGMT SYSTEM</span>
                </div>
            </a>
        </div>

        <!-- 2. USER STATUS PILL -->
        <div class="px-4 py-3 border-b border-stone-800/60 bg-[#161619] shrink-0">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2.5 min-w-0">
                    <div class="w-7 h-7 rounded-md bg-stone-800 border border-stone-700 text-stone-200 font-mono text-xs flex items-center justify-center font-semibold shrink-0">
                        {{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-stone-200 truncate leading-tight">{{ auth()->user()?->name ?? 'Administrator' }}</p>
                        <p class="text-[10px] font-mono text-emerald-400 mt-0.5 flex items-center space-x-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>{{ auth()->user()?->role?->role_name ?? (auth()->user()?->isAdmin() ? 'Admin' : 'Staff') }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. NAVIGATION MENU -->
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-4 text-xs font-sans">
            
            <div>
                <span class="px-2 text-[10px] font-mono tracking-widest text-stone-500 uppercase font-semibold block mb-1.5">MANAJEMEN</span>
                <div class="space-y-0.5">
                    <a href="{{ route('control.dashboard') }}" class="flex items-center justify-between px-2.5 py-2 rounded-md transition-colors {{ request()->routeIs('control.dashboard') ? 'bg-stone-800 text-white font-medium border-l-2 border-[#C27835]' : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50' }}">
                        <div class="flex items-center space-x-2.5">
                            <i class="fas fa-gauge-high text-xs w-4 text-center {{ request()->routeIs('control.dashboard') ? 'text-[#C27835]' : 'text-stone-400' }}"></i>
                            <span>Dashboard</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.menu') }}" class="flex items-center justify-between px-2.5 py-2 rounded-md transition-colors {{ request()->routeIs('admin.menu*') ? 'bg-stone-800 text-white font-medium border-l-2 border-[#C27835]' : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50' }}">
                        <div class="flex items-center space-x-2.5">
                            <i class="fas fa-mug-hot text-xs w-4 text-center {{ request()->routeIs('admin.menu*') ? 'text-[#C27835]' : 'text-stone-400' }}"></i>
                            <span>Katalog Menu</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.hpp') }}" class="flex items-center justify-between px-2.5 py-2 rounded-md transition-colors {{ request()->routeIs('admin.hpp*') ? 'bg-stone-800 text-white font-medium border-l-2 border-[#C27835]' : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50' }}">
                        <div class="flex items-center space-x-2.5">
                            <i class="fas fa-scale-balanced text-xs w-4 text-center {{ request()->routeIs('admin.hpp*') ? 'text-[#C27835]' : 'text-stone-400' }}"></i>
                            <span>HPP & Resep</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.inventory') }}" class="flex items-center justify-between px-2.5 py-2 rounded-md transition-colors {{ request()->routeIs('admin.inventory*') ? 'bg-stone-800 text-white font-medium border-l-2 border-[#C27835]' : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50' }}">
                        <div class="flex items-center space-x-2.5">
                            <svg class="w-3.5 h-3.5 {{ request()->routeIs('admin.inventory*') ? 'text-[#C27835]' : 'text-stone-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                                <path d="m3.3 7 8.7 5 8.7-5"/>
                                <path d="M12 22V12"/>
                            </svg>
                            <span>Stok Barang</span>
                        </div>
                        @php
                            $criticalStockCount = \App\Models\Inventory::where('status', 'kritis')->count();
                        @endphp
                        @if($criticalStockCount > 0)
                            <span class="text-[9px] font-mono px-1.5 py-0.2 rounded bg-red-500/20 text-red-400 border border-red-500/30">
                                {{ $criticalStockCount }} Kritis
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('admin.staffoption.index') }}" class="flex items-center justify-between px-2.5 py-2 rounded-md transition-colors {{ request()->routeIs('admin.staffoption*') ? 'bg-stone-800 text-white font-medium border-l-2 border-[#C27835]' : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50' }}">
                        <div class="flex items-center space-x-2.5">
                            <i class="fas fa-users text-xs w-4 text-center {{ request()->routeIs('admin.staffoption*') ? 'text-[#C27835]' : 'text-stone-400' }}"></i>
                            <span>Staff & Akun</span>
                        </div>
                    </a>

                    @php
                        $pendingOrdersCount = \App\Models\Order::where('status_order', 'pending')->orWhere('status_pembayaran', 'pending')->count();
                    @endphp
                    <a href="{{ route('admin.orders') }}" class="flex items-center justify-between px-2.5 py-2 rounded-md transition-colors {{ request()->routeIs('admin.orders') ? 'bg-stone-800 text-white font-medium border-l-2 border-[#C27835]' : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50' }}">
                        <div class="flex items-center space-x-2.5">
                            <i class="fas fa-bag-shopping text-xs w-4 text-center {{ request()->routeIs('admin.orders') ? 'text-[#C27835]' : 'text-stone-400' }}"></i>
                            <span>Pesanan Aktif</span>
                        </div>
                        @if($pendingOrdersCount > 0)
                            <span class="text-[10px] font-mono px-1.5 py-0.2 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30">{{ $pendingOrdersCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.history') }}" class="flex items-center justify-between px-2.5 py-2 rounded-md transition-colors {{ request()->routeIs('admin.history') ? 'bg-stone-800 text-white font-medium border-l-2 border-[#C27835]' : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50' }}">
                        <div class="flex items-center space-x-2.5">
                            <i class="fas fa-clock-rotate-left text-xs w-4 text-center {{ request()->routeIs('admin.history') ? 'text-[#C27835]' : 'text-stone-400' }}"></i>
                            <span>Riwayat Transaksi</span>
                        </div>
                    </a>
                </div>
            </div>

            <div>
                <span class="px-2 text-[10px] font-mono tracking-widest text-stone-500 uppercase font-semibold block mb-1.5">LAPORAN & KONTROL</span>
                <div class="space-y-0.5">
                    @php
                        $pendingRequestsCount = \App\Models\PasswordResetRequest::where('status', 'pending')->count();
                    @endphp
                    <a href="{{ route('admin.requests') }}" class="flex items-center justify-between px-2.5 py-2 rounded-md transition-colors {{ request()->routeIs('admin.requests') ? 'bg-stone-800 text-white font-medium border-l-2 border-[#C27835]' : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50' }}">
                        <div class="flex items-center space-x-2.5">
                            <i class="fas fa-bell text-xs w-4 text-center {{ request()->routeIs('admin.requests') ? 'text-[#C27835]' : 'text-stone-400' }}"></i>
                            <span>Permintaan Reset</span>
                        </div>
                        @if($pendingRequestsCount > 0)
                            <span class="text-[10px] font-mono px-1.5 py-0.2 rounded bg-red-500/20 text-red-300 border border-red-500/30">{{ $pendingRequestsCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.receipt.edit') }}" class="flex items-center justify-between px-2.5 py-2 rounded-md transition-colors {{ request()->routeIs('admin.receipt.edit') ? 'bg-stone-800 text-white font-medium border-l-2 border-[#C27835]' : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50' }}">
                        <div class="flex items-center space-x-2.5">
                            <i class="fas fa-receipt text-xs w-4 text-center {{ request()->routeIs('admin.receipt.edit') ? 'text-[#C27835]' : 'text-stone-400' }}"></i>
                            <span>Format Struk</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.shifts.index') }}" class="flex items-center justify-between px-2.5 py-2 rounded-md transition-colors {{ request()->routeIs('admin.shifts*') ? 'bg-stone-800 text-white font-medium border-l-2 border-[#C27835]' : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50' }}">
                        <div class="flex items-center space-x-2.5">
                            <i class="fas fa-business-time text-xs w-4 text-center {{ request()->routeIs('admin.shifts*') ? 'text-[#C27835]' : 'text-stone-400' }}"></i>
                            <span>Monitoring Shift</span>
                        </div>
                        @php
                            $openShiftsCount = \App\Models\CashierShift::where('status', 'open')->count();
                        @endphp
                        @if($openShiftsCount > 0)
                            <span class="text-[10px] font-mono px-1.5 py-0.2 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center space-x-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>{{ $openShiftsCount }} Aktif</span>
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('admin.expenses.index') }}" class="flex items-center justify-between px-2.5 py-2 rounded-md transition-colors {{ request()->routeIs('admin.expenses*') ? 'bg-stone-800 text-white font-medium border-l-2 border-[#C27835]' : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50' }}">
                        <div class="flex items-center space-x-2.5">
                            <i class="fas fa-file-invoice-dollar text-xs w-4 text-center {{ request()->routeIs('admin.expenses*') ? 'text-[#C27835]' : 'text-stone-400' }}"></i>
                            <span>Pengeluaran Toko</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.stats') }}" class="flex items-center justify-between px-2.5 py-2 rounded-md transition-colors {{ request()->routeIs('admin.stats*') ? 'bg-stone-800 text-white font-medium border-l-2 border-[#C27835]' : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50' }}">
                        <div class="flex items-center space-x-2.5">
                            <i class="fas fa-chart-simple text-xs w-4 text-center {{ request()->routeIs('admin.stats*') ? 'text-[#C27835]' : 'text-stone-400' }}"></i>
                            <span>Analitik & Laba</span>
                        </div>
                    </a>
                </div>
            </div>

        </nav>

        <!-- 4. FOOTER LOGOUT -->
        <div class="p-3 border-t border-stone-800/80 bg-[#141416] shrink-0">
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center space-x-2 px-3 py-2 rounded-md text-stone-400 hover:text-red-300 hover:bg-red-500/10 border border-stone-800 hover:border-red-500/30 text-xs font-mono transition-colors">
                    <i class="fas fa-arrow-right-from-bracket text-xs"></i>
                    <span>Keluar Sesi</span>
                </button>
            </form>
        </div>

    </div>
</aside>

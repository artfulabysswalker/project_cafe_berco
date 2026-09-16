<!DOCTYPE html>
<html lang="id" class="h-full bg-stone-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Berco Cafe — Operational & Management')</title>
    
    {{-- Typography & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            charcoal: '#18181B',
                            terracotta: '#C27835',
                            'terracotta-dark': '#9A3412',
                            canvas: '#FBFBFA',
                            surface: '#FFFFFF',
                            border: '#E7E5E4',
                            'border-subtle': '#F0EFEB',
                        }
                    }
                }
            }
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    @livewireStyles
</head>
<body class="h-full font-sans antialiased text-stone-900 flex overflow-hidden selection:bg-stone-200">

    <div class="flex w-full h-full overflow-hidden bg-stone-100">
        {{-- SIDEBAR --}}
        @include('partials.sidebar')

        {{-- MAIN CONTENT AREA --}}
        <div class="flex-1 flex flex-col min-w-0 bg-[#FBFBFA] overflow-hidden">
            
            {{-- TOPBAR --}}
            <header class="h-14 bg-white border-b border-stone-200 px-6 flex items-center justify-between shrink-0">
                <div class="flex items-center space-x-3">
                    <h1 class="text-sm font-semibold text-stone-900 leading-none">@yield('page-title', 'Dashboard')</h1>
                    <span class="text-stone-300">/</span>
                    <span class="text-xs font-mono text-stone-500">@yield('breadcrumb', 'Overview')</span>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="relative hidden sm:block w-64">
                        <input 
                            type="text" 
                            placeholder="Cari transaksi, menu, SKU..." 
                            class="w-full bg-stone-50 border border-stone-200 rounded-md pl-8 pr-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#C27835] focus:border-[#C27835]"
                        />
                        <i class="fas fa-magnifying-glass text-stone-400 text-xs absolute left-2.5 top-2.5"></i>
                    </div>

                    <div class="flex items-center space-x-2">
                        @php
                            $user = auth()->user();
                            $activeShift = $user ? $user->activeShift : null;
                            $openShiftsCount = \App\Models\CashierShift::where('status', 'open')->count();
                        @endphp
                        @if($activeShift)
                            <a href="{{ route('admin.shifts.index') }}" class="text-[11px] font-mono font-medium px-2.5 py-1.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition-colors inline-flex items-center space-x-1.5" title="Sesi Shift Anda Aktif">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>{{ $activeShift->shift_type == 'shift_1' ? 'Shift 1' : 'Shift 2' }}: Rp {{ number_format($activeShift->starting_cash + $activeShift->cash_sales, 0, ',', '.') }}</span>
                            </a>
                        @elseif($user && $user->isAdmin() && $openShiftsCount > 0)
                            <a href="{{ route('admin.shifts.index') }}" class="text-[11px] font-mono font-medium px-2.5 py-1.5 rounded-md bg-purple-50 text-purple-700 border border-purple-200 hover:bg-purple-100 transition-colors inline-flex items-center space-x-1.5" title="Monitoring Multi-Kasir Aktif">
                                <span class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></span>
                                <span>{{ $openShiftsCount }} Kasir Aktif</span>
                            </a>
                        @endif

                        <a href="{{ route('admin.orders') }}" class="p-1.5 rounded-md text-stone-500 hover:text-stone-900 hover:bg-stone-100 border border-stone-200 relative transition-colors" title="Pesanan">
                            <i class="fas fa-bell text-xs"></i>
                        </a>
                        <a href="{{ route('home') }}" target="_blank" class="text-xs font-mono text-stone-600 hover:text-stone-900 border border-stone-200 px-2.5 py-1.5 rounded-md hover:bg-stone-100 transition-colors inline-flex items-center space-x-1.5" title="Buka Halaman Pelanggan">
                            <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                            <span class="hidden md:inline">Web Publik</span>
                        </a>
                    </div>
                </div>
            </header>

            {{-- PAGE CONTENT CONTAINER --}}
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
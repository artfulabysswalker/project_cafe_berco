<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Berco Cafe')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    @livewireStyles
    <style>
        :root {
            --color-dark-brown: #5d2e1a;
            --color-brown: #8b5e34;
            --color-light-brown: #c78c4e;
            --color-golden: #bbaa3a;
            --color-cream: #FFFCEC;
            --color-white: #ffffff;
            --color-gray: #f9fafb;
            --color-red: #dc2626;
            --color-green: #16a34a;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--color-cream);
            color: #1f2937;
        }
    </style>
</head>
<body class="bg-yellow-50">
    <div class="flex h-screen bg-yellow-50">
        <!-- SIDEBAR -->
        <aside class="w-64 bg-gradient-to-b from-amber-900 to-amber-800 text-white shadow-2xl fixed left-0 top-0 h-screen flex flex-col">
            <!-- Logo Section -->
            <div class="p-8 border-b-2 border-amber-700 flex-shrink-0">
                <div class="flex flex-col items-center">
                    <div class="w-20 h-20 bg-yellow-50 rounded-full flex items-center justify-center mb-4 shadow-2xl border-4 border-amber-600">
                        <span class="text-4xl">☕</span>
                    </div>
                    <h1 class="text-2xl font-bold text-center tracking-wide">ADMIN</h1>
                    <p class="text-sm text-amber-200 mt-2 font-medium">Berco Cafe</p>
                </div>
            </div>

            <!-- Menu Navigation -->
            <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-6">
                <!-- Main Menu Section -->
                <div>
                    <h3 class="text-xs font-bold text-amber-300 px-4 py-2 mb-3 uppercase tracking-widest">📋 Main Menu</h3>
                    <div class="space-y-2">
                        <a href="{{ route('admin.menu') }}" 
                           class="menu-link {{ request()->routeIs('admin.menu') ? 'active' : '' }}">
                            <i class="fas fa-utensils"></i>
                            <span>Menu</span>
                        </a>
                        <a href="{{ route('admin.staffoption.index') }}" 
                           class="menu-link {{ request()->routeIs('admin.staffoption*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span>Staff</span>
                        </a>
                        <a href="{{ route('admin.orders') }}" 
                           class="menu-link {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Orders</span>
                        </a>
                        <a href="{{ route('admin.history') }}" 
                           class="menu-link {{ request()->routeIs('admin.history') ? 'active' : '' }}">
                            <i class="fas fa-history"></i>
                            <span>Order History</span>
                        </a>
                    </div>
                </div>

                <!-- Settings Section -->
                <div>
                    <h3 class="text-xs font-bold text-amber-300 px-4 py-2 mb-3 uppercase tracking-widest">⚙️ Settings</h3>
                    <div class="space-y-2">
                        <a href="{{ route('admin.requests') }}" 
                           class="menu-link {{ request()->routeIs('admin.requests') ? 'active' : '' }}">
                            <i class="fas fa-envelope"></i>
                            <span>Requests</span>
                        </a>
                        <a href="{{ route('admin.receipt.edit') }}" 
                           class="menu-link {{ request()->routeIs('admin.receipt.edit') ? 'active' : '' }}">
                            <i class="fas fa-receipt"></i>
                            <span>Receipt</span>
                        </a>
                        <a href="{{ route('admin.stats') }}" 
                           class="menu-link {{ request()->routeIs('admin.stats') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar"></i>
                            <span>Statistics</span>
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Bottom Actions -->
            <div class="p-4 border-t-2 border-amber-700 bg-gradient-to-t from-amber-950 to-transparent flex-shrink-0 space-y-2">
                <a href="{{ route('admin.password.edit') }}" 
                   class="menu-link">
                    <i class="fas fa-cog"></i>
                    <span>Account</span>
                </a>
                <button onclick="openLogout()" 
                        class="w-full menu-link bg-red-600 hover:bg-red-700 text-white">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="flex-1 ml-64 flex flex-col">
            <!-- TOP BAR -->
            <div class="bg-gradient-to-r from-amber-500 to-amber-400 shadow-md px-8 py-4 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-amber-900">Dashboard</h2>
                    <p class="text-sm text-amber-800">Welcome back, Admin</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <button class="p-2 hover:bg-amber-600 rounded-lg transition-colors text-white">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                        </button>
                    </div>
                    <div class="w-10 h-10 bg-amber-900 rounded-full flex items-center justify-center text-white font-bold">
                        A
                    </div>
                </div>
            </div>

            <!-- PAGE CONTENT -->
            <main class="flex-1 overflow-auto bg-yellow-50 p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- LOGOUT MODAL -->
    <div id="logout-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-8 w-96">
            <div class="text-center mb-6">
                <i class="fas fa-exclamation-circle text-3xl text-amber-600 mb-4"></i>
                <h3 class="text-lg font-bold text-gray-800">Confirm Logout</h3>
            </div>
            <p class="text-gray-600 text-center mb-6">Are you sure you want to logout?</p>
            <div class="flex gap-4">
                <button onclick="closeLogout()" 
                        class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors font-medium">
                    Cancel
                </button>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                        Yes, Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <style>
        .menu-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            color: #fef3c7;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .menu-link:hover {
            background-color: #b45309;
            color: white;
        }
        
        .menu-link i {
            flex-shrink: 0;
            width: 1.25rem;
            text-align: center;
            font-size: 1.125rem;
        }
        
        .menu-link.active {
            background: linear-gradient(90deg, #92400e 0%, #b45309 100%);
            color: white;
            border-left: 4px solid #fcd34d;
            padding-left: calc(1rem - 4px);
            font-weight: 600;
        }
    </style>

    <script>
        function openLogout() {
            document.getElementById('logout-modal').classList.remove('hidden');
        }

        function closeLogout() {
            document.getElementById('logout-modal').classList.add('hidden');
        }

        document.getElementById('logout-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLogout();
            }
        });
    </script>

    @livewireScripts
</body>
</html>
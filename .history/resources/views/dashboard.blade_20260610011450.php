<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Berco Cafe')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/tabler-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    @livewireStyles
    <style>
        :root {
            --color-background-primary: #ffffff;
            --color-background-secondary: #f5f5f5;
            --color-border-tertiary: #e5e5e5;
            --color-text-primary: #1a1a1a;
            --color-text-secondary: #666666;
            --color-accent: #D4752C;
            --color-sidebar: #3B1F0A;
            --border-radius-lg: 8px;
            --font-sans: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-sans);
            background: #F9F5F0;
            color: var(--color-text-primary);
        }

        .admin-wrap {
            display: flex;
            height: 100vh;
            background: #F9F5F0;
        }

        .admin-sidebar {
            width: 220px;
            background: var(--color-sidebar);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            overflow-y: auto;
            box-shadow: 2px 0 8px rgba(0,0,0,0.1);
        }

        .sb-logo {
            padding: 20px 16px 12px;
            border-bottom: 0.5px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
        }

        .sb-logo-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sb-logo-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #D4752C;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
        }

        .sb-logo-text {
            color: #fff;
            font-size: 15px;
            font-weight: 500;
        }

        .sb-sub {
            color: rgba(255,255,255,0.45);
            font-size: 11px;
            margin-top: 2px;
        }

        .sb-user {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            border-bottom: 0.5px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
        }

        .sb-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #D4752C;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 500;
            color: #fff;
            flex-shrink: 0;
        }

        .sb-uname {
            color: rgba(255,255,255,0.8);
            font-size: 12px;
        }

        .sb-role {
            color: rgba(255,255,255,0.4);
            font-size: 11px;
        }

        .sb-nav {
            flex: 1;
            padding: 10px 8px;
            overflow-y: auto;
        }

        .sb-section {
            color: rgba(255,255,255,0.3);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 8px 8px 4px;
            font-weight: 600;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border-radius: 8px;
            cursor: pointer;
            margin-bottom: 2px;
            color: rgba(255,255,255,0.6);
            font-size: 13px;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .nav-item i {
            font-size: 16px;
            flex-shrink: 0;
            width: 16px;
            text-align: center;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.06);
            color: rgba(255,255,255,0.85);
        }

        .nav-item.active {
            background: #D4752C;
            color: #fff;
            font-weight: 500;
        }

        .nav-badge {
            margin-left: auto;
            background: rgba(255,255,255,0.15);
            color: #fff;
            font-size: 10px;
            padding: 1px 6px;
            border-radius: 10px;
            flex-shrink: 0;
        }

        .nav-item.active .nav-badge {
            background: rgba(255,255,255,0.25);
        }

        .sb-footer {
            padding: 10px 8px;
            border-top: 0.5px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border-radius: 8px;
            cursor: pointer;
            color: rgba(255,200,150,0.7);
            font-size: 13px;
            width: 100%;
            border: none;
            background: transparent;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .logout-btn:hover {
            background: rgba(255,100,50,0.15);
            color: #F07050;
        }

        .logout-btn i {
            font-size: 16px;
        }

        .admin-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #F9F5F0;
            overflow: hidden;
        }

        .admin-topbar {
            background: var(--color-background-primary);
            border-bottom: 0.5px solid var(--color-border-tertiary);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .page-title {
            font-size: 16px;
            font-weight: 500;
            color: var(--color-text-primary);
        }

        .page-breadcrumb {
            font-size: 12px;
            color: var(--color-text-secondary);
            margin-top: 1px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .tb-search {
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--color-background-secondary);
            border: 0.5px solid var(--color-border-tertiary);
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 12px;
            color: var(--color-text-secondary);
        }

        .tb-search i {
            font-size: 15px;
        }

        .tb-btn {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 0.5px solid var(--color-border-tertiary);
            background: var(--color-background-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--color-text-secondary);
            transition: all 0.15s ease;
        }

        .tb-btn:hover {
            background: var(--color-background-secondary);
        }

        .tb-btn i {
            font-size: 15px;
        }

        .admin-content {
            flex: 1;
            overflow-y: auto;
            padding: 16px 20px;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 16px;
        }

        .stat-card {
            background: var(--color-background-primary);
            border: 0.5px solid var(--color-border-tertiary);
            border-radius: 10px;
            padding: 12px 14px;
        }

        .stat-label {
            font-size: 11px;
            color: var(--color-text-secondary);
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
        }

        .stat-label i {
            font-size: 13px;
        }

        .stat-val {
            font-size: 20px;
            font-weight: 600;
            color: var(--color-text-primary);
        }

        .stat-diff {
            font-size: 11px;
            margin-top: 2px;
        }

        .stat-diff.up {
            color: #2D7A4A;
        }

        .stat-diff.dn {
            color: #A32D2D;
        }

        .card {
            background: var(--color-background-primary);
            border: 0.5px solid var(--color-border-tertiary);
            border-radius: 10px;
            overflow: hidden;
        }

        .card-header {
            padding: 12px 16px;
            border-bottom: 0.5px solid var(--color-border-tertiary);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
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
            font-size: 15px;
            color: #D4752C;
        }

        .filter-row {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .filter-chip {
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 20px;
            border: 0.5px solid var(--color-border-tertiary);
            cursor: pointer;
            color: var(--color-text-secondary);
            background: transparent;
            transition: all 0.15s ease;
        }

        .filter-chip.active {
            background: #D4752C;
            color: #fff;
            border-color: #D4752C;
        }

        .tbl {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .tbl th {
            padding: 8px 14px;
            text-align: left;
            color: var(--color-text-secondary);
            font-weight: 600;
            font-size: 11px;
            border-bottom: 0.5px solid var(--color-border-tertiary);
            background: var(--color-background-secondary);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .tbl td {
            padding: 10px 14px;
            border-bottom: 0.5px solid var(--color-border-tertiary);
            color: var(--color-text-primary);
            vertical-align: middle;
        }

        .tbl tr:last-child td {
            border-bottom: none;
        }

        .tbl tbody tr:hover td {
            background: #FDF8F4;
        }

        .order-id {
            font-weight: 600;
            color: #D4752C;
            font-size: 13px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
        }

        .badge-paid {
            background: #EAF3DE;
            color: #27500A;
        }

        .badge-done {
            background: #E1F5EE;
            color: #085041;
        }

        .badge-pending {
            background: #FAEEDA;
            color: #633806;
        }

        .cust-row {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .cust-av {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #FAEEDA;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 500;
            color: #633806;
            flex-shrink: 0;
        }

        .act-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 7px;
            font-size: 11px;
            border: 0.5px solid var(--color-border-tertiary);
            cursor: pointer;
            color: var(--color-text-secondary);
            background: transparent;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .act-btn:hover {
            background: var(--color-background-secondary);
        }

        .act-btn.primary {
            background: #D4752C;
            color: #fff;
            border-color: #D4752C;
        }

        .act-btn.primary:hover {
            background: #c26620;
        }

        .act-btn i {
            font-size: 13px;
        }

        .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 3px;
        }

        .dot-paid {
            background: #4CAF72;
        }

        .dot-done {
            background: #1D9E75;
        }

        @media (max-width: 1024px) {
            .stat-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .admin-sidebar {
                width: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-wrap">
        <!-- SIDEBAR -->
        <aside class="admin-sidebar">
            <div class="sb-logo">
                <div class="sb-logo-row">
                    <div class="sb-logo-icon"><i class="ti ti-coffee"></i></div>
                    <div>
                        <div class="sb-logo-text">Berco Cafe</div>
                        <div class="sb-sub">Admin Panel</div>
                    </div>
                </div>
            </div>

            <div class="sb-user">
                <div class="sb-avatar">{{ auth()->user()->name[0] ?? 'A' }}</div>
                <div>
                    <div class="sb-uname">{{ auth()->user()->name ?? 'Administrator' }}</div>
                    <div class="sb-role">{{ auth()->user()->role?->nama_role ?? 'Admin' }}</div>
                </div>
            </div>

            <nav class="sb-nav">
                <div class="sb-section">Manajemen</div>
                <a href="{{ route('admin.menu') }}" class="nav-item {{ request()->routeIs('admin.menu') ? 'active' : '' }}">
                    <i class="ti ti-layout-dashboard"></i> Menu Produk
                </a>
                <a href="{{ route('admin.staffoption.index') }}" class="nav-item {{ request()->routeIs('admin.staffoption*') ? 'active' : '' }}">
                    <i class="ti ti-users"></i> Staff
                </a>
                <a href="{{ route('orders.index') }}" class="nav-item {{ request()->routeIs('orders.index') ? 'active' : '' }}">
                    <i class="ti ti-shopping-bag"></i> Orders
                    <span class="nav-badge">3</span>
                </a>
                <a href="{{ route('admin.history') }}" class="nav-item {{ request()->routeIs('admin.history') ? 'active' : '' }}">
                    <i class="ti ti-history"></i> Order History
                </a>

                <div class="sb-section" style="margin-top: 16px;">Lainnya</div>
                <a href="{{ route('admin.requests') }}" class="nav-item {{ request()->routeIs('admin.requests') ? 'active' : '' }}">
                    <i class="ti ti-message"></i> Requests
                    <span class="nav-badge">2</span>
                </a>
                <a href="{{ route('admin.receipt.edit') }}" class="nav-item {{ request()->routeIs('admin.receipt.edit') ? 'active' : '' }}">
                    <i class="ti ti-receipt"></i> Edit Receipt
                </a>
                <a href="{{ route('admin.stats') }}" class="nav-item {{ request()->routeIs('admin.stats') ? 'active' : '' }}">
                    <i class="ti ti-chart-bar"></i> Stats
                </a>
            </nav>

            <div class="sb-footer">
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="ti ti-logout"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="admin-main">
            <!-- TOP BAR -->
            <div class="admin-topbar">
                <div>
                    <div class="page-title">@yield('page-title', 'Dashboard')</div>
                    <div class="page-breadcrumb">Admin / @yield('breadcrumb', 'Home')</div>
                </div>
                <div class="topbar-right">
                    <div class="tb-search"><i class="ti ti-search"></i> Cari...</div>
                    <button class="tb-btn"><i class="ti ti-bell"></i></button>
                    <button class="tb-btn"><i class="ti ti-settings"></i></button>
                </div>
            </div>

            <!-- PAGE CONTENT -->
            <main class="admin-content">
                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
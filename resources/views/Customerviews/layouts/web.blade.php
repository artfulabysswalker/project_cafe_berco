<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cafe Berco')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            color: #333;
        }

        .header {
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #333;
        }

        .logo-area .cup-icon {
            font-size: 24px;
            color: #bf4f08;
        }

        .logo-text h1 {
            margin: 0;
            font-size: 20px;
            color: #bf4f08;
        }

        .nav {
            display: flex;
            gap: 30px;
            flex: 1;
            margin-left: 40px;
        }

        .nav ul {
            display: flex;
            gap: 30px;
            list-style: none;
        }

        .nav a {
            text-decoration: none;
            color: #666;
            font-size: 14px;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
            position: relative;
        }

        .nav a:hover,
        .nav a.active {
            color: #bf4f08;
        }

        .badge {
            background: #ff4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
            position: absolute;
            top: -8px;
            right: -8px;
        }

        .user-action {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn-login {
            background: #bf4f08;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.3s;
        }

        .btn-login:hover {
            background: #a23f06;
        }

        .status-badge {
            background: #e74c3c;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        main {
            min-height: calc(100vh - 200px);
            padding: 20px 0;
        }

        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
        }

        @media (max-width: 768px) {
            .nav {
                display: none;
            }

            .header-container {
                flex-wrap: wrap;
            }

            .nav ul {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <header class="header">
        <div class="header-container">
            <div class="logo-area">
                <i class="fas fa-coffee cup-icon"></i>
                <div class="logo-text">
                    <h1>BERCO</h1>
                </div>
            </div>
            <nav class="nav">
                <ul>
                    <li><a href="{{ route('home') }}"><i class="fas fa-home"></i> Beranda</a></li>
                    <li><a href="{{ route('menu.index') }}" {{ request()->routeIs('menu*') ? 'class=active' : '' }}><i class="fas fa-mug-hot"></i> Menu</a></li>
                    @auth
                        <li><a href="{{ route('redeem.index') }}" {{ request()->routeIs('redeem*') ? 'class=active' : '' }}><i class="fas fa-gift"></i> Tukar EXP</a></li>
                        <li><a href="{{ route('daily-quest') }}" {{ request()->routeIs('daily-quest') ? 'class=active' : '' }}><i class="fas fa-trophy"></i> Daily Quest</a></li>
                        <li><a href="{{ route('rewards') }}" {{ request()->routeIs('rewards') ? 'class=active' : '' }}><i class="fas fa-gift"></i> Rewards</a></li>
                        <li>
                            <a href="{{ route('cart.index') }}" {{ request()->routeIs('cart*') ? 'class=active' : '' }} style="position: relative;">
                                <i class="fas fa-shopping-cart"></i> Keranjang
                                <span class="badge" id="cart-badge" style="display: none;">0</span>
                            </a>
                        </li>
                        <li><a href="{{ route('order.history') }}" {{ request()->routeIs('order.history') ? 'class=active' : '' }}><i class="fas fa-history"></i> Pesanan</a></li>
                    @endauth
                </ul>
            </nav>
            <div class="user-action">
                @auth
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-login">Logout</button>
                    </form>
                @else
                    <a href="{{ route('testlogin') }}" class="btn-login">Login</a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <p>&copy; 2025 Cafe Berco. Semua hak cipta dilindungi.</p>
    </footer>

    <script>
        @auth
        // Update cart badge on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCartBadge();
        });

        function updateCartBadge() {
            fetch('{{ route('cart.count') }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('cart-badge');
                    if (badge && data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'flex';
                    } else if (badge) {
                        badge.style.display = 'none';
                    }
                });
        }
        @endauth
    </script>

    @yield('scripts')
</body>
</html>

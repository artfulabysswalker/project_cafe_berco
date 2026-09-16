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
            background: linear-gradient(135deg, #8b5e34 0%, #c78c4e 100%);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            width: 100%;
        }

        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 20px;
            gap: 20px;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: white;
            flex-shrink: 0;
        }

        .logo-area .cup-icon {
            font-size: 28px;
            color: white;
        }

        .logo-text h1 {
            margin: 0;
            font-size: 18px;
            color: white;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .nav {
            display: flex;
            gap: 5px;
            flex: 1;
        }

        .nav ul {
            display: flex;
            gap: 8px;
            list-style: none;
            flex-wrap: wrap;
        }

        .nav a {
            text-decoration: none;
            color: rgba(255,255,255,0.9);
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
            position: relative;
            padding: 8px 12px;
            border-radius: 6px;
            white-space: nowrap;
        }

        .nav a:hover,
        .nav a.active {
            color: white;
            background: rgba(255,255,255,0.2);
        }

        .badge {
            background: #ff4444;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
            position: absolute;
            top: 2px;
            right: 2px;
        }

        .user-action {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .btn-login {
            background: rgba(255,255,255,0.25);
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s;
            border: 1px solid rgba(255,255,255,0.4);
            cursor: pointer;
            border: none;
        }

        .btn-login:hover {
            background: rgba(255,255,255,0.35);
            transform: translateY(-1px);
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
            padding: 100px 0 20px 0;
            margin-top: 0;
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
                padding: 10px 15px;
                gap: 10px;
            }

            .nav ul {
                flex-direction: column;
                gap: 10px;
            }

            .logo-text h1 {
                font-size: 16px;
            }

            .logo-area .cup-icon {
                font-size: 22px;
            }

            .user-action {
                width: 100%;
            }

            .btn-login {
                flex: 1;
                text-align: center;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <header class="header">
        <div class="header-container">
            <div class="logo-area">
                <div class="logo-text">
                    <h1>BERCO</h1>
                </div>
            </div>
            <nav class="nav">
                <ul>
                    <li><a href="{{ route('home') }}"><i class="fas fa-home"></i> Beranda</a></li>
                    <li><a href="{{ route('menu.index') }}" {{ request()->routeIs('menu*') ? 'class=active' : '' }}><i class="fas fa-mug-hot"></i> Menu</a></li>
                    @auth
                        <li><a href="{{ route('daily-quest') }}" {{ request()->routeIs('daily-quest') ? 'class=active' : '' }}><i class="fas fa-trophy"></i> Daily Quest</a></li>
                        <li><a href="{{ route('redeem.index') }}" {{ request()->routeIs('redeem*') ? 'class=active' : '' }}><i class="fas fa-gift"></i> Tukar EXP</a></li>
                        <li><a href="{{ route('rewards') }}" {{ request()->routeIs('rewards') ? 'class=active' : '' }}><i class="fas fa-star"></i> Rewards</a></li>
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
                    <a href="{{ route('login') }}" class="btn-login">Login</a>
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

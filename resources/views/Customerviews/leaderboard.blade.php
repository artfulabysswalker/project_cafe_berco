<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - Berco Cafe</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="menu-page">
    <header class="header">
        <div class="container header-container">
            <div class="logo-area">
                <i class="fas fa-coffee cup-icon"></i>
                <h1 class="brand-name">BERCO</h1>
            </div>
            <nav class="nav">
                <ul>
                    <li><a href="/"><i class="fas fa-home"></i> Beranda</a></li>
                    <li><a href="/menu"><i class="fas fa-mug-hot"></i> Menu</a></li>
                    <li><a href="/leaderboard" class="active"><i class="fas fa-trophy"></i> Leaderboard</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container" style="margin-top: 50px; max-width: 600px;">
        <div class="menu-header" style="text-align: center; margin-bottom: 30px;">
            <h1><i class="fas fa-crown" style="color: gold;"></i> Top 10 Pelanggan</h1>
            <p>Apresiasi untuk para pecinta kopi setia kami</p>
        </div>

        <div class="cart-summary-card">
            @foreach($topUsers as $index => $user)
                <div class="summary-row" style="padding: 15px 0; border-bottom: 1px solid #eee;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <span style="font-weight: bold; width: 30px;">#{{ $index + 1 }}</span>
                        <div style="width: 40px; height: 40px; background: #800000; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <span style="font-size: 1.1rem;">{{ $user->name }}</span>
                    </div>
                    <span class="summary-val" style="color: #bf4f08; font-weight: bold;">
                        {{ number_format($user->exp) }} XP
                    </span>
                </div>
            @endforeach
            
            <div style="margin-top: 20px; text-align: center;">
                <a href="/menu" class="btn-checkout" style="text-decoration: none; display: inline-block;">Kembali Pesan Menu</a>
            </div>
        </div>
    </main>
</body>
</html>
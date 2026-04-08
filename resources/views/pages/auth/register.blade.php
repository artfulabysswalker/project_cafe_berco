<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Berco Cafe</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-header">
            <div class="login-logo-circle">
                <i class="fas fa-mug-hot"></i>
            </div>
            <h1>BERCO CAFE</h1>
            <p>Sistem Pemesanan Online</p>
        </div>

        @if ($errors->any())
            <div class="alert-warning" style="background:#fee2e2; border-color:#fecaca; color:#991b1b;">
                <ul style="margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="login-card">
            <h2>Selamat Datang</h2>
            <p class="subtitle">Login atau daftar untuk mulai memesan</p>

            <div class="login-toggle">
                <button type="button" onclick="window.location.href='{{ route('login') }}'">Login</button>
                <button class="active">Daftar</button>
            </div>

            <form method="POST" action="{{ route('register.store') }}">
                @csrf
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" placeholder="Nama Anda" value="{{ old('name') }}" required autofocus>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="email@example.com" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Minimal 6 karakter" required>
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password" required>
                </div>
                <button type="submit" class="btn-login-submit">Daftar</button>
            </form>

            <div class="divider"><span>Atau</span></div>

            <button class="btn-guest" type="button" onclick="window.location.href='{{ route('menu') }}'">
                <i class="far fa-user-circle"></i> Lanjutkan sebagai Guest
            </button>
            <p class="guest-note">Mode guest memungkinkan Anda memesan tanpa akun</p>
        </div>

        <a href="{{ route('home') }}" class="back-link">Kembali ke Beranda</a>
    </div>
</body>
</html>

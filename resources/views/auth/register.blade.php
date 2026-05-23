<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - Berco Cafe</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-slideIn {
            animation: slideInUp 0.6s ease-out;
        }
        
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.1);
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-[#FEF3C7] via-[#FEF9E7] to-[#FDE68A] antialiased text-[#422006]">

    {{-- Background Decoration --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 -right-32 w-64 h-64 bg-orange-200/30 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 -left-32 w-64 h-64 bg-yellow-200/20 rounded-full blur-3xl"></div>
    </div>

    <div class="relative flex flex-col items-center pt-8 md:pt-12 px-4 min-h-screen">
        {{-- Header Logo --}}
        <div class="animate-slideIn flex flex-col items-center mb-8 text-center">
            <a href="{{ route('menu.index') }}" class="mb-4 inline-block hover:scale-105 transition">
                <div class="w-20 h-20 bg-gradient-to-br from-[#78350F] to-[#5a2308] rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition">
                    <span class="text-3xl">☕</span>
                </div>
            </a>
            <h1 class="text-4xl md:text-5xl font-bold tracking-tight text-[#78350F]">BERCO CAFE</h1>
            <p class="text-zinc-600 mt-2 text-sm md:text-base">Sistem Pemesanan Online & Loyalty Program</p>
        </div>

        {{-- Status Alert --}}
        <div class="w-full max-w-lg bg-gradient-to-r from-[#ECF0FF] to-[#F3E8FF] border-l-4 border-[#10B981] rounded-lg p-4 mb-6 text-center shadow-sm animate-slideIn">
            <div class="flex items-center justify-center gap-2">
                <i class="fas fa-check-circle text-[#10B981]"></i>
                <p class="text-[#065F46] text-sm font-medium">
                    Daftar sekarang dan dapatkan <span class="font-bold">100 poin bonus!</span>
                </p>
            </div>
        </div>

        {{-- Register Card --}}
        <div class="w-full max-w-lg bg-white p-8 md:p-10 rounded-3xl shadow-2xl border border-orange-100/50 animate-slideIn" style="animation-delay: 0.1s;">
            
            {{-- Tabs --}}
            <div class="flex gap-2 bg-zinc-100 rounded-full p-1.5 mb-8">
                <a href="{{ route('login') }}" class="w-1/2 text-center text-zinc-500 py-2.5 rounded-full font-medium hover:text-zinc-800 transition">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
                <a href="#register" class="w-1/2 text-center bg-white text-[#78350F] py-2.5 rounded-full font-bold shadow-sm transition">
                    <i class="fas fa-user-plus mr-2"></i>Daftar
                </a>
            </div>

            <h2 class="text-2xl md:text-3xl font-bold mb-2">Buat Akun Baru</h2>
            <p class="text-zinc-500 mb-8 text-sm">Bergabunglah dengan komunitas pecinta kopi kami dan mulai kumpulkan poin reward!</p>

            {{-- Register Form --}}
            <form action="{{ route('register') }}" method="POST" class="space-y-5">
                @csrf
                
                {{-- Full Name Input --}}
                <div>
                    <label class="block text-sm font-semibold mb-2 ml-1 flex items-center gap-2">
                        <i class="fas fa-user text-[#78350F]"></i>
                        Nama Lengkap
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="input-focus w-full px-5 py-3.5 bg-zinc-50 border border-zinc-200 rounded-2xl focus:ring-2 focus:ring-orange-200 outline-none transition"
                        placeholder="Masukkan nama lengkap" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email Input --}}
                <div>
                    <label class="block text-sm font-semibold mb-2 ml-1 flex items-center gap-2">
                        <i class="fas fa-envelope text-[#78350F]"></i>
                        Email Address
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="input-focus w-full px-5 py-3.5 bg-zinc-50 border border-zinc-200 rounded-2xl focus:ring-2 focus:ring-orange-200 outline-none transition"
                        placeholder="masukkan@email.com" required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Input --}}
                <div>
                    <label class="block text-sm font-semibold mb-2 ml-1 flex items-center gap-2">
                        <i class="fas fa-lock text-[#78350F]"></i>
                        Password
                    </label>
                    <input type="password" name="password" id="password"
                        class="input-focus w-full px-5 py-3.5 bg-zinc-50 border border-zinc-200 rounded-2xl focus:ring-2 focus:ring-orange-200 outline-none transition"
                        placeholder="Minimal 6 karakter" required onchange="checkPasswordStrength()">
                    <p id="strength-text" class="text-xs text-zinc-500 mt-1"></p>
                    @error('password')
                        <p class="text-red-500 text-xs mt-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password Input --}}
                <div>
                    <label class="block text-sm font-semibold mb-2 ml-1 flex items-center gap-2">
                        <i class="fas fa-lock text-[#78350F]"></i>
                        Konfirmasi Password
                    </label>
                    <input type="password" name="password_confirmation"
                        class="input-focus w-full px-5 py-3.5 bg-zinc-50 border border-zinc-200 rounded-2xl focus:ring-2 focus:ring-orange-200 outline-none transition"
                        placeholder="Konfirmasi password Anda" required>
                    @error('password_confirmation')
                        <p class="text-red-500 text-xs mt-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Error Messages --}}
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl shadow-sm">
                        <div class="font-bold mb-3 flex items-center gap-2">
                            <i class="fas fa-exclamation-circle"></i> Daftar Gagal
                        </div>
                        <ul class="space-y-1 text-sm">
                            @foreach ($errors->all() as $error)
                                <li class="flex items-start gap-2">
                                    <span class="text-red-400 mt-1">•</span>
                                    <span>{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Submit Button --}}
                <button type="submit" class="w-full bg-gradient-to-r from-[#10B981] to-[#059669] text-white py-4 rounded-2xl font-bold text-lg hover:shadow-lg hover:shadow-green-900/30 transition-all transform hover:scale-[1.02] active:scale-95">
                    <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
                </button>
            </form>

            {{-- Divider --}}
            <div class="flex items-center gap-4 my-8">
                <div class="flex-1 h-px bg-zinc-200"></div>
                <span class="text-xs text-zinc-400 uppercase font-medium">atau</span>
                <div class="flex-1 h-px bg-zinc-200"></div>
            </div>

            {{-- Continue as Guest --}}
            <form method="POST" action="{{ route('guest.login') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 border-2 border-orange-100 bg-transparent text-[#78350F] py-3 rounded-2xl font-semibold hover:bg-orange-50 transition">
                    <i class="fas fa-user-secret"></i> Lanjutkan sebagai Guest
                </button>
            </form>

            {{-- Login Link --}}
            <p class="text-center text-sm text-zinc-600 mt-8">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-[#78350F] font-bold hover:underline">
                    Login sekarang
                </a>
            </p>
        </div>

        {{-- Footer Link --}}
        <p class="text-center text-xs text-zinc-600 mt-8 max-w-lg">
            Dengan mendaftar, Anda menyetujui <a href="#" class="text-[#78350F] font-semibold hover:underline">Syarat & Ketentuan</a> kami
        </p>
    </div>

    <script>
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthText = document.getElementById('strength-text');
            
            if (password.length < 6) {
                strengthText.innerHTML = '<i class="fas fa-times-circle"></i> Terlalu pendek (minimal 6 karakter)';
                strengthText.style.color = '#ef4444';
            } else if (password.length < 10) {
                strengthText.innerHTML = '<i class="fas fa-exclamation-circle"></i> Password sedang';
                strengthText.style.color = '#f59e0b';
            } else {
                strengthText.innerHTML = '<i class="fas fa-check-circle"></i> Password kuat!';
                strengthText.style.color = '#10b981';
            }
        }
    </script>
</body>
</html>

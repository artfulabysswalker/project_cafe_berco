<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Berco Cafe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FFFBEB] min-h-screen flex items-center justify-center p-6">
    <div class="bg-white w-full max-w-md rounded-[2rem] shadow-2xl overflow-hidden border border-orange-100">
        <div class="bg-[#78350F] p-8 text-center text-white">
            <i class="fas fa-user-plus text-4xl mb-4"></i>
            <h1 class="text-3xl font-bold italic">BERCO CAFE</h1>
            <p class="text-orange-200 text-sm mt-2">Daftar sekarang untuk mulai mengumpulkan EXP!</p>
        </div>

        <div class="p-8">
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 text-sm text-red-600">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-bold text-[#422006] mb-1">Nama Lengkap</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-zinc-400">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-zinc-200 focus:ring-2 focus:ring-[#C2410C] focus:border-[#C2410C] outline-none transition"
                            placeholder="Nama Anda">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-bold text-[#422006] mb-1">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-zinc-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-zinc-200 focus:ring-2 focus:ring-[#C2410C] focus:border-[#C2410C] outline-none transition"
                            placeholder="nama@email.com">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-bold text-[#422006] mb-1">Kata Sandi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-zinc-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" required
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-zinc-200 focus:ring-2 focus:ring-[#C2410C] focus:border-[#C2410C] outline-none transition"
                            placeholder="••••••••">
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-bold text-[#422006] mb-1">Konfirmasi Sandi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-zinc-400">
                            <i class="fas fa-check-double"></i>
                        </span>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-zinc-200 focus:ring-2 focus:ring-[#C2410C] focus:border-[#C2410C] outline-none transition"
                            placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="w-full bg-[#78350F] hover:bg-[#422006] text-white font-bold py-3 px-4 rounded-xl transition-all transform hover:scale-[1.02] shadow-lg mt-4">
                    Daftar Akun
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-[#422006]">
                Sudah punya akun? <a href="{{ route('login') }}" class="text-[#C2410C] font-bold hover:underline">Masuk di sini</a>
            </div>
        </div>
    </div>
</body>
</html>

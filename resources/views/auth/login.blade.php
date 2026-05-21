<x-guest-layout>

<div class="min-h-screen bg-[#F7F0D9] flex flex-col items-center px-5 py-8">

    <!-- Logo -->
    <div class="text-center">

        <div class="w-24 h-24 rounded-full bg-[#8B3E00]
        flex items-center justify-center mx-auto">

            ☕

        </div>

        <h1 class="text-5xl font-bold text-[#8B3E00] mt-4">
            BERCO CAFE
        </h1>

        <p class="text-gray-600 mt-2">
            Sistem Pemesanan Online
        </p>

    </div>


    <!-- Notifikasi -->

    <div class="mt-8 w-full max-w-xl">

        <div class="bg-[#FFF7E6]
        border border-yellow-300
        text-yellow-700
        px-5 py-4 rounded-xl">

            Kami sudah tutup. Buka kembali besok pukul 16.00 WIB

        </div>

    </div>


    <!-- Card -->

    <div class="bg-gray-100
    rounded-3xl
    border
    p-8
    mt-5
    w-full
    max-w-xl">

        <h2 class="text-2xl font-bold">
            Selamat Datang
        </h2>

        <p class="text-gray-500 mt-2">
            Login atau daftar untuk mulai memesan
        </p>

        <!-- Tab -->
        <div class="bg-gray-200 rounded-full p-1 flex mt-6">
            <button class="bg-white rounded-full py-2 flex-1 font-semibold shadow">Login</button>
            <a href="{{ route('register') }}" class="flex-1 py-2 font-semibold text-center">Daftar</a>
        </div>

        <!-- Session Status -->
        <div class="mt-4">
            <x-auth-session-status :status="session('status')" />
        </div>

        <form method="POST" action="{{ route('login') }}">

            @csrf

            <!-- Email -->

            <div class="mt-6">

                <label class="font-semibold">Email</label>

                <input
                type="email"
                name="email"
                value="{{ old('email') }}"

                placeholder="email@example.com"

                class="w-full mt-2 rounded-lg
                bg-gray-200
                border-0
                focus:ring-[#8B3E00]">

                @error('email') <p class="text-red-600 mt-1">{{ $message }}</p> @enderror

            </div>


            <!-- Password -->

            <div class="mt-4">

                <label class="font-semibold">Password</label>

                <input
                type="password"
                name="password"

                placeholder="******"

                class="w-full mt-2 rounded-lg
                bg-gray-200
                border-0
                focus:ring-[#8B3E00]">

                @error('password') <p class="text-red-600 mt-1">{{ $message }}</p> @enderror

            </div>


            <button
            type="submit"

            class="w-full mt-6
            bg-[#8B3E00]
            hover:bg-[#723000]
            text-white
            py-3
            rounded-lg
            font-bold">

                Login

            </button>

        </form>


        <div class="border-t mt-8 pt-6">

            <form method="POST" action="{{ route('guest.login') }}">
                @csrf
                <button type="submit" class="w-full bg-white border rounded-xl py-3 font-semibold">👤 Lanjutkan sebagai Guest</button>
            </form>

            <a href="{{ url('/') }}" class="mt-4 w-full text-[#8B3E00] font-semibold block text-center">Kembali ke Beranda</a>

        </div>

    </div>

</div>

</x-guest-layout>

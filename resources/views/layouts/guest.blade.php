<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Berco Cafe') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center" style="background: linear-gradient(135deg, #f5e6d3 0%, #f9ede1 100%);">
            <!-- Logo and Title -->
            <div class="mb-8 text-center">
                <!-- Berco Logo -->
                <img src="{{ asset('images/berco-logo.svg') }}" alt="Berco Cafe" class="w-28 h-28 mx-auto mb-3">
                
                <h1 class="text-3xl font-bold text-amber-900 mb-1">BERCO CAFE</h1>
                <p class="text-sm text-amber-700">Sistem Pemesanan Online</p>
            </div>

            <!-- Main Card -->
            <div class="w-full max-w-md px-6 py-8 bg-white rounded-lg shadow-lg">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center text-sm text-amber-700">
                <p>&copy; 2024 Berco Cafe. All rights reserved.</p>
            </div>
        </div>

        <script>
            // Real-time shop status
            function updateShopStatus() {
                const now = new Date();
                const hour = now.getHours();
                const minute = now.getMinutes();
                
                // Shop opens at 16:00 (4 PM) and closes at 23:59
                const openHour = 16;
                const closeHour = 24;
                const isOpen = hour >= openHour && hour < closeHour;
                
                const alertElement = document.getElementById('shopStatusAlert');
                const timeElement = document.getElementById('shopTime');
                
                if (alertElement) {
                    if (isOpen) {
                        alertElement.className = 'mb-4 p-3 bg-green-100 border border-green-300 rounded-lg text-center';
                        const timeUntilClose = ((23 - hour) * 60) + (60 - minute);
                        const closeHours = Math.floor(timeUntilClose / 60);
                        const closeMinutes = timeUntilClose % 60;
                        alertElement.innerHTML = `<p class="text-sm text-green-800">Toko sedang buka sampai jam <strong>${String(23).padStart(2, '0')}:00 WIB</strong> (${closeHours}j ${closeMinutes}m lagi)</p>`;
                    } else {
                        alertElement.className = 'mb-4 p-3 bg-orange-100 border border-orange-300 rounded-lg text-center';
                        const timeUntilOpen = ((openHour - hour) * 60) + (60 - minute);
                        const openHours = Math.floor(timeUntilOpen / 60);
                        const openMinutes = timeUntilOpen % 60;
                        alertElement.innerHTML = `<p class="text-sm text-orange-800">Kami sudah tutup. Buka kembali besok jam <strong>16:00 WIB</strong> (${openHours}j ${openMinutes}m lagi)</p>`;
                    }
                }
            }
            
            // Update on page load
            updateShopStatus();
            // Update every minute
            setInterval(updateShopStatus, 60000);
        </script>
    </body>
</html>
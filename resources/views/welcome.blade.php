<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }} - Cafe Berco</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-zinc-50 dark:bg-zinc-950 antialiased">
        <flux:header container class="bg-white border-b border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            <flux:brand href="/" logo="/favicon.svg" name="Cafe Berco" class="px-2" />
            <flux:spacer />
            <flux:navbar class="-mb-px">
                <flux:navbar.item href="#" current>Home</flux:navbar.item>
                <flux:navbar.item href="#">Menu</flux:navbar.item>
                <flux:navbar.item href="#">Order</flux:navbar.item>
            </flux:navbar>
        </flux:header>

        <flux:main container>
            <div class="py-10">
                <flux:heading size="xl" level="1">Selamat Datang di Cafe Berco</flux:heading>
                <flux:subheading>Mulai kembangkan menu favoritmu di sini.</flux:subheading>

                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Tempat komponen card menu dari Figma nanti --}}
                </div>
            </div>
        </flux:main>
    </body>
</html>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'Cafe Berco'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body>
    <div id="app">
        <header>
            <!-- simple header -->
        </header>

        <main class="container mx-auto py-6">
            @yield('content')
        </main>
    </div>

    @livewireScripts
</body>
</html>

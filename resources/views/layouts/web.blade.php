<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Cafe Berco')</title>
    @if (app()->environment('local'))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @livewireStyles
</head>
<body>
    <div id="app">
        @if(session('status'))
            <div class="alert alert-info">{{ session('status') }}</div>
        @endif

        @yield('content')
    </div>

    @livewireScripts
</body>
</html>

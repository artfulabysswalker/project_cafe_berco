<!DOCTYPE html>
<html>
<head>
    <title>Stats Dashboard</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        a { margin-right: 10px; padding: 6px 10px; text-decoration: none; border: 1px solid #ccc; }
        .active { font-weight: bold; background: #ddd; }
        .box { margin-top: 20px; }
    </style>
</head>
<body>

<h1>Stats Dashboard</h1>

@php
    $range = request('range', 'today');
@endphp

<!-- Tabs (NOW SAFE) -->
<div>
    <a href="?range=today" class="{{ $range === 'today' ? 'active' : '' }}">Today</a>
    <a href="?range=week" class="{{ $range === 'week' ? 'active' : '' }}">Week</a>
    <a href="?range=month" class="{{ $range === 'month' ? 'active' : '' }}">Month</a>
    <a href="?range=all" class="{{ $range === 'all' ? 'active' : '' }}">All</a>
</div>

<div class="box">
    <h2>{{ ucfirst($range) }}</h2>

    @include('admin.stats.partials.stats', ['data' => $stats])
</div>

</body>
</html>
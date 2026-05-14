<!-- resources/views/admin/stats/partials/stats.blade.php -->

<div class="stats-box">

    <p><strong>Total Sales:</strong> {{ $data['total_sales'] }}</p>
    <p><strong>Total Orders:</strong> {{ $data['total_orders'] }}</p>
    <p><strong>Avg Order:</strong> {{ $data['avg_order'] }}</p>

    <hr>

    <h3>Best Selling</h3>
    @foreach($data['best_selling'] as $item)
        <p>
            {{ $item->menu->name ?? 'Menu '.$item->id_menu }}
            ({{ $item->total }})
        </p>
    @endforeach

    <hr>

    <h3>Worst Selling</h3>
    @foreach($data['worst_selling'] as $item)
        <p>
            {{ $item->menu->name ?? 'Menu '.$item->id_menu }}
            ({{ $item->total }})
        </p>
    @endforeach

    <hr>

    <h3>Peak Hours</h3>
    @foreach($data['peak_hours'] as $hour)
        <p>{{ $hour->hour }}:00 - {{ $hour->total }} orders</p>
    @endforeach

    <hr>

    <h3>Customers Per Day</h3>
    @foreach($data['customers_per_day'] as $c)
        <p>{{ $c->date }} - {{ $c->total }} customers</p>
    @endforeach

</div>
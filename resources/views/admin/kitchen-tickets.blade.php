<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kitchen Ticket — {{ $order->public_code }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Courier New', monospace;
            color: #111;
            width: 80mm;
            margin: 0 auto;
            font-size: 13px;
        }
        .ticket {
            padding: 10px 8px;
            border-bottom: 1px dashed #999;
        }
        .ticket + .ticket { margin-top: 12px; }
        h1 { font-size: 15px; text-align: center; letter-spacing: 0.05em; }
        .sub { text-align: center; font-size: 10px; margin-top: 2px; }
        .hr { border-top: 1px dashed #333; margin: 8px 0; }
        .row { display: flex; justify-content: space-between; font-size: 12px; }
        .station { font-weight: bold; text-align: center; text-transform: uppercase; letter-spacing: 0.08em; font-size: 13px; padding: 4px 0; border: 2px solid #111; border-radius: 4px; margin: 8px 0; }
        ul { list-style: none; margin-top: 6px; }
        li { margin-bottom: 4px; }
        .qty { display: inline-block; min-width: 18px; background: #111; color: #fff; text-align: center; padding: 1px 3px; }
        .note { font-size: 10px; color: #333; margin-left: 24px; }
        .nowrap { white-space: nowrap; }
        @media print {
            body { width: 80mm; }
            .ticket { page-break-after: always; border-bottom: none; }
            .ticket:last-child { page-break-after: auto; }
        }
    </style>
</head>
<body>
@foreach($groups as $group)
    <div class="ticket">
        <h1>BERCO CAFE</h1>
        <div class="sub">KITCHEN TICKET</div>
        <div class="hr"></div>
        <div class="row"><span>No:</span><span class="nowrap">{{ $order->public_code }}</span></div>
        <div class="row"><span>Meja:</span><span>{{ $order->table?->nama_meja ?? 'Take Away' }}</span></div>
        <div class="row"><span>Waktu:</span><span>{{ $order->tanggal->format('H:i d/m') }}</span></div>
        <div class="row"><span>Pelanggan:</span><span>{{ $order->customer_name ?: $order->nama_pelanggan }}</span></div>
        <div class="hr"></div>

        <div class="station">{{ $group['station'] }}</div>

        <ul>
            @foreach($group['items'] as $item)
                <li>
                    <span class="qty">{{ $item->quantity }}</span>
                    {{ $item->menu?->nama_menu ?? 'Item' }}
                    @if($item->notes)
                        <div class="note">📝 {{ $item->notes }}</div>
                    @endif
                </li>
            @endforeach
        </ul>

        <div class="hr"></div>
        <div class="sub">— Bon Kerja Dapur —</div>
    </div>
@endforeach

<script>
    window.addEventListener('load', function () {
        if (window.print) {
            setTimeout(function () { window.print(); }, 300);
        }
    });
</script>
</body>
</html>
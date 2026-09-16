<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Order #{{ $order->id_order }}</title>
    <style>
        @page {
            margin: 4mm 4mm;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 9.5px;
            color: #000;
            line-height: 1.35;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }

        .header {
            text-align: center;
            margin-bottom: 6px;
        }

        .cafe-name {
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .cafe-sub {
            font-size: 8.5px;
            color: #333;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-tbl td {
            font-size: 8.5px;
            padding: 1px 0;
        }

        .items-tbl td {
            font-size: 9px;
            padding: 1.5px 0;
            vertical-align: top;
        }

        .totals-tbl td {
            font-size: 9px;
            padding: 1.5px 0;
        }

        .grand-row td {
            font-size: 11px;
            font-weight: bold;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 3px 0;
        }

        .footer {
            text-align: center;
            font-size: 8.5px;
            margin-top: 6px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="cafe-name">{{ $settings->cafe_name ?? 'BERCO CAFE' }}</div>
        <div class="cafe-sub">{{ $settings->address ?? 'Jl. SMA Negeri 1, Krajan, Purwoharjo, Banyuwangi' }}</div>
        <div class="cafe-sub">Telp/WA: {{ $settings->phone ?? '+62 821 4103 1234' }}</div>
    </div>

    <div class="divider"></div>

    <table class="meta-tbl">
        <tr>
            <td style="width: 32%;">No. Struk</td>
            <td style="width: 4%;">:</td>
            <td><strong>#{{ $order->id_order }}</strong></td>
        </tr>
        <tr>
            <td>Waktu</td>
            <td>:</td>
            <td>{{ $order->tanggal ? \Carbon\Carbon::parse($order->tanggal)->format('d/m/Y H:i') : ($order->created_at ? $order->created_at->format('d/m/Y H:i') : date('d/m/Y H:i')) }}</td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td>:</td>
            <td>{{ $order->user?->name ?? 'Kasir Berco' }}</td>
        </tr>
        <tr>
            <td>Pelanggan</td>
            <td>:</td>
            <td>{{ $order->nama_pelanggan ?: 'Pelanggan Umum' }}</td>
        </tr>
        <tr>
            <td>Metode</td>
            <td>:</td>
            <td><strong>{{ strtoupper($order->metode_pembayaran ?? ($order->payment_method ?? 'CASH')) }}</strong></td>
        </tr>
    </table>

    <div class="divider"></div>

    <table class="items-tbl">
        @foreach($order->items as $item)
            @php
                $menuName = $item->menu?->nama_menu ?? ($item->menu?->name ?? 'Menu Produk');
                $unitPrice = $item->harga_satuan ?? ($item->menu?->harga ?? ($item->subtotal / max(1, $item->quantity)));
            @endphp
            <tr>
                <td colspan="2" class="font-bold">{{ $menuName }}</td>
            </tr>
            <tr>
                <td style="padding-left: 6px; color: #333;">{{ $item->quantity }} x {{ number_format($unitPrice, 0, ',', '.') }}</td>
                <td class="text-right font-bold">{{ number_format($item->subtotal ?? ($unitPrice * $item->quantity), 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <table class="totals-tbl">
        <tr>
            <td>Subtotal</td>
            <td class="text-right">{{ number_format($order->subtotal ?? $order->total_harga, 0, ',', '.') }}</td>
        </tr>
        @if(!empty($order->discount_amount) && $order->discount_amount > 0)
            <tr>
                <td>Diskon</td>
                <td class="text-right">-{{ number_format($order->discount_amount, 0, ',', '.') }}</td>
            </tr>
        @endif
        @if(!empty($order->service_charge) && $order->service_charge > 0)
            <tr>
                <td>Layanan</td>
                <td class="text-right">{{ number_format($order->service_charge, 0, ',', '.') }}</td>
            </tr>
        @endif
        <tr class="grand-row">
            <td>TOTAL</td>
            <td class="text-right">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
        </tr>
        @if(!empty($order->uang_diterima) && $order->uang_diterima > 0)
            <tr>
                <td>Bayar Tunai</td>
                <td class="text-right">{{ number_format($order->uang_diterima, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kembalian</td>
                <td class="text-right">{{ number_format(max(0, $order->uang_diterima - $order->total_harga), 0, ',', '.') }}</td>
            </tr>
        @endif
    </table>

    <div class="footer">
        <p>{{ $settings->footer_message ?? 'Terima kasih atas kunjungan Anda!' }}</p>
        @if(!empty($settings->wifi_name))
            <p style="font-size: 8px;">WiFi: {{ $settings->wifi_name }} | Pass: {{ $settings->wifi_password ?? '-' }}</p>
        @endif
        <p style="font-size: 7.5px; color: #555; margin-top: 3px;">*** LUNAS / TERIMA KASIH ***</p>
    </div>

</body>
</html>

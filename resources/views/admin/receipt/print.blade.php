<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $order->id_order }} - {{ $settings->cafe_name ?? 'BERCO CAFE' }}</title>
    <style>
        @page {
            size: auto;
            margin: 0mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            background-color: #f1f5f9;
            color: #000000;
            font-family: 'Courier New', Courier, Consolas, monospace;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Receipt Roll Wrapper */
        .receipt-container {
            width: 76mm; /* Standard 80mm/58mm Thermal Receipt Width */
            max-width: 100%;
            margin: 20px auto;
            background: #ffffff;
            padding: 12px 14px 20px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
            font-size: 11.5px;
            line-height: 1.35;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }

        .header {
            text-align: center;
            margin-bottom: 8px;
        }

        .logo {
            max-width: 55px;
            max-height: 55px;
            margin: 0 auto 6px;
            display: block;
            object-fit: contain;
        }

        .cafe-title {
            font-size: 15px;
            font-weight: 900;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .cafe-info {
            font-size: 10px;
            color: #222;
            line-height: 1.3;
        }

        .divider {
            border-top: 1.5px dashed #000000;
            margin: 8px 0;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
        }

        .meta-table td {
            vertical-align: top;
            padding: 1.5px 0;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .items-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .item-subline {
            padding-left: 8px;
            font-size: 10px;
            color: #333;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .totals-table td {
            padding: 2px 0;
        }

        .grand-total {
            font-size: 13px;
            font-weight: 900;
            border-top: 1.5px dashed #000000;
            border-bottom: 1.5px dashed #000000;
            padding: 4px 0 !important;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 8px;
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .wifi-box {
            background: #f8fafc;
            border: 1px dashed #666;
            padding: 3px 6px;
            font-size: 9.5px;
            margin-top: 4px;
        }

        .closing-tag {
            margin-top: 4px;
            font-size: 9px;
            letter-spacing: 1px;
        }

        /* PRINT STYLES FOR THERMAL PRINTER */
        @media print {
            html, body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 76mm !important;
            }

            .receipt-container {
                width: 76mm !important;
                max-width: 76mm !important;
                margin: 0 auto !important;
                padding: 6px 4px 14px !important;
                box-shadow: none !important;
                border: none !important;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="receipt-container">
        {{-- Header & Cafe Info --}}
        <div class="header">
            @if(!empty($settings->logo))
                <img src="{{ asset('storage/' . $settings->logo) }}" alt="Logo" class="logo">
            @endif
            <div class="cafe-title">{{ $settings->cafe_name ?? 'BERCO CAFE' }}</div>
            <div class="cafe-info">{{ $settings->address ?? 'Jl. SMA Negeri 1, Krajan, Purwoharjo, Banyuwangi' }}</div>
            <div class="cafe-info">Telp/WA: {{ $settings->phone ?? '+62 821 4103 1234' }}</div>
        </div>

        <div class="divider"></div>

        {{-- Meta Order Details --}}
        <table class="meta-table">
            <tr>
                <td style="width: 36%;">No. Struk</td>
                <td style="width: 4%;">:</td>
                <td><strong>#{{ $order->id_order }}</strong></td>
            </tr>
            <tr>
                <td>Waktu</td>
                <td>:</td>
                <td>{{ $order->tanggal ? \Carbon\Carbon::parse($order->tanggal)->format('d/m/y H:i') : ($order->created_at ? $order->created_at->format('d/m/y H:i') : date('d/m/y H:i')) }}</td>
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
                <td>Pembayaran</td>
                <td>:</td>
                <td><strong>{{ strtoupper($order->metode_pembayaran ?? ($order->payment_method ?? 'CASH')) }}</strong></td>
            </tr>
        </table>

        <div class="divider"></div>

        {{-- Items List --}}
        <table class="items-table">
            @foreach($order->items as $item)
                @php
                    $menuName = $item->menu?->nama_menu ?? ($item->menu?->name ?? 'Menu Item');
                    $unitPrice = $item->harga_satuan ?? ($item->menu?->harga ?? ($item->subtotal / max(1, $item->quantity)));
                @endphp
                <tr>
                    <td colspan="2" class="font-bold">{{ $menuName }}</td>
                </tr>
                <tr>
                    <td class="item-subline">{{ $item->quantity }} x {{ number_format($unitPrice, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">{{ number_format($item->subtotal ?? ($unitPrice * $item->quantity), 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </table>

        <div class="divider"></div>

        {{-- Totals --}}
        <table class="totals-table">
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
                    <td>Biaya Layanan</td>
                    <td class="text-right">{{ number_format($order->service_charge, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr class="grand-total">
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

        {{-- Footer --}}
        <div class="footer">
            <p>{{ $settings->footer_message ?? 'Terima kasih atas kunjungan Anda!' }}</p>
            @if(!empty($settings->wifi_name))
                <div class="wifi-box">
                    WiFi: <strong>{{ $settings->wifi_name }}</strong> | Pass: <strong>{{ $settings->wifi_password ?? '-' }}</strong>
                </div>
            @endif
            <p class="closing-tag">*** LUNAS / TERIMA KASIH ***</p>
        </div>
    </div>

</body>
</html>

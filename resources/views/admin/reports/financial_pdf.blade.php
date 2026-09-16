<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan - Berco Cafe</title>
    <style>
        @page { margin: 20px 30px; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8.5pt;
            color: #1C1917;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #1C1917;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .brand-name {
            font-size: 18pt;
            font-weight: bold;
            color: #8B4513;
            margin: 0;
        }
        .brand-sub {
            font-size: 9pt;
            color: #444;
            margin: 2px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .doc-info {
            text-align: right;
            font-size: 8pt;
            color: #555;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .summary-box {
            background-color: #F9FAFB;
            border: 1px solid #E5E7EB;
            padding: 8px;
            text-align: center;
            width: 25%;
        }
        .summary-label {
            font-size: 7.5pt;
            color: #6B7280;
            text-transform: uppercase;
            display: block;
            margin-bottom: 3px;
            font-weight: bold;
        }
        .summary-value {
            font-size: 10pt;
            font-weight: bold;
            color: #111827;
        }
        .section-title {
            font-size: 9.5pt;
            font-weight: bold;
            margin: 12px 0 6px 0;
            color: #1C1917;
            background-color: #F3F4F6;
            padding: 4px 8px;
            border-left: 3px solid #8B4513;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .data-table th {
            background-color: #F3F4F6;
            color: #374151;
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
            padding: 6px;
            border: 1px solid #E5E7EB;
            text-align: left;
        }
        .data-table td {
            padding: 5px 6px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 8pt;
            vertical-align: top;
            word-wrap: break-word;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        .footer-table {
            width: 100%;
            margin-top: 30px;
        }
        .signature-box {
            text-align: center;
            width: 180px;
        }
        .signature-space {
            height: 50px;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td width="65%">
                <h1 class="brand-name">BERCO CAFE</h1>
                <p class="brand-sub">Laporan Ringkasan Penjualan & Transaksi</p>
                <p style="font-size: 8pt; color: #666; margin: 0;">Jl. Raya Berco No. 123, Purwoharjo, Banyuwangi</p>
            </td>
            <td width="35%" class="doc-info">
                <strong>Periode:</strong> {{ $startDate }} - {{ $endDate }}<br>
                <strong>Tanggal Cetak:</strong> {{ $printedAt }}<br>
                <strong>Staff Bertugas:</strong> {{ $selectedStaff }}
            </td>
        </tr>
    </table>

    {{-- Executive Summary (4 Boxes) --}}
    <table class="summary-table">
        <tr>
            <td class="summary-box">
                <span class="summary-label">Total Omzet</span>
                <span class="summary-value">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</span>
            </td>
            <td class="summary-box" style="border-left: none;">
                <span class="summary-label">Total Pengeluaran</span>
                <span class="summary-value" style="color: #dc2626;">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</span>
            </td>
            <td class="summary-box" style="border-left: none;">
                <span class="summary-label">Laba Bersih</span>
                <span class="summary-value" style="color: #059669;">Rp {{ number_format($labaBersih, 0, ',', '.') }}</span>
            </td>
            <td class="summary-box" style="border-left: none;">
                <span class="summary-label">Total Transaksi</span>
                <span class="summary-value">{{ $countTotal }} Trx</span>
            </td>
        </tr>
    </table>

    {{-- Payment Method Breakdown --}}
    <div class="section-title">Ringkasan Penerimaan Berdasarkan Metode Pembayaran</div>
    <table class="data-table" style="margin-bottom: 15px;">
        <thead>
            <tr>
                <th width="40%">Metode Pembayaran</th>
                <th class="text-center" width="20%">Jumlah (Trx)</th>
                <th class="text-center" width="15%">Persentase</th>
                <th class="text-right" width="25%">Total Penerimaan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paymentBreakdown as $data)
            <tr>
                <td>{{ $data['method'] }}</td>
                <td class="text-center">{{ $data['count'] }} Trx</td>
                <td class="text-center">{{ $data['percentage'] }}%</td>
                <td class="text-right font-bold">Rp {{ number_format($data['total'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot style="background-color: #F3F4F6;">
            <tr>
                <td class="font-bold">TOTAL PENERIMAAN</td>
                <td class="text-center font-bold">{{ $countTotal }} Trx</td>
                <td class="text-center font-bold">100%</td>
                <td class="text-right font-bold">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Detailed Transactions --}}
    <div class="section-title">Rincian Butiran Transaksi</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="15%">ID Nota</th>
                <th width="15%">Waktu</th>
                <th width="12%">Kasir</th>
                <th width="28%">Pesanan</th>
                <th width="10%" class="text-center">Bayar</th>
                <th width="15%" class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($allOrders as $index => $order)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>#{{ $order->id_order }}</td>
                <td>{{ $order->tanggal ? $order->tanggal->format('d/m/y H:i') : '-' }}</td>
                <td>{{ $order->cashier_name ?: ($order->user?->name ?: 'Staff') }}</td>
                <td>
                    @foreach($order->items as $item)
                        {{ $item->quantity }}x {{ $item->menu?->nama_menu ?? 'Item' }}{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </td>
                <td class="text-center">{{ strtoupper($order->payment_method ?: 'CASH') }}</td>
                <td class="text-right font-bold">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px; color: #999;">Tidak ada data transaksi.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #F3F4F6;">
                <td colspan="6" class="text-right font-bold">TOTAL KESELURUHAN</td>
                <td class="text-right font-bold">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Verification Footer --}}
    <table class="footer-table">
        <tr>
            <td width="65%"></td>
            <td width="35%">
                <div class="signature-box" style="float: right;">
                    <p style="margin-bottom: 5px;">Disediakan / Dicetak oleh:</p>
                    <p class="font-bold">{{ auth()->user()->name }}</p>
                    <div class="signature-space"></div>
                    <p class="font-bold">__________________________</p>
                    <p style="margin-top: 5px;">Pengurus / Pemilik Berco Cafe</p>
                </div>
            </td>
        </tr>
    </table>

    <div style="position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 7pt; color: #999;">
        Laporan ini dihasilkan secara otomatis oleh Sistem POS Berco Cafe pada {{ date('d/m/Y H:i:s') }}
    </div>

</body>
</html>

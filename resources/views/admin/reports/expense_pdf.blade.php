<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rincian Pengeluaran - Berco Cafe</title>
    <style>
        @page {
            margin: 25px 30px;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1a1a1a;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        .report-header {
            text-align: center;
            border-bottom: 2px solid #231206;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .cafe-title {
            font-size: 18px;
            font-weight: bold;
            color: #231206;
            letter-spacing: 1px;
            margin: 0 0 3px 0;
            text-transform: uppercase;
        }

        .report-subtitle {
            font-size: 12px;
            font-weight: bold;
            color: #dc2626;
            margin: 0 0 4px 0;
        }

        .meta-info {
            font-size: 9px;
            color: #555555;
            margin: 0;
        }

        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 14px;
        }

        .table-data th {
            background-color: #fef2f2;
            color: #991b1b;
            font-weight: bold;
            font-size: 9.5px;
            text-transform: uppercase;
            border: 1px solid #fecdd3;
            padding: 7px 10px;
            text-align: left;
        }

        .table-data td {
            border: 1px solid #e5e7eb;
            padding: 6px 10px;
            vertical-align: middle;
            font-size: 9.5px;
        }

        .table-data tr:nth-child(even) {
            background-color: #fafafa;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }

        .total-row {
            background-color: #fee2e2 !important;
            font-weight: bold;
            color: #991b1b;
        }

        .total-row td {
            border: 1.5px solid #fca5a5;
            padding: 8px 10px;
            font-size: 11px;
        }

        .footer-note {
            margin-top: 30px;
            text-align: center;
            font-size: 8.5px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <div class="report-header">
        <h1 class="cafe-title">BERCO CAFE</h1>
        <div class="report-subtitle">LAPORAN RINCIAN PENGELUARAN OPERASIONAL</div>
        <p class="meta-info">
            <strong>Periode:</strong> {{ $periodeLabel }} &nbsp;|&nbsp; 
            <strong>Akun Kasir:</strong> {{ $staffLabel ?? 'Semua Staff' }} &nbsp;|&nbsp; 
            <strong>Shift:</strong> {{ $shiftLabel ?? 'Semua Shift' }} &nbsp;|&nbsp; 
            <strong>Waktu Cetak:</strong> {{ $printedAt ?? date('d/m/Y H:i') }}
        </p>
    </div>

    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 18%;">Waktu & Tanggal</th>
                <th style="width: 50%;">Deskripsi / Keperluan Pengeluaran</th>
                <th style="width: 15%;">Kasir / Operator Shift</th>
                <th style="width: 12%;" class="text-right">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $index => $expense)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $expense->tanggal ? $expense->tanggal->format('d/m/Y H:i') : '-' }}</td>
                    <td><strong>{{ $expense->deskripsi }}</strong></td>
                    <td>{{ $expense->operator ?: ($expense->user?->name ?? 'Kasir') }}</td>
                    <td class="text-right text-bold" style="color: #dc2626;">Rp {{ number_format($expense->nominal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 16px; color: #9ca3af;">Tidak ada catatan pengeluaran untuk filter yang dipilih.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="4" class="text-right text-bold">TOTAL PENGELUARAN OPERASIONAL:</td>
                <td class="text-right text-bold">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer-note">
        Laporan Rincian Pengeluaran Kasir Berco Cafe.<br>
        Dokumen resmi rekonsiliasi pengeluaran operasional toko.
    </div>

</body>
</html>

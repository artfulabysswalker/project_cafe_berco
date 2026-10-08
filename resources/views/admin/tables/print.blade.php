<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR — {{ $table->nama_meja }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f4f5;
            color: #1c1917;
        }

        .toolbar {
            max-width: 800px;
            margin: 24px auto;
            display: flex;
            gap: 12px;
            justify-content: space-between;
            align-items: center;
        }

        .toolbar a {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 8px;
            background: #fff;
            border: 1px solid #d6d3d1;
            color: #44403c;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }

        .toolbar button {
            padding: 10px 18px;
            border: 0;
            border-radius: 8px;
            background: #C27835;
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
        }

        .toolbar button:hover { background: #a8642a; }

        .qr-card {
            width: 320px;
            margin: 24px auto;
            background: #fff;
            border: 2px solid #1c1917;
            border-radius: 16px;
            padding: 28px 24px;
            text-align: center;
        }

        .brand {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #1c1917;
        }

        .brand span { color: #C27835; }

        .hint {
            margin-top: 4px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #78716c;
        }

        .qr-img {
            margin: 20px auto;
            width: 200px;
            height: 200px;
            border: 1px solid #e7e5e4;
            border-radius: 12px;
            padding: 8px;
            background: #fff;
        }

        .qr-img img { width: 100%; height: 100%; display: block; }

        .table-name {
            font-size: 28px;
            font-weight: 800;
            color: #C27835;
        }

        .table-meta {
            margin-top: 4px;
            font-size: 13px;
            color: #57534e;
        }

        .cta {
            margin-top: 14px;
            padding: 10px;
            border-radius: 8px;
            background: #C27835;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
        }

        .expired {
            margin-top: 10px;
            font-size: 11px;
            color: #78716c;
        }

        .footer {
            margin-top: 14px;
            font-size: 11px;
            color: #a8a29e;
        }

        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .qr-card { margin: 0 auto; border-width: 2px; box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <a href="{{ route('admin.tables.qrcode', $table->id_meja) }}">← Kembali ke QR</a>
        <button type="button" onclick="window.print()">🖨 Cetak</button>
    </div>

    <div class="qr-card">
        <div class="brand">BERCO<span>.</span> CAFE</div>
        <div class="hint">Scan QR untuk pesan</div>

        <div class="qr-img">
            @if($imageExists)
                <img src="{{ route('admin.tables.qrcode.image', $table->id_meja) }}" alt="QR Code {{ $table->nama_meja }}">
            @endif
        </div>

        <div class="table-name">{{ $table->nama_meja }}</div>

        <div class="table-meta">
            @if($table->kapasitas) Kapasitas {{ $table->kapasitas }} orang @endif
            @if($table->kapasitas && $table->area) · @endif
            @if($table->area) Area {{ $table->area }} @endif
        </div>

        <div class="cta">Pindai &amp; Pesan Sekarang</div>

        @if($qr->expired_at)
            <div class="expired">Berlaku sampai {{ $qr->expired_at->format('d/m/Y H:i') }}</div>
        @endif

        <div class="footer">Ada masalah? Hubungi kasir kami.</div>
    </div>
</body>
</html>

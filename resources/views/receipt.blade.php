<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - Berco Cafe</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .receipt-container {
            max-width: 600px;
            margin: 2rem auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
        }
        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
        .receipt-logo {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .receipt-details {
            margin: 1rem 0;
        }
        .receipt-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .receipt-total {
            font-weight: bold;
            font-size: 1.2rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #eee;
        }
        .receipt-footer {
            text-align: center;
            margin-top: 2rem;
            color: #666;
        }
        .btn-print {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 1rem;
        }
        .btn-print:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="receipt-container">
            <div class="receipt-header">
                <div class="receipt-logo">☕ BERCO CAFE</div>
                <h2>Struk Pembayaran</h2>
                <p>Terima Kasih Atas Kunjungan Anda</p>
            </div>

            <div class="receipt-details">
                <div class="receipt-item">
                    <span><strong>No. Order:</strong></span>
                    <span>#{{ str_pad($order->id_order, 4, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="receipt-item">
                    <span><strong>Tanggal:</strong></span>
                    <span>{{ $order->tanggal->format('d/m/Y H:i') }}</span>
                </div>
                <div class="receipt-item">
                    <span><strong>Pelanggan:</strong></span>
                    <span>{{ $order->nama_pelanggan }}</span>
                </div>
                <div class="receipt-item">
                    <span><strong>Status:</strong></span>
                    <span>{{ $order->status_pembayaran }}</span>
                </div>
            </div>

            <div class="receipt-items">
                <h3>Detail Pesanan:</h3>
                @foreach($order->orderItems as $item)
                <div class="receipt-item">
                    <span>{{ $item->menu->nama_menu }} x{{ $item->qty }}</span>
                    <span>Rp {{ number_format($item->harga * $item->qty, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>

            <div class="receipt-total">
                <div class="receipt-item">
                    <span><strong>Total Pembayaran:</strong></span>
                    <span>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="receipt-footer">
                <p>Terima kasih telah memesan di Berco Cafe</p>
                <p>Semoga hari Anda menyenangkan!</p>

                <button onclick="window.print()" class="btn-print">
                    <i class="fas fa-print"></i> Print Struk
                </button>
                <a href="{{ route('menu') }}" class="btn-print" style="background: #2196F3;">
                    <i class="fas fa-arrow-left"></i> Kembali ke Menu
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <script>
        alert('{{ session('success') }}');
    </script>
    @endif
</body>
</html>
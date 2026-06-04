@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <!-- Payment Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">💳 Pembayaran QRIS</h1>
        <p class="text-gray-600">Selesaikan pembayaran dengan mudah menggunakan QRIS</p>
    </div>

    <!-- Order Summary -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">📋 Ringkasan Pesanan</h2>
        
        <div class="space-y-2 mb-4">
            <div class="flex justify-between">
                <span class="text-gray-600">Order ID:</span>
                <span class="font-semibold">#{{ $order->id_order }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Nama Pelanggan:</span>
                <span class="font-semibold">{{ $order->nama_pelanggan }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Tanggal Pesanan:</span>
                <span class="font-semibold">{{ $order->tanggal->format('d M Y H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Tipe Layanan:</span>
                <span class="font-semibold">
                    {{ $order->service_type === 'dine_in' ? '🍽️ Dine In' : '🛍️ Take Away' }}
                </span>
            </div>
        </div>

        <hr class="my-4">

        <!-- Order Items -->
        <div class="mb-4">
            <h3 class="font-semibold text-gray-800 mb-2">Items:</h3>
            <div class="space-y-2">
                @foreach($order->orderItems as $item)
                <div class="flex justify-between text-sm">
                    <span>{{ $item->menu->nama_menu }} x{{ $item->quantity }}</span>
                    <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <hr class="my-4">

        <!-- Total -->
        <div class="space-y-2">
            <div class="flex justify-between text-gray-700">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-gray-700">
                <span>PPN (10%):</span>
                <span>Rp {{ number_format($tax, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-lg font-bold text-amber-700 bg-amber-50 p-2 rounded">
                <span>Total Pembayaran:</span>
                <span>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- QRIS Payment Method -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">📱 QRIS Payment</h2>
        
        <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg mb-4">
            <p class="text-blue-700 font-semibold">✅ Pembayaran dengan QRIS</p>
            <p class="text-sm text-blue-600 mt-1">Pembayaran aman menggunakan kode QRIS yang dapat discan dengan aplikasi mobile banking Anda</p>
        </div>

        @if($qrisTransaction && $qrisTransaction->status === 'paid')
        <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-700 font-semibold">✅ Pembayaran Berhasil</p>
            <p class="text-sm text-green-600 mt-1">Pembayaran Anda telah diconfirm pada {{ $qrisTransaction->paid_at->format('d M Y H:i') }}</p>
        </div>
        @elseif($qrisTransaction && $qrisTransaction->status === 'failed')
        <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-700 font-semibold">❌ Pembayaran Gagal</p>
            <p class="text-sm text-red-600 mt-1">Pembayaran Anda gagal. Silahkan coba lagi.</p>
        </div>
        @elseif($qrisTransaction && $qrisTransaction->isExpired())
        <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-yellow-700 font-semibold">⏳ QRIS Code Expired</p>
            <p class="text-sm text-yellow-600 mt-1">Kode QRIS telah kadaluarsa. Silahkan buat invoice baru.</p>
        </div>
        @elseif($qrisTransaction)
        <!-- Show QRIS Invoice Details -->
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-4">
            <div class="text-sm text-gray-700 space-y-2">
                <div>
                    <p class="text-gray-600 text-xs">Invoice ID</p>
                    <p class="font-mono font-semibold text-gray-800">{{ $qrisTransaction->invoice_id ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-xs">Jumlah Pembayaran</p>
                    <p class="font-semibold text-lg text-amber-700">Rp {{ number_format($qrisTransaction->amount, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-xs">Kadaluarsa</p>
                    <p class="font-semibold text-gray-800">{{ $qrisTransaction->expires_at->format('d M Y H:i') }} ({{ $qrisTransaction->expires_at->diffForHumans() }})</p>
                </div>
            </div>
        </div>

        <!-- QRIS Code Display -->
        <div class="p-4 bg-white border-2 border-blue-300 rounded-lg text-center mb-4">
            <p class="text-gray-700 font-semibold mb-3">📲 Pindai kode QRIS di bawah dengan aplikasi mobile banking Anda:</p>
            <div id="qris-container" class="flex justify-center">
                <canvas id="qrcode" style="width: 280px; height: 280px;"></canvas>
            </div>
            <p class="text-xs text-gray-500 mt-3">atau copy manual code QRIS di bawah</p>
        </div>

        <!-- Manual QRIS Code -->
        @if($qrisTransaction->qris_code)
        <div class="p-3 bg-gray-50 rounded-lg mb-4">
            <p class="text-xs text-gray-600 mb-2">Kode QRIS Manual:</p>
            <div class="flex items-center justify-between gap-2">
                <code class="text-xs font-mono text-gray-700 break-all flex-1">{{ substr($qrisTransaction->qris_code, 0, 50) }}...</code>
                <button 
                    onclick="copyToClipboard('{{ $qrisTransaction->qris_code }}')"
                    class="px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600"
                >
                    Copy
                </button>
            </div>
        </div>
        @endif

        <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-700 font-semibold">⏳ Menunggu Pembayaran</p>
            <p class="text-sm text-green-600 mt-1">Pembayaran Anda akan dikonfirmasi secara otomatis setelah kami menerima notifikasi dari bank</p>
        </div>
        @else
        <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-blue-700 font-semibold">⏳ Menunggu Pembayaran</p>
            <p class="text-sm text-blue-600 mt-1">Silahkan ikuti instruksi pembayaran di bawah</p>
        </div>
        @endif
    </div>

    <!-- Payment Buttons -->
    <div class="bg-white rounded-lg shadow-md p-6">
        @if(!$qrisTransaction || ($qrisTransaction->status !== 'paid' && $qrisTransaction->isExpired()))
        <button 
            id="create-qris-btn" 
            class="w-full bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white font-bold py-3 rounded-lg transition duration-300 shadow-lg"
        >
            📱 Buat Kode QRIS Baru
        </button>
        @elseif($qrisTransaction && $qrisTransaction->status === 'paid')
        <div class="w-full bg-green-100 text-green-800 font-bold py-3 rounded-lg text-center">
            ✅ Pembayaran Berhasil - Terima Kasih!
        </div>
        @elseif($qrisTransaction && $qrisTransaction->status === 'pending')
        <div class="space-y-2">
            <button 
                id="retry-btn"
                class="w-full bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-700 hover:to-orange-600 text-white font-bold py-3 rounded-lg transition duration-300 shadow-lg"
                onclick="location.href='{{ route('xendit.qris.show', $order) }}'"
            >
                🔄 Refresh Status
            </button>
            <button 
                id="new-qris-btn"
                class="w-full bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white font-bold py-2 rounded-lg transition duration-300"
            >
                📱 Buat QRIS Baru
            </button>
        </div>
        @else
        <button 
            id="create-qris-btn" 
            class="w-full bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white font-bold py-3 rounded-lg transition duration-300 shadow-lg"
        >
            📱 Buat Kode QRIS Baru
        </button>
        @endif
        
        <a 
            href="{{ route('menu.index') }}" 
            class="block text-center mt-3 text-gray-600 hover:text-gray-800 transition"
        >
            ← Kembali ke Menu
        </a>
    </div>

    <!-- Payment Status Poll (Hidden) -->
    <div id="status-check" style="display: none;"></div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    const orderId = "{{ $order->id_order }}";
    const createQrisBtn = document.getElementById('create-qris-btn');
    const newQrisBtn = document.getElementById('new-qris-btn');
    const payButton = document.getElementById('pay-button');
    let statusCheckInterval = null;
    
    // Function to create QRIS and redirect to Xendit
    function createAndRedirectToXendit(button) {
        button.disabled = true;
        button.textContent = '⏳ Membuat kode QRIS...';

        fetch('{{ route("xendit.qris.create", $order) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.invoice_url) {
                button.textContent = '✅ Redirecting ke Xendit...';
                // Redirect to Xendit checkout page to show real QRIS QR code
                setTimeout(() => {
                    window.location.href = data.invoice_url;
                }, 500);
            } else {
                alert('Error: ' + data.message);
                button.disabled = false;
                button.textContent = button.id === 'new-qris-btn' ? '📱 Buat QRIS Baru' : '📱 Buat Kode QRIS Baru';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error creating QRIS code');
            button.disabled = false;
            button.textContent = button.id === 'new-qris-btn' ? '📱 Buat QRIS Baru' : '📱 Buat Kode QRIS Baru';
        });
    }

    // Generate QR Code if QRIS transaction exists
    @if($qrisTransaction && $qrisTransaction->qris_code)
    window.addEventListener('load', function() {
        const qrisCode = "{{ $qrisTransaction->qris_code }}";
        const qrContainer = document.getElementById('qris-container');
        
        // Clear previous QR code if any
        qrContainer.innerHTML = '<canvas id="qrcode" style="width: 280px; height: 280px;"></canvas>';
        
        if (qrisCode) {
            new QRCode(document.getElementById("qrcode"), {
                text: qrisCode,
                width: 280,
                height: 280,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        }
    });
    @endif

    // Copy to clipboard function
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('✅ Kode QRIS berhasil disalin!');
        }).catch(() => {
            alert('❌ Gagal menyalin kode');
        });
    }

    // Create QRIS QR Code & Redirect to Xendit
    if (createQrisBtn) {
        createQrisBtn.addEventListener('click', function() {
            createAndRedirectToXendit(this);
        });
    }

    // New QRIS Button - same behavior as create button
    if (newQrisBtn) {
        newQrisBtn.addEventListener('click', function() {
            if (confirm('Buat QRIS code baru?')) {
                createAndRedirectToXendit(this);
            }
        });
    }

    // Pay button - redirect to Xendit
    if (payButton) {
        payButton.addEventListener('click', function() {
            this.disabled = true;
            this.textContent = '⏳ Redirecting...';
            
            fetch('{{ route("xendit.qris.create", $order) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.invoice_url) {
                    window.location.href = data.invoice_url;
                } else {
                    alert('Error: ' + (data.message || 'Failed to create invoice'));
                    this.disabled = false;
                    this.textContent = '💳 Lanjutkan ke Pembayaran';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: ' + error.message);
                this.disabled = false;
                this.textContent = '💳 Lanjutkan ke Pembayaran';
            });
        });
    }

    // Poll payment status
    function checkPaymentStatus() {
        fetch('{{ route("xendit.qris.status", $order) }}', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.status === 'paid') {
                clearInterval(statusCheckInterval);
                window.location.href = '{{ route("order.receipt", $order) }}?success=1';
            }
        })
        .catch(error => console.error('Status check error:', error));
    }

    // Start polling if transaction exists and is pending
    @if($qrisTransaction && $qrisTransaction->status === 'pending')
    statusCheckInterval = setInterval(checkPaymentStatus, 3000); // Check every 3 seconds
    @endif
</script>
@endsection

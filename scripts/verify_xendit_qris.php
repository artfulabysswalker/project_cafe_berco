<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\QrisTransaction;

try {
    $order = Order::find(31);
    $qris = QrisTransaction::where('id_order', 31)->latest()->first();
    
    echo "════════════════════════════════════════════════════════\n";
    echo "📦 ORDER #31 - XENDIT QRIS PAYMENT\n";
    echo "════════════════════════════════════════════════════════\n\n";
    
    echo "Customer: " . $order->nama_pelanggan . "\n";
    echo "Amount: Rp " . number_format($order->total_harga, 0) . "\n";
    echo "Payment Method: " . $order->payment_method . "\n\n";
    
    echo "════════════════════════════════════════════════════════\n";
    echo "💳 XENDIT QRIS TRANSACTION\n";
    echo "════════════════════════════════════════════════════════\n\n";
    
    echo "Transaction ID: #" . $qris->id_qris_transaction . "\n";
    echo "Invoice ID: " . $qris->invoice_id . "\n";
    echo "Status: " . $qris->status . "\n";
    echo "Amount: Rp " . number_format($qris->amount, 0) . "\n";
    echo "Payment Channel: " . $qris->payment_channel . "\n";
    echo "Created: " . $qris->created_at . "\n";
    echo "Expires: " . $qris->expires_at . "\n\n";
    
    $metadata = $qris->metadata;
    if ($metadata) {
        echo "════════════════════════════════════════════════════════\n";
        echo "📋 XENDIT METADATA\n";
        echo "════════════════════════════════════════════════════════\n\n";
        
        echo "External ID: " . ($metadata['external_id'] ?? 'N/A') . "\n";
        echo "Invoice URL: " . ($metadata['invoice_url'] ?? 'N/A') . "\n";
        echo "QRIS String: " . ($metadata['qris_string'] ?? 'Not available (retrieve from Xendit page)') . "\n\n";
        
        if (isset($metadata['invoice_url'])) {
            echo "🌐 Open this URL to see the QRIS code:\n";
            echo $metadata['invoice_url'] . "\n\n";
        }
    }
    
    echo "════════════════════════════════════════════════════════\n";
    echo "✅ QRIS INVOICE CREATED SUCCESSFULLY FROM XENDIT!\n";
    echo "════════════════════════════════════════════════════════\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

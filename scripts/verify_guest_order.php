<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap/app.php';

use App\Models\Order;
use App\Models\QrisTransaction;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== GUEST ORDER #26 VERIFICATION ===\n\n";

// Check order
$order = Order::find(26);
if ($order) {
    echo "✅ Order Found:\n";
    echo "   ID: #26\n";
    echo "   Customer: {$order->nama_pelanggan}\n";
    echo "   Total: Rp " . number_format($order->total_harga, 0) . "\n";
    echo "   Payment Status: {$order->status_pembayaran}\n";
    echo "   Payment Method: {$order->payment_method}\n";
    echo "   Service Type: {$order->service_type}\n";
} else {
    echo "❌ Order #26 not found\n";
    exit(1);
}

echo "\n";

// Check QRIS transaction
$tx = QrisTransaction::where('id_order', 26)->first();
if ($tx) {
    echo "✅ QRIS Transaction Found:\n";
    echo "   Transaction ID: #{$tx->id_qris_transaction}\n";
    echo "   Invoice ID: {$tx->invoice_id}\n";
    echo "   Status: {$tx->status}\n";
    echo "   Amount: Rp " . number_format($tx->amount, 0) . "\n";
    echo "   Payment Channel: {$tx->payment_channel}\n";
    echo "   Created: {$tx->created_at}\n";
    echo "   Expires: {$tx->expires_at}\n";
    
    echo "\n📱 QRIS Code:\n";
    echo "   {$tx->qris_code}\n";
    
    // Check reconciliation
    $reconciliation = $tx->reconciliation;
    if ($reconciliation) {
        echo "\n✅ Reconciliation Record Found:\n";
        echo "   Reconciliation ID: #{$reconciliation->id_reconciliation}\n";
        echo "   Status: {$reconciliation->reconciliation_status}\n";
        echo "   System Amount: Rp " . number_format($reconciliation->system_amount, 0) . "\n";
        echo "   Bank Amount: " . ($reconciliation->bank_amount ? "Rp " . number_format($reconciliation->bank_amount, 0) : "NULL") . "\n";
    } else {
        echo "\n❌ No reconciliation record found\n";
    }
} else {
    echo "❌ QRIS Transaction not found for order #26\n";
}

echo "\n=== END VERIFICATION ===\n";

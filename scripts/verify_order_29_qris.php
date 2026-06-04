<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;
use App\Models\QrisTransaction;

echo "═══════════════════════════════════════\n";
echo "📦 ORDER #29\n";
echo "═══════════════════════════════════════\n";

$order = Order::find(29);
if ($order) {
    echo "Customer: " . $order->nama_pelanggan . "\n";
    echo "Total: Rp " . number_format($order->total_harga, 0) . "\n";
    echo "Status Pembayaran: " . $order->status_pembayaran . "\n";
    echo "Status Order: " . $order->status_order . "\n";
    echo "\n";
} else {
    echo "❌ Order #29 not found\n";
    exit(1);
}

$qris = QrisTransaction::where('id_order', 29)->first();
if ($qris) {
    echo "═══════════════════════════════════════\n";
    echo "💳 QRIS TRANSACTION\n";
    echo "═══════════════════════════════════════\n";
    echo "Transaction ID: #" . $qris->id_qris_transaction . "\n";
    echo "Invoice ID: " . $qris->invoice_id . "\n";
    echo "Status: " . $qris->status . "\n";
    echo "Amount: Rp " . number_format($qris->amount, 0) . "\n";
    echo "Payment Channel: " . $qris->payment_channel . "\n";
    echo "Created: " . $qris->created_at . "\n";
    echo "Expires: " . $qris->expires_at . "\n";
    echo "Paid At: " . ($qris->paid_at ? $qris->paid_at : "Not paid yet") . "\n";
    echo "\n";

    $rec = $qris->reconciliation;
    if ($rec) {
        echo "═══════════════════════════════════════\n";
        echo "🔄 RECONCILIATION\n";
        echo "═══════════════════════════════════════\n";
        echo "Status: " . $rec->reconciliation_status . "\n";
        echo "System Amount: Rp " . number_format($rec->system_amount, 0) . "\n";
        echo "Bank Amount: " . ($rec->bank_amount ? "Rp " . number_format($rec->bank_amount, 0) : "Not reconciled yet") . "\n";
        echo "Amount Difference: Rp " . number_format($rec->amount_difference ?? 0, 0) . "\n";
        echo "Match: " . ($rec->amountsMatch() ? "✅ YES" : "❌ NO") . "\n";
        echo "\n";
    }
} else {
    echo "❌ QRIS Transaction not found for order #29\n";
    exit(1);
}

echo "═══════════════════════════════════════\n";
echo "✅ PAYMENT SUCCESSFUL!\n";
echo "═══════════════════════════════════════\n";

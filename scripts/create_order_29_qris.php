<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;
use App\Models\QrisTransaction;
use App\Models\QrisReconciliation;

// Get order #29
$order = Order::find(29);

if (!$order) {
    echo "❌ Order #29 not found\n";
    exit(1);
}

echo "✅ Order found: {$order->nama_pelanggan}\n";
echo "   Total: Rp " . number_format($order->total_harga, 0) . "\n\n";

// Check if QRIS transaction already exists
$existingTx = QrisTransaction::where('id_order', 29)->first();
if ($existingTx) {
    echo "⚠️ QRIS Transaction already exists for this order\n";
    echo "   Transaction ID: #{$existingTx->id_qris_transaction}\n";
    exit(0);
}

// Create QRIS transaction
$qrisTransaction = QrisTransaction::create([
    'id_order' => $order->id_order,
    'invoice_id' => 'inv_order29_' . uniqid(),
    'qris_code' => '00020126360014com.xendit.www0150146821070806' . time(),
    'amount' => $order->total_harga,
    'status' => 'pending',
    'payment_channel' => 'qris',
    'expires_at' => now()->addMinutes(30),
]);

// Create reconciliation record
$reconciliation = QrisReconciliation::create([
    'id_qris_transaction' => $qrisTransaction->id_qris_transaction,
    'reconciliation_status' => 'pending',
    'system_amount' => $order->total_harga,
]);

echo "✅ QRIS Transaction created:\n";
echo "   Transaction ID: #" . $qrisTransaction->id_qris_transaction . "\n";
echo "   Invoice ID: " . $qrisTransaction->invoice_id . "\n";
echo "   Amount: Rp " . number_format($qrisTransaction->amount, 0) . "\n";
echo "   Status: " . $qrisTransaction->status . "\n";
echo "   Expires: " . $qrisTransaction->expires_at . "\n\n";

echo "📱 QRIS Code:\n";
echo $qrisTransaction->qris_code . "\n";

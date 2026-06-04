<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;
use App\Models\QrisTransaction;
use App\Models\QrisReconciliation;

// Get order
$order = Order::find(24);

if (!$order) {
    echo "❌ Order #24 not found\n";
    exit(1);
}

echo "✅ Order found: {$order->nama_pelanggan}\n";

// Create QRIS transaction
$qrisTransaction = QrisTransaction::create([
    'id_order' => $order->id_order,
    'invoice_id' => 'inv_' . uniqid(),
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

echo "✅ QRIS Transaction created: " . $qrisTransaction->id_qris_transaction . "\n";
echo "✅ Invoice ID: " . $qrisTransaction->invoice_id . "\n";
echo "✅ Amount: Rp " . number_format($qrisTransaction->amount, 0) . "\n";
echo "✅ Expires at: " . $qrisTransaction->expires_at . "\n";

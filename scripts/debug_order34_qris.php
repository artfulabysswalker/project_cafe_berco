<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\QrisTransaction;

echo "═══════════════════════════════════════════════════════\n";
echo "📦 ORDER #34 - FULL QRIS DATA\n";
echo "═══════════════════════════════════════════════════════\n\n";

$order = Order::find(34);
$qris = QrisTransaction::where('id_order', 34)->latest()->first();

if (!$order || !$qris) {
    echo "❌ Order or QRIS not found\n";
    exit(1);
}

echo "Order: {$order->nama_pelanggan}\n";
echo "Amount: Rp " . number_format($order->total_harga, 0) . "\n";
echo "Status: {$order->status_pembayaran}\n\n";

echo "═══════════════════════════════════════════════════════\n";
echo "💳 QRIS TRANSACTION DATA\n";
echo "═══════════════════════════════════════════════════════\n\n";

echo "ID: #" . $qris->id_qris_transaction . "\n";
echo "Invoice ID: " . $qris->invoice_id . "\n";
echo "Status: " . $qris->status . "\n";
echo "Amount: Rp " . number_format($qris->amount, 0) . "\n";
echo "Channel: " . $qris->payment_channel . "\n";
echo "Created: " . $qris->created_at . "\n";
echo "Expires: " . $qris->expires_at . "\n";

echo "\n📋 QRIS Code:\n";
echo "QRIS Code Value: " . ($qris->qris_code ? $qris->qris_code : "❌ NULL/EMPTY") . "\n";

echo "\n📋 Metadata:\n";
if ($qris->metadata) {
    echo json_encode($qris->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
} else {
    echo "❌ No metadata\n";
}

echo "\n═══════════════════════════════════════════════════════\n";
echo "✅ Order #34 QRIS Details Complete\n";
echo "═══════════════════════════════════════════════════════\n";

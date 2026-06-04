<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Services\QrisPaymentService;

try {
    $order = Order::find(31);
    if (!$order) {
        echo "❌ Order #31 not found\n";
        exit(1);
    }

    echo "✅ Order found: " . $order->nama_pelanggan . "\n";
    echo "   Amount: Rp " . number_format($order->total_harga, 0) . "\n\n";

    $service = new QrisPaymentService();
    $result = $service->createQrisInvoice($order);
    
    echo "✅ QRIS Invoice Created Successfully!\n\n";
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
    echo "Trace:\n";
    echo $e->getTraceAsString();
    exit(1);
}

<?php

namespace App\Events;

use App\Models\Order;
use App\Models\PaymentLog;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;

    public PaymentLog $paymentLog;

    public function __construct(Order $order, PaymentLog $paymentLog)
    {
        $this->order = $order;
        $this->paymentLog = $paymentLog;
    }

    /**
     * Channel yang dipantau dashboard kasir.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('cashier.dashboard'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'PaymentReceived';
    }

    public function broadcastWith(): array
    {
        return [
            'id_order' => $this->order->id_order,
            'order_code' => $this->order->public_code,
            'gross_amount' => (int) $this->paymentLog->gross_amount,
            'total_formatted' => 'Rp '.number_format($this->paymentLog->gross_amount, 0, ',', '.'),
            'transaction_id' => $this->paymentLog->transaction_id,
            'payment_type' => $this->paymentLog->payment_type,
            'paid_at' => $this->paymentLog->paid_at?->toIso8601String(),
            'customer_name' => $this->order->customer_name ?? $this->order->nama_pelanggan,
        ];
    }
}

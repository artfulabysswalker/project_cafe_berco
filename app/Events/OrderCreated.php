<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order->loadMissing(['items.menu', 'user']);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('cashier.orders'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'OrderCreated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        $itemsSummary = $this->order->items->map(function ($item) {
            $temp = $item->temperature ? ' ('.ucfirst($item->temperature).')' : '';

            return [
                'name' => ($item->menu->nama_menu ?? 'Menu').$temp,
                'quantity' => $item->quantity,
                'subtotal' => (float) $item->subtotal,
                'note' => $item->note ?? null,
            ];
        })->toArray();

        // Ekstraksi nomor meja dari notes atau service_type
        $tableNumber = 'Meja -';
        if (preg_match('/meja\s*([0-9a-zA-Z_-]+)/i', $this->order->notes ?? '', $matches)) {
            $tableNumber = 'Meja '.strtoupper($matches[1]);
        } elseif ($this->order->service_type === 'dine_in') {
            $tableNumber = 'Dine-In (Meja)';
        } else {
            $tableNumber = 'Take Away';
        }

        return [
            'id_order' => $this->order->id_order,
            'order_code' => '#ORD-'.$this->order->id_order,
            'table_number' => $tableNumber,
            'customer_name' => $this->order->nama_pelanggan ?? ($this->order->user->name ?? 'Pelanggan'),
            'service_type' => $this->order->service_type ?? 'dine_in',
            'payment_method' => strtoupper($this->order->payment_method ?? 'CASH'),
            'payment_status' => strtolower($this->order->status_pembayaran ?? 'pending'),
            'status_order' => strtolower($this->order->status_order ?? 'pending'),
            'total_amount' => (float) $this->order->total_harga,
            'total_formatted' => 'Rp '.number_format($this->order->total_harga ?? 0, 0, ',', '.'),
            'notes' => $this->order->notes,
            'items' => $itemsSummary,
            'items_count' => $this->order->items->sum('quantity'),
            'created_at_human' => $this->order->tanggal
                ? $this->order->tanggal->format('H:i')
                : ($this->order->created_at ? $this->order->created_at->format('H:i') : now()->format('H:i')),
            'receipt_url' => route('admin.receipt.view', $this->order->id_order),
            'complete_url' => route('admin.orders.complete', $this->order->id_order),
        ];
    }
}

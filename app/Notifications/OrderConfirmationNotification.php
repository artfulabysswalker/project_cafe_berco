<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderConfirmationNotification extends Notification
{
    use Queueable;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $items = $this->order->orderItems()->with('menu')->get();
        $subtotal = $items->sum(function ($item) {
            return $item->menu->harga * $item->quantity;
        });
        $tax = $subtotal * 0.1;

        $mailMessage = (new MailMessage)
            ->subject('🎉 Pesanan Diterima - Order #' . $this->order->id_order . ' - Berco Cafe')
            ->greeting('Halo ' . $this->order->nama_pelanggan . '!')
            ->line('Pesanan Anda telah berhasil kami terima!')
            ->line('Terima kasih telah memesan di Berco Cafe.')
            ->line('')
            ->line('📋 **Detail Pesanan:**')
            ->line('Order ID: #' . $this->order->id_order)
            ->line('Tanggal: ' . $this->order->tanggal->format('d M Y H:i'))
            ->line('Tipe Layanan: ' . ($this->order->service_type === 'dine_in' ? '🍽️ Dine In' : '🛍️ Take Away'))
            ->line('Metode Pembayaran: ' . ($this->order->payment_method === 'cash' ? '💵 Tunai' : '💳 Kartu'))
            ->line('')
            ->line('📦 **Item yang Dipesan:**');

        // Add items
        foreach ($items as $item) {
            $mailMessage->line('  • ' . $item->menu->nama_menu . ' x' . $item->quantity . ' = Rp ' . number_format($item->subtotal, 0, ',', '.'));
        }

        $mailMessage
            ->line('')
            ->line('💰 **Rincian Harga:**')
            ->line('Subtotal: Rp ' . number_format($subtotal, 0, ',', '.'))
            ->line('PPN (10%): Rp ' . number_format($tax, 0, ',', '.'))
            ->line('')
            ->line('🔴 **TOTAL: Rp ' . number_format($this->order->total_harga, 0, ',', '.') . '**')
            ->line('')
            ->line('⏳ **Status:** Pesanan sedang diproses')
            ->line('')
            ->action('Lihat Detail Pesanan', url('/order/' . $this->order->id_order . '/receipt'))
            ->line('')
            ->line('Pesanan Anda akan segera siap!')
            ->salutation('Salam Hangat,')
            ->line('☕ Tim Berco Cafe');

        return $mailMessage;
    }
}

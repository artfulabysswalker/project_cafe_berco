<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmationNotification extends Notification
{
    use Queueable;

    public $order;

    public $payment;

    public function __construct(Order $order, Payment $payment)
    {
        $this->order = $order;
        $this->payment = $payment;
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
        $serviceCharge = $this->order->service_charge ?? 0;

        $mailMessage = (new MailMessage)
            ->subject('✅ Pembayaran Berhasil - Order #'.$this->order->id_order.' - Berco Cafe')
            ->greeting('Halo '.$this->order->nama_pelanggan.'!')
            ->line('Terima kasih telah memesan di Berco Cafe!')
            ->line('Pembayaran Anda telah berhasil diproses.')
            ->line('')
            ->line('📋 **Detail Pesanan:**')
            ->line('Order ID: #'.$this->order->id_order)
            ->line('Tanggal: '.$this->order->tanggal->format('d M Y H:i'))
            ->line('Tipe Layanan: '.($this->order->service_type === 'dine_in' ? 'Dine In' : 'Take Away'))
            ->line('')
            ->line('📦 **Item yang Dipesan:**');

        // Add items
        foreach ($items as $item) {
            $mailMessage->line('  • '.$item->menu->nama_menu.' x'.$item->quantity.' = Rp '.number_format($item->subtotal, 0, ',', '.'));
        }

        $mailMessage
            ->line('')
            ->line('💰 **Rincian Harga:**')
            ->line('Subtotal: Rp '.number_format($subtotal, 0, ',', '.'))
            ->when($serviceCharge > 0, fn ($message) => $message->line('Biaya Take-away: Rp '.number_format($serviceCharge, 0, ',', '.')))
            ->line('')
            ->line('🔴 **TOTAL: Rp '.number_format($this->order->total_harga, 0, ',', '.').'**')
            ->line('')
            ->line('💳 **Detail Pembayaran:**')
            ->line('Metode: '.$this->getPaymentMethodLabel())
            ->line('Transaction ID: '.($this->payment->transaction_id ?? 'Pending'))
            ->line('Status: ✅ Berhasil Dibayar')
            ->line('')
            ->action('Lihat Pesanan', url('/order/'.$this->order->id_order.'/receipt'))
            ->line('')
            ->line('Pesanan Anda akan segera diproses.')
            ->line('Terima kasih telah berbisnis dengan kami!')
            ->salutation('Salam Hangat,')
            ->line('☕ Tim Berco Cafe');

        return $mailMessage;
    }

    private function getPaymentMethodLabel(): string
    {
        return match ($this->order->payment_method) {
            'cash' => 'Tunai',
            'qris' => 'QRIS',
            'debit' => 'Debit',
            'credit' => 'Kredit',
            default => ucfirst($this->order->payment_method ?? ''),
        };
    }
}

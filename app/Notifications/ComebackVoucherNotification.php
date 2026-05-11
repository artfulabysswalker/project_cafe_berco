<?php

namespace App\Notifications;

use App\Models\Voucher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ComebackVoucherNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Voucher $voucher;

    /**
     * Create a new notification instance.
     */
    public function __construct(Voucher $voucher)
    {
        $this->voucher = $voucher;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 Promo Eksklusif Menanti Anda! - ' . $this->voucher->name)
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Kami merindukan Anda! Sebagai pelanggan setia, kami memberikan voucher promo eksklusif.')
            ->line('')
            ->line('📋 Detail Voucher:')
            ->line('• ' . $this->voucher->name)
            ->line('• ' . $this->voucher->description)
            ->line('')
            ->line($this->getDiscountText())
            ->line('')
            ->line('🔑 Kode Voucher: <strong>' . $this->voucher->code . '</strong>')
            ->line('')
            ->line('⏰ Berlaku hingga: ' . $this->voucher->valid_until->format('d M Y'))
            ->action('Kunjungi Toko Kami', url('/menu'))
            ->line('')
            ->line('Jangan lewatkan kesempatan ini untuk menikmati kopi favorit Anda dengan harga spesial!')
            ->salutation('Terima kasih,');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'voucher_id' => $this->voucher->id,
            'voucher_code' => $this->voucher->code,
            'voucher_name' => $this->voucher->name,
            'message' => 'Anda mendapatkan voucher promo: ' . $this->voucher->name,
            'type' => 'comeback_voucher',
        ];
    }

    /**
     * Get the discount text based on voucher type
     */
    private function getDiscountText(): string
    {
        if ($this->voucher->discount_percentage) {
            if ($this->voucher->max_discount) {
                return '💰 Dapatkan diskon ' . $this->voucher->discount_percentage . '% hingga Rp ' . number_format($this->voucher->max_discount, 0, ',', '.');
            }
            return '💰 Dapatkan diskon ' . $this->voucher->discount_percentage . '%';
        } elseif ($this->voucher->discount_amount) {
            return '💰 Dapatkan diskon Rp ' . number_format($this->voucher->discount_amount, 0, ',', '.');
        }
        return '';
    }
}

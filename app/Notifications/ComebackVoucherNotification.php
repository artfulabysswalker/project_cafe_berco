<?php

namespace App\Notifications;

use App\Models\Voucher;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ComebackVoucherNotification extends Notification
{
    use Queueable;

    public $voucher;

    public function __construct(Voucher $voucher)
    {
        $this->voucher = $voucher;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Welcome Back Voucher!')
            ->line('We miss you! Here is a special voucher for you.')
            ->line('Code: ' . $this->voucher->code)
            ->line('Discount: ' . ($this->voucher->discount_percentage ?? $this->voucher->discount_amount))
            ->action('Use Now', url('/vouchers'))
            ->line('Thank you for staying with us!');
    }
}
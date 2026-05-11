<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InactiveUserReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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
            ->subject('⏰ Kami Merindukan Anda! Kembali ke Cafe Berco')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Sudah lama kami tidak melihat Anda berkunjung ke Cafe Berco.')
            ->line('')
            ->line('Kami telah menyiapkan penawaran spesial untuk menyambut kembali Anda!')
            ->line('')
            ->line('✨ Keuntungan menunggu Anda:')
            ->line('• Promo eksklusif dan diskon menarik')
            ->line('• Menu terbaru yang siap memanjakan lidah Anda')
            ->line('• Kesempatan mendapatkan poin reward')
            ->line('')
            ->action('Lihat Menu & Penawaran Spesial', url('/menu'))
            ->line('')
            ->line('Jangan tunda lagi, kembali ke Cafe Berco sekarang dan rasakan suasana hangat kami lagi!')
            ->salutation('Terima kasih atas kesetiaan Anda,');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Kami merindukan Anda! Kembali ke Cafe Berco dan nikmati penawaran spesial.',
            'type' => 'inactive_user_reminder',
        ];
    }
}

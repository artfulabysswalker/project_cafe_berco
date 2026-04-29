<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;

class NotificationService
{
    /**
     * Send comeback reminder notifications to inactive users
     * This method should be called from a scheduled job
     */
    public function sendComebackReminders(): int
    {
        // Get inactive users who haven't received a reminder recently
        $inactiveUsers = User::whereNotNull('last_visit_at')
            ->where('notification_enabled', true)
            ->where('is_active', true)
            ->where('last_visit_at', '<=', now()->subDays(30))
            ->get();

        $totalSent = 0;

        foreach ($inactiveUsers as $user) {
            // Check if user already received a reminder in the last 7 days
            $recentReminder = $user->notifications()
                ->where('type', 'comeback_reminder')
                ->where('created_at', '>=', now()->subDays(7))
                ->exists();

            if (!$recentReminder) {
                $this->createComebackReminder($user);
                $totalSent++;
            }
        }

        return $totalSent;
    }

    /**
     * Create a comeback reminder notification for a user
     */
    public function createComebackReminder(User $user): Notification
    {
        $daysSinceVisit = $user->daysSinceLastVisit();

        $message = "Kami merindukanmu! Sudah {$daysSinceVisit} hari sejak kunjungan terakhirmu. "
            . "Dapatkan pengalaman terbaik di kafe kami dan nikmati diskon spesial untuk member setia.";

        return Notification::create([
            'user_id' => $user->id,
            'title' => 'Kami Tunggu Kunjunganmu!',
            'message' => $message,
            'type' => 'comeback_reminder',
            'channel' => 'both',
            'related_url' => route('menu'),
        ]);
    }

    /**
     * Create a notification
     */
    public function create(
        User $user,
        string $title,
        string $message,
        string $type = 'promotional',
        ?string $relatedUrl = null,
        string $channel = 'in_app'
    ): Notification {
        return Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'related_url' => $relatedUrl,
            'channel' => $channel,
        ]);
    }

    /**
     * Get unread notifications count for a user
     */
    public function getUnreadCount(User $user): int
    {
        return $user->notifications()->unread()->count();
    }

    /**
     * Mark all notifications as sent
     */
    public function markAllAsSent(): int
    {
        return Notification::where('is_sent', false)
            ->update(['is_sent' => true]);
    }
}

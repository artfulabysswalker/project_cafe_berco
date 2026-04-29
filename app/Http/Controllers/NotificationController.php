<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\View\View;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display notifications for the current user
     */
    public function index(): View
    {
        $notifications = auth()->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Notification $notification)
    {
        $this->authorize('view', $notification);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        auth()->user()->notifications()
            ->whereUnread()
            ->update(['is_read' => true, 'read_at' => now()]);

        return back()->with('success', 'Semua notifikasi ditandai sebagai dibaca');
    }

    /**
     * Delete a notification
     */
    public function destroy(Notification $notification)
    {
        $this->authorize('delete', $notification);
        $notification->delete();

        return back()->with('success', 'Notifikasi dihapus');
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount()
    {
        $count = auth()->user()->notifications()->unread()->count();
        return response()->json(['count' => $count]);
    }
}

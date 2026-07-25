<?php

namespace App\Http\Controllers;

class NotificationController extends Controller
{
    /**
     * Mark all notifications as read for the authenticated user.
     */
    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }
}

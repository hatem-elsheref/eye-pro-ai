<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display user notifications
     */
    public function index()
    {
        // Get notifications for the authenticated user
        // This uses Laravel's built-in notification system
        $notifications = auth()->user()->notifications;

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($id);
        
        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }
}





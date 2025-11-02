<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $data = $this->notificationService->getNotificationsData(auth()->user());
        return view('admin.notifications.index', $data);
    }

    public function markAsRead($id)
    {
        $this->notificationService->markAsRead(auth()->user(), $id);
        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead(auth()->user());
        return back()->with('success', 'All notifications marked as read.');
    }

    public function getCount()
    {
        $user = auth()->user();
        return response()->json([
            'unreadCount' => $user->unreadNotifications->count()
        ]);
    }

    public function getList()
    {
        $user = auth()->user();
        $notifications = $user->unreadNotifications->take(5);
        
        $notificationsData = $notifications->map(function ($notification) {
            $notifType = $notification->data['type'] ?? $notification->type;
            $title = $notification->data['title'] ?? 'Notification';
            $message = $notification->data['message'] ?? '';
            
            // Determine icon and color based on type
            $icon = 'fa-bell';
            $iconBg = 'bg-gray-100';
            $iconColor = 'text-gray-600';
            
            if ($notifType === 'account_approved') {
                $icon = 'fa-user-check';
                $iconBg = 'bg-blue-100';
                $iconColor = 'text-blue-600';
            } elseif ($notifType === 'account_rejected') {
                $icon = 'fa-user-times';
                $iconBg = 'bg-red-100';
                $iconColor = 'text-red-600';
            } elseif ($notifType === 'match_analysis_complete') {
                $icon = 'fa-check-circle';
                $iconBg = 'bg-green-100';
                $iconColor = 'text-green-600';
            } elseif ($notifType === 'match_upload_processing') {
                $icon = 'fa-upload';
                $iconBg = 'bg-blue-100';
                $iconColor = 'text-blue-600';
            } elseif ($notifType === 'match_processing_failed') {
                $icon = 'fa-exclamation-circle';
                $iconBg = 'bg-red-100';
                $iconColor = 'text-red-600';
            } elseif ($notifType === 'match_processing_ended_no_predictions') {
                $icon = 'fa-exclamation-triangle';
                $iconBg = 'bg-yellow-100';
                $iconColor = 'text-yellow-600';
            } elseif ($notifType === 'match_processing_stopped') {
                $icon = 'fa-stop-circle';
                $iconBg = 'bg-blue-100';
                $iconColor = 'text-blue-600';
            } elseif ($notifType === 'match_processing_stopped_failed') {
                $icon = 'fa-exclamation-circle';
                $iconBg = 'bg-red-100';
                $iconColor = 'text-red-600';
            } elseif ($notifType === 'match_processing_started') {
                $icon = 'fa-play-circle';
                $iconBg = 'bg-blue-100';
                $iconColor = 'text-blue-600';
            }
            
            return [
                'id' => $notification->id,
                'type' => $notifType,
                'title' => $title,
                'message' => $message,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at->diffForHumans(),
                'icon' => $icon,
                'iconBg' => $iconBg,
                'iconColor' => $iconColor,
            ];
        });
        
        return response()->json([
            'notifications' => $notificationsData,
            'unreadCount' => $user->unreadNotifications->count()
        ]);
    }
}

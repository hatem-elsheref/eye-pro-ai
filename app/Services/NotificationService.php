<?php

namespace App\Services;

use App\Models\MatchVideo;
use App\Models\User;
use App\Notifications\MatchUploadProcessing;
use App\Notifications\MatchAnalysisComplete;
use App\Notifications\MatchProcessingFailed;
use App\Notifications\AccountApproved;
use App\Notifications\AccountRejected;
use App\Helpers\WebSocketHelper;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Get notifications data for user
     */
    public function getNotificationsData(User $user): array
    {
        return [
            'notifications' => $user->notifications,
            'unreadCount' => $user->unreadNotifications->count(),
        ];
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(User $user, string $notificationId): void
    {
        $notification = $user->notifications()->findOrFail($notificationId);
        $notification->markAsRead();
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(User $user): void
    {
        $user->unreadNotifications->markAsRead();
    }

    /**
     * Notify user that upload is complete and processing started
     */
    public function notifyUploadProcessing(MatchVideo $match): void
    {
        try {
            $notification = new MatchUploadProcessing($match);
            $match->user->notify($notification);
            
            // Send via WebSocket for real-time updates
            $data = $notification->toArray($match->user);
            WebSocketHelper::sendNotification($match->user->id, $data);
        } catch (\Exception $e) {
            Log::error('Failed to send upload notification', [
                'matchId' => $match->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notify user that analysis is complete
     */
    public function notifyAnalysisComplete(MatchVideo $match, $analysis = null): void
    {
        try {
            $notification = new MatchAnalysisComplete($match, $analysis);
            $match->user->notify($notification);
            
            // Send via WebSocket for real-time updates
            $data = $notification->toArray($match->user);
            WebSocketHelper::sendNotification($match->user->id, $data);
        } catch (\Exception $e) {
            Log::error('Failed to send analysis notification', [
                'matchId' => $match->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notify user that processing failed
     */
    public function notifyProcessingFailed(MatchVideo $match): void
    {
        try {
            $notification = new MatchProcessingFailed($match);
            $match->user->notify($notification);
            
            // Send via WebSocket for real-time updates
            $data = $notification->toArray($match->user);
            WebSocketHelper::sendNotification($match->user->id, $data);
        } catch (\Exception $e) {
            Log::error('Failed to send failure notification', [
                'matchId' => $match->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notify user that account is approved
     */
    public function notifyAccountApproved(User $user): void
    {
        try {
            $notification = new AccountApproved();
            $user->notify($notification);
            
            // Send via WebSocket for real-time updates
            $data = $notification->toArray($user);
            WebSocketHelper::sendNotification($user->id, $data);
        } catch (\Exception $e) {
            Log::error('Failed to send account approved notification', [
                'userId' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notify user that account is rejected
     */
    public function notifyAccountRejected(User $user): void
    {
        try {
            $notification = new AccountRejected();
            $user->notify($notification);
            
            // Send via WebSocket for real-time updates
            $data = $notification->toArray($user);
            WebSocketHelper::sendNotification($user->id, $data);
        } catch (\Exception $e) {
            Log::error('Failed to send account rejected notification', [
                'userId' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

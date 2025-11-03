<?php

namespace App\Notifications;

use App\Models\MatchVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchUploadStarted extends Notification
{
    use Queueable;

    protected $match;

    /**
     * Create a new notification instance.
     */
    public function __construct(MatchVideo $match)
    {
        $this->match = $match;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title_key' => 'admin.notification_upload_started_title',
            'message_key' => 'admin.notification_upload_started_message',
            'title' => __('admin.notification_upload_started_title'),
            'message' => __('admin.notification_upload_started_message', ['match_name' => $this->match->name]),
            'match_id' => $this->match->id,
            'match_name' => $this->match->name,
            'type' => 'match_upload_started',
        ];
    }
}

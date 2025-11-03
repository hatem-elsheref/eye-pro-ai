<?php

namespace App\Notifications;

use App\Models\MatchVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchProcessingFailed extends Notification
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
            'title_key' => 'admin.notification_processing_failed_title',
            'message_key' => 'admin.notification_processing_failed_message',
            'title' => 'Processing Failed', // Keep for backward compatibility
            'message' => "Processing failed for your match '{$this->match->name}'. Please try uploading again or contact support.", // Keep for backward compatibility
            'match_id' => $this->match->id,
            'match_name' => $this->match->name,
            'type' => 'match_processing_failed',
        ];
    }
}

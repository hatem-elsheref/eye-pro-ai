<?php

namespace App\Notifications;

use App\Models\MatchVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchUploadProcessing extends Notification
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
            'title' => 'File Upload Successful',
            'message' => "Your match '{$this->match->name}' has been uploaded successfully. You can start processing now.",
            'match_id' => $this->match->id,
            'match_name' => $this->match->name,
            'type' => 'match_upload_processing',
        ];
    }
}

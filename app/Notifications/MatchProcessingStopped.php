<?php

namespace App\Notifications;

use App\Models\MatchVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchProcessingStopped extends Notification
{
    use Queueable;

    protected $match;
    protected $success;

    /**
     * Create a new notification instance.
     */
    public function __construct(MatchVideo $match, bool $success = true)
    {
        $this->match = $match;
        $this->success = $success;
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
        if ($this->success) {
            return [
                'title' => 'Processing Stopped',
                'message' => "AI model processing for match '{$this->match->name}' has been stopped successfully.",
                'match_id' => $this->match->id,
                'match_name' => $this->match->name,
                'type' => 'match_processing_stopped',
            ];
        } else {
            return [
                'title' => 'Failed to Stop Processing',
                'message' => "Failed to stop AI model processing for match '{$this->match->name}'. Something went wrong.",
                'match_id' => $this->match->id,
                'match_name' => $this->match->name,
                'type' => 'match_processing_stopped_failed',
                'success' => false,
            ];
        }
    }
}


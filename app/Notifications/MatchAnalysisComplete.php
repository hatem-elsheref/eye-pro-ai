<?php

namespace App\Notifications;

use App\Models\MatchVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchAnalysisComplete extends Notification
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
            'title_key' => 'admin.notification_analysis_complete_title',
            'message_key' => 'admin.notification_analysis_complete_message',
            'title' => 'AI Analysis Complete', // Keep for backward compatibility
            'message' => "AI model has finished processing your match '{$this->match->name}'. Results are now available.", // Keep for backward compatibility
            'match_id' => $this->match->id,
            'match_name' => $this->match->name,
            'type' => 'match_analysis_complete',
            'has_results' => $this->match->predictions()->count() > 0,
        ];
    }
}

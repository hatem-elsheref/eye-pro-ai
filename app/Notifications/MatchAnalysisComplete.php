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
    protected $analysis;

    /**
     * Create a new notification instance.
     */
    public function __construct(MatchVideo $match, $analysis = null)
    {
        $this->match = $match;
        $this->analysis = $analysis;
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
            'title' => 'AI Analysis Complete',
            'message' => "AI model has finished processing your match '{$this->match->name}'. Results are now available.",
            'match_id' => $this->match->id,
            'match_name' => $this->match->name,
            'type' => 'match_analysis_complete',
            'has_results' => !empty($this->analysis),
        ];
    }
}

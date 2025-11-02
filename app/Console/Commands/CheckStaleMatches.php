<?php

namespace App\Console\Commands;

use App\Models\MatchVideo;
use App\Services\AIModelService;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckStaleMatches extends Command
{
    protected $signature = 'matches:check-stale';
    protected $description = 'Check for matches older than 24 hours that are still processing - queries AI model /status endpoint and updates accordingly';

    protected $aiModelService;
    protected $notificationService;

    public function __construct(AIModelService $aiModelService, NotificationService $notificationService)
    {
        parent::__construct();
        $this->aiModelService = $aiModelService;
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for stale matches...');

        // Get matches created more than 48 hours ago that are still processing
        $staleMatches = MatchVideo::where('status', 'processing')
            ->where('created_at', '<', now()->subHours(48))
            ->get();

        if ($staleMatches->isEmpty()) {
            $this->info('No stale matches found.');
            return 0;
        }

        $this->info("Found {$staleMatches->count()} stale match(es) - checking AI model status...");

        $failedCount = 0;
        $skippedCount = 0;

        foreach ($staleMatches as $match) {
            try {
                // Check AI model status
                $statusResult = $this->aiModelService->getStatus($match->id);

                if (!$statusResult['success']) {
                    // If we can't get status, assume failed after 24 hours
                    $this->warn("Could not get status for match #{$match->id}, marking as failed");
                    $this->markAsFailed($match);
                    $failedCount++;
                    continue;
                }

                $aiStatus = $statusResult['status'];

                if ($aiStatus === 'failed') {
                    // AI model confirms it failed
                    $this->line("AI model reports match #{$match->id} ({$match->name}) as failed");
                    $this->markAsFailed($match, 'AI model reported processing failure');
                    $failedCount++;
                } elseif ($aiStatus === 'completed' || $aiStatus === 'finished') {
                    // AI model says it's done, but we don't have analysis yet
                    // Wait a bit more or check if we can get results
                    $this->line("AI model reports match #{$match->id} ({$match->name}) as completed - but no analysis in database");
                    $this->line("  Status retrieved successfully - if analysis not received, it may be queued");
                    $skippedCount++;
                } elseif ($aiStatus === 'processing' || $aiStatus === 'in_progress') {
                    // Still processing after 24 hours - mark as failed
                    $this->line("Match #{$match->id} ({$match->name}) still processing after 24 hours - marking as failed");
                    $this->markAsFailed($match, 'Processing timeout - still processing after 24 hours');
                    $failedCount++;
                } else {
                    // Unknown status or null - assume failed
                    $this->warn("Unknown status '{$aiStatus}' for match #{$match->id} - marking as failed");
                    $this->markAsFailed($match, "Unknown status from AI model: {$aiStatus}");
                    $failedCount++;
                }
            } catch (\Exception $e) {
                $this->error("Failed to process match #{$match->id}: {$e->getMessage()}");
                Log::error('Error processing stale match', [
                    'match_id' => $match->id,
                    'error' => $e->getMessage()
                ]);

                // On exception, mark as failed
                try {
                    $this->markAsFailed($match, 'Error checking status: ' . $e->getMessage());
                    $failedCount++;
                } catch (\Exception $e2) {
                    $this->error("Could not mark match #{$match->id} as failed: {$e2->getMessage()}");
                }
            }
        }

        $this->info("\nResults:");
        $this->info("  - Failed: {$failedCount}");
        $this->info("  - Completed (skipped): {$skippedCount}");
        $this->info("  - Total processed: " . ($failedCount + $skippedCount));

        return 0;
    }

    /**
     * Mark match as failed
     */
    protected function markAsFailed($match, $reason = null)
    {
        $reason = $reason ?? 'Processing timeout - no response from AI model after 24 hours';

        $match->update([
            'status' => 'failed',
            'analysis' => json_encode([
                'error' => 'Processing failed',
                'message' => $reason,
                'failed_at' => now()->toIso8601String(),
                'checked_at' => now()->toIso8601String()
            ])
        ]);

        // Send notification to user
        $this->notificationService->notifyProcessingFailed($match);

        Log::warning('Stale match marked as failed', [
            'match_id' => $match->id,
            'match_name' => $match->name,
            'created_at' => $match->created_at,
            'reason' => $reason
        ]);
    }
}

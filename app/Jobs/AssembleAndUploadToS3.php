<?php

namespace App\Jobs;

use App\Models\MatchVideo;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class AssembleAndUploadToS3 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $matchId;
    protected $chunkDir;
    protected $finalPath;
    protected $totalChunks;
    protected $chunkStorageDisk;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     * Set to 0 to disable timeout (unlimited) - ensures upload completes regardless of file size
     */
    public $timeout = 0; // Unlimited timeout - job will run until completion

    /**
     * Create a new job instance.
     */
    public function __construct(int $matchId, string $chunkDir, string $finalPath, int $totalChunks, string $chunkStorageDisk)
    {
        $this->matchId = $matchId;
        $this->chunkDir = $chunkDir;
        $this->finalPath = $finalPath;
        $this->totalChunks = $totalChunks;
        $this->chunkStorageDisk = $chunkStorageDisk;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Disable PHP execution time limit to ensure upload completes regardless of file size
        set_time_limit(0);

        $match = MatchVideo::find($this->matchId);

        if (!$match) {
            Log::error('Match not found for assembly and upload', ['matchId' => $this->matchId]);
            return;
        }

        $chunkStorage = Storage::disk($this->chunkStorageDisk);
        $s3Storage = Storage::disk('s3');

        try {
            Log::info('Starting chunk assembly and S3 upload', [
                'matchId' => $this->matchId,
                'finalPath' => $this->finalPath,
                'totalChunks' => $this->totalChunks,
                'chunkDir' => $this->chunkDir
            ]);

            // Use streaming approach: read chunks and write directly to temp file, then stream to S3
            // This avoids loading entire file into memory
            $tempStorage = Storage::disk('local');
            $tempUploadDir = 'temp_uploads/assembly_' . $this->matchId;
            $tempFilePath = $tempUploadDir . '/' . basename($this->finalPath);

            // Ensure directory exists
            $tempFullPath = storage_path('app/' . $tempFilePath);
            $tempFullDir = dirname($tempFullPath);
            if (!file_exists($tempFullDir)) {
                mkdir($tempFullDir, 0755, true);
            }

            // Open temp file for writing (stream mode)
            $tempFileHandle = fopen($tempFullPath, 'wb');
            if ($tempFileHandle === false) {
                throw new \Exception('Failed to create temp file for assembly');
            }

            try {
                // Stream chunks directly to temp file (memory efficient)
                $totalBytesWritten = 0;
                for ($i = 0; $i < $this->totalChunks; $i++) {
                    $chunkPath = $this->chunkDir . '/' . $i;

                    try {
                        // Try to read chunk (with error handling for S3 permissions)
                        try {
                            if (!$chunkStorage->exists($chunkPath)) {
                                Log::error('Chunk missing during assembly', [
                                    'matchId' => $this->matchId,
                                    'chunkIndex' => $i,
                                    'totalChunks' => $this->totalChunks,
                                    'chunkPath' => $chunkPath
                                ]);
                                throw new \Exception("Chunk {$i} is missing. Please try uploading again.");
                            }
                        } catch (\Exception $e) {
                            // If it's a permission error, try to get the file directly (might still exist)
                            if (str_contains($e->getMessage(), 'AccessDenied') || str_contains($e->getMessage(), 'ListBucket')) {
                                Log::warning('Cannot check chunk existence (may need s3:ListBucket), attempting direct read', [
                                    'chunkPath' => $chunkPath,
                                    'matchId' => $this->matchId
                                ]);
                                // Continue to try reading the file directly
                            } else {
                                // Re-throw if it's a different error or chunk is truly missing
                                throw $e;
                            }
                        }

                        // Stream chunk directly to temp file instead of loading into memory
                        $chunkStream = $chunkStorage->readStream($chunkPath);
                        if ($chunkStream === false) {
                            throw new \Exception("Failed to open chunk {$i} for reading");
                        }

                        try {
                            // Stream chunk content to temp file (buffered for efficiency)
                            $chunkBytesWritten = stream_copy_to_stream($chunkStream, $tempFileHandle);
                            if ($chunkBytesWritten === false) {
                                throw new \Exception("Failed to write chunk {$i} to temp file");
                            }
                            $totalBytesWritten += $chunkBytesWritten;
                        } finally {
                            // Always close chunk stream
                            if (is_resource($chunkStream)) {
                                fclose($chunkStream);
                            }
                        }

                    } catch (\Exception $e) {
                        Log::error('Failed to process chunk during assembly', [
                            'matchId' => $this->matchId,
                            'chunkIndex' => $i,
                            'error' => $e->getMessage()
                        ]);
                        throw new \Exception("Failed to process chunk {$i}: " . $e->getMessage());
                    }
                }
            } finally {
                // Always close temp file handle
                if (is_resource($tempFileHandle)) {
                    fclose($tempFileHandle);
                }
            }

            Log::info('Chunks assembled via streaming, cleaning up chunks before S3 upload', [
                'matchId' => $this->matchId,
                'finalPath' => $this->finalPath,
                'tempFilePath' => $tempFilePath,
                'totalBytes' => $totalBytesWritten,
                'fileSizeMB' => round($totalBytesWritten / 1024 / 1024, 2)
            ]);

            // Delete chunks immediately after assembly to free up storage
            // Chunks are no longer needed - we have the assembled temp file
            try {
                if ($chunkStorage->exists($this->chunkDir)) {
                    $chunkStorage->deleteDirectory($this->chunkDir);
                    Log::info('Chunks deleted successfully after assembly', [
                        'matchId' => $this->matchId,
                        'chunkDir' => $this->chunkDir
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to delete chunk directory after assembly (will retry later)', [
                    'chunkDir' => $this->chunkDir,
                    'error' => $e->getMessage()
                ]);
                // Don't throw - continue with S3 upload even if chunk cleanup fails
            }

            Log::info('Starting S3 upload from temp file', [
                'matchId' => $this->matchId,
                'tempFilePath' => $tempFilePath
            ]);

            // Stream upload to S3 from temp file
            $fileHandle = fopen($tempFullPath, 'rb');
            if ($fileHandle === false) {
                throw new \Exception('Failed to open temp file for S3 upload');
            }

            try {
                // Stream upload to S3 - use putStream for streaming uploads
                $uploaded = $s3Storage->writeStream($this->finalPath, $fileHandle);

                if (!$uploaded) {
                    throw new \Exception('S3 upload returned false');
                }

                // Set visibility after upload
                try {
                    $s3Storage->setVisibility($this->finalPath, 'public');
                } catch (\Exception $visibilityError) {
                    Log::warning('Failed to set public visibility (file still uploaded)', [
                        'path' => $this->finalPath,
                        'error' => $visibilityError->getMessage()
                    ]);
                }

                // Generate URL and update match
                $matchService = app(\App\Services\MatchService::class);
                $videoUrl = $matchService->generateStorageUrl('s3', $this->finalPath);

                // Update match with video URL and change status to pending
                $match->update([
                    'video_url' => $videoUrl,
                    'status'    => 'pending',
                ]);

                Log::info('S3 background upload completed successfully', [
                    'matchId' => $this->matchId,
                    'finalPath' => $this->finalPath,
                    'videoUrl' => $videoUrl
                ]);

                // Clean up temp file immediately after S3 upload
                // Temp file is no longer needed - file is now on S3
                try {
                    if (file_exists($tempFullPath)) {
                        unlink($tempFullPath);
                        Log::info('Temp file deleted successfully after S3 upload', [
                            'matchId' => $this->matchId,
                            'tempFilePath' => $tempFullPath
                        ]);
                    }
                    // Also try to remove parent directory if empty
                    if (file_exists($tempFullDir) && count(scandir($tempFullDir)) === 2) { // 2 = . and ..
                        rmdir($tempFullDir);
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to delete temp file after S3 upload', [
                        'tempFilePath' => $tempFullPath,
                        'error' => $e->getMessage()
                    ]);
                }

                // Chunks should already be deleted after assembly, but try again as fallback
                // This ensures cleanup even if previous deletion failed
                try {
                    if ($chunkStorage->exists($this->chunkDir)) {
                        $chunkStorage->deleteDirectory($this->chunkDir);
                        Log::info('Chunks deleted in fallback cleanup after S3 upload', [
                            'matchId' => $this->matchId,
                            'chunkDir' => $this->chunkDir
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to delete chunk directory in fallback cleanup', [
                        'chunkDir' => $this->chunkDir,
                        'error' => $e->getMessage()
                    ]);
                }

                // Clean up cache
                $uploadId = basename($this->finalPath, '_' . basename($this->finalPath));
                $userId = $match->user_id;
                $uploadKey = 'upload_user_' . $userId . '_' . $uploadId;
                Cache::forget($uploadKey);

                // Send success notification
                $notificationService = app(NotificationService::class);
                $notificationService->notifyUploadSuccess($match);

            } finally {
                // Always close the file handle
                if (is_resource($fileHandle)) {
                    fclose($fileHandle);
                }
            }

        } catch (\Exception $e) {
            Log::error('Chunk assembly and S3 upload failed', [
                'matchId' => $this->matchId,
                'finalPath' => $this->finalPath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $match->update(['status' => 'failed']);

            // Don't delete chunks on failure - keep them for retry
            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $match = MatchVideo::find($this->matchId);

        if ($match) {
            $match->update(['status' => 'failed']);

            Log::error('Chunk assembly and S3 upload job failed after all retries', [
                'matchId' => $this->matchId,
                'error' => $exception->getMessage()
            ]);
        }

        // Clean up any temp files
        try {
            $tempFullPath = storage_path('app/temp_uploads/assembly_' . $this->matchId);
            if (file_exists($tempFullPath)) {
                // Remove directory and all contents
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($tempFullPath, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::CHILD_FIRST
                );
                foreach ($files as $fileinfo) {
                    $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                    $todo($fileinfo->getRealPath());
                }
                rmdir($tempFullPath);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to delete temp directory in failed handler', [
                'tempPath' => $tempFullPath ?? 'unknown',
                'error' => $e->getMessage()
            ]);
        }
    }
}

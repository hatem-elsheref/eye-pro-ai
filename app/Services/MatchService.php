<?php

namespace App\Services;

use App\Models\MatchVideo;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MatchService
{
    protected $chunkStorageDisk;
    protected $finalStorageDisk;

    public function __construct()
    {
        $this->chunkStorageDisk = env('CHUNK_STORAGE_DISK', 'public');
        $this->finalStorageDisk = env('FINAL_STORAGE_DISK', env('FILESYSTEM_DISK', 'public'));
    }

    /**
     * Generate storage URL with proper handling for S3 (pre-signed URLs)
     * Public method so controllers can use it
     */
    public function generateStorageUrl(string $disk, string $path): string
    {
        if ($disk === 'public' || $disk === 'local') {
            return Storage::disk($disk)->url($path);
        }

        // For S3 and other cloud storage, use pre-signed URLs if bucket is private
        // Pre-signed URLs expire after 1 hour (3600 seconds)
        // For public buckets, regular url() will work, but pre-signed is safer
        try {
            $storage = Storage::disk($disk);

            // Check if we should use pre-signed URLs (default: true for S3)
            $usePreSigned = env('S3_USE_PRESIGNED_URLS', true);

            if ($usePreSigned && $disk === 's3') {
                // Generate pre-signed URL that expires in 1 hour (3600 seconds)
                // You can adjust the expiration time as needed
                try {
                    return $storage->temporaryUrl($path, now()->addHours(1));
                } catch (\Exception $e) {
                    // If temporaryUrl fails (e.g., permission denied), try regular URL
                    Log::warning('Failed to generate pre-signed URL, trying regular URL', [
                        'disk' => $disk,
                        'path' => $path,
                        'error' => $e->getMessage()
                    ]);
                    return $storage->url($path);
                }
            }

            // Fallback to regular URL (works if bucket has public read access)
            return $storage->url($path);
        } catch (\Exception $e) {
            Log::error('Failed to generate storage URL', [
                'disk' => $disk,
                'path' => $path,
                'error' => $e->getMessage()
            ]);

            // If all else fails, construct a basic URL from bucket and path
            if ($disk === 's3') {
                $bucket = env('AWS_BUCKET');
                $region = env('AWS_DEFAULT_REGION', 'us-east-1');
                $url = env('AWS_URL');
                if ($url) {
                    return rtrim($url, '/') . '/' . ltrim($path, '/');
                }
                return "https://{$bucket}.s3.{$region}.amazonaws.com/" . ltrim($path, '/');
            }

            // Last resort fallback
            return Storage::disk($disk)->url($path);
        }
    }

    /**
     * Create match from file upload
     */
    public function createFromFile(User $user, string $name, $file): MatchVideo
    {
        $finalDisk = $this->finalStorageDisk;

        // Store file with public visibility for S3 (allows direct access)
        // If using private bucket, we'll use pre-signed URLs when generating URLs
        $videoPath = $file->store('Full_Matches', $finalDisk);

        // Set visibility to public for S3 if bucket supports it
        if ($finalDisk === 's3') {
            try {
                Storage::disk($finalDisk)->setVisibility($videoPath, 'public');
            } catch (\Exception $e) {
                Log::warning('Failed to set public visibility for S3 file', [
                    'path' => $videoPath,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $videoUrl = $this->generateStorageUrl($finalDisk, $videoPath);

        return MatchVideo::create([
            'user_id' => $user->id,
            'name' => $name,
            'type' => 'file',
            'status' => 'pending',
            'video_url' => $videoUrl,
            'video_path' => $videoPath,
            'file_size' => $this->formatBytes($file->getSize()),
            'storage_disk' => $finalDisk,
        ]);
    }

    /**
     * Create match from URL
     */
    public function createFromUrl(User $user, string $name, string $url): MatchVideo
    {
        return MatchVideo::create([
            'user_id' => $user->id,
            'name' => $name,
            'type' => 'url',
            'status' => 'pending',
            'video_url' => $url,
            'storage_disk' => $this->finalStorageDisk,
        ]);
    }

    /**
     * Store upload chunk
     */
    public function storeChunk(User $user, array $data): array
    {
        $uploadId = $data['uploadId'];
        $userId = $user->id;

        // Validate upload ID
        if (!str_contains($uploadId, '_user_' . $userId . '_')) {
            throw new \Exception('Invalid upload ID');
        }

        // Note: Removed concurrent upload lock - users can now upload multiple files simultaneously

        $chunkDir = 'chunks/user_' . $userId . '/' . $uploadId;
        $chunkStorage = Storage::disk($this->chunkStorageDisk);

        if (in_array($this->chunkStorageDisk, ['public', 'local'])) {
            if (!$chunkStorage->exists($chunkDir)) {
                $chunkStorage->makeDirectory($chunkDir, 0755, true);
            }
        } else {
            // For S3 and other cloud storage, try to check if directory exists
            // If ListBucket permission is missing, this will fail gracefully
            try {
                if (!$chunkStorage->exists($chunkDir)) {
                    // Directory doesn't exist, but that's ok - we'll create it when storing chunks
                }
            } catch (\Exception $e) {
                // Permission denied or other error - continue anyway
                // S3 will create the "directory" (prefix) automatically when we put the file
                Log::debug('Could not check if chunk directory exists (may need s3:ListBucket permission)', [
                    'chunkDir' => $chunkDir,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $chunkPath = $chunkDir . '/' . $data['chunkIndex'];
        $chunkStorage->putFileAs($chunkDir, $data['chunk'], (string) $data['chunkIndex']);

        // Update upload status
        $uploadKey = 'upload_user_' . $userId . '_' . $uploadId;
        $uploadStatus = Cache::get($uploadKey, [
            'userId' => $userId,
            'uploadId' => $uploadId,
            'fileName' => $data['fileName'],
            'fileSize' => $data['fileSize'],
            'totalChunks' => $data['totalChunks'],
            'uploadedChunks' => [],
            'created_at' => now()->toIso8601String(),
        ]);

        $uploadStatus['uploadedChunks'][] = $data['chunkIndex'];
        $uploadStatus['uploadedChunks'] = array_unique($uploadStatus['uploadedChunks']);
        sort($uploadStatus['uploadedChunks']);

        Cache::put($uploadKey, $uploadStatus, now()->addHours(2));

        return [
            'success' => true,
            'chunkIndex' => $data['chunkIndex'],
            'uploadedChunks' => $uploadStatus['uploadedChunks'],
            'totalChunks' => $data['totalChunks'],
            'progress' => (count($uploadStatus['uploadedChunks']) / $data['totalChunks']) * 100,
        ];
    }

    /**
     * Finalize chunked upload
     */
    public function finalizeUpload(User $user, string $uploadId, string $matchName): MatchVideo
    {
        $userId = $user->id;

        // Validate upload ID
        if (!str_contains($uploadId, '_user_' . $userId . '_')) {
            throw new \Exception('Invalid upload ID');
        }

        $uploadKey = 'upload_user_' . $userId . '_' . $uploadId;
        $uploadStatus = Cache::get($uploadKey);

        if (!$uploadStatus || ($uploadStatus['userId'] ?? null) != $userId) {
            throw new \Exception('Upload session not found');
        }

        $totalChunks = (int) $uploadStatus['totalChunks'];
        $uploadedChunks = $uploadStatus['uploadedChunks'] ?? [];

        if (count($uploadedChunks) !== $totalChunks) {
            throw new \Exception('Not all chunks uploaded. Expected: ' . $totalChunks . ', Uploaded: ' . count($uploadedChunks));
        }

        $chunkDir = 'chunks/user_' . $userId . '/' . $uploadId;
        $finalFileName = $uploadStatus['fileName'];
        $finalPath = 'Full_Matches/' . $uploadId . '_' . $finalFileName;

        // Always upload to S3 using background job for assembly and upload
        $match = MatchVideo::create([
            'user_id' => $userId,
            'name' => $matchName,
            'type' => 'file',
            'status' => 'uploading', // Temporary status while uploading to S3
            'video_url' => null, // Will be set by background job
            'video_path' => $finalPath,
            'file_size' => $this->formatBytes($uploadStatus['fileSize']),
            'storage_disk' => 's3', // Always use S3
        ]);

        // Dispatch background job to assemble chunks and upload to S3
        // This happens entirely in background - user doesn't wait
        \App\Jobs\AssembleAndUploadToS3::dispatch(
            $match->id,
            $chunkDir,
            $finalPath,
            $totalChunks,
            $this->chunkStorageDisk
        )->onQueue('uploads')->delay(now()->addSeconds(5)); // Use dedicated queue for uploads

        Log::info('Dispatched assembly and S3 upload job', [
            'matchId' => $match->id,
            'uploadId' => $uploadId,
            'finalPath' => $finalPath,
            'totalChunks' => $totalChunks,
            'chunkDir' => $chunkDir
        ]);

        // Send notification that upload has started
        $notificationService = app(\App\Services\NotificationService::class);
        $notificationService->notifyUploadStarted($match);

        // Don't delete temp file or chunks yet - job will handle cleanup
        // Don't delete cache yet - job might need it

        return $match;
    }

    /**
     * Get upload status
     */
    public function getUploadStatus(User $user, string $uploadId): array
    {
        $userId = $user->id;

        if (!str_contains($uploadId, '_user_' . $userId . '_')) {
            throw new \Exception('Invalid upload ID');
        }

        $uploadKey = 'upload_user_' . $userId . '_' . $uploadId;
        $uploadStatus = Cache::get($uploadKey);

        if (!$uploadStatus || ($uploadStatus['userId'] ?? null) != $userId) {
            throw new \Exception('Upload session not found');
        }

        return [
            'success' => true,
            'uploadStatus' => $uploadStatus,
            'progress' => (count($uploadStatus['uploadedChunks']) / $uploadStatus['totalChunks']) * 100,
        ];
    }

    /**
     * Check if should assemble directly in storage
     */
    protected function shouldAssembleDirectlyInStorage(): bool
    {
        return !in_array($this->finalStorageDisk, ['public', 'local']);
    }

    /**
     * Format bytes to human readable
     */
    protected function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}



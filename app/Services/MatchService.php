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
     * Create match from file upload
     */
    public function createFromFile(User $user, string $name, $file): MatchVideo
    {
        $finalDisk = $this->finalStorageDisk;
        $videoPath = $file->store('matches', $finalDisk);
        
        $videoUrl = ($finalDisk === 'public') 
            ? Storage::disk('public')->url($videoPath)
            : Storage::disk($finalDisk)->url($videoPath);

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

        // Check concurrent upload lock
        $lockKey = 'upload_lock_user_' . $userId;
        $lock = Cache::get($lockKey);
        if ($lock && $lock !== $uploadId) {
            throw new \Exception('Another upload is in progress');
        }

        Cache::put($lockKey, $uploadId, now()->addHours(2));

        $chunkDir = 'chunks/user_' . $userId . '/' . $uploadId;
        $chunkStorage = Storage::disk($this->chunkStorageDisk);

        if (in_array($this->chunkStorageDisk, ['public', 'local'])) {
            if (!$chunkStorage->exists($chunkDir)) {
                $chunkStorage->makeDirectory($chunkDir, 0755, true);
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

        $totalChunks = $uploadStatus['totalChunks'];
        $uploadedChunks = $uploadStatus['uploadedChunks'];

        if (count($uploadedChunks) !== (int) $totalChunks) {
            throw new \Exception('Not all chunks uploaded');
        }

        $chunkDir = 'chunks/user_' . $userId . '/' . $uploadId;
        $finalFileName = $uploadStatus['fileName'];
        $finalPath = 'matches/' . $uploadId . '_' . $finalFileName;

        $chunkStorage = Storage::disk($this->chunkStorageDisk);
        $finalStorage = Storage::disk($this->finalStorageDisk);

        // Assemble chunks
        if ($this->shouldAssembleDirectlyInStorage()) {
            $tempPath = sys_get_temp_dir() . '/' . $uploadId . '_' . $finalFileName;
            $tempFile = fopen($tempPath, 'wb');

            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkPath = $chunkDir . '/' . $i;
                if (!$chunkStorage->exists($chunkPath)) {
                    fclose($tempFile);
                    @unlink($tempPath);
                    throw new \Exception("Chunk {$i} is missing");
                }
                fwrite($tempFile, $chunkStorage->get($chunkPath));
            }

            fclose($tempFile);
            $finalStorage->put($finalPath, file_get_contents($tempPath));
            @unlink($tempPath);
            $videoUrl = $finalStorage->url($finalPath);
        } else {
            $finalDir = dirname($finalPath);
            if (in_array($this->finalStorageDisk, ['public', 'local'])) {
                if (!$finalStorage->exists($finalDir)) {
                    $finalStorage->makeDirectory($finalDir, 0755, true);
                }
            }

            $finalFile = fopen($finalStorage->path($finalPath), 'wb');
            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkPath = $chunkDir . '/' . $i;
                if (!$chunkStorage->exists($chunkPath)) {
                    fclose($finalFile);
                    if ($finalStorage->exists($finalPath)) {
                        $finalStorage->delete($finalPath);
                    }
                    throw new \Exception("Chunk {$i} is missing");
                }
                fwrite($finalFile, $chunkStorage->get($chunkPath));
            }
            fclose($finalFile);

            $videoUrl = ($this->finalStorageDisk === 'public')
                ? Storage::disk('public')->url($finalPath)
                : $finalStorage->url($finalPath);
        }

        // Clean up
        try {
            $chunkStorage->deleteDirectory($chunkDir);
        } catch (\Exception $e) {
            Log::warning('Failed to delete chunk directory: ' . $e->getMessage());
        }

        Cache::forget($uploadKey);
        Cache::forget('upload_lock_user_' . $userId);

        // Create match
        $match = MatchVideo::create([
            'user_id' => $userId,
            'name' => $matchName,
            'type' => 'file',
            'status' => 'pending',
            'video_url' => $videoUrl,
            'video_path' => $finalPath,
            'file_size' => $this->formatBytes($uploadStatus['fileSize']),
            'storage_disk' => $this->finalStorageDisk,
        ]);

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



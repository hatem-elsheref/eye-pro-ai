<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupOrphanedChunks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chunks:cleanup
                            {--force : Force cleanup without confirmation}
                            {--older-than=2 : Clean up chunks older than N hours (default: 2)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up orphaned chunks from failed/expired uploads and expired cache keys';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of orphaned chunks and expired cache keys...');

        $olderThanHours = (int) $this->option('older-than');
        $force = $this->option('force');

        // Get chunk storage disk
        $chunkDisk = $this->getChunkStorageDisk();
        $chunkStorage = Storage::disk($chunkDisk);

        $this->info("Using chunk storage disk: {$chunkDisk}");

        // Step 1: Clean up expired cache entries
        $expiredCacheCount = $this->cleanupExpiredCache();
        $this->info("Cleaned up {$expiredCacheCount} expired cache entries");

        // Step 2: Find and clean orphaned chunks
        $chunksPath = 'chunks';

        if (!$chunkStorage->exists($chunksPath)) {
            $this->info('No chunks directory found. Nothing to clean.');
            return 0;
        }

        $orphanedChunks = $this->findOrphanedChunks($chunkStorage, $chunksPath, $olderThanHours);

        if (empty($orphanedChunks)) {
            $this->info('No orphaned chunks found.');
            return 0;
        }

        $this->warn('Found ' . count($orphanedChunks) . ' orphaned chunk directories:');
        foreach ($orphanedChunks as $chunkDir) {
            $this->line("  - {$chunkDir}");
        }

        if (!$force) {
            if (!$this->confirm('Do you want to delete these orphaned chunks?', true)) {
                $this->info('Cleanup cancelled.');
                return 0;
            }
        }

        // Delete orphaned chunks
        $deletedCount = 0;
        $errors = [];

        foreach ($orphanedChunks as $chunkDir) {
            try {
                if ($chunkStorage->exists($chunkDir)) {
                    $chunkStorage->deleteDirectory($chunkDir);
                    $deletedCount++;
                    $this->line("Deleted: {$chunkDir}");
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to delete {$chunkDir}: " . $e->getMessage();
                Log::error("Failed to delete chunk directory {$chunkDir}: " . $e->getMessage());
            }
        }

        if (!empty($errors)) {
            $this->error('Some deletions failed:');
            foreach ($errors as $error) {
                $this->error("  - {$error}");
            }
        }

        $this->info("Successfully deleted {$deletedCount} orphaned chunk directories.");

        $this->info('Cleanup completed!');

        return 0;
    }

    /**
     * Get chunk storage disk (same logic as controller)
     */
    protected function getChunkStorageDisk()
    {
        return env('CHUNK_STORAGE_DISK', 'public');
    }

    /**
     * Clean up expired cache entries from database
     */
    protected function cleanupExpiredCache()
    {
        $cacheDriver = config('cache.default');

        if ($cacheDriver !== 'database') {
            // For non-database cache, we can't easily query expired entries
            // Cache will expire automatically, so we'll just clean up locks
            return 0;
        }

        $now = now()->timestamp;
        $table = config('cache.stores.database.table', 'cache');

        // Delete expired cache entries (upload status only - locks removed to allow concurrent uploads)
        $deleted = DB::table($table)
            ->where('key', 'like', Cache::getPrefix() . 'upload_user_%')
            ->where('expiration', '<', $now)
            ->delete();

        return $deleted;
    }

    /**
     * Find orphaned chunks (directories without active cache entries)
     */
    protected function findOrphanedChunks($storage, $path, $olderThanHours)
    {
        $orphaned = [];
        $cutoffTime = now()->subHours($olderThanHours)->timestamp;

        // Get all directories under chunks/user_*/upload_*
        try {
            $directories = $this->getDirectoriesRecursive($storage, $path);

            foreach ($directories as $dir) {
                // Extract uploadId from path: chunks/user_{userId}/{uploadId}
                $parts = explode('/', $dir);
                if (count($parts) >= 3 && $parts[0] === 'chunks' && str_starts_with($parts[1], 'user_')) {
                    $userId = str_replace('user_', '', $parts[1]);
                    $uploadId = $parts[2];

                    // Check if cache entry exists (Cache::has() handles prefix automatically)
                    $uploadKey = 'upload_user_' . $userId . '_' . $uploadId;
                    $hasCache = Cache::has($uploadKey);

                    // Also check if it's a very recent directory (might be in progress)
                    $isRecent = false;
                    try {
                        $dirTime = $this->getDirectoryModifiedTime($storage, $dir);
                        // If modified in last 50 minutes, assume still in progress
                        if ($dirTime && $dirTime > now()->subMinutes(50)->timestamp) {
                            $isRecent = true;
                        }
                    } catch (\Exception $e) {
                        // Ignore time check errors
                    }

                    if ($isRecent) {
                        continue; // Skip recent directories
                    }

                    if (!$hasCache) {
                        // Check if directory is older than cutoff
                        try {
                            $dirModifiedTime = $this->getDirectoryModifiedTime($storage, $dir);

                            if ($dirModifiedTime && $dirModifiedTime < $cutoffTime) {
                                $orphaned[] = $dir;
                            } else if (!$dirModifiedTime) {
                                // If we can't get time, assume it's orphaned if no cache
                                $orphaned[] = $dir;
                            }
                        } catch (\Exception $e) {
                            // If we can't check time and no cache, consider it orphaned
                            $orphaned[] = $dir;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->error('Error scanning directories: ' . $e->getMessage());
            Log::error('Error scanning chunk directories: ' . $e->getMessage());
        }

        return $orphaned;
    }

    /**
     * Get directories recursively
     */
    protected function getDirectoriesRecursive($storage, $path)
    {
        $directories = [];

        try {
            // Try to get directories first
            $dirs = $storage->directories($path);

            foreach ($dirs as $dir) {
                $parts = explode('/', $dir);
                $parts = array_filter($parts); // Remove empty parts
                $parts = array_values($parts); // Re-index

                // Check if this matches chunks/user_*/upload_* pattern
                if (count($parts) >= 3 &&
                    $parts[0] === 'chunks' &&
                    str_starts_with($parts[1], 'user_') &&
                    str_starts_with($parts[2], 'upload_')) {
                    // This is a complete upload directory
                    $directories[] = implode('/', $parts);
                } else if (count($parts) === 2 &&
                          $parts[0] === 'chunks' &&
                          str_starts_with($parts[1], 'user_')) {
                    // This is a user folder, recurse into it
                    $directories = array_merge($directories, $this->getDirectoriesRecursive($storage, $dir));
                }
            }
        } catch (\Exception $e) {
            // For S3 or if directories() doesn't work, use allFiles()
            try {
                $allFiles = $storage->allFiles($path);
                $foundDirs = [];

                foreach ($allFiles as $file) {
                    $fileParts = explode('/', $file);
                    $fileParts = array_filter($fileParts);
                    $fileParts = array_values($fileParts);

                    // Extract upload directory: chunks/user_*/upload_*
                    if (count($fileParts) >= 3 &&
                        $fileParts[0] === 'chunks' &&
                        str_starts_with($fileParts[1], 'user_')) {
                        // Get upload directory (chunks/user_*/uploadId)
                        $uploadDir = implode('/', array_slice($fileParts, 0, 3));

                        if (!in_array($uploadDir, $foundDirs)) {
                            $foundDirs[] = $uploadDir;
                            $directories[] = $uploadDir;
                        }
                    }
                }
            } catch (\Exception $e2) {
                Log::error('Error getting directories recursively: ' . $e2->getMessage());
            }
        }

        return array_unique($directories);
    }

    /**
     * Get directory modified time (approximate)
     */
    protected function getDirectoryModifiedTime($storage, $dir)
    {
        try {
            // Try to get the oldest file in the directory as a proxy for dir time
            $files = $storage->files($dir);

            if (empty($files)) {
                return null;
            }

            $oldestTime = null;
            foreach ($files as $file) {
                try {
                    $fileTime = $storage->lastModified($file);
                    if (!$oldestTime || $fileTime < $oldestTime) {
                        $oldestTime = $fileTime;
                    }
                } catch (\Exception $e) {
                    // Skip if can't get time
                }
            }

            return $oldestTime;
        } catch (\Exception $e) {
            return null;
        }
    }

}

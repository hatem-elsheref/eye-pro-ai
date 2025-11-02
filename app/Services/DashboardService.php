<?php

namespace App\Services;

use App\Models\MatchVideo;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Calculate storage used by checking folder sizes
     */
    protected function calculateStorageUsed(User $user, bool $isAdmin): int
    {
        $totalSize = 0;
        $chunkStorageDisk = env('CHUNK_STORAGE_DISK', 'public');
        $finalStorageDisk = env('FINAL_STORAGE_DISK', env('FILESYSTEM_DISK', 'public'));

        try {
            if ($isAdmin) {
                // Admin: Check size of entire chunks and matches folders
                $chunkDisk = Storage::disk($chunkStorageDisk);
                $finalDisk = Storage::disk($finalStorageDisk);

                // Calculate chunks folder size
                if ($chunkDisk->exists('chunks')) {
                    $totalSize += $this->getDirectorySize($chunkDisk, 'chunks');
                }

                // Calculate matches folder size
                if ($finalDisk->exists('matches')) {
                    $totalSize += $this->getDirectorySize($finalDisk, 'matches');
                }
            } else {
                // Regular user: Check only their folders
                $userId = $user->id;
                $chunkDisk = Storage::disk($chunkStorageDisk);
                $finalDisk = Storage::disk($finalStorageDisk);

                // Check chunks/user_{user_id} folder
                $userChunkDir = "chunks/user_{$userId}";
                if ($chunkDisk->exists($userChunkDir)) {
                    $totalSize += $this->getDirectorySize($chunkDisk, $userChunkDir);
                }

                // Check matches folder but only files that contain upload_user_{user_id} pattern
                // Files are stored as: matches/{uploadId}_{fileName}
                // Where uploadId contains: upload_user_{userId}_...
                if ($finalDisk->exists('matches')) {
                    $userPattern = "_user_{$userId}_";
                    $files = $finalDisk->files('matches');

                    foreach ($files as $file) {
                        $fileName = basename($file);
                        // Check if filename contains the user pattern
                        if (str_contains($fileName, $userPattern)) {
                            try {
                                $totalSize += $finalDisk->size($file);
                            } catch (\Exception $e) {
                                // Skip if file doesn't exist or can't be read
                                continue;
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Error calculating storage used', [
                'userId' => $user->id,
                'isAdmin' => $isAdmin,
                'error' => $e->getMessage()
            ]);
        }

        return $totalSize;
    }

    /**
     * Get total size of a directory recursively
     */
    protected function getDirectorySize($disk, string $directory): int
    {
        $size = 0;

        try {
            // Get all files in directory recursively
            $files = $disk->allFiles($directory);

            foreach ($files as $file) {
                try {
                    $size += $disk->size($file);
                } catch (\Exception $e) {
                    // Skip files that can't be read
                    continue;
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Error getting directory size', [
                'directory' => $directory,
                'error' => $e->getMessage()
            ]);
        }

        return $size;
    }

    /**
     * Format bytes to human readable
     */
    protected function formatBytes($bytes, $precision = 2): string
    {
        if ($bytes == 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $base = log($bytes, 1024);
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $units[floor($base)];
    }

    /**
     * Get dashboard data for user
     */
    public function getDashboardData(User $user): array
    {
        $isAdmin = $user->is_admin ?? false;

        // Build query - admins see all, users see only their own
        $matchesQuery = MatchVideo::query();
        if (!$isAdmin) {
            $matchesQuery->where('user_id', $user->id);
        }

        $totalMatches = $matchesQuery->count();

        // Processing matches count
        $processingQuery = clone $matchesQuery;
        $processingCount = $processingQuery->where('status', 'processing')->count();

        // Calculate storage used by checking folder sizes directly
        $totalStorageUsed = $this->calculateStorageUsed($user, $isAdmin);
        $storageUsedFormatted = $this->formatBytes($totalStorageUsed);

        // Default storage limit (can be made configurable)
        $storageLimit = 100 * 1024 * 1024 * 1024; // 100 GB in bytes
        $storagePercentage = $storageLimit > 0 ? ($totalStorageUsed / $storageLimit) * 100 : 0;
        $storageLimitFormatted = $this->formatBytes($storageLimit);

        // Recent matches
        $recentMatchesQuery = MatchVideo::query();
        if (!$isAdmin) {
            $recentMatchesQuery->where('user_id', $user->id);
        }
        $recentMatches = $recentMatchesQuery->latest()->take(5)->get();

        // Activity Overview - Last 30 days
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $activityQuery = MatchVideo::query();
        if (!$isAdmin) {
            $activityQuery->where('user_id', $user->id);
        }

        // Uploads this month
        $uploadsThisMonth = (clone $activityQuery)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->count();

        // Completed matches
        $completedCount = (clone $matchesQuery)
            ->where('status', 'completed')
            ->count();

        // Processing matches (already calculated above)
        $processingMatches = $processingCount;

        // Average processing time (mock for now, could be calculated from timestamps)
        $avgProcessingTime = '2.5m'; // This could be calculated from actual processing times

        // Calculate uploads trend
        $previousMonthQuery = MatchVideo::query();
        if (!$isAdmin) {
            $previousMonthQuery->where('user_id', $user->id);
        }
        $uploadsPreviousMonth = $previousMonthQuery
            ->whereBetween('created_at', [
                Carbon::now()->subDays(60),
                $thirtyDaysAgo
            ])
            ->count();

        $uploadTrend = 0;
        if ($uploadsPreviousMonth > 0) {
            $uploadTrend = (($uploadsThisMonth - $uploadsPreviousMonth) / $uploadsPreviousMonth) * 100;
        } elseif ($uploadsThisMonth > 0) {
            $uploadTrend = 100; // 100% increase if went from 0 to something
        }

        return [
            'totalMatches' => $totalMatches,
            'processingCount' => $processingCount,
            'storageUsed' => $storageUsedFormatted,
            'storageUsedBytes' => $totalStorageUsed,
            'storageLimit' => $storageLimitFormatted,
            'storageLimitBytes' => $storageLimit,
            'storagePercentage' => round($storagePercentage, 2),
            'recentMatches' => $recentMatches,
            'accountPending' => !$user->is_approved,
            'uploadsThisMonth' => $uploadsThisMonth,
            'completedCount' => $completedCount,
            'avgProcessingTime' => $avgProcessingTime,
            'uploadTrend' => round($uploadTrend, 0),
            'isAdmin' => $isAdmin,
        ];
    }
}



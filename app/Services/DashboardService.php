<?php

namespace App\Services;

use App\Models\MatchVideo;
use App\Models\User;
use App\Services\AdminService;
use Carbon\Carbon;

class DashboardService
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }
    /**
     * Calculate storage used from database (sum of file_size for matches)
     * Only counts matches with type='file' and status NOT in ['uploading', 'failed']
     */
    protected function calculateStorageUsed(User $user, bool $isAdmin): int
    {
        $totalSize = 0;

        try {
            // Build query for matches with type='file' and status not in ['uploading', 'failed']
            $query = MatchVideo::where('type', 'file')
                ->whereNotIn('status', ['uploading', 'failed']);

            // Filter by user if not admin
            if (!$isAdmin) {
                $query->where('user_id', $user->id);
            }

            // Get all matches and sum their file sizes
            $matches = $query->get(['id', 'file_size']);

            foreach ($matches as $match) {
                if ($match->file_size) {
                    // Parse file_size from human-readable format (e.g., "500 MB") to bytes
                    $sizeInBytes = $this->parseFileSizeToBytes($match->file_size);
                    $totalSize += $sizeInBytes;
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Error calculating storage used from database', [
                'userId' => $user->id,
                'isAdmin' => $isAdmin,
                'error' => $e->getMessage()
            ]);
        }

        return $totalSize;
    }

    /**
     * Parse human-readable file size (e.g., "500 MB", "1.5 GB") to bytes
     */
    protected function parseFileSizeToBytes(string $fileSize): int
    {
        // Remove whitespace and convert to uppercase
        $fileSize = trim(strtoupper($fileSize));

        // If already in bytes (numeric only), return as is
        if (is_numeric($fileSize)) {
            return (int) $fileSize;
        }

        // Extract number and unit
        preg_match('/^([\d.]+)\s*([A-Z]+)$/', $fileSize, $matches);

        if (count($matches) !== 3) {
            return 0; // Invalid format
        }

        $number = (float) $matches[1];
        $unit = $matches[2];

        // Convert to bytes based on unit
        $multipliers = [
            'B' => 1,
            'KB' => 1024,
            'MB' => 1024 * 1024,
            'GB' => 1024 * 1024 * 1024,
            'TB' => 1024 * 1024 * 1024 * 1024,
        ];

        $multiplier = $multipliers[$unit] ?? 1;

        return (int) ($number * $multiplier);
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

        // Build base query - admins see all, users see only their own
        $baseQuery = MatchVideo::query();
        if (!$isAdmin) {
            $baseQuery->where('user_id', $user->id);
        }

        // Total matches count
        $totalMatches = (clone $baseQuery)->count();

        // Processing matches count
        $processingCount = (clone $baseQuery)
            ->where('status', 'processing')
            ->count();

        // Calculate storage used from database
        // Only counts matches with type='file' and status not in ['uploading', 'failed']
        $totalStorageUsed = $this->calculateStorageUsed($user, $isAdmin);
        $storageUsedFormatted = $this->formatBytes($totalStorageUsed);

        // Default storage limit (can be made configurable)
        $storageLimit = 100 * 1024 * 1024 * 1024; // 100 GB in bytes
        $storagePercentage = $storageLimit > 0 ? ($totalStorageUsed / $storageLimit) * 100 : 0;
        $storageLimitFormatted = $this->formatBytes($storageLimit);

        // Recent matches (latest 5)
        $recentMatches = (clone $baseQuery)
            ->latest()
            ->take(5)
            ->get();

        // Activity Overview - Last 30 days
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // Uploads this month (last 30 days)
        $uploadsThisMonth = (clone $baseQuery)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->count();

        // Completed matches count
        $completedCount = (clone $baseQuery)
            ->where('status', 'completed')
            ->count();

        // Calculate uploads trend (comparing this month vs previous month)
        $uploadsPreviousMonth = (clone $baseQuery)
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

        $result = [
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
            'uploadTrend' => round($uploadTrend, 0),
            'isAdmin' => $isAdmin,
        ];

        // If admin, add admin stats
        if ($isAdmin) {
            $adminStats = $this->adminService->getDashboardStats();
            $result = array_merge($result, $adminStats);
        }

        return $result;
    }
}



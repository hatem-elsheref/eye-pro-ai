<?php

namespace App\Services;

use App\Models\User;
use App\Models\MatchVideo;
use App\Services\NotificationService;

class AdminService
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Get admin dashboard statistics
     */
    public function getDashboardStats(): array
    {
        return [
            'totalUsers' => User::where('is_admin', false)->count(),
            'pendingUsers' => User::where('is_admin', false)->where('status', 'pending')->count(),
            'approvedUsers' => User::where('is_admin', false)->where('status', 'approved')->count(),
            'rejectedUsers' => User::where('is_admin', false)->where('status', 'rejected')->count(),
            'totalMatches' => MatchVideo::count(),
        ];
    }

    /**
     * Get users management statistics
     */
    public function getUsersStats(): array
    {
        return [
            'totalUsers' => User::where('is_admin', false)->count(),
            'pendingUsers' => User::where('is_admin', false)->where('status', 'pending')->count(),
            'approvedUsers' => User::where('is_admin', false)->where('status', 'approved')->count(),
            'rejectedUsers' => User::where('is_admin', false)->where('status', 'rejected')->count(),
        ];
    }

    /**
     * Get users query for DataTables
     */
    public function getUsersQuery($statusFilter = null)
    {
        $query = User::where('is_admin', false)
            ->select(['id', 'name', 'email', 'status', 'created_at']);

        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        return $query;
    }

    /**
     * Get avatar color for user
     */
    public function getAvatarColor(int $userId): array
    {
        $colors = [
            ['#60a5fa', '#818cf8'], ['#f59e0b', '#ea580c'],
            ['#10b981', '#059669'], ['#ef4444', '#dc2626'],
            ['#8b5cf6', '#7c3aed'], ['#ec4899', '#db2777'],
            ['#06b6d4', '#0891b2'], ['#f97316', '#ea580c'],
            ['#14b8a6', '#0d9488'], ['#6366f1', '#4f46e5'],
            ['#d946ef', '#c026d3'], ['#3b82f6', '#2563eb'],
            ['#84cc16', '#65a30d'], ['#a855f7', '#9333ea'],
        ];

        $colorIndex = abs($userId) % count($colors);
        return $colors[$colorIndex];
    }

    /**
     * Approve user
     */
    public function approveUser(User $user): void
    {
        $user->update([
            'is_approved' => true,
            'status' => 'approved',
        ]);

        // Send notification to user
        $this->notificationService->notifyAccountApproved($user);
    }

    /**
     * Reject user
     */
    public function rejectUser(User $user): void
    {
        $user->update([
            'status' => 'rejected',
            'is_approved' => false,
        ]);

        // Send notification to user
        $this->notificationService->notifyAccountRejected($user);
    }
}


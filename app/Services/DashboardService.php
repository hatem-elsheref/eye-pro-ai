<?php

namespace App\Services;

use App\Models\MatchVideo;
use App\Models\User;

class DashboardService
{
    /**
     * Get dashboard data for user
     */
    public function getDashboardData(User $user): array
    {
        return [
            'totalMatches' => MatchVideo::where('user_id', $user->id)->count(),
            'recentMatches' => MatchVideo::where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get(),
            'accountPending' => !$user->is_approved,
        ];
    }
}


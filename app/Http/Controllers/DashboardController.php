<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MatchVideo;

class DashboardController extends Controller
{
    /**
     * Display the dashboard
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get total matches for this user
        $totalMatches = MatchVideo::where('user_id', $user->id)->count();
        
        // Get recent matches
        $recentMatches = MatchVideo::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();
        
        return view('admin.dashboard', [
            'totalMatches' => $totalMatches,
            'recentMatches' => $recentMatches,
            'accountPending' => !$user->is_approved,
        ]);
    }
}


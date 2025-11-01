<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MatchVideo;
use Yajra\DataTables\Facades\DataTables;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function index(Request $request)
    {
        // Get statistics
        $totalUsers = User::where('is_admin', false)->count();
        $pendingUsers = User::where('is_admin', false)->where('status', 'pending')->count();
        $approvedUsers = User::where('is_admin', false)->where('status', 'approved')->count();
        $rejectedUsers = User::where('is_admin', false)->where('status', 'rejected')->count();
        $totalMatches = MatchVideo::count();

        return view('admin.admin.index', [
            'totalUsers' => $totalUsers,
            'pendingUsers' => $pendingUsers,
            'approvedUsers' => $approvedUsers,
            'rejectedUsers' => $rejectedUsers,
            'totalMatches' => $totalMatches,
            'settings' => [
                'require_approval' => true,
                'allow_uploads' => true,
            ],
        ]);
    }

    /**
     * Display users management page
     */
    public function users(Request $request)
    {
        // Get statistics
        $totalUsers = User::where('is_admin', false)->count();
        $pendingUsers = User::where('is_admin', false)->where('status', 'pending')->count();
        $approvedUsers = User::where('is_admin', false)->where('status', 'approved')->count();
        $rejectedUsers = User::where('is_admin', false)->where('status', 'rejected')->count();

        return view('admin.admin.users', [
            'totalUsers' => $totalUsers,
            'pendingUsers' => $pendingUsers,
            'approvedUsers' => $approvedUsers,
            'rejectedUsers' => $rejectedUsers,
        ]);
    }

    /**
     * Get users data for DataTables
     */
    public function getUsers(Request $request)
    {
        $query = User::where('is_admin', false)
            ->select(['id', 'name', 'email', 'status', 'created_at']);
        
        // Filter by status
        if ($request->filled('status_filter') && $request->status_filter !== 'all') {
            $query->where('status', $request->status_filter);
        }
        
        return DataTables::of($query)
            ->addColumn('avatar', function ($user) {
                $initials = strtoupper(substr($user->name, 0, 2));
                return '<div class="h-10 w-10 rounded-xl flex items-center justify-center text-white font-bold shadow-lg text-xs" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">' . $initials . '</div>';
            })
            ->addColumn('status_badge', function ($user) {
                if ($user->status === 'approved') {
                    return '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                        <span class="h-2 w-2 rounded-full bg-green-600 mr-2"></span>
                        Approved
                    </span>';
                } elseif ($user->status === 'rejected') {
                    return '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                        <span class="h-2 w-2 rounded-full bg-red-600 mr-2"></span>
                        Rejected
                    </span>';
                } else {
                    return '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200">
                        <span class="h-2 w-2 rounded-full bg-amber-600 mr-2 animate-pulse"></span>
                        Pending
                    </span>';
                }
            })
            ->addColumn('actions', function ($user) {
                $html = '<div class="flex items-center justify-end space-x-2">';
                
                if ($user->status === 'pending') {
                    $html .= '<form action="' . route('admin.users.approve', $user->id) . '" method="POST" class="inline-block approve-form">
                        ' . csrf_field() . '
                        <button type="submit" class="inline-flex items-center space-x-1.5 px-4 py-2 bg-green-600 text-white rounded-lg font-semibold text-xs hover:bg-green-700 transition-all shadow-md hover:shadow-lg hover:scale-105">
                            <i class="fas fa-check text-xs"></i>
                            <span>Approve</span>
                        </button>
                    </form>';
                    
                    $html .= '<form action="' . route('admin.users.reject', $user->id) . '" method="POST" class="inline-block reject-form">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="inline-flex items-center space-x-1.5 px-4 py-2 bg-red-600 text-white rounded-lg font-semibold text-xs hover:bg-red-700 transition-all shadow-md hover:shadow-lg hover:scale-105">
                            <i class="fas fa-times text-xs"></i>
                            <span>Reject</span>
                        </button>
                    </form>';
                } elseif ($user->status === 'approved') {
                    $html .= '<span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                        <i class="fas fa-check-circle mr-1.5 text-xs"></i>
                        Approved
                    </span>';
                } else {
                    $html .= '<span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                        <i class="fas fa-ban mr-1.5 text-xs"></i>
                        Rejected
                    </span>';
                }
                
                $html .= '</div>';
                return $html;
            })
            ->editColumn('created_at', function ($user) {
                return '<div class="flex items-center text-xs text-gray-500">
                    <i class="fas fa-clock mr-1.5"></i>
                    ' . $user->created_at->diffForHumans() . '
                </div>';
            })
            ->rawColumns(['avatar', 'status_badge', 'actions', 'created_at'])
            ->make(true);
    }

    /**
     * Approve a user
     */
    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        
        $user->update([
            'is_approved' => true,
            'status' => 'approved',
        ]);

        // You can send a notification email here
        // Mail::to($user->email)->send(new AccountApprovedMail());

        return response()->json([
            'success' => true,
            'message' => 'User "' . $user->name . '" has been approved successfully!'
        ]);
    }

    /**
     * Reject a user
     */
    public function rejectUser($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent rejecting yourself
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot reject your own account!'
            ], 403);
        }
        
        $user->update([
            'status' => 'rejected',
            'is_approved' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User "' . $user->name . '" has been rejected successfully!'
        ]);
    }

    /**
     * Update system settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'require_approval' => 'nullable|boolean',
            'allow_uploads' => 'nullable|boolean',
        ]);

        // Here you would typically store these in a settings table or config
        // For now, we'll just return success
        // You can implement a Settings model or use Laravel's config system
        
        return back()->with('success', 'System settings updated successfully!');
    }
}


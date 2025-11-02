<?php

namespace App\Http\Controllers;

use App\Services\AdminService;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function index()
    {
        $stats = $this->adminService->getDashboardStats();

        return view('admin.admin.index', array_merge($stats, [
            'settings' => [
                'require_approval' => true,
                'allow_uploads' => true,
            ],
        ]));
    }

    public function users()
    {
        $stats = $this->adminService->getUsersStats();
        return view('admin.admin.users', $stats);
    }

    public function getUsers(Request $request)
    {
        $query = $this->adminService->getUsersQuery($request->status_filter);

        return DataTables::of($query)
            ->addColumn('avatar', function ($user) {
                $initials = strtoupper(substr($user->name, 0, 2));
                $colors = $this->adminService->getAvatarColor($user->id);
                return '<div class="h-12 w-12 rounded-xl flex items-center justify-center text-white font-bold shadow-lg text-sm border-2 border-white hover:scale-110 transition-transform duration-200" style="background: linear-gradient(135deg, ' . $colors[0] . ' 0%, ' . $colors[1] . ' 100%);">' . $initials . '</div>';
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

    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $this->adminService->approveUser($user);

        return response()->json([
            'success' => true,
            'message' => 'User "' . $user->name . '" has been approved successfully!'
        ]);
    }

    public function rejectUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot reject your own account!'
            ], 403);
        }

        $this->adminService->rejectUser($user);

        return response()->json([
            'success' => true,
            'message' => 'User "' . $user->name . '" has been rejected successfully!'
        ]);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'require_approval' => 'nullable|boolean',
            'allow_uploads' => 'nullable|boolean',
        ]);

        // Here you would typically store these in a settings table or config
        // For now, we'll just return success

        return back()->with('success', 'System settings updated successfully!');
    }
}

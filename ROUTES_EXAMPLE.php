<?php

/**
 * ROUTES EXAMPLE FILE
 * 
 * This file contains all the routes you need to add to your routes/web.php file
 * to support the Blade views created in resources/views/admin/
 * 
 * Copy these routes to your routes/web.php file and adjust as needed.
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Guest Routes (Authentication)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    
    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    
    // Forgot Password
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    
    // Reset Password
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    
    // Matches
    Route::resource('matches', MatchController::class);
    // This creates the following routes:
    // GET    /matches              -> matches.index     (List all matches)
    // GET    /matches/create       -> matches.create    (Show upload form)
    // POST   /matches              -> matches.store     (Store new match)
    // GET    /matches/{id}         -> matches.show      (Show match details)
    // GET    /matches/{id}/edit    -> matches.edit      (Show edit form)
    // PUT    /matches/{id}         -> matches.update    (Update match)
    // DELETE /matches/{id}         -> matches.destroy   (Delete match)
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.delete');
    
    // Support
    Route::get('/support', [SupportController::class, 'index'])->name('support');
    Route::post('/support', [SupportController::class, 'submit'])->name('support.submit');
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    
    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        // Admin Dashboard
        Route::get('/', [AdminController::class, 'index'])->name('index');
        
        // User Management
        Route::post('/users/{id}/approve', [AdminController::class, 'approveUser'])->name('users.approve');
        Route::delete('/users/{id}/reject', [AdminController::class, 'rejectUser'])->name('users.reject');
        
        // Settings
        Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    });
});

/**
 * EXAMPLE CONTROLLERS
 * 
 * Below are example controller methods you need to create.
 * Create these controllers using: php artisan make:controller ControllerName
 */

/*
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function showRegister()
    {
        return view('admin.auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_approved' => false, // Pending approval
        ]);

        return redirect()->route('login')->with('success', 'Account created successfully. Please wait for approval.');
    }

    public function showForgotPassword()
    {
        return view('admin.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword($token)
    {
        return view('admin.auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
*/

/*
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Match;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $totalMatches = Match::where('user_id', $user->id)->count();
        $recentMatches = Match::where('user_id', $user->id)
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
*/

/*
// app/Http/Controllers/MatchController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Match;

class MatchController extends Controller
{
    public function index()
    {
        $matches = Match::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);
        
        return view('admin.matches.index', [
            'matches' => $matches,
            'accountPending' => !auth()->user()->is_approved,
        ]);
    }

    public function create()
    {
        return view('admin.matches.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'match_name' => 'required|string|max:255',
            'video_file' => 'nullable|file|mimes:mp4,avi,mov,mkv|max:2048000',
            'video_url' => 'nullable|url',
        ]);

        $match = Match::create([
            'user_id' => auth()->id(),
            'name' => $validated['match_name'],
            'type' => $request->hasFile('video_file') ? 'file' : 'url',
            'status' => 'processing',
        ]);

        // Handle file upload or URL processing here

        return redirect()->route('matches.show', $match->id)
            ->with('success', 'Match uploaded successfully!');
    }

    public function show($id)
    {
        $match = Match::where('user_id', auth()->id())->findOrFail($id);
        return view('admin.matches.show', compact('match'));
    }

    public function edit($id)
    {
        $match = Match::where('user_id', auth()->id())->findOrFail($id);
        return view('admin.matches.edit', compact('match'));
    }

    public function update(Request $request, $id)
    {
        $match = Match::where('user_id', auth()->id())->findOrFail($id);
        
        $validated = $request->validate([
            'match_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|string',
        ]);

        $match->update([
            'name' => $validated['match_name'],
            'description' => $validated['description'] ?? null,
            'tags' => $validated['tags'] ?? null,
        ]);

        return redirect()->route('matches.show', $match->id)
            ->with('success', 'Match updated successfully!');
    }

    public function destroy($id)
    {
        $match = Match::where('user_id', auth()->id())->findOrFail($id);
        $match->delete();

        return redirect()->route('matches.index')
            ->with('success', 'Match deleted successfully!');
    }
}
*/

/*
// app/Http/Controllers/ProfileController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return view('admin.profile');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
        ]);

        auth()->user()->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        auth()->user()->update([
            'password' => Hash::make($validated['new_password'])
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    public function destroy()
    {
        auth()->user()->delete();
        auth()->logout();
        return redirect('/login')->with('success', 'Account deleted successfully');
    }
}
*/

/*
// app/Http/Controllers/SupportController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        return view('admin.support');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|string',
            'priority' => 'required|string',
            'message' => 'required|string',
        ]);

        // Handle support ticket creation here
        // You might want to send an email or store in database

        return back()->with('success', 'Support ticket submitted successfully!');
    }
}
*/

/*
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Match;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $pendingUsers = User::where('is_approved', false)->count();
        $totalMatches = Match::count();
        $pendingUsersList = User::where('is_approved', false)->latest()->get();

        return view('admin.admin.index', [
            'totalUsers' => $totalUsers,
            'pendingUsers' => $pendingUsers,
            'totalMatches' => $totalMatches,
            'pendingUsersList' => $pendingUsersList,
            'settings' => [
                'require_approval' => true,
                'allow_uploads' => true,
            ],
        ]);
    }

    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_approved' => true]);

        return back()->with('success', 'User approved successfully!');
    }

    public function rejectUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'User rejected and deleted successfully!');
    }

    public function updateSettings(Request $request)
    {
        // Handle settings update
        return back()->with('success', 'Settings updated successfully!');
    }
}
*/

/*
// app/Http/Controllers/NotificationController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications;
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return back();
    }
}
*/




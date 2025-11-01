<?php

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
| Web Routes - Match Analysis Platform
|--------------------------------------------------------------------------
|
| Here are all the routes for the Match Analysis Platform.
| Routes are organized by functionality and protected by middleware.
|
*/

/*
|--------------------------------------------------------------------------
| Guest Routes (Authentication)
|--------------------------------------------------------------------------
| These routes are only accessible to guests (not logged in users)
*/

Route::middleware('guest')->group(function () {
    // Login Routes
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    
    // Register Routes
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    
    // Forgot Password Routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    
    // Reset Password Routes
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
| These routes require the user to be logged in
*/

Route::middleware(['auth'])->group(function () {
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    /*
    |--------------------------------------------------------------------------
    | Dashboard Routes
    |--------------------------------------------------------------------------
    */
    
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    
    /*
    |--------------------------------------------------------------------------
    | Match Management Routes
    |--------------------------------------------------------------------------
    | Full CRUD operations for matches
    */
    
    Route::resource('matches', MatchController::class);
    // This automatically creates:
    // GET    /matches              -> matches.index     (List all matches)
    // GET    /matches/create       -> matches.create    (Show upload form)
    // POST   /matches              -> matches.store     (Store new match)
    // GET    /matches/{id}         -> matches.show      (Show match details)
    // GET    /matches/{id}/edit    -> matches.edit      (Show edit form)
    // PUT    /matches/{id}         -> matches.update    (Update match)
    // DELETE /matches/{id}         -> matches.destroy   (Delete match)
    
    // Chunked upload routes
    Route::post('/matches/upload/chunk', [MatchController::class, 'uploadChunk'])->name('matches.upload.chunk');
    Route::post('/matches/upload/finalize', [MatchController::class, 'finalizeUpload'])->name('matches.upload.finalize');
    Route::get('/matches/upload/status/{uploadId}', [MatchController::class, 'getUploadStatus'])->name('matches.upload.status');
    
    /*
    |--------------------------------------------------------------------------
    | Profile Routes
    |--------------------------------------------------------------------------
    */
    
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.delete');
    
    /*
    |--------------------------------------------------------------------------
    | Support Routes
    |--------------------------------------------------------------------------
    */
    
    Route::get('/support', [SupportController::class, 'index'])->name('support');
    Route::post('/support', [SupportController::class, 'submit'])->name('support.submit');
    
    /*
    |--------------------------------------------------------------------------
    | Notification Routes
    |--------------------------------------------------------------------------
    */
    
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    
    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    | These routes are only accessible to admin users
    */
    
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        // Admin Dashboard
        Route::get('/', [AdminController::class, 'index'])->name('index');
        
        // Users Management Page
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::get('/users/data', [AdminController::class, 'getUsers'])->name('users.data');
        Route::post('/users/{id}/approve', [AdminController::class, 'approveUser'])->name('users.approve');
        Route::delete('/users/{id}/reject', [AdminController::class, 'rejectUser'])->name('users.reject');
        
        // System Settings
        Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    });
});

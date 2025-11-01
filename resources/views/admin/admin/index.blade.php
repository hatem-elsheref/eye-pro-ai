@extends('admin.layouts.master')

@section('title', 'Admin Panel - Eye Pro')
@section('page-title', 'Admin Panel')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6">

    <!-- Admin Header -->
    <div class="relative overflow-hidden rounded-2xl p-4 sm:p-6 shadow-lg border border-blue-200" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-white opacity-10"></div>
        <div class="absolute bottom-0 left-0 -mb-6 -ml-6 h-32 w-32 rounded-full bg-white opacity-10"></div>
        <div class="relative z-10">
            <h1 class="text-xl sm:text-2xl font-bold text-white mb-1">Admin Panel</h1>
            <p class="text-xs sm:text-sm text-blue-50 font-medium">Manage users and system settings</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Users -->
        <div class="group bg-white rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-3 border-2 border-gray-100 hover:border-blue-200 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-100 rounded-full -mr-16 -mt-16 opacity-50"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center justify-center h-16 w-16 rounded-2xl shadow-xl group-hover:shadow-2xl transition-all duration-300 group-hover:scale-110" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                        <i class="fas fa-users text-3xl text-white"></i>
                    </div>
                </div>
                <h3 class="text-gray-500 text-sm font-bold mb-2 uppercase tracking-wide">Total Users</h3>
                <p class="text-5xl font-black text-blue-700 mb-2">{{ $totalUsers ?? 0 }}</p>
                <p class="text-xs text-gray-400 font-medium">Registered users</p>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="group bg-white rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-3 border-2 border-gray-100 hover:border-amber-200 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-amber-100 rounded-full -mr-16 -mt-16 opacity-50"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center justify-center h-16 w-16 rounded-2xl shadow-xl group-hover:shadow-2xl transition-all duration-300 group-hover:scale-110" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="fas fa-clock text-3xl text-white"></i>
                    </div>
                </div>
                <h3 class="text-gray-500 text-sm font-bold mb-2 uppercase tracking-wide">Pending Approvals</h3>
                <p class="text-5xl font-black text-amber-700 mb-2">{{ $pendingUsers ?? 0 }}</p>
                <p class="text-xs text-gray-400 font-medium">Users awaiting approval</p>
            </div>
        </div>

        <!-- Approved Users -->
        <div class="group bg-white rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-3 border-2 border-gray-100 hover:border-green-200 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-green-100 rounded-full -mr-16 -mt-16 opacity-50"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center justify-center h-16 w-16 rounded-2xl shadow-xl group-hover:shadow-2xl transition-all duration-300 group-hover:scale-110" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fas fa-check-circle text-3xl text-white"></i>
                    </div>
                </div>
                <h3 class="text-gray-500 text-sm font-bold mb-2 uppercase tracking-wide">Approved Users</h3>
                <p class="text-5xl font-black text-green-700 mb-2">{{ $approvedUsers ?? 0 }}</p>
                <p class="text-xs text-gray-400 font-medium">Active users</p>
            </div>
        </div>

        <!-- Total Matches -->
        <div class="group bg-white rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-3 border-2 border-gray-100 hover:border-green-200 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-green-100 rounded-full -mr-16 -mt-16 opacity-50"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center justify-center h-16 w-16 rounded-2xl shadow-xl group-hover:shadow-2xl transition-all duration-300 group-hover:scale-110" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fas fa-video text-3xl text-white"></i>
                    </div>
                </div>
                <h3 class="text-gray-500 text-sm font-bold mb-2 uppercase tracking-wide">Total Matches</h3>
                <p class="text-5xl font-black text-green-700 mb-2">{{ $totalMatches ?? 0 }}</p>
                <p class="text-xs text-gray-400 font-medium">All uploaded matches</p>
            </div>
        </div>
    </div>

    <!-- System Settings -->
    <div class="bg-white rounded-3xl shadow-xl p-6 sm:p-8 border-2 border-gray-100">
        <!-- Header -->
        <div class="flex items-center gap-4 mb-8">
            <div class="h-12 w-12 rounded-xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                <i class="fas fa-cog text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">System Settings</h2>
                <p class="text-sm text-gray-500 mt-1">Configure platform behavior</p>
            </div>
        </div>

        <!-- Settings Form -->
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <!-- Setting Item: Require User Approval -->
                <div class="flex items-start justify-between gap-4 p-5 rounded-xl bg-gradient-to-r from-teal-50 to-blue-50 border-2 border-blue-200 hover:border-blue-300 transition-all">
                    <div class="flex gap-3 flex-1 min-w-0">
                        <div class="flex-shrink-0 mt-1">
                            <i class="fas fa-user-shield text-blue-600 text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 mb-1">Require User Approval</h3>
                            <p class="text-sm text-gray-600">New users must be approved by an administrator before accessing the platform</p>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <label for="setting-approval" class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="require_approval" value="1" {{ (old('require_approval', $settings['require_approval'] ?? true)) ? 'checked' : '' }} class="setting-checkbox sr-only" id="setting-approval">
                            <span class="setting-checkbox-label"></span>
                        </label>
                    </div>
                </div>

                <!-- Setting Item: Allow Video Uploads -->
                <div class="flex items-start justify-between gap-4 p-5 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 hover:border-blue-300 transition-all">
                    <div class="flex gap-3 flex-1 min-w-0">
                        <div class="flex-shrink-0 mt-1">
                            <i class="fas fa-cloud-upload-alt text-blue-600 text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 mb-1">Allow Video Uploads</h3>
                            <p class="text-sm text-gray-600">Enable users to upload match videos to the platform</p>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <label for="setting-uploads" class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="allow_uploads" value="1" {{ (old('allow_uploads', $settings['allow_uploads'] ?? true)) ? 'checked' : '' }} class="setting-checkbox sr-only" id="setting-uploads">
                            <span class="setting-checkbox-label"></span>
                        </label>
                    </div>
                </div>

                <!-- Setting Item: Email Notifications -->
                <div class="flex items-start justify-between gap-4 p-5 rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 hover:border-green-300 transition-all">
                    <div class="flex gap-3 flex-1 min-w-0">
                        <div class="flex-shrink-0 mt-1">
                            <i class="fas fa-envelope text-green-600 text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 mb-1">Email Notifications</h3>
                            <p class="text-sm text-gray-600">Send email notifications for important account activities</p>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <label for="setting-email" class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_notifications" value="1" checked class="setting-checkbox sr-only" id="setting-email">
                            <span class="setting-checkbox-label"></span>
                        </label>
                    </div>
                </div>

                <!-- Setting Item: Maintenance Mode -->
                <div class="flex items-start justify-between gap-4 p-5 rounded-xl bg-gradient-to-r from-red-50 to-rose-50 border-2 border-red-200 hover:border-red-300 transition-all">
                    <div class="flex gap-3 flex-1 min-w-0">
                        <div class="flex-shrink-0 mt-1">
                            <i class="fas fa-tools text-red-600 text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 mb-1">Maintenance Mode</h3>
                            <p class="text-sm text-gray-600">Temporarily disable access for all non-admin users</p>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <label for="setting-maintenance" class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="maintenance_mode" value="1" class="setting-checkbox sr-only" id="setting-maintenance">
                            <span class="setting-checkbox-label"></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-base rounded-xl shadow-md hover:shadow-lg transition-shadow duration-200">
                    <i class="fas fa-save"></i>
                    <span>Save Settings</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" style="display: none">
        <a href="{{ route('admin.users.index') }}" class="group bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-blue-400 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
            <div class="flex items-center justify-center h-14 w-14 rounded-xl bg-blue-100 mb-4 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-users text-2xl text-blue-600"></i>
            </div>
            <h3 class="font-extrabold text-lg text-gray-900 mb-1">Manage Users</h3>
            <p class="text-sm text-gray-500">View and manage all users</p>
        </a>

        <a href="#" class="group bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-blue-400 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
            <div class="flex items-center justify-center h-14 w-14 rounded-xl bg-blue-100 mb-4 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-chart-pie text-2xl text-blue-600"></i>
            </div>
            <h3 class="font-extrabold text-lg text-gray-900 mb-1">Analytics</h3>
            <p class="text-sm text-gray-500">View platform statistics</p>
        </a>

        <a href="#" class="group bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-green-400 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
            <div class="flex items-center justify-center h-14 w-14 rounded-xl bg-green-100 mb-4 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-server text-2xl text-green-600"></i>
            </div>
            <h3 class="font-extrabold text-lg text-gray-900 mb-1">System Health</h3>
            <p class="text-sm text-gray-500">Check system status</p>
        </a>
    </div>
</div>
@push('styles')
<style>
    /* Custom Checkbox Styles */
    .setting-checkbox-label {
        position: relative;
        display: inline-block;
        width: 52px;
        height: 32px;
        background-color: #e5e7eb;
        border-radius: 16px;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .setting-checkbox-label::after {
        content: '';
        position: absolute;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background-color: white;
        top: 4px;
        left: 4px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    
    .setting-checkbox:checked + .setting-checkbox-label {
        background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);
    }
    
    .setting-checkbox:checked + .setting-checkbox-label::after {
        transform: translateX(20px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    }
    
    .setting-checkbox:focus + .setting-checkbox-label {
        outline: 2px solid rgba(96, 165, 250, 0.5);
        outline-offset: 2px;
    }
    
    .setting-checkbox-label:hover {
        background-color: #d1d5db;
    }
    
    .setting-checkbox:checked + .setting-checkbox-label:hover {
        background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
    }
    
    
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border-width: 0;
    }
</style>
@endpush
@endsection

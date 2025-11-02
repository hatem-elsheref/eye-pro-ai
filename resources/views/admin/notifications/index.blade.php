@extends('admin.layouts.master')

@section('title', 'Notifications - Eye Pro')
@section('page-title', 'Notifications')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Notifications Header -->
    <div class="relative overflow-hidden rounded-2xl p-6 shadow-lg border border-blue-200" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-white opacity-10"></div>
        <div class="absolute bottom-0 left-0 -mb-6 -ml-6 h-32 w-32 rounded-full bg-white opacity-10"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white mb-1">Notifications</h1>
                <p class="text-sm text-blue-50 font-medium">Stay updated with your account activity</p>
            </div>
            @if(isset($unreadCount) && $unreadCount > 0)
            <div class="hidden md:flex items-center space-x-2 px-4 py-2 rounded-lg bg-red-500/30 backdrop-blur-sm border border-red-300/50">
                <span class="text-white font-bold text-sm">{{ $unreadCount }} Unread</span>
                <i class="fas fa-circle text-red-200 text-xs"></i>
            </div>
            @else
            <div class="hidden md:flex items-center space-x-2 px-4 py-2 rounded-lg bg-white/20 backdrop-blur-sm border border-white/30">
                <span class="text-white font-bold text-sm">All Read</span>
                <i class="fas fa-check-double text-white text-sm"></i>
            </div>
            @endif
        </div>
    </div>
    
    @if(isset($notifications) && count($notifications) > 0)
    <!-- Notifications List -->
    <div class="bg-white rounded-3xl shadow-xl border-2 border-gray-100 overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-teal-50 to-blue-50 border-b-2 border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold text-gray-900">Recent Activity</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ count($notifications) }} notification(s)
                        @if(isset($unreadCount) && $unreadCount > 0)
                            <span class="ml-2 px-2 py-1 bg-red-100 text-red-700 rounded-lg font-bold text-xs">{{ $unreadCount }} unread</span>
                        @endif
                    </p>
                </div>
                @if(isset($unreadCount) && $unreadCount > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-xl font-bold text-sm hover:bg-blue-200 transition-colors">
                        <i class="fas fa-check-double mr-1"></i>
                        Mark All Read
                    </button>
                </form>
                @endif
            </div>
        </div>
        
        <div class="divide-y-2 divide-gray-100">
            @foreach($notifications as $notification)
            <div class="p-6 hover:bg-blue-50 transition-all duration-200 {{ $notification->read_at ? 'opacity-60' : '' }}">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        @php
                            $notifType = $notification->data['type'] ?? $notification->type;
                        @endphp
                        @if($notifType === 'match_upload_processing')
                        <div class="h-14 w-14 rounded-2xl bg-green-100 flex items-center justify-center shadow-lg">
                            <i class="fas fa-upload text-green-600 text-2xl"></i>
                        </div>
                        @elseif($notifType === 'match_analysis_complete')
                        <div class="h-14 w-14 rounded-2xl bg-blue-100 flex items-center justify-center shadow-lg">
                            <i class="fas fa-check-circle text-blue-600 text-2xl"></i>
                        </div>
                        @elseif($notifType === 'match_processing_failed')
                        <div class="h-14 w-14 rounded-2xl bg-red-100 flex items-center justify-center shadow-lg">
                            <i class="fas fa-exclamation-circle text-red-600 text-2xl"></i>
                        </div>
                        @elseif($notifType === 'match_processing_ended_no_predictions')
                        <div class="h-14 w-14 rounded-2xl bg-yellow-100 flex items-center justify-center shadow-lg">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                        </div>
                        @elseif($notifType === 'match_processing_stopped')
                        <div class="h-14 w-14 rounded-2xl bg-blue-100 flex items-center justify-center shadow-lg">
                            <i class="fas fa-stop-circle text-blue-600 text-2xl"></i>
                        </div>
                        @elseif($notifType === 'match_processing_stopped_failed')
                        <div class="h-14 w-14 rounded-2xl bg-red-100 flex items-center justify-center shadow-lg">
                            <i class="fas fa-exclamation-circle text-red-600 text-2xl"></i>
                        </div>
                        @elseif($notifType === 'match_processing_started')
                        <div class="h-14 w-14 rounded-2xl bg-blue-100 flex items-center justify-center shadow-lg">
                            <i class="fas fa-play-circle text-blue-600 text-2xl"></i>
                        </div>
                        @elseif($notifType === 'account_approved')
                        <div class="h-14 w-14 rounded-2xl bg-blue-100 flex items-center justify-center shadow-lg">
                            <i class="fas fa-user-check text-blue-600 text-2xl"></i>
                        </div>
                        @elseif($notifType === 'account_rejected')
                        <div class="h-14 w-14 rounded-2xl bg-red-100 flex items-center justify-center shadow-lg">
                            <i class="fas fa-user-times text-red-600 text-2xl"></i>
                        </div>
                        @elseif($notifType === 'match_uploaded')
                        <div class="h-14 w-14 rounded-2xl bg-blue-100 flex items-center justify-center shadow-lg">
                            <i class="fas fa-upload text-blue-600 text-2xl"></i>
                        </div>
                        @else
                        <div class="h-14 w-14 rounded-2xl bg-blue-100 flex items-center justify-center shadow-lg">
                            <i class="fas fa-bell text-blue-600 text-2xl"></i>
                        </div>
                        @endif
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-bold text-gray-900 mb-1">{{ $notification->data['title'] ?? 'Notification' }}</h3>
                        <p class="text-sm text-gray-600 mb-2">{{ $notification->data['message'] ?? '' }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400 flex items-center space-x-1">
                                <i class="fas fa-clock"></i>
                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                            </span>
                            @if(!$notification->read_at)
                            <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs font-bold text-blue-600 hover:text-blue-700 px-3 py-1 rounded-lg hover:bg-blue-100 transition-all">
                                    Mark as Read
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="bg-white rounded-3xl shadow-xl p-16 text-center border-2 border-gray-100">
        <div class="mx-auto h-28 w-28 rounded-full bg-gradient-to-br from-teal-100 to-blue-200 flex items-center justify-center mb-6">
            <i class="fas fa-bell-slash text-5xl text-blue-600"></i>
        </div>
        <h3 class="text-3xl font-extrabold text-gray-900 mb-3">No notifications</h3>
        <p class="text-lg text-gray-500">You're all caught up! 🎉</p>
    </div>
    @endif
</div>
@endsection

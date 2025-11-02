<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Eye Pro - Match Analysis Platform')</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        cairo: ['Cairo', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="h-full bg-gray-50 font-cairo @yield('body-class', '')" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="hidden lg:flex lg:flex-shrink-0">
            <div class="flex w-64 flex-col">
                <div class="flex min-h-0 flex-1 flex-col bg-gray-900">
                    <!-- Logo -->
                    <div class="flex h-16 flex-shrink-0 items-center px-6 bg-black">
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                            <img src="{{ asset('logo.png') }}" alt="Eye Pro" class="h-24 w-32 rounded-lg shadow-lg group-hover:scale-110 transition-transform duration-300 object-contain bg-transparent">
                            <span class="text-xl font-bold text-white"></span>
                        </a>
                    </div>

                    <!-- Navigation -->
                    <nav class="flex-1 space-y-1 px-3 py-6 overflow-y-auto">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Main Menu</p>

                        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-th-large text-lg"></i>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('matches.index') }}" class="sidebar-link {{ request()->routeIs('matches.index') || (request()->routeIs('matches.*') && !request()->routeIs('matches.create')) ? 'active' : '' }}">
                            <i class="fas fa-video text-lg"></i>
                            <span>Matches</span>
                        </a>

                        <a href="{{ route('matches.create') }}" class="sidebar-link {{ request()->routeIs('matches.create') ? 'active' : '' }}">
                            <i class="fas fa-plus-circle text-lg"></i>
                            <span>Add Match</span>
                        </a>

                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 mt-6">Support</p>

                        <a href="{{ route('notifications.index') }}" class="sidebar-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                            <i class="fas fa-bell text-lg"></i>
                            <span>Notifications</span>
                        </a>

                        <a href="{{ route('support') }}" class="sidebar-link {{ request()->routeIs('support') ? 'active' : '' }}">
                            <i class="fas fa-life-ring text-lg"></i>
                            <span>User Guide</span>
                        </a>

                        @if(Auth::user()->is_admin ?? false)
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 mt-6">Admin</p>

                        <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="fas fa-users text-lg"></i>
                            <span>Users</span>
                        </a>

                        <a href="{{ route('tickets.index') }}" class="sidebar-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
                            <i class="fas fa-ticket-alt text-lg"></i>
                            <span>All Tickets</span>
                        </a>

                        <a href="{{ route('admin.index') }}" class="sidebar-link {{ request()->routeIs('admin.index') && !request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="fas fa-cog text-lg"></i>
                            <span>Settings</span>
                        </a>
                        @endif
                    </nav>

                    <!-- User Profile -->
                    <div class="flex flex-shrink-0 border-t border-gray-800 p-4">
                        <a href="{{ route('profile') }}" class="group flex w-full items-center">
                            <div class="flex items-center space-x-3 flex-1">
                                @php
                                    $userId = Auth::user()->id ?? 0;
                                    $avatarColors = [
                                        ['#60a5fa', '#818cf8'], ['#f59e0b', '#ea580c'], ['#10b981', '#059669'],
                                        ['#ef4444', '#dc2626'], ['#8b5cf6', '#7c3aed'], ['#ec4899', '#db2777'],
                                        ['#06b6d4', '#0891b2'], ['#f97316', '#ea580c'], ['#14b8a6', '#0d9488'],
                                        ['#6366f1', '#4f46e5'], ['#d946ef', '#c026d3'], ['#3b82f6', '#2563eb'],
                                        ['#84cc16', '#65a30d'], ['#a855f7', '#9333ea']
                                    ];
                                    $colorIndex = abs($userId) % count($avatarColors);
                                    $userColors = $avatarColors[$colorIndex];
                                @endphp
                                <div class="flex h-10 w-10 items-center justify-center rounded-full text-white font-bold group-hover:scale-110 transition-transform duration-300 shadow-lg" style="background: linear-gradient(135deg, {{ $userColors[0] }} 0%, {{ $userColors[1] }} 100%);">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name ?? 'User' }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email ?? '' }}</p>
                                </div>
                            </div>
                            <form action="{{ route('logout') }}" method="POST" class="ml-2">
                                @csrf
                                <button type="submit" class="text-gray-400 hover:text-red-400 transition-colors">
                                    <i class="fas fa-sign-out-alt"></i>
                                </button>
                            </form>
                        </a>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Mobile sidebar -->
        <div x-show="sidebarOpen"
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
             style="display: none;"></div>

        <aside x-show="sidebarOpen"
               x-transition:enter="transition ease-in-out duration-300 transform"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in-out duration-300 transform"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 lg:hidden"
               style="display: none;">
            <!-- Mobile sidebar content (same as desktop) -->
            <div class="flex h-16 flex-shrink-0 items-center px-6 bg-black justify-between">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                    <img src="{{ asset('logo.jpeg') }}" alt="Eye Pro" class="h-12 w-12 rounded-lg shadow-lg object-contain bg-transparent">
                    <span class="text-xl font-bold text-white">Eye Pro</span>
                </a>
                <button @click="sidebarOpen = false" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <nav class="flex-1 space-y-1 px-3 py-6 overflow-y-auto">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Main Menu</p>

                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-th-large text-lg"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('matches.index') }}" class="sidebar-link {{ request()->routeIs('matches.index') || (request()->routeIs('matches.*') && !request()->routeIs('matches.create')) ? 'active' : '' }}">
                    <i class="fas fa-video text-lg"></i>
                    <span>Matches</span>
                </a>

                <a href="{{ route('matches.create') }}" class="sidebar-link {{ request()->routeIs('matches.create') ? 'active' : '' }}">
                    <i class="fas fa-plus-circle text-lg"></i>
                    <span>Add Match</span>
                </a>

                @if(Auth::user()->is_admin ?? false)
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 mt-6">Admin</p>

                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users text-lg"></i>
                    <span>Users</span>
                </a>

                <a href="{{ route('tickets.index') }}" class="sidebar-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
                    <i class="fas fa-ticket-alt text-lg"></i>
                    <span>All Tickets</span>
                </a>

                <a href="{{ route('admin.index') }}" class="sidebar-link {{ request()->routeIs('admin.index') && !request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-cog text-lg"></i>
                    <span>Settings</span>
                </a>
                @endif
            </nav>
        </aside>

        <!-- Main content -->
        <div class="flex flex-1 flex-col overflow-hidden">
            <!-- Top bar -->
            <div class="relative flex h-16 flex-shrink-0 bg-white shadow-sm" style="z-index: 50;">
                <button @click="sidebarOpen = true" class="px-4 text-gray-500 focus:outline-none lg:hidden">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <div class="flex flex-1 justify-between px-6">
                    <div class="flex flex-1 items-center">
                        <h2 class="text-2xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h2>
                    </div>
                    <div class="ml-4 flex items-center space-x-4">
                        <!-- Notifications Dropdown -->
                        @php
                            $user = auth()->user();
                            $notifications = $user->unreadNotifications->take(5);
                            $unreadCount = $user->unreadNotifications->count();
                        @endphp
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="relative p-3 rounded-xl transition-all duration-300 hover:bg-blue-50 group">
                                <i class="fas fa-bell text-2xl text-gray-600 group-hover:text-blue-600 transition-colors duration-300"></i>
                                @if($unreadCount > 0)
                                <span class="notification-badge absolute top-1 right-1 flex h-5 w-5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-500 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-5 w-5 text-white text-[10px] items-center justify-center font-bold shadow-lg" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                                </span>
                                @else
                                <span class="notification-badge absolute top-1 right-1 flex h-5 w-5" style="display: none;">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-500 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-5 w-5 text-white text-[10px] items-center justify-center font-bold shadow-lg" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">0</span>
                                </span>
                                @endif
                            </button>

                            <!-- Notifications Dropdown -->
                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-3 w-96 rounded-2xl bg-white shadow-2xl border-2 border-gray-100 overflow-hidden"
                                 style="display: none; z-index: 9999;">
                                <div class="p-5 border-b-2 border-blue-100 bg-gradient-to-r from-blue-50 to-purple-50">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="h-10 w-10 rounded-xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                                                <i class="fas fa-bell text-white text-lg"></i>
                                            </div>
                                            <h3 class="text-xl font-extrabold text-gray-900">Notifications</h3>
                                        </div>
                                        <span id="notificationHeaderBadge" class="relative inline-flex items-center px-4 py-2 rounded-xl font-extrabold text-sm text-white shadow-xl animate-pulse" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);{{ $unreadCount > 0 ? '' : 'display: none;' }}">
                                            <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-400"></span>
                                            </span>
                                            <span id="notificationHeaderCount">{{ $unreadCount }}</span> New
                                        </span>
                                    </div>
                                </div>
                                <div id="notificationsList" class="max-h-96 overflow-y-auto">
                                    @forelse($notifications as $notification)
                                        @php
                                            $notifType = $notification->data['type'] ?? $notification->type;
                                            $title = $notification->data['title'] ?? 'Notification';
                                            $message = $notification->data['message'] ?? '';
                                        @endphp
                                        <a href="{{ route('notifications.index') }}" class="flex items-start space-x-4 p-5 hover:bg-blue-50 transition-all duration-200 {{ !$loop->last ? 'border-b border-gray-100' : '' }} group {{ $notification->read_at ? 'opacity-60' : '' }}">
                                            <div class="flex-shrink-0">
                                                @if($notifType === 'account_approved')
                                                <div class="h-12 w-12 rounded-xl bg-blue-100 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                                    <i class="fas fa-user-check text-blue-600 text-xl"></i>
                                                </div>
                                                @elseif($notifType === 'account_rejected')
                                                <div class="h-12 w-12 rounded-xl bg-red-100 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                                    <i class="fas fa-user-times text-red-600 text-xl"></i>
                                                </div>
                                                @elseif($notifType === 'match_analysis_complete')
                                                <div class="h-12 w-12 rounded-xl bg-green-100 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                                                </div>
                                                @elseif($notifType === 'match_upload_processing')
                                                <div class="h-12 w-12 rounded-xl bg-blue-100 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                                    <i class="fas fa-upload text-blue-600 text-xl"></i>
                                                </div>
                                                @elseif($notifType === 'match_processing_failed')
                                                <div class="h-12 w-12 rounded-xl bg-red-100 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                                    <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                                                </div>
                                                @elseif($notifType === 'match_processing_ended_no_predictions')
                                                <div class="h-12 w-12 rounded-xl bg-yellow-100 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                                                </div>
                                                @elseif($notifType === 'match_processing_stopped')
                                                <div class="h-12 w-12 rounded-xl bg-blue-100 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                                    <i class="fas fa-stop-circle text-blue-600 text-xl"></i>
                                                </div>
                                                @elseif($notifType === 'match_processing_stopped_failed')
                                                <div class="h-12 w-12 rounded-xl bg-red-100 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                                    <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                                                </div>
                                                @elseif($notifType === 'match_processing_started')
                                                <div class="h-12 w-12 rounded-xl bg-blue-100 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                                    <i class="fas fa-play-circle text-blue-600 text-xl"></i>
                                                </div>
                                                @else
                                                <div class="h-12 w-12 rounded-xl bg-gray-100 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                                    <i class="fas fa-bell text-gray-600 text-xl"></i>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-bold text-gray-900">{{ $title }}</p>
                                                <p class="text-xs text-gray-600 mt-1">{{ $message }}</p>
                                                <p class="text-xs text-gray-400 mt-2 flex items-center space-x-1">
                                                    <i class="fas fa-clock"></i>
                                                    <span>{{ $notification->created_at->diffForHumans() }}</span>
                                                </p>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="p-8 text-center">
                                            <i class="fas fa-bell-slash text-4xl text-gray-300 mb-3"></i>
                                            <p class="text-sm text-gray-500">No new notifications</p>
                                        </div>
                                    @endforelse
                                </div>
                                <div class="p-4 border-t-2 border-gray-100 bg-gray-50">
                                    <a href="{{ route('notifications.index') }}" class="flex items-center justify-center space-x-2 text-sm font-bold text-blue-600 hover:text-blue-700 py-2 transition-colors">
                                        <span>View All Notifications</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- User Menu Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center space-x-3 px-3 py-2 rounded-xl hover:bg-blue-50 transition-all duration-300 group border-2 border-transparent hover:border-blue-200">
                                @php
                                    $name = Auth::user()->name ?? 'User';
                                    $nameParts = explode(' ', $name);
                                    $initials = count($nameParts) >= 2
                                        ? strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1))
                                        : strtoupper(substr($name, 0, 2));
                                    $userId = Auth::user()->id ?? 0;
                                    $avatarColors = [
                                        ['#60a5fa', '#818cf8'], ['#f59e0b', '#ea580c'], ['#10b981', '#059669'],
                                        ['#ef4444', '#dc2626'], ['#8b5cf6', '#7c3aed'], ['#ec4899', '#db2777'],
                                        ['#06b6d4', '#0891b2'], ['#f97316', '#ea580c'], ['#14b8a6', '#0d9488'],
                                        ['#6366f1', '#4f46e5'], ['#d946ef', '#c026d3'], ['#3b82f6', '#2563eb'],
                                        ['#84cc16', '#65a30d'], ['#a855f7', '#9333ea']
                                    ];
                                    $colorIndex = abs($userId) % count($avatarColors);
                                    $userColors = $avatarColors[$colorIndex];
                                @endphp

                                <div class="h-11 w-11 rounded-xl flex items-center justify-center text-white font-extrabold text-base shadow-lg group-hover:shadow-xl group-hover:scale-105 transition-all duration-300" style="background: linear-gradient(135deg, {{ $userColors[0] }} 0%, {{ $userColors[1] }} 100%);">
                                    {{ $initials }}
                                </div>
                                <div class="hidden lg:block text-left">
                                    <p class="text-sm font-bold text-gray-900 group-hover:text-blue-700 transition-colors">{{ Auth::user()->name ?? 'User' }}</p>
                                    <p class="text-xs text-gray-500">{{ Auth::user()->email ?? '' }}</p>
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-600 text-sm transition-all duration-300" :class="{ 'rotate-180': open }"></i>
                            </button>

                            <!-- User Dropdown Menu -->
                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-3 w-72 rounded-2xl bg-white shadow-2xl border-2 border-gray-100 overflow-hidden"
                                 style="display: none; z-index: 9999;">
                                <!-- User Info Header -->
                                <div class="p-5 border-b-2 border-gray-100 bg-gradient-to-r from-blue-50 to-purple-50">
                                    <div class="flex items-center space-x-3">
                                        <div class="h-14 w-14 rounded-xl flex items-center justify-center text-white font-extrabold text-lg shadow-xl" style="background: linear-gradient(135deg, {{ $userColors[0] }} 0%, {{ $userColors[1] }} 100%);">
                                            {{ $initials }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-base font-extrabold text-gray-900 truncate">{{ Auth::user()->name ?? 'User' }}</p>
                                            <p class="text-xs text-gray-600 truncate font-medium">{{ Auth::user()->email ?? '' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Menu Items -->
                                <div class="p-3">
                                    <a href="{{ route('profile') }}" class="flex items-center space-x-3 px-4 py-3.5 rounded-xl hover:bg-blue-50 text-gray-700 hover:text-blue-700 transition-all duration-200 group">
                                        <div class="h-10 w-10 rounded-xl bg-blue-100 flex items-center justify-center group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                                            <i class="fas fa-user text-blue-600 text-lg"></i>
                                        </div>
                                        <span class="font-bold">My Profile</span>
                                    </a>
                                    <a href="{{ route('support') }}" class="flex items-center space-x-3 px-4 py-3.5 rounded-xl hover:bg-blue-50 text-gray-700 hover:text-blue-700 transition-all duration-200 group">
                                        <div class="h-10 w-10 rounded-xl bg-blue-100 flex items-center justify-center group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                                            <i class="fas fa-life-ring text-blue-600 text-lg"></i>
                                        </div>
                                        <span class="font-bold">Support</span>
                                    </a>
                                    @if(Auth::user()->is_admin ?? false)
                                    <a href="{{ route('admin.index') }}" class="flex items-center space-x-3 px-4 py-3.5 rounded-xl hover:bg-blue-50 text-gray-700 hover:text-blue-700 transition-all duration-200 group">
                                        <div class="h-10 w-10 rounded-xl bg-blue-100 flex items-center justify-center group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                                            <i class="fas fa-cog text-blue-600 text-lg"></i>
                                        </div>
                                        <span class="font-bold">Admin Panel</span>
                                    </a>
                                    @endif
                                </div>

                                <!-- Logout -->
                                <div class="p-3 border-t-2 border-gray-100">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="flex items-center space-x-3 px-4 py-3.5 rounded-xl hover:bg-red-50 text-gray-700 hover:text-red-700 transition-all duration-200 w-full group">
                                            <div class="h-10 w-10 rounded-xl bg-red-100 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                                <i class="fas fa-sign-out-alt text-red-600 text-lg"></i>
                                            </div>
                                            <span class="font-bold">Sign Out</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
                @include('admin._shared._alerts')

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')

    <style>
        /* Sidebar Links - No Animation */
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            color: #d1d5db;
            position: relative;
            overflow: hidden;
        }

        .sidebar-link:hover {
            background: #374151;
            color: white;
        }

        .sidebar-link.active {
            background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);
            color: white;
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.4);
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: white;
            border-radius: 0 4px 4px 0;
        }

        .sidebar-link i {
            width: 20px;
            text-align: center;
        }

        /* Custom Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Toast Notification Styles */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .toast {
            min-width: 320px;
            max-width: 420px;
            padding: 16px 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 16px;
            animation: slideInRight 0.3s ease-out;
            border-left: 4px solid #60a5fa;
        }

        .toast.success {
            border-left-color: #10b981;
        }

        .toast.error {
            border-left-color: #ef4444;
        }

        .toast.warning {
            border-left-color: #f59e0b;
        }

        .toast.info {
            border-left-color: #60a5fa;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .toast-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 700;
            font-size: 14px;
            color: #111827;
            margin-bottom: 4px;
        }

        .toast-message {
            font-size: 12px;
            color: #6b7280;
        }
    </style>

    <!-- Toast Container -->
    <div id="toastContainer" class="toast-container"></div>

    <!-- WebSocket Notification Handler -->
    <script>
        (function() {
            @auth
            const userId = {{ auth()->id() }};
            const wsProtocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
            const wsHost = '{{ env("WEBSOCKET_HOST", "localhost:3001") }}';
            const wsUrl = `${wsProtocol}//${wsHost}/ws`;
            
            let notificationWs = null;
            let reconnectAttempts = 0;
            const maxReconnectAttempts = 10;
            let reconnectTimeout = null;

            // Create audio context for notification sound
            let audioContext = null;
            try {
                audioContext = new (window.AudioContext || window.webkitAudioContext)();
            } catch (e) {
                console.warn('Audio context not supported:', e);
            }

            // Function to play notification sound
            function playNotificationSound() {
                if (!audioContext) return;

                try {
                    const oscillator = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();

                    oscillator.connect(gainNode);
                    gainNode.connect(audioContext.destination);

                    // Pleasant notification sound (two-tone chime)
                    oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
                    oscillator.frequency.setValueAtTime(1000, audioContext.currentTime + 0.1);

                    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);

                    oscillator.start(audioContext.currentTime);
                    oscillator.stop(audioContext.currentTime + 0.3);
                } catch (e) {
                    console.warn('Could not play sound:', e);
                }
            }

            // Function to show toast notification
            function showToast(notification) {
                const container = document.getElementById('toastContainer');
                if (!container) return;

                const title = notification.title || 'Notification';
                const message = notification.message || '';
                const type = notification.type || 'info';

                // Determine icon and color based on type
                let iconClass = 'fas fa-bell';
                let bgClass = 'bg-blue-100';
                let textClass = 'text-blue-600';
                let toastClass = 'info';

                if (type === 'account_approved') {
                    iconClass = 'fas fa-user-check';
                    bgClass = 'bg-green-100';
                    textClass = 'text-green-600';
                    toastClass = 'success';
                } else if (type === 'account_rejected') {
                    iconClass = 'fas fa-user-times';
                    bgClass = 'bg-red-100';
                    textClass = 'text-red-600';
                    toastClass = 'error';
                } else if (type === 'match_analysis_complete') {
                    iconClass = 'fas fa-check-circle';
                    bgClass = 'bg-green-100';
                    textClass = 'text-green-600';
                    toastClass = 'success';
                } else if (type === 'match_upload_processing') {
                    iconClass = 'fas fa-upload';
                    bgClass = 'bg-blue-100';
                    textClass = 'text-blue-600';
                    toastClass = 'info';
                } else if (type === 'match_processing_failed') {
                    iconClass = 'fas fa-exclamation-circle';
                    bgClass = 'bg-red-100';
                    textClass = 'text-red-600';
                    toastClass = 'error';
                } else if (type === 'match_processing_ended_no_predictions') {
                    iconClass = 'fas fa-exclamation-triangle';
                    bgClass = 'bg-yellow-100';
                    textClass = 'text-yellow-600';
                    toastClass = 'warning';
                } else if (type === 'match_processing_stopped') {
                    iconClass = 'fas fa-stop-circle';
                    bgClass = 'bg-blue-100';
                    textClass = 'text-blue-600';
                    toastClass = 'info';
                } else if (type === 'match_processing_stopped_failed') {
                    iconClass = 'fas fa-exclamation-circle';
                    bgClass = 'bg-red-100';
                    textClass = 'text-red-600';
                    toastClass = 'error';
                } else if (type === 'match_processing_started') {
                    iconClass = 'fas fa-play-circle';
                    bgClass = 'bg-blue-100';
                    textClass = 'text-blue-600';
                    toastClass = 'info';
                }

                const toast = document.createElement('div');
                toast.className = `toast ${toastClass}`;
                toast.innerHTML = `
                    <div class="toast-icon ${bgClass}">
                        <i class="${iconClass} ${textClass}"></i>
                    </div>
                    <div class="toast-content">
                        <div class="toast-title">${title}</div>
                        <div class="toast-message">${message}</div>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                `;

                container.appendChild(toast);

                // Auto-remove after 5 seconds
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.style.animation = 'slideInRight 0.3s ease-out reverse';
                        setTimeout(() => toast.remove(), 300);
                    }
                }, 5000);
            }

            // Helper function to escape HTML
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Function to refresh notification list
            function refreshNotifications() {
                // Fetch updated notification list and count via AJAX
                fetch('/notifications/list', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const count = data.unreadCount || 0;
                    const notifications = data.notifications || [];
                    
                    // Update notification badge count on button
                    const badge = document.querySelector('.notification-badge');
                    if (badge) {
                        const countSpan = badge.querySelector('span:last-child');
                        if (countSpan) {
                            countSpan.textContent = count > 99 ? '99+' : count;
                        }
                        if (count > 0) {
                            badge.style.display = 'flex';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                    
                    // Update header badge count
                    const headerBadge = document.getElementById('notificationHeaderBadge');
                    const headerCount = document.getElementById('notificationHeaderCount');
                    if (headerBadge && headerCount) {
                        headerCount.textContent = count;
                        if (count > 0) {
                            headerBadge.style.display = 'inline-flex';
                        } else {
                            headerBadge.style.display = 'none';
                        }
                    }
                    
                    // Update notification list in dropdown
                    const listContainer = document.getElementById('notificationsList');
                    if (listContainer) {
                        if (notifications.length === 0) {
                            listContainer.innerHTML = `
                                <div class="p-8 text-center">
                                    <i class="fas fa-bell-slash text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-sm text-gray-500">No new notifications</p>
                                </div>
                            `;
                        } else {
                            listContainer.innerHTML = notifications.map((notif, index) => `
                                <a href="/notifications" class="flex items-start space-x-4 p-5 hover:bg-blue-50 transition-all duration-200 ${index < notifications.length - 1 ? 'border-b border-gray-100' : ''} group ${notif.read_at ? 'opacity-60' : ''}">
                                    <div class="flex-shrink-0">
                                        <div class="h-12 w-12 rounded-xl ${notif.iconBg} flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                            <i class="fas ${notif.icon} ${notif.iconColor} text-xl"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-900">${escapeHtml(notif.title)}</p>
                                        <p class="text-xs text-gray-600 mt-1">${escapeHtml(notif.message)}</p>
                                        <p class="text-xs text-gray-400 mt-2 flex items-center space-x-1">
                                            <i class="fas fa-clock"></i>
                                            <span>${escapeHtml(notif.created_at)}</span>
                                        </p>
                                    </div>
                                </a>
                            `).join('');
                        }
                    }
                    
                    // Dispatch event for other components to react
                    window.dispatchEvent(new CustomEvent('notifications-updated', { 
                        detail: { count: count, notifications: notifications } 
                    }));
                    
                    // If on notifications page, reload to show new notification
                    if (window.location.pathname === '/notifications' || window.location.pathname.includes('/notifications')) {
                        // Small delay to allow toast to be visible
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                })
                .catch(error => {
                    console.error('Error refreshing notifications:', error);
                    // Fallback: reload page if AJAX fails
                    if (window.location.pathname === '/notifications' || window.location.pathname.includes('/notifications')) {
                        setTimeout(() => window.location.reload(), 1000);
                    }
                });
            }

            // Function to connect to WebSocket
            function connectWebSocket() {
                if (notificationWs && notificationWs.readyState === WebSocket.OPEN) {
                    return; // Already connected
                }

                try {
                    notificationWs = new WebSocket(wsUrl);

                    notificationWs.onopen = function() {
                        console.log('Notification WebSocket connected');
                        reconnectAttempts = 0;

                        // Subscribe to notifications channel
                        notificationWs.send(JSON.stringify({
                            type: 'subscribe',
                            userId: userId,
                            subscribeType: 'notifications'
                        }));
                    };

                    notificationWs.onmessage = function(event) {
                        try {
                            const data = JSON.parse(event.data);

                            if (data.type === 'subscribed') {
                                console.log('Subscribed to notifications channel:', data.channel);
                            } else if (data.type === 'notification') {
                                console.log('Received notification:', data.notification);

                                // Play sound
                                playNotificationSound();

                                // Show toast
                                showToast(data.notification);

                                // Refresh notification list
                                refreshNotifications();
                            }
                        } catch (error) {
                            console.error('Error parsing WebSocket message:', error);
                        }
                    };

                    notificationWs.onerror = function(error) {
                        console.error('Notification WebSocket error:', error);
                    };

                    notificationWs.onclose = function(event) {
                        console.log('Notification WebSocket closed');
                        notificationWs = null;

                        // Reconnect with exponential backoff
                        if (reconnectAttempts < maxReconnectAttempts) {
                            const delay = Math.min(1000 * Math.pow(2, reconnectAttempts), 30000);
                            reconnectAttempts++;
                            console.log(`Reconnecting in ${delay}ms (attempt ${reconnectAttempts}/${maxReconnectAttempts})`);
                            
                            reconnectTimeout = setTimeout(() => {
                                connectWebSocket();
                            }, delay);
                        } else {
                            console.warn('Max reconnection attempts reached');
                        }
                    };

                } catch (error) {
                    console.error('Error connecting to WebSocket:', error);
                    
                    // Retry connection
                    if (reconnectAttempts < maxReconnectAttempts) {
                        const delay = Math.min(1000 * Math.pow(2, reconnectAttempts), 30000);
                        reconnectAttempts++;
                        reconnectTimeout = setTimeout(() => {
                            connectWebSocket();
                        }, delay);
                    }
                }
            }

            // Connect when page loads
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', connectWebSocket);
            } else {
                connectWebSocket();
            }

            // Reconnect when page becomes visible (user switched tabs back)
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden && (!notificationWs || notificationWs.readyState !== WebSocket.OPEN)) {
                    clearTimeout(reconnectTimeout);
                    connectWebSocket();
                }
            });

            // Cleanup on page unload
            window.addEventListener('beforeunload', function() {
                if (reconnectTimeout) {
                    clearTimeout(reconnectTimeout);
                }
                if (notificationWs) {
                    notificationWs.close();
                }
            });
            @endauth
        })();
    </script>
</body>
</html>

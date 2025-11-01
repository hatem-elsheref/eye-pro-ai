<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="relative p-3 rounded-xl transition-all duration-300 hover:bg-blue-50 group">
                                <i class="fas fa-bell text-2xl text-gray-600 group-hover:text-blue-600 transition-colors duration-300"></i>
                                <span class="absolute top-1 right-1 flex h-5 w-5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-500 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-5 w-5 text-white text-[10px] items-center justify-center font-bold shadow-lg" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">3</span>
                                </span>
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
                                        <span class="relative inline-flex items-center px-4 py-2 rounded-xl font-extrabold text-sm text-white shadow-xl animate-pulse" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                                            <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-400"></span>
                                            </span>
                                            3 New
                                        </span>
                                    </div>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    <a href="#" class="flex items-start space-x-4 p-5 hover:bg-blue-50 transition-all duration-200 border-b border-gray-100 group">
                                        <div class="flex-shrink-0">
                                            <div class="h-12 w-12 rounded-xl bg-green-100 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-gray-900">Match analysis completed</p>
                                            <p class="text-xs text-gray-600 mt-1">Your match "Championship Final" has been analyzed</p>
                                            <p class="text-xs text-gray-400 mt-2 flex items-center space-x-1">
                                                <i class="fas fa-clock"></i>
                                                <span>2 hours ago</span>
                                            </p>
                                        </div>
                                    </a>
                                    <a href="#" class="flex items-start space-x-4 p-5 hover:bg-blue-50 transition-all duration-200 border-b border-gray-100 group">
                                        <div class="flex-shrink-0">
                                            <div class="h-12 w-12 rounded-xl bg-blue-100 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                                <i class="fas fa-upload text-blue-600 text-xl"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-gray-900">Upload successful</p>
                                            <p class="text-xs text-gray-600 mt-1">Your video has been uploaded successfully</p>
                                            <p class="text-xs text-gray-400 mt-2 flex items-center space-x-1">
                                                <i class="fas fa-clock"></i>
                                                <span>5 hours ago</span>
                                            </p>
                                        </div>
                                    </a>
                                    <a href="#" class="flex items-start space-x-4 p-5 hover:bg-blue-50 transition-all duration-200 group">
                                        <div class="flex-shrink-0">
                                            <div class="h-12 w-12 rounded-xl bg-blue-100 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                                <i class="fas fa-user-check text-blue-600 text-xl"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-gray-900">Account approved</p>
                                            <p class="text-xs text-gray-600 mt-1">Your account has been approved by admin</p>
                                            <p class="text-xs text-gray-400 mt-2 flex items-center space-x-1">
                                                <i class="fas fa-clock"></i>
                                                <span>1 day ago</span>
                                            </p>
                                        </div>
                                    </a>
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
    </style>
</body>
</html>

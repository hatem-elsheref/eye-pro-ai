@extends('admin.layouts.master')

@section('title', 'Dashboard - Eye Pro')
@section('page-title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 animate-fade-in">
    
    <!-- Welcome Section with Add Match Button -->
    <div class="relative overflow-hidden rounded-2xl p-6 shadow-lg border border-blue-200" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full opacity-10" style="background: rgba(255,255,255,0.5);"></div>
        <div class="absolute bottom-0 left-0 -mb-6 -ml-6 h-32 w-32 rounded-full opacity-10" style="background: rgba(255,255,255,0.5);"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white mb-1">Welcome back, {{ Auth::user()->name }}! 👋</h1>
                <p class="text-sm text-white/90 font-medium">Here's an overview of your match analysis account</p>
            </div>
            <a href="{{ route('matches.create') }}" class="hidden lg:flex items-center space-x-2 px-6 py-3 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3);">
                <i class="fas fa-plus text-base text-white"></i>
                <span class="text-white">Add Match</span>
            </a>
        </div>
    </div>
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Matches Card -->
        <div class="group bg-white rounded-2xl p-5 shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border-2 border-gray-100 hover:border-blue-200">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center justify-center h-14 w-14 rounded-xl shadow-xl group-hover:shadow-2xl group-hover:scale-110 transition-all duration-300" style="background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);">
                    <i class="fas fa-video text-2xl text-white"></i>
                </div>
                <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-full border border-green-200">↑ 12%</span>
            </div>
            <h3 class="text-gray-500 text-xs font-bold mb-1 uppercase tracking-wide">Total Matches</h3>
            <p class="text-3xl font-extrabold text-gray-900">{{ $totalMatches ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">Your uploaded matches</p>
        </div>
        
        <!-- Upload Status Card -->
        <div class="group rounded-2xl p-5 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-3 border-2 border-indigo-300" style="background: linear-gradient(135deg, #818cf8 0%, #6366f1 100%); box-shadow: 0 10px 30px rgba(129, 140, 248, 0.3);">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center justify-center h-12 w-12 rounded-xl bg-white/20 backdrop-blur-sm border border-white/30">
                    <i class="fas fa-cloud-upload-alt text-xl text-white"></i>
                </div>
                <span class="flex h-5 w-5">
                    <span class="animate-ping absolute h-5 w-5 rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative h-5 w-5 rounded-full bg-green-400 border-2 border-white shadow-lg"></span>
                </span>
            </div>
            <h3 class="text-white/90 text-xs font-bold mb-1 uppercase tracking-wide">Upload Status</h3>
            <p class="text-3xl font-extrabold text-white">Ready</p>
            <p class="text-xs text-white/90 mt-1">System operational</p>
        </div>
        
        <!-- Processing Card -->
        <div class="group bg-white rounded-2xl p-5 shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border-2 border-gray-100 hover:border-amber-200">
            <div class="mb-3">
                <div class="flex items-center justify-center h-14 w-14 rounded-xl shadow-xl group-hover:shadow-2xl group-hover:scale-110 group-hover:rotate-180 transition-all duration-500" style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);">
                    <i class="fas fa-cog text-2xl text-white"></i>
                </div>
            </div>
            <h3 class="text-gray-500 text-xs font-bold mb-1 uppercase tracking-wide">Processing</h3>
            <p class="text-3xl font-extrabold text-gray-900">0</p>
            <p class="text-xs text-gray-500 mt-1">Matches being analyzed</p>
        </div>
        
        <!-- Storage Card -->
        <div class="group bg-white rounded-2xl p-5 shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border-2 border-gray-100 hover:border-green-200">
            <div class="mb-3">
                <div class="flex items-center justify-center h-14 w-14 rounded-xl shadow-xl group-hover:shadow-2xl group-hover:scale-110 transition-all duration-300" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="fas fa-hdd text-2xl text-white"></i>
                </div>
            </div>
            <h3 class="text-gray-500 text-xs font-bold mb-1 uppercase tracking-wide">Storage Used</h3>
            <p class="text-3xl font-extrabold text-gray-900">2.4 GB</p>
            <p class="text-xs text-gray-500 mt-1">of 50 GB available</p>
            <div class="mt-3">
                <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden border border-gray-200">
                    <div class="h-full rounded-full shadow-sm" style="width: 5%; background: linear-gradient(90deg, #10b981 0%, #059669 100%);"></div>
                </div>
            </div>
        </div>
    </div>
    
    @if(isset($accountPending) && $accountPending)
    <!-- Account Pending Alert -->
    <div class="bg-amber-50 border-l-4 border-amber-500 rounded-xl p-6 shadow-md animate-scale-in">
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100">
                    <i class="fas fa-exclamation-triangle text-amber-600 text-xl"></i>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-amber-900">Account Pending Approval</h3>
                <p class="mt-1 text-sm text-amber-700">Your account needs to be approved by an administrator before you can upload matches.</p>
                <button class="mt-3 text-sm font-semibold text-amber-600 hover:text-amber-800 transition-colors">
                    Contact Support <i class="fas fa-arrow-right ml-1"></i>
                </button>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-200">
        <div class="flex items-center space-x-2 mb-5">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                <i class="fas fa-bolt text-white text-sm"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900">Quick Actions</h2>
        </div>
        
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <a href="{{ route('matches.create') }}" class="group rounded-xl p-5 text-white shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 hover:scale-105 transform" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%); box-shadow: 0 10px 25px rgba(96, 165, 250, 0.4);">
                <div class="h-12 w-12 rounded-xl bg-white/25 flex items-center justify-center mb-3 group-hover:scale-125 group-hover:rotate-12 transition-all duration-300">
                    <i class="fas fa-plus text-xl"></i>
                </div>
                <h3 class="font-extrabold text-lg mb-1">Upload Match</h3>
                <p class="text-xs text-white/90">Add new video</p>
            </a>
            
            <a href="{{ route('matches.index') }}" class="group rounded-xl bg-white border-2 border-gray-200 p-5 hover:border-indigo-300 hover:shadow-xl transition-all duration-300 hover:-translate-y-2 transform">
                <div class="h-10 w-10 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                    <i class="fas fa-list text-lg text-white"></i>
                </div>
                <h3 class="font-bold text-base text-gray-900 mb-1 group-hover:text-indigo-600 transition-colors">View Matches</h3>
                <p class="text-xs text-gray-500">Browse videos</p>
            </a>
            
            <a href="{{ route('profile') }}" class="group rounded-xl bg-white border-2 border-gray-200 p-5 hover:border-purple-300 hover:shadow-xl transition-all duration-300 hover:-translate-y-2 transform">
                <div class="h-10 w-10 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                    <i class="fas fa-user text-lg text-white"></i>
                </div>
                <h3 class="font-bold text-base text-gray-900 mb-1 group-hover:text-purple-600 transition-colors">Profile</h3>
                <p class="text-xs text-gray-500">Edit settings</p>
            </a>
            
            <a href="{{ route('support') }}" class="group rounded-xl bg-white border-2 border-gray-200 p-5 hover:border-teal-300 hover:shadow-xl transition-all duration-300 hover:-translate-y-2 transform">
                <div class="h-10 w-10 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300" style="background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);">
                    <i class="fas fa-life-ring text-lg text-white"></i>
                </div>
                <h3 class="font-bold text-base text-gray-900 mb-1 group-hover:text-teal-600 transition-colors">Support</h3>
                <p class="text-xs text-gray-500">Get help</p>
            </a>
        </div>
    </div>
    
    @if(isset($recentMatches) && count($recentMatches) > 0)
    <!-- Recent Matches -->
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Recent Matches</h2>
            <a href="{{ route('matches.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 flex items-center space-x-2 group">
                <span>View All</span>
                <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
        
        <div class="space-y-3">
            @foreach($recentMatches as $match)
            <a href="{{ route('matches.show', $match->id) }}" class="flex items-center space-x-4 p-4 rounded-xl hover:bg-gray-50 transition-all duration-200 group border border-transparent hover:border-blue-200">
                <div class="flex items-center justify-center h-12 w-12 rounded-xl text-white group-hover:scale-110 transition-transform duration-300" style="background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);">
                    <i class="fas fa-video text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900 truncate">{{ $match->name }}</p>
                    <p class="text-xs text-gray-500 flex items-center space-x-2 mt-1">
                        <i class="fas fa-calendar"></i>
                        <span>{{ $match->created_at->format('M d, Y') }}</span>
                    </p>
                </div>
                <div>
                    @if($match->status === 'completed')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-green-600 mr-1.5"></span>
                        Completed
                    </span>
                    @elseif($match->status === 'processing')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-600 mr-1.5 animate-pulse"></span>
                        Processing
                    </span>
                    @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">
                        {{ ucfirst($match->status) }}
                    </span>
                    @endif
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-600 group-hover:translate-x-1 transition-all"></i>
            </a>
            @endforeach
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="bg-white rounded-3xl shadow-xl p-16 text-center border-2 border-gray-100 relative overflow-hidden">
        <!-- Decorative Elements -->
        <div class="absolute top-0 left-0 w-32 h-32 bg-blue-100 rounded-full -ml-16 -mt-16 opacity-50"></div>
        <div class="absolute bottom-0 right-0 w-40 h-40 bg-blue-100 rounded-full -mr-20 -mb-20 opacity-50"></div>
        
        <div class="relative z-10">
            <!-- Icon -->
            <div class="mx-auto h-20 w-20 rounded-2xl flex items-center justify-center mb-6 shadow-xl relative" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                <div class="absolute inset-0 rounded-2xl bg-white opacity-20 animate-pulse"></div>
                <i class="fas fa-video text-3xl text-white drop-shadow-lg relative z-10"></i>
            </div>
            
            <!-- Text -->
            <h3 class="text-4xl font-extrabold text-gray-900 mb-4">No Matches Yet</h3>
            <p class="text-lg text-gray-500 mb-10 max-w-lg mx-auto font-medium">Get started by uploading your first match video for professional analysis</p>
            
            <!-- Enhanced Button -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('matches.create') }}" class="group relative inline-flex items-center justify-center space-x-3 px-10 py-5 rounded-2xl font-extrabold text-lg text-white shadow-2xl hover:shadow-3xl transition-all duration-500 hover:scale-110 hover:-translate-y-2 overflow-hidden" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                    <!-- Animated Background -->
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-300 to-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <!-- Icon -->
                    <div class="relative z-10 h-12 w-12 rounded-xl bg-white/30 backdrop-blur-sm flex items-center justify-center group-hover:rotate-90 transition-transform duration-500">
                        <i class="fas fa-plus text-2xl"></i>
                    </div>
                    
                    <!-- Text -->
                    <span class="relative z-10">Upload Your First Match</span>
                    
                    <!-- Arrow -->
                    <i class="fas fa-arrow-right relative z-10 group-hover:translate-x-2 transition-transform duration-300"></i>
                </a>
                
                <a href="{{ route('support') }}" class="inline-flex items-center space-x-2 px-6 py-3 rounded-xl font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 transition-all duration-300">
                    <i class="fas fa-question-circle"></i>
                    <span>Learn How</span>
                </a>
            </div>
            
            <!-- Additional Info -->
            <div class="mt-10 pt-8 border-t-2 border-gray-100">
                <p class="text-sm text-gray-400 font-medium mb-4">Supported formats:</p>
                <div class="flex items-center justify-center space-x-6 text-xs font-bold text-gray-500">
                    <span class="px-4 py-2 bg-gray-100 rounded-lg">MP4</span>
                    <span class="px-4 py-2 bg-gray-100 rounded-lg">AVI</span>
                    <span class="px-4 py-2 bg-gray-100 rounded-lg">MOV</span>
                    <span class="px-4 py-2 bg-gray-100 rounded-lg">MKV</span>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Activity & Tips Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Activity Chart -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-300 relative overflow-hidden">
            <!-- Decorative Background -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-full -mr-16 -mt-16 opacity-50"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-blue-50 to-purple-50 rounded-full -ml-12 -mb-12 opacity-50"></div>
            
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-2.5">
                        <div class="h-10 w-10 rounded-xl flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">
                            <i class="fas fa-chart-line text-white text-base"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">Activity Overview</h2>
                    </div>
                    <div class="text-xs text-gray-400 font-medium">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        {{ now()->format('M Y') }}
                    </div>
                </div>
                
                <div class="space-y-3">
                    <!-- Uploads Card -->
                    <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-blue-50 via-blue-50/80 to-cyan-50 border border-blue-200/60 hover:border-blue-400 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 transform">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-blue-100 rounded-full -mr-10 -mt-10 opacity-30 group-hover:opacity-50 transition-opacity"></div>
                        <div class="relative p-4 flex items-center justify-between">
                            <div class="flex items-center space-x-3.5 flex-1">
                                <div class="relative">
                                    <div class="h-12 w-12 rounded-xl flex items-center justify-center text-white shadow-lg group-hover:shadow-xl group-hover:scale-110 transition-all duration-300 z-10" style="background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);">
                                        <i class="fas fa-arrow-up text-base"></i>
                                    </div>
                                    <div class="absolute inset-0 rounded-xl bg-blue-400 blur-md opacity-30 group-hover:opacity-50 transition-opacity"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 mb-0.5">Uploads this month</p>
                                    <div class="flex items-center space-x-2">
                                        <p class="text-xs text-gray-500">Last 30 days</p>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-700 border border-green-200">
                                            <i class="fas fa-arrow-up text-xs mr-0.5"></i>
                                            +12%
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right ml-3">
                                <p class="text-3xl font-black text-blue-700 mb-0 leading-none">{{ $totalMatches ?? 0 }}</p>
                                <p class="text-xs text-gray-400 font-medium mt-0.5">matches</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Completed Card -->
                    <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-green-50 via-emerald-50/80 to-teal-50 border border-green-200/60 hover:border-green-400 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 transform">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-green-100 rounded-full -mr-10 -mt-10 opacity-30 group-hover:opacity-50 transition-opacity"></div>
                        <div class="relative p-4 flex items-center justify-between">
                            <div class="flex items-center space-x-3.5 flex-1">
                                <div class="relative">
                                    <div class="h-12 w-12 rounded-xl flex items-center justify-center text-white shadow-lg group-hover:shadow-xl group-hover:scale-110 transition-all duration-300 z-10" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                        <i class="fas fa-check-circle text-base"></i>
                                    </div>
                                    <div class="absolute inset-0 rounded-xl bg-green-400 blur-md opacity-30 group-hover:opacity-50 transition-opacity"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 mb-0.5">Analyses completed</p>
                                    <div class="flex items-center space-x-2">
                                        <p class="text-xs text-gray-500">All time</p>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-200">
                                            <i class="fas fa-star text-xs mr-0.5"></i>
                                            100%
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right ml-3">
                                <p class="text-3xl font-black text-green-700 mb-0 leading-none">{{ $totalMatches ?? 0 }}</p>
                                <p class="text-xs text-gray-400 font-medium mt-0.5">completed</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Processing Time Card -->
                    <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-cyan-50 via-sky-50/80 to-blue-50 border border-cyan-200/60 hover:border-cyan-400 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 transform">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-cyan-100 rounded-full -mr-10 -mt-10 opacity-30 group-hover:opacity-50 transition-opacity"></div>
                        <div class="relative p-4 flex items-center justify-between">
                            <div class="flex items-center space-x-3.5 flex-1">
                                <div class="relative">
                                    <div class="h-12 w-12 rounded-xl flex items-center justify-center text-white shadow-lg group-hover:shadow-xl group-hover:scale-110 transition-all duration-300 z-10" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">
                                        <i class="fas fa-clock text-base"></i>
                                    </div>
                                    <div class="absolute inset-0 rounded-xl bg-cyan-400 blur-md opacity-30 group-hover:opacity-50 transition-opacity"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 mb-0.5">Avg. processing time</p>
                                    <div class="flex items-center space-x-2">
                                        <p class="text-xs text-gray-500">Per match</p>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-700 border border-purple-200">
                                            <i class="fas fa-bolt text-xs mr-0.5"></i>
                                            Fast
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right ml-3">
                                <p class="text-3xl font-black text-cyan-700 mb-0 leading-none">2.5m</p>
                                <p class="text-xs text-gray-400 font-medium mt-0.5">average</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Stats Footer -->
                <div class="mt-3 pt-3 border-t border-gray-200 flex items-center justify-between text-xs">
                    <div class="flex items-center space-x-4 text-gray-500">
                        <div class="flex items-center space-x-1.5">
                            <div class="h-2 w-2 rounded-full bg-blue-500"></div>
                            <span class="font-medium">Uploads</span>
                        </div>
                        <div class="flex items-center space-x-1.5">
                            <div class="h-2 w-2 rounded-full bg-green-500"></div>
                            <span class="font-medium">Completed</span>
                        </div>
                        <div class="flex items-center space-x-1.5">
                            <div class="h-2 w-2 rounded-full bg-cyan-500"></div>
                            <span class="font-medium">Processing</span>
                        </div>
                    </div>
                    <a href="{{ route('matches.index') }}" class="text-blue-600 hover:text-blue-700 font-semibold flex items-center space-x-1 group">
                        <span>View All</span>
                        <i class="fas fa-arrow-right text-xs group-hover:translate-x-0.5 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Tips & Resources -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-300 relative overflow-hidden">
            <!-- Decorative Background -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-teal-50 to-cyan-50 rounded-full -mr-16 -mt-16 opacity-50"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-teal-50 to-blue-50 rounded-full -ml-12 -mb-12 opacity-50"></div>
            
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-2.5">
                        <div class="h-10 w-10 rounded-xl flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);">
                            <i class="fas fa-lightbulb text-white text-base"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">Tips & Resources</h2>
                    </div>
                    <div class="text-xs text-gray-400 font-medium">
                        <i class="fas fa-sparkles mr-1"></i>
                        Pro Tips
                    </div>
                </div>
                
                <div class="space-y-3">
                    <!-- Upload in HD Card -->
                    <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-purple-50 via-purple-50/80 to-pink-50 border border-purple-200/60 hover:border-purple-400 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 transform">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-purple-100 rounded-full -mr-10 -mt-10 opacity-30 group-hover:opacity-50 transition-opacity"></div>
                        <div class="relative p-4 flex items-center justify-between">
                            <div class="flex items-center space-x-3.5 flex-1">
                                <div class="relative">
                                    <div class="h-12 w-12 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl group-hover:scale-110 transition-all duration-300 z-10" style="background: linear-gradient(135deg, #a855f7 0%, #9333ea 100%);">
                                        <i class="fas fa-video text-base text-white"></i>
                                    </div>
                                    <div class="absolute inset-0 rounded-xl bg-purple-400 blur-md opacity-30 group-hover:opacity-50 transition-opacity"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 mb-0.5">Upload in HD</p>
                                    <p class="text-xs text-gray-500">Better quality, better results</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tag Matches Card -->
                    <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-indigo-50 via-indigo-50/80 to-blue-50 border border-indigo-200/60 hover:border-indigo-400 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 transform">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-indigo-100 rounded-full -mr-10 -mt-10 opacity-30 group-hover:opacity-50 transition-opacity"></div>
                        <div class="relative p-4 flex items-center justify-between">
                            <div class="flex items-center space-x-3.5 flex-1">
                                <div class="relative">
                                    <div class="h-12 w-12 rounded-xl flex items-center justify-center text-white shadow-lg group-hover:shadow-xl group-hover:scale-110 transition-all duration-300 z-10" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                                        <i class="fas fa-tags text-base"></i>
                                    </div>
                                    <div class="absolute inset-0 rounded-xl bg-indigo-400 blur-md opacity-30 group-hover:opacity-50 transition-opacity"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 mb-0.5">Tag your matches</p>
                                    <p class="text-xs text-gray-500">Organize with tags</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Share with Team Card -->
                    <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-teal-50 via-teal-50/80 to-cyan-50 border border-teal-200/60 hover:border-teal-400 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 transform">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-teal-100 rounded-full -mr-10 -mt-10 opacity-30 group-hover:opacity-50 transition-opacity"></div>
                        <div class="relative p-4 flex items-center justify-between">
                            <div class="flex items-center space-x-3.5 flex-1">
                                <div class="relative">
                                    <div class="h-12 w-12 rounded-xl flex items-center justify-center text-white shadow-lg group-hover:shadow-xl group-hover:scale-110 transition-all duration-300 z-10" style="background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);">
                                        <i class="fas fa-users text-base"></i>
                                    </div>
                                    <div class="absolute inset-0 rounded-xl bg-teal-400 blur-md opacity-30 group-hover:opacity-50 transition-opacity"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 mb-0.5">Share with team</p>
                                    <p class="text-xs text-gray-500">Collaborate better</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Track Progress Card -->
                    <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-amber-50 via-amber-50/80 to-orange-50 border border-amber-200/60 hover:border-amber-400 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 transform">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-amber-100 rounded-full -mr-10 -mt-10 opacity-30 group-hover:opacity-50 transition-opacity"></div>
                        <div class="relative p-4 flex items-center justify-between">
                            <div class="flex items-center space-x-3.5 flex-1">
                                <div class="relative">
                                    <div class="h-12 w-12 rounded-xl flex items-center justify-center text-white shadow-lg group-hover:shadow-xl group-hover:scale-110 transition-all duration-300 z-10" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                                        <i class="fas fa-chart-line text-base"></i>
                                    </div>
                                    <div class="absolute inset-0 rounded-xl bg-amber-400 blur-md opacity-30 group-hover:opacity-50 transition-opacity"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 mb-0.5">Track progress</p>
                                    <p class="text-xs text-gray-500">Monitor your analytics</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer Link -->
                <div class="mt-5 pt-4 border-t border-gray-200 flex items-center justify-between text-xs">
                    <div class="flex items-center space-x-4 text-gray-500">
                        <div class="flex items-center space-x-1.5">
                            <div class="h-2 w-2 rounded-full bg-purple-500"></div>
                            <span class="font-medium">Tips</span>
                        </div>
                        <div class="flex items-center space-x-1.5">
                            <div class="h-2 w-2 rounded-full bg-teal-500"></div>
                            <span class="font-medium">Resources</span>
                        </div>
                    </div>
                    <a href="{{ route('support') }}" class="text-blue-600 hover:text-blue-700 font-semibold flex items-center space-x-1 group">
                        <span>Get Help</span>
                        <i class="fas fa-arrow-right text-xs group-hover:translate-x-0.5 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @keyframes fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slide-in {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes scale-in {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    @keyframes pulse-glow {
        0%, 100% {
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.3);
        }
        50% {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.6), 0 0 30px rgba(139, 92, 246, 0.4);
        }
    }
    
    @keyframes shimmer {
        0% {
            background-position: -1000px 0;
        }
        100% {
            background-position: 1000px 0;
        }
    }
    
    .animate-fade-in {
        animation: fade-in 0.6s ease-out;
    }
    
    .animate-slide-in {
        animation: slide-in 0.8s ease-out;
        animation-fill-mode: both;
    }
    
    .animate-scale-in {
        animation: scale-in 0.5s ease-out;
    }
    
    /* Card hover animations - Enhanced */
    .group:hover {
        transform: translateY(-8px);
        transition: all 0.3s ease;
    }
    
    /* Icon glow on hover */
    .group:hover [style*="linear-gradient"] {
        box-shadow: 0 0 25px rgba(59, 130, 246, 0.5), 0 0 50px rgba(139, 92, 246, 0.3);
        transform: scale(1.15);
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    /* Icon rotation for gear */
    .fa-cog {
        transition: transform 0.5s ease;
    }
    
    .group:hover .fa-cog {
        transform: rotate(180deg);
    }
    
    /* Stats cards entrance animation */
    .group {
        animation: slide-in 0.6s ease-out;
        animation-fill-mode: both;
    }
    
    .group:nth-child(1) { animation-delay: 0.1s; }
    .group:nth-child(2) { animation-delay: 0.2s; }
    .group:nth-child(3) { animation-delay: 0.3s; }
    .group:nth-child(4) { animation-delay: 0.4s; }
</style>
@endpush
@endsection

@extends('admin.layouts.master')

@section('title', 'Matches - Eye Pro')
@section('page-title', 'Matches')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ view: 'cards' }">
    
    <!-- Matches Header -->
    <div class="relative overflow-hidden rounded-2xl p-6 shadow-lg border border-blue-200" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full opacity-10" style="background: rgba(255,255,255,0.5);"></div>
        <div class="absolute bottom-0 left-0 -mb-6 -ml-6 h-32 w-32 rounded-full opacity-10" style="background: rgba(255,255,255,0.5);"></div>
        <div class="relative z-10 flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white mb-1">Your Matches</h1>
                <p class="text-sm text-blue-50 font-medium">Manage and analyze your uploaded videos</p>
            </div>
            <a href="{{ route('matches.create') }}" class="flex items-center space-x-2 px-6 py-3 rounded-xl font-bold text-sm text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3);">
                <i class="fas fa-plus text-base"></i>
                <span>Upload Match</span>
            </a>
        </div>
    </div>
    
    @if(isset($accountPending) && $accountPending)
    <div class="bg-amber-50 border-l-4 border-amber-500 rounded-2xl p-6 shadow-lg">
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 shadow-lg">
                    <i class="fas fa-exclamation-triangle text-amber-600 text-xl"></i>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-extrabold text-amber-900">Account Pending Approval</h3>
                <p class="mt-1 text-sm text-amber-700 font-medium">Your account needs to be approved by an administrator before you can upload matches.</p>
            </div>
        </div>
    </div>
    @endif
    
    <!-- View Toggle & Filter -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-3 sm:space-y-0 gap-3">
        <div class="bg-white rounded-lg p-1 shadow-sm border border-gray-200">
            <button @click="view = 'cards'" :class="view === 'cards' ? 'text-white' : 'text-gray-600 hover:bg-gray-100'" class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all duration-200 flex items-center space-x-1.5" :style="view === 'cards' ? 'background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);' : ''">
                <i class="fas fa-th-large text-xs"></i>
                <span>Cards</span>
            </button>
            <button @click="view = 'table'" :class="view === 'table' ? 'text-white' : 'text-gray-600 hover:bg-gray-100'" class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all duration-200 flex items-center space-x-1.5" :style="view === 'table' ? 'background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);' : ''">
                <i class="fas fa-list text-xs"></i>
                <span>Table</span>
            </button>
        </div>
        
        <form action="{{ route('matches.index') }}" method="GET" class="relative">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search matches..." class="pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all w-56">
            <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"></i>
            @if(request('search'))
                <a href="{{ route('matches.index') }}" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
    </div>
    
    @if(isset($matches) && count($matches) > 0)
    
    <!-- Cards View -->
    <div x-show="view === 'cards'" x-transition class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($matches as $match)
        <a href="{{ route('matches.show', $match->id) }}" class="group bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-1 overflow-hidden border border-gray-200 hover:border-blue-300">
            <!-- Compact Header -->
            <div class="p-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2.5 flex-1 min-w-0">
                        <div class="h-8 w-8 rounded-lg flex items-center justify-center flex-shrink-0 text-white text-xs shadow-sm" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                            <i class="fas fa-video"></i>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 truncate group-hover:text-blue-600 transition-colors">{{ $match->name }}</h3>
                    </div>
                    <div class="flex-shrink-0 ml-2">
                        @if($match->status === 'completed')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-700 border border-green-200">
                            <span class="h-1 w-1 rounded-full bg-green-600 mr-1"></span>
                            Done
                        </span>
                        @elseif($match->status === 'processing')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200">
                            <span class="h-1 w-1 rounded-full bg-amber-600 mr-1 animate-pulse"></span>
                            Processing
                        </span>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                            {{ ucfirst($match->status) }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Compact Content -->
            <div class="p-4 space-y-2">
                <div class="flex items-center justify-between text-xs text-gray-600">
                    <div class="flex items-center space-x-1.5">
                        <i class="fas fa-calendar text-gray-400 text-xs"></i>
                        <span>{{ $match->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <i class="fas {{ $match->type === 'url' ? 'fa-link' : 'fa-file-video' }} text-gray-400 text-xs"></i>
                        <span>{{ ucfirst($match->type) }}</span>
                    </div>
                </div>
                <div class="flex items-center text-xs text-blue-600 group-hover:text-blue-700 font-medium pt-1">
                    <span>View Details</span>
                    <i class="fas fa-arrow-right ml-1.5 text-xs group-hover:translate-x-0.5 transition-transform"></i>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    
    <!-- Table View -->
    <div x-show="view === 'table'" x-transition class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Match Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($matches as $match)
                    <tr class="hover:bg-blue-50/50 transition-colors group">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center space-x-2.5">
                                <div class="h-7 w-7 rounded-md flex items-center justify-center text-white text-xs shadow-sm" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                                    <i class="fas fa-video text-xs"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $match->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($match->status === 'completed')
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-700 border border-green-200">
                                <span class="h-1 w-1 rounded-full bg-green-600 mr-1.5"></span>
                                Done
                            </span>
                            @elseif($match->status === 'processing')
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200">
                                <span class="h-1 w-1 rounded-full bg-amber-600 mr-1.5 animate-pulse"></span>
                                Processing
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                {{ ucfirst($match->status) }}
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600 whitespace-nowrap">
                            <i class="fas {{ $match->type === 'url' ? 'fa-link' : 'fa-file-video' }} text-gray-400 mr-1.5 text-xs"></i>
                            {{ ucfirst($match->type) }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600 whitespace-nowrap">
                            <i class="fas fa-calendar text-gray-400 mr-1.5 text-xs"></i>
                            {{ $match->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            <a href="{{ route('matches.show', $match->id) }}" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-md text-xs font-semibold text-white shadow-sm hover:shadow transition-all" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                                <i class="fas fa-eye text-xs"></i>
                                <span>View</span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if(method_exists($matches, 'links'))
        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
            {{ $matches->links() }}
        </div>
        @endif
    </div>
    
    @else
    <!-- Empty State -->
    <div class="bg-white rounded-3xl shadow-xl p-16 text-center border-2 border-gray-100 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-40 h-40 bg-blue-100 rounded-full -ml-20 -mt-20 opacity-50"></div>
        <div class="absolute bottom-0 right-0 w-48 h-48 bg-blue-100 rounded-full -mr-24 -mb-24 opacity-50"></div>
        
        <div class="relative z-10">
            <div class="mx-auto h-32 w-32 rounded-3xl flex items-center justify-center mb-8 shadow-2xl" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                <div class="absolute inset-0 rounded-3xl bg-white opacity-20 animate-pulse"></div>
                <i class="fas fa-video text-6xl text-white drop-shadow-lg relative z-10"></i>
            </div>
            
            <h3 class="text-4xl font-extrabold text-gray-900 mb-4">No Matches Yet</h3>
            <p class="text-lg text-gray-500 mb-10 max-w-lg mx-auto font-medium">
                @if(isset($accountPending) && $accountPending)
                    Wait for approval to upload matches
                @else
                    Upload your first match to get started with analysis
                @endif
            </p>
            
            @if(!isset($accountPending) || !$accountPending)
            <a href="{{ route('matches.create') }}" class="inline-flex items-center justify-center space-x-3 px-10 py-5 rounded-2xl font-extrabold text-lg text-white shadow-2xl hover:shadow-3xl transition-all duration-500 hover:scale-110 hover:-translate-y-2" style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);">
                <div class="h-12 w-12 rounded-xl bg-white/30 backdrop-blur-sm flex items-center justify-center">
                    <i class="fas fa-plus text-2xl"></i>
                </div>
                <span>Upload Your First Match</span>
            </a>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection

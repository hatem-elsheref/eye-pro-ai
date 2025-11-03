@extends('admin.layouts.master')

@section('title', __('admin.matches') . ' - Eye Pro')
@section('page-title', __('admin.matches'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    <!-- Matches Header -->
    <div class="relative overflow-hidden rounded-2xl p-6 shadow-lg border border-blue-200" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full opacity-10" style="background: rgba(255,255,255,0.5);"></div>
        <div class="absolute bottom-0 left-0 -mb-6 -ml-6 h-32 w-32 rounded-full opacity-10" style="background: rgba(255,255,255,0.5);"></div>
        <div class="relative z-10 flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white mb-1">{{ __('admin.your_matches') }}</h1>
                <p class="text-sm text-blue-50 font-medium">{{ __('admin.manage_and_analyze') }}</p>
            </div>
            <a href="{{ route('matches.create') }}" class="flex items-center space-x-2 px-6 py-3 rounded-xl font-bold text-sm text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 cursor-pointer" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3);">
                <i class="fas fa-plus text-base pointer-events-none"></i>
                <span class="pointer-events-none">{{ __('admin.upload_match') }}</span>
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
                <h3 class="text-lg font-extrabold text-amber-900">{{ __('admin.account_pending_approval') }}</h3>
                <p class="mt-1 text-sm text-amber-700 font-medium">{{ __('admin.account_pending_approval_message') }}</p>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Search Filter -->
    <div class="flex flex-col sm:flex-row justify-end items-start sm:items-center space-y-3 sm:space-y-0 gap-3">
        <form action="{{ route('matches.index') }}" method="GET" class="relative" id="searchForm">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="{{ __('admin.search_matches') }}" 
                class="pl-8 pr-8 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all w-56"
                id="searchInput"
                autocomplete="off"
            >
            <button type="submit" class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs" style="background: none; border: none; cursor: pointer;">
                <i class="fas fa-search"></i>
            </button>
            @if(request('search'))
                <a href="{{ route('matches.index') }}" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs" title="{{ __('admin.clear_search') }}">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
    </div>
    
    @if(isset($matches) && count($matches) > 0)
    
    <!-- Cards View -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
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
                            {{ __('admin.done') }}
                        </span>
                        @elseif($match->status === 'processing')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200">
                            <span class="h-1 w-1 rounded-full bg-amber-600 mr-1 animate-pulse"></span>
                            {{ __('admin.processing') }}
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
                    <span>{{ __('admin.view_details') }}</span>
                    <i class="fas fa-arrow-right ml-1.5 text-xs group-hover:translate-x-0.5 transition-transform"></i>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    
    <!-- Pagination Controls -->
    @if(method_exists($matches, 'links'))
    <div class="matches-cards-pagination mt-6">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-white rounded-xl shadow-lg p-4 border border-gray-200">
            <div class="text-sm text-gray-600 font-medium">
                {{ __('admin.showing_results', ['from' => $matches->firstItem() ?? 0, 'to' => $matches->lastItem() ?? 0, 'total' => $matches->total()]) }}
            </div>
            <div class="flex items-center gap-2">
                @php
                    $currentPage = $matches->currentPage();
                    $lastPage = $matches->lastPage();
                    $hasPrevious = $currentPage > 1;
                    $hasNext = $currentPage < $lastPage;
                @endphp
                
                {{-- Previous Button --}}
                @if($hasPrevious && $matches->previousPageUrl())
                    <a href="{{ $matches->previousPageUrl() }}" style="background: linear-gradient(to right, #9333ea, #ec4899);" class="px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 shadow-md hover:shadow-lg transition-all duration-200 flex items-center">
                        <i class="fas fa-chevron-left mr-2"></i>
                        {{ __('admin.previous') }}
                    </a>
                @else
                    <button type="button" disabled style="background-color: #d1d5db;" class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-400 border border-gray-400 cursor-not-allowed opacity-60">
                        <i class="fas fa-chevron-left mr-2"></i>
                        {{ __('admin.previous') }}
                    </button>
                @endif
                
                {{-- Next Button --}}
                @if($hasNext && $matches->nextPageUrl())
                    <a href="{{ $matches->nextPageUrl() }}" style="background: linear-gradient(to right, #9333ea, #ec4899);" class="px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 shadow-md hover:shadow-lg transition-all duration-200 flex items-center">
                        {{ __('admin.next') }}
                        <i class="fas fa-chevron-right ml-2"></i>
                    </a>
                @else
                    <button type="button" disabled style="background-color: #d1d5db;" class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-400 border border-gray-400 cursor-not-allowed opacity-60">
                        {{ __('admin.next') }}
                        <i class="fas fa-chevron-right ml-2"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>
    @endif
    
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
            
            <h3 class="text-4xl font-extrabold text-gray-900 mb-4">{{ __('admin.no_matches_yet') }}</h3>
            <p class="text-lg text-gray-500 mb-10 max-w-lg mx-auto font-medium">
                @if(isset($accountPending) && $accountPending)
                    {{ __('admin.wait_for_approval') }}
                @else
                    {{ __('admin.upload_first_match_message') }}
                @endif
            </p>
            
            @if(!isset($accountPending) || !$accountPending)
            <a href="{{ route('matches.create') }}" class="upload-match-btn inline-flex items-center justify-center space-x-3 px-10 py-5 rounded-2xl font-extrabold text-lg text-white shadow-2xl hover:shadow-3xl transition-all duration-500 hover:scale-110 hover:-translate-y-2 cursor-pointer relative z-20" style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); display: inline-flex; position: relative; z-index: 20;">
                <div class="h-12 w-12 rounded-xl bg-white/30 backdrop-blur-sm flex items-center justify-center pointer-events-none">
                    <i class="fas fa-plus text-2xl pointer-events-none"></i>
                </div>
                <span class="pointer-events-none">{{ __('admin.upload_your_first_match') }}</span>
            </a>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    /* Ensure orange button is fully clickable */
    .upload-match-btn {
        pointer-events: auto !important;
        cursor: pointer !important;
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        position: relative !important;
        z-index: 999 !important;
    }
    
    .upload-match-btn * {
        pointer-events: none !important;
    }
    
    .upload-match-btn:hover {
        pointer-events: auto !important;
    }
    
    /* Ensure no parent element blocks clicks */
    .upload-match-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: -1;
        pointer-events: none;
    }
    
    /* Enhanced Table Wrapper */
    .matches-table-wrapper {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08), 0 4px 10px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        border: 2px solid #e5e7eb;
        position: relative;
    }
    
    .matches-table-wrapper::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #60a5fa 0%, #818cf8 50%, #a78bfa 100%);
        z-index: 1;
    }
    
    /* Enhanced Table Container */
    .matches-table-container {
        background: white;
        border-collapse: separate;
        border-spacing: 0;
        position: relative;
    }
    
    .matches-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        background: white;
    }
    
    /* Enhanced Table Header */
    .matches-table-header {
        padding: 1.25rem 1.5rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #4338ca;
        background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 50%, #e0e7ff 100%);
        border-bottom: 3px solid #c7d2fe;
        position: sticky;
        top: 0;
        z-index: 20;
        box-shadow: 0 2px 8px rgba(139, 92, 246, 0.1);
        transition: all 0.3s ease;
    }
    
    .matches-table-header:hover {
        background: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 50%, #ede9fe 100%);
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.15);
    }
    
    .matches-table-header:first-child {
        border-top-left-radius: 0.875rem;
    }
    
    .matches-table-header:last-child {
        border-top-right-radius: 0.875rem;
    }
    
    /* Enhanced Table Rows */
    .matches-table-row {
        background-color: #ffffff;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        border-bottom: 1px solid #e5e7eb;
        position: relative;
    }
    
    .matches-table-row::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: transparent;
        transition: all 0.25s ease;
    }
    
    .matches-table-row:nth-child(odd) {
        background-color: #ffffff;
    }
    
    .matches-table-row:nth-child(even) {
        background-color: #f8fafc;
    }
    
    .matches-table-row:hover {
        background: linear-gradient(90deg, #f0f9ff 0%, #eff6ff 50%, #f0f9ff 100%) !important;
        transform: translateX(2px);
        box-shadow: -4px 0 12px rgba(96, 165, 250, 0.15), 0 2px 8px rgba(96, 165, 250, 0.1);
        border-left: 3px solid #60a5fa;
    }
    
    .matches-table-row:hover::before {
        background: linear-gradient(180deg, #60a5fa 0%, #818cf8 100%);
    }
    
    /* Enhanced Table Cells */
    .matches-table-cell {
        padding: 1.25rem 1.5rem;
        font-size: 0.875rem;
        color: #374151;
        vertical-align: middle;
        border-right: 1px solid #f1f5f9;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .matches-table-row:hover .matches-table-cell {
        color: #1e293b;
    }
    
    .matches-table-row .matches-table-cell:last-child {
        border-right: none;
    }
    
    .matches-table-row:last-child {
        border-bottom: none;
    }
    
    .matches-table-row:last-child .matches-table-cell {
        border-bottom: none;
    }
    
    /* Enhanced icon containers in cells */
    .matches-table-cell .flex.items-center > div[style*="gradient"] {
        box-shadow: 0 2px 8px rgba(96, 165, 250, 0.3);
        transition: all 0.3s ease;
    }
    
    .matches-table-row:hover .matches-table-cell .flex.items-center > div[style*="gradient"] {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 4px 12px rgba(96, 165, 250, 0.4);
    }
    
    /* Enhanced action buttons */
    .matches-table-cell a[style*="gradient"] {
        box-shadow: 0 2px 6px rgba(96, 165, 250, 0.25);
        transition: all 0.25s ease;
    }
    
    .matches-table-cell a[style*="gradient"]:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 6px 16px rgba(96, 165, 250, 0.4);
    }
    
    /* Enhanced Pagination Styling */
    .matches-pagination-container {
        padding: 1rem 1.25rem;
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        border-top: 2px solid #e5e7eb;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .matches-pagination-container nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .matches-pagination-container .pagination {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        list-style: none;
        padding: 0;
        margin: 0;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .matches-pagination-container .pagination li {
        margin: 0;
    }
    
    .matches-pagination-container .pagination a,
    .matches-pagination-container .pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 0.75rem;
        min-width: 2.5rem;
        height: 2.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #4b5563;
        background-color: #ffffff;
        border: 2px solid #e5e7eb;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    
    .matches-pagination-container .pagination a:hover:not(.disabled) {
        background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);
        color: #ffffff;
        border-color: #60a5fa;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(96, 165, 250, 0.3);
    }
    
    .matches-pagination-container .pagination .active span {
        background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);
        color: #ffffff;
        border-color: #60a5fa;
        box-shadow: 0 2px 4px rgba(96, 165, 250, 0.3);
    }
    
    .matches-pagination-container .pagination .disabled span,
    .matches-pagination-container .pagination .disabled a {
        background-color: #f3f4f6;
        color: #9ca3af;
        border-color: #e5e7eb;
        cursor: not-allowed;
        opacity: 0.5;
    }
    
    .matches-pagination-container .pagination .disabled span:hover,
    .matches-pagination-container .pagination .disabled a:hover {
        transform: none;
        box-shadow: none;
        background-color: #f3f4f6;
        color: #9ca3af;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure orange upload button is fully clickable
        const uploadBtn = document.querySelector('.upload-match-btn');
        if (uploadBtn) {
            // Make the entire button clickable
            uploadBtn.style.pointerEvents = 'auto';
            uploadBtn.style.cursor = 'pointer';
            
            // Add click handler to entire button area
            uploadBtn.addEventListener('click', function(e) {
                // Allow the default link behavior
                // Don't prevent default - let the anchor work normally
            }, true);
            
            // Ensure all child elements don't block clicks
            const children = uploadBtn.querySelectorAll('*');
            children.forEach(function(child) {
                child.style.pointerEvents = 'none';
            });
        }
        
        // Ensure search form submits properly
        const searchForm = document.getElementById('searchForm');
        const searchInput = document.getElementById('searchInput');
        
        if (searchForm && searchInput) {
            // Submit form when Enter key is pressed
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchForm.submit();
                }
            });
            
            // Ensure form submission works
            searchForm.addEventListener('submit', function(e) {
                // Allow normal form submission
                return true;
            });
        }
    });
</script>
@endpush

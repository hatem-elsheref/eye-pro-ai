@extends('admin.layouts.master')

@section('title', $match->name ?? 'Match Details')

@push('styles')
<link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
<style>
    .plyr {
        border-radius: 12px;
        overflow: hidden;
    }
    .plyr__video-wrapper {
        background: #000;
    }
    .info-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    .info-card:hover {
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .stat-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: white;
        border-radius: 8px;
        margin-bottom: 8px;
        border-left: 3px solid;
    }
    .stat-item.pending { border-left-color: #f59e0b; }
    .stat-item.processing { border-left-color: #3b82f6; }
    .stat-item.completed { border-left-color: #10b981; }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('matches.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors mb-4">
            <i class="fas fa-arrow-left text-sm"></i>
            <span class="font-medium">Back to Matches</span>
        </a>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $match->name ?? 'Match Details' }}</h1>
                <p class="text-sm text-gray-500">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    Uploaded on {{ isset($match->created_at) ? $match->created_at->format('F d, Y \a\t h:i A') : 'N/A' }}
        </p>
    </div>

            <div class="flex gap-3">
                <a href="{{ route('matches.edit', $match->id ?? 1) }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-white rounded-lg font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                    <i class="fas fa-edit text-sm"></i>
                    <span>Edit Match</span>
                </a>

                <form id="deleteMatchForm" action="{{ route('matches.destroy', $match->id ?? 1) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                        <i class="fas fa-trash text-sm"></i>
                        <span>Delete Match</span>
                    </button>
                </form>
            </div>
    </div>
</div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <!-- Main Content (3/5 width) -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Video Player (Smaller) -->
            <div class="info-card rounded-2xl p-6 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-play-circle text-blue-500"></i>
                        Match Video
                    </h2>
                    @if(isset($match->status) && $match->status === 'processing')
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                            <i class="fas fa-spinner fa-spin mr-1"></i>
                            Processing
                        </span>
                    @endif
                </div>

                <div class="relative bg-black rounded-xl overflow-hidden shadow-2xl max-w-3xl mx-auto">
                @if(isset($match->video_url))
                        <video id="player" class="w-full" playsinline controls data-poster="">
                        <source src="{{ $match->video_url }}" type="video/mp4">
                            <source src="{{ $match->video_url }}" type="video/webm">
                        Your browser does not support the video tag.
                    </video>
                @else
                        <div class="aspect-video flex items-center justify-center bg-gradient-to-br from-gray-900 to-black">
                            <div class="text-center text-white">
                                <i class="fas fa-video text-6xl opacity-30 mb-4"></i>
                                <p class="text-lg opacity-70 font-medium">Video not available</p>
                            </div>
                    </div>
                @endif
            </div>
        </div>

                <!-- Match Info (Under Video) - Compact Design -->
            <div class="info-card rounded-2xl p-5 shadow-lg">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-indigo-500 text-base"></i>
                    <span>Match Information</span>
                </h2>

                <!-- Compact Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mb-4">
                    <!-- Status -->
                    <div class="flex items-center gap-2 p-2.5 bg-white rounded-lg border border-gray-200 hover:border-blue-300 transition-colors">
                        <div class="flex-shrink-0">
                            @if(isset($match->status) && $match->status === 'completed')
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-check-circle text-green-600 text-xs"></i>
                                </div>
                            @elseif(isset($match->status) && $match->status === 'processing')
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-spinner fa-spin text-blue-600 text-xs"></i>
                                </div>
                            @else
                                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-clock text-yellow-600 text-xs"></i>
                                </div>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 font-medium truncate">Status</p>
                            <p class="text-xs font-bold text-gray-900 truncate">
                                {{ isset($match->status) ? ucfirst($match->status) : 'Pending' }}
                            </p>
                        </div>
                    </div>

                    <!-- Type -->
                    <div class="flex items-center gap-2 p-2.5 bg-white rounded-lg border border-gray-200 hover:border-indigo-300 transition-colors">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas {{ isset($match->type) && $match->type === 'url' ? 'fa-link' : 'fa-file-video' }} text-indigo-600 text-xs"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 font-medium truncate">Type</p>
                            <p class="text-xs font-bold text-gray-900 truncate">
                                {{ isset($match->type) ? ucfirst($match->type) : 'File' }}
                            </p>
                        </div>
                    </div>

                    <!-- File Size -->
                    @if(isset($match->file_size))
                    <div class="flex items-center gap-2 p-2.5 bg-white rounded-lg border border-gray-200 hover:border-pink-300 transition-colors">
                        <div class="w-8 h-8 bg-pink-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-hdd text-pink-600 text-xs"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 font-medium truncate">Size</p>
                            <p class="text-xs font-bold text-gray-900 truncate">{{ $match->file_size }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Created Date -->
                    <div class="flex items-center gap-2 p-2.5 bg-white rounded-lg border border-gray-200 hover:border-teal-300 transition-colors">
                        <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-calendar text-teal-600 text-xs"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 font-medium truncate">Created</p>
                            <p class="text-xs font-bold text-gray-900 truncate">
                                {{ isset($match->created_at) ? $match->created_at->format('M d, Y') : 'N/A' }}
                            </p>
                        </div>
                    </div>

                    <!-- Uploaded By -->
                    <div class="flex items-center gap-2 p-2.5 bg-white rounded-lg border border-gray-200 hover:border-purple-300 transition-colors">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-purple-600 text-xs"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 font-medium truncate">Uploaded By</p>
                            <p class="text-xs font-bold text-gray-900 truncate">{{ $match->user->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Used Disk -->
                    <div class="flex items-center gap-2 p-2.5 bg-white rounded-lg border border-gray-200 hover:border-blue-300 transition-colors">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-server text-blue-600 text-xs"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 font-medium truncate">Storage</p>
                            <p class="text-xs font-bold text-gray-900 truncate">{{ $usedDisk ?? 'Public' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                @if(isset($match->description) && !empty($match->description))
                <div class="mb-4 p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
                    <div class="flex items-start gap-2 mb-2">
                        <i class="fas fa-align-left text-indigo-600 text-sm mt-0.5"></i>
                        <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wide">Description</h3>
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $match->description }}</p>
                </div>
                @endif

                <!-- Tags -->
                @if(isset($match->tags) && !empty($match->tags))
                <div class="flex flex-wrap items-center gap-2">
                    <div class="flex items-center gap-1.5 mr-1">
                        <i class="fas fa-hashtag text-indigo-600 text-xs"></i>
                        <span class="text-xs font-semibold text-gray-700">Tags:</span>
                    </div>
                    @php
                        $tagsArray = is_string($match->tags) ? explode(',', $match->tags) : [];
                        $tagsArray = array_map('trim', $tagsArray);
                        $tagsArray = array_filter($tagsArray);
                    @endphp
                    @foreach($tagsArray as $tag)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold text-white shadow-sm" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                            {{ $tag }}
                        </span>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Right Sidebar - Match Analysis (2/5 width) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Match Analysis -->
            <div class="info-card rounded-2xl p-6 shadow-lg">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-line text-purple-500"></i>
                    Match Analysis
                </h2>

                    <!-- Analysis Container (will be populated by JavaScript) -->
                    <div id="analysisLoadingContainer" class="space-y-4">
                        <!-- Skeleton Loader (shown initially, replaced by analysis if available) -->
                        <!-- Header Skeleton -->
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 bg-purple-200 rounded-lg"></div>
                            <div class="h-4 w-32 bg-gray-200 rounded"></div>
                        </div>

                        <!-- Content Skeleton Lines -->
                        <div class="space-y-3">
                            <div class="h-4 bg-gray-200 rounded w-full"></div>
                            <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                            <div class="h-4 bg-gray-200 rounded w-full"></div>
                            <div class="h-4 bg-gray-200 rounded w-4/6"></div>
                            <div class="h-4 bg-gray-200 rounded w-full"></div>
                            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                        </div>

                        <!-- Chart/Graph Skeleton -->
                        <div class="mt-6 space-y-2">
                            <div class="h-32 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-lg border-2 border-dashed border-purple-200 flex items-center justify-center">
                                <div class="text-center">
                                    <i class="fas fa-chart-line text-3xl text-purple-300 mb-2"></i>
                                    <p class="text-sm text-purple-400 font-medium">Analyzing match data...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Loading indicator -->
                        <div class="flex items-center justify-center gap-2 pt-4">
                            <div class="w-2 h-2 bg-purple-500 rounded-full animate-bounce" style="animation-delay: 0s;"></div>
                            <div class="w-2 h-2 bg-purple-500 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
                            <div class="w-2 h-2 bg-purple-500 rounded-full animate-bounce" style="animation-delay: 0.4s;"></div>
                            <span class="ml-2 text-sm text-gray-500 font-medium">Processing analysis...</span>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Plyr video player with smaller size
    const video = document.getElementById('player');
    if (video) {
        const player = new Plyr(video, {
            controls: [
                'play-large',
                'play',
                'progress',
                'current-time',
                'duration',
                'mute',
                'volume',
                'settings',
                'fullscreen'
            ],
            settings: ['captions', 'quality', 'speed'],
            speed: {
                selected: 1,
                options: [0.5, 0.75, 1, 1.25, 1.5, 1.75, 2]
            },
            keyboard: {
                focused: true,
                global: false
            },
            tooltips: {
                controls: true,
                seek: true
            },
            ratio: '16:9'
        });

        // Log player events for debugging
        player.on('ready', () => {
            console.log('Player ready');
        });

        player.on('play', () => {
            console.log('Video playing');
        });
    }

    // Check and render analysis if available, otherwise connect WebSocket
    @php
        $hasAnalysis = isset($match->analysis) && !empty($match->analysis);
        $analysisData = $hasAnalysis ? $match->analysis : null;
    @endphp

    const hasAnalysis = {{ $hasAnalysis ? 'true' : 'false' }};
    const analysisData = @json($analysisData);

    // Render analysis result function (used by both direct render and WebSocket)
    function renderAnalysisResult(analysis) {
        const analysisContainer = document.getElementById('analysisLoadingContainer');
        if (!analysisContainer) return;

        let analysisJson = '';
        try {
            // Try to parse and pretty print
            const parsed = typeof analysis === 'string' 
                ? JSON.parse(analysis) 
                : analysis;
            analysisJson = JSON.stringify(parsed, null, 2);
        } catch (e) {
            // If not valid JSON, just display as is
            analysisJson = typeof analysis === 'string' 
                ? analysis 
                : JSON.stringify(analysis);
        }

        analysisContainer.innerHTML = `
            <div id="analysisResults" class="space-y-4">
                <div class="bg-gray-900 rounded-lg p-4 max-h-96 overflow-y-auto">
                    <pre id="analysisJson" class="text-green-400 text-xs font-mono whitespace-pre-wrap break-words">${analysisJson}</pre>
                </div>
            </div>
        `;
    }

    // If analysis exists, render it directly (no WebSocket needed)
    if (hasAnalysis && analysisData) {
        renderAnalysisResult(analysisData);
    } else {
        // No analysis - connect to WebSocket for real-time updates
        const wsProtocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        const wsHost = '{{ env("WEBSOCKET_HOST", "localhost:3001") }}';
        const wsUrl = `${wsProtocol}//${wsHost}/ws`;
        let ws = null;
        let reconnectAttempts = 0;
        const maxReconnectAttempts = 5;
        let reconnectTimeout = null;

        function connectWebSocket() {
            try {
                ws = new WebSocket(wsUrl);

                ws.onopen = function() {
                    console.log('WebSocket connected');
                    reconnectAttempts = 0;
                    
                    // Subscribe to private channel
                    const userId = {{ auth()->id() ?? 0 }};
                    const matchId = {{ $match->id ?? 0 }};
                    
                    ws.send(JSON.stringify({
                        type: 'subscribe',
                        userId: userId,
                        matchId: matchId
                    }));
                };

                ws.onmessage = function(event) {
                    try {
                        const data = JSON.parse(event.data);
                        
                        if (data.type === 'subscribed') {
                            console.log('Subscribed to channel:', data.channel);
                        } else if (data.type === 'analysis_result') {
                            // Received analysis result - render it
                            renderAnalysisResult(data.analysis);
                            // Close connection after receiving result
                            if (ws) {
                                ws.close();
                                ws = null;
                            }
                        }
                    } catch (error) {
                        console.error('Error parsing WebSocket message:', error);
                    }
                };

                ws.onerror = function(error) {
                    console.error('WebSocket error:', error);
                };

                ws.onclose = function(event) {
                    console.log('WebSocket disconnected', event.code, event.reason);
                    
                    // Don't reconnect if we received the result or intentionally closed
                    if (event.code !== 1000 && reconnectAttempts < maxReconnectAttempts) {
                        reconnectAttempts++;
                        const delay = Math.min(1000 * Math.pow(2, reconnectAttempts - 1), 10000); // Exponential backoff, max 10s
                        
                        console.log(`Reconnecting in ${delay}ms (attempt ${reconnectAttempts}/${maxReconnectAttempts})`);
                        
                        reconnectTimeout = setTimeout(() => {
                            connectWebSocket();
                        }, delay);
                    } else if (reconnectAttempts >= maxReconnectAttempts) {
                        console.error('Max reconnection attempts reached');
                        showConnectionError();
                    }
                };

            } catch (error) {
                console.error('Error creating WebSocket connection:', error);
                showConnectionError();
            }
        }


        function showConnectionError() {
            const analysisContainer = document.getElementById('analysisLoadingContainer');
            if (analysisContainer) {
                analysisContainer.innerHTML = `
                    <div class="text-center py-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-red-100 to-pink-100 rounded-full mb-4 shadow-lg">
                            <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
                        </div>
                        <p class="text-gray-800 font-bold text-base mb-1">Connection Error</p>
                        <p class="text-xs text-gray-500">Unable to connect to analysis stream. Please refresh the page manually.</p>
                    </div>
                `;
            }
        }

            // Connect when page loads
            connectWebSocket();

            // Cleanup on page unload
            window.addEventListener('beforeunload', function() {
                if (reconnectTimeout) {
                    clearTimeout(reconnectTimeout);
                }
                if (ws) {
                    ws.close();
                }
            });
        }

    // Delete confirmation
    const deleteForm = document.getElementById('deleteMatchForm');

    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: 'Are you sure you want to delete this match? This action cannot be undone.',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteForm.submit();
                }
            });
        });
    }
});

function shareMatch() {
    const url = window.location.href;

    if (navigator.share) {
        navigator.share({
            title: '{{ $match->name ?? "Match Video" }}',
            text: 'Check out this match video',
            url: url
        }).catch(err => {
            console.log('Error sharing:', err);
            copyToClipboard(url);
        });
    } else {
        copyToClipboard(url);
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Link copied to clipboard',
            timer: 2000,
            showConfirmButton: false
        });
    });
}

function exportAnalysis() {
    const analysis = `{{ addslashes($match->analysis ?? '') }}`;
    const name = `{{ $match->name ?? 'match' }}`;
    const blob = new Blob([analysis], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${name}_analysis.txt`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
@endpush
@endsection

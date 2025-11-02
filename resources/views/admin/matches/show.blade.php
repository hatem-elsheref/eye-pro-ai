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
                <!-- Start/Stop Processing Button -->
                @if(isset($match->status) && $match->status === 'pending')
                    <button id="processingBtn" type="button" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-white rounded-lg font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fas fa-play text-sm"></i>
                        <span>Start Processing</span>
                    </button>
                @elseif(isset($match->status) && $match->status === 'processing')
                    <button id="processingBtn" type="button" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-white rounded-lg font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5 bg-red-600 hover:bg-red-700">
                        <i class="fas fa-stop text-sm"></i>
                        <span>Stop Processing</span>
                    </button>
                @elseif(isset($match->status) && $match->status === 'completed')
                    <!-- Button hidden when completed -->
                    <button id="processingBtn" type="button" style="display: none;"></button>
                @endif

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
                    <div id="statusCard" class="flex items-center gap-2 p-2.5 bg-white rounded-lg border border-gray-200 hover:border-blue-300 transition-colors">
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
                            <p id="statusText" class="text-xs font-bold text-gray-900 truncate">
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
                        @if(isset($match->status) && $match->status === 'completed' && (!$match->predictions || $match->predictions->count() === 0))
                            <!-- Completed but no predictions -->
                            <div class="text-center py-8">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full mb-4 shadow-lg">
                                    <i class="fas fa-info-circle text-2xl text-gray-600"></i>
                                </div>
                                <p class="text-gray-800 font-bold text-base mb-1">No Predictions Available</p>
                                <p class="text-xs text-gray-500">Processing completed but no predictions were generated for this match.</p>
                            </div>
                        @elseif(isset($match->status) && $match->status !== 'completed')
                            <!-- Loading state (only show if not completed) -->
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 bg-purple-200 rounded-lg animate-pulse"></div>
                                <div class="h-4 w-32 bg-gray-200 rounded animate-pulse"></div>
                            </div>
                            <div class="space-y-3">
                                <div class="h-4 bg-gray-200 rounded w-full animate-pulse"></div>
                                <div class="h-4 bg-gray-200 rounded w-5/6 animate-pulse"></div>
                                <div class="h-4 bg-gray-200 rounded w-full animate-pulse"></div>
                            </div>
                            <div class="mt-6">
                                <div class="h-32 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-lg border-2 border-dashed border-purple-200 flex items-center justify-center">
                                    <div class="text-center">
                                        <i class="fas fa-chart-line text-3xl text-purple-300 mb-2"></i>
                                        <p class="text-sm text-purple-400 font-medium">Analyzing match data...</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-center gap-2 pt-4">
                                <div class="w-2 h-2 bg-purple-500 rounded-full animate-bounce" style="animation-delay: 0s;"></div>
                                <div class="w-2 h-2 bg-purple-500 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
                                <div class="w-2 h-2 bg-purple-500 rounded-full animate-bounce" style="animation-delay: 0.4s;"></div>
                                <span class="ml-2 text-sm text-gray-500 font-medium">Processing analysis...</span>
                            </div>
                        @else
                            <!-- Will be populated by JavaScript if predictions exist -->
                        @endif
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

    // Load predictions from database
    @php
        $predictions = $match->predictions ?? collect([]);
        $hasPredictions = $predictions->count() > 0;
        $predictionsData = $predictions->toArray();
        $matchStatus = $match->status ?? 'pending';
    @endphp

    const predictionsData = @json($predictionsData);
    const hasPredictions = {{ $hasPredictions ? 'true' : 'false' }};
    const matchStatus = '{{ $matchStatus }}';
    let allPredictions = hasPredictions ? predictionsData : [];

    // Render predictions function
    function renderPredictions(predictions) {
        const analysisContainer = document.getElementById('analysisLoadingContainer');
        if (!analysisContainer) return;

        if (predictions.length === 0) {
            // If status is completed, show message that no predictions were found
            // Otherwise show loading state
            if (matchStatus === 'completed') {
                analysisContainer.innerHTML = `
                    <div class="space-y-4">
                        <div class="text-center py-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full mb-4 shadow-lg">
                                <i class="fas fa-info-circle text-2xl text-gray-600"></i>
                            </div>
                            <p class="text-gray-800 font-bold text-base mb-1">No Predictions Available</p>
                            <p class="text-xs text-gray-500">Processing completed but no predictions were generated for this match.</p>
                        </div>
                    </div>
                `;
            } else {
                // Show loading state only if not completed
                analysisContainer.innerHTML = `
                    <div class="space-y-4">
                        <div class="text-center py-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-full mb-4 shadow-lg">
                                <i class="fas fa-spinner fa-spin text-2xl text-purple-600"></i>
                            </div>
                            <p class="text-gray-800 font-bold text-base mb-1">Waiting for predictions...</p>
                            <p class="text-xs text-gray-500">Processing match video. Predictions will appear here.</p>
                        </div>
                    </div>
                `;
            }
            return;
        }

        // Build predictions HTML
        let predictionsHtml = '<div class="space-y-4">';
        predictionsHtml += `<div class="mb-4"><h3 class="text-lg font-bold text-gray-900">Found ${predictions.length} prediction(s)</h3></div>`;

        predictions.forEach((prediction, index) => {
            predictionsHtml += `
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-lg p-4 border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold text-gray-900">Prediction #${index + 1}</h4>
                        ${prediction.relative_time ? `<span class="text-xs text-gray-500"><i class="fas fa-clock mr-1"></i>${prediction.relative_time}</span>` : ''}
                    </div>
                    ${prediction.clip_path ? `<div class="mb-2 text-xs text-gray-600"><i class="fas fa-video mr-1"></i>Clip: ${prediction.clip_path}</div>` : ''}
                    ${prediction.first_model_prop !== null ? `<div class="mb-2 text-xs"><span class="font-medium">First Model Accuracy:</span> <span class="text-purple-600">${(prediction.first_model_prop * 100).toFixed(2)}%</span></div>` : ''}
                    
                    ${prediction.prediction_0 ? `
                        <div class="mb-3 p-3 bg-purple-50 rounded border border-purple-200">
                            <div class="font-medium text-xs text-purple-900 mb-2">Model 0 Prediction:</div>
                            ${renderPredictionData(prediction.prediction_0)}
                        </div>
                    ` : ''}
                    
                    ${prediction.prediction_1 ? `
                        <div class="mb-3 p-3 bg-indigo-50 rounded border border-indigo-200">
                            <div class="font-medium text-xs text-indigo-900 mb-2">Model 1 Prediction:</div>
                            ${renderPredictionData(prediction.prediction_1)}
                        </div>
                    ` : ''}
                </div>
            `;
        });

        predictionsHtml += '</div>';
        analysisContainer.innerHTML = predictionsHtml;
    }

    // Helper function to render prediction data
    function renderPredictionData(prediction) {
        if (!prediction) return '<div class="text-xs text-gray-500">No data</div>';
        
        let html = '';
        
        if (prediction.classes && Array.isArray(prediction.classes[0])) {
            html += '<div class="text-xs space-y-1">';
            prediction.classes[0].forEach((classId, idx) => {
                const accuracy = prediction.acc && prediction.acc[0] && prediction.acc[0][idx] 
                    ? (prediction.acc[0][idx] * 100).toFixed(2) + '%' 
                    : 'N/A';
                html += `<div class="flex justify-between"><span>Class ${classId}:</span><span class="font-semibold">${accuracy}</span></div>`;
            });
            html += '</div>';
        }
        
        return html || '<pre class="text-xs">' + JSON.stringify(prediction, null, 2) + '</pre>';
    }

    // If predictions exist, render them directly
    if (hasPredictions) {
        renderPredictions(allPredictions);
    } else {
        // No predictions - render empty state (with status check)
        renderPredictions([]);
    }

    // Connect to WebSocket only if processing (not if completed)
    if (matchStatus === 'processing') {
        // Connect to WebSocket for real-time updates
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
                        } else if (data.type === 'prediction') {
                            // Received new prediction - add to array and re-render
                            console.log('Received prediction:', data.prediction);
                            allPredictions.push(data.prediction);
                            renderPredictions(allPredictions);
                            // Keep connection open for more predictions
                        } else if (data.type === 'processing_complete') {
                            // Processing is complete - hide loading indicator
                            console.log('Processing complete');
                            const analysisContainer = document.getElementById('analysisLoadingContainer');
                            if (analysisContainer && allPredictions.length > 0) {
                                // Update status to show completion
                                const statusText = document.getElementById('statusText');
                                if (statusText) {
                                    statusText.textContent = 'Completed';
                                }
                                // Status already updated in renderPredictions
                            }
                            // Don't close connection, keep it open for any late predictions
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

            // Connect when page loads only if processing (not if completed)
            if (matchStatus === 'processing') {
                connectWebSocket();
            }

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

    // Start/Stop Processing Button Handler
    const processingBtn = document.getElementById('processingBtn');
    const statusCard = document.getElementById('statusCard');
    const statusText = document.getElementById('statusText');
    const matchId = {{ $match->id ?? 0 }};
    let currentMatchStatus = '{{ isset($match->status) ? $match->status : "pending" }}';

    if (processingBtn) {
        processingBtn.addEventListener('click', function() {
            const isProcessing = currentMatchStatus === 'processing';
            const action = isProcessing ? 'stop' : 'start';
            const actionUrl = isProcessing 
                ? `/matches/${matchId}/stop-processing`
                : `/matches/${matchId}/start-processing`;

            // Disable button during request
            processingBtn.disabled = true;
            const originalHTML = processingBtn.innerHTML;
            processingBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i> <span>Processing...</span>';

            fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button state
                    if (action === 'start') {
                        // Change to Stop button with red/danger styling
                        processingBtn.className = 'inline-flex items-center justify-center gap-2 px-5 py-2.5 text-white rounded-lg font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5 bg-red-600 hover:bg-red-700';
                        processingBtn.style.background = ''; // Clear inline style if any
                        processingBtn.innerHTML = '<i class="fas fa-stop text-sm"></i> <span>Stop Processing</span>';
                        
                        // Update status display
                        if (statusCard) {
                            const iconContainer = statusCard.querySelector('.w-8.h-8');
                            if (iconContainer) {
                                iconContainer.className = 'w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center';
                                iconContainer.innerHTML = '<i class="fas fa-spinner fa-spin text-blue-600 text-xs"></i>';
                            }
                            statusCard.className = 'flex items-center gap-2 p-2.5 bg-white rounded-lg border border-blue-300 hover:border-blue-400 transition-colors';
                        }
                        if (statusText) {
                            statusText.textContent = 'Processing';
                        }
                        
                        // Update video header badge if exists
                        const videoBadge = document.querySelector('.info-card .bg-blue-100');
                        if (videoBadge) {
                            videoBadge.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Processing';
                        }

                        // Update current status immediately
                        currentMatchStatus = 'processing';

                        Swal.fire({
                            icon: 'success',
                            title: 'Processing Started',
                            text: 'Match has been sent to AI model. Processing is now in progress.',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    } else {
                        // Processing stopped - hide the button (status is now completed)
                        processingBtn.style.display = 'none';
                        
                        // Update status display to completed
                        if (statusCard) {
                            const iconContainer = statusCard.querySelector('.w-8.h-8');
                            if (iconContainer) {
                                iconContainer.className = 'w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center';
                                iconContainer.innerHTML = '<i class="fas fa-check-circle text-green-600 text-xs"></i>';
                            }
                            statusCard.className = 'flex items-center gap-2 p-2.5 bg-white rounded-lg border border-green-300 hover:border-green-400 transition-colors';
                        }
                        if (statusText) {
                            statusText.textContent = 'Completed';
                        }
                        
                        // Update video header badge if exists
                        const videoBadge = document.querySelector('.info-card .bg-blue-100');
                        if (videoBadge) {
                            videoBadge.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Completed';
                            videoBadge.className = 'px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold';
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Processing Stopped',
                            text: 'AI model processing has been stopped and marked as completed.',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }

                    // Update current status variable for next click
                    currentMatchStatus = action === 'start' ? 'processing' : 'completed';
                } else {
                    // Show error
                    Swal.fire({
                        icon: 'error',
                        title: action === 'start' ? 'Failed to Start' : 'Failed to Stop',
                        text: data.message || (action === 'start' ? 'Failed to start processing. Something went wrong.' : 'Failed to stop processing or something went wrong.'),
                        confirmButtonColor: '#ef4444'
                    });
                    
                    // Restore button
                    processingBtn.innerHTML = originalHTML;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: action === 'start' ? 'Failed to start processing. Please try again.' : 'Failed to stop processing. Please try again.',
                    confirmButtonColor: '#ef4444'
                });
                
                // Restore button
                processingBtn.innerHTML = originalHTML;
            })
            .finally(() => {
                processingBtn.disabled = false;
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
    const predictions = allPredictions;
    const name = `{{ $match->name ?? 'match' }}`;
    const data = JSON.stringify(predictions, null, 2);
    const blob = new Blob([data], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${name}_predictions.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
@endpush
@endsection

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
            <span class="font-medium">{{ __('admin.back_to_matches') }}</span>
        </a>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $match->name ?? __('admin.match_details') }}</h1>
                <p class="text-sm text-gray-500">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    {{ __('admin.uploaded_on') }} {{ isset($match->created_at) ? $match->created_at->format('F d, Y \a\t h:i A') : 'N/A' }}
        </p>
    </div>

            <div class="flex gap-3">
                <!-- Start/Stop Processing Button -->
                @if(isset($match->status) && $match->status === 'pending')
                    <button id="processingBtn" type="button" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-white rounded-lg font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fas fa-play text-sm"></i>
                        <span>{{ __('admin.start_processing_button') }}</span>
                    </button>
                @elseif(isset($match->status) && $match->status === 'processing')
                    <button id="processingBtn" type="button" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-white rounded-lg font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5 bg-red-600 hover:bg-red-700">
                        <i class="fas fa-stop text-sm"></i>
                        <span>{{ __('admin.stop_processing_button') }}</span>
                    </button>
                @elseif(isset($match->status) && $match->status === 'completed')
                    <!-- Button hidden when completed -->
                    <button id="processingBtn" type="button" style="display: none;"></button>
                @endif

                <a href="{{ route('matches.edit', $match->id ?? 1) }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-white rounded-lg font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                    <i class="fas fa-edit text-sm"></i>
                    <span>{{ __('admin.edit_match') }}</span>
                </a>

                <form id="deleteMatchForm" action="{{ route('matches.destroy', $match->id ?? 1) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                        <i class="fas fa-trash text-sm"></i>
                        <span>{{ __('admin.delete_match_button') }}</span>
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
                        {{ __('admin.match_video') }}
                    </h2>
                    @if(isset($match->status) && $match->status === 'processing')
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-semibold">
                            <i class="fas fa-spinner fa-spin mr-1"></i>
                            {{ __('admin.processing') }}
                        </span>
                    @elseif(isset($match->status) && $match->status === 'pending')
                        <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-semibold">
                            <i class="fas fa-clock mr-1"></i>
                            {{ __('admin.pending') }}
                        </span>
                    @elseif(isset($match->status) && $match->status === 'uploading')
                        <span class="px-3 py-1 bg-cyan-100 text-cyan-700 rounded-full text-xs font-semibold">
                            <i class="fas fa-cloud-upload-alt fa-spin mr-1"></i>
                            {{ __('admin.uploading') }}
                        </span>
                    @elseif(isset($match->status) && $match->status === 'failed')
                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                            <i class="fas fa-times-circle mr-1"></i>
                            {{ __('admin.failed') }}
                        </span>
                    @endif
                </div>

                <div class="relative bg-black rounded-xl overflow-hidden shadow-2xl max-w-3xl mx-auto">
                @if(isset($isExternalVideo) && $isExternalVideo && isset($embedUrl))
                    <!-- YouTube/Vimeo Embed -->
                    <div class="aspect-video w-full">
                        <iframe
                            id="player"
                            src="{{ $embedUrl }}"
                            class="w-full h-full"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                            loading="lazy">
                        </iframe>
                    </div>
                @elseif(isset($matchVideoUrl) || isset($match->video_url))
                    <!-- Regular Video File -->
                    <video id="player" class="w-full" playsinline controls data-poster="">
                        <source src="{{ $matchVideoUrl ?? $match->video_url }}" type="video/mp4">
                        <source src="{{ $matchVideoUrl ?? $match->video_url }}" type="video/webm">
                        Your browser does not support the video tag.
                    </video>
                @else
                    <div class="aspect-video flex items-center justify-center bg-gradient-to-br from-gray-900 to-black">
                        <div class="text-center text-white">
                            <i class="fas fa-video text-6xl opacity-30 mb-4"></i>
                            <p class="text-lg opacity-70 font-medium">{{ __('admin.video_not_available') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

                <!-- Match Info (Under Video) - Compact Design -->
            <div class="info-card rounded-2xl p-5 shadow-lg">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-indigo-500 text-base"></i>
                    <span>{{ __('admin.match_information') }}</span>
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
                                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-spinner fa-spin text-indigo-600 text-xs"></i>
                                </div>
                            @elseif(isset($match->status) && $match->status === 'pending')
                                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-clock text-amber-600 text-xs"></i>
                                </div>
                            @elseif(isset($match->status) && $match->status === 'uploading')
                                <div class="w-8 h-8 bg-cyan-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-cloud-upload-alt fa-spin text-cyan-600 text-xs"></i>
                                </div>
                            @elseif(isset($match->status) && $match->status === 'failed')
                                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-times-circle text-red-600 text-xs"></i>
                                </div>
                            @else
                                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-clock text-amber-600 text-xs"></i>
                                </div>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 font-medium truncate">{{ __('admin.status') }}</p>
                            <p id="statusText" class="text-xs font-bold text-gray-900 truncate">
                                {{ isset($match->status) ? __('admin.' . strtolower($match->status)) : __('admin.pending') }}
                            </p>
                        </div>
                    </div>

                    <!-- Type -->
                    <div class="flex items-center gap-2 p-2.5 bg-white rounded-lg border border-gray-200 hover:border-indigo-300 transition-colors">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas {{ isset($match->type) && $match->type === 'url' ? 'fa-link' : 'fa-file-video' }} text-indigo-600 text-xs"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 font-medium truncate">{{ __('admin.type_label') }}</p>
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
                            <p class="text-xs text-gray-500 font-medium truncate">{{ __('admin.size') }}</p>
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
                            <p class="text-xs text-gray-500 font-medium truncate">{{ __('admin.created_label') }}</p>
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
                            <p class="text-xs text-gray-500 font-medium truncate">{{ __('admin.uploaded_by') }}</p>
                            <p class="text-xs font-bold text-gray-900 truncate">{{ $match->user->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Used Disk -->
                    <div class="flex items-center gap-2 p-2.5 bg-white rounded-lg border border-gray-200 hover:border-blue-300 transition-colors">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-server text-blue-600 text-xs"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 font-medium truncate">{{ __('admin.storage') }}</p>
                            <p class="text-xs font-bold text-gray-900 truncate">{{ $usedDisk ?? 'Public' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                @if(isset($match->description) && !empty($match->description))
                <div class="mb-4 p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
                    <div class="flex items-start gap-2 mb-2">
                        <i class="fas fa-align-left text-indigo-600 text-sm mt-0.5"></i>
                        <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wide">{{ __('admin.description') }}</h3>
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $match->description }}</p>
                </div>
                @endif

                <!-- Tags -->
                @if(isset($match->tags) && !empty($match->tags))
                <div class="flex flex-wrap items-center gap-2">
                    <div class="flex items-center gap-1.5 mr-1">
                        <i class="fas fa-hashtag text-indigo-600 text-xs"></i>
                        <span class="text-xs font-semibold text-gray-700">{{ __('admin.tags') }}</span>
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
                    {{ __('admin.match_analysis') }}
                </h2>

                    <!-- Analysis Container (will be populated by JavaScript) -->
                    <div id="analysisLoadingContainer" class="space-y-4">
                        @if(isset($match->status) && $match->status === 'completed' && (!$match->predictions || $match->predictions->count() === 0))
                            <!-- Completed but no predictions -->
                            <div class="text-center py-8">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full mb-4 shadow-lg">
                                    <i class="fas fa-info-circle text-2xl text-gray-600"></i>
                                </div>
                                <p class="text-gray-800 font-bold text-base mb-1">{{ __('admin.no_predictions_available') }}</p>
                                <p class="text-xs text-gray-500">{{ __('admin.no_predictions_generated') }}</p>
                            </div>
                        @elseif(isset($match->status) && $match->status === 'pending')
                            <!-- Pending state - show message that nothing is running -->
                            <div class="text-center py-8">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-yellow-100 to-orange-100 rounded-full mb-4 shadow-lg">
                                    <i class="fas fa-clock text-2xl text-yellow-600"></i>
                                </div>
                                <p class="text-gray-800 font-bold text-base mb-1">{{ __('admin.match_pending_message') }}</p>
                                <p class="text-xs text-gray-500">{{ __('admin.nothing_running_background') }}</p>
                            </div>
                        @elseif(isset($match->status) && $match->status === 'processing')
                            <!-- Loading state (only show if processing) -->
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
                                <span class="ml-2 text-sm text-gray-500 font-medium">{{ __('admin.processing_analysis') }}</span>
                            </div>
                        @else
                            <!-- Will be populated by JavaScript if predictions exist -->
                        @endif
                    </div>
            </div>
        </div>
    </div>
</div>

<!-- Video Clip Modal -->
<div id="clipVideoModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 id="clipModalTitle" class="text-xl font-bold text-gray-900">{{ __('admin.match_video') }}</h3>
            <button id="closeClipModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <div class="flex-1 p-6 overflow-auto">
            <video id="clipVideoPlayer" class="w-full rounded-lg" controls>
                <source id="clipVideoSource" src="" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
<script>
// Translations
const translations = {
    waitingForPredictions: '{{ __('admin.waiting_for_predictions') }}',
    processingMatchVideo: '{{ __('admin.processing_match_video') }}',
    foundPredictions: '{{ __('admin.found_predictions') }}',
    predictionNumber: '{{ __('admin.prediction_number') }}',
    clip: '{{ __('admin.clip') }}',
    firstModelAccuracy: '{{ __('admin.first_model_accuracy') }}',
    model0Prediction: '{{ __('admin.model_0_prediction') }}',
    model1Prediction: '{{ __('admin.model_1_prediction') }}',
    offenceSeverity: '{{ __('admin.offence_severity') }}',
    noData: '{{ __('admin.no_data') }}',
    matchPendingMessage: '{{ __('admin.match_pending_message') }}',
    nothingRunningBackground: '{{ __('admin.nothing_running_background') }}'
};

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Plyr video player with smaller size
    const video = document.getElementById('player');
    let player = null; // Make player accessible to other functions
    if (video) {
        player = new Plyr(video, {
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
        $matchStatus = $match->status ?? 'pending';
        $hasPredictions = isset($predictions) && $predictions->count() > 0;
        $predictionsData = isset($predictions) ? $predictions->toArray() : [];
    @endphp

    const predictionsData = @json($predictionsData);
    const hasPredictions = {{ $hasPredictions ? 'true' : 'false' }};
    let matchStatus = '{{ $matchStatus }}'; // Make it mutable so we can update it
    let allPredictions = hasPredictions ? predictionsData : [];

    // Render predictions function
    function renderPredictions(predictions) {
        const analysisContainer = document.getElementById('analysisLoadingContainer');
        if (!analysisContainer) return;

        if (predictions.length === 0) {
            // Check status and show appropriate message
            if (matchStatus === 'completed') {
                // Completed but no predictions
                analysisContainer.innerHTML = `
                    <div class="space-y-4">
                        <div class="text-center py-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full mb-4 shadow-lg">
                                <i class="fas fa-info-circle text-2xl text-gray-600"></i>
                            </div>
                            <p class="text-gray-800 font-bold text-base mb-1">{{ __('admin.no_predictions_available') }}</p>
                            <p class="text-xs text-gray-500">{{ __('admin.no_predictions_generated') }}</p>
                        </div>
                    </div>
                `;
            } else if (matchStatus === 'pending') {
                // Pending - show message that nothing is running
                analysisContainer.innerHTML = `
                    <div class="space-y-4">
                        <div class="text-center py-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-yellow-100 to-orange-100 rounded-full mb-4 shadow-lg">
                                <i class="fas fa-clock text-2xl text-yellow-600"></i>
                            </div>
                            <p class="text-gray-800 font-bold text-base mb-1">${translations.matchPendingMessage}</p>
                            <p class="text-xs text-gray-500">${translations.nothingRunningBackground}</p>
                        </div>
                    </div>
                `;
            } else {
                // Processing - show loading state
                analysisContainer.innerHTML = `
                    <div class="space-y-4">
                        <div class="text-center py-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-full mb-4 shadow-lg">
                                <i class="fas fa-spinner fa-spin text-2xl text-purple-600"></i>
                            </div>
                            <p class="text-gray-800 font-bold text-base mb-1">${translations.waitingForPredictions}</p>
                            <p class="text-xs text-gray-500">${translations.processingMatchVideo}</p>
                        </div>
                    </div>
                `;
            }
            return;
        }

        // Build predictions HTML
        let predictionsHtml = '<div class="space-y-4">';
        predictionsHtml += `<div class="mb-4"><h3 class="text-lg font-bold text-gray-900">${translations.foundPredictions.replace(':count', predictions.length)}</h3></div>`;

        predictions.forEach((prediction, index) => {
            predictionsHtml += `
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-lg p-4 border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold text-gray-900">${translations.predictionNumber.replace(':number', index + 1)}</h4>
                        ${prediction.relative_time ? `<span class="text-xs text-gray-500"><i class="fas fa-clock mr-1"></i>${prediction.relative_time}</span>` : ''}
                    </div>
                    ${prediction.clip_path ? `<div class="mb-2 text-xs text-gray-600 flex items-center"><i class="fas fa-video mr-1"></i><span style="15%">${translations.clip}</span><a style="overflow-wrap: anywhere" href="#" class="clip-path-link ml-2" data-clip-title="${translations.predictionNumber.replace(':number', index + 1)}" data-clip-url="${prediction.url || prediction.clip_path}">${prediction.clip_path}</a></div>` : ''}
                    ${prediction.first_model_prop !== null ? `<div class="mb-2 text-xs"><span class="font-medium">${translations.firstModelAccuracy}</span> <span class="text-purple-600">${(prediction.first_model_prop * 100).toFixed(2)}%</span></div>` : ''}

                    ${prediction.prediction_0 ? `
                        <div class="mb-3 p-3 bg-purple-50 rounded border border-purple-200">
                            <div class="font-medium text-xs text-purple-900 mb-2">${translations.model0Prediction}</div>
                            ${renderPredictionData(prediction.prediction_0)}
                        </div>
                    ` : ''}

                    ${prediction.prediction_1 ? `
                        <div class="mb-3 p-3 bg-indigo-50 rounded border border-indigo-200">
                            <div class="font-medium text-xs text-indigo-900 mb-2">${translations.model1Prediction}</div>
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
        if (!prediction) return '<div class="text-xs text-gray-500">' + translations.noData + '</div>';

        let html = '';

        // Check if labels are available (formatted data from backend)
        if (prediction.labels) {
            html += '<div class="text-xs space-y-2">';

            // Offence Severity
            if (prediction.labels.offence_severity) {
                const severityAcc = prediction.acc && prediction.acc[0] && prediction.acc[0][0]
                    ? (prediction.acc[0][0] * 100).toFixed(2) + '%'
                    : 'N/A';
                html += `
                    <div class="flex justify-between items-center p-2 bg-white rounded border border-gray-200">
                        <div>
                            <div class="font-semibold text-gray-900">${prediction.labels.offence_severity}</div>
                            <div class="text-gray-500 text-xs mt-0.5">${translations.offenceSeverity.replace(':class', prediction.labels.offence_severity_class || 'N/A')}</div>
                        </div>
                        <span class="font-bold text-purple-600 ml-2">${severityAcc}</span>
                    </div>
                `;
            }

            // Action (Reason)
            if (prediction.labels.action) {
                const actionAcc = prediction.acc && prediction.acc[0] && prediction.acc[0][1]
                    ? (prediction.acc[0][1] * 100).toFixed(2) + '%'
                    : 'N/A';
                html += `
                    <div class="flex justify-between items-center p-2 bg-white rounded border border-gray-200">
                        <div>
                            <div class="font-semibold text-gray-900">${prediction.labels.action}</div>
                            <div class="text-gray-500 text-xs mt-0.5">Action/Reason (Class ${prediction.labels.action_class || 'N/A'})</div>
                        </div>
                        <span class="font-bold text-purple-600 ml-2">${actionAcc}</span>
                    </div>
                `;
            }

            html += '</div>';
        } else if (prediction.classes && Array.isArray(prediction.classes[0])) {
            // Fallback: show raw class IDs if labels are not available
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

    // Function to fetch predictions from server via AJAX (fallback if WebSocket fails)
    function fetchPredictionsFromServer() {
        const currentMatchId = {{ $match->id ?? 0 }};

        // Simply reload the page to get fresh predictions
        // This is a fallback - WebSocket should handle real-time updates
        console.log('Fetching predictions from server (fallback)...');

        // Option 1: Reload page (simplest, ensures everything is synced)
        // window.location.reload();

        // Option 2: Fetch and parse (more complex but no page reload)
        fetch(`/matches/${currentMatchId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Try to extract predictions from the page HTML
            const match = html.match(/const predictionsData = (\[[\s\S]*?\]);/);
            if (match) {
                try {
                    const fetchedPredictions = JSON.parse(match[1]);
                    if (fetchedPredictions && fetchedPredictions.length > 0) {
                        // Merge with existing predictions (avoid duplicates)
                        let updated = false;
                        fetchedPredictions.forEach(pred => {
                            const exists = allPredictions.some(existing =>
                                existing.id === pred.id ||
                                (existing.relative_time === pred.relative_time && existing.clip_path === pred.clip_path)
                            );
                            if (!exists) {
                                allPredictions.push(pred);
                                updated = true;
                            }
                        });

                        if (updated) {
                            // Re-render with all predictions
                            renderPredictions(allPredictions);
                            console.log('Predictions updated from server via AJAX');
                        }
                    }
                } catch (e) {
                    console.error('Error parsing predictions from server:', e);
                }
            }
        })
        .catch(error => {
            console.error('Error fetching predictions from server:', error);
        });
    }

    // If predictions exist, render them directly
    if (hasPredictions) {
        renderPredictions(allPredictions);
    } else {
        // No predictions - render empty state (with status check)
        renderPredictions([]);

        // If processing, also set up periodic polling as fallback (every 10 seconds)
        if (matchStatus === 'processing') {
            const pollInterval = setInterval(() => {
                if (matchStatus === 'processing' && allPredictions.length === 0) {
                    fetchPredictionsFromServer();
                } else {
                    clearInterval(pollInterval);
                }
            }, 10000); // Poll every 10 seconds

            // Clear interval when page unloads
            window.addEventListener('beforeunload', () => {
                clearInterval(pollInterval);
            });
        }
    }

    // Connect to WebSocket if processing or pending (to receive completion notifications)
    // Also connect if processing to receive predictions
    if (matchStatus === 'processing' || matchStatus === 'pending') {
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
                            // Received new prediction - add to array and re-render immediately
                            console.log('Received prediction via WebSocket:', data.prediction);

                            // Check if prediction already exists (avoid duplicates)
                            const exists = allPredictions.some(p =>
                                p.id === data.prediction.id ||
                                (p.relative_time === data.prediction.relative_time && p.clip_path === data.prediction.clip_path)
                            );

                            if (!exists) {
                                allPredictions.push(data.prediction);
                            }

                            // Immediately render predictions - this will hide loading state and show predictions
                            renderPredictions(allPredictions);

                            // Update status badge if this is the first prediction
                            if (allPredictions.length === 1) {
                                const statusText = document.getElementById('statusText');
                                if (statusText) {
                                    statusText.textContent = 'Processing';
                                }
                            }

                            // Keep connection open for more predictions
                        } else if (data.type === 'processing_complete') {
                            // Processing is complete - update status and fetch any remaining predictions
                            console.log('Processing complete via WebSocket (match channel)');

                            // Use the global function to update status
                            if (window.updateMatchStatusToCompleted) {
                                window.updateMatchStatusToCompleted();
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
                title: '{{ __('admin.delete_confirmation_title') }}',
                text: '{{ __('admin.delete_confirmation_text') }}',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '{{ __('admin.delete_confirmation_yes') }}',
                cancelButtonText: '{{ __('admin.delete_confirmation_cancel') }}'
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

    // Function to update match status to completed (called from notification handler)
    window.updateMatchStatusToCompleted = function() {
        console.log('Updating match status to completed via notification');

        // Update status variables
        currentMatchStatus = 'completed';
        matchStatus = 'completed';

        // Update status card
        if (statusCard) {
            const iconContainer = statusCard.querySelector('.w-8.h-8');
            if (iconContainer) {
                iconContainer.className = 'w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center';
                iconContainer.innerHTML = '<i class="fas fa-check-circle text-green-600 text-xs"></i>';
            }
            statusCard.className = 'flex items-center gap-2 p-2.5 bg-white rounded-lg border border-green-300 hover:border-green-400 transition-colors';
        }

        // Update status text
        if (statusText) {
            statusText.textContent = 'Completed';
        }

        // Update video header badge
        const videoBadge = document.querySelector('.px-3.py-1.bg-indigo-100, .px-3.py-1.bg-cyan-100, .px-3.py-1.bg-amber-100, .px-3.py-1.bg-blue-100');
        if (videoBadge) {
            videoBadge.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Completed';
            videoBadge.className = 'px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold';
        }

        // Hide processing button
        if (processingBtn) {
            processingBtn.style.display = 'none';
        }

        // Fetch and render predictions if not already rendered
        fetchPredictionsFromServer();

        // Update analysis container if no predictions yet
        const analysisContainer = document.getElementById('analysisLoadingContainer');
        if (analysisContainer && allPredictions.length === 0) {
            renderPredictions([]); // This will show "no predictions" message for completed status
        }
    };

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
                        // Play the video player when starting processing
                        if (player) {
                            player.play().catch(err => {
                                console.log('Auto-play prevented or error:', err);
                                // If autoplay is prevented, try to play after a short delay
                                setTimeout(() => {
                                    player.play().catch(e => console.log('Delayed play also prevented:', e));
                                }, 500);
                            });
                        }

                        // Change to Stop button with red/danger styling
                        processingBtn.className = 'inline-flex items-center justify-center gap-2 px-5 py-2.5 text-white rounded-lg font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5 bg-red-600 hover:bg-red-700';
                        processingBtn.style.background = ''; // Clear inline style if any
                        processingBtn.innerHTML = '<i class="fas fa-stop text-sm"></i> <span>Stop Processing</span>';

                        // Update status display
                        if (statusCard) {
                            const iconContainer = statusCard.querySelector('.w-8.h-8');
                            if (iconContainer) {
                                iconContainer.className = 'w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center';
                                iconContainer.innerHTML = '<i class="fas fa-spinner fa-spin text-indigo-600 text-xs"></i>';
                            }
                            statusCard.className = 'flex items-center gap-2 p-2.5 bg-white rounded-lg border border-indigo-300 hover:border-indigo-400 transition-colors';
                        }
                        if (statusText) {
                            statusText.textContent = 'Processing';
                        }

                        // Update video header badge if exists
                        const videoBadge = document.querySelector('.px-3.py-1.bg-indigo-100, .px-3.py-1.bg-cyan-100, .px-3.py-1.bg-amber-100, .px-3.py-1.bg-blue-100');
                        if (videoBadge) {
                            videoBadge.className = 'px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-semibold';
                            videoBadge.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Processing';
                        }

                        // Update current status immediately
                        currentMatchStatus = 'processing';
                        matchStatus = 'processing'; // Update matchStatus for renderPredictions

                        // Update the analysis container to show processing state
                        const analysisContainer = document.getElementById('analysisLoadingContainer');
                        if (analysisContainer) {
                            analysisContainer.innerHTML = `
                                <div class="space-y-4">
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
                                    </div>
                                </div>
                            `;
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Processing Started',
                            text: 'Match has been sent to AI model. Processing is now in progress.',
                            timer: 3000,
                            showConfirmButton: false
                        }).then(() => {
                            // Refresh page after 1 second to ensure all data is synced
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
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
                        const videoBadge = document.querySelector('.px-3.py-1.bg-amber-100, .px-3.py-1.bg-blue-100');
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

                    // If start failed, restore pending state in analysis container
                    if (action === 'start') {
                        matchStatus = 'pending'; // Reset to pending
                        const analysisContainer = document.getElementById('analysisLoadingContainer');
                        if (analysisContainer) {
                            analysisContainer.innerHTML = `
                                <div class="space-y-4">
                                    <div class="text-center py-8">
                                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-yellow-100 to-orange-100 rounded-full mb-4 shadow-lg">
                                            <i class="fas fa-clock text-2xl text-yellow-600"></i>
                                        </div>
                                        <p class="text-gray-800 font-bold text-base mb-1">${translations.matchPendingMessage || 'This match is pending and has not been processed yet.'}</p>
                                        <p class="text-xs text-gray-500">${translations.nothingRunningBackground || 'Nothing is running in the background. Please start processing to analyze this match.'}</p>
                                    </div>
                                </div>
                            `;
                        }
                    }

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

                // If start failed, restore pending state in analysis container
                if (action === 'start') {
                    matchStatus = 'pending'; // Reset to pending
                    const analysisContainer = document.getElementById('analysisLoadingContainer');
                    if (analysisContainer) {
                        analysisContainer.innerHTML = `
                            <div class="space-y-4">
                                <div class="text-center py-8">
                                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-yellow-100 to-orange-100 rounded-full mb-4 shadow-lg">
                                        <i class="fas fa-clock text-2xl text-yellow-600"></i>
                                    </div>
                                    <p class="text-gray-800 font-bold text-base mb-1">${translations.matchPendingMessage || 'This match is pending and has not been processed yet.'}</p>
                                    <p class="text-xs text-gray-500">${translations.nothingRunningBackground || 'Nothing is running in the background. Please start processing to analyze this match.'}</p>
                                </div>
                            </div>
                        `;
                    }
                }

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

// Handle clip path clicks - show video modal
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('clip-path-link')) {
        e.preventDefault();
        const clipUrl = e.target.getAttribute('data-clip-url');
        const clipTitle = e.target.getAttribute('data-clip-title');

        if (clipUrl) {
            const modal = document.getElementById('clipVideoModal');
            const videoSource = document.getElementById('clipVideoSource');
            const videoPlayer = document.getElementById('clipVideoPlayer');
            const modalTitle = document.getElementById('clipModalTitle');

            // Set modal title
            if (clipTitle && modalTitle) {
                modalTitle.textContent = clipTitle;
            }

            // Set video source
            videoSource.src = clipUrl;
            videoPlayer.load();
            modal.classList.remove('hidden');

            // Play video when modal opens
            videoPlayer.play().catch(err => {
                console.log('Auto-play prevented:', err);
            });
        }
    }
});

// Close modal handlers
const closeClipModal = document.getElementById('closeClipModal');
const clipVideoModal = document.getElementById('clipVideoModal');

if (closeClipModal) {
    closeClipModal.addEventListener('click', function() {
        const videoPlayer = document.getElementById('clipVideoPlayer');
        if (videoPlayer) {
            videoPlayer.pause();
            videoPlayer.currentTime = 0;
        }
        clipVideoModal.classList.add('hidden');
    });
}

// Close modal on backdrop click
if (clipVideoModal) {
    clipVideoModal.addEventListener('click', function(e) {
        if (e.target === clipVideoModal) {
            const videoPlayer = document.getElementById('clipVideoPlayer');
            if (videoPlayer) {
                videoPlayer.pause();
                videoPlayer.currentTime = 0;
            }
            clipVideoModal.classList.add('hidden');
        }
    });
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !clipVideoModal.classList.contains('hidden')) {
        const videoPlayer = document.getElementById('clipVideoPlayer');
        if (videoPlayer) {
            videoPlayer.pause();
            videoPlayer.currentTime = 0;
        }
        clipVideoModal.classList.add('hidden');
    }
});
</script>
@endpush
@endsection

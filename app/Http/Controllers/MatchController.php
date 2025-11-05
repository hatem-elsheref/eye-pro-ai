<?php

namespace App\Http\Controllers;

use App\Services\MatchService;
use App\Services\AIModelService;
use App\Services\NotificationService;
use App\Models\MatchVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MatchController extends Controller
{
    protected $matchService;
    protected $aiModelService;
    protected $notificationService;

    public function __construct(
        MatchService $matchService,
        AIModelService $aiModelService,
        NotificationService $notificationService
    ) {
        $this->matchService = $matchService;
        $this->aiModelService = $aiModelService;
        $this->notificationService = $notificationService;
    }

    /**
     * Generate storage URL with proper handling for S3 (pre-signed URLs)
     */
    protected function generateStorageUrl(string $disk, string $path): string
    {
        if ($disk === 'public' || $disk === 'local') {
            return Storage::disk($disk)->url($path);
        }

        // For S3 and other cloud storage, use pre-signed URLs if bucket is private
        try {
            $storage = Storage::disk($disk);

            // Check if we should use pre-signed URLs (default: true for S3)
            $usePreSigned = env('S3_USE_PRESIGNED_URLS', true);

            if ($usePreSigned && $disk === 's3') {
                // Generate pre-signed URL that expires in 1 hour (3600 seconds)
                return $storage->temporaryUrl($path, now()->addHours(1));
            }

            // Fallback to regular URL (works if bucket has public read access)
            return $storage->url($path);
        } catch (\Exception $e) {
            Log::error('Failed to generate storage URL', [
                'disk' => $disk,
                'path' => $path,
                'error' => $e->getMessage()
            ]);

            // Fallback to regular URL attempt
            return Storage::disk($disk)->url($path);
        }
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();

        $query = MatchVideo::query();

        if (!$authUser->is_admin){
            $query = $query->where('user_id', $authUser->id);
        }

        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            if (!empty($search)) {
                $query->where('name', 'like', '%' . $search . '%');
            }
        }

        $matches = $query->latest()->paginate(9)->withQueryString();

        return view('admin.matches.index', [
            'matches' => $matches,
            'accountPending' => !auth()->user()->is_approved,
        ]);
    }

    public function create()
    {
        if (!auth()->user()->is_approved) {
            return redirect()->route('matches.index')
                ->with('warning', 'Your account must be approved before you can upload matches.');
        }

        return view('admin.matches.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->is_approved) {
            return redirect()->route('matches.index')
                ->with('error', __('admin.account_must_be_approved'));
        }

        $validated = $request->validate([
            'match_name' => 'required|string|max:255',
            'video_file' => 'nullable|file|mimes:mp4,avi,mov,mkv,webm|max:2048000',
            'video_url' => 'nullable|url|max:500',
        ]);

        try {
            $user = auth()->user();

            if ($request->hasFile('video_file')) {
                $match = $this->matchService->createFromFile(
                    $user,
                    $validated['match_name'],
                    $request->file('video_file')
                );
            } elseif (!empty($validated['video_url'])) {
                $match = $this->matchService->createFromUrl(
                    $user,
                    $validated['match_name'],
                    $validated['video_url']
                );
            } else {
                return back()->with('error', __('admin.please_provide_file_or_url'));
            }

            // For URL uploads, send upload success notification (not processing started)
            // For file uploads, notifications are handled by the upload flow
            if ($match->type === 'url') {
                $this->notificationService->notifyUploadSuccess($match);
            }

            return redirect()->route('matches.show', $match->id)
                ->with('success', __('admin.match_uploaded_success'));
        } catch (\Exception $e) {
            Log::error('Failed to create match', ['error' => $e->getMessage()]);
            return back()->with('error', __('admin.failed_to_upload', ['error' => $e->getMessage()]));
        }
    }

    public function show($id)
    {
        $user = auth()->user();

        $match = MatchVideo::with(['user', 'predictions'])
            ->where(function ($query) use ($user) {
                if (!$user->is_admin)
                    $query->where('user_id', $user->id);
            })->findOrFail($id);

        $usedDisk = ucfirst($match->storage_disk ?? 'public');

        // Regenerate match video URL if it's a file stored on S3 or other cloud storage
        // This ensures pre-signed URLs are fresh on each page load
        $matchVideoUrl = $match->video_url;
        $isExternalVideo = false;
        $embedUrl = null;

        if ($match->status === 'pending' && $match->type === 'file' && $match->video_path && $match->storage_disk) {
            try {
                $matchVideoUrl = $this->generateStorageUrl($match->storage_disk, $match->video_path);
            } catch (\Exception $e) {
                Log::warning('Failed to regenerate match video URL', [
                    'match_id' => $match->id,
                    'video_path' => $match->video_path,
                    'disk' => $match->storage_disk,
                    'error' => $e->getMessage()
                ]);
                // Keep original URL if regeneration fails
            }
        } elseif ($match->type === 'url' && $match->video_url) {
            // Check if it's a YouTube or Vimeo URL
            $videoInfo = $this->parseExternalVideoUrl($match->video_url);
            if ($videoInfo['is_external']) {
                $isExternalVideo = true;
                $embedUrl = $videoInfo['embed_url'];
            }
        }

        // Get the storage disk for clips (use same disk as match, or check for clip-specific disk)
        $clipStorageDisk = env('CLIP_STORAGE_DISK', $match->storage_disk ?? 'public');

        // Format predictions with labels based on current locale
        // Regenerate clip URLs on each page load to ensure fresh pre-signed URLs
        $predictions = $match->predictions->map(function ($prediction) use ($clipStorageDisk) {
            $locale = app()->getLocale();

            // Generate fresh URL from clip_path if it exists (regenerated on each page load)
            $clipUrl = null;
            if ($prediction->clip_path) {
                try {
                    $clipUrl = $this->generateStorageUrl($clipStorageDisk, $prediction->clip_path);
                } catch (\Exception $e) {
                    Log::warning('Failed to regenerate clip URL', [
                        'prediction_id' => $prediction->id,
                        'clip_path' => $prediction->clip_path,
                        'disk' => $clipStorageDisk,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return [
                'id' => $prediction->id,
                'match_id' => $prediction->match_id,
                'clip_path' => $prediction->clip_path,
                'url' => $clipUrl,
                'relative_time' => $prediction->relative_time,
                'first_model_prop' => $prediction->first_model_prop,
                'prediction_0' => $prediction->formatPredictionData($prediction->prediction_0, $locale),
                'prediction_1' => $prediction->formatPredictionData($prediction->prediction_1, $locale),
            ];
        });

        return view('admin.matches.show', compact('match', 'usedDisk', 'predictions', 'matchVideoUrl', 'isExternalVideo', 'embedUrl'));
    }

    /**
     * Parse external video URL (YouTube, Vimeo) and convert to embed URL
     */
    protected function parseExternalVideoUrl(string $url): array
    {
        $result = [
            'is_external' => false,
            'embed_url' => null,
            'provider' => null,
            'video_id' => null
        ];

        // YouTube patterns
        $youtubePatterns = [
            // youtube.com/watch?v=VIDEO_ID
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
            // youtube.com/embed/VIDEO_ID
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
            // youtube.com/v/VIDEO_ID
            '/youtube\.com\/v\/([a-zA-Z0-9_-]+)/',
            // youtu.be/VIDEO_ID
            '/youtu\.be\/([a-zA-Z0-9_-]+)/',
            // youtube.com/watch?v=VIDEO_ID&other_params
            '/youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]+)/',
        ];

        // Vimeo patterns
        $vimeoPatterns = [
            // vimeo.com/VIDEO_ID
            '/vimeo\.com\/(\d+)/',
            // vimeo.com/channels/CHANNEL/VIDEO_ID
            '/vimeo\.com\/channels\/[^\/]+\/(\d+)/',
            // vimeo.com/groups/GROUP/videos/VIDEO_ID
            '/vimeo\.com\/groups\/[^\/]+\/videos\/(\d+)/',
        ];

        // Check YouTube
        foreach ($youtubePatterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                $result['is_external'] = true;
                $result['provider'] = 'youtube';
                $result['video_id'] = $matches[1];
                $result['embed_url'] = 'https://www.youtube.com/embed/' . $matches[1] . '?rel=0&modestbranding=1';
                return $result;
            }
        }

        // Check Vimeo
        foreach ($vimeoPatterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                $result['is_external'] = true;
                $result['provider'] = 'vimeo';
                $result['video_id'] = $matches[1];
                $result['embed_url'] = 'https://player.vimeo.com/video/' . $matches[1];
                return $result;
            }
        }

        return $result;
    }

    public function edit($id)
    {
        $match = MatchVideo::where('user_id', auth()->id())->findOrFail($id);

        // Prevent editing while uploading
        if ($match->status === 'uploading') {
            return redirect()->route('matches.show', $match->id)
                ->with('error', __('admin.cannot_edit_during_upload'));
        }

        return view('admin.matches.edit', compact('match'));
    }

    public function update(Request $request, $id)
    {
        $match = MatchVideo::where('user_id', auth()->id())->findOrFail($id);

        // Prevent updating while uploading
        if ($match->status === 'uploading') {
            return back()->with('error', __('admin.cannot_edit_during_upload'));
        }

        $validated = $request->validate([
            'match_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'tags' => 'nullable|string|max:255',
        ]);

        $match->update($validated);

        return redirect()->route('matches.show', $match->id)
            ->with('success', __('admin.match_updated_success'));
    }

    public function destroy($id)
    {
        $match = MatchVideo::where('user_id', auth()->id())->findOrFail($id);

        // Prevent deleting while uploading
        if ($match->status === 'uploading') {
            return back()->with('error', __('admin.cannot_delete_during_upload'));
        }

        $match->delete();

        return redirect()->route('matches.index')
            ->with('success', __('admin.match_deleted_success'));
    }

    public function uploadChunk(Request $request)
    {
        // Increase execution time limit for chunk uploads (2 minutes)
        set_time_limit(120);

        try {
            $validated = $request->validate([
                'uploadId' => 'required|string',
                'chunkIndex' => 'required|integer|min:0',
                'totalChunks' => 'required|integer|min:1',
                'chunk' => 'required|file',
                'fileName' => 'required|string',
                'fileSize' => 'required|integer',
            ]);

            $result = $this->matchService->storeChunk(auth()->user(), $validated);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Failed to upload chunk', [
                'uploadId' => $request->input('uploadId'),
                'chunkIndex' => $request->input('chunkIndex'),
                'error' => $e->getMessage(),
                'userId' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return appropriate status code based on error type
            $statusCode = 400;
            if (str_contains($e->getMessage(), 'timeout') || str_contains($e->getMessage(), 'Gateway')) {
                $statusCode = 504;
            } elseif (str_contains($e->getMessage(), 'Service unavailable')) {
                $statusCode = 503;
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    public function finalizeUpload(Request $request)
    {
        try {
            $validated = $request->validate([
                'uploadId' => 'required|string',
                'match_name' => 'required|string|max:255',
            ]);

            $match = $this->matchService->finalizeUpload(
                auth()->user(),
                $validated['uploadId'],
                $validated['match_name']
            );

            // Notification is sent by the background job when upload completes
            // For S3, we always use background job, so no notification here

            return response()->json([
                'success' => true,
                'message' => __('admin.upload_started_background'),
                'matchId' => $match->id,
                'redirectUrl' => route('matches.show', $match->id),
                'status' => $match->status, // Include status so frontend knows if it's uploading
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to finalize upload', [
                'uploadId' => $validated['uploadId'] ?? 'unknown',
                'error' => $e->getMessage(),
                'userId' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getUploadStatus($uploadId)
    {
        try {
            $result = $this->matchService->getUploadStatus(auth()->user(), $uploadId);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Start processing a match
     */
    public function startProcessing($id)
    {
        try {
            $match = MatchVideo::where('user_id', auth()->id())->findOrFail($id);

            // Prevent starting processing while uploading
            if ($match->status === 'uploading') {
                return response()->json([
                    'success' => false,
                    'message' => __('admin.cannot_start_during_upload')
                ], 400);
            }

            // Check if match is in pending status
            if ($match->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Match is not in pending status. Current status: ' . $match->status
                ], 400);
            }

            DB::beginTransaction();

            // Update status to processing
            $match->update(['status' => 'processing']);

            // Start AI processing
            $result = $this->aiModelService->startProcessing($match->id);

            if ($result['success']) {
                // Send notification
                $this->notificationService->notifyProcessingStarted($match);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Processing started successfully',
                    'status' => 'processing'
                ]);
            } else {
                // Revert status on failure
                $match->update(['status' => 'pending']);

                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to start processing'
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Failed to start match processing', ['matchId' => $id, 'error' => $e->getMessage()]);

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to start processing: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Stop processing a match
     */
    public function stopProcessing($id)
    {
        try {
            $match = MatchVideo::where('user_id', auth()->id())->findOrFail($id);

            // Prevent stopping while uploading
            if ($match->status === 'uploading') {
                return response()->json([
                    'success' => false,
                    'message' => __('admin.cannot_stop_during_upload')
                ], 400);
            }

            // Check if match is in processing status
            if ($match->status !== 'processing') {
                return response()->json([
                    'success' => false,
                    'message' => 'Match is not currently processing. Current status: ' . $match->status
                ], 400);
            }

            // Stop AI processing
            $result = $this->aiModelService->stopProcessing($match->id);

            if ($result['success']) {
                // Update status to completed when stopped
                $match->update(['status' => 'completed']);

                // Send success notification for stopping
                $this->notificationService->notifyProcessingStopped($match, true);

                // Check if match has predictions
                $predictionCount = $match->predictions()->count();

                if ($predictionCount > 0) {
                    // Has predictions - send complete success notification
                    $this->notificationService->notifyAnalysisComplete($match);
                } else {
                    // No predictions - send notification that process ended without predictions
                    $this->notificationService->notifyProcessingEndedWithoutPredictions($match);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Processing stopped successfully',
                    'status' => 'completed',
                    'has_predictions' => $predictionCount > 0
                ]);
            } else {
                // Send failure notification
                $this->notificationService->notifyProcessingStopped($match, false);

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to stop processing'
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Failed to stop match processing', ['matchId' => $id, 'error' => $e->getMessage()]);

            // Send failure notification
            try {
                $match = MatchVideo::where('user_id', auth()->id())->findOrFail($id);
                $this->notificationService->notifyProcessingStopped($match, false);
            } catch (\Exception $notifError) {
                Log::error('Failed to send stop failure notification', ['error' => $notifError->getMessage()]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to stop processing: ' . $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Models\MatchVideo;
use App\Models\Prediction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Helpers\WebSocketHelper;

class MatchApiController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
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

    protected function validateApiKey(Request $request): bool
    {
        $apiKey = $request->header('X-API-Key');
        $expectedApiKey = env('AI_API_KEY');

        if (!$expectedApiKey) {
            Log::error('AI_API_KEY not configured');
            return false;
        }

        return $apiKey === $expectedApiKey;
    }

    public function getMatch($id)
    {
        if (!$this->validateApiKey(request())) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $match = MatchVideo::query()->with('predictions')->find($id);
        if (!$match) {
            return response()->json(['success' => false, 'message' => 'Match not found'], 404);
        }

        $disk = $match->storage_disk ?? 'public';
        $fileInfo = [
            'id'      => $match->id,
            'name'    => $match->name,
            'path'    => $match->video_path,
            'disk'    => $disk,
            'size'    => $match->file_size,
            'url'     => $match->video_url,
            'results' => $match->predictions
        ];

        try {
            if ($match->video_path) {
                try {
                    if (Storage::disk($disk)->exists($match->video_path)) {
                        $fileInfo['size_bytes'] = Storage::disk($disk)->size($match->video_path);
                    }
                } catch (\Exception $e) {
                    // If exists() fails due to permission (e.g., missing s3:ListBucket),
                    // we can't get the size but the file might still be accessible
                    if (str_contains($e->getMessage(), 'AccessDenied') || str_contains($e->getMessage(), 'ListBucket')) {
                        Log::warning('Cannot check file existence (missing s3:ListBucket permission)', [
                            'matchId' => $id,
                            'disk' => $disk
                        ]);
                    } else {
                        throw $e; // Re-throw if it's a different error
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Could not get file size', [
                'matchId' => $id,
                'error' => $e->getMessage()
            ]);
        }

        return response()->json(['success' => true, 'data' => $fileInfo]);
    }

    /**
     * Store a new prediction from AI model
     */
    public function storePrediction(Request $request, $id)
    {
        if (!$this->validateApiKey($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'clip_path'         => 'nullable|string',
            'relative_time'     => 'nullable|string',
            'first_model_prop'  => 'nullable|numeric',
            'prediction_0'      => 'nullable|array',
            'prediction_1'      => 'nullable|array',
        ]);

        $match = MatchVideo::find($id);
        if (!$match) {
            return response()->json(['success' => false, 'message' => 'Match not found'], 404);
        }

        // Create prediction
        $prediction = Prediction::create([
            'match_id'         => $match->id,
            'clip_path'        => $validated['clip_path'] ?? null,
            'relative_time'    => $validated['relative_time'] ?? null,
            'first_model_prop' => $validated['first_model_prop'] ?? null,
            'prediction_0'     => $validated['prediction_0'] ?? null,
            'prediction_1'     => $validated['prediction_1'] ?? null,
        ]);

        // Format prediction with labels for WebSocket
        $locale = app()->getLocale();

        // Generate URL from clip_path if it exists
        $clipUrl = null;
        if ($prediction->clip_path) {
            $clipStorageDisk = env('CLIP_STORAGE_DISK', $match->storage_disk ?? 'public');
            try {
                $clipUrl = $this->generateStorageUrl($clipStorageDisk, $prediction->clip_path);
            } catch (\Exception $e) {
                Log::warning('Failed to generate clip URL for WebSocket', [
                    'clip_path' => $prediction->clip_path,
                    'disk' => $clipStorageDisk,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $formattedPrediction = [
            'id' => $prediction->id,
            'match_id' => $prediction->match_id,
            'clip_path' => $prediction->clip_path,
            'url' => $clipUrl,
            'relative_time' => $prediction->relative_time,
            'first_model_prop' => $prediction->first_model_prop,
            'prediction_0' => $prediction->formatPredictionData($prediction->prediction_0, $locale),
            'prediction_1' => $prediction->formatPredictionData($prediction->prediction_1, $locale),
        ];

        // Send prediction to WebSocket for real-time updates
        WebSocketHelper::sendPrediction($match->user_id, $match->id, $formattedPrediction);

        // If this is the first prediction, update status to processing if still pending
        if ($match->status === 'pending') {
            $match->update(['status' => 'processing']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Prediction stored successfully',
            'data' => [
                'prediction_id' => $prediction->id,
                'match_id' => $match->id
            ]
        ]);
    }

    /**
     * Mark match as completed (called when AI model finishes)
     */
    public function completeProcessing(Request $request, $id)
    {
        if (!$this->validateApiKey($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $match = MatchVideo::find($id);
        if (!$match) {
            return response()->json(['success' => false, 'message' => 'Match not found'], 404);
        }

        $match->update(['status' => 'completed']);

        // Notify user that analysis is complete
        $this->notificationService->notifyAnalysisComplete($match);

        // Notify WebSocket that processing is complete
        WebSocketHelper::sendProcessingComplete($match->user_id, $match->id);

        return response()->json([
            'success' => true,
            'message' => 'Match processing marked as completed',
            'data' => ['match_id' => $match->id, 'status' => $match->status]
        ]);
    }
}

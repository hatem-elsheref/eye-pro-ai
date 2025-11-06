<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Models\MatchVideo;
use App\Models\Prediction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
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
     * 
     * Requires all fields: clip_path, relative_time, prediction_0, prediction_1, first_model_prop
     */
    public function storePrediction(Request $request, $id)
    {
        if (!$this->validateApiKey($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Validate all required fields - all must be present
        try {
            $validated = $request->validate([
                'clip_path'         => 'required|string|min:1',
                'relative_time'     => 'required|string|min:1',
                'first_model_prop'  => 'required|numeric|min:0|max:1',
                'prediction_0'      => 'required|array|min:1',
                'prediction_1'      => 'required|array|min:1',
            ], [
            'clip_path.required' => 'clip_path is required',
            'clip_path.string' => 'clip_path must be a string',
            'clip_path.min' => 'clip_path cannot be empty',
            'relative_time.required' => 'relative_time is required',
            'relative_time.string' => 'relative_time must be a string',
            'relative_time.min' => 'relative_time cannot be empty',
            'first_model_prop.required' => 'first_model_prop is required',
            'first_model_prop.numeric' => 'first_model_prop must be a number',
            'first_model_prop.min' => 'first_model_prop must be between 0 and 1',
            'first_model_prop.max' => 'first_model_prop must be between 0 and 1',
            'prediction_0.required' => 'prediction_0 (JSON 1) is required',
            'prediction_0.array' => 'prediction_0 must be a valid JSON object/array',
            'prediction_0.min' => 'prediction_0 cannot be empty',
            'prediction_1.required' => 'prediction_1 (JSON 2) is required',
            'prediction_1.array' => 'prediction_1 must be a valid JSON object/array',
            'prediction_1.min' => 'prediction_1 cannot be empty',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors for debugging
            Log::warning('Prediction validation failed', [
                'match_id' => $id,
                'errors' => $e->errors(),
                'request_data' => $request->except(['prediction_0', 'prediction_1']) // Don't log full JSON to avoid huge logs
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: Missing required fields',
                'errors' => $e->errors()
            ], 422);
        }

        $match = MatchVideo::find($id);
        if (!$match) {
            return response()->json(['success' => false, 'message' => 'Match not found'], 404);
        }

        // Additional validation: ensure prediction_0 and prediction_1 are valid JSON structures
        if (empty($validated['prediction_0']) || !is_array($validated['prediction_0'])) {
            return response()->json([
                'success' => false,
                'message' => 'prediction_0 must be a valid non-empty JSON object/array'
            ], 422);
        }

        if (empty($validated['prediction_1']) || !is_array($validated['prediction_1'])) {
            return response()->json([
                'success' => false,
                'message' => 'prediction_1 must be a valid non-empty JSON object/array'
            ], 422);
        }

        // All validations passed - create prediction with all required data
        $prediction = Prediction::create([
            'match_id'         => $match->id,
            'clip_path'        => $validated['clip_path'],
            'relative_time'    => $validated['relative_time'],
            'first_model_prop' => $validated['first_model_prop'],
            'prediction_0'     => $validated['prediction_0'],
            'prediction_1'     => $validated['prediction_1'],
        ]);

        Log::info('Prediction stored successfully', [
            'match_id' => $match->id,
            'prediction_id' => $prediction->id,
            'clip_path' => $prediction->clip_path,
            'relative_time' => $prediction->relative_time,
            'first_model_prop' => $prediction->first_model_prop,
            'has_prediction_0' => !empty($prediction->prediction_0),
            'has_prediction_1' => !empty($prediction->prediction_1),
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

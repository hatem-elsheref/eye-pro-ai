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

        $match = MatchVideo::find($id);
        if (!$match) {
            return response()->json(['success' => false, 'message' => 'Match not found'], 404);
        }

        $disk = $match->storage_disk ?? 'public';
        $fileInfo = [
            'id' => $match->id,
            'name' => $match->name,
            'path' => $match->video_path,
            'disk' => $disk,
            'size' => $match->file_size,
            'url' => $match->video_url,
        ];

        try {
            if ($match->video_path && Storage::disk($disk)->exists($match->video_path)) {
                $fileInfo['size_bytes'] = Storage::disk($disk)->size($match->video_path);
            }
        } catch (\Exception $e) {
            Log::warning('Could not get file size', ['matchId' => $id]);
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

        // Send prediction to WebSocket for real-time updates
        WebSocketHelper::sendPrediction($match->user_id, $match->id, $prediction->toArray());

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

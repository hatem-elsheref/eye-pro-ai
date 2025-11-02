<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Models\MatchVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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

    public function updateAnalysis(Request $request, $id)
    {
        if (!$this->validateApiKey($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'analysis' => 'required',
            'status' => 'nullable|in:completed,failed,processing'
        ]);

        $match = MatchVideo::find($id);
        if (!$match) {
            return response()->json(['success' => false, 'message' => 'Match not found'], 404);
        }

        $updateData = [
            'analysis' => is_array($validated['analysis']) 
                ? json_encode($validated['analysis']) 
                : $validated['analysis'],
            'status' => $validated['status'] ?? 'completed'
        ];

        $match->update($updateData);
        $this->notificationService->notifyAnalysisComplete($match, $validated['analysis']);

        return response()->json([
            'success' => true,
            'message' => 'Match analysis updated successfully',
            'data' => ['match_id' => $match->id, 'status' => $match->status]
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\MatchService;
use App\Services\AIModelService;
use App\Services\NotificationService;
use App\Models\MatchVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    public function index(Request $request)
    {
        $query = MatchVideo::where('user_id', auth()->id());

        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            if (!empty($search)) {
                $query->where('name', 'like', '%' . $search . '%');
            }
        }

        $matches = $query->latest()->paginate(10)->withQueryString();

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
                ->with('error', 'Your account must be approved before you can upload matches.');
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
                return back()->with('error', 'Please provide either a video file or URL.');
            }

            // Send notification only (no auto-processing)
            $this->notificationService->notifyUploadProcessing($match);

            return redirect()->route('matches.show', $match->id)
                ->with('success', 'Match uploaded successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to create match', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to upload match: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $match = MatchVideo::with(['user', 'predictions'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $usedDisk = ucfirst($match->storage_disk ?? 'public');

        // Format predictions with labels based on current locale
        $predictions = $match->predictions->map(function ($prediction) {
            $locale = app()->getLocale();
            return [
                'id' => $prediction->id,
                'match_id' => $prediction->match_id,
                'clip_path' => $prediction->clip_path,
                'relative_time' => $prediction->relative_time,
                'first_model_prop' => $prediction->first_model_prop,
                'prediction_0' => $prediction->formatPredictionData($prediction->prediction_0, $locale),
                'prediction_1' => $prediction->formatPredictionData($prediction->prediction_1, $locale),
            ];
        });

        return view('admin.matches.show', compact('match', 'usedDisk', 'predictions'));
    }

    public function edit($id)
    {
        $match = MatchVideo::where('user_id', auth()->id())->findOrFail($id);
        return view('admin.matches.edit', compact('match'));
    }

    public function update(Request $request, $id)
    {
        $match = MatchVideo::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'match_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'tags' => 'nullable|string|max:255',
        ]);

        $match->update($validated);

        return redirect()->route('matches.show', $match->id)
            ->with('success', 'Match updated successfully!');
    }

    public function destroy($id)
    {
        $match = MatchVideo::where('user_id', auth()->id())->findOrFail($id);
        $match->delete();

        return redirect()->route('matches.index')
            ->with('success', 'Match deleted successfully!');
    }

    public function uploadChunk(Request $request)
    {
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
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
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

            // Send notification only (no auto-processing)
            $this->notificationService->notifyUploadProcessing($match);

            return response()->json([
                'success' => true,
                'message' => 'Upload completed successfully!',
                'matchId' => $match->id,
                'redirectUrl' => route('matches.show', $match->id),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to finalize upload', ['error' => $e->getMessage()]);
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

<?php

namespace App\Http\Controllers;

use App\Services\MatchService;
use App\Services\AIModelService;
use App\Services\NotificationService;
use App\Models\MatchVideo;
use Illuminate\Http\Request;
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

            // Send notification and start AI processing
            $this->notificationService->notifyUploadProcessing($match);
            $this->aiModelService->startProcessing($match->id);

            return redirect()->route('matches.show', $match->id)
                ->with('success', 'Match uploaded successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to create match', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to upload match: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $match = MatchVideo::with('user')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $usedDisk = ucfirst($match->storage_disk ?? 'public');

        return view('admin.matches.show', compact('match', 'usedDisk'));
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

            // Send notification and start AI processing
            $this->notificationService->notifyUploadProcessing($match);
            $this->aiModelService->startProcessing($match->id);

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
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MatchVideo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class MatchController extends Controller
{
    /**
     * Display a listing of matches
     */
    public function index(Request $request)
    {
        $query = MatchVideo::where('user_id', auth()->id());

        // Handle search
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

    /**
     * Show the form for creating a new match
     */
    public function create()
    {
        // Check if user is approved
        if (!auth()->user()->is_approved) {
            return redirect()->route('matches.index')
                ->with('warning', 'Your account must be approved before you can upload matches.');
        }

        return view('admin.matches.create');
    }

    /**
     * Store a newly created match
     */
    public function store(Request $request)
    {
        // Check if user is approved
        if (!auth()->user()->is_approved) {
            return redirect()->route('matches.index')
                ->with('error', 'Your account must be approved before you can upload matches.');
        }

        $validated = $request->validate([
            'match_name' => 'required|string|max:255',
            'video_file' => 'nullable|file|mimes:mp4,avi,mov,mkv,webm|max:2048000', // 2GB max
            'video_url' => 'nullable|url|max:500',
        ]);

        // Determine if it's a file upload or URL
        $type = 'url';
        $videoPath = null;
        $videoUrl = $validated['video_url'] ?? null;
        $fileSize = null;

        if ($request->hasFile('video_file')) {
            $type = 'file';
            $file = $request->file('video_file');

            // Store the file
            $videoPath = $file->store('matches', 'public');
            $videoUrl = Storage::url($videoPath);
            $fileSize = $this->formatBytes($file->getSize());
        }

        // Create the match
        $match = MatchVideo::create([
            'user_id' => auth()->id(),
            'name' => $validated['match_name'],
            'type' => $type,
            'status' => 'processing',
            'video_url' => $videoUrl,
            'video_path' => $videoPath,
            'file_size' => $fileSize,
        ]);

        return redirect()->route('matches.show', $match->id)
            ->with('success', 'Match uploaded successfully!');
    }

    /**
     * Display the specified match
     */
    public function show($id)
    {
        $match = MatchVideo::where('user_id', auth()->id())->findOrFail($id);

        return view('admin.matches.show', compact('match'));
    }

    /**
     * Show the form for editing the specified match
     */
    public function edit($id)
    {
        $match = MatchVideo::where('user_id', auth()->id())->findOrFail($id);

        return view('admin.matches.edit', compact('match'));
    }

    /**
     * Update the specified match
     */
    public function update(Request $request, $id)
    {
        $match = MatchVideo::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'match_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'tags' => 'nullable|string|max:255',
        ]);

        $match->update([
            'name' => $validated['match_name'],
            'description' => $validated['description'] ?? null,
            'tags' => $validated['tags'] ?? null,
        ]);

        return redirect()->route('matches.show', $match->id)
            ->with('success', 'Match updated successfully!');
    }

    /**
     * Remove the specified match
     */
    public function destroy($id)
    {
        $match = MatchVideo::where('user_id', auth()->id())->findOrFail($id);

        // Delete the video file if it exists
        if ($match->video_path && Storage::disk('public')->exists($match->video_path)) {
            Storage::disk('public')->delete($match->video_path);
        }

        $match->delete();

        return redirect()->route('matches.index')
            ->with('success', 'Match deleted successfully!');
    }

    /**
     * Upload a chunk of the video file
     */
    public function uploadChunk(Request $request)
    {
        $validated = $request->validate([
            'uploadId' => 'required|string',
            'chunkIndex' => 'required|integer|min:0',
            'totalChunks' => 'required|integer|min:1',
            'chunk' => 'required|file',
            'fileName' => 'required|string',
            'fileSize' => 'required|integer',
        ]);

        $uploadId = $validated['uploadId'];
        $chunkIndex = $validated['chunkIndex'];
        $totalChunks = $validated['totalChunks'];
        $chunk = $validated['chunk'];
        $fileName = $validated['fileName'];
        $fileSize = $validated['fileSize'];

        // Create directory for this upload if it doesn't exist
        $chunkDir = 'chunks/' . $uploadId;
        if (!Storage::disk('public')->exists($chunkDir)) {
            Storage::disk('public')->makeDirectory($chunkDir);
        }

        // Store the chunk
        $chunkPath = $chunkDir . '/' . $chunkIndex;
        $chunk->storeAs('public/' . $chunkDir, $chunkIndex);

        // Update upload status in cache
        $uploadKey = 'upload_' . $uploadId;
        $uploadStatus = Cache::get($uploadKey, [
            'fileName' => $fileName,
            'fileSize' => $fileSize,
            'totalChunks' => $totalChunks,
            'uploadedChunks' => [],
            'created_at' => now()->toIso8601String(),
        ]);

        $uploadStatus['uploadedChunks'][] = $chunkIndex;
        $uploadStatus['uploadedChunks'] = array_unique($uploadStatus['uploadedChunks']);
        sort($uploadStatus['uploadedChunks']);

        // Store for 24 hours
        Cache::put($uploadKey, $uploadStatus, now()->addHours(24));

        return response()->json([
            'success' => true,
            'chunkIndex' => $chunkIndex,
            'uploadedChunks' => $uploadStatus['uploadedChunks'],
            'totalChunks' => $totalChunks,
            'progress' => (count($uploadStatus['uploadedChunks']) / $totalChunks) * 100,
        ]);
    }

    /**
     * Finalize upload by assembling all chunks
     */
    public function finalizeUpload(Request $request)
    {
        $validated = $request->validate([
            'uploadId' => 'required|string',
            'match_name' => 'required|string|max:255',
        ]);

        $uploadId = $validated['uploadId'];
        $uploadKey = 'upload_' . $uploadId;
        $uploadStatus = Cache::get($uploadKey);

        if (!$uploadStatus) {
            return response()->json([
                'success' => false,
                'message' => 'Upload session not found. Please start over.',
            ], 404);
        }

        $totalChunks = $uploadStatus['totalChunks'];
        $uploadedChunks = $uploadStatus['uploadedChunks'];

        // Check if all chunks are uploaded
        if (count($uploadedChunks) !== $totalChunks) {
            return response()->json([
                'success' => false,
                'message' => 'Not all chunks have been uploaded. Missing chunks: ' . implode(', ', array_diff(range(0, $totalChunks - 1), $uploadedChunks)),
                'uploadedChunks' => $uploadedChunks,
                'totalChunks' => $totalChunks,
            ], 400);
        }

        $chunkDir = 'chunks/' . $uploadId;
        $finalFileName = $uploadStatus['fileName'];
        $finalPath = 'matches/' . $uploadId . '_' . $finalFileName;

        // Assemble chunks
        $finalFile = fopen(Storage::disk('public')->path($finalPath), 'wb');

        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkPath = $chunkDir . '/' . $i;
            if (Storage::disk('public')->exists($chunkPath)) {
                $chunkContent = Storage::disk('public')->get($chunkPath);
                fwrite($finalFile, $chunkContent);
            } else {
                fclose($finalFile);
                Storage::disk('public')->delete($finalPath);
                return response()->json([
                    'success' => false,
                    'message' => "Chunk {$i} is missing.",
                ], 400);
            }
        }

        fclose($finalFile);

        // Clean up chunks
        Storage::disk('public')->deleteDirectory($chunkDir);
        Cache::forget($uploadKey);

        // Create the match record
        $match = MatchVideo::create([
            'user_id' => auth()->id(),
            'name' => $validated['match_name'],
            'type' => 'file',
            'status' => 'processing',
            'video_url' => Storage::url($finalPath),
            'video_path' => $finalPath,
            'file_size' => $this->formatBytes($uploadStatus['fileSize']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Upload completed successfully!',
            'matchId' => $match->id,
            'redirectUrl' => route('matches.show', $match->id),
        ]);
    }

    /**
     * Get upload status (for resuming uploads)
     */
    public function getUploadStatus($uploadId)
    {
        $uploadKey = 'upload_' . $uploadId;
        $uploadStatus = Cache::get($uploadKey);

        if (!$uploadStatus) {
            return response()->json([
                'success' => false,
                'message' => 'Upload session not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'uploadStatus' => $uploadStatus,
            'progress' => (count($uploadStatus['uploadedChunks']) / $uploadStatus['totalChunks']) * 100,
        ]);
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}


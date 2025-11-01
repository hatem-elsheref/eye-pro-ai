<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MatchVideo;
use Illuminate\Support\Facades\Storage;

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
            $search = $request->get('search');
            $query->where('name', 'like', '%' . $search . '%');
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


@extends('admin.layouts.master')

@section('title', 'Edit Match - Match Analysis Platform')

@section('content')
<div style="margin-bottom: 24px;">
    <a href="{{ route('matches.show', $match->id ?? 1) }}" style="color: var(--text-gray); text-decoration: none; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 16px;">
        <i class="fas fa-arrow-left"></i>
        Back to Match
    </a>
</div>

<div class="upload-header" style="margin-bottom: 32px;">
    <h1 class="upload-title">Edit Match</h1>
    <p class="upload-subtitle">Update your match information</p>
</div>

<div class="upload-card">
    <form action="{{ route('matches.update', $match->id ?? 1) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="match_name" class="form-label">Match Name</label>
            <input 
                type="text" 
                id="match_name" 
                name="match_name" 
                class="form-control" 
                value="{{ old('match_name', $match->name ?? '') }}"
                required
            >
        </div>
        
        <div class="form-group">
            <label for="description" class="form-label">Description (Optional)</label>
            <textarea 
                id="description" 
                name="description" 
                class="form-control" 
                rows="4"
                placeholder="Add notes or description about this match..."
            >{{ old('description', $match->description ?? '') }}</textarea>
        </div>
        
        <div class="form-group">
            <label for="tags" class="form-label">Tags (Optional)</label>
            <input 
                type="text" 
                id="tags" 
                name="tags" 
                class="form-control" 
                value="{{ old('tags', $match->tags ?? '') }}"
                placeholder="e.g., championship, final, 2024"
            >
            <p style="margin: 8px 0 0 0; font-size: 12px; color: var(--text-gray);">
                Separate tags with commas
            </p>
        </div>
        
        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Save Changes
            </button>
            
            <a href="{{ route('matches.show', $match->id ?? 1) }}" class="btn btn-secondary" style="text-decoration: none;">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection




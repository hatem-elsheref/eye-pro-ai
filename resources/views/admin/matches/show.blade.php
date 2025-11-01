@extends('admin.layouts.master')

@section('title', $match->name ?? 'Match Details')

@section('content')
<div style="margin-bottom: 24px;">
    <a href="{{ route('matches.index') }}" style="color: var(--text-gray); text-decoration: none; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 16px;">
        <i class="fas fa-arrow-left"></i>
        Back to Matches
    </a>
</div>

<div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 32px;">
    <div>
        <h1 class="dashboard-title" style="margin-bottom: 8px;">{{ $match->name ?? 'Match Details' }}</h1>
        <p class="dashboard-subtitle">
            Uploaded on {{ isset($match->created_at) ? $match->created_at->format('F d, Y') : 'N/A' }}
        </p>
    </div>
    
    <div style="display: flex; gap: 12px;">
        <a href="{{ route('matches.edit', $match->id ?? 1) }}" class="btn btn-secondary" style="text-decoration: none;">
            <i class="fas fa-edit"></i>
            Edit
        </a>
        
        <form id="deleteMatchForm" action="{{ route('matches.destroy', $match->id ?? 1) }}" method="POST" style="margin: 0;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn" style="background: var(--danger-color); color: white;">
                <i class="fas fa-trash"></i>
                Delete
            </button>
        </form>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <div>
        <!-- Video Player -->
        <div style="background: var(--bg-white); border-radius: 12px; padding: 24px; margin-bottom: 24px;">
            <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">Match Video</h2>
            
            <div style="background: #000; border-radius: 8px; aspect-ratio: 16/9; display: flex; align-items: center; justify-content: center; position: relative;">
                @if(isset($match->video_url))
                    <video controls style="width: 100%; height: 100%; border-radius: 8px;">
                        <source src="{{ $match->video_url }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                @else
                    <div style="text-align: center; color: #fff;">
                        <i class="fas fa-video" style="font-size: 48px; opacity: 0.5; margin-bottom: 12px;"></i>
                        <p style="opacity: 0.7;">Video not available</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Match Analysis -->
        <div style="background: var(--bg-white); border-radius: 12px; padding: 24px;">
            <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">Match Analysis</h2>
            
            @if(isset($match->analysis))
                <div style="color: var(--text-gray); line-height: 1.8;">
                    {!! $match->analysis !!}
                </div>
            @else
                <div style="text-align: center; padding: 40px; color: var(--text-gray);">
                    <i class="fas fa-chart-line" style="font-size: 48px; opacity: 0.3; margin-bottom: 12px;"></i>
                    <p style="margin: 0;">Analysis is being processed...</p>
                </div>
            @endif
        </div>
    </div>
    
    <div>
        <!-- Match Info -->
        <div style="background: var(--bg-white); border-radius: 12px; padding: 24px; margin-bottom: 24px;">
            <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">Match Information</h2>
            
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label style="font-size: 12px; color: var(--text-gray); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Status</label>
                    @if(isset($match->status) && $match->status === 'completed')
                    <p style="margin: 4px 0 0 0;">
                        <span style="padding: 4px 12px; border-radius: 12px; background: #d1fae5; color: #065f46; font-size: 12px; font-weight: 600;">
                            Completed
                        </span>
                    </p>
                    @elseif(isset($match->status) && $match->status === 'processing')
                    <p style="margin: 4px 0 0 0;">
                        <span style="padding: 4px 12px; border-radius: 12px; background: #fef3c7; color: #92400e; font-size: 12px; font-weight: 600;">
                            Processing
                        </span>
                    </p>
                    @else
                    <p style="margin: 4px 0 0 0;">
                        <span style="padding: 4px 12px; border-radius: 12px; background: #e5e7eb; color: #374151; font-size: 12px; font-weight: 600;">
                            {{ $match->status ?? 'Unknown' }}
                        </span>
                    </p>
                    @endif
                </div>
                
                <div>
                    <label style="font-size: 12px; color: var(--text-gray); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Upload Type</label>
                    <p style="margin: 4px 0 0 0; font-weight: 500;">
                        <i class="fas {{ isset($match->type) && $match->type === 'url' ? 'fa-link' : 'fa-file-video' }}"></i>
                        {{ isset($match->type) ? ucfirst($match->type) : 'File' }}
                    </p>
                </div>
                
                <div>
                    <label style="font-size: 12px; color: var(--text-gray); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Duration</label>
                    <p style="margin: 4px 0 0 0; font-weight: 500;">{{ $match->duration ?? 'N/A' }}</p>
                </div>
                
                <div>
                    <label style="font-size: 12px; color: var(--text-gray); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">File Size</label>
                    <p style="margin: 4px 0 0 0; font-weight: 500;">{{ $match->file_size ?? 'N/A' }}</p>
                </div>
                
                <div>
                    <label style="font-size: 12px; color: var(--text-gray); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Created</label>
                    <p style="margin: 4px 0 0 0; font-weight: 500;">
                        {{ isset($match->created_at) ? $match->created_at->format('M d, Y h:i A') : 'N/A' }}
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div style="background: var(--bg-white); border-radius: 12px; padding: 24px;">
            <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">Quick Actions</h2>
            
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <button class="btn btn-secondary" style="width: 100%; justify-content: flex-start;">
                    <i class="fas fa-download"></i>
                    Download Video
                </button>
                
                <button class="btn btn-secondary" style="width: 100%; justify-content: flex-start;">
                    <i class="fas fa-share-alt"></i>
                    Share Match
                </button>
                
                <button class="btn btn-secondary" style="width: 100%; justify-content: flex-start;">
                    <i class="fas fa-file-export"></i>
                    Export Analysis
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteForm = document.getElementById('deleteMatchForm');
    
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: 'Are you sure you want to delete this match?',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteForm.submit();
                }
            });
        });
    }
});
</script>
@endpush
@endsection




@extends('admin.layouts.master')

@section('title', 'Edit Match - Eye Pro')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Back Link -->
    <div class="mb-6">
        <a href="{{ route('matches.show', $match->id ?? 1) }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-arrow-left text-sm"></i>
            <span class="font-medium">Back to Match</span>
        </a>
    </div>

    <!-- Header -->
    <div class="relative overflow-hidden rounded-2xl p-6 shadow-lg border border-blue-200 mb-6" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full opacity-10" style="background: rgba(255,255,255,0.5);"></div>
        <div class="absolute bottom-0 left-0 -mb-6 -ml-6 h-32 w-32 rounded-full opacity-10" style="background: rgba(255,255,255,0.5);"></div>
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white mb-1">Edit Match</h1>
            <p class="text-sm text-blue-50 font-medium">Update your match information and details</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <form action="{{ route('matches.update', $match->id ?? 1) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Match Name -->
            <div>
                <label for="match_name" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-tag text-blue-500 mr-2"></i>
                    Match Name <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="match_name" 
                    name="match_name" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all text-sm font-medium"
                    value="{{ old('match_name', $match->name ?? '') }}"
                    required
                    placeholder="Enter match name..."
                >
                @error('match_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-align-left text-purple-500 mr-2"></i>
                    Description <span class="text-gray-400 text-xs font-normal">(Optional)</span>
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all text-sm font-medium resize-none"
                    rows="5"
                    placeholder="Add notes or description about this match..."
                >{{ old('description', $match->description ?? '') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Tags -->
            <div>
                <label for="tags" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-hashtag text-indigo-500 mr-2"></i>
                    Tags <span class="text-gray-400 text-xs font-normal">(Optional)</span>
                </label>
                <input 
                    type="text" 
                    id="tags" 
                    name="tags" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all text-sm font-medium"
                    value="{{ old('tags', $match->tags ?? '') }}"
                    placeholder="e.g., championship, final, 2024"
                >
                <p class="mt-2 text-xs text-gray-500 flex items-center gap-1">
                    <i class="fas fa-info-circle text-gray-400"></i>
                    Separate tags with commas
                </p>
                @error('tags')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Action Buttons -->
            <div class="flex gap-3 pt-4 border-t border-gray-200">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 text-white rounded-lg font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                    <i class="fas fa-save"></i>
                    <span>Save Changes</span>
                </button>
                
                <a href="{{ route('matches.show', $match->id ?? 1) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </a>
            </div>
        </form>
    </div>
</div>
@endsection




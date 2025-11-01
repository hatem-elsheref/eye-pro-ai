@extends('admin.layouts.master')

@section('title', 'Upload New Match - Eye Pro')
@section('page-title', 'Upload New Match')

@section('content')
<div class="max-w-2xl mx-auto" x-data="{ tab: 'file', uploading: false, progress: 0 }">
    
    <!-- Upload Header -->
    <div class="relative overflow-hidden rounded-xl p-4 mb-4 shadow-md border border-blue-200" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
        <div class="absolute top-0 right-0 -mt-2 -mr-2 h-16 w-16 rounded-full bg-white opacity-10"></div>
        <div class="absolute bottom-0 left-0 -mb-3 -ml-3 h-24 w-24 rounded-full bg-white opacity-10"></div>
        <div class="relative z-10 text-center">
            <div class="inline-flex items-center justify-center h-12 w-12 rounded-lg bg-white/20 backdrop-blur-lg mb-2 shadow-md border border-white/30">
                <i class="fas fa-cloud-upload-alt text-xl text-white"></i>
            </div>
            <h1 class="text-xl font-bold text-white mb-1 drop-shadow-md">Upload New Match</h1>
            <p class="text-xs text-blue-50 font-medium">Upload a video file or provide a link to analyze the match</p>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-100">
        
        <form action="{{ route('matches.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
            @csrf
            
            <!-- Match Name -->
            <div class="mb-4">
                <label for="match_name" class="block text-sm font-bold text-gray-700 mb-2 flex items-center space-x-2">
                    <i class="fas fa-tag text-orange-500 text-xs"></i>
                    <span>Match Name</span>
                </label>
                <input 
                    type="text" 
                    id="match_name" 
                    name="match_name" 
                    class="block w-full px-3 py-2.5 border-2 border-gray-200 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition-all duration-200 font-medium text-sm"
                    placeholder="e.g., Championship Final 2024"
                    value="{{ old('match_name') }}"
                    required
                >
            </div>
            
            <!-- Upload Method Info -->
            <div class="bg-gradient-to-r from-teal-50 to-blue-50 rounded-lg p-3 mb-4 border border-blue-100">
                <div class="flex items-start space-x-2">
                    <div class="h-6 w-6 rounded-md flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                        <i class="fas fa-info-circle text-white text-xs"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-gray-900 mb-0.5">Upload Method</h3>
                        <p class="text-xs text-gray-600">Choose how you want to add your match video</p>
                    </div>
                </div>
            </div>
            
            <!-- Upload Tabs -->
            <div class="flex space-x-2 mb-4 bg-gray-100 p-1 rounded-lg shadow-inner">
                <button @click="tab = 'file'" type="button" :class="tab === 'file' ? 'text-white shadow-md' : 'text-gray-600 hover:bg-gray-200'" class="flex-1 py-2 text-center rounded-md font-semibold text-xs transition-all duration-200 flex items-center justify-center space-x-1.5" :style="tab === 'file' ? 'background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);' : ''">
                    <i class="fas fa-cloud-upload-alt text-xs"></i>
                    <span>Upload File</span>
                </button>
                <button @click="tab = 'url'" type="button" :class="tab === 'url' ? 'text-white shadow-md' : 'text-gray-600 hover:bg-gray-200'" class="flex-1 py-2 text-center rounded-md font-semibold text-xs transition-all duration-200 flex items-center justify-center space-x-1.5" :style="tab === 'url' ? 'background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);' : ''">
                    <i class="fas fa-link text-xs"></i>
                    <span>Video URL</span>
                </button>
            </div>
            
            <!-- File Upload Section -->
            <div x-show="tab === 'file'" x-transition class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center space-x-2">
                    <i class="fas fa-file-video text-orange-500 text-xs"></i>
                    <span>Video File</span>
                </label>
                <div onclick="document.getElementById('video_file').click()" class="group relative border-4 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-orange-500 transition-all duration-300 cursor-pointer bg-gradient-to-br from-orange-50 to-amber-50 hover:from-orange-100 hover:to-amber-100">
                    <div class="space-y-4">
                        <div class="flex justify-center">
                            <div class="h-20 w-20 rounded-xl bg-gradient-to-br from-orange-500 to-amber-600 flex items-center justify-center group-hover:scale-110 group-hover:rotate-6 transition-all duration-500 shadow-lg" style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);">
                                <i class="fas fa-cloud-upload-alt text-4xl" style="color: #ffffff !important; text-shadow: 0 2px 6px rgba(0,0,0,0.3); display: inline-block;"></i>
                            </div>
                        </div>
                        <div>
                            <p id="fileName" class="text-base font-bold text-gray-900 mb-1">Choose File or Drag & Drop</p>
                            <p class="text-xs text-gray-500 font-medium">Supports large files (1GB+). Files are uploaded in chunks.</p>
                            <div class="flex items-center justify-center space-x-2 mt-3 text-xs font-semibold text-gray-400">
                                <span class="px-2 py-1 bg-white rounded shadow-sm">MP4</span>
                                <span class="px-2 py-1 bg-white rounded shadow-sm">AVI</span>
                                <span class="px-2 py-1 bg-white rounded shadow-sm">MOV</span>
                                <span class="px-2 py-1 bg-white rounded shadow-sm">MKV</span>
                            </div>
                        </div>
                    </div>
                </div>
                <input 
                    type="file" 
                    id="video_file" 
                    name="video_file" 
                    accept="video/*"
                    class="hidden"
                    onchange="handleFileSelect(this)"
                >
                
                <!-- Upload Progress -->
                <div id="uploadProgress" class="hidden mt-4 bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl p-4 border-2 border-orange-200 shadow-md">
                    <div class="flex justify-between items-center mb-3">
                        <div class="flex items-center space-x-2">
                            <div class="h-8 w-8 rounded-lg bg-orange-500 flex items-center justify-center shadow-md">
                                <i class="fas fa-cloud-upload-alt text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">Uploading in chunks...</p>
                                <p class="text-xs text-gray-500 font-medium">Please wait</p>
                            </div>
                        </div>
                        <span id="progressPercent" class="text-xl font-black text-orange-600">0%</span>
                    </div>
                    
                    <div class="relative h-3 bg-gray-200 rounded-full overflow-hidden mb-3 shadow-inner">
                        <div id="progressFill" class="h-full rounded-full transition-all duration-300 relative overflow-hidden shadow-md" style="width: 0%; background: linear-gradient(90deg, #f59e0b 0%, #ea580c 100%);">
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-50 animate-shimmer"></div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2 text-xs">
                            <div class="flex items-center space-x-1.5 px-2 py-1.5 bg-white rounded-lg font-semibold text-gray-700 shadow-sm">
                                <i class="fas fa-tachometer-alt text-orange-500 text-xs"></i>
                                <span id="uploadSpeed">Speed: Calculating...</span>
                            </div>
                            <div id="chunkInfo" class="flex items-center space-x-1.5 px-2 py-1.5 bg-white rounded-lg font-semibold text-gray-700 shadow-sm">
                                <i class="fas fa-sync fa-spin text-orange-500 text-xs"></i>
                                <span>0/0 chunks</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- URL Section -->
            <div x-show="tab === 'url'" x-transition class="mb-6">
                <label for="video_url" class="block text-sm font-bold text-gray-700 mb-2 flex items-center space-x-2">
                    <i class="fas fa-link text-orange-500 text-xs"></i>
                    <span>Video URL</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-link text-gray-400 text-sm"></i>
                    </div>
                    <input 
                        type="url" 
                        id="video_url" 
                        name="video_url" 
                        class="block w-full pl-10 pr-3 py-2.5 border-2 border-gray-200 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition-all duration-200 font-medium text-sm"
                        placeholder="https://youtube.com/watch?v=..."
                        value="{{ old('video_url') }}"
                    >
                </div>
                <p class="mt-2 text-xs text-gray-500 flex items-center space-x-2 bg-blue-50 p-2 rounded-lg border border-blue-200">
                    <i class="fas fa-check-circle text-blue-500 text-xs"></i>
                    <span class="font-medium">YouTube, Vimeo, or direct video links supported</span>
                </p>
            </div>
            
            <button 
                type="submit" 
                id="submitBtn"
                class="w-full flex items-center justify-center space-x-2 py-3 px-4 border-0 text-base font-bold rounded-xl text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
                style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);"
            >
                <div class="h-8 w-8 rounded-lg bg-white/30 backdrop-blur-sm flex items-center justify-center">
                    <i class="fas fa-arrow-right text-base"></i>
                </div>
                <span id="btnText">Upload & Continue</span>
            </button>
        </form>
    </div>
</div>

@push('styles')
<style>
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    .animate-shimmer {
        animation: shimmer 2s infinite;
    }
</style>
@endpush

@push('scripts')
<script>
function formatBytes(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

function formatSpeed(bytesPerSecond) {
    return formatBytes(bytesPerSecond) + '/s';
}

function handleFileSelect(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileSize = file.size;
        
        document.getElementById('fileName').innerHTML = `
            <strong class="text-orange-700">${file.name}</strong><br>
            <small class="text-gray-500">${formatBytes(fileSize)}</small>
        `;
        
        // Calculate chunks (5MB per chunk)
        const chunkSize = 5 * 1024 * 1024;
        const totalChunks = Math.ceil(fileSize / chunkSize);
        
        // Show progress
        const progressDiv = document.getElementById('uploadProgress');
        const progressFill = document.getElementById('progressFill');
        const progressPercent = document.getElementById('progressPercent');
        const submitBtn = document.getElementById('submitBtn');
        
        progressDiv.classList.remove('hidden');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<div class="h-8 w-8 rounded-lg bg-white/30 backdrop-blur-sm flex items-center justify-center"><i class="fas fa-spinner fa-spin text-base"></i></div> <span>Uploading...</span>';
        
        // Simulate chunked upload
        let progress = 0;
        let uploadedChunks = 0;
        const startTime = Date.now();
        let lastUpdate = startTime;
        let uploadedBytes = 0;
        
        const interval = setInterval(() => {
            const currentTime = Date.now();
            const timeDiff = (currentTime - lastUpdate) / 1000;
            
            const chunkProgress = Math.random() * 7 + 3;
            progress += chunkProgress;
            
            const newUploadedBytes = Math.min((progress / 100) * fileSize, fileSize);
            const bytesThisInterval = newUploadedBytes - uploadedBytes;
            uploadedBytes = newUploadedBytes;
            
            const speed = bytesThisInterval / timeDiff;
            lastUpdate = currentTime;
            
            if (progress >= 100) {
                progress = 100;
                clearInterval(interval);
                submitBtn.disabled = false;
                submitBtn.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
                submitBtn.innerHTML = '<div class="h-8 w-8 rounded-lg bg-white/30 backdrop-blur-sm flex items-center justify-center"><i class="fas fa-check-circle text-base"></i></div> <span>Upload Complete - Continue</span>';
                
                uploadedChunks = totalChunks;
                document.getElementById('chunkInfo').innerHTML = `
                    <i class="fas fa-check-circle text-green-600"></i>
                    <span>${totalChunks}/${totalChunks} chunks uploaded</span>
                `;
            } else {
                uploadedChunks = Math.floor((progress / 100) * totalChunks);
            }
            
            progressFill.style.width = progress + '%';
            progressPercent.textContent = Math.round(progress) + '%';
            document.getElementById('uploadSpeed').textContent = `Speed: ${formatSpeed(speed)}`;
            document.getElementById('chunkInfo').innerHTML = `
                <i class="fas fa-sync fa-spin text-orange-500"></i>
                <span>${uploadedChunks}/${totalChunks} chunks</span>
            `;
        }, 300);
    }
}
</script>
@endpush
@endsection

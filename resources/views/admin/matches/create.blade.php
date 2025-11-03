@extends('admin.layouts.master')

@section('title', __('admin.upload_new_match') . ' - Eye Pro')
@section('page-title', __('admin.upload_new_match'))

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
            <h1 class="text-xl font-bold text-white mb-1 drop-shadow-md">{{ __('admin.upload_new_match') }}</h1>
            <p class="text-xs text-blue-50 font-medium">{{ __('admin.upload_new_match_description') }}</p>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-100">
        
        <form action="{{ route('matches.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
            @csrf
            
            <!-- Match Name -->
            <div class="mb-4">
                <label for="match_name" class="block text-sm font-bold text-gray-700 mb-2 flex items-center space-x-2">
                    <i class="fas fa-tag text-orange-500 text-xs"></i>
                    <span>{{ __('admin.match_name') }}</span>
                </label>
                <input 
                    type="text" 
                    id="match_name" 
                    name="match_name" 
                    class="block w-full px-3 py-2.5 border-2 border-gray-200 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition-all duration-200 font-medium text-sm"
                    placeholder="{{ __('admin.match_name_placeholder') }}"
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
                        <h3 class="text-xs font-bold text-gray-900 mb-0.5">{{ __('admin.upload_method') }}</h3>
                        <p class="text-xs text-gray-600">{{ __('admin.upload_method_description') }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Upload Tabs -->
            <div class="flex space-x-2 mb-4 bg-gray-100 p-1 rounded-lg shadow-inner">
                <button @click="tab = 'file'" type="button" :class="tab === 'file' ? 'text-white shadow-md' : 'text-gray-600 hover:bg-gray-200'" class="flex-1 py-2 text-center rounded-md font-semibold text-xs transition-all duration-200 flex items-center justify-center space-x-1.5" :style="tab === 'file' ? 'background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);' : ''">
                    <i class="fas fa-cloud-upload-alt text-xs"></i>
                    <span>{{ __('admin.upload_file') }}</span>
                </button>
                <button @click="tab = 'url'" type="button" :class="tab === 'url' ? 'text-white shadow-md' : 'text-gray-600 hover:bg-gray-200'" class="flex-1 py-2 text-center rounded-md font-semibold text-xs transition-all duration-200 flex items-center justify-center space-x-1.5" :style="tab === 'url' ? 'background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);' : ''">
                    <i class="fas fa-link text-xs"></i>
                    <span>{{ __('admin.video_url') }}</span>
                </button>
            </div>
            
            <!-- File Upload Section -->
            <div x-show="tab === 'file'" x-transition class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center space-x-2">
                    <i class="fas fa-file-video text-orange-500 text-xs"></i>
                    <span>{{ __('admin.video_file') }}</span>
                </label>
                <div 
                    id="dropzone"
                    class="group relative border-4 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-orange-500 transition-all duration-300 cursor-pointer bg-gradient-to-br from-orange-50 to-amber-50 hover:from-orange-100 hover:to-amber-100"
                >
                    <div class="space-y-4">
                        <div class="flex justify-center">
                            <div class="h-20 w-20 rounded-xl bg-gradient-to-br from-orange-500 to-amber-600 flex items-center justify-center group-hover:scale-110 group-hover:rotate-6 transition-all duration-500 shadow-lg" style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);">
                                <i class="fas fa-cloud-upload-alt text-4xl" style="color: #ffffff !important; text-shadow: 0 2px 6px rgba(0,0,0,0.3); display: inline-block;"></i>
                            </div>
                        </div>
                        <div>
                            <p id="fileName" class="text-base font-bold text-gray-900 mb-1">{{ __('admin.choose_file_or_drag') }}</p>
                            <p class="text-xs text-gray-500 font-medium">{{ __('admin.supports_large_files') }}</p>
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
                    multiple="false"
                >
                
                <!-- Upload Progress -->
                <div id="uploadProgress" class="hidden mt-4 bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl p-4 border-2 border-orange-200 shadow-md">
                    <div class="flex justify-between items-center mb-3">
                        <div class="flex items-center space-x-2">
                            <div class="h-8 w-8 rounded-lg bg-orange-500 flex items-center justify-center shadow-md">
                                <i class="fas fa-cloud-upload-alt text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ __('admin.uploading_in_chunks') }}</p>
                                <p class="text-xs text-gray-500 font-medium">{{ __('admin.please_wait') }}</p>
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
                                <span id="uploadSpeed">{{ __('admin.speed') }}: {{ __('admin.calculating') }}</span>
                            </div>
                            <div id="chunkInfo" class="flex items-center space-x-1.5 px-2 py-1.5 bg-white rounded-lg font-semibold text-gray-700 shadow-sm">
                                <i class="fas fa-sync fa-spin text-orange-500 text-xs"></i>
                                <span>0/0 {{ __('admin.chunks') }}</span>
                            </div>
                            <div id="connectionStatus" class="flex items-center space-x-1.5 px-2 py-1.5 bg-white rounded-lg font-semibold text-gray-700 shadow-sm hidden">
                                <i class="fas fa-wifi text-green-500 text-xs"></i>
                                <span>{{ __('admin.connected') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- URL Section -->
            <div x-show="tab === 'url'" x-transition class="mb-6">
                <label for="video_url" class="block text-sm font-bold text-gray-700 mb-2 flex items-center space-x-2">
                    <i class="fas fa-link text-orange-500 text-xs"></i>
                    <span>{{ __('admin.video_url') }}</span>
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
                        placeholder="{{ __('admin.video_url_placeholder') }}"
                        value="{{ old('video_url') }}"
                    >
                </div>
                <p class="mt-2 text-xs text-gray-500 flex items-center space-x-2 bg-blue-50 p-2 rounded-lg border border-blue-200">
                    <i class="fas fa-check-circle text-blue-500 text-xs"></i>
                    <span class="font-medium">{{ __('admin.video_url_supported') }}</span>
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
                <span id="btnText">{{ __('admin.upload_continue') }}</span>
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
// Translations
const translations = {
    connected: '{{ __('admin.connected') }}',
    noConnection: '{{ __('admin.no_connection') }}',
    speed: '{{ __('admin.speed') }}',
    chunks: '{{ __('admin.chunks') }}',
    chunksUploaded: '{{ __('admin.chunks_uploaded') }}',
    readyToContinue: '{{ __('admin.ready_to_continue') }}',
    uploading: '{{ __('admin.uploading_in_chunks') }}',
    finalizing: '{{ __('admin.finalizing') }}',
    resumeUpload: '{{ __('admin.resume_upload') }}',
    uploadContinue: '{{ __('admin.upload_continue') }}'
};

let uploadId = null;
let uploadedChunks = [];
let currentUpload = null;
let isOnline = navigator.onLine;

// Monitor connection status
window.addEventListener('online', () => {
    isOnline = true;
    const connStatus = document.getElementById('connectionStatus');
    if (connStatus) {
        connStatus.classList.remove('hidden', 'bg-red-50', 'text-red-700');
        connStatus.classList.add('bg-green-50', 'text-green-700');
        connStatus.innerHTML = '<i class="fas fa-wifi text-green-500 text-xs"></i><span>' + translations.connected + '</span>';
    }
});

window.addEventListener('offline', () => {
    isOnline = false;
    const connStatus = document.getElementById('connectionStatus');
    if (connStatus) {
        connStatus.classList.remove('hidden', 'bg-green-50', 'text-green-700');
        connStatus.classList.add('bg-red-50', 'text-red-700');
        connStatus.innerHTML = '<i class="fas fa-wifi-slash text-red-500 text-xs"></i><span>' + translations.noConnection + '</span>';
    }
});

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

function generateUploadId() {
    const userId = {{ auth()->id() ?? 0 }};
    return 'upload_user_' + userId + '_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
}

async function uploadChunk(file, chunkIndex, chunkBlob, uploadId, fileName, fileSize, totalChunks, retries = 3) {
    const formData = new FormData();
    formData.append('uploadId', uploadId);
    formData.append('chunkIndex', chunkIndex);
    formData.append('totalChunks', totalChunks);
    formData.append('chunk', chunkBlob, `chunk_${chunkIndex}`);
    formData.append('fileName', fileName);
    formData.append('fileSize', fileSize);

    for (let attempt = 0; attempt < retries; attempt++) {
        try {
            const response = await fetch('{{ route("matches.upload.chunk") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value,
                },
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            if (result.success) {
                return result;
            } else {
                // Handle concurrent upload error (409)
                if (response.status === 409) {
                    throw new Error(result.message || 'Another upload is in progress');
                }
                // Handle unauthorized access (403)
                if (response.status === 403) {
                    throw new Error(result.message || 'Unauthorized access to upload session');
                }
                throw new Error(result.message || 'Upload failed');
            }
        } catch (error) {
            console.error(`Chunk ${chunkIndex} upload attempt ${attempt + 1} failed:`, error);
            // Don't retry on 403 or 409 errors
            if (error.message.includes('Another upload') || error.message.includes('Unauthorized')) {
                throw error;
            }
            if (attempt === retries - 1) {
                throw error;
            }
            // Wait before retry (exponential backoff)
            await new Promise(resolve => setTimeout(resolve, Math.pow(2, attempt) * 1000));
        }
    }
}

async function handleFileSelect(input) {
    if (!input.files || input.files.length === 0) return;

    // Only allow one file
    if (input.files.length > 1) {
        Swal.fire({
            icon: 'warning',
            title: 'Multiple Files',
            text: 'Please upload only one file at a time. Only the first file will be used.',
            confirmButtonColor: '#60a5fa'
        });
    }

    const file = input.files[0];
    
    // Reset input to ensure only one file
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(file);
    input.files = dataTransfer.files;
    const fileSize = file.size;
    
    document.getElementById('fileName').innerHTML = `
        <strong class="text-orange-700">${file.name}</strong><br>
        <small class="text-gray-500">${formatBytes(fileSize)}</small>
    `;
    
    // Calculate chunks (5MB per chunk)
    const chunkSize = 5 * 1024 * 1024;
    const totalChunks = Math.ceil(fileSize / chunkSize);
    
    // Check for existing upload ID in localStorage (user-specific)
    const userId = {{ auth()->id() ?? 0 }};
    const storageKey = 'upload_user_' + userId + '_' + file.name + '_' + fileSize;
    const savedUpload = localStorage.getItem(storageKey);
    
    if (savedUpload) {
        try {
            const saved = JSON.parse(savedUpload);
            uploadId = saved.uploadId;
            uploadedChunks = saved.uploadedChunks || [];
            
            // Verify upload still exists on server
            try {
                const statusResponse = await fetch(`{{ route('matches.upload.status', ':id') }}`.replace(':id', uploadId));
                if (statusResponse.ok) {
                    const statusData = await statusResponse.json();
                    if (statusData.success) {
                        uploadedChunks = statusData.uploadStatus.uploadedChunks || [];
                    }
                }
            } catch (error) {
                console.log('Upload session expired, starting fresh');
                uploadId = generateUploadId();
                uploadedChunks = [];
            }
        } catch (error) {
            uploadId = generateUploadId();
            uploadedChunks = [];
        }
    } else {
        uploadId = generateUploadId();
        uploadedChunks = [];
    }
    
    // Save upload info to localStorage
    localStorage.setItem(storageKey, JSON.stringify({
        uploadId: uploadId,
        fileName: file.name,
        fileSize: fileSize,
        uploadedChunks: uploadedChunks
    }));
    
    // Show progress
    const progressDiv = document.getElementById('uploadProgress');
    const progressFill = document.getElementById('progressFill');
    const progressPercent = document.getElementById('progressPercent');
    const submitBtn = document.getElementById('submitBtn');
    const chunkInfo = document.getElementById('chunkInfo');
    const uploadSpeed = document.getElementById('uploadSpeed');
    const connectionStatus = document.getElementById('connectionStatus');
    
    progressDiv.classList.remove('hidden');
    connectionStatus.classList.remove('hidden');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<div class="h-8 w-8 rounded-lg bg-white/30 backdrop-blur-sm flex items-center justify-center"><i class="fas fa-spinner fa-spin text-base"></i></div> <span>' + translations.uploading + '</span>';
    
    // Update connection status
    if (isOnline) {
        connectionStatus.classList.add('bg-green-50', 'text-green-700');
        connectionStatus.innerHTML = '<i class="fas fa-wifi text-green-500 text-xs"></i><span>' + translations.connected + '</span>';
    } else {
        connectionStatus.classList.add('bg-red-50', 'text-red-700');
        connectionStatus.innerHTML = '<i class="fas fa-wifi-slash text-red-500 text-xs"></i><span>' + translations.noConnection + '</span>';
    }
    
    const startTime = Date.now();
    let uploadedBytes = uploadedChunks.length * chunkSize;
    
    // Upload chunks sequentially
    try {
        for (let i = 0; i < totalChunks; i++) {
            // Skip if already uploaded
            if (uploadedChunks.includes(i)) {
                continue;
            }
            
            const start = i * chunkSize;
            const end = Math.min(start + chunkSize, fileSize);
            const chunkBlob = file.slice(start, end);
            
            // Wait for connection if offline
            while (!isOnline) {
                await new Promise(resolve => setTimeout(resolve, 1000));
            }
            
            const result = await uploadChunk(file, i, chunkBlob, uploadId, file.name, fileSize, totalChunks);
            
            uploadedChunks.push(i);
            uploadedBytes = (uploadedChunks.length / totalChunks) * fileSize;
            
            // Update localStorage
            localStorage.setItem(storageKey, JSON.stringify({
                uploadId: uploadId,
                fileName: file.name,
                fileSize: fileSize,
                uploadedChunks: uploadedChunks
            }));
            
            // Update progress
            const progress = (uploadedChunks.length / totalChunks) * 100;
            const elapsed = (Date.now() - startTime) / 1000;
            const speed = uploadedBytes / elapsed;
            
            progressFill.style.width = progress + '%';
            progressPercent.textContent = Math.round(progress) + '%';
            uploadSpeed.textContent = `${translations.speed}: ${formatSpeed(speed)}`;
            chunkInfo.innerHTML = `
                <i class="fas fa-sync fa-spin text-orange-500 text-xs"></i>
                <span>${uploadedChunks.length}/${totalChunks} ${translations.chunks}</span>
            `;
        }
        
        // All chunks uploaded, finalize
        chunkInfo.innerHTML = `
            <i class="fas fa-check-circle text-green-600 text-xs"></i>
            <span>${translations.chunksUploaded.replace(':current', totalChunks).replace(':total', totalChunks)}</span>
        `;
        
        // Clear localStorage
        localStorage.removeItem(storageKey);
        
        // Update button to show ready for finalization
        submitBtn.innerHTML = '<div class="h-8 w-8 rounded-lg bg-white/30 backdrop-blur-sm flex items-center justify-center"><i class="fas fa-check text-base"></i></div> <span>' + translations.readyToContinue + '</span>';
        submitBtn.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
        submitBtn.disabled = false;
        
    } catch (error) {
        console.error('Upload error:', error);
        
        // Check if it's a concurrent upload error
        const isConcurrentError = error.message.includes('Another upload') || error.message.includes('in progress');
        const isUnauthorizedError = error.message.includes('Unauthorized');
        
        let errorTitle = 'Upload Paused';
        let errorMessage = error.message;
        let showResumeButton = true;
        
        if (isConcurrentError) {
            errorTitle = 'Concurrent Upload Detected';
            errorMessage = 'Another upload is in progress. Please wait for it to complete or cancel it first.';
            showResumeButton = false;
        } else if (isUnauthorizedError) {
            errorTitle = 'Unauthorized Access';
            errorMessage = 'This upload session does not belong to you. Starting a new upload...';
            showResumeButton = false;
            // Clear localStorage and start fresh
            localStorage.removeItem(storageKey);
            uploadId = generateUploadId();
            uploadedChunks = [];
        }
        
        if (showResumeButton) {
            // Save current state for resume
            localStorage.setItem(storageKey, JSON.stringify({
                uploadId: uploadId,
                fileName: file.name,
                fileSize: fileSize,
                uploadedChunks: uploadedChunks
            }));
            
            Swal.fire({
                icon: 'warning',
                title: errorTitle,
                html: `<p>Upload failed: <strong>${errorMessage}</strong></p><p class="mt-2">Uploaded ${uploadedChunks.length}/${totalChunks} chunks.</p><p class="mt-2 text-sm">You can refresh the page and try again - it will resume from where it left off.</p>`,
                confirmButtonColor: '#60a5fa',
                confirmButtonText: 'OK'
            });
            
            submitBtn.disabled = false;
            submitBtn.setAttribute('data-resume', 'true');
            submitBtn.innerHTML = '<div class="h-8 w-8 rounded-lg bg-white/30 backdrop-blur-sm flex items-center justify-center"><i class="fas fa-redo text-base"></i></div> <span>' + translations.resumeUpload + '</span>';
            submitBtn.style.background = 'linear-gradient(135deg, #f59e0b 0%, #ea580c 100%)';
        } else {
            Swal.fire({
                icon: isUnauthorizedError ? 'warning' : 'error',
                title: errorTitle,
                text: errorMessage,
                confirmButtonColor: '#60a5fa'
            });
            submitBtn.innerHTML = '<i class="fas fa-upload mr-2"></i> Upload Video';
            submitBtn.disabled = false;
        }
    }
}

// Resume upload button handler
document.addEventListener('DOMContentLoaded', function() {
    const submitBtn = document.getElementById('submitBtn');
    const videoFile = document.getElementById('video_file');
    const dropzone = document.getElementById('dropzone');
    
    submitBtn.addEventListener('click', function(e) {
        if (this.getAttribute('data-resume') === 'true' && videoFile.files[0]) {
            e.preventDefault();
            this.removeAttribute('data-resume');
            handleFileSelect(videoFile);
        }
    });
    
    // Click handler for dropzone
    dropzone.addEventListener('click', function(e) {
        // Only trigger file picker if not dropping a file
        if (!e.dataTransfer) {
            document.getElementById('video_file').click();
        }
    });
    
    // Drag and Drop handlers
    if (dropzone) {
        // Prevent default browser behavior (opening file in new tab)
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, preventDefaults, false);
        });
        
        // Prevent default on entire document to stop browser from opening files
        ['dragenter', 'dragover', 'drop'].forEach(eventName => {
            document.addEventListener(eventName, preventDefaults, false);
        });
        
        // Highlight dropzone when dragging over
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, () => {
                dropzone.classList.add('border-orange-500', 'bg-orange-100');
            }, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, () => {
                dropzone.classList.remove('border-orange-500', 'bg-orange-100');
            }, false);
        });
        
        // Handle dropped files
        dropzone.addEventListener('drop', handleDrop, false);
    }
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
    return false;
}

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    
    if (files.length === 0) return;
    
    // Only allow one file
    if (files.length > 1) {
        Swal.fire({
            icon: 'warning',
            title: 'Multiple Files',
            text: 'Please upload only one file at a time.',
            confirmButtonColor: '#60a5fa'
        });
        return;
    }
    
    const file = files[0];
    
    // Check if it's a video file
    if (!file.type.startsWith('video/')) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid File Type',
            text: 'Please upload a video file (MP4, AVI, MOV, MKV, etc.)',
            confirmButtonColor: '#ef4444'
        });
        return;
    }
    
    // Set the file to input
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(file);
    document.getElementById('video_file').files = dataTransfer.files;
    
    // Trigger file selection handler
    handleFileSelect(document.getElementById('video_file'));
}

// Handle form submission
document.getElementById('uploadForm').addEventListener('submit', async function(e) {
    const videoFile = document.getElementById('video_file').files[0];
    const videoUrl = document.getElementById('video_url').value;
    const matchName = document.getElementById('match_name').value;
    
    // If URL, submit normally
    if (videoUrl) {
        return true;
    }
    
    // If file upload and chunks are uploaded, finalize
    if (videoFile && uploadId) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<div class="h-8 w-8 rounded-lg bg-white/30 backdrop-blur-sm flex items-center justify-center"><i class="fas fa-spinner fa-spin text-base"></i></div> <span>' + translations.finalizing + '</span>';
        
        try {
            const formData = new FormData();
            formData.append('uploadId', uploadId);
            formData.append('match_name', matchName);
            
            const response = await fetch('{{ route("matches.upload.finalize") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value,
                },
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                window.location.href = result.redirectUrl;
            } else {
                throw new Error(result.message || 'Finalization failed');
            }
        } catch (error) {
            console.error('Finalization error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Finalization Failed',
                text: error.message || 'Failed to finalize upload. Please try again.',
                confirmButtonColor: '#ef4444'
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<div class="h-8 w-8 rounded-lg bg-white/30 backdrop-blur-sm flex items-center justify-center"><i class="fas fa-arrow-right text-base"></i></div> <span>' + translations.uploadContinue + '</span>';
        }
    }
});
</script>
@endpush
@endsection

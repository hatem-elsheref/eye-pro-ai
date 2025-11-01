@if(session('success'))
<div class="mb-6 rounded-xl bg-green-50 border-l-4 border-green-500 p-4 shadow-md animate-slide-in">
    <div class="flex items-start space-x-3">
        <div class="flex-shrink-0">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100">
                <i class="fas fa-check-circle text-green-600 text-lg"></i>
            </div>
        </div>
        <div class="flex-1">
            <h3 class="text-sm font-bold text-green-900">Success!</h3>
            <p class="mt-1 text-sm text-green-700">{{ session('success') }}</p>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="text-green-400 hover:text-green-600">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('error'))
<div class="mb-6 rounded-xl bg-red-50 border-l-4 border-red-500 p-4 shadow-md animate-slide-in">
    <div class="flex items-start space-x-3">
        <div class="flex-shrink-0">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100">
                <i class="fas fa-exclamation-circle text-red-600 text-lg"></i>
            </div>
        </div>
        <div class="flex-1">
            <h3 class="text-sm font-bold text-red-900">Error!</h3>
            <p class="mt-1 text-sm text-red-700">{{ session('error') }}</p>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-600">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('warning'))
<div class="mb-6 rounded-xl bg-amber-50 border-l-4 border-amber-500 p-4 shadow-md animate-slide-in">
    <div class="flex items-start space-x-3">
        <div class="flex-shrink-0">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100">
                <i class="fas fa-exclamation-triangle text-amber-600 text-lg"></i>
            </div>
        </div>
        <div class="flex-1">
            <h3 class="text-sm font-bold text-amber-900">Warning!</h3>
            <p class="mt-1 text-sm text-amber-700">{{ session('warning') }}</p>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="text-amber-400 hover:text-amber-600">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('info'))
<div class="mb-6 rounded-xl bg-blue-50 border-l-4 border-blue-500 p-4 shadow-md animate-slide-in">
    <div class="flex items-start space-x-3">
        <div class="flex-shrink-0">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100">
                <i class="fas fa-info-circle text-blue-600 text-lg"></i>
            </div>
        </div>
        <div class="flex-1">
            <h3 class="text-sm font-bold text-blue-900">Info!</h3>
            <p class="mt-1 text-sm text-blue-700">{{ session('info') }}</p>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="text-blue-400 hover:text-blue-600">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if($errors->any())
<div class="mb-6 rounded-xl bg-red-50 border-l-4 border-red-500 p-4 shadow-md animate-slide-in">
    <div class="flex items-start space-x-3">
        <div class="flex-shrink-0">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100">
                <i class="fas fa-exclamation-circle text-red-600 text-lg"></i>
            </div>
        </div>
        <div class="flex-1">
            <h3 class="text-sm font-bold text-red-900">Please correct the following errors:</h3>
            <ul class="mt-2 text-sm text-red-700 space-y-1 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-600">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('success'))
<div class="mb-6 rounded-xl bg-green-50 border-l-4 border-green-500 p-4 shadow-md animate-slide-in alert-border-rtl">
    <div class="flex items-start space-x-3 alert-success-container">
        <div class="flex-shrink-0">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100">
                <i class="fas fa-check-circle text-green-600 text-lg alert-success-icon"></i>
            </div>
        </div>
        <div class="flex-1">
            <h3 class="text-sm font-bold text-green-900 alert-title-rtl">{{ __('admin.success') }}</h3>
            <p class="mt-1 text-sm text-green-700 alert-message-rtl">{{ session('success') }}</p>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="text-green-400 hover:text-green-600">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('error'))
<div class="mb-6 rounded-xl bg-red-50 border-l-4 border-red-500 p-4 shadow-md animate-slide-in alert-border-rtl">
    <div class="flex items-start space-x-3">
        <div class="flex-shrink-0">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100">
                <i class="fas fa-exclamation-circle text-red-600 text-lg"></i>
            </div>
        </div>
        <div class="flex-1">
            <h3 class="text-sm font-bold text-red-900 alert-title-rtl">{{ __('admin.error') }}</h3>
            <p class="mt-1 text-sm text-red-700 alert-message-rtl">{{ session('error') }}</p>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-600">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('warning'))
<div class="mb-6 rounded-xl bg-amber-50 border-l-4 border-amber-500 p-4 shadow-md animate-slide-in alert-border-rtl">
    <div class="flex items-start space-x-3">
        <div class="flex-shrink-0">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100">
                <i class="fas fa-exclamation-triangle text-amber-600 text-lg"></i>
            </div>
        </div>
        <div class="flex-1">
            <h3 class="text-sm font-bold text-amber-900 alert-title-rtl">{{ __('admin.warning') }}</h3>
            <p class="mt-1 text-sm text-amber-700 alert-message-rtl">{{ session('warning') }}</p>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="text-amber-400 hover:text-amber-600">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('info'))
<div class="mb-6 rounded-xl bg-blue-50 border-l-4 border-blue-500 p-4 shadow-md animate-slide-in alert-border-rtl">
    <div class="flex items-start space-x-3">
        <div class="flex-shrink-0">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100">
                <i class="fas fa-info-circle text-blue-600 text-lg"></i>
            </div>
        </div>
        <div class="flex-1">
            <h3 class="text-sm font-bold text-blue-900 alert-title-rtl">{{ __('admin.info') }}</h3>
            <p class="mt-1 text-sm text-blue-700 alert-message-rtl">{{ session('info') }}</p>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="text-blue-400 hover:text-blue-600">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if($errors->any())
<div class="mb-6 rounded-xl bg-red-50 border-l-4 border-red-500 p-4 shadow-md animate-slide-in alert-border-rtl">
    <div class="flex items-start space-x-3">
        <div class="flex-shrink-0">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100">
                <i class="fas fa-exclamation-circle text-red-600 text-lg"></i>
            </div>
        </div>
        <div class="flex-1">
            <h3 class="text-sm font-bold text-red-900 alert-title-rtl">{{ __('admin.please_correct_errors') }}</h3>
            <ul class="mt-2 text-sm text-red-700 space-y-1 list-disc list-inside alert-message-rtl">
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

<style>
    /* RTL Alert Border Styles */
    [dir='rtl'] .alert-border-rtl {
        border-left: 0 !important;
        border-right: 4px solid !important;
    }
    
    [dir='rtl'] .alert-border-rtl.border-green-500 {
        border-right-color: rgb(34 197 94) !important;
    }
    
    [dir='rtl'] .alert-border-rtl.border-red-500 {
        border-right-color: rgb(239 68 68) !important;
    }
    
    [dir='rtl'] .alert-border-rtl.border-amber-500 {
        border-right-color: rgb(245 158 11) !important;
    }
    
    [dir='rtl'] .alert-border-rtl.border-blue-500 {
        border-right-color: rgb(59 130 246) !important;
    }
    
    /* RTL Alert Title Margin - Add space between text and icon */
    [dir='rtl'] .alert-title-rtl {
        margin-right: 1rem !important;
    }
    
    /* RTL Alert Message Margin - Add space for message text */
    [dir='rtl'] .alert-message-rtl {
        margin-right: 1rem !important;
    }
</style>

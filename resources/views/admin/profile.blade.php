@extends('admin.layouts.master')

@section('title', __('admin.profile') . ' - Eye Pro')
@section('page-title', __('admin.profile_settings'))

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    
    <!-- Profile Header -->
    <div class="relative overflow-hidden rounded-2xl p-6 shadow-lg border border-blue-200" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-white opacity-10"></div>
        <div class="absolute bottom-0 left-0 -mb-6 -ml-6 h-32 w-32 rounded-full bg-white opacity-10"></div>
        <div class="relative z-10 flex items-center space-x-4 profile-header">
            <div class="h-24 w-24 rounded-2xl flex items-center justify-center text-white font-extrabold text-4xl shadow-2xl" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px);">
                @php
                    $name = Auth::user()->name ?? __('admin.user');
                    $nameParts = explode(' ', $name);
                    $initials = count($nameParts) >= 2 
                        ? strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1))
                        : strtoupper(substr($name, 0, 2));
                @endphp
                {{ $initials }}
            </div>
            <div class="flex-1">
                <h1 class="text-3xl font-extrabold text-white mb-2 drop-shadow-lg">{{ Auth::user()->name ?? __('admin.user') }}</h1>
                <p class="text-blue-50 font-medium text-lg">{{ Auth::user()->email ?? '' }}</p>
                <div class="flex items-center space-x-3 mt-3 profile-badges">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur-sm profile-role-badge">
                        <i class="fas fa-shield-alt profile-role-icon"></i>
                        <span>{{ Auth::user()->is_admin ? __('admin.admin') : __('admin.user') }}</span>
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-400 text-green-900 profile-status-badge">
                        <span class="h-1.5 w-1.5 rounded-full bg-green-900 profile-status-dot"></span>
                        <span>{{ __('admin.active') }}</span>
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Personal Information -->
    <div class="bg-white rounded-3xl shadow-xl p-8 border-2 border-gray-100">
        <div class="flex items-center space-x-3 mb-6 profile-section-header">
            <div class="h-12 w-12 rounded-xl flex items-center justify-center shadow-lg profile-section-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="fas fa-user text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">{{ __('admin.personal_information') }}</h2>
                <p class="text-sm text-gray-500">{{ __('admin.update_account_details') }}</p>
            </div>
        </div>
        
        <form action="{{ route('profile.update') }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-sm font-bold text-gray-700 mb-2">{{ __('admin.full_name') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400 profile-input-icon"></i>
                        </div>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="block w-full pl-11 pr-4 py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-teal-100 transition-all duration-200"
                            value="{{ Auth::user()->name ?? old('name') }}"
                            required
                        >
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-bold text-gray-700 mb-2">{{ __('admin.email_address') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400 profile-input-icon"></i>
                        </div>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="block w-full pl-11 pr-4 py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-teal-100 transition-all duration-200"
                            value="{{ Auth::user()->email ?? old('email') }}"
                            required
                        >
                    </div>
                </div>
            </div>
            
            <div>
                <label for="phone" class="block text-sm font-bold text-gray-700 mb-2">{{ __('admin.phone_number_optional') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-phone text-gray-400 profile-input-icon"></i>
                    </div>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone" 
                        class="block w-full pl-11 pr-4 py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200"
                        value="{{ Auth::user()->phone ?? old('phone') }}"
                        placeholder="+1 (555) 000-0000"
                    >
                </div>
            </div>
            
            <button type="submit" class="inline-flex items-center space-x-2 px-4 py-2 text-sm font-bold rounded-lg text-white shadow-md hover:shadow-lg transition-shadow duration-200 bg-blue-600 hover:bg-blue-700 profile-save-btn">
                <i class="fas fa-save profile-save-icon"></i>
                <span>{{ __('admin.save_changes') }}</span>
            </button>
        </form>
    </div>
    
    <!-- Change Password -->
    <div class="bg-white rounded-3xl shadow-xl p-8 border-2 border-gray-100">
        <div class="flex items-center space-x-3 mb-6 profile-section-header">
            <div class="h-12 w-12 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg profile-section-icon">
                <i class="fas fa-key text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">{{ __('admin.change_password') }}</h2>
                <p class="text-sm text-gray-500">{{ __('admin.update_password_regularly') }}</p>
            </div>
        </div>
        
        <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-5" x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
            @csrf
            @method('PUT')
            
            <div>
                <label for="current_password" class="block text-sm font-bold text-gray-700 mb-2">{{ __('admin.current_password') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400 profile-input-icon"></i>
                    </div>
                    <input 
                        :type="showCurrent ? 'text' : 'password'" 
                        id="current_password" 
                        name="current_password" 
                        class="block w-full pl-11 pr-12 py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200"
                        required
                    >
                    <button @click="showCurrent = !showCurrent" type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-blue-600 transition-colors">
                        <i :class="showCurrent ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-lg"></i>
                    </button>
                </div>
            </div>
            
            <div>
                <label for="new_password" class="block text-sm font-bold text-gray-700 mb-2">{{ __('admin.new_password') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400 profile-input-icon"></i>
                    </div>
                    <input 
                        :type="showNew ? 'text' : 'password'" 
                        id="new_password" 
                        name="new_password" 
                        class="block w-full pl-11 pr-12 py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200"
                        required
                    >
                    <button @click="showNew = !showNew" type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-blue-600 transition-colors">
                        <i :class="showNew ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-lg"></i>
                    </button>
                </div>
            </div>
            
            <div>
                <label for="new_password_confirmation" class="block text-sm font-bold text-gray-700 mb-2">{{ __('admin.confirm_new_password') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400 profile-input-icon"></i>
                    </div>
                    <input 
                        :type="showConfirm ? 'text' : 'password'" 
                        id="new_password_confirmation" 
                        name="new_password_confirmation" 
                        class="block w-full pl-11 pr-12 py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200"
                        required
                    >
                    <button @click="showConfirm = !showConfirm" type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-blue-600 transition-colors">
                        <i :class="showConfirm ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-lg"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="inline-flex items-center space-x-2 px-4 py-2 text-sm font-bold rounded-lg text-white bg-blue-600 hover:bg-blue-700 shadow-md hover:shadow-lg transition-shadow duration-200 profile-update-password-btn">
                <i class="fas fa-key profile-update-password-icon"></i>
                <span>{{ __('admin.update_password') }}</span>
            </button>
        </form>
    </div>
    
    <!-- Danger Zone - Hidden and Disabled -->
    <div class="bg-white rounded-3xl shadow-xl p-8 border-2 border-red-200" style="display: none;">
        <div class="flex items-center space-x-3 mb-6">
            <div class="h-12 w-12 rounded-xl bg-red-600 flex items-center justify-center shadow-lg">
                <i class="fas fa-exclamation-triangle text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-extrabold text-red-600">{{ __('admin.danger_zone') }}</h2>
                <p class="text-sm text-gray-500">{{ __('admin.irreversible_actions') }}</p>
            </div>
        </div>
        
        <div class="bg-red-50 border-2 border-red-200 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-red-900 mb-2">{{ __('admin.delete_account') }}</h3>
            <p class="text-sm text-red-700 mb-4">{{ __('admin.delete_account_warning') }}</p>
            
            <form id="deleteAccountForm" action="{{ route('profile.delete') }}" method="POST">
                @csrf
                @method('DELETE')
                
                <button type="submit" class="inline-flex items-center space-x-2 px-4 py-2 text-sm font-bold rounded-lg text-white bg-red-600 hover:bg-red-700 shadow-md hover:shadow-lg transition-shadow duration-200" disabled>
                    <i class="fas fa-trash"></i>
                    <span>{{ __('admin.delete_my_account') }}</span>
                </button>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Profile page - spacing between icons and text */
    .profile-header {
        gap: 1rem !important;
    }
    
    .profile-badges {
        gap: 0.75rem !important;
    }
    
    .profile-role-badge,
    .profile-status-badge {
        gap: 0.375rem !important;
    }
    
    .profile-section-header {
        gap: 0.75rem !important;
    }
    
    .profile-save-btn,
    .profile-update-password-btn {
        gap: 0.5rem !important;
    }
    
    .profile-role-icon,
    .profile-status-dot,
    .profile-save-icon,
    .profile-update-password-icon {
        margin-right: 0 !important;
        margin-left: 0 !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteForm = document.getElementById('deleteAccountForm');
    
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const translations = {
                title: '{{ __('admin.are_you_sure') }}',
                message: '{{ __('admin.delete_account_confirm_message') }}',
                confirm: '{{ __('admin.yes_delete_my_account') }}',
                cancel: '{{ __('admin.cancel') }}'
            };
            
            Swal.fire({
                icon: 'warning',
                title: translations.title,
                html: '<strong>' + translations.message + '</strong><br><br>{{ __('admin.this_action_cannot_be_undone') }}',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: translations.confirm,
                cancelButtonText: translations.cancel,
                reverseButtons: true
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

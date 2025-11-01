@extends('admin.layouts.master')

@section('title', 'Profile - Eye Pro')
@section('page-title', 'Profile Settings')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    
    <!-- Profile Header -->
    <div class="relative overflow-hidden rounded-2xl p-6 shadow-lg border border-blue-200" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-white opacity-10"></div>
        <div class="absolute bottom-0 left-0 -mb-6 -ml-6 h-32 w-32 rounded-full bg-white opacity-10"></div>
        <div class="relative z-10 flex items-center space-x-4">
            <div class="h-24 w-24 rounded-2xl flex items-center justify-center text-white font-extrabold text-4xl shadow-2xl" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px);">
                @php
                    $name = Auth::user()->name ?? 'User';
                    $nameParts = explode(' ', $name);
                    $initials = count($nameParts) >= 2 
                        ? strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1))
                        : strtoupper(substr($name, 0, 2));
                @endphp
                {{ $initials }}
            </div>
            <div class="flex-1">
                <h1 class="text-3xl font-extrabold text-white mb-2 drop-shadow-lg">{{ Auth::user()->name ?? 'User' }}</h1>
                <p class="text-blue-50 font-medium text-lg">{{ Auth::user()->email ?? '' }}</p>
                <div class="flex items-center space-x-3 mt-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur-sm">
                        <i class="fas fa-shield-alt mr-1"></i>
                        {{ Auth::user()->is_admin ? 'Admin' : 'User' }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-400 text-green-900">
                        <span class="h-1.5 w-1.5 rounded-full bg-green-900 mr-1.5"></span>
                        Active
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Personal Information -->
    <div class="bg-white rounded-3xl shadow-xl p-8 border-2 border-gray-100">
        <div class="flex items-center space-x-3 mb-6">
            <div class="h-12 w-12 rounded-xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="fas fa-user text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">Personal Information</h2>
                <p class="text-sm text-gray-500">Update your account details</p>
            </div>
        </div>
        
        <form action="{{ route('profile.update') }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Full Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
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
                    <label for="email" class="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
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
                <label for="phone" class="block text-sm font-bold text-gray-700 mb-2">Phone Number (Optional)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-phone text-gray-400"></i>
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
            
            <button type="submit" class="inline-flex items-center space-x-2 px-4 py-2 text-sm font-bold rounded-lg text-white shadow-md hover:shadow-lg transition-shadow duration-200 bg-blue-600 hover:bg-blue-700">
                <i class="fas fa-save"></i>
                <span>Save Changes</span>
            </button>
        </form>
    </div>
    
    <!-- Change Password -->
    <div class="bg-white rounded-3xl shadow-xl p-8 border-2 border-gray-100">
        <div class="flex items-center space-x-3 mb-6">
            <div class="h-12 w-12 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg">
                <i class="fas fa-key text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">Change Password</h2>
                <p class="text-sm text-gray-500">Update your password regularly</p>
            </div>
        </div>
        
        <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-5" x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
            @csrf
            @method('PUT')
            
            <div>
                <label for="current_password" class="block text-sm font-bold text-gray-700 mb-2">Current Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
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
                <label for="new_password" class="block text-sm font-bold text-gray-700 mb-2">New Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
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
                <label for="new_password_confirmation" class="block text-sm font-bold text-gray-700 mb-2">Confirm New Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
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
            
            <button type="submit" class="inline-flex items-center space-x-2 px-4 py-2 text-sm font-bold rounded-lg text-white bg-blue-600 hover:bg-blue-700 shadow-md hover:shadow-lg transition-shadow duration-200">
                <i class="fas fa-key"></i>
                <span>Update Password</span>
            </button>
        </form>
    </div>
    
    <!-- Danger Zone -->
    <div class="bg-white rounded-3xl shadow-xl p-8 border-2 border-red-200">
        <div class="flex items-center space-x-3 mb-6">
            <div class="h-12 w-12 rounded-xl bg-red-600 flex items-center justify-center shadow-lg">
                <i class="fas fa-exclamation-triangle text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-extrabold text-red-600">Danger Zone</h2>
                <p class="text-sm text-gray-500">Irreversible actions</p>
            </div>
        </div>
        
        <div class="bg-red-50 border-2 border-red-200 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-red-900 mb-2">Delete Account</h3>
            <p class="text-sm text-red-700 mb-4">Once you delete your account, there is no going back. Please be certain.</p>
            
            <form action="{{ route('profile.delete') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                
                <button type="submit" class="inline-flex items-center space-x-2 px-4 py-2 text-sm font-bold rounded-lg text-white bg-red-600 hover:bg-red-700 shadow-md hover:shadow-lg transition-shadow duration-200">
                    <i class="fas fa-trash"></i>
                    <span>Delete My Account</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

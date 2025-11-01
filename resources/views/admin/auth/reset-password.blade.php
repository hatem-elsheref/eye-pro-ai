@extends('admin.layouts.auth')

@section('title', 'Reset Password - Eye Pro')

@section('content')
<div class="w-full max-w-md">
    <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-100 animate-scale-in">
        <!-- Logo -->
        <div class="flex justify-center mb-6">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-2xl blur opacity-75"></div>
                <div class="relative bg-white rounded-2xl p-3 shadow-xl">
                    <img src="{{ asset('logo.jpeg') }}" alt="Eye Pro" class="h-16 w-16 object-contain">
                </div>
            </div>
        </div>
        
        <!-- Title -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold mb-1" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                Eye Pro
            </h1>
            <p class="text-gray-600 font-medium text-base mb-1">Reset Password</p>
            <p class="text-sm text-gray-400">Enter your new password below</p>
        </div>
        
        <!-- Form -->
        <form action="{{ route('password.update') }}" method="POST" autocomplete="off" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
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
                        class="block w-full pl-11 pr-4 py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200"
                        placeholder="Enter your email address"
                        value="{{ $email ?? old('email') }}"
                        required
                    >
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-red-600 flex items-center space-x-1">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>
            
            <div x-data="{ showPassword: false }">
                <label for="password" class="block text-sm font-bold text-gray-700 mb-2">New Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input 
                        :type="showPassword ? 'text' : 'password'" 
                        id="password" 
                        name="password" 
                        class="block w-full pl-11 pr-12 py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200"
                        placeholder="Create a strong password"
                        autocomplete="new-password"
                        required
                    >
                    <button @click="showPassword = !showPassword" type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-purple-600 transition-colors">
                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-lg"></i>
                    </button>
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600 flex items-center space-x-1">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>
            
            <div x-data="{ showConfirmPassword: false }">
                <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-2">Confirm Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input 
                        :type="showConfirmPassword ? 'text' : 'password'" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="block w-full pl-11 pr-12 py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200"
                        placeholder="Re-enter your password"
                        autocomplete="new-password"
                        required
                    >
                    <button @click="showConfirmPassword = !showConfirmPassword" type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-purple-600 transition-colors">
                        <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-lg"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="w-full flex items-center justify-center space-x-2 py-4 px-6 border-0 text-base font-extrabold rounded-xl text-white shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105 hover:-translate-y-1 mt-6" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                <i class="fas fa-lock text-lg"></i>
                <span>Reset Password</span>
            </button>
        </form>
        
        <!-- Back to Login -->
        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center space-x-2 text-sm font-semibold text-purple-600 hover:text-purple-800 transition-colors px-4 py-2 rounded-lg hover:bg-purple-50">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Sign In</span>
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
    @keyframes scale-in {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    .animate-scale-in {
        animation: scale-in 0.5s ease-out;
    }
</style>
@endpush
@endsection

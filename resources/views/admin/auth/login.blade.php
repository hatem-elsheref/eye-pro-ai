@extends('admin.layouts.auth')

@section('title', 'Sign In - Eye Pro')

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
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold mb-1" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                Eye Pro
            </h1>
            <p class="text-gray-600 font-medium text-base mb-1">{{ __('admin.match_analysis_platform') }}</p>
            <p class="text-sm text-gray-400">{{ __('admin.sign_in_to_continue') }}</p>
        </div>
        
        <!-- Language Switcher -->
        <div class="flex justify-end mb-4">
            @php
                $currentLocale = app()->getLocale();
                $otherLocale = $currentLocale === 'en' ? 'ar' : 'en';
                $currentFlag = $currentLocale === 'en' ? '🇬🇧' : '🇸🇦';
                $otherFlag = $otherLocale === 'en' ? '🇬🇧' : '🇸🇦';
            @endphp
            <a href="{{ route('language.switch', $otherLocale) }}" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                <span class="text-xl">{{ $otherFlag }}</span>
                <span class="text-sm font-semibold text-gray-700">{{ $otherLocale === 'en' ? __('admin.english') : __('admin.arabic') }}</span>
            </a>
        </div>
        
        <!-- Tabs -->
        <div class="flex space-x-2 mb-6 bg-gray-100 p-1.5 rounded-xl shadow-inner">
            <a href="{{ route('login') }}" class="flex-1 py-3 text-center rounded-lg font-bold text-sm transition-all duration-200 text-white shadow-lg" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                {{ __('admin.sign_in') }}
            </a>
            <a href="{{ route('register') }}" class="flex-1 py-3 text-center rounded-lg font-bold text-sm transition-all duration-200 text-gray-600 hover:bg-white hover:shadow-md">
                {{ __('admin.sign_up') }}
            </a>
        </div>
        
        <!-- Form -->
        <form action="{{ route('login.post') }}" method="POST" autocomplete="off" class="space-y-5">
            @csrf
            
            <div>
                <label for="email" class="block text-sm font-bold text-gray-700 mb-2">{{ __('admin.email') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </div>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="block w-full pl-11 pr-4 py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200"
                        placeholder="{{ __('admin.email') }}"
                        value="{{ old('email') }}"
                        autocomplete="off"
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
                <label for="password" class="block text-sm font-bold text-gray-700 mb-2">{{ __('admin.password') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input 
                        :type="showPassword ? 'text' : 'password'" 
                        id="password" 
                        name="password" 
                        class="block w-full pl-11 pr-12 py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200"
                        placeholder="{{ __('admin.password') }}"
                        autocomplete="off"
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
            
            <div class="flex items-center justify-between">
                <label class="flex items-center cursor-pointer group">
                    <input type="checkbox" name="remember" class="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 cursor-pointer">
                    <span class="ml-2 text-sm font-medium text-gray-600 group-hover:text-gray-900 transition-colors">{{ __('admin.remember_me') }}</span>
                </label>
                
                <a href="{{ route('password.request') }}" class="text-sm font-semibold text-purple-600 hover:text-purple-800 transition-colors">
                    {{ __('admin.forgot_password') }}
                </a>
            </div>
            
            <button type="submit" class="w-full flex items-center justify-center space-x-2 py-4 px-6 border-0 text-base font-extrabold rounded-xl text-white shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105 hover:-translate-y-1" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                <i class="fas fa-sign-in-alt text-lg"></i>
                <span>{{ __('admin.sign_in') }}</span>
            </button>
        </form>
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

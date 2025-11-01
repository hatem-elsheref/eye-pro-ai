@extends('admin.layouts.auth')

@section('title', 'Sign Up - Eye Pro')

@section('content')
<div class="w-full max-w-md">
    <div class="bg-white rounded-3xl shadow-2xl p-6 md:p-8 border border-gray-100 animate-scale-in">
        <!-- Logo -->
        <div class="flex justify-center mb-4">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-2xl blur opacity-75"></div>
                <div class="relative bg-white rounded-2xl p-2 shadow-xl">
                    <img src="{{ asset('logo.jpeg') }}" alt="Eye Pro" class="h-12 w-12 object-contain">
                </div>
            </div>
        </div>
        
        <!-- Title -->
        <div class="text-center mb-4">
            <h1 class="text-2xl font-bold mb-1" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                Eye Pro
            </h1>
            <p class="text-sm text-gray-500">Create your account to get started</p>
        </div>
        
        <!-- Tabs -->
        <div class="flex space-x-2 mb-5 bg-gray-100 p-1.5 rounded-xl shadow-inner">
            <a href="{{ route('login') }}" class="flex-1 py-2.5 text-center rounded-lg font-bold text-sm transition-all duration-200 text-gray-600 hover:bg-white hover:shadow-md">
                Sign In
            </a>
            <a href="{{ route('register') }}" class="flex-1 py-2.5 text-center rounded-lg font-bold text-sm transition-all duration-200 text-white shadow-lg" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                Sign Up
            </a>
        </div>
        
        <!-- Form -->
        <form action="{{ route('register.post') }}" method="POST" autocomplete="off" class="space-y-4">
            @csrf
            
            <div>
                <label for="name" class="block text-sm font-bold text-gray-700 mb-1.5">Full Name</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-400 text-sm"></i>
                    </div>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="block w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200"
                        placeholder="Enter your full name"
                        value="{{ old('name') }}"
                        autocomplete="off"
                        required
                    >
                </div>
                @error('name')
                    <p class="mt-1 text-xs text-red-600 flex items-center space-x-1">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>
            
            <div>
                <label for="email" class="block text-sm font-bold text-gray-700 mb-1.5">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400 text-sm"></i>
                    </div>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="block w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200"
                        placeholder="Enter your email address"
                        value="{{ old('email') }}"
                        autocomplete="off"
                        required
                    >
                </div>
                @error('email')
                    <p class="mt-1 text-xs text-red-600 flex items-center space-x-1">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>
            
            <div x-data="{ showPassword: false }">
                <label for="password" class="block text-sm font-bold text-gray-700 mb-1.5">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400 text-sm"></i>
                    </div>
                    <input 
                        :type="showPassword ? 'text' : 'password'" 
                        id="password" 
                        name="password" 
                        class="block w-full pl-10 pr-12 py-3 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200"
                        placeholder="Min. 8 characters"
                        autocomplete="new-password"
                        required
                    >
                    <button @click="showPassword = !showPassword" type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-purple-600 transition-colors">
                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-lg"></i>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-xs text-red-600 flex items-center space-x-1">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>
            
            <div x-data="{ showConfirmPassword: false }">
                <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-1.5">Confirm Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400 text-sm"></i>
                    </div>
                    <input 
                        :type="showConfirmPassword ? 'text' : 'password'" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="block w-full pl-10 pr-12 py-3 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200"
                        placeholder="Re-enter your password"
                        autocomplete="new-password"
                        required
                    >
                    <button @click="showConfirmPassword = !showConfirmPassword" type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-purple-600 transition-colors">
                        <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-lg"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="w-full flex items-center justify-center space-x-2 py-4 px-6 border-0 text-base font-extrabold rounded-xl text-white shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105 hover:-translate-y-1 mt-6" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                <i class="fas fa-user-plus text-lg"></i>
                <span>Create Account</span>
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

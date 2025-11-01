@extends('admin.layouts.auth')

@section('title', 'Forgot Password - Eye Pro')

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
            <p class="text-gray-600 font-medium text-base mb-1">Forgot Password?</p>
            <p class="text-sm text-gray-400">Enter your email address and we'll send you a reset link</p>
        </div>
        
        @if(session('status'))
        <div class="mb-6 rounded-xl bg-green-50 border-l-4 border-green-500 p-4">
            <div class="flex items-center space-x-2">
                <i class="fas fa-check-circle text-green-600"></i>
                <p class="text-sm text-green-700 font-medium">{{ session('status') }}</p>
            </div>
        </div>
        @endif
        
        <!-- Form -->
        <form action="{{ route('password.email') }}" method="POST" autocomplete="off" class="space-y-5">
            @csrf
            
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
                        value="{{ old('email') }}"
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
            
            <button type="submit" class="w-full flex items-center justify-center space-x-2 py-4 px-6 border-0 text-base font-extrabold rounded-xl text-white shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105 hover:-translate-y-1 mt-6" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                <i class="fas fa-paper-plane text-lg"></i>
                <span>Send Reset Link</span>
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

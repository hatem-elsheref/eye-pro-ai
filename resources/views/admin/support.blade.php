@extends('admin.layouts.master')

@section('title', 'Support - Eye Pro')
@section('page-title', 'Support Center')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    
    <!-- Support Header -->
    <div class="relative overflow-hidden rounded-2xl p-6 shadow-lg border border-blue-200" style="background: linear-gradient(135deg, #60a5fa 0%, #2563eb 100%);">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-white opacity-10"></div>
        <div class="absolute bottom-0 left-0 -mb-6 -ml-6 h-32 w-32 rounded-full bg-white opacity-10"></div>
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white mb-1">Support Center</h1>
            <p class="text-sm text-blue-50 font-medium">Get help with your account or report an issue</p>
        </div>
    </div>
    
    <!-- Contact Support -->
    <div class="bg-white rounded-3xl shadow-xl p-8 border-2 border-gray-100">
        <div class="flex items-center space-x-3 mb-6">
            <div class="h-12 w-12 rounded-xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #60a5fa 0%, #1d4ed8 100%);">
                <i class="fas fa-envelope text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">Contact Support</h2>
                <p class="text-sm text-gray-500">We typically respond within 24 hours</p>
            </div>
        </div>
        
        <form action="{{ route('support.submit') }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label for="subject" class="block text-sm font-bold text-gray-700 mb-2">Subject</label>
                <input 
                    type="text" 
                    id="subject" 
                    name="subject" 
                    class="block w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200"
                    placeholder="Brief description of your issue"
                    value="{{ old('subject') }}"
                    required
                >
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="category" class="block text-sm font-bold text-gray-700 mb-2">Category</label>
                    <div class="relative">
                        <select id="category" name="category" class="block w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 appearance-none bg-white" required>
                            <option value="">Select a category</option>
                            <option value="technical">🔧 Technical Issue</option>
                            <option value="account">👤 Account Related</option>
                            <option value="billing">💳 Billing Question</option>
                            <option value="feature">✨ Feature Request</option>
                            <option value="other">📋 Other</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label for="priority" class="block text-sm font-bold text-gray-700 mb-2">Priority</label>
                    <div class="relative">
                        <select id="priority" name="priority" class="block w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 appearance-none bg-white" required>
                            <option value="low">🟢 Low</option>
                            <option value="medium" selected>🟡 Medium</option>
                            <option value="high">🟠 High</option>
                            <option value="urgent">🔴 Urgent</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <label for="message" class="block text-sm font-bold text-gray-700 mb-2">Message</label>
                <textarea 
                    id="message" 
                    name="message" 
                    rows="6"
                    class="block w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 resize-none"
                    placeholder="Please describe your issue in detail..."
                    required
                >{{ old('message') }}</textarea>
            </div>
            
            <button type="submit" class="inline-flex items-center space-x-2 px-8 py-4 border-0 text-base font-extrabold rounded-xl text-white bg-blue-600 hover:bg-blue-700 shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105 hover:-translate-y-1">
                <i class="fas fa-paper-plane text-lg"></i>
                <span>Submit Support Ticket</span>
            </button>
        </form>
    </div>
    
    <!-- FAQ Section -->
    <div class="bg-white rounded-3xl shadow-xl p-8 border-2 border-gray-100">
        <div class="flex items-center space-x-3 mb-6">
            <div class="h-12 w-12 rounded-xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <i class="fas fa-question-circle text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">Frequently Asked Questions</h2>
                <p class="text-sm text-gray-500">Quick answers to common questions</p>
            </div>
        </div>
        
        <div class="space-y-3" x-data="{ openFaq: null }">
            <div class="border-2 border-gray-100 rounded-2xl overflow-hidden hover:border-blue-200 transition-colors">
                <button @click="openFaq = openFaq === 1 ? null : 1" type="button" class="w-full flex items-center justify-between p-5 text-left hover:bg-blue-50 transition-colors">
                    <span class="font-bold text-gray-900 text-base">How do I upload a match?</span>
                    <i :class="openFaq === 1 ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-blue-600 transition-transform"></i>
                </button>
                <div x-show="openFaq === 1" x-transition class="px-5 pb-5 text-sm text-gray-600 leading-relaxed">
                    To upload a match, navigate to the "Matches" page and click the "Upload Match" button. 
                    You can either upload a video file directly or provide a URL to a video hosted on YouTube, Vimeo, or other supported platforms.
                </div>
            </div>
            
            <div class="border-2 border-gray-100 rounded-2xl overflow-hidden hover:border-blue-200 transition-colors">
                <button @click="openFaq = openFaq === 2 ? null : 2" type="button" class="w-full flex items-center justify-between p-5 text-left hover:bg-blue-50 transition-colors">
                    <span class="font-bold text-gray-900 text-base">What video formats are supported?</span>
                    <i :class="openFaq === 2 ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-blue-600 transition-transform"></i>
                </button>
                <div x-show="openFaq === 2" x-transition class="px-5 pb-5 text-sm text-gray-600 leading-relaxed">
                    We support most common video formats including MP4, AVI, MOV, and MKV. 
                    Files larger than 1GB are uploaded in chunks for reliability.
                </div>
            </div>
            
            <div class="border-2 border-gray-100 rounded-2xl overflow-hidden hover:border-blue-200 transition-colors">
                <button @click="openFaq = openFaq === 3 ? null : 3" type="button" class="w-full flex items-center justify-between p-5 text-left hover:bg-blue-50 transition-colors">
                    <span class="font-bold text-gray-900 text-base">Why is my account pending approval?</span>
                    <i :class="openFaq === 3 ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-blue-600 transition-transform"></i>
                </button>
                <div x-show="openFaq === 3" x-transition class="px-5 pb-5 text-sm text-gray-600 leading-relaxed">
                    New accounts require approval from an administrator to prevent abuse. 
                    This process typically takes 24-48 hours. You'll receive an email notification once approved.
                </div>
            </div>
            
            <div class="border-2 border-gray-100 rounded-2xl overflow-hidden hover:border-blue-200 transition-colors">
                <button @click="openFaq = openFaq === 4 ? null : 4" type="button" class="w-full flex items-center justify-between p-5 text-left hover:bg-blue-50 transition-colors">
                    <span class="font-bold text-gray-900 text-base">Can I delete a match after uploading?</span>
                    <i :class="openFaq === 4 ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-blue-600 transition-transform"></i>
                </button>
                <div x-show="openFaq === 4" x-transition class="px-5 pb-5 text-sm text-gray-600 leading-relaxed">
                    Yes, you can delete any match you've uploaded by going to the match details page and clicking the delete button.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

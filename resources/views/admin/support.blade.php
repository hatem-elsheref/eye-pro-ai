@extends('admin.layouts.master')

@section('title', __('admin.support_center') . ' - Eye Pro')
@section('page-title', __('admin.support_center'))

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    
    <!-- Support Header -->
    <div class="relative overflow-hidden rounded-2xl p-6 shadow-lg border border-blue-200" style="background: linear-gradient(135deg, #60a5fa 0%, #2563eb 100%);">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-white opacity-10"></div>
        <div class="absolute bottom-0 left-0 -mb-6 -ml-6 h-32 w-32 rounded-full bg-white opacity-10"></div>
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white mb-1">{{ __('admin.support_center') }}</h1>
            <p class="text-sm text-blue-50 font-medium">{{ __('admin.support_center_description') }}</p>
        </div>
    </div>
    
    <!-- Contact Support -->
    <div class="bg-white rounded-3xl shadow-xl p-8 border-2 border-gray-100">
        <div class="flex items-center gap-4 mb-6 support-section-header">
            <div class="h-12 w-12 rounded-xl flex items-center justify-center shadow-lg support-section-icon" style="background: linear-gradient(135deg, #60a5fa 0%, #1d4ed8 100%);">
                <i class="fas fa-envelope text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">{{ __('admin.contact_support') }}</h2>
                <p class="text-sm text-gray-500">{{ __('admin.support_response_time') }}</p>
            </div>
        </div>
        
        <form action="{{ route('support.submit') }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label for="subject" class="block text-sm font-bold text-gray-700 mb-2">{{ __('admin.subject') }}</label>
                <input 
                    type="text" 
                    id="subject" 
                    name="subject" 
                    class="block w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200"
                    placeholder="{{ __('admin.subject_placeholder') }}"
                    value="{{ old('subject') }}"
                    required
                >
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="category" class="block text-sm font-bold text-gray-700 mb-2">{{ __('admin.category') }}</label>
                    <div class="relative">
                        <select id="category" name="category" class="block w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 appearance-none bg-white" required>
                            <option value="">{{ __('admin.select_category') }}</option>
                            <option value="technical">🔧 {{ __('admin.technical_issue') }}</option>
                            <option value="account">👤 {{ __('admin.account_related') }}</option>
                            <option value="billing">💳 {{ __('admin.billing_question') }}</option>
                            <option value="feature">✨ {{ __('admin.feature_request') }}</option>
                            <option value="other">📋 {{ __('admin.other') }}</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none select-arrow-container">
                            <i class="fas fa-chevron-down text-gray-400 select-arrow"></i>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label for="priority" class="block text-sm font-bold text-gray-700 mb-2">{{ __('admin.priority') }}</label>
                    <div class="relative">
                        <select id="priority" name="priority" class="block w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 appearance-none bg-white" required>
                            <option value="low">🟢 {{ __('admin.priority_low') }}</option>
                            <option value="medium" selected>🟡 {{ __('admin.priority_medium') }}</option>
                            <option value="high">🟠 {{ __('admin.priority_high') }}</option>
                            <option value="urgent">🔴 {{ __('admin.priority_urgent') }}</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none select-arrow-container">
                            <i class="fas fa-chevron-down text-gray-400 select-arrow"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <label for="message" class="block text-sm font-bold text-gray-700 mb-2">{{ __('admin.message') }}</label>
                <textarea 
                    id="message" 
                    name="message" 
                    rows="6"
                    class="block w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 resize-none"
                    placeholder="{{ __('admin.message_placeholder') }}"
                    required
                >{{ old('message') }}</textarea>
            </div>
            
            <button type="submit" class="inline-flex items-center gap-2 px-8 py-4 border-0 text-base font-extrabold rounded-xl text-white bg-blue-600 hover:bg-blue-700 shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105 hover:-translate-y-1 submit-ticket-button">
                <i class="fas fa-paper-plane text-lg submit-icon"></i>
                <span>{{ __('admin.submit_support_ticket') }}</span>
            </button>
        </form>
    </div>
    
    <!-- My Tickets Section -->
    @if($tickets && $tickets->count() > 0)
    <div class="bg-white rounded-3xl shadow-xl p-8 border-2 border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4 support-section-header">
                <div class="h-12 w-12 rounded-xl flex items-center justify-center shadow-lg support-section-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="fas fa-ticket-alt text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-gray-900">{{ __('admin.my_tickets') }}</h2>
                    <p class="text-sm text-gray-500">{{ __('admin.view_submitted_tickets') }}</p>
                </div>
            </div>
            <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                <i class="fas fa-list"></i>
                <span>{{ __('admin.view_all') }}</span>
            </a>
        </div>
        
        <div class="space-y-4">
            @foreach($tickets->take(5) as $ticket)
            <div class="border-2 border-gray-100 rounded-xl p-5 hover:border-blue-200 transition-colors">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="font-bold text-gray-900">#{{ $ticket->id }} - {{ $ticket->subject }}</h3>
                            @if($ticket->status === 'resolved')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">{{ __('admin.resolved') }}</span>
                            @elseif($ticket->status === 'in_progress')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">{{ __('admin.in_progress') }}</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">{{ __('admin.open') }}</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mb-2">{{ Str::limit($ticket->message, 100) }}</p>
                        <div class="flex items-center gap-4 text-xs text-gray-500">
                            <span class="inline-flex items-center gap-1.5"><i class="fas fa-tag ticket-info-icon"></i>{{ ucfirst($ticket->category) }}</span>
                            <span class="inline-flex items-center gap-1.5"><i class="fas fa-flag ticket-info-icon"></i>{{ ucfirst($ticket->priority) }}</span>
                            <span class="inline-flex items-center gap-1.5"><i class="fas fa-clock ticket-info-icon"></i>{{ $ticket->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        @if($tickets->count() > 5)
        <div class="mt-4 text-center">
            <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-blue-600 hover:text-blue-700">
                <span>{{ __('admin.view_all_tickets_count', ['count' => $tickets->count()]) }}</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        @endif
    </div>
    @endif
    
    <!-- FAQ Section -->
    <div class="bg-white rounded-3xl shadow-xl p-8 border-2 border-gray-100">
        <div class="flex items-center gap-4 mb-6 support-section-header">
            <div class="h-12 w-12 rounded-xl flex items-center justify-center shadow-lg support-section-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <i class="fas fa-question-circle text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">{{ __('admin.faq') }}</h2>
                <p class="text-sm text-gray-500">{{ __('admin.faq_description') }}</p>
            </div>
        </div>
        
        <div class="space-y-3" x-data="{ openFaq: null }">
            <div class="border-2 border-gray-100 rounded-2xl overflow-hidden hover:border-blue-200 transition-colors">
                <button @click="openFaq = openFaq === 1 ? null : 1" type="button" class="w-full flex items-center justify-between p-5 text-left hover:bg-blue-50 transition-colors">
                    <span class="font-bold text-gray-900 text-base">{{ __('admin.faq_upload_match') }}</span>
                    <i :class="openFaq === 1 ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-blue-600 transition-transform"></i>
                </button>
                <div x-show="openFaq === 1" x-transition class="px-5 pb-5 text-sm text-gray-600 leading-relaxed">
                    {{ __('admin.faq_upload_match_answer') }}
                </div>
            </div>
            
            <div class="border-2 border-gray-100 rounded-2xl overflow-hidden hover:border-blue-200 transition-colors">
                <button @click="openFaq = openFaq === 2 ? null : 2" type="button" class="w-full flex items-center justify-between p-5 text-left hover:bg-blue-50 transition-colors">
                    <span class="font-bold text-gray-900 text-base">{{ __('admin.faq_video_formats') }}</span>
                    <i :class="openFaq === 2 ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-blue-600 transition-transform"></i>
                </button>
                <div x-show="openFaq === 2" x-transition class="px-5 pb-5 text-sm text-gray-600 leading-relaxed">
                    {{ __('admin.faq_video_formats_answer') }}
                </div>
            </div>
            
            <div class="border-2 border-gray-100 rounded-2xl overflow-hidden hover:border-blue-200 transition-colors">
                <button @click="openFaq = openFaq === 3 ? null : 3" type="button" class="w-full flex items-center justify-between p-5 text-left hover:bg-blue-50 transition-colors">
                    <span class="font-bold text-gray-900 text-base">{{ __('admin.faq_account_pending') }}</span>
                    <i :class="openFaq === 3 ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-blue-600 transition-transform"></i>
                </button>
                <div x-show="openFaq === 3" x-transition class="px-5 pb-5 text-sm text-gray-600 leading-relaxed">
                    {{ __('admin.faq_account_pending_answer') }}
                </div>
            </div>
            
            <div class="border-2 border-gray-100 rounded-2xl overflow-hidden hover:border-blue-200 transition-colors">
                <button @click="openFaq = openFaq === 4 ? null : 4" type="button" class="w-full flex items-center justify-between p-5 text-left hover:bg-blue-50 transition-colors">
                    <span class="font-bold text-gray-900 text-base">{{ __('admin.faq_delete_match') }}</span>
                    <i :class="openFaq === 4 ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-blue-600 transition-transform"></i>
                </button>
                <div x-show="openFaq === 4" x-transition class="px-5 pb-5 text-sm text-gray-600 leading-relaxed">
                    {{ __('admin.faq_delete_match_answer') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

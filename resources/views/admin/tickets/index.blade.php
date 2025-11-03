@extends('admin.layouts.master')

@section('title', __('admin.support_tickets') . ' - Eye Pro')
@section('page-title', __('admin.support_tickets'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('admin.support_tickets') }}</h1>
            <p class="text-sm text-gray-500">{{ __('admin.view_manage_all_tickets') }}</p>
        </div>
        <a href="{{ route('support') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-white rounded-lg font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5 new-ticket-button" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
            <i class="fas fa-plus text-sm new-ticket-icon"></i>
            <span>{{ __('admin.new_ticket') }}</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-200">
        <form method="GET" action="{{ route('tickets.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">{{ __('admin.status') }}</label>
                <select name="status" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="">{{ __('admin.all_statuses') }}</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>{{ __('admin.open') }}</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>{{ __('admin.in_progress') }}</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>{{ __('admin.resolved') }}</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>{{ __('admin.closed') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">{{ __('admin.category') }}</label>
                <select name="category" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="">{{ __('admin.all_categories') }}</option>
                    <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>{{ __('admin.technical') }}</option>
                    <option value="account" {{ request('category') === 'account' ? 'selected' : '' }}>{{ __('admin.account') }}</option>
                    <option value="billing" {{ request('category') === 'billing' ? 'selected' : '' }}>{{ __('admin.billing') }}</option>
                    <option value="feature" {{ request('category') === 'feature' ? 'selected' : '' }}>{{ __('admin.feature') }}</option>
                    <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>{{ __('admin.other') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">{{ __('admin.priority') }}</label>
                <select name="priority" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="">{{ __('admin.all_priorities') }}</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>{{ __('admin.low') }}</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>{{ __('admin.medium') }}</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>{{ __('admin.high') }}</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>{{ __('admin.urgent') }}</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm transition-colors shadow-md hover:shadow-lg filter-button">
                    <i class="fas fa-filter filter-icon"></i><span>{{ __('admin.filter') }}</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Tickets Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        @if($tickets->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b-2 border-gray-300">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('admin.id') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('admin.subject') }}</th>
                            @if(auth()->user()->is_admin ?? false)
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('admin.user') }}</th>
                            @endif
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('admin.category') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('admin.priority') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('admin.status') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('admin.created') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($tickets as $ticket)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $ticket->id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-semibold">{{ Str::limit($ticket->subject, 50) }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ Str::limit($ticket->message, 60) }}</div>
                            </td>
                            @if(auth()->user()->is_admin ?? false)
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $ticket->user->name ?? 'N/A' }}
                            </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                                    {{ __("admin.{$ticket->category}") }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($ticket->priority === 'urgent')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">{{ __('admin.urgent') }}</span>
                                @elseif($ticket->priority === 'high')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">{{ __('admin.high') }}</span>
                                @elseif($ticket->priority === 'medium')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">{{ __('admin.medium') }}</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">{{ __('admin.low') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($ticket->status === 'resolved')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">{{ __('admin.resolved') }}</span>
                                @elseif($ticket->status === 'in_progress')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">{{ __('admin.in_progress') }}</span>
                                @elseif($ticket->status === 'closed')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">{{ __('admin.closed') }}</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">{{ __('admin.open') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $ticket->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center gap-2 ticket-actions">
                                    <button 
                                        onclick="openTicketModal({{ $ticket->id }}, '{{ addslashes($ticket->subject) }}', '{{ addslashes($ticket->message) }}', '{{ $ticket->status }}', '{{ addslashes($ticket->admin_response ?? '') }}')"
                                        class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg text-white transition-all shadow-sm hover:shadow-md ticket-view-btn" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                                        <i class="fas fa-eye ticket-view-icon"></i><span>{{ __('admin.view') }}</span>
                                    </button>
                                    @if(($ticket->status !== 'resolved' && $ticket->status !== 'closed') && (auth()->user()->is_admin ?? false))
                                    <form action="{{ route('tickets.resolve', $ticket->id) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('admin.confirm_resolve_ticket') }}');">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg text-white transition-all shadow-sm hover:shadow-md ticket-resolve-btn" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                            <i class="fas fa-check ticket-resolve-icon"></i><span>{{ __('admin.resolve') }}</span>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $tickets->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                    <i class="fas fa-ticket-alt text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('admin.no_tickets_found') }}</h3>
                <p class="text-sm text-gray-500 mb-6">{{ __('admin.no_tickets_match_filters') }}</p>
                <a href="{{ route('support') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-white rounded-lg font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200 create-ticket-empty-button" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                    <i class="fas fa-plus text-sm create-ticket-empty-icon"></i>
                    <span>{{ __('admin.create_new_ticket') }}</span>
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Ticket Modal -->
<div id="ticketModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900">{{ __('admin.ticket_details') }}</h2>
                <button onclick="closeTicketModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('admin.subject') }}</label>
                <p id="modalSubject" class="text-gray-900 font-medium"></p>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('admin.message') }}</label>
                <div id="modalMessage" class="bg-gray-50 rounded-lg p-4 text-gray-700 whitespace-pre-wrap"></div>
            </div>

            <div id="modalAdminResponse" class="hidden">
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('admin.admin_response') }}</label>
                <div class="bg-blue-50 rounded-lg p-4 text-gray-700 whitespace-pre-wrap"></div>
            </div>
        </div>

        <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
            <button onclick="closeTicketModal()" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold transition-colors">
                {{ __('admin.close') }}
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Ticket page - spacing between icons and text */
    .new-ticket-button,
    .create-ticket-empty-button {
        gap: 0.5rem !important;
    }
    
    .filter-button {
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
    }
    
    .ticket-actions {
        gap: 0.5rem !important;
    }
    
    .ticket-view-btn,
    .ticket-resolve-btn {
        gap: 0.375rem !important;
    }
    
    /* RTL spacing fixes */
    [dir="rtl"] .new-ticket-button,
    [dir="rtl"] .create-ticket-empty-button {
        flex-direction: row-reverse !important;
        gap: 0.5rem !important;
    }
    
    [dir="rtl"] .filter-button {
        flex-direction: row-reverse !important;
        gap: 0.5rem !important;
    }
    
    [dir="rtl"] .ticket-view-btn,
    [dir="rtl"] .ticket-resolve-btn {
        flex-direction: row-reverse !important;
        gap: 0.375rem !important;
    }
    
    .new-ticket-icon,
    .create-ticket-empty-icon,
    .filter-icon,
    .ticket-view-icon,
    .ticket-resolve-icon {
        margin-right: 0 !important;
        margin-left: 0 !important;
    }
</style>
@endpush

@push('scripts')
<script>
function openTicketModal(id, subject, message, status, adminResponse) {
    document.getElementById('modalSubject').textContent = subject;
    document.getElementById('modalMessage').textContent = message;
    
    const responseDiv = document.getElementById('modalAdminResponse');
    if (adminResponse && adminResponse.trim() !== '') {
        responseDiv.classList.remove('hidden');
        responseDiv.querySelector('div').textContent = adminResponse;
    } else {
        responseDiv.classList.add('hidden');
    }
    
    document.getElementById('ticketModal').classList.remove('hidden');
}

function closeTicketModal() {
    document.getElementById('ticketModal').classList.add('hidden');
}

// Close modal on outside click
document.getElementById('ticketModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeTicketModal();
    }
});
</script>
@endpush
@endsection


@extends('admin.layouts.master')

@section('title', 'Tickets - Eye Pro')
@section('page-title', 'Support Tickets')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Support Tickets</h1>
            <p class="text-sm text-gray-500">View and manage all support tickets</p>
        </div>
        <a href="{{ route('support') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-white rounded-lg font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
            <i class="fas fa-plus text-sm"></i>
            <span>New Ticket</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-200">
        <form method="GET" action="{{ route('tickets.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="">All Statuses</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Category</label>
                <select name="category" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="">All Categories</option>
                    <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>Technical</option>
                    <option value="account" {{ request('category') === 'account' ? 'selected' : '' }}>Account</option>
                    <option value="billing" {{ request('category') === 'billing' ? 'selected' : '' }}>Billing</option>
                    <option value="feature" {{ request('category') === 'feature' ? 'selected' : '' }}>Feature</option>
                    <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Priority</label>
                <select name="priority" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="">All Priorities</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Tickets Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        @if($tickets->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-blue-50 to-indigo-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Subject</th>
                            @if(auth()->user()->is_admin ?? false)
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">User</th>
                            @endif
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
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
                                    {{ ucfirst($ticket->category) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($ticket->priority === 'urgent')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Urgent</span>
                                @elseif($ticket->priority === 'high')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">High</span>
                                @elseif($ticket->priority === 'medium')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Medium</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Low</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($ticket->status === 'resolved')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Resolved</span>
                                @elseif($ticket->status === 'in_progress')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">In Progress</span>
                                @elseif($ticket->status === 'closed')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Closed</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Open</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $ticket->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center gap-2">
                                    <button 
                                        onclick="openTicketModal({{ $ticket->id }}, '{{ addslashes($ticket->subject) }}', '{{ addslashes($ticket->message) }}', '{{ $ticket->status }}', '{{ addslashes($ticket->admin_response ?? '') }}')"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg text-blue-600 hover:text-blue-700 hover:bg-blue-50 transition-colors">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </button>
                                    @if(($ticket->status !== 'resolved' && $ticket->status !== 'closed') && (auth()->user()->is_admin ?? false))
                                    <form action="{{ route('tickets.resolve', $ticket->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to mark this ticket as resolved?');">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg text-green-600 hover:text-green-700 hover:bg-green-50 transition-colors">
                                            <i class="fas fa-check mr-1"></i>Resolve
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
                <h3 class="text-lg font-bold text-gray-900 mb-2">No Tickets Found</h3>
                <p class="text-sm text-gray-500 mb-6">No tickets match your filters. Try adjusting your search criteria.</p>
                <a href="{{ route('support') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-white rounded-lg font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200" style="background: linear-gradient(135deg, #60a5fa 0%, #818cf8 100%);">
                    <i class="fas fa-plus text-sm"></i>
                    <span>Create New Ticket</span>
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
                <h2 class="text-2xl font-bold text-gray-900">Ticket Details</h2>
                <button onclick="closeTicketModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Subject</label>
                <p id="modalSubject" class="text-gray-900 font-medium"></p>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Message</label>
                <div id="modalMessage" class="bg-gray-50 rounded-lg p-4 text-gray-700 whitespace-pre-wrap"></div>
            </div>

            <div id="modalAdminResponse" class="hidden">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Admin Response</label>
                <div class="bg-blue-50 rounded-lg p-4 text-gray-700 whitespace-pre-wrap"></div>
            </div>
        </div>

        <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
            <button onclick="closeTicketModal()" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

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


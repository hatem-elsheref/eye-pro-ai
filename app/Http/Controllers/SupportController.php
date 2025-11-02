<?php

namespace App\Http\Controllers;

use App\Services\SupportService;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    protected $supportService;

    public function __construct(SupportService $supportService)
    {
        $this->supportService = $supportService;
    }

    public function index()
    {
        $tickets = $this->supportService->getUserTickets(auth()->user());
        return view('admin.support', compact('tickets'));
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|string|in:technical,account,billing,feature,other',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'message' => 'required|string|max:2000',
        ]);

        $this->supportService->submitTicket(auth()->user(), $validated);

        return back()->with('success', 'Your support ticket has been submitted successfully! We will get back to you soon.');
    }

    public function tickets()
    {
        $filters = [
            'status' => request('status'),
            'category' => request('category'),
            'priority' => request('priority'),
        ];

        // Admins see all tickets, regular users see only their own
        if (auth()->user()->is_admin ?? false) {
            $tickets = $this->supportService->getAllTickets($filters);
        } else {
            // For regular users, apply filters and paginate
            $query = \App\Models\Ticket::where('user_id', auth()->id());
            
            if (isset($filters['status']) && $filters['status']) {
                $query->where('status', $filters['status']);
            }
            
            if (isset($filters['category']) && $filters['category']) {
                $query->where('category', $filters['category']);
            }
            
            if (isset($filters['priority']) && $filters['priority']) {
                $query->where('priority', $filters['priority']);
            }
            
            $tickets = $query->latest()->paginate(15);
        }
        
        return view('admin.tickets.index', compact('tickets', 'filters'));
    }

    public function resolve(Request $request, $id)
    {
        // Only admins can resolve tickets
        if (!(auth()->user()->is_admin ?? false)) {
            abort(403, 'Only administrators can resolve tickets.');
        }

        $ticket = Ticket::findOrFail($id);

        $validated = $request->validate([
            'admin_response' => 'nullable|string|max:2000',
        ]);

        $success = $this->supportService->resolveTicket(
            $ticket,
            auth()->user(),
            $validated['admin_response'] ?? null
        );

        if ($success) {
            return back()->with('success', 'Ticket marked as resolved successfully.');
        }

        return back()->with('error', 'Failed to resolve ticket. Please try again.');
    }
}

<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SupportService
{
    /**
     * Submit support ticket
     */
    public function submitTicket(User $user, array $data): Ticket
    {
        return Ticket::create([
            'user_id' => $user->id,
            'subject' => $data['subject'],
            'category' => $data['category'],
            'priority' => $data['priority'] ?? 'medium',
            'message' => $data['message'],
            'status' => 'open',
        ]);
    }

    /**
     * Get all tickets for a user
     */
    public function getUserTickets(User $user)
    {
        return Ticket::where('user_id', $user->id)
            ->with('resolver')
            ->latest()
            ->get();
    }

    /**
     * Get all tickets (admin)
     */
    public function getAllTickets($filters = [])
    {
        $query = Ticket::with(['user', 'resolver']);

        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['category']) && $filters['category']) {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['priority']) && $filters['priority']) {
            $query->where('priority', $filters['priority']);
        }

        return $query->latest()->paginate(15);
    }

    /**
     * Resolve a ticket
     */
    public function resolveTicket(Ticket $ticket, User $resolver, ?string $response = null): bool
    {
        try {
            $ticket->update([
                'status' => 'resolved',
                'resolved_by' => $resolver->id,
                'resolved_at' => now(),
                'admin_response' => $response,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to resolve ticket', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}



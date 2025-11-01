<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupportController extends Controller
{
    /**
     * Display the support page
     */
    public function index()
    {
        return view('admin.support');
    }

    /**
     * Submit a support ticket
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|string|in:technical,account,billing,feature,other',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'message' => 'required|string|max:2000',
        ]);

        // Here you can:
        // 1. Store in database (create a SupportTicket model and migration)
        // 2. Send email to support team
        // 3. Create a notification for admins
        
        // For now, we'll just return a success message
        // You can implement actual storage/email later
        
        // Example: Send email to support
        // Mail::to('support@example.com')->send(new SupportTicketMail($validated));
        
        return back()->with('success', 'Your support ticket has been submitted successfully! We will get back to you soon.');
    }
}





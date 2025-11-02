<?php

namespace App\Http\Controllers;

use App\Services\SupportService;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    protected $supportService;

    public function __construct(SupportService $supportService)
    {
        $this->supportService = $supportService;
    }

    public function index()
    {
        return view('admin.support');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|string|in:technical,account,billing,feature,other',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'message' => 'required|string|max:2000',
        ]);

        $this->supportService->submitTicket($validated);

        return back()->with('success', 'Your support ticket has been submitted successfully! We will get back to you soon.');
    }
}

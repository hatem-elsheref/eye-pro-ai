<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        return view('admin.profile');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
        ]);

        $this->userService->updateProfile(auth()->user(), $validated);
        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!$this->userService->updatePassword(
            auth()->user(),
            $validated['current_password'],
            $validated['new_password']
        )) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        return back()->with('success', 'Password updated successfully!');
    }

    public function destroy(Request $request)
    {
        $this->userService->deleteAccount(auth()->user());

        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Your account has been deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's home page.
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Display the user's profile page.
     */
    public function show()
    {
        return view('profile.show');
    }

    /**
     * Display the user's settings page.
     */
    public function settings()
    {
        return view('settings.show');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('settings.show')->with('status', 'password-updated');
    }
}

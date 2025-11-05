<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the user's profile page
     */
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    /**
     * Update the user's profile information
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        // Update user information
        $user->email = $validated['email'];
        $user->save();

        // Update name translation
        $user->translations()->updateOrCreate(
            ['lang_key' => 'name', 'lang' => app()->getLocale()],
            ['lang_value' => $validated['name']]
        );

        return redirect()->route('profile.index')->with('success', __('Profile updated successfully'));
    }

    /**
     * Update the user's password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('profile.index')->with('success', __('Password updated successfully'));
    }
}

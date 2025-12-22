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
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
                'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
                'translations' => ['required', 'array', 'min:1'],
                'translations.*.name' => ['required', 'string', 'max:255'],
            ]);

            // Update user information
            $user->email = $validated['email'];
            
            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($user->image) {
                    \Storage::disk('public')->delete($user->image);
                }
                $user->image = $request->file('image')->store('admins', 'public');
            }

            $user->save();

            // Update name translations
            foreach ($validated['translations'] as $langId => $data) {
                $user->translations()->updateOrCreate(
                    ['lang_key' => 'name', 'lang_id' => $langId],
                    ['lang_value' => $data['name']]
                );
            }

            return redirect()->route('admin.profile.index')->with('success', __('admin.profile_updated_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('admin.error_occurred') . ': ' . $e->getMessage())->withInput();
        }
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

        return redirect()->route('admin.profile.index')->with('success', __('admin.password_updated_successfully'));
    }
}

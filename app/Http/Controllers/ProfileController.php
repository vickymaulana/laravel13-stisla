<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Authenticated user profile management.
 */
class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit(): View
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->save();

        ActivityLog::log('Profile updated', 'User Profile', 'updated', $user);

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    /**
     * Show the change password form.
     */
    public function changePassword(): View
    {
        return view('profile.changepassword', ['user' => Auth::user()]);
    }

    /**
     * Update the authenticated user's password.
     */
    public function password(ChangePasswordRequest $request): RedirectResponse
    {
        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->fill([
            'password' => Hash::make($request->new_password),
        ])->save();

        ActivityLog::log('Password changed', 'User Profile', 'updated', $user);

        return back()->with('success', 'Password changed successfully.');
    }
}

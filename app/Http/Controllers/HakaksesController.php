<?php

namespace App\Http\Controllers;

use App\Http\Requests\Roles\UpdateUserRoleRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Role access management (superadmin only).
 *
 * Allows the superadmin to view, edit, and delete user roles.
 */
class HakaksesController extends Controller
{
    /**
     * Display a listing of users with their roles.
     */
    public function index(Request $request): View
    {
        $query = User::with('roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $hakakses = $query->orderBy('name')->get();

        return view('layouts.hakakses.index', compact('hakakses'));
    }

    /**
     * Show the form for editing the specified user's role.
     */
    public function edit(int $id): View
    {
        $hakakses = User::findOrFail($id);

        return view('layouts.hakakses.edit', compact('hakakses'));
    }

    /**
     * Update the specified user's role.
     */
    public function update(UpdateUserRoleRequest $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->syncRoles([$request->role]);

        ActivityLog::log(
            "Role updated for {$user->name} to {$request->role}",
            'Role Access',
            'updated',
            $user
        );

        return redirect()->route('hakakses.index')
            ->with('success', 'User role updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('hakakses.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $userName = $user->name;
        $user->delete();

        ActivityLog::log(
            "User deleted: {$userName}",
            'Role Access',
            'deleted'
        );

        return redirect()->route('hakakses.index')
            ->with('success', 'User deleted successfully.');
    }
}

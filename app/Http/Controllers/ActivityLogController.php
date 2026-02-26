<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Activity log viewer (superadmin only).
 *
 * Provides a filterable log of all recorded user and system events.
 */
class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs.
     */
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by event
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(20);
        $users = \App\Models\User::select('id', 'name')->get();

        return view('activity-logs.index', compact('logs', 'users'));
    }

    /**
     * Display the specified activity log.
     */
    public function show(int $id): View
    {
        $log = ActivityLog::with('user')->findOrFail($id);
        return view('activity-logs.show', compact('log'));
    }

    /**
     * Remove the specified activity log from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $log = ActivityLog::findOrFail($id);
        $log->delete();

        return redirect()->route('activity-logs.index')
            ->with('success', 'Activity log deleted successfully.');
    }

    /**
     * Remove all activity logs, optionally filtered by retention period.
     */
    public function clear(Request $request): RedirectResponse
    {
        $request->validate([
            'confirm' => 'required|string|in:CLEAR',
            'retention_days' => 'nullable|integer|min:0|max:3650',
        ]);

        $retentionDays = (int) $request->input('retention_days', 0);

        if ($retentionDays > 0) {
            ActivityLog::where('created_at', '<=', now()->subDays($retentionDays))->delete();
            return redirect()->route('activity-logs.index')
                ->with('success', "Activity logs older than {$retentionDays} day(s) were cleared.");
        }

        ActivityLog::truncate();

        return redirect()->route('activity-logs.index')
            ->with('success', 'All activity logs cleared successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\Notifications\SendNotificationRequest;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * User notification management.
 *
 * Handles listing, reading, deleting, and (for admins) sending
 * notifications via the database channel.
 */
class NotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index(): View
    {
        $notifications = auth()->user()
            ->notifications()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read and redirect to its action URL if set.
     */
    public function markAsRead(string $id): RedirectResponse
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        if (isset($notification->data['action_url'])) {
            return redirect($notification->data['action_url']);
        }

        return redirect()->route('notifications.index');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()->route('notifications.index')
            ->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a notification.
     */
    public function destroy(string $id): RedirectResponse
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($id);

        $notification->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'Notification deleted successfully.');
    }

    /**
     * Delete all notifications.
     */
    public function destroyAll(): RedirectResponse
    {
        auth()->user()->notifications()->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'All notifications deleted successfully.');
    }

    /**
     * Get unread notifications count (AJAX endpoint).
     */
    public function unreadCount(): JsonResponse
    {
        return response()->json([
            'count' => auth()->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Get recent notifications for the dropdown (AJAX endpoint).
     */
    public function recent(): JsonResponse
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->take(5)
            ->get();

        return response()->json($notifications);
    }

    /**
     * Send a test notification to the current user.
     */
    public function sendTest(): RedirectResponse
    {
        auth()->user()->notify(new GeneralNotification(
            'Test Notification',
            'This is a test notification to verify the notification system is working correctly.',
            route('notifications.index'),
            'View Notifications',
            'info'
        ));

        return redirect()->route('notifications.index')
            ->with('success', 'Test notification sent successfully.');
    }

    /**
     * Show create notification form (admin only).
     */
    public function create(): View
    {
        $users = User::select('id', 'name', 'email')->get();

        return view('notifications.create', compact('users'));
    }

    /**
     * Send notification to selected users (admin only).
     */
    public function send(SendNotificationRequest $request): RedirectResponse
    {
        $users = User::whereIn('id', $request->users)->get();

        foreach ($users as $user) {
            $user->notify(new GeneralNotification(
                $request->title,
                $request->message,
                $request->action_url,
                $request->action_text ?? 'View',
                $request->type
            ));
        }

        return redirect()->route('notifications.create')
            ->with('success', 'Notification sent to '.count($users).' user(s).');
    }
}

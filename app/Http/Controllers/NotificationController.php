<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = auth()->user()
            ->notifications()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a single notification as read and redirect to its target.
     */
    public function read(string $id): RedirectResponse
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $url = $notification->data['url'] ?? null;

        return $url ? redirect($url) : redirect()->route('notifications.index');
    }

    public function readAll(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $notifications = $user->notifications()->orderByDesc('created_at')->paginate(20);

        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    public function markRead($id)
    {
        $user = auth()->user();
        $notification = $user->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }

        return back();
    }

    public function markAllRead()
    {
        $user = auth()->user();
        foreach ($user->unreadNotifications as $n) {
            $n->markAsRead();
        }

        return back();
    }
}

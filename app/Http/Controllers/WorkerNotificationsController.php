<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WorkerNotificationsController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $unread = $user->unreadNotifications()->latest()->get();
        $read   = $user->readNotifications()->latest()->take(50)->get();

        return view('worker.notifications', compact('unread', 'read'));
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('status', 'All notifications marked as read.');
    }

    public function markRead(Request $request, string $id)
    {
        $request->user()->notifications()->where('id', $id)->update(['read_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('status', 'Notification marked as read.');
    }
}

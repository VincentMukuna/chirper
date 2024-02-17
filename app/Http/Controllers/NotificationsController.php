<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Inertia\Inertia;

class NotificationsController extends Controller
{
    public function index()
    {
        return Inertia::render('Notifications/Index', [
            'notifications' => auth()->user()->notifications,
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return redirect()->back();
    }

    public function markAsRead(DatabaseNotification $notification)
    {
        $notification->markAsRead();
        return redirect()->back();
    }

    public function destroy(DatabaseNotification $notification){
        $notification->delete();
        return redirect()->back();
    }
}

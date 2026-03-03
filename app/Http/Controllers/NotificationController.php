<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{    
    public function index(Request $request)
    {  
        $user = auth()->user();
    
        return Inertia::render('Locator/Notifications/Index',[
            'unread' => $user->unreadNotifications,
            'all'    => $user->notifications,
            'count_notification' => $user->notifications->count(),
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }
    public function markAsRead(Request $request, $id)
{
    $user = $request->user();

    $notification = $user->notifications()->where('id', $id)->first();
   
    if (!$notification) {
        return response()->json(['message' => 'Notification not found'], 404);
    }

    $notification->markAsRead();
     
    if (!$notification->read_at) {
        $notification->read_at = now();
        $notification->save();
    }
      
    return response()->json(['success' => 'Notification marked as read']);
}
}

<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class TutorNotificationController extends Controller
{
    /**
     * Display notifications
     */
    public function index()
    {
        $tutor = Auth::guard('tutor')->user();
        
        $notifications = Notification::where('tutor_id', $tutor->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $unreadCount = Notification::where('tutor_id', $tutor->id)
            ->unread()
            ->count();

        return view('tutor.notifications', compact('notifications', 'tutor', 'unreadCount'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $tutor = Auth::guard('tutor')->user();
        
        $notification = Notification::where('tutor_id', $tutor->id)
            ->findOrFail($id);

        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $tutor = Auth::guard('tutor')->user();
        
        Notification::where('tutor_id', $tutor->id)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return redirect()->back()->with('success', 'All notifications marked as read');
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $tutor = Auth::guard('tutor')->user();
        
        $notification = Notification::where('tutor_id', $tutor->id)
            ->findOrFail($id);

        $notification->delete();

        return redirect()->back()->with('success', 'Notification deleted');
    }
}
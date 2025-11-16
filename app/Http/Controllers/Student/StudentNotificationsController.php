<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentNotificationsController extends Controller
{
    /**
     * Display notifications
     */
    public function index()
    {
        $student = Auth::guard('student')->user();
        
        $notifications = DB::table('student_notifications')
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $unreadCount = DB::table('student_notifications')
            ->where('student_id', $student->id)
            ->where('is_read', false)
            ->count();

        return view('student.notifications', compact('notifications', 'student', 'unreadCount'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $student = Auth::guard('student')->user();
        
        DB::table('student_notifications')
            ->where('id', $id)
            ->where('student_id', $student->id)
            ->update([
                'is_read' => true,
                'read_at' => now(),
                'updated_at' => now()
            ]);

        return redirect()->back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $student = Auth::guard('student')->user();
        
        DB::table('student_notifications')
            ->where('student_id', $student->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
                'updated_at' => now()
            ]);

        return redirect()->back()->with('success', 'All notifications marked as read');
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $student = Auth::guard('student')->user();
        
        DB::table('student_notifications')
            ->where('id', $id)
            ->where('student_id', $student->id)
            ->delete();

        return redirect()->back()->with('success', 'Notification deleted');
    }
}
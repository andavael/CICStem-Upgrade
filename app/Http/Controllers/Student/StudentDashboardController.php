<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Session;
use App\Models\Announcement;
use Carbon\Carbon;

class StudentDashboardController extends Controller
{
    /**
     * Display the student dashboard
     */
    public function index()
    {
        $student = Auth::guard('student')->user();

        // Check if student is active
        if ($student->status === 'Inactive') {
            Auth::guard('student')->logout();
            return redirect()->route('login')
                ->with('error', 'Your account has been deactivated.');
        }

        // Get enrolled sessions
        $enrolledSessions = $student->sessions()
            ->where('session_date', '>=', Carbon::today())
            ->orderBy('session_date', 'asc')
            ->orderBy('session_time', 'asc')
            ->limit(5)
            ->get();

        // Get upcoming sessions (next 3 days)
        $upcomingSessions = $student->sessions()
            ->where('status', 'Scheduled')
            ->whereBetween('session_date', [Carbon::today(), Carbon::today()->addDays(3)])
            ->orderBy('session_date', 'asc')
            ->orderBy('session_time', 'asc')
            ->get();

        // Statistics
        $totalEnrolled = $student->sessions()->count();
        
        $attendedSessions = $student->sessions()
            ->wherePivot('attendance_status', 'Present')
            ->count();
        
        $absentSessions = $student->sessions()
            ->wherePivot('attendance_status', 'Absent')
            ->count();
        
        $pendingSessions = $student->sessions()
            ->where('status', 'Scheduled')
            ->where('session_date', '>=', Carbon::today())
            ->count();

        // Recent announcements
        $recentAnnouncements = Announcement::where(function($query) {
                $query->where('target_audience', 'All')
                      ->orWhere('target_audience', 'Students');
            })
            ->whereNull('archived_at')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Attendance rate
        $attendanceRate = $totalEnrolled > 0 
            ? round(($attendedSessions / $totalEnrolled) * 100, 1) 
            : 0;

        return view('student.dashboard', compact(
            'student',
            'enrolledSessions',
            'upcomingSessions',
            'totalEnrolled',
            'attendedSessions',
            'absentSessions',
            'pendingSessions',
            'recentAnnouncements',
            'attendanceRate'
        ));
    }
}
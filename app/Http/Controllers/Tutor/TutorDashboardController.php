<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Session;
use App\Models\Announcement;
use App\Models\Student;
use Carbon\Carbon;

class TutorDashboardController extends Controller
{
    /**
     * Display the tutor dashboard
     */
    public function index()
    {
        $tutor = Auth::guard('tutor')->user();

        // Check if tutor is approved
        if (!$tutor->is_approved) {
            return redirect()->route('tutor.pending')
                ->with('warning', 'Your account is still pending approval.');
        }

        // Check if tutor is active
        if ($tutor->status === 'Inactive') {
            Auth::guard('tutor')->logout();
            return redirect()->route('login')
                ->with('error', 'Your account has been deactivated.');
        }

        // Get upcoming sessions for this tutor
        $upcomingSessions = Session::where('tutor_id', $tutor->id)
            ->where('status', 'Scheduled')
            ->where('session_date', '>=', Carbon::today())
            ->orderBy('session_date', 'asc')
            ->orderBy('session_time', 'asc')
            ->limit(5)
            ->get();

        // Get recent announcements
        $recentAnnouncements = Announcement::where(function($query) {
                $query->where('target_audience', 'All')
                      ->orWhere('target_audience', 'Tutors');
            })
            ->whereNull('archived_at')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Statistics
        $totalSessions = Session::where('tutor_id', $tutor->id)->count();
        $completedSessions = Session::where('tutor_id', $tutor->id)
            ->where('status', 'Completed')
            ->count();
        $upcomingCount = Session::where('tutor_id', $tutor->id)
            ->where('status', 'Scheduled')
            ->where('session_date', '>=', Carbon::today())
            ->count();
        $totalStudents = Session::where('tutor_id', $tutor->id)
            ->join('session_enrollments', 'tutor_sessions.id', '=', 'session_enrollments.session_id')
            ->distinct('session_enrollments.student_id')
            ->count('session_enrollments.student_id');

        return view('tutor.dashboard', compact(
            'tutor',
            'upcomingSessions',
            'recentAnnouncements',
            'totalSessions',
            'completedSessions',
            'upcomingCount',
            'totalStudents'
        ));
    }
}
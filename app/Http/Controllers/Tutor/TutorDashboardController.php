<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Session;
use App\Models\Student;
use App\Models\Notification;
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

        // Get unread notifications count
        $unreadNotifications = Notification::where('tutor_id', $tutor->id)
            ->unread()
            ->count();

        // Get recent notifications
        $recentNotifications = Notification::where('tutor_id', $tutor->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get tutor ratings and feedback
        $averageRating = DB::table('session_feedback')
            ->where('tutor_id', $tutor->id)
            ->avg('rating') ?? 0;

        $totalFeedback = DB::table('session_feedback')
            ->where('tutor_id', $tutor->id)
            ->count();

        // Get recent feedback with student names
        $recentFeedback = DB::table('session_feedback')
            ->join('students', 'session_feedback.student_id', '=', 'students.id')
            ->join('tutor_sessions', 'session_feedback.session_id', '=', 'tutor_sessions.id')
            ->where('session_feedback.tutor_id', $tutor->id)
            ->select(
                'session_feedback.*',
                DB::raw("CONCAT(students.first_name, ' ', students.last_name) as student_name"),
                'tutor_sessions.subject',
                'tutor_sessions.session_code'
            )
            ->orderBy('session_feedback.created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($feedback) {
                return [
                    'student_name' => $feedback->student_name,
                    'rating' => $feedback->rating,
                    'comment' => $feedback->comment ?? 'No comment provided',
                    'date' => Carbon::parse($feedback->created_at)->format('M d, Y'),
                    'subject' => $feedback->subject,
                    'session_code' => $feedback->session_code
                ];
            })
            ->toArray();

        return view('tutor.dashboard', compact(
            'tutor',
            'upcomingSessions',
            'totalSessions',
            'completedSessions',
            'upcomingCount',
            'totalStudents',
            'unreadNotifications',
            'recentNotifications',
            'averageRating',
            'totalFeedback',
            'recentFeedback'
        ));
    }
}
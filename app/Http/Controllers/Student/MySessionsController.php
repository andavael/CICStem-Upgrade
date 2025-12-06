<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MySessionsController extends Controller
{
    /**
     * Display student's enrolled sessions (only approved, exclude Pending)
     */
    public function index(Request $request)
    {
        $student = Auth::guard('student')->user();
        
        // Base query - EXCLUDE Pending attendance status
        $query = $student->sessions()
            ->with('tutor')
            ->wherePivot('attendance_status', '!=', 'Pending');

        // Apply status filter
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'upcoming':
                    $query->where('status', 'Scheduled')
                          ->where('session_date', '>=', Carbon::today());
                    break;
                case 'completed':
                    $query->where('status', 'Completed');
                    break;
                case 'past':
                    $query->where('session_date', '<', Carbon::today());
                    break;
            }
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('session_code', 'like', "%{$search}%");
            });
        }

        $sessions = $query->orderBy('session_date', 'desc')
            ->orderBy('session_time', 'desc')
            ->paginate(10);

        return view('student.my_sessions', compact('sessions', 'student'));
    }

    /**
     * Show session detail
     */
    public function show($sessionId)
    {
        $student = Auth::guard('student')->user();
        
        // Get session with pivot data - EXCLUDE Pending
        $session = $student->sessions()
            ->with('tutor')
            ->wherePivot('attendance_status', '!=', 'Pending')
            ->where('tutor_sessions.id', $sessionId)
            ->firstOrFail();

        // Get enrollment info
        $enrollment = $student->sessions()
            ->where('tutor_sessions.id', $sessionId)
            ->first()
            ->pivot;

        // Get other enrolled students count (only approved)
        $enrolledCount = $session->students()
            ->wherePivot('attendance_status', '!=', 'Pending')
            ->count();

        return view('student.session_detail', compact(
            'session',
            'student',
            'enrollment',
            'enrolledCount'
        ));
    }
}
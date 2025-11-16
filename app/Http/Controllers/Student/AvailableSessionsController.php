<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Session;
use Carbon\Carbon;

class AvailableSessionsController extends Controller
{
    /**
     * Display available sessions for student enrollment
     */
    public function index(Request $request)
    {
        $student = Auth::guard('student')->user();
        
        // Base query - sessions matching student's year level
        $query = Session::where('status', 'Scheduled')
            ->where('session_date', '>=', Carbon::today())
            ->where('year_level', $student->year_level);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('session_code', 'like', "%{$search}%");
            });
        }

        // Apply subject filter
        if ($request->filled('subject')) {
            $query->where('subject', $request->subject);
        }

        // Apply date filter
        if ($request->filled('date')) {
            $query->whereDate('session_date', $request->date);
        }

        $sessions = $query->with('tutor')
            ->orderBy('session_date', 'asc')
            ->orderBy('session_time', 'asc')
            ->paginate(12);

        // Get unique subjects for filter
        $subjects = Session::where('status', 'Scheduled')
            ->where('session_date', '>=', Carbon::today())
            ->where('year_level', $student->year_level)
            ->distinct()
            ->pluck('subject')
            ->sort();

        // Get student's enrolled session IDs
        $enrolledSessionIds = $student->sessions()->pluck('tutor_sessions.id')->toArray();

        return view('student.available_sessions', compact(
            'sessions',
            'subjects',
            'student',
            'enrolledSessionIds'
        ));
    }

    /**
     * Enroll student in a session
     */
    public function enroll($sessionId)
    {
        $student = Auth::guard('student')->user();
        $session = Session::findOrFail($sessionId);

        // Validation checks
        if ($session->status !== 'Scheduled') {
            return redirect()->back()->with('error', 'This session is not available for enrollment.');
        }

        if ($session->year_level !== $student->year_level) {
            return redirect()->back()->with('error', 'This session is not for your year level.');
        }

        if ($session->isFull()) {
            return redirect()->back()->with('error', 'This session is already full.');
        }

        if ($student->sessions()->where('tutor_sessions.id', $sessionId)->exists()) {
            return redirect()->back()->with('warning', 'You are already enrolled in this session.');
        }

        // Enroll student
        $student->sessions()->attach($sessionId, [
            'enrolled_at' => now(),
            'attendance_status' => 'Pending'
        ]);

        return redirect()->back()->with('success', 'Successfully enrolled in the session!');
    }

    /**
     * Unenroll student from a session
     */
    public function unenroll($sessionId)
    {
        $student = Auth::guard('student')->user();
        $session = Session::findOrFail($sessionId);

        // Check if session is in the future
        if ($session->session_date < Carbon::today()) {
            return redirect()->back()->with('error', 'Cannot unenroll from past sessions.');
        }

        // Check if enrolled
        if (!$student->sessions()->where('tutor_sessions.id', $sessionId)->exists()) {
            return redirect()->back()->with('error', 'You are not enrolled in this session.');
        }

        // Unenroll
        $student->sessions()->detach($sessionId);

        return redirect()->back()->with('success', 'Successfully unenrolled from the session.');
    }
}
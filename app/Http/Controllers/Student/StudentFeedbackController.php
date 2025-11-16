<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Session;
use Illuminate\Support\Facades\DB;

class StudentFeedbackController extends Controller
{
    /**
     * Display feedback page with completed sessions
     */
    public function index()
    {
        $student = Auth::guard('student')->user();
        
        // Get completed sessions that student attended
        $completedSessions = $student->sessions()
            ->where('status', 'Completed')
            ->wherePivot('attendance_status', 'Present')
            ->with('tutor')
            ->orderBy('session_date', 'desc')
            ->get();

        // Check which sessions already have feedback
        $feedbackGiven = DB::table('session_feedback')
            ->where('student_id', $student->id)
            ->pluck('session_id')
            ->toArray();

        return view('student.feedback', compact(
            'student',
            'completedSessions',
            'feedbackGiven'
        ));
    }

    /**
     * Show feedback form for specific session
     */
    public function create($sessionId)
    {
        $student = Auth::guard('student')->user();
        
        // Verify student attended this session
        $session = $student->sessions()
            ->where('tutor_sessions.id', $sessionId)
            ->where('status', 'Completed')
            ->wherePivot('attendance_status', 'Present')
            ->with('tutor')
            ->firstOrFail();

        // Check if feedback already exists
        $existingFeedback = DB::table('session_feedback')
            ->where('student_id', $student->id)
            ->where('session_id', $sessionId)
            ->first();

        if ($existingFeedback) {
            return redirect()->route('student.feedback.index')
                ->with('warning', 'You have already submitted feedback for this session.');
        }

        return view('student.feedback_form', compact('session', 'student'));
    }

    /**
     * Store feedback
     */
    public function store(Request $request, $sessionId)
    {
        $student = Auth::guard('student')->user();
        
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Verify student attended this session
        $session = $student->sessions()
            ->where('tutor_sessions.id', $sessionId)
            ->where('status', 'Completed')
            ->wherePivot('attendance_status', 'Present')
            ->firstOrFail();

        // Check if feedback already exists
        $exists = DB::table('session_feedback')
            ->where('student_id', $student->id)
            ->where('session_id', $sessionId)
            ->exists();

        if ($exists) {
            return redirect()->route('student.feedback.index')
                ->with('warning', 'You have already submitted feedback for this session.');
        }

        // Store feedback
        DB::table('session_feedback')->insert([
            'session_id' => $sessionId,
            'student_id' => $student->id,
            'tutor_id' => $session->tutor_id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('student.feedback.index')
            ->with('success', 'Thank you for your feedback!');
    }

    /**
     * View submitted feedback
     */
    public function show($sessionId)
    {
        $student = Auth::guard('student')->user();
        
        $session = $student->sessions()
            ->where('tutor_sessions.id', $sessionId)
            ->with('tutor')
            ->firstOrFail();

        $feedback = DB::table('session_feedback')
            ->where('student_id', $student->id)
            ->where('session_id', $sessionId)
            ->first();

        if (!$feedback) {
            return redirect()->route('student.feedback.index')
                ->with('error', 'Feedback not found.');
        }

        return view('student.feedback_view', compact('session', 'feedback', 'student'));
    }
}
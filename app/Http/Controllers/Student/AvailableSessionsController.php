<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Session;
use App\Models\StudentNotification;
use App\Models\TutorNotification;
use Carbon\Carbon;

class AvailableSessionsController extends Controller
{
    /**
     * Normalize year level for comparison
     */
    private function normalizeYearLevel($yearLevel)
    {
        $yearLevel = strtolower(trim($yearLevel));
        
        $mappings = [
            '1st year' => ['1st year', 'first year', '1st', 'first'],
            '2nd year' => ['2nd year', 'second year', '2nd', 'second'],
            '3rd year' => ['3rd year', 'third year', '3rd', 'third'],
            '4th year' => ['4th year', 'fourth year', '4th', 'fourth'],
            'all' => ['all']
        ];
        
        foreach ($mappings as $standard => $variants) {
            if (in_array($yearLevel, $variants)) {
                return $standard;
            }
        }
        
        return $yearLevel;
    }
    
    /**
     * Display available sessions for student enrollment
     */
    public function index(Request $request)
    {
        $student = Auth::guard('student')->user();
        
        $enrolledSessionIds = $student->sessions()->pluck('tutor_sessions.id')->toArray();
        
        $normalizedStudentYear = $this->normalizeYearLevel($student->year_level);
        
        $query = Session::where('status', 'Scheduled')
            ->where('session_date', '>=', Carbon::today());

        $allSessions = $query->with('tutor', 'students')->get();
        
        $now = Carbon::now();
        $allSessions = $allSessions->filter(function($session) use ($now) {
            $timeString = substr($session->session_time, 0, 5);
            $sessionStart = Carbon::createFromFormat(
                'Y-m-d H:i',
                $session->session_date->format('Y-m-d') . ' ' . $timeString
            );
            
            return $now->lt($sessionStart);
        });
        
        $filteredSessions = $allSessions->filter(function($session) use ($normalizedStudentYear) {
            $sessionYearLevel = $this->normalizeYearLevel($session->year_level);
            return $sessionYearLevel === $normalizedStudentYear || $sessionYearLevel === 'all';
        });
        
        $filteredSessions = $filteredSessions->reject(function($session) use ($enrolledSessionIds) {
            return in_array($session->id, $enrolledSessionIds);
        });
        
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $filteredSessions = $filteredSessions->filter(function($session) use ($search) {
                return str_contains(strtolower($session->subject), $search) ||
                       str_contains(strtolower($session->session_code), $search);
            });
        }
        
        if ($request->filled('subject')) {
            $filteredSessions = $filteredSessions->filter(function($session) use ($request) {
                return $session->subject === $request->subject;
            });
        }
        
        if ($request->filled('date')) {
            $filterDate = Carbon::parse($request->date)->format('Y-m-d');
            $filteredSessions = $filteredSessions->filter(function($session) use ($filterDate) {
                return $session->session_date->format('Y-m-d') === $filterDate;
            });
        }
        
        $filteredSessions = $filteredSessions->sortBy([
            ['session_date', 'asc'],
            ['session_time', 'asc']
        ])->values();
        
        $perPage = 12;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        $paginatedItems = $filteredSessions->slice($offset, $perPage)->values();
        
        $sessions = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $filteredSessions->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $subjects = $allSessions->filter(function($session) use ($normalizedStudentYear) {
            $sessionYearLevel = $this->normalizeYearLevel($session->year_level);
            return $sessionYearLevel === $normalizedStudentYear || $sessionYearLevel === 'all';
        })->pluck('subject')->unique()->sort()->values();

        return view('student.available_sessions', compact(
            'sessions',
            'subjects',
            'student',
            'enrolledSessionIds'
        ));
    }

    /**
     * Check if student has conflicting sessions
     */
    private function checkStudentConflict($student, $sessionDate, $sessionTime)
    {
        $timeString = substr($sessionTime, 0, 5);
        $newSessionStart = Carbon::createFromFormat('Y-m-d H:i', $sessionDate . ' ' . $timeString);
        
        $newSessionEnd = $newSessionStart->copy()->addHours(2);
        
        // Only check approved sessions (not pending)
        $enrolledSessions = $student->sessions()
            ->where('status', '!=', 'Cancelled')
            ->wherePivot('attendance_status', '!=', 'Pending')
            ->get();
        
        foreach ($enrolledSessions as $existing) {
            $existingTimeString = substr($existing->session_time, 0, 5);
            $existingStart = Carbon::createFromFormat(
                'Y-m-d H:i',
                $existing->session_date->format('Y-m-d') . ' ' . $existingTimeString
            );
            $existingEnd = $existingStart->copy()->addHours(2);
            
            if ($newSessionStart->lt($existingEnd) && $newSessionEnd->gt($existingStart)) {
                return [
                    'conflict' => true,
                    'session' => $existing,
                    'start_time' => $existingStart,
                    'end_time' => $existingEnd
                ];
            }
        }
        
        return ['conflict' => false];
    }

    /**
     * Enroll student in a session (send request)
     */
    public function enroll($sessionId)
    {
        $student = Auth::guard('student')->user();
        $session = Session::findOrFail($sessionId);

        if ($session->status !== 'Scheduled') {
            return redirect()->back()->with('error', 'This session is not available for enrollment. Only scheduled sessions can be enrolled in.');
        }
        
        $now = Carbon::now();
        $timeString = substr($session->session_time, 0, 5);
        $sessionStart = Carbon::createFromFormat(
            'Y-m-d H:i',
            $session->session_date->format('Y-m-d') . ' ' . $timeString
        );
        
        if ($now->gte($sessionStart)) {
            return redirect()->back()->with('error', 'This session has already started. You can only enroll in sessions that have not yet begun.');
        }

        $normalizedStudentYear = $this->normalizeYearLevel($student->year_level);
        $normalizedSessionYear = $this->normalizeYearLevel($session->year_level);
        
        if ($normalizedSessionYear !== $normalizedStudentYear && $normalizedSessionYear !== 'all') {
            return redirect()->back()->with('error', 'This session is not for your year level.');
        }

        if ($session->isFull()) {
            return redirect()->back()->with('error', 'This session is already full.');
        }

        if ($student->sessions()->where('tutor_sessions.id', $sessionId)->exists()) {
            return redirect()->back()->with('warning', 'You have already sent a request for this session.');
        }

        $conflictCheck = $this->checkStudentConflict(
            $student,
            $session->session_date->format('Y-m-d'),
            $session->session_time
        );
        
        if ($conflictCheck['conflict']) {
            $conflictingSession = $conflictCheck['session'];
            $conflictStart = $conflictCheck['start_time'];
            $conflictEnd = $conflictCheck['end_time'];
            
            return redirect()->back()->with('error', 
                "You cannot enroll in this session because it conflicts with another approved session: " .
                "{$conflictingSession->subject} ({$conflictingSession->session_code}) on " .
                "{$conflictStart->format('M d, Y')} from {$conflictStart->format('g:i A')} to {$conflictEnd->format('g:i A')}. " .
                "Please choose a different session or unenroll from the conflicting session first."
            );
        }

        try {
            // Send enrollment request with Pending status
            $student->sessions()->attach($sessionId, [
                'enrolled_at' => now(),
                'attendance_status' => 'Pending'
            ]);

            // NO notification to student - they only get notified when approved
            
            // Notify tutor of new enrollment request
            TutorNotification::notifyNewEnrollment($session->tutor_id, $session, $student);

            return redirect()->back()->with('success', 'Enrollment request sent! Please wait for tutor approval.');
            
        } catch (\Exception $e) {
            \Log::error("Enrollment error: " . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred during enrollment. Please try again.');
        }
    }

    /**
     * Unenroll student from a session
     */
    public function unenroll($sessionId)
    {
        $student = Auth::guard('student')->user();
        $session = Session::findOrFail($sessionId);

        $now = Carbon::now();
        $timeString = substr($session->session_time, 0, 5);
        $sessionStart = Carbon::createFromFormat(
            'Y-m-d H:i',
            $session->session_date->format('Y-m-d') . ' ' . $timeString
        );
        
        if ($now->gte($sessionStart)) {
            return redirect()->back()->with('error', 'Cannot unenroll from a session that has already started.');
        }

        if (!$student->sessions()->where('tutor_sessions.id', $sessionId)->exists()) {
            return redirect()->back()->with('error', 'You are not enrolled in this session.');
        }

        // Get enrollment status before unenrolling
        $enrollment = $student->sessions()
            ->where('tutor_sessions.id', $sessionId)
            ->first()
            ->pivot;

        // Unenroll
        $student->sessions()->detach($sessionId);

        // Notify tutor of unenrollment
        $sessionDate = $session->session_date->format('M d, Y');
        $sessionTime = substr($session->session_time, 0, 5);
        
        $message = $enrollment->attendance_status === 'Pending' 
            ? "Student {$student->first_name} {$student->last_name} ({$student->sr_code}) has cancelled their enrollment request for your session '{$session->subject} ({$session->session_code})' scheduled for {$sessionDate} at {$sessionTime}."
            : "Student {$student->first_name} {$student->last_name} ({$student->sr_code}) has unenrolled from your session '{$session->subject} ({$session->session_code})' scheduled for {$sessionDate} at {$sessionTime}.";
        
        TutorNotification::notifyTutors(
            $session->tutor_id,
            'student_enrollment',
            'Student Unenrolled',
            $message,
            $session->id
        );

        return redirect()->back()->with('success', 'Successfully unenrolled from the session.');
    }
}
<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Session;
use Carbon\Carbon;

class AvailableSessionsController extends Controller
{
    /**
     * Normalize year level for comparison
     */
    private function normalizeYearLevel($yearLevel)
    {
        $yearLevel = strtolower(trim($yearLevel));
        
        // Map different formats to a standard format
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
        
        // Get student's enrolled session IDs
        $enrolledSessionIds = $student->sessions()->pluck('tutor_sessions.id')->toArray();
        
        // Normalize student's year level for comparison
        $normalizedStudentYear = $this->normalizeYearLevel($student->year_level);
        
        // Base query - get only SCHEDULED sessions from today onwards
        $query = Session::where('status', 'Scheduled')
            ->where('session_date', '>=', Carbon::today());

        // Get all matching sessions
        $allSessions = $query->with('tutor', 'students')->get();
        
        // Filter out sessions that have already started
        $now = Carbon::now();
        $allSessions = $allSessions->filter(function($session) use ($now) {
            // Parse session time
            $timeString = substr($session->session_time, 0, 5);
            $sessionStart = Carbon::createFromFormat(
                'Y-m-d H:i',
                $session->session_date->format('Y-m-d') . ' ' . $timeString
            );
            
            // Only keep sessions that haven't started yet
            return $now->lt($sessionStart);
        });
        
        // Filter by year level using normalized comparison
        $filteredSessions = $allSessions->filter(function($session) use ($normalizedStudentYear) {
            $sessionYearLevel = $this->normalizeYearLevel($session->year_level);
            return $sessionYearLevel === $normalizedStudentYear || $sessionYearLevel === 'all';
        });
        
        // Exclude sessions student is already enrolled in
        $filteredSessions = $filteredSessions->reject(function($session) use ($enrolledSessionIds) {
            return in_array($session->id, $enrolledSessionIds);
        });
        
        // Apply search filter if present
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $filteredSessions = $filteredSessions->filter(function($session) use ($search) {
                return str_contains(strtolower($session->subject), $search) ||
                       str_contains(strtolower($session->session_code), $search);
            });
        }
        
        // Apply subject filter if present
        if ($request->filled('subject')) {
            $filteredSessions = $filteredSessions->filter(function($session) use ($request) {
                return $session->subject === $request->subject;
            });
        }
        
        // Apply date filter if present
        if ($request->filled('date')) {
            $filterDate = Carbon::parse($request->date)->format('Y-m-d');
            $filteredSessions = $filteredSessions->filter(function($session) use ($filterDate) {
                return $session->session_date->format('Y-m-d') === $filterDate;
            });
        }
        
        // Sort the collection
        $filteredSessions = $filteredSessions->sortBy([
            ['session_date', 'asc'],
            ['session_time', 'asc']
        ])->values();
        
        // Manual pagination
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

        // Get unique subjects for filter
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
        // Parse the new session datetime
        $timeString = substr($sessionTime, 0, 5); // Get HH:MM part
        $newSessionStart = Carbon::createFromFormat('Y-m-d H:i', $sessionDate . ' ' . $timeString);
        
        // Each session lasts 2 hours
        $newSessionEnd = $newSessionStart->copy()->addHours(2);
        
        // Get student's enrolled sessions that are not cancelled
        $enrolledSessions = $student->sessions()
            ->where('status', '!=', 'Cancelled')
            ->get();
        
        // Check for overlaps
        foreach ($enrolledSessions as $existing) {
            // Parse existing session time
            $existingTimeString = substr($existing->session_time, 0, 5);
            $existingStart = Carbon::createFromFormat(
                'Y-m-d H:i',
                $existing->session_date->format('Y-m-d') . ' ' . $existingTimeString
            );
            $existingEnd = $existingStart->copy()->addHours(2);
            
            // Check if sessions overlap
            // Sessions overlap if: new starts before existing ends AND new ends after existing starts
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
     * Enroll student in a session
     */
    public function enroll($sessionId)
    {
        $student = Auth::guard('student')->user();
        $session = Session::findOrFail($sessionId);

        // Validation checks
        if ($session->status !== 'Scheduled') {
            return redirect()->back()->with('error', 'This session is not available for enrollment. Only scheduled sessions can be enrolled in.');
        }
        
        // Check if session has already started
        $now = Carbon::now();
        $timeString = substr($session->session_time, 0, 5);
        $sessionStart = Carbon::createFromFormat(
            'Y-m-d H:i',
            $session->session_date->format('Y-m-d') . ' ' . $timeString
        );
        
        if ($now->gte($sessionStart)) {
            return redirect()->back()->with('error', 'This session has already started. You can only enroll in sessions that have not yet begun.');
        }

        // Check year level using normalized comparison
        $normalizedStudentYear = $this->normalizeYearLevel($student->year_level);
        $normalizedSessionYear = $this->normalizeYearLevel($session->year_level);
        
        if ($normalizedSessionYear !== $normalizedStudentYear && $normalizedSessionYear !== 'all') {
            return redirect()->back()->with('error', 'This session is not for your year level.');
        }

        if ($session->isFull()) {
            return redirect()->back()->with('error', 'This session is already full.');
        }

        if ($student->sessions()->where('tutor_sessions.id', $sessionId)->exists()) {
            return redirect()->back()->with('warning', 'You are already enrolled in this session.');
        }

        // Check for scheduling conflicts
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
                "You cannot enroll in this session because it conflicts with another session you're enrolled in: " .
                "{$conflictingSession->subject} ({$conflictingSession->session_code}) on " .
                "{$conflictStart->format('M d, Y')} from {$conflictStart->format('g:i A')} to {$conflictEnd->format('g:i A')}. " .
                "Please choose a different session or unenroll from the conflicting session first."
            );
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

        // Check if session has already started
        $now = Carbon::now();
        $timeString = substr($session->session_time, 0, 5);
        $sessionStart = Carbon::createFromFormat(
            'Y-m-d H:i',
            $session->session_date->format('Y-m-d') . ' ' . $timeString
        );
        
        if ($now->gte($sessionStart)) {
            return redirect()->back()->with('error', 'Cannot unenroll from a session that has already started.');
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
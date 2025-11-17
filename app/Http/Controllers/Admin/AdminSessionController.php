<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\Tutor;
use App\Models\Subject;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminSessionController extends Controller
{
    /**
     * Display list of sessions
     */
    public function index(Request $request)
    {
        // Auto-update expired sessions to Completed
        $this->updateExpiredSessions();
        
        // Base query with tutor relation
        $query = Session::with('tutor');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by subject or session code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                ->orWhere('session_code', 'like', "%{$search}%");
            });
        }

        // Paginate results
        $sessions = $query->orderBy('session_date', 'desc')
                        ->orderBy('session_time', 'desc')
                        ->paginate(15);

        // Refresh each session to get updated status
        $sessions->getCollection()->transform(function ($session) {
            return $session->fresh(['tutor', 'students']);
        });

        // Optional: tutors for filter dropdown (only active & approved)
        $tutors = Tutor::where('is_approved', true)
                    ->where('status', 'Active')
                    ->orderBy('last_name')
                    ->get();

        // Return view with sessions and tutors
        return view('admin.sessions.index', compact('sessions', 'tutors'));
    }

    
    /**
     * Show form to create new session
     */
    public function create()
    {
        $tutors = Tutor::where('is_approved', true)
                      ->where('status', 'Active')
                      ->orderBy('last_name')
                      ->get();
         // Get all subjects
        $subjects = Subject::orderBy('name')->get();

        // Only 1st and 2nd Year students
        $yearLevels = ['1st Year', '2nd Year'];

        return view('admin.sessions.create', compact('tutors', 'subjects', 'yearLevels'));
    }
    
    /**
     * Generate a Google Meet code in xxx-yyyy-zzz format
     */
    private function generateMeetCode()
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        
        $part1 = substr(str_shuffle($characters), 0, 3);
        $part2 = substr(str_shuffle($characters), 0, 4);
        $part3 = substr(str_shuffle($characters), 0, 3);
        
        return "{$part1}-{$part2}-{$part3}";
    }
    
    /**
     * Auto-update sessions to Completed if 2 hours have passed
     */
    private function updateExpiredSessions()
    {
        $now = now(); // Gets current time in Laravel's configured timezone
        
        // Get all Scheduled or Ongoing sessions
        $sessions = Session::whereIn('status', ['Scheduled', 'Ongoing'])->get();
        
        foreach ($sessions as $session) {
            try {
                // Parse session time (handles both H:i and H:i:s formats)
                $timeString = substr($session->session_time, 0, 5); // Get HH:MM part
                
                // Combine session date and time - use same timezone as $now
                $sessionDateStr = $session->session_date->format('Y-m-d');
                $sessionStart = Carbon::parse($sessionDateStr . ' ' . $timeString, $now->timezone);
                
                // Add 2 hours to get session end time
                $sessionEnd = $sessionStart->copy()->addHours(2);
                
                // Debug logging (remove after testing)
                \Log::info("Session Check", [
                    'session_id' => $session->id,
                    'current_time' => $now->format('Y-m-d H:i:s'),
                    'session_start' => $sessionStart->format('Y-m-d H:i:s'),
                    'session_end' => $sessionEnd->format('Y-m-d H:i:s'),
                    'current_status' => $session->status,
                    'timezone' => $now->timezone->getName()
                ]);
                
                // If session has started but not ended, mark as Ongoing
                if ($now->gte($sessionStart) && $now->lt($sessionEnd) && $session->status === 'Scheduled') {
                    $session->update(['status' => 'Ongoing']);
                    \Log::info("Updated session {$session->id} to Ongoing");
                }
                // If current time is past session end time, mark as Completed
                elseif ($now->gte($sessionEnd) && in_array($session->status, ['Scheduled', 'Ongoing'])) {
                    $session->update(['status' => 'Completed']);
                    \Log::info("Updated session {$session->id} to Completed");
                }
            } catch (\Exception $e) {
                \Log::error("Error updating session {$session->id}: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Check if tutor has conflicting sessions
     */
    private function checkTutorConflict($tutorId, $sessionDate, $sessionTime, $excludeSessionId = null)
    {
        // Parse the new session datetime
        $newSessionStart = Carbon::createFromFormat('Y-m-d H:i', $sessionDate . ' ' . $sessionTime);
        
        // Each session lasts 2 hours
        $newSessionEnd = $newSessionStart->copy()->addHours(2);
        
        // Query for existing sessions for this tutor
        $query = Session::where('tutor_id', $tutorId)
                       ->where('status', '!=', 'Cancelled'); // Ignore cancelled sessions
        
        // Exclude current session when editing
        if ($excludeSessionId) {
            $query->where('id', '!=', $excludeSessionId);
        }
        
        $existingSessions = $query->get();
        
        // Check for overlaps
        foreach ($existingSessions as $existing) {
            // Parse session time (handles both H:i and H:i:s formats)
            $timeString = substr($existing->session_time, 0, 5); // Get HH:MM part
            
            $existingStart = Carbon::createFromFormat(
                'Y-m-d H:i',
                $existing->session_date->format('Y-m-d') . ' ' . $timeString
            );
            $existingEnd = $existingStart->copy()->addHours(2);
            
            // Check if sessions overlap
            // Sessions overlap if: new starts before existing ends AND new ends after existing starts
            if ($newSessionStart->lt($existingEnd) && $newSessionEnd->gt($existingStart)) {
                return [
                    'conflict' => true,
                    'session' => $existing
                ];
            }
        }
        
        return ['conflict' => false];
    }
    
    /**
     * Store new session
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'session_date' => 'required|date|after_or_equal:today',
            'session_time' => 'required|date_format:H:i',
            'tutor_id' => 'required|exists:tutors,id',
            'capacity' => 'required|integer|min:1|max:100',
            'year_level' => 'required|in:1st Year,2nd Year,3rd Year,4th Year,All',
            'google_meet_link' => 'nullable|url',
            'description' => 'nullable|string',
            'status' => 'nullable|in:Scheduled,Ongoing,Completed,Cancelled',
        ], [
            'session_date.after_or_equal' => 'The session date cannot be in the past.'
        ]);

        // Build exact datetime using createFromFormat (MOST RELIABLE)
        $sessionDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['session_date'] . ' ' . $validated['session_time']
        );

        // Compare with current datetime (in Laravel timezone)
        if ($sessionDateTime->lt(now())) {
            return back()
                ->withErrors([
                    'session_time' => 'The selected date and time has already passed. Please choose a future date and time.'
                ])
                ->withInput();
        }

        // Check for tutor conflicts
        $conflictCheck = $this->checkTutorConflict(
            $validated['tutor_id'],
            $validated['session_date'],
            $validated['session_time']
        );
        
        if ($conflictCheck['conflict']) {
            $conflictingSession = $conflictCheck['session'];
            // Parse session time (handles both H:i and H:i:s formats)
            $timeString = substr($conflictingSession->session_time, 0, 5);
            $conflictDateTime = Carbon::createFromFormat(
                'Y-m-d H:i',
                $conflictingSession->session_date->format('Y-m-d') . ' ' . $timeString
            );
            
            return back()
                ->withErrors([
                    'tutor_id' => "This tutor is already assigned to another session on {$conflictDateTime->format('M d, Y')} at {$timeString}. Please select a different tutor or change the date/time."
                ])
                ->withInput();
        }

        // Set default status to Scheduled if not provided
        $validated['status'] = $validated['status'] ?? 'Scheduled';

        // Generate session code
        $validated['session_code'] = 'SES-' . strtoupper(substr(uniqid(), -8));

        // Auto-generate Google Meet link with note
        if (empty($validated['google_meet_link'])) {
            $validated['google_meet_link'] = 'https://meet.google.com/new';
        }

        Session::create($validated);

        return redirect()->route('admin.sessions.index')
            ->with('success', 'Session created successfully. Note: You can create the actual Google Meet link and update it later.');
    }

    
    /**
     * Display session details and enrollment list
     */
    public function show(Session $session)
    {
        // Auto-update expired sessions
        $this->updateExpiredSessions();
        
        // Refresh the session to get updated status
        $session = $session->fresh(['tutor', 'students']);
        
        $enrolledCount = $session->students()->count();
        
        return view('admin.sessions.show', compact('session', 'enrolledCount'));
    }
    
    /**
     * Show form to edit session
     */
    public function edit(Session $session)
    {
        $tutors = Tutor::where('is_approved', true)
                      ->where('status', 'Active')
                      ->orderBy('last_name')
                      ->get();
        $subjects = Subject::orderBy('name')->get();
        
        return view('admin.sessions.edit', compact('session', 'tutors', 'subjects'));
    }
    
    /**
     * Update session
     */
    public function update(Request $request, Session $session)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'session_date' => 'required|date',
            'session_time' => 'required|date_format:H:i',
            'tutor_id' => 'required|exists:tutors,id',
            'capacity' => 'required|integer|min:1|max:100',
            'year_level' => 'required|in:1st Year,2nd Year,3rd Year,4th Year,All',
            'google_meet_link' => 'required|url|regex:/^https:\/\/meet\.google\.com\/[a-z]{3}-[a-z]{4}-[a-z]{3}$/',
            'description' => 'nullable|string',
            'status' => 'required|in:Scheduled,Ongoing,Completed,Cancelled',
        ], [
            'google_meet_link.regex' => 'Google Meet link must be in the format: https://meet.google.com/xxx-yyyy-zzz'
        ]);

        // Build exact datetime using createFromFormat
        $sessionDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['session_date'] . ' ' . $validated['session_time']
        );

        // Compare with current datetime - prevent past dates
        if ($sessionDateTime->lt(now())) {
            return back()
                ->withErrors([
                    'session_time' => 'The selected date and time has already passed. Please choose a future date and time.'
                ])
                ->withInput();
        }

        // Check for tutor conflicts (excluding current session)
        $conflictCheck = $this->checkTutorConflict(
            $validated['tutor_id'],
            $validated['session_date'],
            $validated['session_time'],
            $session->id // Exclude current session from conflict check
        );
        
        if ($conflictCheck['conflict']) {
            $conflictingSession = $conflictCheck['session'];
            // Parse session time (handles both H:i and H:i:s formats)
            $timeString = substr($conflictingSession->session_time, 0, 5);
            $conflictDateTime = Carbon::createFromFormat(
                'Y-m-d H:i',
                $conflictingSession->session_date->format('Y-m-d') . ' ' . $timeString
            );
            
            return back()
                ->withErrors([
                    'tutor_id' => "This tutor is already assigned to another session on {$conflictDateTime->format('M d, Y')} at {$timeString}. Please select a different tutor or change the date/time."
                ])
                ->withInput();
        }
        
        $session->update($validated);
        
        return redirect()->route('admin.sessions.show', $session)
            ->with('success', 'Session updated successfully');
    }
    
    /**
     * Delete session
     */
    public function destroy(Session $session)
    {
        $session->delete();
        
        return redirect()->route('admin.sessions.index')
            ->with('success', 'Session deleted successfully');
    }
    
    /**
     * Cancel session
     */
    public function cancel(Session $session)
    {
        $session->update(['status' => 'Cancelled']);
        
        return redirect()->back()
            ->with('success', 'Session cancelled successfully');
    }
}
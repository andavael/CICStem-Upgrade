<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\Tutor;
use App\Models\Subject;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\StudentNotification;
use App\Models\TutorNotification;

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
        $now = now();
        
        $sessions = Session::whereIn('status', ['Scheduled', 'Ongoing'])->get();
        
        foreach ($sessions as $session) {
            try {
                $timeString = substr($session->session_time, 0, 5);
                
                $sessionDateStr = $session->session_date->format('Y-m-d');
                $sessionStart = Carbon::parse($sessionDateStr . ' ' . $timeString, $now->timezone);
                
                $sessionEnd = $sessionStart->copy()->addHours(2);
                
                \Log::info("Session Check", [
                    'session_id' => $session->id,
                    'current_time' => $now->format('Y-m-d H:i:s'),
                    'session_start' => $sessionStart->format('Y-m-d H:i:s'),
                    'session_end' => $sessionEnd->format('Y-m-d H:i:s'),
                    'current_status' => $session->status,
                    'timezone' => $now->timezone->getName()
                ]);
                
                if ($now->gte($sessionStart) && $now->lt($sessionEnd) && $session->status === 'Scheduled') {
                    $session->update(['status' => 'Ongoing']);
                    \Log::info("Updated session {$session->id} to Ongoing");
                }
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
        $newSessionStart = Carbon::createFromFormat('Y-m-d H:i', $sessionDate . ' ' . $sessionTime);
        
        $newSessionEnd = $newSessionStart->copy()->addHours(2);
        
        $query = Session::where('tutor_id', $tutorId)
                       ->where('status', '!=', 'Cancelled');
        
        if ($excludeSessionId) {
            $query->where('id', '!=', $excludeSessionId);
        }
        
        $existingSessions = $query->get();
        
        foreach ($existingSessions as $existing) {
            $timeString = substr($existing->session_time, 0, 5);
            
            $existingStart = Carbon::createFromFormat(
                'Y-m-d H:i',
                $existing->session_date->format('Y-m-d') . ' ' . $timeString
            );
            $existingEnd = $existingStart->copy()->addHours(2);
            
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
            'year_level' => 'required|in:First Year,Second Year',
            'google_meet_link' => 'nullable|url',
            'description' => 'nullable|string',
            'status' => 'nullable|in:Scheduled,Ongoing,Completed,Cancelled',
        ], [
            'session_date.after_or_equal' => 'The session date cannot be in the past.'
        ]);

        $sessionDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['session_date'] . ' ' . $validated['session_time']
        );

        if ($sessionDateTime->lt(now())) {
            return back()
                ->withErrors([
                    'session_time' => 'The selected date and time has already passed. Please choose a future date and time.'
                ])
                ->withInput();
        }

        $conflictCheck = $this->checkTutorConflict(
            $validated['tutor_id'],
            $validated['session_date'],
            $validated['session_time']
        );
        
        if ($conflictCheck['conflict']) {
            $conflictingSession = $conflictCheck['session'];
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

        $validated['status'] = $validated['status'] ?? 'Scheduled';

        $validated['session_code'] = 'SES-' . strtoupper(substr(uniqid(), -8));

        if (empty($validated['google_meet_link'])) {
            $validated['google_meet_link'] = 'https://meet.google.com/new';
        }

        $session = Session::create($validated);

        // Notify the assigned tutor
        $this->notifyTutorOfNewAssignment($session);

        return redirect()->route('admin.sessions.index')
            ->with('success', 'Session created successfully. The assigned tutor has been notified.');
    }

    
    /**
     * Display session details and enrollment list
     */
    public function show(Session $session)
    {
        $this->updateExpiredSessions();
        
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
        // Store old values for comparison
        $oldDate = $session->session_date->format('Y-m-d');
        $oldTime = $session->session_time;
        $oldStatus = $session->status;
        $oldTutorId = $session->tutor_id;

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'session_date' => 'required|date',
            'session_time' => 'required|date_format:H:i',
            'tutor_id' => 'required|exists:tutors,id',
            'capacity' => 'required|integer|min:1|max:100',
            'year_level' => 'required|in:First Year,Second Year',
            'google_meet_link' => 'nullable|url',
            'description' => 'nullable|string',
            'status' => 'required|in:Scheduled,Ongoing,Completed,Cancelled',
        ]);

        $sessionDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['session_date'] . ' ' . $validated['session_time']
        );

        if ($sessionDateTime->lt(now())) {
            return back()
                ->withErrors([
                    'session_time' => 'The selected date and time has already passed. Please choose a future date and time.'
                ])
                ->withInput();
        }

        $conflictCheck = $this->checkTutorConflict(
            $validated['tutor_id'],
            $validated['session_date'],
            $validated['session_time'],
            $session->id
        );
        
        if ($conflictCheck['conflict']) {
            $conflictingSession = $conflictCheck['session'];
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
        
        if (empty($validated['google_meet_link'])) {
            $validated['google_meet_link'] = 'https://meet.google.com/new';
        }
        
        // Check what changed
        $dateChanged = ($oldDate !== $validated['session_date']);
        $timeChanged = ($oldTime !== $validated['session_time']);
        $statusChanged = ($oldStatus !== $validated['status']);
        $tutorChanged = ($oldTutorId !== $validated['tutor_id']);
        $wasCancelled = ($validated['status'] === 'Cancelled' && $oldStatus !== 'Cancelled');

        // Update the session
        $session->update($validated);
        
        // Send notifications to students
        if ($wasCancelled) {
            $this->notifyStudentsOfSessionChange($session, 'cancelled');
            $this->notifyTutorOfCancellation($session);
        } elseif ($dateChanged || $timeChanged) {
            $this->notifyStudentsOfSessionChange($session, 'rescheduled', $oldDate, $oldTime);
            $this->notifyTutorOfReschedule($session, $oldDate, $oldTime);
        } elseif ($statusChanged || $session->wasChanged()) {
            $this->notifyStudentsOfSessionChange($session, 'modified');
            $this->notifyTutorOfModification($session);
        }
        
        // If tutor was changed, notify both old and new tutor
        if ($tutorChanged) {
            $this->notifyTutorOfReassignment($session, $oldTutorId);
        }
        
        return redirect()->route('admin.sessions.show', $session)
            ->with('success', 'Session updated successfully. All affected parties have been notified.');
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
        
        // Notify all enrolled students
        $this->notifyStudentsOfSessionChange($session, 'cancelled');
        
        // Notify the tutor
        $this->notifyTutorOfCancellation($session);
        
        return redirect()->back()
            ->with('success', 'Session cancelled successfully. All enrolled students and the tutor have been notified.');
    }

    /**
     * Notify students of session changes
     */
    private function notifyStudentsOfSessionChange($session, $changeType, $oldDate = null, $oldTime = null)
    {
        $enrolledStudents = $session->students()->pluck('students.id')->toArray();
        
        if (empty($enrolledStudents)) {
            return;
        }

        $sessionDate = \Carbon\Carbon::parse($session->session_date)->format('M d, Y');
        $sessionTime = substr($session->session_time, 0, 5);

        switch ($changeType) {
            case 'modified':
                $title = 'Session Schedule Changed';
                if ($oldDate && $oldTime) {
                    $oldDateFormatted = \Carbon\Carbon::parse($oldDate)->format('M d, Y');
                    $oldTimeFormatted = substr($oldTime, 0, 5);
                    $message = "The session '{$session->subject} ({$session->session_code})' has been rescheduled from {$oldDateFormatted} at {$oldTimeFormatted} to {$sessionDate} at {$sessionTime}. Please check your schedule.";
                } else {
                    $message = "The session '{$session->subject} ({$session->session_code})' scheduled for {$sessionDate} at {$sessionTime} has been updated. Please review the changes.";
                }
                $type = 'session_modified';
                break;

            case 'cancelled':
                $title = 'Session Cancelled';
                $message = "Unfortunately, the session '{$session->subject} ({$session->session_code})' scheduled for {$sessionDate} at {$sessionTime} has been cancelled. We apologize for any inconvenience.";
                $type = 'session_cancelled';
                break;

            case 'rescheduled':
                $title = 'Session Rescheduled';
                $oldDateFormatted = \Carbon\Carbon::parse($oldDate)->format('M d, Y');
                $oldTimeFormatted = substr($oldTime, 0, 5);
                $message = "The session '{$session->subject} ({$session->session_code})' has been rescheduled from {$oldDateFormatted} at {$oldTimeFormatted} to {$sessionDate} at {$sessionTime}.";
                $type = 'session_rescheduled';
                break;

            default:
                return;
        }

        StudentNotification::notifyStudents(
            $enrolledStudents,
            $type,
            $title,
            $message,
            $session->id
        );
    }

    /**
     * Notify tutor of new session assignment
     */
    private function notifyTutorOfNewAssignment($session)
    {
        TutorNotification::notifyTutors(
            $session->tutor_id,
            'session_update',
            'New Session Assigned',
            "You have been assigned to a new tutoring session: '{$session->subject} ({$session->session_code})' scheduled for {$session->session_date->format('M d, Y')} at " . substr($session->session_time, 0, 5) . ". Please review the session details.",
            $session->id
        );
    }

    /**
     * Notify tutor of session cancellation
     */
    private function notifyTutorOfCancellation($session)
    {
        TutorNotification::notifySessionCancelled($session->tutor_id, $session);
    }

    /**
     * Notify tutor of session reschedule
     */
    private function notifyTutorOfReschedule($session, $oldDate, $oldTime)
    {
        TutorNotification::notifySessionRescheduled($session->tutor_id, $session, $oldDate, $oldTime);
    }

    /**
     * Notify tutor of session modification
     */
    private function notifyTutorOfModification($session)
    {
        TutorNotification::notifySessionModified($session->tutor_id, $session);
    }

    /**
     * Notify tutors of session reassignment
     */
    private function notifyTutorOfReassignment($session, $oldTutorId)
    {
        // Notify old tutor that session was reassigned
        TutorNotification::notifyTutors(
            $oldTutorId,
            'session_update',
            'Session Reassigned',
            "The session '{$session->subject} ({$session->session_code})' that was previously assigned to you has been reassigned to another tutor.",
            $session->id
        );

        // Notify new tutor of assignment
        $this->notifyTutorOfNewAssignment($session);
    }
}
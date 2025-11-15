<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Session;
use App\Models\Student;
use Carbon\Carbon;

class TutorSessionController extends Controller
{
    /**
     * Display combined sessions page with tabs
     */
    public function index(Request $request)
    {
        $tutor = Auth::guard('tutor')->user();
        $tab = $request->get('tab', 'my');
        $filter = $request->get('filter', 'all');

        if ($tab === 'my') {
            $query = Session::where('tutor_id', $tutor->id);

            // Apply time-based filters
            if ($filter === 'upcoming') {
                $query->where('status', 'Scheduled')
                      ->where('session_date', '>=', Carbon::today());
            } elseif ($filter === 'ongoing') {
                $query->where('status', 'Ongoing');
            } elseif ($filter === 'finished') {
                $query->where('status', 'Completed');
            }

            // Filter by status
            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            // Search
            if ($request->has('search') && $request->search !== '') {
                $query->where(function($q) use ($request) {
                    $q->where('subject', 'ILIKE', '%' . $request->search . '%')
                      ->orWhere('session_code', 'ILIKE', '%' . $request->search . '%');
                });
            }

            $mySessions = $query->orderBy('session_date', 'desc')
                ->orderBy('session_time', 'desc')
                ->paginate(10);

            return view('tutor.sessions.index', compact('mySessions', 'tutor', 'tab', 'filter'));
        } else {
            $query = Session::where('status', 'Scheduled')
                ->where('session_date', '>=', Carbon::today());

            // Apply time-based filters
            if ($filter === 'upcoming') {
                $query->where('session_date', '>=', Carbon::today());
            }

            // Search
            if ($request->has('search') && $request->search !== '') {
                $query->where(function($q) use ($request) {
                    $q->where('subject', 'ILIKE', '%' . $request->search . '%')
                      ->orWhere('session_code', 'ILIKE', '%' . $request->search . '%');
                });
            }

            $allSessions = $query->orderBy('session_date', 'asc')
                ->orderBy('session_time', 'asc')
                ->paginate(10);

            return view('tutor.sessions.index', compact('allSessions', 'tutor', 'tab', 'filter'));
        }
    }

    /**
     * Show session details with student applications
     */
    public function show($id)
    {
        $tutor = Auth::guard('tutor')->user();
        
        $session = Session::with(['students', 'tutor'])->findOrFail($id);

        if ($session->tutor_id !== $tutor->id) {
            abort(403, 'Unauthorized access to this session');
        }

        // Get pending student applications
        $pendingApplications = $session->students()
            ->wherePivot('attendance_status', 'Pending')
            ->get();

        return view('tutor.sessions.show', compact('session', 'tutor', 'pendingApplications'));
    }

    /**
     * Update session status
     */
    public function updateStatus(Request $request, $sessionId)
    {
        $tutor = Auth::guard('tutor')->user();
        $session = Session::findOrFail($sessionId);

        if ($session->tutor_id !== $tutor->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'status' => 'required|in:Ongoing,Completed'
        ]);

        // Validate status transitions
        if ($request->status === 'Ongoing' && $session->status !== 'Scheduled') {
            return redirect()->back()->with('error', 'Only scheduled sessions can be marked as ongoing');
        }

        if ($request->status === 'Completed' && $session->status !== 'Ongoing') {
            return redirect()->back()->with('error', 'Only ongoing sessions can be marked as completed');
        }

        $session->status = $request->status;
        $session->save();

        $statusMessage = $request->status === 'Ongoing' ? 'started' : 'completed';
        return redirect()->back()->with('success', "Session marked as {$statusMessage} successfully");
    }

    /**
     * Update attendance for a student
     */
    public function updateAttendance(Request $request, $sessionId, $studentId)
    {
        $tutor = Auth::guard('tutor')->user();
        
        $session = Session::findOrFail($sessionId);

        if ($session->tutor_id !== $tutor->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'attendance_status' => 'required|in:Pending,Present,Absent'
        ]);

        $session->students()->updateExistingPivot($studentId, [
            'attendance_status' => $request->attendance_status
        ]);

        return redirect()->back()->with('success', 'Attendance updated successfully');
    }

    /**
     * Manually add a student to the session
     */
    public function addStudent(Request $request, $sessionId)
    {
        $tutor = Auth::guard('tutor')->user();
        $session = Session::findOrFail($sessionId);

        if ($session->tutor_id !== $tutor->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'student_id' => 'required|exists:students,id'
        ]);

        // Check if session is full
        if ($session->isFull()) {
            return redirect()->back()->with('error', 'Session is already full');
        }

        // Check if student is already enrolled
        if ($session->students()->where('student_id', $request->student_id)->exists()) {
            return redirect()->back()->with('error', 'Student is already enrolled in this session');
        }

        $session->students()->attach($request->student_id, [
            'enrolled_at' => now(),
            'attendance_status' => 'Pending'
        ]);

        return redirect()->back()->with('success', 'Student added successfully');
    }

    /**
     * Approve student application
     */
    public function approveStudent(Request $request, $sessionId, $studentId)
    {
        $tutor = Auth::guard('tutor')->user();
        $session = Session::findOrFail($sessionId);

        if ($session->tutor_id !== $tutor->id) {
            abort(403, 'Unauthorized');
        }

        $session->students()->updateExistingPivot($studentId, [
            'attendance_status' => 'Present'
        ]);

        return redirect()->back()->with('success', 'Student application approved');
    }

    /**
     * Reject student application
     */
    public function rejectStudent(Request $request, $sessionId, $studentId)
    {
        $tutor = Auth::guard('tutor')->user();
        $session = Session::findOrFail($sessionId);

        if ($session->tutor_id !== $tutor->id) {
            abort(403, 'Unauthorized');
        }

        $session->students()->detach($studentId);

        return redirect()->back()->with('success', 'Student application rejected');
    }
}
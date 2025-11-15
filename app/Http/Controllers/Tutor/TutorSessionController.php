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
        $tab = $request->get('tab', 'my'); // Default to 'my' tab

        if ($tab === 'my') {
            // My Sessions - only sessions assigned to this tutor
            $query = Session::where('tutor_id', $tutor->id);

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

            return view('tutor.sessions.index', compact('mySessions', 'tutor', 'tab'));
        } else {
            // All Sessions - view all scheduled sessions
            $query = Session::where('status', 'Scheduled')
                ->where('session_date', '>=', Carbon::today());

            // Filter by year level if provided
            if ($request->has('year_level') && $request->year_level !== '') {
                $query->where('year_level', $request->year_level);
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

            return view('tutor.sessions.index', compact('allSessions', 'tutor', 'tab'));
        }
    }

    /**
     * Show session details
     */
    public function show($id)
    {
        $tutor = Auth::guard('tutor')->user();
        
        $session = Session::with(['students', 'tutor'])->findOrFail($id);

        // Ensure tutor can only view their own sessions
        if ($session->tutor_id !== $tutor->id) {
            abort(403, 'Unauthorized access to this session');
        }

        return view('tutor.sessions.show', compact('session', 'tutor'));
    }

    /**
     * Update attendance for a student
     */
    public function updateAttendance(Request $request, $sessionId, $studentId)
    {
        $tutor = Auth::guard('tutor')->user();
        
        $session = Session::findOrFail($sessionId);

        // Ensure tutor owns this session
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
}
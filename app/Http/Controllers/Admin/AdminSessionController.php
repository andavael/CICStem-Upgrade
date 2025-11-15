<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\Tutor;
use App\Models\Subject;
use Illuminate\Http\Request;

class AdminSessionController extends Controller
{
    /**
     * Display list of sessions
     */
    public function index(Request $request)
    {
        // Base query with tutor relation
        $query = Session::with('tutor');

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'upcoming') {
                $query->where('session_date', '>=', now());
            } elseif ($request->status === 'completed') {
                $query->where('session_date', '<', now());
            }
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
            'status' => 'required|in:Scheduled,Ongoing,Completed,Cancelled',
        ]);
        
        // Generate session code
        $validated['session_code'] = 'SES-' . strtoupper(substr(uniqid(), -8));

        // Auto-generate Google Meet link if not provided
        if (empty($validated['google_meet_link'])) {
            $uniqueId = substr(uniqid(), -10); // random string
            $validated['google_meet_link'] = "https://meet.google.com/{$uniqueId}";
        }       
        
        Session::create($validated);
        
        return redirect()->route('admin.sessions.index')
            ->with('success', 'Session created successfully');
    }
    
    /**
     * Display session details and enrollment list
     */
    public function show(Session $session)
    {
        $session->load(['tutor', 'students']);
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
            'google_meet_link' => 'required|url',
            'description' => 'nullable|string',
            'status' => 'required|in:Scheduled,Ongoing,Completed,Cancelled',
        ]);
        
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
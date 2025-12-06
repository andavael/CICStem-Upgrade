<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use App\Models\StudentNotification;
use App\Models\Student;

class TutorAnnouncementController extends Controller
{
    /**
     * Display announcements
     */
    public function index()
    {
        $tutor = Auth::guard('tutor')->user();
        
        $announcements = Announcement::where(function($query) use ($tutor) {
                // Show announcements targeted to tutors or all
                $query->where('target_audience', 'All')
                      ->orWhere('target_audience', 'Tutors');
            })
            ->whereNull('archived_at')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tutor.announcements.index', compact('announcements', 'tutor'));
    }

    /**
     * Show form to create new announcement
     */
    public function create()
    {
        return view('tutor.announcements.create');
    }

    /**
     * Store new announcement
     */
    public function store(Request $request)
    {
        $tutor = Auth::guard('tutor')->user();
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:General,Event,Maintenance,Important',
            'target_audience' => 'required|in:All,Students,Tutors',
            'priority' => 'required|in:Normal,High,Urgent',
        ]);
        
        // Add the tutor ID to track who created it
        $validated['created_by_tutor_id'] = $tutor->id;
        
        $announcement = Announcement::create($validated);
        
        // Notify students if priority is Urgent and audience includes Students
        if ($validated['priority'] === 'Urgent' && in_array($validated['target_audience'], ['All', 'Students'])) {
            $this->notifyStudentsOfUrgentAnnouncement($announcement);
        }
        
        return redirect()->route('tutor.announcements.index')
            ->with('success', 'Announcement posted successfully');
    }

    /**
     * Show form to edit announcement
     */
    public function edit($id)
    {
        $tutor = Auth::guard('tutor')->user();
        $announcement = Announcement::findOrFail($id);
        
        // Only allow editing own announcements
        if ($announcement->created_by_tutor_id !== $tutor->id) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('tutor.announcements.edit', compact('announcement'));
    }

    /**
     * Update announcement
     */
    public function update(Request $request, $id)
    {
        $tutor = Auth::guard('tutor')->user();
        $announcement = Announcement::findOrFail($id);
        
        // Only allow updating own announcements
        if ($announcement->created_by_tutor_id !== $tutor->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $oldPriority = $announcement->priority;
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:General,Event,Maintenance,Important',
            'target_audience' => 'required|in:All,Students,Tutors',
            'priority' => 'required|in:Normal,High,Urgent',
        ]);
        
        $announcement->update($validated);
        
        // Notify students if priority was changed to Urgent and audience includes Students
        if ($validated['priority'] === 'Urgent' && 
            $oldPriority !== 'Urgent' && 
            in_array($validated['target_audience'], ['All', 'Students'])) {
            $this->notifyStudentsOfUrgentAnnouncement($announcement);
        }
        
        return redirect()->route('tutor.announcements.index')
            ->with('success', 'Announcement updated successfully');
    }

    /**
     * Notify all students about urgent announcements
     */
    private function notifyStudentsOfUrgentAnnouncement($announcement)
    {
        // Get all active students
        $studentIds = Student::where('status', 'Active')->pluck('id')->toArray();
        
        if (empty($studentIds)) {
            return;
        }

        // Truncate content if too long
        $message = strlen($announcement->content) > 200 
            ? substr($announcement->content, 0, 200) . '...' 
            : $announcement->content;

        StudentNotification::notifyStudents(
            $studentIds,
            'urgent_announcement',
            'URGENT: ' . $announcement->title,  // ⭐ REMOVED EMOJI
            $message,
            $announcement->id
        );
    }
}
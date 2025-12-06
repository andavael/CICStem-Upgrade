<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Models\StudentNotification;
use App\Models\Student;
use App\Models\TutorNotification;
use App\Models\Tutor;

class AdminAnnouncementController extends Controller
{
    /**
     * Display list of announcements
     */
    public function index(Request $request)
    {
        $query = Announcement::query();
        
        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->whereNull('archived_at');
            } elseif ($request->status === 'archived') {
                $query->whereNotNull('archived_at');
            }
        } else {
            // Default: show only active announcements
            $query->whereNull('archived_at');
        }
        
        $announcements = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.announcements.index', compact('announcements'));
    }
    
    /**
     * Show form to create new announcement
     */
    public function create()
    {
        return view('admin.announcements.create');
    }
    
    /**
     * Store new announcement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:General,Event,Maintenance,Important',
            'target_audience' => 'required|in:All,Students,Tutors',
            'priority' => 'required|in:Normal,High,Urgent',
        ]);
        
        $announcement = Announcement::create($validated);
        
        // Notify users if priority is Urgent
        if ($validated['priority'] === 'Urgent') {
            // Notify students if audience includes Students
            if (in_array($validated['target_audience'], ['All', 'Students'])) {
                $this->notifyStudentsOfUrgentAnnouncement($announcement);
            }
            
            // Notify tutors if audience includes Tutors
            if (in_array($validated['target_audience'], ['All', 'Tutors'])) {
                $this->notifyTutorsOfUrgentAnnouncement($announcement);
            }
        }
        
        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement posted successfully');
    }
    
    /**
     * Show form to edit announcement
     */
    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }
    
    /**
     * Update announcement
     */
    public function update(Request $request, Announcement $announcement)
    {
        $oldPriority = $announcement->priority;
        $oldAudience = $announcement->target_audience;
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:General,Event,Maintenance,Important',
            'target_audience' => 'required|in:All,Students,Tutors',
            'priority' => 'required|in:Normal,High,Urgent',
        ]);
        
        $announcement->update($validated);
        
        // Notify users if priority was changed to Urgent
        if ($validated['priority'] === 'Urgent' && $oldPriority !== 'Urgent') {
            // Notify students if audience includes Students
            if (in_array($validated['target_audience'], ['All', 'Students'])) {
                $this->notifyStudentsOfUrgentAnnouncement($announcement);
            }
            
            // Notify tutors if audience includes Tutors
            if (in_array($validated['target_audience'], ['All', 'Tutors'])) {
                $this->notifyTutorsOfUrgentAnnouncement($announcement);
            }
        }
        
        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement updated successfully');
    }
    
    /**
     * Delete announcement
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        
        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement deleted successfully');
    }
    
    /**
     * Archive announcement
     */
    public function archive(Announcement $announcement)
    {
        $announcement->update(['archived_at' => now()]);
        
        return redirect()->back()
            ->with('success', 'Announcement archived successfully');
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
            'URGENT: ' . $announcement->title,
            $message,
            $announcement->id
        );
    }
    
    /**
     * Notify all tutors about urgent announcements
     */
    private function notifyTutorsOfUrgentAnnouncement($announcement)
    {
        // Get all active and approved tutors
        $tutorIds = Tutor::where('status', 'Active')
                        ->where('is_approved', true)
                        ->pluck('id')
                        ->toArray();
        
        if (empty($tutorIds)) {
            return;
        }

        TutorNotification::notifyUrgentAnnouncement($tutorIds, $announcement);
    }
}
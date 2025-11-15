<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;

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
        
        Announcement::create($validated);
        
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
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:General,Event,Maintenance,Important',
            'target_audience' => 'required|in:All,Students,Tutors',
            'priority' => 'required|in:Normal,High,Urgent',
        ]);
        
        $announcement->update($validated);
        
        return redirect()->route('tutor.announcements.index')
            ->with('success', 'Announcement updated successfully');
    }


}
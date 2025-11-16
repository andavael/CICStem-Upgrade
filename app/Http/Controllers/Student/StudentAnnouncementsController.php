<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;

class StudentAnnouncementsController extends Controller
{
    /**
     * Display announcements for students
     */
    public function index(Request $request)
    {
        $student = Auth::guard('student')->user();
        
        // Base query - only show announcements for students or all
        $query = Announcement::where(function($q) {
                $q->where('target_audience', 'All')
                  ->orWhere('target_audience', 'Students');
            })
            ->whereNull('archived_at');

        // Apply category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Apply priority filter
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $announcements = $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Get filter options
        $categories = ['General', 'Event', 'Maintenance', 'Important'];
        $priorities = ['Normal', 'High', 'Urgent'];

        return view('student.announcements', compact(
            'announcements',
            'student',
            'categories',
            'priorities'
        ));
    }

    /**
     * Show single announcement
     */
    public function show($id)
    {
        $student = Auth::guard('student')->user();
        
        $announcement = Announcement::where(function($q) {
                $q->where('target_audience', 'All')
                  ->orWhere('target_audience', 'Students');
            })
            ->whereNull('archived_at')
            ->findOrFail($id);

        return view('student.announcement_detail', compact('announcement', 'student'));
    }
}
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
        
        $announcements = Announcement::where(function($query) {
                $query->where('target_audience', 'All')
                      ->orWhere('target_audience', 'Tutors');
            })
            ->whereNull('archived_at')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tutor.announcements', compact('announcements', 'tutor'));
    }
}
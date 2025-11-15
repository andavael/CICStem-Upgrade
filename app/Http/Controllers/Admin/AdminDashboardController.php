<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Tutor;
use App\Models\Session;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Display admin dashboard with statistics
     */
    public function index()
    {
        // ------------------------------
        // Statistics
        // ------------------------------
        $stats = [
            'total_students' => Student::count(),
            'active_students' => Student::where('status', 'Active')->count(),
            'total_tutors' => Tutor::count(),
            'approved_tutors' => Tutor::where('is_approved', true)->count(),
            'pending_tutors' => Tutor::where('is_approved', false)->count(),
            'total_sessions' => Session::count(),
            'upcoming_sessions' => Session::where('session_date', '>=', now())->count(),
            'completed_sessions' => Session::where('session_date', '<', now())->count(),
            'total_announcements' => Announcement::count(),
        ];

        // ------------------------------
        // Popular subjects (top 5)
        // ------------------------------
        $popularSubjects = Session::select('subject', DB::raw('COUNT(*) AS session_count'))
            ->groupBy('subject')
            ->orderByDesc('session_count')
            ->limit(5)
            ->get();

        // ------------------------------
        // Active tutors with session count
        // ------------------------------
        $activeTutors = Tutor::where('is_approved', true)
            ->where('status', 'Active')
            ->withCount(['sessions as sessions_count' => function ($query) {
                $query->where('status', 'Scheduled');
            }])
            ->orderByDesc('sessions_count')
            ->limit(5)
            ->get();

        // ------------------------------
        // Recent sessions
        // ------------------------------
        $recentSessions = Session::with('tutor')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // ------------------------------
        // Monthly session chart (PostgreSQL-compatible)
        // ------------------------------
        $monthlySessions = Session::select(
                DB::raw('EXTRACT(MONTH FROM session_date) AS month'),
                DB::raw('COUNT(*) AS count')
            )
            ->whereRaw('EXTRACT(YEAR FROM session_date) = ?', [date('Y')])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // ------------------------------
        // Student participation by year level
        // ------------------------------
        $studentsByYear = Student::select('year_level', DB::raw('COUNT(*) AS count'))
            ->where('status', 'Active')
            ->groupBy('year_level')
            ->get();

        return view('admin.dashboard.index', compact(
            'stats',
            'popularSubjects',
            'activeTutors',
            'recentSessions',
            'monthlySessions',
            'studentsByYear'
        ));
    }

    /**
     * Show form to create a new session
     */
    public function create()
    {
        // Only active and approved tutors
        $tutors = Tutor::where('is_approved', true)
                       ->where('status', 'Active')
                       ->get();

        return view('admin.sessions.create', compact('tutors'));
    }

    /**
     * Store a newly created session
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'session_date' => 'required|date',
            'session_time' => 'required',
            'tutor_id' => 'required|exists:tutors,id',
            'year_level' => 'required|string',
            'capacity' => 'required|integer|min:1|max:100',
            'status' => 'required|in:Scheduled,Ongoing,Completed,Cancelled',
            'google_meet_link' => 'required|url',
            'description' => 'nullable|string',
        ]);

        Session::create($request->all());

        return redirect()->route('admin.sessions.index')->with('success', 'Session created successfully.');
    }

    /**
     * Export reports
     */
    public function export(Request $request)
    {
        $format = $request->input('format', 'csv');

        $data = [
            'students' => Student::all(),
            'tutors' => Tutor::all(),
            'sessions' => Session::with('tutor')->get(),
        ];

        if ($format === 'csv') {
            return $this->exportCsv($data);
        }

        if ($format === 'pdf') {
            return $this->exportPdf($data);
        }

        return redirect()->back()->with('error', 'Invalid export format');
    }

    /**
     * Export data as CSV
     */
    private function exportCsv($data)
    {
        $filename = 'cicstem_report_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Students
            fputcsv($file, ['STUDENTS REPORT']);
            fputcsv($file, ['SR Code', 'Name', 'Email', 'Year Level', 'Course', 'Status']);
            foreach ($data['students'] as $student) {
                fputcsv($file, [
                    $student->sr_code,
                    $student->full_name,
                    $student->email,
                    $student->year_level,
                    $student->course_program,
                    $student->status,
                ]);
            }
            fputcsv($file, []);

            // Tutors
            fputcsv($file, ['TUTORS REPORT']);
            fputcsv($file, ['SR Code', 'Name', 'Email', 'Year Level', 'GWA', 'Status', 'Approved']);
            foreach ($data['tutors'] as $tutor) {
                fputcsv($file, [
                    $tutor->sr_code,
                    $tutor->full_name,
                    $tutor->email,
                    $tutor->year_level,
                    $tutor->gwa,
                    $tutor->status,
                    $tutor->is_approved ? 'Yes' : 'No',
                ]);
            }
            fputcsv($file, []);

            // Sessions
            fputcsv($file, ['SESSIONS REPORT']);
            fputcsv($file, ['Subject', 'Date', 'Time', 'Tutor', 'Capacity', 'Year Level']);
            foreach ($data['sessions'] as $session) {
                fputcsv($file, [
                    $session->subject,
                    $session->session_date,
                    $session->session_time,
                    $session->tutor ? $session->tutor->full_name : 'N/A',
                    $session->capacity,
                    $session->year_level,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export PDF placeholder
     */
    private function exportPdf($data)
    {
        return redirect()->back()->with('info', 'PDF export feature coming soon');
    }
}

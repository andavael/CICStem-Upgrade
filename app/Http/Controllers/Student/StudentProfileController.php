<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class StudentProfileController extends Controller
{
    /**
     * Show profile page
     */
    public function index()
    {
        $student = Auth::guard('student')->user();
        
        // Get enrollment history
        $enrollmentHistory = $student->sessions()
            ->with('tutor')
            ->orderBy('session_date', 'desc')
            ->limit(10)
            ->get();

        // Statistics
        $totalEnrolled = $student->sessions()->count();
        $attended = $student->sessions()
            ->wherePivot('attendance_status', 'Present')
            ->count();
        $attendanceRate = $totalEnrolled > 0 
            ? round(($attended / $totalEnrolled) * 100, 1) 
            : 0;

        return view('student.profile', compact(
            'student',
            'enrollmentHistory',
            'totalEnrolled',
            'attended',
            'attendanceRate'
        ));
    }

    /**
     * Update profile information
     */
    public function update(Request $request)
    {
        $student = Auth::guard('student')->user();

        $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'course_program' => 'required|string|max:255',
        ]);

        $student->update([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'course_program' => $request->course_program,
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $student = Auth::guard('student')->user();

        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $student->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        // Update password
        $student->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->back()->with('success', 'Password updated successfully');
    }
}
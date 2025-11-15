<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class TutorProfileController extends Controller
{
    /**
     * Show profile page
     */
    public function index()
    {
        $tutor = Auth::guard('tutor')->user();
        return view('tutor.profile', compact('tutor'));
    }

    /**
     * Update profile information
     */
    public function update(Request $request)
    {
        $tutor = Auth::guard('tutor')->user();

        $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'course_program' => 'required|string|max:255',
            'gwa' => 'required|numeric|min:1.00|max:5.00',
            'tutor_level_preference' => 'required|in:First-Year Students,Second-Year Students',
        ]);

        $tutor->update([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'course_program' => $request->course_program,
            'gwa' => $request->gwa,
            'tutor_level_preference' => $request->tutor_level_preference,
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $tutor = Auth::guard('tutor')->user();

        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $tutor->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        // Update password
        $tutor->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->back()->with('success', 'Password updated successfully');
    }

    /**
     * Update resume
     */
    public function updateResume(Request $request)
    {
        $tutor = Auth::guard('tutor')->user();

        $request->validate([
            'resume' => 'required|file|mimes:pdf|max:5120', // 5MB max
        ]);

        // Delete old resume if exists
        if ($tutor->resume_path && \Storage::disk('public')->exists($tutor->resume_path)) {
            \Storage::disk('public')->delete($tutor->resume_path);
        }

        // Store new resume
        $resumePath = $request->file('resume')->store('resumes', 'public');

        $tutor->update([
            'resume_path' => $resumePath
        ]);

        return redirect()->back()->with('success', 'Resume updated successfully');
    }
}
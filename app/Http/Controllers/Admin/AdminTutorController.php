<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tutor;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminTutorController extends Controller
{
    /**
     * Display list of tutors
     */
    public function index()
    {
        $tutors = Tutor::with('subjects')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.users.tutors', compact('tutors'));
    }
    
    /**
     * Show form to create new tutor
     */
    public function create()
    {
        $subjects = Subject::orderBy('name')->get();
        return view('admin.users.create_tutor', compact('subjects'));
    }
    
    /**
     * Store new tutor
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'sr_code' => 'required|string|unique:tutors,sr_code|regex:/^\d{2}-\d{5}$/',
            'email' => 'required|email|unique:tutors,email|regex:/^\d{2}-\d{5}@g\.batstate-u\.edu\.ph$/',
            'password' => 'required|string|min:8|confirmed',
            'year_level' => 'required|in:1st Year,2nd Year,3rd Year,4th Year',
            'course_program' => 'required|string|max:255',
            'tutor_level_preference' => 'required|in:Lower Year,Same Year,All',
            'gwa' => 'required|numeric|min:1.00|max:5.00',
            'resume' => 'nullable|file|mimes:pdf|max:5120',
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'exists:subjects,id',
            'status' => 'required|in:Active,Inactive',
            'is_approved' => 'required|boolean',
        ]);
        
        // Handle resume upload
        if ($request->hasFile('resume')) {
            $validated['resume_path'] = $request->file('resume')->store('resumes', 'public');
        }
        
        $validated['password'] = Hash::make($validated['password']);
        $validated['terms_accepted'] = true;
        $validated['terms_accepted_at'] = now();
        
        $subjects = $validated['subjects'];
        unset($validated['subjects']);
        
        $tutor = Tutor::create($validated);
        
        // Attach subjects
        $tutor->subjects()->attach($subjects, ['proficiency_level' => 'Intermediate']);
        
        return redirect()->route('admin.users.index', ['tab' => 'tutors'])
            ->with('success', 'Tutor created successfully');
    }
    
    /**
     * Display tutor profile
     */
    public function show(Tutor $tutor)
    {
        $tutor->load('subjects');
        return view('admin.users.show', compact('tutor'));
    }
    
    /**
     * Show form to edit tutor
     */
    public function edit(Tutor $tutor)
    {
        $subjects = Subject::orderBy('name')->get();
        $tutor->load('subjects');
        return view('admin.users.edit_tutor', compact('tutor', 'subjects'));
    }
    
    /**
     * Update tutor
     */
    public function update(Request $request, Tutor $tutor)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'sr_code' => [
                'required',
                'string',
                'regex:/^\d{2}-\d{5}$/',
                Rule::unique('tutors', 'sr_code')->ignore($tutor->id)
            ],
            'email' => [
                'required',
                'email',
                'regex:/^\d{2}-\d{5}@g\.batstate-u\.edu\.ph$/',
                Rule::unique('tutors', 'email')->ignore($tutor->id)
            ],
            'year_level' => 'required|in:1st Year,2nd Year,3rd Year,4th Year',
            'course_program' => 'required|string|max:255',
            'tutor_level_preference' => 'required|in:Lower Year,Same Year,All',
            'gwa' => 'required|numeric|min:1.00|max:5.00',
            'resume' => 'nullable|file|mimes:pdf|max:5120',
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'exists:subjects,id',
            'status' => 'required|in:Active,Inactive',
            'is_approved' => 'required|boolean',
        ]);
        
        // Handle resume upload
        if ($request->hasFile('resume')) {
            // Delete old resume
            if ($tutor->resume_path) {
                Storage::disk('public')->delete($tutor->resume_path);
            }
            $validated['resume_path'] = $request->file('resume')->store('resumes', 'public');
        }
        
        // Update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed',
            ]);
            $validated['password'] = Hash::make($request->password);
        }
        
        $subjects = $validated['subjects'];
        unset($validated['subjects']);
        
        $tutor->update($validated);
        
        // Sync subjects
        $tutor->subjects()->sync($subjects);
        
        return redirect()->route('admin.tutors.show', $tutor)
            ->with('success', 'Tutor updated successfully');
    }
    
    /**
     * Delete tutor
     */
    public function destroy(Tutor $tutor)
    {
        // Delete resume file
        if ($tutor->resume_path) {
            Storage::disk('public')->delete($tutor->resume_path);
        }
        
        $tutor->delete();
        
        return redirect()->route('admin.users.index', ['tab' => 'tutors'])
            ->with('success', 'Tutor deleted successfully');
    }
    
    /**
     * Approve tutor application
     */
    public function approve(Tutor $tutor)
    {
        $tutor->update([
            'is_approved' => true,
            'status' => 'Active',
        ]);
        
        return redirect()->back()
            ->with('success', 'Tutor application approved');
    }
    
    /**
     * Reject tutor application
     */
    public function reject(Tutor $tutor)
    {
        $tutor->update([
            'is_approved' => false,
            'status' => 'Inactive',
        ]);
        
        return redirect()->back()
            ->with('success', 'Tutor application rejected');
    }
    
    /**
     * Toggle tutor status (Active/Inactive)
     */
    public function toggleStatus(Tutor $tutor)
    {
        $tutor->status = $tutor->status === 'Active' ? 'Inactive' : 'Active';
        $tutor->save();
        
        return redirect()->back()
            ->with('success', 'Tutor status updated to ' . $tutor->status);
    }

    public function downloadResume(Tutor $tutor)
    {
        if (!$tutor->resume_path || !Storage::disk('public')->exists($tutor->resume_path)) {
            abort(404, 'Resume not found');
        }

        return Storage::disk('public')->download($tutor->resume_path, $tutor->full_name . '_Resume.pdf');
    }
}
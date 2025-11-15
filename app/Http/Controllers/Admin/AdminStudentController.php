<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Tutor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminStudentController extends Controller
{
    /**
     * Display combined users page (Students and Tutors tabs)
     */
    public function usersIndex(Request $request)
    {
        $tab = $request->input('tab', 'students');
        
        if ($tab === 'tutors') {
            $tutors = Tutor::orderBy('created_at', 'desc')->paginate(15);
            return view('admin.users.index', compact('tutors', 'tab'));
        }
        
        $students = Student::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.users.index', compact('students', 'tab'));
    }
    
    /**
     * Display list of students
     */
    public function index()
    {
        $students = Student::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.users.students', compact('students'));
    }
    
    /**
     * Show form to create new student
     */
    public function create()
    {
        return view('admin.users.create_student');
    }
    
    /**
     * Store new student
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'sr_code' => 'required|string|unique:students,sr_code|regex:/^\d{2}-\d{5}$/',
            'email' => 'required|email|unique:students,email|regex:/^\d{2}-\d{5}@g\.batstate-u\.edu\.ph$/',
            'password' => 'required|string|min:8|confirmed',
            'year_level' => 'required|in:1st Year,2nd Year,3rd Year,4th Year',
            'course_program' => 'required|string|max:255',
            'status' => 'required|in:Active,Inactive',
        ]);
        
        $validated['password'] = Hash::make($validated['password']);
        $validated['terms_accepted'] = true;
        $validated['terms_accepted_at'] = now();
        
        Student::create($validated);
        
        return redirect()->route('admin.users.index', ['tab' => 'students'])
            ->with('success', 'Student created successfully');
    }
    
    /**
     * Display student profile
     */
    public function show(Student $student)
    {
        return view('admin.users.show', compact('student'));
    }
    
    /**
     * Show form to edit student
     */
    public function edit(Student $student)
    {
        return view('admin.users.edit_student', compact('student'));
    }
    
    /**
     * Update student
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'sr_code' => [
                'required',
                'string',
                'regex:/^\d{2}-\d{5}$/',
                Rule::unique('students', 'sr_code')->ignore($student->id)
            ],
            'email' => [
                'required',
                'email',
                'regex:/^\d{2}-\d{5}@g\.batstate-u\.edu\.ph$/',
                Rule::unique('students', 'email')->ignore($student->id)
            ],
            'year_level' => 'required|in:1st Year,2nd Year,3rd Year,4th Year',
            'course_program' => 'required|string|max:255',
            'status' => 'required|in:Active,Inactive',
        ]);
        
        // Update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed',
            ]);
            $validated['password'] = Hash::make($request->password);
        }
        
        $student->update($validated);
        
        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Student updated successfully');
    }
    
    /**
     * Delete student
     */
    public function destroy(Student $student)
    {
        $student->delete();
        
        return redirect()->route('admin.users.index', ['tab' => 'students'])
            ->with('success', 'Student deleted successfully');
    }
    
    /**
     * Toggle student status (Active/Inactive)
     */
    public function toggleStatus(Student $student)
    {
        $student->status = $student->status === 'Active' ? 'Inactive' : 'Active';
        $student->save();
        
        return redirect()->back()
            ->with('success', 'Student status updated to ' . $student->status);
    }
}
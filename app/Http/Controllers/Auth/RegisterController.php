<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Tutor;
use App\Models\Subject;
use App\Models\TermsAcceptance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        // Get subjects for first and second year students only
        $firstYearSubjects = Subject::orderBy('name')
            ->get([
                'id',
                'name as subject_name',
                'code as subject_code'
            ]);

        $secondYearSubjects = Subject::orderBy('name')
            ->get([
                'id',
                'name as subject_name',
                'code as subject_code'
            ]);

        return view('auth.register', compact('firstYearSubjects', 'secondYearSubjects'));
    }

    /**
     * Handle registration submission
     */
    public function register(Request $request)
    {
        // Validate the request
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation', 'resume'));
        }

        try {
            DB::beginTransaction();

            // Create the user based on type
            if ($request->user_type === 'student') {
                $user = $this->createStudent($request->all());
            } else {
                $user = $this->createTutor($request->all());
            }

            // Record terms acceptance
            $this->recordTermsAcceptance($user, $request);

            DB::commit();

            // Redirect based on user type
            if ($request->user_type === 'tutor') {
                return redirect()->route('login')
                    ->with('success', 'Registration successful! Your account is pending admin approval. You will be notified once approved.');
            } else {
                return redirect()->route('login')
                    ->with('success', 'Registration successful! You can now login to your account.');
            }

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Registration failed. Please try again. Error: ' . $e->getMessage())
                ->withInput($request->except('password', 'password_confirmation', 'resume'));
        }
    }

    /**
     * Get a validator for an incoming registration request
     */
    protected function validator(array $data)
    {
        $rules = [
            'user_type' => ['required', 'in:student,tutor'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],

            // SR Code validated manually in after() for dynamic rules
            'sr_code' => ['required'],

            'email' => ['required', 'email', 'regex:/^\d{2}-\d{5}@g\.batstate-u\.edu\.ph$/'],
            'year_level' => ['required'],
            'course_program' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms_accepted' => ['required', 'accepted'],
        ];

        // Additional validation for tutors
        if (isset($data['user_type']) && $data['user_type'] === 'tutor') {
            $rules['tutor_level_preference'] = ['required', 'in:First-Year Students,Second-Year Students'];
            $rules['gwa'] = ['required', 'numeric', 'min:1.00', 'max:5.00'];
            $rules['resume'] = ['required', 'file', 'mimes:pdf', 'max:5120']; // 5MB max
        }

        $messages = [
            'email.regex' => 'Email must be a valid G-Suite email (@g.batstate-u.edu.ph)',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'terms_accepted.accepted' => 'You must accept the terms and conditions',
            'gwa.required' => 'GWA is required for tutors',
            'gwa.min' => 'GWA must be between 1.00 and 5.00',
            'gwa.max' => 'GWA must be between 1.00 and 5.00',
            'resume.required' => 'Resume is required for tutors',
            'resume.mimes' => 'Resume must be a PDF file',
            'resume.max' => 'Resume file size must not exceed 5MB',
        ];

        $validator = Validator::make($data, $rules, $messages);

        // Custom validation
        $validator->after(function ($validator) use ($data) {

            /**
             * ----------------------------------------------
             * SR CODE RULES
             * Option 1:
             * Students → 24 or higher
             * Tutors   → 23 or lower
             * ----------------------------------------------
             */

            if (isset($data['sr_code']) && isset($data['user_type'])) {

                // Validate format first
                if (!preg_match('/^\d{2}-\d{5}$/', $data['sr_code'])) {
                    $validator->errors()->add('sr_code', 'SR Code must follow the format YY-XXXXX.');
                    return;
                }

                $yearPrefix = (int) substr($data['sr_code'], 0, 2);

                if ($data['user_type'] === 'student') {
                    // Students must start with 24+
                    if ($yearPrefix < 24) {
                        $validator->errors()->add('sr_code', 'Students must have an SR Code starting with 24 or higher.');
                    }
                }

                if ($data['user_type'] === 'tutor') {
                    // Tutors must be 23 or lower
                    if ($yearPrefix > 23) {
                        $validator->errors()->add('sr_code', 'Tutors must have an SR Code starting with 23 or lower.');
                    }
                }
            }

            // Email must match SR code
            if (isset($data['sr_code']) && isset($data['email'])) {
                $expectedEmail = $data['sr_code'] . '@g.batstate-u.edu.ph';
                if ($data['email'] !== $expectedEmail) {
                    $validator->errors()->add('email', 'Email must match your SR Code: ' . $expectedEmail);
                }
            }

            // Validate year level matches user type
            if (isset($data['user_type']) && isset($data['year_level'])) {

                if ($data['user_type'] === 'student') {
                    if (!in_array($data['year_level'], ['First Year', 'Second Year'])) {
                        $validator->errors()->add('year_level', 'Students must be First or Second Year');
                    }
                }

                if ($data['user_type'] === 'tutor') {
                    if (!in_array($data['year_level'], ['Third Year', 'Fourth Year or above'])) {
                        $validator->errors()->add('year_level', 'Tutors must be Third Year or above');
                    }
                }
            }

            // Check for duplicate SR code across both tables
            if (isset($data['sr_code'])) {
                $studentExists = Student::where('sr_code', $data['sr_code'])->exists();
                $tutorExists = Tutor::where('sr_code', $data['sr_code'])->exists();

                if ($studentExists || $tutorExists) {
                    $validator->errors()->add('sr_code', 'This SR Code is already registered');
                }
            }

            // Check for duplicate email across both tables
            if (isset($data['email'])) {
                $studentExists = Student::where('email', $data['email'])->exists();
                $tutorExists = Tutor::where('email', $data['email'])->exists();

                if ($studentExists || $tutorExists) {
                    $validator->errors()->add('email', 'This email is already registered');
                }
            }
        });

        return $validator;
    }

    /**
     * Create a new student instance
     */
    protected function createStudent(array $data)
    {
        return Student::create([
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'sr_code' => $data['sr_code'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'year_level' => $data['year_level'],
            'course_program' => $data['course_program'],
            'status' => 'Active',
            'terms_accepted' => true,
            'terms_accepted_at' => now(),
        ]);
    }

    /**
     * Create a new tutor instance
     */
    protected function createTutor(array $data)
    {
        // Handle resume upload
        $resumePath = null;
        if (isset($data['resume']) && $data['resume'] instanceof \Illuminate\Http\UploadedFile) {
            $resumePath = $data['resume']->store('resumes', 'public');
        }

        return Tutor::create([
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'sr_code' => $data['sr_code'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'year_level' => $data['year_level'],
            'course_program' => $data['course_program'],
            'tutor_level_preference' => $data['tutor_level_preference'],
            'gwa' => $data['gwa'],
            'resume_path' => $resumePath,
            'status' => 'Pending',
            'is_approved' => false,
            'terms_accepted' => true,
            'terms_accepted_at' => now(),
        ]);
    }

    /**
     * Record terms acceptance
     */
    protected function recordTermsAcceptance($user, Request $request)
    {
        TermsAcceptance::create([
            'user_type' => $request->user_type,
            'user_id' => $user->id,
            'terms_version' => '1.0',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'accepted_at' => now(),
        ]);
    }
}

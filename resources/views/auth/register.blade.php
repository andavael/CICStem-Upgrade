<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - CICStem</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #004DA9 0%, #0066CC 100%);
        }
        .file-upload-wrapper {
            position: relative;
            margin-bottom: 10px;
        }
        .file-upload-input {
            display: none;
        }
        .file-upload-label {
            display: block;
            padding: 12px 16px;
            border: 2px dashed #DEE2E6;
            border-radius: 8px;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        .file-upload-label:hover {
            border-color: #2d5f8d;
            background: #e3f2fd;
        }
        .file-name-display {
            font-size: 14px;
            color: #495057;
            margin-top: 8px;
            padding: 8px 12px;
            background: #e9ecef;
            border-radius: 6px;
            display: none;
        }
        .file-name-display.show {
            display: block;
        }
        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }
        .error-message.show {
            display: block;
        }
        input.error, select.error {
            border-color: #dc3545;
        }
        input[type="number"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #DEE2E6;
            border-radius: 8px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            background: white;
            color: #212529;
        }
        input[type="number"]:focus {
            outline: none;
            border-color: #2d5f8d;
            box-shadow: 0 0 0 3px rgba(45, 95, 141, 0.1);
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-header">
                <img src="/assets/logo_blue.png" style="width:75px;">

                <h1>SIGN UP</h1>
                <p>Register your CICStem account</p>
            </div>

            <div class="progress-bar">
                <div class="progress-step active" id="progress-1">
                    <div class="progress-step-circle">1</div>
                    <div class="progress-step-label">User Type</div>
                </div>
                <div class="progress-step" id="progress-2">
                    <div class="progress-step-circle">2</div>
                    <div class="progress-step-label">Basic Info</div>
                </div>
                <div class="progress-step" id="progress-3">
                    <div class="progress-step-circle">3</div>
                    <div class="progress-step-label" id="step3-label">Account Setup</div>
                </div>
            </div>

            <div class="auth-content">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form id="registrationForm" method="POST" action="{{ route('register.submit') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- STEP 1: User Type Selection -->
                    <div class="form-step active" data-step="1">
                        <h2 style="margin-bottom: 20px; color: #212529;">Choose Your Role</h2>
                        
                        <div class="user-type-selection">
                            <div class="user-type-card" onclick="selectUserType('student')">
                                <input type="radio" name="user_type" id="student" value="student" {{ old('user_type') == 'student' ? 'checked' : '' }}>
                                <div class="user-type-icon">🎓</div>
                                <div class="user-type-title">Student</div>
                                <div class="user-type-desc">First or Second Year</div>
                            </div>

                            <div class="user-type-card" onclick="selectUserType('tutor')">
                                <input type="radio" name="user_type" id="tutor" value="tutor" {{ old('user_type') == 'tutor' ? 'checked' : '' }}>
                                <div class="user-type-icon">👨‍🏫</div>
                                <div class="user-type-title">Tutor</div>
                                <div class="user-type-desc">Third Year or above</div>
                            </div>
                        </div>

                        <div class="error-message" id="userTypeError">
                            @error('user_type'){{ $message }}@enderror
                        </div>

                        <div class="button-group">
                            <button type="button" class="btn btn-primary" onclick="nextStep(1)">Continue</button>
                        </div>
                    </div>

                    <!-- STEP 2: Basic Information -->
                    <div class="form-step" data-step="2">
                        <h2 style="margin-bottom: 20px; color: #212529;">Basic Information</h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label>First Name <span class="required">*</span></label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required>
                                <div class="error-message" id="firstNameError">
                                    @error('first_name'){{ $message }}@enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Middle Name</label>
                                <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Last Name <span class="required">*</span></label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required>
                            <div class="error-message" id="lastNameError">
                                @error('last_name'){{ $message }}@enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label>SR Code <span class="required">*</span></label>
                            <input type="text" name="sr_code" id="sr_code" value="{{ old('sr_code') }}" placeholder="YY-XXXXX (e.g., 24-12345)" required>
                            <div class="error-message" id="srCodeError">
                                @error('sr_code'){{ $message }}@enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label>G-Suite Email <span class="required">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="YY-XXXXX@g.batstate-u.edu.ph" required>
                            <div class="error-message" id="emailError">
                                @error('email'){{ $message }}@enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Year Level <span class="required">*</span></label>
                                <select name="year_level" id="year_level" required>
                                    <option value="">Select Year Level</option>
                                    <option value="First Year" {{ old('year_level') == 'First Year' ? 'selected' : '' }}>First Year</option>
                                    <option value="Second Year" {{ old('year_level') == 'Second Year' ? 'selected' : '' }}>Second Year</option>
                                    <option value="Third Year" {{ old('year_level') == 'Third Year' ? 'selected' : '' }}>Third Year</option>
                                    <option value="Fourth Year or above" {{ old('year_level') == 'Fourth Year or above' ? 'selected' : '' }}>Fourth Year or above</option>
                                </select>
                                <div class="error-message" id="yearLevelError">
                                    @error('year_level'){{ $message }}@enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Course / Program <span class="required">*</span></label>
                                <select name="course_program" id="course_program" required>
                                    <option value="">Select Course</option>
                                    <option value="BS Computer Science" {{ old('course_program') == 'BS Computer Science' ? 'selected' : '' }}>BS Computer Science</option>
                                    <option value="BS Information Technology" {{ old('course_program') == 'BS Information Technology' ? 'selected' : '' }}>BS Information Technology</option>
                                </select>
                                <div class="error-message" id="courseProgramError">
                                    @error('course_program'){{ $message }}@enderror
                                </div>
                            </div>
                        </div>

                        <div class="button-group">
                            <button type="button" class="btn btn-secondary" onclick="prevStep(2)">Back</button>
                            <button type="button" class="btn btn-primary" onclick="nextStep(2)">Continue</button>
                        </div>
                    </div>

                    <!-- STEP 3: Tutoring Profile (Tutors Only) -->
                    <div class="form-step" data-step="3-tutor">
                        <h2 style="margin-bottom: 20px; color: #212529;">Tutoring Profile</h2>

                        <div class="form-group">
                            <label>Who would you like to tutor? <span class="required">*</span></label>
                            <div class="radio-group">
                                <div class="radio-item" onclick="selectTutorLevel('First-Year Students')">
                                    <input type="radio" name="tutor_level_preference" id="tutor_first" value="First-Year Students" {{ old('tutor_level_preference') == 'First-Year Students' ? 'checked' : '' }}>
                                    <label for="tutor_first">First-Year Students</label>
                                </div>
                                <div class="radio-item" onclick="selectTutorLevel('Second-Year Students')">
                                    <input type="radio" name="tutor_level_preference" id="tutor_second" value="Second-Year Students" {{ old('tutor_level_preference') == 'Second-Year Students' ? 'checked' : '' }}>
                                    <label for="tutor_second">Second-Year Students</label>
                                </div>
                            </div>
                            <div class="error-message" id="tutorLevelError">
                                @error('tutor_level_preference'){{ $message }}@enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label>GWA (General Weighted Average) <span class="required">*</span></label>
                            <input type="number" name="gwa" id="gwa" value="{{ old('gwa') }}" step="0.01" min="1.00" max="5.00" placeholder="e.g., 1.75">
                            <div class="error-message" id="gwaError">
                                @error('gwa'){{ $message }}@enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Resume <span class="required">*</span></label>
                            <div class="file-upload-wrapper">
                                <input type="file" name="resume" id="resume" class="file-upload-input" accept=".pdf">
                                <label for="resume" class="file-upload-label">
                                    📄 Click to upload resume (PDF only, max 5MB)
                                </label>
                                <div class="file-name-display" id="fileNameDisplay"></div>
                            </div>
                            <div class="error-message" id="resumeError">
                                @error('resume'){{ $message }}@enderror
                            </div>
                        </div>

                        <div class="button-group">
                            <button type="button" class="btn btn-secondary" onclick="prevStep(3)">Back</button>
                            <button type="button" class="btn btn-primary" onclick="nextStep(3)">Continue</button>
                        </div>
                    </div>

                    <!-- STEP 4: Account Setup (Password & Terms) -->
                    <div class="form-step" data-step="4">
                        <h2 style="margin-bottom: 20px; color: #212529;">Account Setup</h2>

                        <div class="form-group">
                            <label>Password <span class="required">*</span></label>
                            <input type="password" name="password" id="password" placeholder="Minimum 8 characters" required>
                            <div class="error-message" id="passwordError">
                                @error('password'){{ $message }}@enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Confirm Password <span class="required">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Re-enter your password" required>
                            <div class="error-message" id="passwordConfirmError"></div>
                        </div>

                        <div class="form-group">
                            <div class="terms-checkbox">
                                <input type="checkbox" name="terms_accepted" id="terms_accepted" value="1" {{ old('terms_accepted') ? 'checked' : '' }}>
                                <label for="terms_accepted">
                                    I accept the <a href="#" target="_blank">Terms and Conditions</a> <span class="required">*</span>
                                </label>
                            </div>
                            <div class="error-message" id="termsError">
                                @error('terms_accepted'){{ $message }}@enderror
                            </div>
                        </div>

                        <div class="button-group">
                            <button type="button" class="btn btn-secondary" onclick="prevStep(4)">Back</button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">Complete Registration</button>
                        </div>
                    </div>
                </form>

                <div class="login-link">
                    Already have an account? <a href="{{ route('login') }}">Login here</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentStep = 1;
        let userType = '{{ old("user_type") }}';

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Check for old user type value
            if (userType) {
                const card = document.querySelector(`input[value="${userType}"]`)?.closest('.user-type-card');
                if (card) {
                    card.classList.add('selected');
                    selectUserType(userType);
                }
            }
            
            // Show validation errors from server
            @if($errors->any())
                let errorStep = 1;
                
                // Determine which step has errors
                @if($errors->has('user_type'))
                    errorStep = 1;
                @elseif($errors->has('first_name') || $errors->has('last_name') || $errors->has('sr_code') || $errors->has('email') || $errors->has('year_level') || $errors->has('course_program'))
                    errorStep = 2;
                @elseif($errors->has('tutor_level_preference') || $errors->has('gwa') || $errors->has('resume'))
                    errorStep = '3-tutor';
                @elseif($errors->has('password') || $errors->has('terms_accepted'))
                    errorStep = 4;
                @endif

                showStep(errorStep);

                // Show all error messages
                document.querySelectorAll('.error-message').forEach(elem => {
                    if (elem.textContent.trim()) {
                        elem.classList.add('show');
                        const input = elem.previousElementSibling;
                        if (input && (input.tagName === 'INPUT' || input.tagName === 'SELECT')) {
                            input.classList.add('error');
                        }
                    }
                });
            @endif
        });

        // File upload handler
        document.getElementById('resume').addEventListener('change', function() {
            const fileDisplay = document.getElementById('fileNameDisplay');
            if (this.files && this.files[0]) {
                const file = this.files[0];
                if (file.size > 5 * 1024 * 1024) {
                    showError('resumeError', 'File size must not exceed 5MB');
                    this.value = '';
                    fileDisplay.classList.remove('show');
                    return;
                }
                if (file.type !== 'application/pdf') {
                    showError('resumeError', 'Only PDF files are allowed');
                    this.value = '';
                    fileDisplay.classList.remove('show');
                    return;
                }
                fileDisplay.textContent = '✓ ' + file.name;
                fileDisplay.classList.add('show');
                hideError('resumeError');
            }
        });

        function selectUserType(type) {
            userType = type;
            document.querySelectorAll('.user-type-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            const selectedCard = document.querySelector(`input[value="${type}"]`).closest('.user-type-card');
            if (selectedCard) {
                selectedCard.classList.add('selected');
            }
            
            document.getElementById(type).checked = true;
            hideError('userTypeError');

            const yearLevelSelect = document.getElementById('year_level');
            if (type === 'student') {
                yearLevelSelect.innerHTML = `
                    <option value="">Select Year Level</option>
                    <option value="First Year">First Year</option>
                    <option value="Second Year">Second Year</option>
                `;
            } else {
                yearLevelSelect.innerHTML = `
                    <option value="">Select Year Level</option>
                    <option value="Third Year">Third Year</option>
                    <option value="Fourth Year or above">Fourth Year or above</option>
                `;
            }
        }

        function selectTutorLevel(level) {
            document.querySelectorAll('.radio-item').forEach(item => {
                item.style.borderColor = '#DEE2E6';
            });
            event.currentTarget.style.borderColor = '#004DA9';
            
            const input = event.currentTarget.querySelector('input');
            input.checked = true;
            
            hideError('tutorLevelError');
        }

        function showStep(stepNumber) {
            document.querySelectorAll('.form-step').forEach(step => {
                step.classList.remove('active');
            });
            document.querySelector(`[data-step="${stepNumber}"]`).classList.add('active');
            updateProgressBar(stepNumber);
            currentStep = stepNumber;
        }

        function nextStep(step) {
            if (!validateStep(step)) {
                return;
            }

            let nextStepNumber;
            
            if (step === 1) {
                nextStepNumber = 2;
            } else if (step === 2) {
                if (userType === 'tutor') {
                    nextStepNumber = '3-tutor';
                    document.getElementById('step3-label').textContent = 'Tutoring Profile';
                } else {
                    nextStepNumber = 4;
                    document.getElementById('step3-label').textContent = 'Account Setup';
                }
            } else if (step === 3 || step === '3-tutor') {
                nextStepNumber = 4;
            }

            showStep(nextStepNumber);
        }

        function prevStep(step) {
            let prevStepNumber;
            
            if (step === 2) {
                prevStepNumber = 1;
            } else if (step === '3-tutor' || step === 3) {
                prevStepNumber = 2;
            } else if (step === 4) {
                if (userType === 'tutor') {
                    prevStepNumber = '3-tutor';
                } else {
                    prevStepNumber = 2;
                }
            }

            showStep(prevStepNumber);
        }

        function updateProgressBar(step) {
            const steps = document.querySelectorAll('.progress-step');
            steps.forEach((elem, index) => {
                elem.classList.remove('active', 'completed');
                const progressNum = getProgressNumber(step);
                if (index + 1 < progressNum) {
                    elem.classList.add('completed');
                } else if (index + 1 === progressNum) {
                    elem.classList.add('active');
                }
            });
        }

        function getProgressNumber(step) {
            if (step === 1) return 1;
            if (step === 2) return 2;
            if (step === '3-tutor' || step === 3) return 3;
            if (step === 4) return 3;
            return 1;
        }

        function validateStep(step) {
            let isValid = true;

            if (step === 1) {
                if (!userType) {
                    showError('userTypeError', 'Please select a user type');
                    isValid = false;
                }
            }

            if (step === 2) {
                if (!document.getElementById('first_name').value.trim()) {
                    showError('firstNameError', 'First name is required');
                    isValid = false;
                }
                if (!document.getElementById('last_name').value.trim()) {
                    showError('lastNameError', 'Last name is required');
                    isValid = false;
                }
                
                const srCode = document.getElementById('sr_code').value.trim();
                if (!srCode.match(/^\d{2}-\d{5}$/)) {
                    showError('srCodeError', 'Invalid SR code format (e.g., 24-12345)');
                    isValid = false;
                } else {
                    const year = parseInt(srCode.substring(0,2));
                    if (userType === 'tutor' && year > 23) {
                        showError('srCodeError', 'Tutors must have SR code 23 or lower');
                        isValid = false;
                    }
                    if (userType === 'student' && year < 24) {
                        showError('srCodeError', 'Students must have SR code 24 or higher');
                        isValid = false;
                    }
                }


                const email = document.getElementById('email').value.trim();
                const expectedEmail = `${srCode}@g.batstate-u.edu.ph`;
                if (!email.match(/^\d{2}-\d{5}@g\.batstate-u\.edu\.ph$/)) {
                    showError('emailError', 'Invalid email format');
                    isValid = false;
                } else if (email !== expectedEmail) {
                    showError('emailError', `Email must match SR code: ${expectedEmail}`);
                    isValid = false;
                }

                if (!document.getElementById('year_level').value) {
                    showError('yearLevelError', 'Please select year level');
                    isValid = false;
                }

                if (!document.getElementById('course_program').value) {
                    showError('courseProgramError', 'Please select course/program');
                    isValid = false;
                }
            }

            if (step === 3 || step === '3-tutor') {
                if (!document.querySelector('input[name="tutor_level_preference"]:checked')) {
                    showError('tutorLevelError', 'Please select tutoring level');
                    isValid = false;
                }

                const gwa = document.getElementById('gwa').value;
                if (!gwa) {
                    showError('gwaError', 'GWA is required');
                    isValid = false;
                } else if (parseFloat(gwa) < 1.00 || parseFloat(gwa) > 5.00) {
                    showError('gwaError', 'GWA must be between 1.00 and 5.00');
                    isValid = false;
                }

                if (!document.getElementById('resume').files[0]) {
                    showError('resumeError', 'Please upload your resume');
                    isValid = false;
                }
            }

            return isValid;
        }

        function showError(elementId, message) {
            const errorElement = document.getElementById(elementId);
            errorElement.textContent = message;
            errorElement.classList.add('show');
            
            const inputId = elementId.replace('Error', '');
            const input = document.getElementById(inputId);
            if (input) {
                input.classList.add('error');
            }
        }

        function hideError(elementId) {
            const errorElement = document.getElementById(elementId);
            errorElement.classList.remove('show');
            
            const inputId = elementId.replace('Error', '');
            const input = document.getElementById(inputId);
            if (input) {
                input.classList.remove('error');
            }
        }

        // Auto-fill email from SR code
        document.getElementById('sr_code').addEventListener('input', function(e) {
            const email = document.getElementById('email');
            if (this.value.match(/^\d{2}-\d{5}$/)) {
                email.value = `${this.value}@g.batstate-u.edu.ph`;
                hideError('srCodeError');
                hideError('emailError');
            }
        });

        // Password validation
        document.getElementById('password').addEventListener('input', function() {
            if (this.value.length >= 8) {
                hideError('passwordError');
            }
        });

        document.getElementById('password_confirmation').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            if (this.value === password && this.value.length > 0) {
                hideError('passwordConfirmError');
            }
        });

        // Form submission
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirmation').value;

            if (password.length < 8) {
                e.preventDefault();
                showError('passwordError', 'Password must be at least 8 characters');
                return false;
            }

            if (password !== passwordConfirm) {
                e.preventDefault();
                showError('passwordConfirmError', 'Passwords do not match');
                return false;
            }

            if (!document.getElementById('terms_accepted').checked) {
                e.preventDefault();
                showError('termsError', 'You must accept the terms and conditions');
                return false;
            }

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Registering...';
        });
    </script>
</body>
</html>
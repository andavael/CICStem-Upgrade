<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - CICStem</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="auth-wrapper">
        <!-- Brand Section (Left Side) -->
        <div class="auth-brand show">
            <img src="{{ asset('assets/logo.png') }}" alt="CICStem Logo" class="brand-logo-img">

            <h1 class="brand-title">
               <span class="highlight">CICS</span>TEM UPGRADE
            </h1>
            <p class="brand-subtitle">Level Up Your Learning – the CICS Way</p>
        </div>

        <!-- Auth Container (Right Side) -->
        <div class="auth-container">
            <div class="auth-header">
                <h1>SIGN IN</h1>
                <p>Log in to access your CICStem account</p>
            </div>

            <div class="auth-content">
                @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('warning'))
                <div class="alert alert-warning">{{ session('warning') }}</div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
                    @csrf

                    <div class="form-group">
                        <label>Login As <span class="required">*</span></label>
                        <div class="user-type-selection" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 0;">
                            <div class="user-type-card" onclick="selectUserType('student')" style="padding: 20px 10px;">
                                <input type="radio" name="user_type" id="student" value="student" {{ old('user_type') == 'student' ? 'checked' : '' }}>
                                <div class="user-type-icon" style="font-size: 32px; margin-bottom: 8px;">🎓</div>
                                <div class="user-type-title" style="font-size: 16px;">Student</div>
                            </div>

                            <div class="user-type-card" onclick="selectUserType('tutor')" style="padding: 20px 10px;">
                                <input type="radio" name="user_type" id="tutor" value="tutor" {{ old('user_type') == 'tutor' ? 'checked' : '' }}>
                                <div class="user-type-icon" style="font-size: 32px; margin-bottom: 8px;">👨‍🏫</div>
                                <div class="user-type-title" style="font-size: 16px;">Tutor</div>
                            </div>

                            <div class="user-type-card" onclick="selectUserType('admin')" style="padding: 20px 10px;">
                                <input type="radio" name="user_type" id="admin" value="admin" {{ old('user_type') == 'admin' ? 'checked' : '' }}>
                                <div class="user-type-icon" style="font-size: 32px; margin-bottom: 8px;">👤</div>
                                <div class="user-type-title" style="font-size: 16px;">Admin</div>
                            </div>
                        </div>
                        <div class="error-message" id="userTypeError"></div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            placeholder="Enter your email"
                            value="{{ old('email') }}"
                            required 
                            autofocus
                        >
                        <div class="error-message" id="emailError"></div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            placeholder="Enter your password"
                            required
                        >
                        <div class="error-message" id="passwordError"></div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" id="loginBtn">
                        Log-in
                    </button>

                    <div class="form-options" style="margin-top: 20px;">
                        <div class="remember-me">
                            <input type="checkbox" name="remember" id="remember">
                            <label for="remember">Remember me</label>
                        </div>
                    </div>
                </form>

                <div class="register-link">
                    <p>Don't have an account yet?</p>
                    <a href="{{ route('register') }}" style="color: #FFA500; font-weight: 600; text-decoration: underline;">Create Account</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedUserType = '{{ old("user_type") }}';

        // Initialize selected user type if exists
        if (selectedUserType) {
            document.getElementById(selectedUserType).checked = true;
            document.querySelector(`[onclick="selectUserType('${selectedUserType}')"]`).classList.add('selected');
        }

        function selectUserType(type) {
            selectedUserType = type;
            document.querySelectorAll('.user-type-card').forEach(card => {
                card.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
            document.getElementById(type).checked = true;
            hideError('userTypeError');
            
            // Update email placeholder based on user type
            const emailInput = document.getElementById('email');
            if (type === 'admin') {
                emailInput.placeholder = 'Enter your admin email';
            } else {
                emailInput.placeholder = 'xx-xxxxx@g.batstate-u.edu.ph';
            }
        }

        // Client-side validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;

            // Clear previous errors
            document.querySelectorAll('.error-message').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('input').forEach(el => el.classList.remove('error'));

            // Validate user type
            if (!selectedUserType) {
                showError('userTypeError', 'Please select your user type', null);
                isValid = false;
            }

            // Validate email
            if (!email) {
                showError('emailError', 'Email is required', 'email');
                isValid = false;
            } else if (selectedUserType !== 'admin' && !email.match(/^\d{2}-\d{5}@g\.batstate-u\.edu\.ph$/)) {
                showError('emailError', 'Please enter a valid G-Suite email', 'email');
                isValid = false;
            }

            // Validate password
            if (!password) {
                showError('passwordError', 'Password is required', 'password');
                isValid = false;
            } else if (password.length < 8) {
                showError('passwordError', 'Password must be at least 8 characters', 'password');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                return false;
            }

            // Disable button and show loading state
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.textContent = 'Signing In...';
        });

        function showError(elementId, message, inputId) {
            const errorElement = document.getElementById(elementId);
            errorElement.textContent = message;
            errorElement.classList.add('show');
            
            if (inputId) {
                const input = document.getElementById(inputId);
                if (input) {
                    input.classList.add('error');
                }
            }
        }

        function hideError(elementId) {
            const errorElement = document.getElementById(elementId);
            errorElement.classList.remove('show');
        }

        // Clear error on input
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('error');
                const errorId = this.id + 'Error';
                const errorElement = document.getElementById(errorId);
                if (errorElement) {
                    errorElement.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Pending - CICStem</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/tutor.css') }}" rel="stylesheet">
</head>
<body class="tutor-body">
    <nav class="tutor-nav">
        <div class="tutor-nav-content">
            <div class="tutor-logo">
                <img src="{{ asset('assets/logo.png') }}" alt="CICStem Logo" class="brand-logo-img">
                <div class="logo-text-wrapper">
                    <span class="logo-text"><span class="highlight">CICS</span>TEM UPGRADE</span>
                    <span class="page-subtitle">Application Status</span>
                </div>
            </div>
            <div class="tutor-nav-right">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout">Log-out</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="pending-container">
        <div class="pending-card">
            @if(Auth::guard('tutor')->user()->is_approved)
                <div class="pending-icon">✅</div>
                <h1 class="pending-title">Application Approved!</h1>
                <p class="pending-message">
                    Congratulations! Your tutor application has been approved by the administrator. 
                    You can now access the full tutor dashboard and start managing your sessions.
                </p>
                <a href="{{ route('tutor.dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
            @else
                <div class="pending-icon">⏳</div>
                <h1 class="pending-title">Application Under Evaluation</h1>
                <p class="pending-message">
                    Thank you for submitting your tutor application! Your profile is currently being reviewed 
                    by our administrators. You will receive a notification once your application has been processed.
                </p>
                <p class="pending-message">
                    <strong>Please check back later or wait for an email notification.</strong>
                </p>
            @endif

            <div class="profile-summary">
                <h3 style="margin-bottom: 20px; color: #212529;">Your Profile Information</h3>
                
                <div class="profile-row">
                    <span class="profile-label">Name:</span>
                    <span class="profile-value">{{ Auth::guard('tutor')->user()->full_name }}</span>
                </div>
                
                <div class="profile-row">
                    <span class="profile-label">SR Code:</span>
                    <span class="profile-value">{{ Auth::guard('tutor')->user()->sr_code }}</span>
                </div>
                
                <div class="profile-row">
                    <span class="profile-label">Email:</span>
                    <span class="profile-value">{{ Auth::guard('tutor')->user()->email }}</span>
                </div>
                
                <div class="profile-row">
                    <span class="profile-label">Year Level:</span>
                    <span class="profile-value">{{ Auth::guard('tutor')->user()->year_level }}</span>
                </div>
                
                <div class="profile-row">
                    <span class="profile-label">Course/Program:</span>
                    <span class="profile-value">{{ Auth::guard('tutor')->user()->course_program }}</span>
                </div>
                
                <div class="profile-row">
                    <span class="profile-label">Preference:</span>
                    <span class="profile-value">{{ Auth::guard('tutor')->user()->tutor_level_preference }}</span>
                </div>
                
                <div class="profile-row">
                    <span class="profile-label">GWA:</span>
                    <span class="profile-value">{{ number_format(Auth::guard('tutor')->user()->gwa, 2) }}</span>
                </div>
                
                <div class="profile-row">
                    <span class="profile-label">Status:</span>
                    <span class="profile-value">
                        @if(Auth::guard('tutor')->user()->is_approved)
                            <span class="badge badge-success">Approved</span>
                        @else
                            <span class="badge badge-warning">Pending Review</span>
                        @endif
                    </span>
                </div>
                
                <div class="profile-row">
                    <span class="profile-label">Submitted:</span>
                    <span class="profile-value">{{ Auth::guard('tutor')->user()->created_at->format('M d, Y h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>

    <footer class="tutor-footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Contact Information</h3>
                <p>If you have any questions about your application, please contact us:</p>
                <div class="contact-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <a href="mailto:cicsscalangilan@g.batstate-u.edu.ph">cicsscalangilan@g.batstate-u.edu.ph</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} CICStem Upgrade. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
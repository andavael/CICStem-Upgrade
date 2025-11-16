<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Dashboard') - CICStem</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/student.css') }}" rel="stylesheet">
    @stack('styles')
    <style>
        /* Alert Animations */
        .alert {
            animation: slideInDown 0.4s ease-out;
            margin-bottom: 20px;
            padding: 16px 20px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%);
            color: #856404;
            border-left: 4px solid #ffc107;
        }

        .alert-info {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }

        /* Enhanced hover effects */
        .student-logo {
            transition: transform 0.3s ease;
        }

        .student-logo:hover {
            transform: scale(1.02);
        }

        /* Smooth transitions */
        * {
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Better scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #2d5f8d;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #1e3a5f;
        }

        /* Loading indicator */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="student-body">
    <!-- Top Navigation -->
    <nav class="student-nav">
        <div class="student-nav-content">
            <div class="student-logo">
                <img src="{{ asset('assets/logo.png') }}" alt="CICStem Logo" class="brand-logo-img">
                <div class="logo-text-wrapper">
                    <span class="logo-text"><span class="highlight">CICS</span>TEM UPGRADE</span>
                    <span class="page-subtitle">Student Dashboard</span>

                </div>
            </div>

            <div class="student-nav-right">
                <span class="student-user">{{ $student->full_name }}</span>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-logout">Log-out</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="student-container">
        <!-- Sidebar -->
        <aside class="student-sidebar">
            <ul class="sidebar-menu">
                <li class="sidebar-item {{ request()->routeIs('student.dashboard*') ? 'active' : '' }}">
                    <a href="{{ route('student.dashboard') }}">📊 Dashboard</a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('student.available-sessions*') ? 'active' : '' }}">
                    <a href="{{ route('student.available-sessions.index') }}">🔍 Browse Sessions</a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('student.my-sessions*') ? 'active' : '' }}">
                    <a href="{{ route('student.my-sessions.index') }}">📚 My Sessions</a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('student.announcements*') ? 'active' : '' }}">
                    <a href="{{ route('student.announcements.index') }}">📢 Announcements</a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('student.feedback*') ? 'active' : '' }}">
                    <a href="{{ route('student.feedback.index') }}">⭐ Feedback</a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('student.notifications*') ? 'active' : '' }}">
                    <a href="{{ route('student.notifications.notifications') }}">
                        🔔 Notifications
                        @if(isset($unreadCount) && $unreadCount > 0)
                            <span class="notification-badge">{{ $unreadCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('student.profile*') ? 'active' : '' }}">
                    <a href="{{ route('student.profile.index') }}">👤 Profile</a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="student-main">
            @if(session('success'))
            <div class="alert alert-success">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
                {{ session('error') }}
            </div>
            @endif

            @if(session('warning'))
            <div class="alert alert-warning">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                {{ session('warning') }}
            </div>
            @endif

            @if(session('info'))
            <div class="alert alert-info">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                {{ session('info') }}
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Enhanced Footer -->
    <footer class="student-footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Contact Information</h3>
                <div class="contact-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <a href="mailto:cicsscalangilan@g.batstate-u.edu.ph">cicsscalangilan@g.batstate-u.edu.ph</a>
                </div>
                <div class="contact-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                    <a href="tel:09672954793">09672954793</a>
                </div>
                <div class="contact-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                    </svg>
                    <a href="https://facebook.com/CICCStudentCouncil" target="_blank">CICC-Student Council</a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} CICStem Upgrade. All rights reserved. | College of Informatics and Computing Sciences</p>
        </div>
    </footer>

    @stack('scripts')

    <script>
        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.animation = 'slideOutUp 0.4s ease-out';
                    setTimeout(() => {
                        alert.remove();
                    }, 400);
                }, 5000);
            });
        });

        // Add slideOutUp animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideOutUp {
                from {
                    opacity: 1;
                    transform: translateY(0);
                }
                to {
                    opacity: 0;
                    transform: translateY(-20px);
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
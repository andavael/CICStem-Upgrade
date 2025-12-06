@extends('student.layouts.app')

@section('title', 'Session Details')
@section('page-title', 'Session Details')

@section('content')
<div class="page-header">
    <h1 class="page-title">📖 Session Details</h1>
    <div class="page-actions">
        <a href="{{ route('student.my-sessions.index') }}" class="btn btn-action btn-secondary-action">
            Back to My Sessions
        </a>
    </div>
</div>

<!-- Session Information -->
<div class="content-panel" style="background: #ffffff; border: 2px solid #dee2e6; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
    <div style="background: linear-gradient(135deg, #2d5f8d 0%, #1a4366 100%); padding: 20px; border-radius: 8px 8px 0 0; margin: -1px -1px 24px -1px; display: flex; justify-content: space-between; align-items: center;">
        <h2 style="font-size: 22px; font-weight: 700; margin: 0; color: #ffffff;">Session Information</h2>
        
        <!-- Session Status Badge -->
        <div>
            @if($session->status === 'Scheduled')
                <span class="badge badge-info" style="font-size: 14px; padding: 8px 16px;">
                    {{ $session->status }}
                </span>
            @elseif($session->status === 'Ongoing')
                <span class="badge badge-warning" style="font-size: 14px; padding: 8px 16px;">
                    {{ $session->status }}
                </span>
            @elseif($session->status === 'Completed')
                <span class="badge badge-success" style="font-size: 14px; padding: 8px 16px;">
                    {{ $session->status }}
                </span>
            @else
                <span class="badge badge-danger" style="font-size: 14px; padding: 8px 16px;">
                    {{ $session->status }}
                </span>
            @endif
        </div>
    </div>
    
    <div style="padding: 0 24px 24px 24px;">
    <div class="form-grid">
        <div class="form-group">
            <label>Subject</label>
            <div class="info-box">
                {{ $session->subject }}
            </div>
        </div>

        <div class="form-group">
            <label>Session Code</label>
            <div class="info-box" style="font-family: 'Courier New', monospace;">
                {{ $session->session_code }}
            </div>
        </div>

        <div class="form-group">
            <label>Date</label>
            <div class="info-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px; vertical-align: middle;">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                {{ \Carbon\Carbon::parse($session->session_date)->format('F d, Y') }}
            </div>
        </div>

        <div class="form-group">
            <label>Time</label>
            <div class="info-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px; vertical-align: middle;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                {{ $session->session_time }}
            </div>
        </div>

        <div class="form-group">
            <label>Tutor</label>
            <div class="info-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px; vertical-align: middle;">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                {{ $session->tutor ? $session->tutor->full_name : 'N/A' }}
            </div>
        </div>

        <div class="form-group">
            <label>Year Level</label>
            <div class="info-box">
                {{ $session->year_level }}
            </div>
        </div>

        <div class="form-group">
            <label>Enrolled Students</label>
            <div class="info-box">
                <strong>{{ $enrolledCount }}</strong> out of <strong>{{ $session->capacity }}</strong>
                @if($enrolledCount >= $session->capacity)
                    <span class="badge badge-danger" style="margin-left: 10px;">Full</span>
                @else
                    <span class="badge badge-success" style="margin-left: 10px;">{{ $session->capacity - $enrolledCount }} slots available</span>
                @endif
            </div>
        </div>

        <div class="form-group">
            <label>Your Attendance Status</label>
            <div class="info-box">
                @if($enrollment->attendance_status === 'Present')
                    <span class="badge badge-success">Present</span>
                @elseif($enrollment->attendance_status === 'Absent')
                    <span class="badge badge-danger">Absent</span>
                @else
                    <span class="badge badge-secondary">Pending</span>
                @endif
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Google Meet Link</label>
        <div class="info-box">
            <a href="{{ $session->google_meet_link }}" target="_blank" style="color: #2d5f8d; text-decoration: underline; display: inline-flex; align-items: center; gap: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                    <polyline points="15 3 21 3 21 9"></polyline>
                    <line x1="10" y1="14" x2="21" y2="3"></line>
                </svg>
                Join Session
            </a>
        </div>
    </div>

    @if($session->description)
    <div class="form-group">
        <label>Description</label>
        <div class="info-box" style="line-height: 1.6; white-space: pre-wrap;">
            {{ $session->description }}
        </div>
    </div>
    @endif

    <div class="form-group">
        <label>Enrollment Information</label>
        <div class="info-box">
            Enrolled on {{ \Carbon\Carbon::parse($enrollment->enrolled_at)->format('F d, Y \a\t h:i A') }}
        </div>
    </div>
    </div>
</div>

<!-- Action Buttons -->
@if($session->status === 'Scheduled' && \Carbon\Carbon::parse($session->session_date)->isFuture())
<div class="content-panel" style="background: #ffffff; border: 2px solid #dee2e6; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-left: 4px solid #ffc107;">
    <div style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); padding: 20px; border-radius: 8px 8px 0 0; margin: -1px -1px 24px -1px;">
        <h2 style="font-size: 22px; font-weight: 700; margin: 0; color: #212529;">⚙️ Actions</h2>
    </div>
    
    <div style="padding: 0 24px 24px 24px;">
    
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="{{ $session->google_meet_link }}" target="_blank" class="btn btn-action btn-primary-action">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px; vertical-align: middle;">
                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                <polyline points="15 3 21 3 21 9"></polyline>
                <line x1="10" y1="14" x2="21" y2="3"></line>
            </svg>
            Join Google Meet
        </a>
        
        <form action="{{ route('student.available-sessions.unenroll', $session->id) }}" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-action btn-danger-action" onclick="return confirm('Are you sure you want to unenroll from this session?')">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px; vertical-align: middle;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
                Unenroll from Session
            </button>
        </form>
    </div>
    </div>
</div>
@endif

<!-- Tips for Students -->
<div class="content-panel" style="background: #ffffff; border: 2px solid #dee2e6; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-left: 4px solid #2d5f8d;">
    <div style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); padding: 20px; border-radius: 8px 8px 0 0; margin: -1px -1px 24px -1px;">
        <h2 style="font-size: 22px; font-weight: 700; margin: 0; color: #1e3a5f;">💡 Session Tips</h2>
    </div>
    
    <div style="padding: 0 24px 24px 24px;">
    
    <ul style="margin: 0; padding-left: 24px; color: #1e3a5f; line-height: 1.8;">
        <li>Join the session 5 minutes before the scheduled time</li>
        <li>Make sure your camera and microphone are working properly</li>
        <li>Prepare your questions and materials in advance</li>
        <li>Take notes during the session for better retention</li>
        <li>Participate actively and don't hesitate to ask questions</li>
        @if($session->status === 'Completed')
        <li>Don't forget to provide feedback about this session!</li>
        @endif
    </ul>
    </div>
</div>

<style>
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
}

.info-box {
    padding: 12px 16px;
    background: #f8f9fa;
    border-radius: 8px;
    font-size: 15px;
    color: #212529;
    border: 1px solid #dee2e6;
}

.btn-action {
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
}

.btn-primary-action {
    background: linear-gradient(135deg, #2d5f8d 0%, #1a4366 100%);
    color: white;
}

.btn-primary-action:hover {
    background: linear-gradient(135deg, #1a4366 0%, #0d2235 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(45, 95, 141, 0.3);
}

.btn-secondary-action {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
}

.btn-secondary-action:hover {
    background: linear-gradient(135deg, #5a6268 0%, #343a40 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
}

.btn-danger-action {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
}

.btn-danger-action:hover {
    background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
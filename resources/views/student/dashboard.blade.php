@extends('student.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">Hello, {{ $student->first_name }}! 👋</h1>
    <div class="page-actions">
        <a href="{{ route('student.available-sessions.index') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
            </svg>
            Browse Sessions
        </a>
    </div>
</div>

<!-- Welcome Message -->
<div class="content-panel" style="background-color: #3797f745;">
    <p style="color: #00213fff; font-size: 15px;">
        Here's an overview of your tutoring activities and upcoming sessions.
    </p>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Enrolled</div>
        <div class="stat-value">{{ $totalEnrolled }}</div>
        <div class="stat-description">Sessions joined</div>
    </div>
    
    <div class="stat-card success">
        <div class="stat-label">Sessions Attended</div>
        <div class="stat-value">{{ $attendedSessions }}</div>
        <div class="stat-description">{{ $attendanceRate }}% attendance rate</div>
    </div>
    
    <div class="stat-card warning">
        <div class="stat-label">Upcoming Sessions</div>
        <div class="stat-value">{{ $pendingSessions }}</div>
        <div class="stat-description">Scheduled ahead</div>
    </div>
    
    <div class="stat-card danger">
        <div class="stat-label">Absences</div>
        <div class="stat-value">{{ $absentSessions }}</div>
        <div class="stat-description">Sessions missed</div>
    </div>
</div>

<!-- Upcoming Sessions -->
<div class="content-panel">
    <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 20px;">Upcoming Sessions (Next 3 Days)</h2>
    
    @if($upcomingSessions->count() > 0)
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Session Details</th>
                    <th>Date & Time</th>
                    <th>Tutor</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($upcomingSessions as $session)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="font-size: 24px;">📚</div>
                            <div>
                                <div style="font-weight: 600; color: #212529;">{{ $session->subject }}</div>
                                <div style="font-size: 12px; color: #6c757d;">{{ $session->session_code }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            <div style="font-weight: 600;">{{ \Carbon\Carbon::parse($session->session_date)->format('M d, Y') }}</div>
                            <div style="font-size: 13px; color: #6c757d;">⏰ {{ $session->session_time }}</div>
                        </div>
                    </td>
                    <td>{{ $session->tutor ? $session->tutor->full_name : 'N/A' }}</td>
                    <td>
                        <span class="badge badge-info">{{ $session->pivot->attendance_status }}</span>
                    </td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('student.my-sessions.show', $session->id) }}" class="btn btn-view btn-sm">
                                View Details
                            </a>
                            @if($session->google_meet_link)
                            <a href="{{ $session->google_meet_link }}" target="_blank" class="btn btn-enroll btn-sm">
                                Join Meeting
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon">📅</div>
        <p>No upcoming sessions in the next 3 days.</p>
        <a href="{{ route('student.available-sessions.index') }}" class="btn btn-primary" style="margin-top: 16px;">
            Browse Available Sessions
        </a>
    </div>
    @endif
</div>

<!-- My Enrolled Sessions -->
<div class="content-panel">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-size: 20px; font-weight: 600; margin: 0;">Recent Enrolled Sessions</h2>
        <a href="{{ route('student.my-sessions.index') }}" style="color: #2d5f8d; text-decoration: none; font-weight: 500;">
            View All →
        </a>
    </div>
    
    @if($enrolledSessions->count() > 0)
    <div class="session-cards-grid">
        @foreach($enrolledSessions as $session)
        <div class="session-card" onclick="window.location='{{ route('student.my-sessions.show', $session->id) }}'">
            <div class="session-card-header">
                <div>
                    <div class="session-card-subject">{{ $session->subject }}</div>
                    <div class="session-card-code">{{ $session->session_code }}</div>
                </div>
                @if($session->status === 'Scheduled')
                    <span class="badge badge-info">{{ $session->status }}</span>
                @elseif($session->status === 'Completed')
                    <span class="badge badge-success">{{ $session->status }}</span>
                @else
                    <span class="badge badge-warning">{{ $session->status }}</span>
                @endif
            </div>
            
            <div class="session-card-body">
                <div class="session-info-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    {{ \Carbon\Carbon::parse($session->session_date)->format('F d, Y') }}
                </div>
                
                <div class="session-info-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    {{ $session->session_time }}
                </div>
                
                <div class="session-info-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    {{ $session->tutor ? $session->tutor->full_name : 'N/A' }}
                </div>
            </div>
            
            <div class="session-card-footer">
                <span class="badge badge-secondary">{{ $session->pivot->attendance_status }}</span>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon">📚</div>
        <p>You haven't enrolled in any sessions yet.</p>
    </div>
    @endif
</div>

<style>

.content-panel {
    /* background-color: removed */
    padding: 20px;               /* keeps spacing inside */
    border-radius: 20px;          /* rounded corners */
    box-shadow: 2px 2px 8px 8px rgba(0, 0, 0, 0.1); /* subtle shadow */
}


.stat-card {
    position: relative;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(45, 95, 141, 0.3);
}

.session-card {
    cursor: pointer;
}

.announcement-card:last-child {
    margin-bottom: 0;
}
</style>
@endsection
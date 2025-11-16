@php
use Illuminate\Support\Str;
@endphp

@extends('tutor.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Tutor Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">Hello, {{ Str::title($tutor->first_name) }}!👋</h1>
</div>

<!-- Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Sessions</div>
        <div class="stat-value">{{ $totalSessions }}</div>
        <div class="stat-description">All time sessions</div>
    </div>

    <div class="stat-card success">
        <div class="stat-label">Completed Sessions</div>
        <div class="stat-value">{{ $completedSessions }}</div>
        <div class="stat-description">Successfully finished</div>
    </div>

    <div class="stat-card warning">
        <div class="stat-label">Upcoming Sessions</div>
        <div class="stat-value">{{ $upcomingCount }}</div>
        <div class="stat-description">Scheduled ahead</div>
    </div>

    <div class="stat-card info">
        <div class="stat-label">Total Students</div>
        <div class="stat-value">{{ $totalStudents }}</div>
        <div class="stat-description">Unique students taught</div>
    </div>
</div>

<!-- Recent Notifications -->
@if($unreadNotifications > 0 || $recentNotifications->count() > 0)
<div class="content-panel">
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #212529; display: flex; align-items: center; justify-content: space-between;">
        <span>🔔 Recent Notifications</span>
        @if($unreadNotifications > 0)
        <span class="badge badge-danger">{{ $unreadNotifications }} New</span>
        @endif
    </h2>
    
    @if($recentNotifications->count() > 0)
        @foreach($recentNotifications as $notification)
        <div class="notification-card-mini {{ $notification->is_read ? 'read' : 'unread' }}">
            <div style="display: flex; gap: 12px; align-items: start;">
                <div class="notification-icon-mini">
                    @if($notification->type === 'student_enrollment')
                        👤
                    @elseif($notification->type === 'session_update')
                        📝
                    @elseif($notification->type === 'session_cancellation')
                        ❌
                    @elseif($notification->type === 'session_reschedule')
                        📅
                    @else
                        🔔
                    @endif
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 600; color: #212529; margin-bottom: 4px;">{{ $notification->title }}</div>
                    <div style="font-size: 14px; color: #495057;">{{ Str::limit($notification->message, 100) }}</div>
                    <div style="font-size: 12px; color: #6c757d; margin-top: 4px;">{{ $notification->time_ago }}</div>
                </div>
                @if(!$notification->is_read)
                <span class="badge badge-primary">New</span>
                @endif
            </div>
        </div>
        @endforeach

        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('tutor.notifications') }}" class="btn btn-secondary">View All Notifications</a>
        </div>
    @endif
</div>
@endif

<!-- Upcoming Sessions -->
<div class="content-panel">
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #212529;">Upcoming Sessions</h2>
    
    @if($upcomingSessions->count() > 0)
        @foreach($upcomingSessions as $session)
        <div class="session-card">
            <div class="session-header">
                <div>
                    <div class="session-title">{{ $session->subject }}</div>
                    <div style="font-size: 13px; color: #6c757d; margin-top: 4px;">
                        {{ $session->session_code }} • {{ $session->year_level }}
                    </div>
                </div>
                <span class="badge badge-info">{{ $session->status }}</span>
            </div>

            <div class="session-body">
                <div class="session-info-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    {{ \Carbon\Carbon::parse($session->session_date)->format('M d, Y') }}
                </div>

                <div class="session-info-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    {{ $session->session_time }}
                </div>

                <div class="session-info-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    {{ $session->students->count() }} / {{ $session->capacity }} students
                </div>
            </div>

            <div class="session-footer">
                <a href="{{ route('tutor.sessions.show', $session->id) }}" class="btn btn-primary btn-sm">View Details</a>
            </div>
        </div>
        @endforeach

        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('tutor.sessions.index') }}" class="btn btn-secondary">View All Sessions</a>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">📅</div>
            <h3>No Upcoming Sessions</h3>
            <p>You don't have any scheduled sessions at the moment.</p>
        </div>
    @endif
</div>

<!-- Ratings & Feedback Section -->
<div class="content-panel">
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #212529;">Ratings & Feedback</h2>
    
    <div class="rating-summary">
        <div class="rating-overview">
            <div class="rating-score">
                <span class="rating-number">{{ number_format($averageRating, 1) }}</span>
                <div class="rating-stars">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($averageRating))
                            ⭐
                        @elseif($i - 0.5 <= $averageRating)
                            ⭐
                        @else
                            ☆
                        @endif
                    @endfor
                </div>
            </div>
            <div class="rating-meta">
                <strong>{{ $totalFeedback }}</strong> total reviews
            </div>
        </div>
    </div>

    @if(count($recentFeedback) > 0)
        <div style="margin-top: 30px;">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; color: #212529;">Recent Feedback</h3>
            @foreach($recentFeedback as $feedback)
            <div class="feedback-card">
                <div class="feedback-header">
                    <div>
                        <strong>{{ $feedback['student_name'] }}</strong>
                        <div style="font-size: 13px; color: #6c757d;">
                            {{ $feedback['subject'] }} • {{ $feedback['session_code'] }}
                        </div>
                        <div style="font-size: 12px; color: #6c757d; margin-top: 2px;">
                            {{ $feedback['date'] }}
                        </div>
                    </div>
                    <div class="feedback-rating">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $feedback['rating'])
                                ⭐
                            @else
                                ☆
                            @endif
                        @endfor
                    </div>
                </div>
                <div class="feedback-comment">
                    {{ $feedback['comment'] }}
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="empty-state" style="padding: 40px 20px;">
            <div class="empty-icon">💬</div>
            <p>No feedback yet. Keep up the great work!</p>
        </div>
    @endif
</div>

<style>
.content-panel {
    /* background-color: removed */
    padding: 20px;               /* keeps spacing inside */
    border: 1px solid #01264dff;   /* blue border */
    border-radius: 20px;          /* rounded corners */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* subtle shadow */
}

.notification-card-mini {
    background: white;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 12px;
    border-left: 4px solid #dee2e6;
    transition: all 0.3s ease;
}

.notification-card-mini.unread {
    background: #f0f7ff;
    border-left-color: #2d5f8d;
}

.notification-card-mini:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.notification-icon-mini {
    font-size: 24px;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 50%;
    flex-shrink: 0;
}

.notification-card-mini.unread .notification-icon-mini {
    background: #e3f2fd;
}

.rating-summary {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    padding: 30px;
}

.rating-overview {
    display: flex;
    align-items: center;
    gap: 30px;
}

.rating-score {
    text-align: center;
}

.rating-number {
    font-size: 48px;
    font-weight: 700;
    color: #FFA500;
    display: block;
    line-height: 1;
}

.rating-stars {
    font-size: 24px;
    margin-top: 8px;
}

.rating-meta {
    font-size: 16px;
    color: #6c757d;
}

.feedback-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 12px;
}

.feedback-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}

.feedback-rating {
    font-size: 16px;
}

.feedback-comment {
    font-size: 14px;
    color: #495057;
    line-height: 1.6;
}

@media (max-width: 768px) {
    .rating-overview {
        flex-direction: column;
        gap: 16px;
    }
    
    .rating-number {
        font-size: 36px;
    }
    
    .rating-stars {
        font-size: 20px;
    }
}
</style>
@endsection
@php
use Illuminate\Support\Str;
@endphp

@extends('tutor.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Tutor Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">📊 Welcome, {{ Str::title($tutor->first_name) }}!</h1>
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

<!-- Upcoming Sessions -->
<div class="content-panel">
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #212529;">📅 Upcoming Sessions</h2>
    
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
            <a href="{{ route('tutor.sessions.index', ['tab' => 'all']) }}" class="btn btn-primary" style="margin-top: 20px;">Find Sessions to Teach</a>
        </div>
    @endif
</div>

<!-- Recent Announcements -->
<div class="content-panel">
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #212529;">📢 Recent Announcements</h2>
    
    @if($recentAnnouncements->count() > 0)
        @foreach($recentAnnouncements as $announcement)
        <div class="announcement-card {{ strtolower($announcement->priority) }}">
            <div class="announcement-header">
                <div>
                    <div class="announcement-title">{{ $announcement->title }}</div>
                    <div class="announcement-meta">
                        <span class="badge badge-{{ $announcement->priority === 'Urgent' ? 'danger' : ($announcement->priority === 'High' ? 'warning' : 'info') }}">
                            {{ $announcement->priority }}
                        </span>
                        <span>{{ $announcement->category }}</span>
                        <span>{{ $announcement->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
            <div class="announcement-content">
                {{ Str::limit($announcement->content, 200) }}
            </div>
        </div>
        @endforeach

        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('tutor.announcements') }}" class="btn btn-secondary">View All Announcements</a>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">📢</div>
            <p>No announcements available</p>
        </div>
    @endif
</div>
@endsection
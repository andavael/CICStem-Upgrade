@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">📊 Dashboard & Reports</h1>
    <div class="page-actions">
        <form action="{{ route('admin.dashboard.export') }}" method="GET" style="display: inline-flex; align-items: center; gap: 10px;">
            <select name="format" class="export-select">
                <option value="csv">📄 CSV Format</option>
                <option value="pdf">📑 PDF Format</option>
            </select>
            <button type="submit" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Export Report
            </button>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card stat-card-animated">
        <div class="stat-icon">👨‍🎓</div>
        <div class="stat-content">
            <div class="stat-label">Total Students</div>
            <div class="stat-value">{{ $stats['total_students'] }}</div>
            <div class="stat-description">✅ {{ $stats['active_students'] }} active</div>
        </div>
    </div>
    
    <div class="stat-card stat-card-animated success">
        <div class="stat-icon">👨‍🏫</div>
        <div class="stat-content">
            <div class="stat-label">Total Tutors</div>
            <div class="stat-value">{{ $stats['total_tutors'] }}</div>
            <div class="stat-description">✅ {{ $stats['approved_tutors'] }} approved • ⏳ {{ $stats['pending_tutors'] }} pending</div>
        </div>
    </div>
    
    <div class="stat-card stat-card-animated warning">
        <div class="stat-icon">📚</div>
        <div class="stat-content">
            <div class="stat-label">Total Sessions</div>
            <div class="stat-value">{{ $stats['total_sessions'] }}</div>
            <div class="stat-description">🔜 {{ $stats['upcoming_sessions'] }} upcoming</div>
        </div>
    </div>
    
    <div class="stat-card stat-card-animated info">
        <div class="stat-icon">📢</div>
        <div class="stat-content">
            <div class="stat-label">Announcements</div>
            <div class="stat-value">{{ $stats['total_announcements'] }}</div>
            <div class="stat-description">Active announcements</div>
        </div>
    </div>
</div>

<!-- Popular Subjects -->
<div class="content-panel panel-enhanced">
    <div class="panel-header">
        <h2 class="panel-title">🎯 Popular Subjects</h2>
        <span class="panel-badge">Top Performing</span>
    </div>
    
    @if($popularSubjects->count() > 0)
    <div class="table-responsive">
        <table class="data-table enhanced-table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Total Sessions</th>
                    <th>Popularity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($popularSubjects as $subject)
                <tr class="table-row-hover">
                    <td>
                        <div class="subject-cell">
                            <span class="subject-icon">📖</span>
                            <strong>{{ $subject->subject }}</strong>
                        </div>
                    </td>
                    <td>
                        <span class="session-count">{{ $subject->session_count }} sessions</span>
                    </td>
                    <td>
                        <div class="progress-bar-container">
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" style="width: {{ ($subject->session_count / $popularSubjects->max('session_count')) * 100 }}%;"></div>
                            </div>
                            <span class="progress-label">{{ round(($subject->session_count / $popularSubjects->max('session_count')) * 100) }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state-enhanced">
        <div class="empty-icon">📊</div>
        <p>No session data available</p>
    </div>
    @endif
</div>

<!-- Active Tutors -->
<div class="content-panel panel-enhanced">
    <div class="panel-header">
        <h2 class="panel-title">⭐ Active Tutors</h2>
        <span class="panel-badge success">{{ $activeTutors->count() }} Online</span>
    </div>
    
    @if($activeTutors->count() > 0)
    <div class="table-responsive">
        <table class="data-table enhanced-table">
            <thead>
                <tr>
                    <th>Tutor</th>
                    <th>Email</th>
                    <th>Year Level</th>
                    <th>Sessions Conducted</th>
                    <th>GWA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activeTutors as $tutor)
                <tr class="table-row-hover">
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar">{{ substr($tutor->full_name, 0, 1) }}</div>
                            <strong>{{ $tutor->full_name }}</strong>
                        </div>
                    </td>
                    <td>{{ $tutor->email }}</td>
                    <td><span class="year-badge">{{ $tutor->year_level }}</span></td>
                    <td>
                        <span class="session-badge">{{ $tutor->sessions_count }} sessions</span>
                    </td>
                    <td><span class="badge badge-success gwa-badge">{{ $tutor->gwa }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state-enhanced">
        <div class="empty-icon">👨‍🏫</div>
        <p>No active tutors</p>
    </div>
    @endif
</div>

<!-- Recent Sessions -->
<div class="content-panel panel-enhanced">
    <div class="panel-header">
        <h2 class="panel-title">🕒 Recent Sessions</h2>
        <a href="{{ route('admin.sessions.index') }}" class="panel-link">View All →</a>
    </div>
    
    @if($recentSessions->count() > 0)
    <div class="table-responsive">
        <table class="data-table enhanced-table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Date & Time</th>
                    <th>Tutor</th>
                    <th>Year Level</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentSessions as $session)
                <tr class="table-row-hover">
                    <td>
                        <div class="subject-cell">
                            <span class="subject-icon">📚</span>
                            <strong>{{ $session->subject }}</strong>
                        </div>
                    </td>
                    <td>
                        <div class="date-cell">
                            <div class="date-primary">{{ \Carbon\Carbon::parse($session->session_date)->format('M d, Y') }}</div>
                            <div class="date-secondary">⏰ {{ $session->session_time }}</div>
                        </div>
                    </td>
                    <td>{{ $session->tutor ? $session->tutor->full_name : 'N/A' }}</td>
                    <td><span class="year-badge">{{ $session->year_level }}</span></td>
                    <td>
                        @if($session->status === 'Scheduled')
                            <span class="badge badge-info status-badge">📅 {{ $session->status }}</span>
                        @elseif($session->status === 'Completed')
                            <span class="badge badge-success status-badge">✅ {{ $session->status }}</span>
                        @elseif($session->status === 'Cancelled')
                            <span class="badge badge-danger status-badge">❌ {{ $session->status }}</span>
                        @else
                            <span class="badge badge-warning status-badge">⚠️ {{ $session->status }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state-enhanced">
        <div class="empty-icon">📅</div>
        <p>No sessions found</p>
    </div>
    @endif
</div>

<!-- Student Distribution by Year Level -->
<div class="content-panel panel-enhanced">
    <div class="panel-header">
        <h2 class="panel-title">📊 Student Distribution by Year Level</h2>
        <span class="panel-badge">Overview</span>
    </div>
    
    @if($studentsByYear->count() > 0)
    <div class="year-distribution-grid">
        @foreach($studentsByYear as $yearData)
        <div class="year-card">
            <div class="year-icon">🎓</div>
            <div class="year-label">{{ $yearData->year_level }}</div>
            <div class="year-count">{{ $yearData->count }}</div>
            <div class="year-description">students</div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state-enhanced">
        <div class="empty-icon">📊</div>
        <p>No student data available</p>
    </div>
    @endif
</div>

<style>
/* Enhanced Stat Cards */
.stat-card-animated {
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card-animated:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(45, 95, 141, 0.3);
}

.stat-icon {
    font-size: 48px;
    opacity: 0.9;
}

.stat-content {
    flex: 1;
}

/* Export Select */
.export-select {
    padding: 12px 16px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    cursor: pointer;
    transition: border-color 0.3s ease;
}

.export-select:hover {
    border-color: #2d5f8d;
}

/* Panel Enhancements */
.panel-enhanced {
    border: 1px solid #e9ecef;
    transition: box-shadow 0.3s ease;
}

.panel-enhanced:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}

.panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid #f0f2f5;
}

.panel-title {
    font-size: 20px;
    font-weight: 600;
    margin: 0;
    color: #212529;
}

.panel-badge {
    padding: 6px 14px;
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    color: #1e3a5f;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.panel-badge.success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
}

.panel-link {
    color: #2d5f8d;
    text-decoration: none;
    font-weight: 500;
    font-size: 14px;
    transition: color 0.3s ease;
}

.panel-link:hover {
    color: #1e3a5f;
}

/* Enhanced Table */
.table-responsive {
    overflow-x: auto;
}

.enhanced-table {
    margin-bottom: 0;
}

.enhanced-table thead {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.table-row-hover {
    transition: background-color 0.2s ease;
}

.table-row-hover:hover {
    background: #f0f7ff !important;
}

/* User Cell */
.user-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2d5f8d 0%, #1e3a5f 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}

/* Subject Cell */
.subject-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}

.subject-icon {
    font-size: 20px;
}

/* Date Cell */
.date-cell {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.date-primary {
    font-weight: 600;
    color: #212529;
}

.date-secondary {
    font-size: 12px;
    color: #6c757d;
}

/* Progress Bar */
.progress-bar-container {
    display: flex;
    align-items: center;
    gap: 12px;
}

.progress-bar-bg {
    flex: 1;
    background: #e9ecef;
    height: 10px;
    border-radius: 5px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #2d5f8d 0%, #5a9fd4 100%);
    border-radius: 5px;
    transition: width 0.3s ease;
}

.progress-label {
    font-size: 12px;
    font-weight: 600;
    color: #2d5f8d;
    min-width: 45px;
}

/* Badges */
.year-badge {
    padding: 4px 12px;
    background: #e3f2fd;
    color: #1e3a5f;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 500;
}

.session-badge {
    padding: 4px 10px;
    background: #fff3cd;
    color: #856404;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.session-count {
    color: #2d5f8d;
    font-weight: 600;
}

.gwa-badge {
    font-size: 13px;
    padding: 6px 14px;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Year Distribution Grid */
.year-distribution-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
}

.year-card {
    text-align: center;
    padding: 28px 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 16px;
    border: 2px solid #dee2e6;
    transition: all 0.3s ease;
}

.year-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    border-color: #2d5f8d;
}

.year-icon {
    font-size: 36px;
    margin-bottom: 12px;
}

.year-label {
    font-size: 14px;
    color: #6c757d;
    margin-bottom: 8px;
    font-weight: 500;
}

.year-count {
    font-size: 36px;
    font-weight: 700;
    color: #2d5f8d;
    margin-bottom: 4px;
}

.year-description {
    font-size: 13px;
    color: #6c757d;
}

/* Empty State Enhanced */
.empty-state-enhanced {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    font-size: 64px;
    margin-bottom: 16px;
    opacity: 0.5;
}

.empty-state-enhanced p {
    color: #6c757d;
    font-size: 16px;
    margin: 0;
}

/* Button Enhancement */
.btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* Responsive */
@media (max-width: 768px) {
    .stat-card-animated {
        flex-direction: column;
        text-align: center;
    }
    
    .panel-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .year-distribution-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .year-distribution-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
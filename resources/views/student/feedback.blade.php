@extends('student.layouts.app')

@section('title', 'Feedback')
@section('page-title', 'Feedback')

@section('content')
<div class="page-header">
    <h1 class="page-title">Session Feedback</h1>
</div>

<div class="content-panel">
    <div style="background: #e3f2fd; padding: 16px 20px; border-radius: 8px; margin-bottom: 24px; border-left: 4px solid #2d5f8d;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 20px;">ℹ️</span>
            <span style="color: #1e3a5f; font-size: 14px; font-weight: 500;">
                Provide feedback for completed sessions you attended. Your feedback helps improve our tutoring services.
            </span>
        </div>
    </div>

    @if($completedSessions->count() > 0)
    <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 20px; color: #212529;">Completed Sessions</h2>
    
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Session Details</th>
                    <th>Date</th>
                    <th>Tutor</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($completedSessions as $session)
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #212529;">{{ $session->subject }}</div>
                        <div style="font-size: 13px; color: #6c757d; margin-top: 4px;">
                            <code style="background: #f8f9fa; padding: 2px 6px; border-radius: 4px;">{{ $session->session_code }}</code>
                        </div>
                    </td>
                    <td>
                        <div>{{ \Carbon\Carbon::parse($session->session_date)->format('M d, Y') }}</div>
                        <div style="font-size: 12px; color: #6c757d;">{{ $session->session_time }}</div>
                    </td>
                    <td>{{ $session->tutor ? $session->tutor->full_name : 'N/A' }}</td>
                    <td>
                        @if(in_array($session->id, $feedbackGiven))
                            <span class="badge badge-success">✓ Feedback Given</span>
                        @else
                            <span class="badge badge-warning">Pending Feedback</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-btns">
                            @if(in_array($session->id, $feedbackGiven))
                                <a href="{{ route('student.feedback.show', $session->id) }}" class="btn btn-view btn-sm">
                                    View Feedback
                                </a>
                            @else
                                <a href="{{ route('student.feedback.create', $session->id) }}" class="btn btn-primary btn-sm">
                                    Give Feedback
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
        <div class="empty-state-icon">⭐</div>
        <h3>No Completed Sessions</h3>
        <p>You don't have any completed sessions to provide feedback for yet.</p>
        <a href="{{ route('student.available-sessions.index') }}" class="btn btn-primary" style="margin-top: 16px;">
            Browse Available Sessions
        </a>
    </div>
    @endif
</div>

<style>
.data-table {
    width: 100%;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    border-collapse: collapse;
}

.data-table thead {
    background: linear-gradient(135deg, #2d5f8d 0%, #1e3a5f 100%);
}

.data-table thead th {
    padding: 16px;
    text-align: left;
    font-weight: 600;
    font-size: 14px;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.data-table tbody tr {
    border-bottom: 1px solid #dee2e6;
    transition: background-color 0.2s ease;
}

.data-table tbody tr:hover {
    background-color: #f8f9fa;
}

.data-table tbody td {
    padding: 16px;
    color: #495057;
    font-size: 14px;
    vertical-align: middle;
}

.action-btns {
    display: flex;
    gap: 8px;
}
</style>
@endsection
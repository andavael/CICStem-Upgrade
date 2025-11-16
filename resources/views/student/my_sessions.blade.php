@extends('student.layouts.app')

@section('title', 'My Sessions')
@section('page-title', 'My Sessions')

@section('content')
<div class="page-header">
    <h1 class="page-title">My Enrolled Sessions</h1>
    <div class="page-actions">
        <a href="{{ route('student.available-sessions.index') }}" class="btn btn-primary">
            Browse More Sessions
        </a>
    </div>
</div>

<div class="content-panel">
    <!-- Filter Bar -->
    <form action="{{ route('student.my-sessions.index') }}" method="GET" class="filter-bar">
        <select name="status" class="form-control" onchange="this.form.submit()">
            <option value="">All Sessions</option>
            <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>📅 Upcoming</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>✅ Completed</option>
        </select>
        
        <input type="text" name="search" class="form-control" placeholder="Search sessions..." value="{{ request('search') }}" style="flex: 1; min-width: 250px;">
        
        <button type="submit" class="btn btn-primary">Search</button>
        
        @if(request()->hasAny(['status', 'search']))
        <a href="{{ route('student.my-sessions.index') }}" class="btn btn-secondary">Clear</a>
        @endif
    </form>
    
    @if($sessions->count() > 0)
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Session Details</th>
                    <th>Date & Time</th>
                    <th>Tutor</th>
                    <th>Status</th>
                    <th>Attendance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessions as $session)
                <tr>
                    <td>{{ $session->subject }}</td>
                    <td>{{ \Carbon\Carbon::parse($session->session_date)->format('M d, Y') }}</td>
                    <td>{{ $session->tutor ? $session->tutor->full_name : 'N/A' }}</td>
                    <td>
                        @if($session->status === 'Completed')
                            <span class="badge badge-success">Completed</span>
                        @elseif($session->status === 'Scheduled')
                            <span class="badge badge-info">Scheduled</span>
                        @else
                            <span class="badge badge-warning">{{ $session->status }}</span>
                        @endif
                    </td>
                    <td>
                        @if($session->pivot->attendance_status === 'Present')
                            <span class="badge badge-success">Present</span>
                        @elseif($session->pivot->attendance_status === 'Absent')
                            <span class="badge badge-danger">Absent</span>
                        @else
                            <span class="badge badge-secondary">Pending</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <p>No enrollment history available.</p>
    </div>
    @endif
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
    color: #212529;
    margin-bottom: 8px;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #2d5f8d;
    box-shadow: 0 0 0 3px rgba(45, 95, 141, 0.1);
}

.form-control:disabled {
    background: #e9ecef;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
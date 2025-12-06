@extends('admin.layouts.app')

@section('title', 'Session Details')

@section('content')
<div class="page-header">
    <h1 class="page-title">Session Details</h1>
    <div class="page-actions">
        <a href="{{ route('admin.sessions.index') }}" class="btn btn-action btn-secondary-action">Back to Sessions</a>
        <a href="{{ route('admin.sessions.edit', $session) }}" class="btn btn-action btn-primary-action">Edit Session</a>
        <form action="{{ route('admin.sessions.destroy', $session) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this session?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-action btn-danger-action">Delete</button>
        </form>
    </div>
</div>

<div class="content-panel" style="background: #ffffff; border: 2px solid #dee2e6; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
    <div style="background: linear-gradient(135deg, #2d5f8d 0%, #1a4366 100%); padding: 20px; border-radius: 8px 8px 0 0; margin: -1px -1px 24px -1px;">
        <h2 style="font-size: 22px; font-weight: 700; margin: 0; color: #ffffff;">Session Information</h2>
    </div>
    
    <div style="padding: 0 24px 24px 24px;">
    <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 20px;">Session Information</h2>
    
    <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 20px;">Session Information</h2>
    
    <div style="display: grid; grid-template-columns: 200px 1fr; gap: 16px; max-width: 800px;">
        <div style="font-weight: 600; color: #495057;">Session Code:</div>
        <div><strong>{{ $session->session_code }}</strong></div>
        
        <div style="font-weight: 600; color: #495057;">Subject:</div>
        <div>{{ $session->subject }}</div>
        
        <div style="font-weight: 600; color: #495057;">Date:</div>
        <div>{{ \Carbon\Carbon::parse($session->session_date)->format('F d, Y (l)') }}</div>
        
        <div style="font-weight: 600; color: #495057;">Time:</div>
        <div>{{ $session->session_time }}</div>
        
        <div style="font-weight: 600; color: #495057;">Tutor:</div>
        <div>{{ $session->tutor ? $session->tutor->full_name : 'Not assigned' }}</div>
        
        <div style="font-weight: 600; color: #495057;">Year Level:</div>
        <div>{{ $session->year_level }}</div>
        
        <div style="font-weight: 600; color: #495057;">Capacity:</div>
        <div>{{ $enrolledCount }} / {{ $session->capacity }} students enrolled</div>
        
        <div style="font-weight: 600; color: #495057;">Status:</div>
        <div>
            @if($session->status === 'Scheduled')
                <span class="badge badge-info">{{ $session->status }}</span>
            @elseif($session->status === 'Ongoing')
                <span class="badge badge-warning">{{ $session->status }}</span>
            @elseif($session->status === 'Completed')
                <span class="badge badge-success">{{ $session->status }}</span>
            @else
                <span class="badge badge-danger">{{ $session->status }}</span>
            @endif
        </div>
        
        <div style="font-weight: 600; color: #495057;">Google Meet Link:</div>
        <div><a href="{{ $session->google_meet_link }}" target="_blank" class="btn btn-action btn-primary-action btn-sm">Open Meeting</a></div>
        
        @if($session->description)
        <div style="font-weight: 600; color: #495057;">Description:</div>
        <div>{{ $session->description }}</div>
        @endif
        
        <div style="font-weight: 600; color: #495057;">Created:</div>
        <div>{{ \Carbon\Carbon::parse($session->created_at)->format('F d, Y h:i A') }}</div>
    </div>
    </div>
</div>

<div class="content-panel" style="background: #ffffff; border: 2px solid #dee2e6; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
    <div style="background: linear-gradient(135deg, #2d5f8d 0%, #1a4366 100%); padding: 20px; border-radius: 8px 8px 0 0; margin: -1px -1px 24px -1px;">
        <h2 style="font-size: 22px; font-weight: 700; margin: 0; color: #ffffff;">Enrolled Students ({{ $enrolledCount }})</h2>
    </div>
    
    <div style="padding: 0 24px 24px 24px;">
    
    @if($session->students->count() > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th>SR Code</th>
                <th>Name</th>
                <th>Email</th>
                <th>Year Level</th>
                <th>Enrolled At</th>
                <th>Attendance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($session->students as $student)
            <tr>
                <td><strong>{{ $student->sr_code }}</strong></td>
                <td>{{ $student->full_name }}</td>
                <td>{{ $student->email }}</td>
                <td>{{ $student->year_level }}</td>
                <td>{{ \Carbon\Carbon::parse($student->pivot->enrolled_at)->format('M d, Y h:i A') }}</td>
                <td>
                    @if($student->pivot->attendance_status === 'Present')
                        <span class="badge badge-success">Present</span>
                    @elseif($student->pivot->attendance_status === 'Absent')
                        <span class="badge badge-danger">Absent</span>
                    @else
                        <span class="badge badge-secondary">Pending</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-state">No students enrolled yet</div>
    @endif
    </div>
</div>

<style>
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

.btn-action.btn-sm {
    padding: 8px 16px;
    font-size: 13px;
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
</style>
@endsection
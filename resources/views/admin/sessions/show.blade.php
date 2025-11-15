@extends('admin.layouts.app')

@section('title', 'Session Details')

@section('content')
<div class="page-header">
    <h1 class="page-title">Session Details</h1>
    <div class="page-actions">
        <a href="{{ route('admin.sessions.index') }}" class="btn btn-secondary">Back to Sessions</a>
        <a href="{{ route('admin.sessions.edit', $session) }}" class="btn btn-edit">Edit Session</a>
        <form action="{{ route('admin.sessions.destroy', $session) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this session?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-delete">Delete</button>
        </form>
    </div>
</div>

<div class="content-panel">
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
        <div><a href="{{ $session->google_meet_link }}" target="_blank" class="btn btn-view btn-sm">Open Meeting</a></div>
        
        @if($session->description)
        <div style="font-weight: 600; color: #495057;">Description:</div>
        <div>{{ $session->description }}</div>
        @endif
        
        <div style="font-weight: 600; color: #495057;">Created:</div>
        <div>{{ \Carbon\Carbon::parse($session->created_at)->format('F d, Y h:i A') }}</div>
    </div>
</div>

<div class="content-panel">
    <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 20px;">Enrolled Students ({{ $enrolledCount }})</h2>
    
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
@endsection
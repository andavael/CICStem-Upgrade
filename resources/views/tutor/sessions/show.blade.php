@extends('tutor.layouts.app')

@section('title', 'Session Details')
@section('page-title', 'Session Details')

@section('content')
<div class="page-header">
    <h1 class="page-title">📖 Session Details</h1>
    <div class="page-actions">
        <a href="{{ route('tutor.sessions.index') }}" class="btn btn-secondary">
            Back to Sessions
        </a>
    </div>
</div>

<!-- Session Information -->
<div class="content-panel">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-size: 24px; font-weight: 600; margin: 0; color: #212529;">Session Information</h2>
        
        <!-- Status Action Buttons -->
        <div style="display: flex; gap: 10px;">
            @if($session->status === 'Scheduled')
                <form action="{{ route('tutor.sessions.updateStatus', $session->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="Ongoing">
                    <button type="submit" class="btn btn-warning" onclick="return confirm('Mark this session as ongoing?')">
                        ▶️ Start Session
                    </button>
                </form>
            @elseif($session->status === 'Ongoing')
                <form action="{{ route('tutor.sessions.updateStatus', $session->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="Completed">
                    <button type="submit" class="btn btn-success" onclick="return confirm('Mark this session as completed?')">
                        ✓ Complete Session
                    </button>
                </form>
            @endif
        </div>
    </div>
    
    <div class="form-grid">
        <div class="form-group">
            <label>Subject</label>
            <div style="padding: 12px 16px; background: #f8f9fa; border-radius: 8px; font-weight: 500;">
                {{ $session->subject }}
            </div>
        </div>

        <div class="form-group">
            <label>Session Code</label>
            <div style="padding: 12px 16px; background: #f8f9fa; border-radius: 8px; font-family: 'Courier New', monospace;">
                {{ $session->session_code }}
            </div>
        </div>

        <div class="form-group">
            <label>Date</label>
            <div style="padding: 12px 16px; background: #f8f9fa; border-radius: 8px;">
                {{ \Carbon\Carbon::parse($session->session_date)->format('F d, Y') }}
            </div>
        </div>

        <div class="form-group">
            <label>Time</label>
            <div style="padding: 12px 16px; background: #f8f9fa; border-radius: 8px;">
                {{ $session->session_time }}
            </div>
        </div>

        <div class="form-group">
            <label>Year Level</label>
            <div style="padding: 12px 16px; background: #f8f9fa; border-radius: 8px;">
                {{ $session->year_level }}
            </div>
        </div>

        <div class="form-group">
            <label>Status</label>
            <div style="padding: 12px 16px; background: #f8f9fa; border-radius: 8px;">
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
        </div>
    </div>

    <div class="form-group">
        <label>Capacity</label>
        <div style="padding: 12px 16px; background: #f8f9fa; border-radius: 8px;">
            <strong>{{ $session->students->count() }}</strong> enrolled out of <strong>{{ $session->capacity }}</strong> capacity
            @if($session->students->count() >= $session->capacity)
                <span class="badge badge-danger" style="margin-left: 10px;">Full</span>
            @else
                <span class="badge badge-success" style="margin-left: 10px;">{{ $session->capacity - $session->students->count() }} slots available</span>
            @endif
        </div>
    </div>

    <div class="form-group">
        <label>Google Meet Link</label>
        <div style="padding: 12px 16px; background: #f8f9fa; border-radius: 8px;">
            <a href="{{ $session->google_meet_link }}" target="_blank" style="color: #2d5f8d; text-decoration: underline;">
                {{ $session->google_meet_link }}
            </a>
        </div>
    </div>

    @if($session->description)
    <div class="form-group">
        <label>Description</label>
        <div style="padding: 12px 16px; background: #f8f9fa; border-radius: 8px; line-height: 1.6;">
            {{ $session->description }}
        </div>
    </div>
    @endif
</div>

<!-- Pending Student Applications -->
@if($pendingApplications->count() > 0)
<div class="content-panel" style="border-left: 4px solid #ffc107;">
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #212529;">
        ⏳ Pending Applications ({{ $pendingApplications->count() }})
    </h2>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>SR Code</th>
                    <th>Email</th>
                    <th>Year Level</th>
                    <th>Applied At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingApplications as $student)
                <tr>
                    <td><strong>{{ $student->full_name }}</strong></td>
                    <td><code style="background: #f8f9fa; padding: 4px 8px; border-radius: 4px;">{{ $student->sr_code }}</code></td>
                    <td>{{ $student->email }}</td>
                    <td>{{ $student->year_level }}</td>
                    <td>{{ \Carbon\Carbon::parse($student->pivot->enrolled_at)->format('M d, Y h:i A') }}</td>
                    <td>
                        <div class="action-btns">
                            <form action="{{ route('tutor.sessions.approve', [$session->id, $student->id]) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-approve btn-sm">Approve</button>
                            </form>
                            <form action="{{ route('tutor.sessions.reject', [$session->id, $student->id]) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-delete btn-sm" onclick="return confirm('Are you sure you want to reject this application?')">Reject</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Manually Add Student -->
@if(!$session->isFull())
<div class="content-panel">
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #212529;">➕ Add Student Manually</h2>
    
    <form action="{{ route('tutor.sessions.addStudent', $session->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Select Student <span style="color: #dc3545;">*</span></label>
            <select name="student_id" class="form-control" required>
                <option value="">-- Select Student --</option>
                @foreach(\App\Models\Student::where('status', 'Active')->orderBy('first_name')->get() as $availableStudent)
                    @if(!$session->students->contains($availableStudent->id))
                        <option value="{{ $availableStudent->id }}">{{ $availableStudent->full_name }} ({{ $availableStudent->sr_code }})</option>
                    @endif
                @endforeach
            </select>
        </div>

        <div style="text-align: right;">
            <button type="submit" class="btn btn-primary">Add Student</button>
        </div>
    </form>
</div>
@endif

<!-- Enrolled Students -->
<div class="content-panel">
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #212529;">
        👥 Enrolled Students ({{ $session->students->count() }})
    </h2>

    @if($session->students->count() > 0)
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>SR Code</th>
                    <th>Email</th>
                    <th>Year Level</th>
                    <th>Enrolled At</th>
                    <th>Attendance</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($session->students->where('pivot.attendance_status', '!=', 'Pending') as $student)
                <tr>
                    <td><strong>{{ $student->full_name }}</strong></td>
                    <td><code style="background: #f8f9fa; padding: 4px 8px; border-radius: 4px;">{{ $student->sr_code }}</code></td>
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
                    <td>
                        <form action="{{ route('tutor.sessions.attendance', [$session->id, $student->id]) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <select name="attendance_status" onchange="this.form.submit()" class="form-control" style="width: auto; padding: 6px 10px; font-size: 13px;">
                                <option value="Pending" {{ $student->pivot->attendance_status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Present" {{ $student->pivot->attendance_status === 'Present' ? 'selected' : '' }}>Present</option>
                                <option value="Absent" {{ $student->pivot->attendance_status === 'Absent' ? 'selected' : '' }}>Absent</option>
                            </select>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <div class="empty-icon">👥</div>
        <p>No students enrolled yet</p>
    </div>
    @endif
</div>

<style>
.btn-warning {
    background: #ffc107;
    color: #212529;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-warning:hover {
    background: #e0a800;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
}

.btn-success {
    background: #28a745;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-success:hover {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
}
</style>
@endsection
@extends('admin.layouts.app')

@section('title', 'User Management')

@section('content')
<div class="page-header">
    <h1 class="page-title">User Management</h1>
    <div class="page-actions">
        @if($tab === 'students')
        <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add Student
        </a>
        @else
        <a href="{{ route('admin.tutors.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add Tutor
        </a>
        @endif
    </div>
</div>

<div class="content-panel panel-enhanced">
    <!-- Enhanced Tabs -->
    <div class="tabs-enhanced">
        <button class="tab-btn-enhanced {{ $tab === 'students' ? 'active' : '' }}" onclick="window.location='{{ route('admin.users.index', ['tab' => 'students']) }}'">
            <span class="tab-icon"></span>
            <span>Students</span>
            @if(isset($students))
            <span class="tab-count">{{ $students->total() }}</span>
            @endif
        </button>
        <button class="tab-btn-enhanced {{ $tab === 'tutors' ? 'active' : '' }}" onclick="window.location='{{ route('admin.users.index', ['tab' => 'tutors']) }}'">
            <span class="tab-icon"></span>
            <span>Tutors</span>
            @if(isset($tutors))
            <span class="tab-count">{{ $tutors->total() }}</span>
            @endif
        </button>
    </div>

    @if($tab === 'students')
        <!-- Students Tab -->
        @if(isset($students) && $students->count() > 0)
        <div class="table-responsive">
            <table class="data-table enhanced-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>SR Code</th>
                        <th>Email</th>
                        <th>Year Level</th>
                        <th>Course</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr class="table-row-hover">
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar student-avatar">{{ substr($student->full_name, 0, 1) }}</div>
                                <div>
                                    <strong>{{ $student->full_name }}</strong>
                                </div>
                            </div>
                        </td>
                        <td>{{ $student->sr_code }}</td>
                        <td>{{ $student->email }}</td>
                        <td>
                            <span class="year-badge">
                                @php
                                    $yearLevel = $student->year_level;
                                    if (stripos($yearLevel, 'Fourth Year') !== false || stripos($yearLevel, '4th Year') !== false) {
                                        echo 'Fourth Year';
                                    } else {
                                        echo $yearLevel;
                                    }
                                @endphp
                            </span>
                        </td>
                        <td><span class="course-badge">{{ $student->course_program }}</span></td>
                        <td>
                            @if($student->status === 'Active')
                                <span class="badge badge-success status-badge">Active</span>
                            @else
                                <span class="badge badge-secondary status-badge">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-btns">
                                <a href="{{ route('admin.students.show', $student) }}" class="btn btn-primary btn-sm btn-icon-text">
                                    View
                                </a>
                                <form action="{{ route('admin.students.toggle-status', $student) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn {{ $student->status === 'Active' ? 'btn-delete' : 'btn-approve' }} btn-sm btn-icon-text">
                                        {{ $student->status === 'Active' ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="pagination">
            {{ $students->links() }}
        </div>
        @else
        <div class="empty-state-enhanced">
            <div class="empty-icon">👨‍🎓</div>
            <h3>No Students Yet</h3>
            <p>No student has registered yet. They will appear here once they sign up.</p>
        </div>
        @endif

    @else
        <!-- Tutors Tab -->
        @if(isset($tutors) && $tutors->count() > 0)
        <div class="table-responsive">
            <table class="data-table enhanced-table">
                <thead>
                    <tr>
                        <th>Tutor</th>
                        <th>SR Code</th>
                        <th>Email</th>
                        <th>Year Level</th>
                        <th>GWA</th>
                        <th>Status</th>
                        <th>Approval</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tutors as $tutor)
                    <tr class="table-row-hover">
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar tutor-avatar">{{ substr($tutor->full_name, 0, 1) }}</div>
                                <div>
                                    <strong>{{ $tutor->full_name }}</strong>
                                </div>
                            </div>
                        </td>
                        <td>{{ $tutor->sr_code }}</td>
                        <td>{{ $tutor->email }}</td>
                        <td>
                            <span class="year-badge">
                                @php
                                    $yearLevel = $tutor->year_level;
                                    if (stripos($yearLevel, 'Fourth Year') !== false || stripos($yearLevel, '4th Year') !== false) {
                                        echo 'Fourth Year';
                                    } else {
                                        echo $yearLevel;
                                    }
                                @endphp
                            </span>
                        </td>
                        <td><span class="badge badge-success gwa-badge">{{ $tutor->gwa }}</span></td>
                        <td>
                            @if($tutor->status === 'Active')
                                <span class="badge badge-success status-badge">Active</span>
                            @else
                                <span class="badge badge-secondary status-badge">Inactive</span>
                            @endif
                        </td>
                        <td>
                            @if($tutor->is_approved)
                                <span class="badge badge-success status-badge">Approved</span>
                            @else
                                <span class="badge badge-warning status-badge">Pending</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-btns-tutor">
                                <a href="{{ route('admin.tutors.show', $tutor) }}" class="btn btn-primary btn-sm btn-icon-text">
                                    View
                                </a>
                                <div class="action-btns-row">
                                    @if(!$tutor->is_approved)
                                    <form action="{{ route('admin.tutors.approve', $tutor) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-approve btn-sm">Approve</button>
                                    </form>
                                    <form action="{{ route('admin.tutors.reject', $tutor) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-delete btn-sm">Reject</button>
                                    </form>
                                    @else
                                    <form action="{{ route('admin.tutors.toggle-status', $tutor) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn {{ $tutor->status === 'Active' ? 'btn-delete' : 'btn-approve' }} btn-sm">
                                            {{ $tutor->status === 'Active' ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="pagination">
            {{ $tutors->links() }}
        </div>
        @else
        <div class="empty-state-enhanced">
            <div class="empty-icon">👨‍🏫</div>
            <h3>No Tutors Yet</h3>
            <p>No tutor has registered yet. They will appear here once they sign up.</p>
        </div>
        @endif
    @endif
</div>

<style>
/* Enhanced Tabs */
.tabs-enhanced {
    display: flex;
    gap: 12px;
    border-bottom: 2px solid #dee2e6;
    margin-bottom: 30px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.tab-btn-enhanced {
    padding: 14px 28px;
    background: none;
    border: none;
    color: #6c757d;
    font-weight: 500;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    display: flex;
    align-items: center;
    gap: 10px;
    border-radius: 8px 8px 0 0;
    white-space: nowrap;
    flex-shrink: 0;
}

.tab-btn-enhanced:hover {
    color: #212529;
    background: #f8f9fa;
}

.tab-btn-enhanced.active {
    color: #1e3a5f;
    font-weight: 600;
    border-bottom-color: #2d5f8d;
    background: linear-gradient(180deg, #f0f7ff 0%, transparent 100%);
}

.tab-icon {
    font-size: 20px;
}

.tab-count {
    background: #2d5f8d;
    color: white;
    padding: 2px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.tab-btn-enhanced.active .tab-count {
    background: #FFA500;
}

/* User Cell Fixes */
.user-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
    flex-shrink: 0;
}

.user-avatar.student-avatar {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
}

.user-avatar.tutor-avatar {
    background: linear-gradient(135deg, #28a745 0%, #218838 100%);
}

/* Badges */
.code-badge {
    padding: 6px 12px;
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    color: #495057;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    font-family: 'Courier New', monospace;
}

.year-badge {
    padding: 4px 12px;
    background: #e3f2fd;
    color: #1e3a5f;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 500;
}

.course-badge {
    padding: 4px 10px;
    background: #fff3cd;
    color: #856404;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
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

/* Button with icon */
.btn-icon-text {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Action buttons for tutors - separate layout */
.action-btns-tutor {
    display: flex;
    flex-direction: column;
    gap: 8px;
    align-items: flex-start;
    width: 100%;
}

.action-btns-tutor > .btn {
    width: 100%;
}

.action-btns-row {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    width: 100%;
}

.action-btns-row form {
    flex: 1;
}

.action-btns-row .btn {
    width: 100%;
}

/* Regular action buttons for students */
.action-btns {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
}

/* Table Enhancements */
.panel-enhanced {
    border: 1px solid #e9ecef;
    transition: box-shadow 0.3s ease;
}

.panel-enhanced:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.enhanced-table {
    margin-bottom: 0;
    width: 100%;
}

.enhanced-table thead {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.enhanced-table thead th {
    white-space: nowrap;
    vertical-align: middle;
}

.enhanced-table tbody td {
    vertical-align: middle;
}

.table-row-hover {
    transition: background-color 0.2s ease;
}

.table-row-hover:hover {
    background: #f0f7ff !important;
}

/* Empty state enhancement */
.empty-state-enhanced {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    font-size: 64px;
    margin-bottom: 16px;
    opacity: 0.5;
}

.empty-state-enhanced h3 {
    font-size: 20px;
    font-weight: 600;
    color: #495057;
    margin: 12px 0 8px 0;
}

.empty-state-enhanced p {
    color: #6c757d;
    font-size: 16px;
    margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .tab-btn-enhanced {
        padding: 12px 20px;
    }
    
    .user-cell {
        flex-direction: row;
        align-items: center;
    }
    
    .action-btns {
        flex-direction: column;
        width: 100%;
    }
    
    .action-btns .btn,
    .action-btns form {
        width: 100%;
    }
    
    .action-btns .btn {
        width: auto;
        padding: 8px 16px;
    }
    
    .action-btns-tutor {
        width: 100%;
    }
    
    .action-btns-row {
        width: 100%;
    }
}
</style>
@endsection
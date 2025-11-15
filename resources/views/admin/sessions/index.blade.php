@extends('admin.layouts.app')

@section('title', 'Sessions Management')

@section('content')
<div class="page-header">
    <h1 class="page-title">📅 Sessions Management</h1>
    <div class="page-actions">
        <a href="{{ route('admin.sessions.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            Create New Session
        </a>
    </div>
</div>

<div class="content-panel panel-enhanced">
    <!-- Enhanced Filter Bar -->
    <form action="{{ route('admin.sessions.index') }}" method="GET" class="filter-bar-enhanced">
        <div class="filter-group">
            <label class="filter-label">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
                Filter by Status
            </label>
            <select name="status" class="filter-input" onchange="this.form.submit()">
                <option value="">All Sessions</option>
                <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>📅 Upcoming</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>✅ Completed</option>
            </select>
        </div>
        
        <div class="filter-group flex-1">
            <label class="filter-label">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                Search
            </label>
            <input type="text" name="search" class="filter-input" placeholder="Search by subject or code..." value="{{ request('search') }}">
        </div>
        
        <div class="filter-actions">
            <button type="submit" class="btn btn-primary btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                Search
            </button>
            
            @if(request()->hasAny(['status', 'search']))
            <a href="{{ route('admin.sessions.index') }}" class="btn btn-secondary btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
                Clear
            </a>
            @endif
        </div>
    </form>
    
    @if($sessions->count() > 0)
    <div class="table-responsive">
        <table class="data-table enhanced-table">
            <thead>
                <tr>
                    <th>Session Details</th>
                    <th>Date & Time</th>
                    <th>Tutor</th>
                    <th>Participants</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessions as $session)
                <tr class="table-row-hover">
                    <td>
                        <div class="session-details">
                            <div class="session-icon">📚</div>
                            <div>
                                <div class="session-subject">{{ $session->subject }}</div>
                                <div class="session-code">{{ $session->session_code }}</div>
                                <span class="year-badge-sm">{{ $session->year_level }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="date-cell">
                            <div class="date-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                {{ \Carbon\Carbon::parse($session->session_date)->format('M d, Y') }}
                            </div>
                            <div class="date-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                {{ $session->session_time }}
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($session->tutor)
                        <div class="tutor-cell">
                            <div class="tutor-avatar-sm">{{ substr($session->tutor->full_name, 0, 1) }}</div>
                            <span>{{ $session->tutor->full_name }}</span>
                        </div>
                        @else
                        <span class="text-muted">Not assigned</span>
                        @endif
                    </td>
                    <td>
                        <div class="capacity-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <strong>{{ $session->students->count() }}</strong> / {{ $session->capacity }}
                        </div>
                    </td>
                    <td>
                        @if($session->status === 'Scheduled')
                            <span class="badge badge-info status-badge-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                {{ $session->status }}
                            </span>
                        @elseif($session->status === 'Ongoing')
                            <span class="badge badge-warning status-badge-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="5 3 19 12 5 21 5 3"></polygon>
                                </svg>
                                {{ $session->status }}
                            </span>
                        @elseif($session->status === 'Completed')
                            <span class="badge badge-success status-badge-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                {{ $session->status }}
                            </span>
                        @else
                            <span class="badge badge-danger status-badge-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                                {{ $session->status }}
                            </span>
                        @endif
                    </td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('admin.sessions.show', $session) }}" class="btn btn-view btn-sm" title="View Details">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </a>
                            <a href="{{ route('admin.sessions.edit', $session) }}" class="btn btn-edit btn-sm" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </a>
                            @if($session->status !== 'Cancelled')
                            <form action="{{ route('admin.sessions.cancel', $session) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-delete btn-sm" onclick="return confirm('Cancel this session?')" title="Cancel">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="15" y1="9" x2="9" y2="15"></line>
                                        <line x1="9" y1="9" x2="15" y2="15"></line>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="pagination">
        {{ $sessions->links() }}
    </div>
    @else
    <div class="empty-state-enhanced">
        <div class="empty-icon">📅</div>
        <h3>No Sessions Found</h3>
        <p>{{ request()->hasAny(['status', 'search']) ? 'Try adjusting your filters' : 'Create your first session to get started' }}</p>
        @if(!request()->hasAny(['status', 'search']))
        <a href="{{ route('admin.sessions.create') }}" class="btn btn-primary" style="margin-top: 20px;">
            Create Session
        </a>
        @endif
    </div>
    @endif
</div>

<style>
/* Enhanced Filter Bar */
.filter-bar-enhanced {
    display: flex;
    gap: 16px;
    margin-bottom: 30px;
    flex-wrap: wrap;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    border: 1px solid #dee2e6;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-width: 200px;
}

.filter-group.flex-1 {
    flex: 1;
}

.filter-label {
    font-size: 13px;
    font-weight: 600;
    color: #495057;
    display: flex;
    align-items: center;
    gap: 6px;
}

.filter-input {
    padding: 10px 14px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    transition: all 0.3s ease;
}

.filter-input:focus {
    outline: none;
    border-color: #2d5f8d;
    box-shadow: 0 0 0 3px rgba(45, 95, 141, 0.1);
}

.filter-actions {
    display: flex;
    align-items: flex-end;
    gap: 8px;
}

/* Session Details Cell */
.session-details {
    display: flex;
    align-items: center;
    gap: 12px;
}

.session-icon {
    font-size: 28px;
    flex-shrink: 0;
}

.session-subject {
    font-weight: 600;
    font-size: 15px;
    color: #212529;
    margin-bottom: 4px;
}

.session-code {
    font-size: 12px;
    color: #6c757d;
    font-family: 'Courier New', monospace;
    margin-bottom: 4px;
}

.year-badge-sm {
    display: inline-block;
    padding: 2px 8px;
    background: #e3f2fd;
    color: #1e3a5f;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 500;
}

/* Tutor Cell */
.tutor-cell {
    display: flex;
    align-items: center;
    gap: 8px;
}

.tutor-avatar-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #28a745 0%, #218838 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 13px;
    flex-shrink: 0;
}

/* Capacity Badge */
.capacity-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%);
    border-radius: 8px;
    font-size: 13px;
    color: #856404;
}

/* Status Badge Large */
.status-badge-lg {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 600;
}

/* Date Cell Enhancement */
.date-cell {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.date-primary, .date-secondary {
    display: flex;
    align-items: center;
}

/* Text Muted */
.text-muted {
    color: #6c757d;
    font-style: italic;
}

/* Responsive */
@media (max-width: 768px) {
    .filter-bar-enhanced {
        flex-direction: column;
    }
    
    .filter-group {
        width: 100%;
        min-width: unset;
    }
    
    .filter-actions {
        width: 100%;
        justify-content: stretch;
    }
    
    .filter-actions .btn {
        flex: 1;
    }
    
    .session-details {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
@endsection
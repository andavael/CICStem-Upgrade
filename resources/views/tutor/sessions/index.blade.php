@extends('tutor.layouts.app')

@section('title', 'Sessions')
@section('page-title', 'Sessions')

@section('content')
<div class="page-header">
    <h1 class="page-title">📚 Sessions</h1>
</div>

<div class="content-panel panel-enhanced">
    <!-- Enhanced Tabs -->
    <div class="tabs-enhanced">
        <button class="tab-btn-enhanced {{ $tab === 'my' ? 'active' : '' }}" onclick="window.location='{{ route('tutor.sessions.index', ['tab' => 'my']) }}'">
            <span class="tab-icon">📖</span>
            <span>My Sessions</span>
            @if(isset($mySessions))
            <span class="tab-count">{{ $mySessions->total() }}</span>
            @endif
        </button>
        <button class="tab-btn-enhanced {{ $tab === 'all' ? 'active' : '' }}" onclick="window.location='{{ route('tutor.sessions.index', ['tab' => 'all']) }}'">
            <span class="tab-icon">📋</span>
            <span>All Sessions</span>
            @if(isset($allSessions))
            <span class="tab-count">{{ $allSessions->total() }}</span>
            @endif
        </button>
    </div>

    @if($tab === 'my')
        <!-- My Sessions Tab -->
        <div style="background: #e3f2fd; border-left: 4px solid #2d5f8d; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
            <strong>My Assigned Sessions:</strong> These are tutoring sessions you have been assigned to teach.
        </div>

        <!-- Time-based Filter Buttons -->
        <div style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
            <a href="{{ route('tutor.sessions.index', ['tab' => 'my', 'filter' => 'all']) }}" 
               class="btn btn-sm {{ !request('filter') || request('filter') === 'all' ? 'btn-primary' : 'btn-secondary' }}">
                All Sessions
            </a>
            <a href="{{ route('tutor.sessions.index', ['tab' => 'my', 'filter' => 'upcoming']) }}" 
               class="btn btn-sm {{ request('filter') === 'upcoming' ? 'btn-primary' : 'btn-secondary' }}">
                Upcoming
            </a>
            <a href="{{ route('tutor.sessions.index', ['tab' => 'my', 'filter' => 'ongoing']) }}" 
               class="btn btn-sm {{ request('filter') === 'ongoing' ? 'btn-primary' : 'btn-secondary' }}">
                Ongoing
            </a>
            <a href="{{ route('tutor.sessions.index', ['tab' => 'my', 'filter' => 'finished']) }}" 
               class="btn btn-sm {{ request('filter') === 'finished' ? 'btn-primary' : 'btn-secondary' }}">
                Finished
            </a>
        </div>

        <!-- Filter Bar -->
        <form action="{{ route('tutor.sessions.index', ['tab' => 'my']) }}" method="GET" class="filter-bar">
            <input type="hidden" name="tab" value="my">
            <input type="hidden" name="filter" value="{{ request('filter', 'all') }}">
            
            <select name="status" class="filter-input" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="Scheduled" {{ request('status') === 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="Ongoing" {{ request('status') === 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            
            <input type="text" name="search" class="filter-input" placeholder="Search by subject or code..." value="{{ request('search') }}" style="flex: 1;">
            
            <button type="submit" class="btn btn-primary btn-sm">Search</button>
            
            @if(request()->hasAny(['status', 'search']))
            <a href="{{ route('tutor.sessions.index', ['tab' => 'my', 'filter' => request('filter', 'all')]) }}" class="btn btn-secondary btn-sm">Clear</a>
            @endif
        </form>

        @if(isset($mySessions) && $mySessions->count() > 0)
        <div class="table-responsive">
            <table class="data-table enhanced-table">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Session Code</th>
                        <th>Date & Time</th>
                        <th>Year Level</th>
                        <th>Enrolled</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mySessions as $session)
                    <tr class="table-row-hover">
                        <td><strong>{{ $session->subject }}</strong></td>
                        <td><code style="background: #f8f9fa; padding: 4px 8px; border-radius: 4px;">{{ $session->session_code }}</code></td>
                        <td>
                            <div>{{ \Carbon\Carbon::parse($session->session_date)->format('M d, Y') }}</div>
                            <div style="font-size: 12px; color: #6c757d;">{{ $session->session_time }}</div>
                        </td>
                        <td>{{ $session->year_level }}</td>
                        <td>
                            <strong>{{ $session->students->count() }}</strong> / {{ $session->capacity }}
                        </td>
                        <td>
                            @if($session->status === 'Scheduled')
                                <span class="badge badge-info">{{ $session->status }}</span>
                            @elseif($session->status === 'Ongoing')
                                <span class="badge badge-warning">{{ $session->status }}</span>
                            @elseif($session->status === 'Completed')
                                <span class="badge badge-success">{{ $session->status }}</span>
                            @else
                                <span class="badge badge-danger">{{ $session->status }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('tutor.sessions.show', $session->id) }}" class="btn btn-view btn-sm btn-icon-text">
                                View Details
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $mySessions->appends(['tab' => 'my', 'filter' => request('filter', 'all'), 'status' => request('status'), 'search' => request('search')])->links() }}
        </div>
        @else
        <div class="empty-state-enhanced">
            <div class="empty-icon">📚</div>
            <h3>No Sessions Found</h3>
            <p>{{ request()->hasAny(['status', 'search']) ? 'Try adjusting your filters' : 'You haven\'t been assigned to any sessions yet' }}</p>
        </div>
        @endif

    @else
        <!-- Available Sessions Tab -->
        <!--<div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
            <strong>Available Sessions:</strong> These sessions match your tutor level preference (<strong>{{ $tutor->tutor_level_preference }}</strong>).
        </div> -->

        <!-- Filter Bar -->
        <form action="{{ route('tutor.sessions.index', ['tab' => 'all']) }}" method="GET" class="filter-bar">
            <input type="hidden" name="tab" value="all">
            <input type="text" name="search" class="filter-input" placeholder="Search by subject or code..." value="{{ request('search') }}" style="flex: 1;">
            
            <button type="submit" class="btn btn-primary btn-sm">Search</button>
            
            @if(request('search'))
            <a href="{{ route('tutor.sessions.index', ['tab' => 'all']) }}" class="btn btn-secondary btn-sm">Clear</a>
            @endif
        </form>

        @if(isset($allSessions) && $allSessions->count() > 0)
            @foreach($allSessions as $session)
            <div class="session-card">
                <div class="session-header">
                    <div>
                        <div class="session-title">{{ $session->subject }}</div>
                        <div style="font-size: 13px; color: #6c757d; margin-top: 4px;">
                            {{ $session->session_code }} • {{ $session->year_level }}
                        </div>
                    </div>
                    <span class="badge badge-success">Available</span>
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
                        Capacity: {{ $session->capacity }} students
                    </div>
                </div>

                @if($session->description)
                <div style="padding: 12px 0; color: #495057; font-size: 14px; border-top: 1px solid #dee2e6; margin-top: 12px;">
                    <strong>Description:</strong> {{ $session->description }}
                </div>
                @endif
            </div>
            @endforeach

            <div class="pagination">
                {{ $allSessions->appends(['tab' => 'all', 'search' => request('search')])->links() }}
            </div>
        @else
        <div class="empty-state-enhanced">
            <div class="empty-icon">📋</div>
            <h3>No Available Sessions</h3>
            <p>{{ request('search') ? 'No sessions match your search' : 'There are no sessions available for your level preference at the moment' }}</p>
            @if(request('search'))
            <a href="{{ route('tutor.sessions.index', ['tab' => 'all']) }}" class="btn btn-secondary" style="margin-top: 20px;">Clear Search</a>
            @endif
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

/* Panel Enhancement */
.panel-enhanced {
    border: 1px solid #e9ecef;
    transition: box-shadow 0.3s ease;
}

.panel-enhanced:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}

/* Table Enhancements */
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

/* Button with icon */
.btn-icon-text {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Filter input improvements */
.filter-input {
    padding: 10px 16px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    font-size: 14px;
    min-width: 150px;
}

/* Responsive */
@media (max-width: 768px) {
    .tab-btn-enhanced {
        padding: 12px 20px;
    }
    
    .filter-bar {
        flex-direction: column;
    }
    
    .filter-input {
        width: 100%;
        min-width: unset;
    }
}
</style>
@endsection
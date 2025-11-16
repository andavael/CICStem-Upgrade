@extends('student.layouts.app')

@section('title', 'Browse Sessions')
@section('page-title', 'Browse Sessions')

@section('content')
<div class="page-header">
    <h1 class="page-title">Browse Available Sessions</h1>
    <div class="page-actions">
        <a href="{{ route('student.my-sessions.index') }}" class="btn btn-secondary">
            My Enrolled Sessions
        </a>
    </div>
</div>

<div class="content-panel">

    <!-- Filter Bar (Search Only) -->
    <form action="{{ route('student.available-sessions.index') }}" method="GET" class="filter-bar">

        <input 
            type="text" 
            name="search" 
            class="form-control" 
            placeholder="Search by subject or code..." 
            value="{{ request('search') }}" 
            style="flex: 1; min-width: 250px;"
        >

        <button type="submit" class="btn btn-primary">Search</button>

        @if(request()->has('search'))
        <a href="{{ route('student.available-sessions.index') }}" class="btn btn-secondary">
            Clear Filters
        </a>
        @endif
    </form>

    <!-- Info Message -->
    <div style="background: #e3f2fd; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #2d5f8d;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 20px;">ℹ️</span>
            <span style="color: #1e3a5f; font-size: 14px; font-weight: 500;">
                Showing sessions for <strong>{{ $student->year_level }}</strong> students only (not inluding your enrolled sessions).
            </span>
        </div>
    </div>

    @if($sessions->count() > 0)
    <div class="session-cards-grid">
        @foreach($sessions as $session)
        <div class="session-card">

            <div class="session-card-header">
                <div>
                    <div class="session-card-subject">{{ $session->subject }}</div>
                    <div class="session-card-code">{{ $session->session_code }}</div>
                </div>
                <span class="badge badge-info">{{ $session->status }}</span>
            </div>

            <div class="session-card-body">

                <!-- Date -->
                <div class="session-info-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    {{ \Carbon\Carbon::parse($session->session_date)->format('F d, Y') }}
                </div>

                <!-- Time -->
                <div class="session-info-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    {{ $session->session_time }}
                </div>

                <!-- Tutor -->
                <div class="session-info-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    {{ $session->tutor ? $session->tutor->full_name : 'N/A' }}
                </div>

                @if($session->description)
                <div class="session-info-item" style="margin-top: 8px; font-size: 13px; color: #6c757d;">
                    {{ Str::limit($session->description, 80) }}
                </div>
                @endif

            </div>

            <div class="session-card-footer">
                <div class="capacity-indicator {{ $session->isFull() ? 'full' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <strong>{{ $session->students->count() }}</strong> / {{ $session->capacity }}
                    {{ $session->isFull() ? '(Full)' : '' }}
                </div>

                <div class="action-btns">
                    @if(in_array($session->id, $enrolledSessionIds))
                        <span class="badge badge-success" style="padding: 8px 14px;">✓ Enrolled</span>
                        <form action="{{ route('student.available-sessions.unenroll', $session->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-unenroll btn-sm" onclick="return confirm('Are you sure you want to unenroll?')">Unenroll</button>
                        </form>
                    @elseif($session->isFull())
                        <button class="btn btn-secondary btn-sm" disabled>Full</button>
                    @else
                        <form action="{{ route('student.available-sessions.enroll', $session->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-enroll btn-sm">Enroll Now</button>
                        </form>
                    @endif
                </div>

            </div>
        </div>
        @endforeach
    </div>

    <div class="pagination">
        {{ $sessions->links() }}
    </div>

    @else
    <div class="empty-state">
        <div class="empty-state-icon">🔍</div>
        <h3>No Sessions Found</h3>
        <p>{{ request()->has('search') ? 'Try adjusting your search query' : 'No available sessions for your year level at this time' }}</p>
        
        @if(request()->has('search'))
        <a href="{{ route('student.available-sessions.index') }}" class="btn btn-secondary" style="margin-top: 16px;">
            Clear Filters
        </a>
        @endif
    </div>
    @endif
</div>

<style>
.session-card {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.session-card-footer {
    margin-top: auto;
}

.form-control {
    padding: 10px 16px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #2d5f8d;
    box-shadow: 0 0 0 3px rgba(45, 95, 141, 0.1);
}
</style>
@endsection

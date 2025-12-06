@extends('admin.layouts.app')

@section('title', 'Announcements')

@section('content')
<div class="page-header">
    <h1 class="page-title">Announcements</h1>
    <div class="page-actions">
        <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;">
                <path d="M12 19l7-7 3 3-7 7-3-3z"></path>
                <path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"></path>
                <path d="M2 2l7.586 7.586"></path>
                <circle cx="11" cy="11" r="2"></circle>
            </svg>
            Post Announcement
        </a>
    </div>
</div>

<div class="content-panel panel-enhanced">
    <!-- Enhanced Filter Bar -->
    <form action="{{ route('admin.announcements.index') }}" method="GET" class="filter-bar-simple">
        <div class="filter-group">
            <label class="filter-label">
                <h2>Filter Status</h2>
            </label>
            <select name="status" class="filter-input" onchange="this.form.submit()">
                <option value="">All Announcements</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived Only</option>
            </select>
        </div>
        
        @if(request('status'))
        <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
            Clear Filter
        </a>
        @endif
    </form>
    
    @if($announcements->count() > 0)
    <div class="table-responsive">
        <table class="data-table enhanced-table">
            <thead>
                <tr>
                    <th>Announcement</th>
                    <th>Category</th>
                    <th>Target Audience</th>
                    <th>Priority</th>
                    <th>Posted Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($announcements as $announcement)
                <tr class="table-row-hover">
                    <td>
                        <div class="announcement-cell">                    
                            <div>
                                <div class="announcement-title">{{ $announcement->title }}</div>
                                <div class="announcement-preview">{{ Str::limit($announcement->content, 60) }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($announcement->category === 'Important')
                            <span class="badge badge-danger category-badge">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                </svg>
                                {{ $announcement->category }}
                            </span>
                        @elseif($announcement->category === 'Event')
                            <span class="badge badge-warning category-badge">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                {{ $announcement->category }}
                            </span>
                        @else
                            <span class="badge badge-info category-badge">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                                {{ $announcement->category }}
                            </span>
                        @endif
                    </td>
                    <td><span class="audience-badge">{{ $announcement->target_audience }}</span></td>
                    <td>
                        @if($announcement->priority === 'Urgent')
                            <span class="badge badge-danger priority-badge">{{ $announcement->priority }}</span>
                        @elseif($announcement->priority === 'High')
                            <span class="badge badge-warning priority-badge">{{ $announcement->priority }}</span>
                        @else
                            <span class="badge badge-secondary priority-badge">{{ $announcement->priority }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="date-info">
                            <div class="date-main">{{ \Carbon\Carbon::parse($announcement->created_at)->format('M d, Y') }}</div>
                            <div class="date-relative">{{ \Carbon\Carbon::parse($announcement->created_at)->diffForHumans() }}</div>
                        </div>
                    </td>
                    <td>
                        @if($announcement->archived_at)
                            <span class="badge badge-secondary status-badge">Archived</span>
                        @else
                            <span class="badge badge-success status-badge">Active</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-edit btn-sm" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </a>
                            
                            @if(!$announcement->archived_at)
                            <form action="{{ route('admin.announcements.archive', $announcement) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-sm" title="Archive">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="21 8 21 21 3 21 3 8"></polyline>
                                        <rect x="1" y="3" width="22" height="5"></rect>
                                        <line x1="10" y1="12" x2="14" y2="12"></line>
                                    </svg>
                                </button>
                            </form>
                            @endif
                            
                            <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this announcement permanently?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-delete btn-sm" title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
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
        {{ $announcements->links() }}
    </div>
    @else
    <div class="empty-state-enhanced">
        <div class="empty-icon">📢</div>
        <h3>No Announcements Found</h3>
        <p>{{ request('status') ? 'No announcements match your filter' : 'Create your first announcement to get started' }}</p>
        @if(!request('status'))
        <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary" style="margin-top: 20px;">
            Post Announcement
        </a>
        @endif
    </div>
    @endif
</div>

<!-- Announcement Preview Cards -->
@if($announcements->count() > 0)
<div class="content-panel panel-enhanced">
    <div class="panel-header">
        <h2 class="panel-title">Recent Announcements Preview</h2>
    </div>
    
    <div class="announcements-grid">
        @foreach($announcements->take(3) as $announcement)
        <div class="announcement-card {{ $announcement->priority === 'Urgent' ? 'urgent' : '' }}">
            <div class="card-header">
                <div class="card-meta">
                    <h3 class="card-title">{{ $announcement->title }}</h3>
                    <span class="card-time">{{ \Carbon\Carbon::parse($announcement->created_at)->diffForHumans() }}</span>
                </div>
            </div>
            <p class="card-content">{{ Str::limit($announcement->content, 200) }}</p>
            <div class="card-footer">
                <span class="badge badge-{{ $announcement->category === 'Important' ? 'danger' : 'info' }}">{{ $announcement->category }}</span>
                <span class="badge badge-secondary">{{ $announcement->target_audience }}</span>
                @if($announcement->priority === 'Urgent')
                <span class="badge badge-danger">🚨 Urgent</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<style>
/* Simple Filter Bar */
.panel-title {
    margin: 15px 0;
}

.filter-bar-simple {
    display: flex;
    align-items: flex-end;
    gap: 12px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}
.filter-input {
    padding: 8px 16px;                /* inner spacing */
    border: 2px solid #dee2e6;         /* soft border */
    border-radius: 8px;                 /* rounded corners */
    font-size: 14px;                    /* readable text */
    background: #ffffff;                /* white background */
    cursor: pointer;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    appearance: none;                   /* removes default arrow style for consistent look */
}

.filter-input:hover {
    border-color: #2d5f8d;             /* hover border color */
    box-shadow: 0 4px 8px rgba(0,0,0,0.05); /* subtle shadow */
}

/* Optional: custom arrow to match modern style */
.filter-input::-ms-expand {
    display: none;                       /* hide arrow in IE/Edge */
}

/* Announcement Cell */
.announcement-cell {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.announcement-icon {
    font-size: 32px;
    flex-shrink: 0;
}

.announcement-title {
    font-weight: 600;
    font-size: 15px;
    color: #212529;
    margin-bottom: 4px;
}

.announcement-preview {
    font-size: 13px;
    color: #6c757d;
    line-height: 1.4;
}

/* Enhanced Badges */
.category-badge, .priority-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 600;
}

.audience-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    background: #e3f2fd;
    color: #1e3a5f;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 500;
}

/* Date Info */
.date-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.date-main {
    font-weight: 600;
    font-size: 14px;
    color: #212529;
}

.date-relative {
    font-size: 12px;
    color: #6c757d;
}

/* Announcements Grid */
.announcements-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 20px;
}

.announcement-card {
    padding: 20px;
    background: linear-gradient(135deg, #02509536 0%, #f8f9fa 100%);
    border-radius: 12px;
    border-left: 4px solid #2d5f8d;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.announcement-card.urgent {
    border-left-color: #dc3545;
    background: linear-gradient(135deg, #fff5f5 0%, #ffe5e5 100%);
}

.announcement-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    background: #02509536;
    
}

.card-header {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 16px;
}

.card-icon {
    font-size: 36px;
    flex-shrink: 0;
}

.card-meta {
    flex: 1;
}

.card-title {
    font-size: 17px;
    font-weight: 600;
    margin: 0 0 6px 0;
    color: #212529;
}

.card-time {
    font-size: 12px;
    color: #6c757d;
}

.card-content {
    margin: 0 0 16px 0;
    color: #002d5aff;
    line-height: 1.6;
    font-size: 14px;
}

.card-footer {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

/* Responsive */
@media (max-width: 768px) {
    .announcements-grid {
        grid-template-columns: 1fr;
    }
    
    .announcement-cell {
        flex-direction: column;
    }
}
</style>
@endsection
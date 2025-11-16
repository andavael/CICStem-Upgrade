@extends('student.layouts.app')

@section('title', 'Announcements')
@section('page-title', 'Announcements')

@section('content')
<div class="page-header">
    <h1 class="page-title">Announcements</h1>
</div>

<div class="content-panel">
    <!-- Filter Bar -->
    <form action="{{ route('student.announcements.index') }}" method="GET" class="filter-bar">
        <select name="category" class="form-control" onchange="this.form.submit()">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                    {{ $category }}
                </option>
            @endforeach
        </select>
        
        <select name="priority" class="form-control" onchange="this.form.submit()">
            <option value="">All Priorities</option>
            @foreach($priorities as $priority)
                <option value="{{ $priority }}" {{ request('priority') === $priority ? 'selected' : '' }}>
                    {{ $priority }}
                </option>
            @endforeach
        </select>
        
        <input type="text" name="search" class="form-control" placeholder="Search announcements..." value="{{ request('search') }}" style="flex: 1; min-width: 250px;">
        
        <button type="submit" class="btn btn-primary">Search</button>
        
        @if(request()->hasAny(['category', 'priority', 'search']))
        <a href="{{ route('student.announcements.index') }}" class="btn btn-secondary">Clear Filters</a>
        @endif
    </form>

    @if($announcements->count() > 0)
        <div style="display: grid; gap: 16px;">
            @foreach($announcements as $announcement)
            <div class="announcement-card {{ strtolower($announcement->priority) }}">
                <div class="announcement-header">
                    <div style="flex: 1;">
                        <div class="announcement-title">{{ $announcement->title }}</div>
                        <div class="announcement-meta">
                            <span class="badge badge-{{ $announcement->priority === 'Urgent' ? 'danger' : ($announcement->priority === 'High' ? 'warning' : 'info') }}">
                                {{ $announcement->priority }} Priority
                            </span>
                            <span class="badge badge-secondary">{{ $announcement->category }}</span>
                            <span style="display: inline-flex; align-items: center; gap: 4px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                {{ $announcement->target_audience }}
                            </span>
                            <span style="display: inline-flex; align-items: center; gap: 4px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                {{ $announcement->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="announcement-content">
                    {{ Str::limit($announcement->content, 200) }}
                </div>
                
                <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                    <div style="font-size: 12px; color: #6c757d;">
                        Posted on {{ $announcement->created_at->format('F d, Y \a\t h:i A') }}
                    </div>
                    <a href="{{ route('student.announcements.show', $announcement->id) }}" class="btn btn-view btn-sm">
                        Read More
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="pagination">
            {{ $announcements->links() }}
        </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon">📢</div>
        <h3>No Announcements Found</h3>
        <p>{{ request()->hasAny(['category', 'priority', 'search']) ? 'Try adjusting your filters' : 'There are no announcements at the moment' }}</p>
        @if(request()->hasAny(['category', 'priority', 'search']))
        <a href="{{ route('student.announcements.index') }}" class="btn btn-secondary" style="margin-top: 16px;">
            Clear Filters
        </a>
        @endif
    </div>
    @endif
</div>

<style>
.announcement-card {
    background: white;
    border: 2px solid #dee2e6;
    border-radius: 12px;
    padding: 24px;
    transition: all 0.3s ease;
}

.announcement-card:hover {
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.announcement-card.urgent {
    border-left: 5px solid #dc3545;
    background: linear-gradient(135deg, #fff5f5 0%, white 100%);
}

.announcement-card.high {
    border-left: 5px solid #ffc107;
    background: linear-gradient(135deg, #fffbf0 0%, white 100%);
}

.announcement-card.normal {
    border-left: 5px solid #17a2b8;
}

.announcement-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
}

.announcement-title {
    font-size: 20px;
    font-weight: 600;
    color: #212529;
    margin-bottom: 10px;
}

.announcement-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    font-size: 13px;
    color: #6c757d;
}

.announcement-content {
    font-size: 15px;
    line-height: 1.6;
    color: #495057;
}
</style>
@endsection
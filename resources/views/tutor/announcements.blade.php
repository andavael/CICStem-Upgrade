@extends('tutor.layouts.app')

@section('title', 'Announcements')
@section('page-title', 'Tutor Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">📢 Announcements</h1>
</div>

<div class="content-panel">
    @if($announcements->count() > 0)
        @foreach($announcements as $announcement)
        <div class="announcement-card {{ strtolower($announcement->priority) }}">
            <div class="announcement-header">
                <div style="flex: 1;">
                    <div class="announcement-title">{{ $announcement->title }}</div>
                    <div class="announcement-meta">
                        <span class="badge badge-{{ $announcement->priority === 'Urgent' ? 'danger' : ($announcement->priority === 'High' ? 'warning' : 'info') }}">
                            {{ $announcement->priority }}
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
                {{ $announcement->content }}
            </div>
            <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #dee2e6; font-size: 12px; color: #6c757d;">
                Posted on {{ $announcement->created_at->format('F d, Y \a\t h:i A') }}
            </div>
        </div>
        @endforeach

        <div class="pagination">
            {{ $announcements->links() }}
        </div>
    @else
    <div class="empty-state">
        <div class="empty-icon">📢</div>
        <h3>No Announcements</h3>
        <p>There are no announcements at the moment. Check back later!</p>
    </div>
    @endif
</div>
@endsection
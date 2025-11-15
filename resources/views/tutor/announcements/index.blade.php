@extends('tutor.layouts.app')

@section('title', 'Announcements')
@section('page-title', 'Announcements')

@section('content')
<div class="page-header">
    <h1 class="page-title">📢 Announcements</h1>
    <div class="page-actions">
        <a href="{{ route('tutor.announcements.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 6px;">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Create Announcement
        </a>
    </div>
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
                
                @if(isset($announcement->created_by_tutor_id) && $announcement->created_by_tutor_id === $tutor->id)
                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('tutor.announcements.edit', $announcement->id) }}" class="btn btn-sm btn-edit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        Edit
                    </a>
                </div>
                @endif
            </div>
            <div class="announcement-content">
                {{ $announcement->content }}
            </div>
            <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #dee2e6; font-size: 12px; color: #6c757d;">
                Posted on {{ $announcement->created_at->format('F d, Y \a\t h:i A') }}
                @if(isset($announcement->created_by_tutor_id) && $announcement->created_by_tutor_id)
                    <span style="margin-left: 8px;">• 
                        @if($announcement->created_by_tutor_id === $tutor->id)
                            <strong style="color: #2d5f8d;">Created by You</strong>
                        @else
                            Created by Tutor
                        @endif
                    </span>
                @else
                    <span style="margin-left: 8px;">• <strong style="color: #856404;">Created by Admin</strong></span>
                @endif
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
        <p>There are no announcements at the moment. Be the first to create one!</p>
        <a href="{{ route('tutor.announcements.create') }}" class="btn btn-primary" style="margin-top: 20px;">Create Announcement</a>
    </div>
    @endif
</div>
@endsection
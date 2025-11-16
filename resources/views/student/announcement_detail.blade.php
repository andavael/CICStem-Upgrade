@extends('student.layouts.app')

@section('title', 'Announcement')
@section('page-title', 'Announcement')

@section('content')
<div class="page-header">
    <h1 class="page-title">📢 Announcement Details</h1>
    <div class="page-actions">
        <a href="{{ route('student.announcements.index') }}" class="btn btn-secondary">Back to Announcements</a>
    </div>
</div>

<div class="content-panel">
    <div style="margin-bottom: 20px;">
        <h1 style="font-size: 28px; font-weight: 700; color: #212529; margin-bottom: 16px;">
            {{ $announcement->title }}
        </h1>
        
        <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 20px;">
            <span class="badge badge-{{ $announcement->priority === 'Urgent' ? 'danger' : ($announcement->priority === 'High' ? 'warning' : 'info') }}">
                {{ $announcement->priority }} Priority
            </span>
            <span class="badge badge-secondary">{{ $announcement->category }}</span>
            <span class="badge badge-info">{{ $announcement->target_audience }}</span>
        </div>
        
        <div style="display: flex; gap: 20px; color: #6c757d; font-size: 14px; margin-bottom: 30px;">
            <div style="display: flex; align-items: center; gap: 6px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                Posted {{ $announcement->created_at->format('F d, Y') }}
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                {{ $announcement->created_at->diffForHumans() }}
            </div>
        </div>
    </div>
    
    <div style="background: #f8f9fa; padding: 30px; border-radius: 12px; border-left: 4px solid #2d5f8d;">
        <div style="font-size: 15px; line-height: 1.8; color: #212529; white-space: pre-wrap;">{{ $announcement->content }}</div>
    </div>
    
    @if($announcement->createdByTutor)
    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6;">
        <div style="display: flex; align-items: center; gap: 12px; color: #6c757d; font-size: 14px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <span>Posted by: <strong>{{ $announcement->createdByTutor->full_name }}</strong></span>
        </div>
    </div>
    @endif
</div>
@endsection
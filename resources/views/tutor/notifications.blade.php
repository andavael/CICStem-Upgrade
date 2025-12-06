@extends('tutor.layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="page-header">
    <h1 class="page-title">Notifications</h1>
    <div class="page-actions">
        @if($unreadCount > 0)
        <form action="{{ route('tutor.notifications.markAllRead') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-primary">
                Mark All as Read ({{ $unreadCount }})
            </button>
        </form>
        @endif
    </div>
</div>

<div class="content-panel">
    @if($notifications->count() > 0)
    <div style="display: flex; flex-direction: column; gap: 12px;">
        @foreach($notifications as $notification)
        <div class="notification-item {{ $notification->is_read ? 'read' : 'unread' }}" data-notification-type="{{ $notification->type }}">
            <div class="notification-header">
                <div class="notification-icon 
                    @if($notification->type === 'student_enrollment') icon-enrollment
                    @elseif($notification->type === 'session_update') icon-update
                    @elseif($notification->type === 'session_cancellation') icon-cancelled
                    @elseif($notification->type === 'session_reschedule') icon-rescheduled
                    @elseif($notification->type === 'urgent_announcement') icon-urgent
                    @else icon-default
                    @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        @if($notification->type === 'student_enrollment')
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        @elseif($notification->type === 'session_update')
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        @elseif($notification->type === 'session_cancellation')
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        @elseif($notification->type === 'session_reschedule')
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        @elseif($notification->type === 'urgent_announcement')
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        @else
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        @endif
                    </svg>
                </div>
                <div class="notification-content">
                    <div class="notification-title">{{ $notification->title }}</div>
                    <div class="notification-message">{{ $notification->message }}</div>
                    <div class="notification-meta">
                        <span class="notification-time">{{ $notification->time_ago }}</span>
                        <span class="notification-type-badge">{{ ucfirst(str_replace('_', ' ', $notification->type)) }}</span>
                    </div>
                </div>
                <div class="notification-actions">
                    @if(!$notification->is_read)
                    <form action="{{ route('tutor.notifications.markRead', $notification->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-view" title="Mark as read">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </button>
                    </form>
                    @endif
                    @if($notification->related_id && in_array($notification->type, ['student_enrollment', 'session_update', 'session_reschedule', 'session_cancellation']))
                    <a href="{{ route('tutor.sessions.show', $notification->related_id) }}" class="btn btn-sm btn-info" title="View session">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </a>
                    @endif
                    <form action="{{ route('tutor.notifications.delete', $notification->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-delete" onclick="return confirm('Delete this notification?')" title="Delete">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="pagination">
        {{ $notifications->links() }}
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
        </div>
        <h3>No Notifications</h3>
        <p>You don't have any notifications at the moment.</p>
    </div>
    @endif
</div>

<style>
.page-actions {
    display: flex;
    gap: 12px;
}

.content-panel {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background-color: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 12px;
    max-width: 500px;
    margin: 0 auto;
}

.empty-state-icon {
    margin-bottom: 16px;
    color: #6c757d;
}

.empty-state h3 {
    font-size: 24px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
}

.empty-state p {
    font-size: 16px;
    color: #6c757d;
    margin: 0;
}

.notification-item {
    background: white;
    border: 2px solid #dee2e6;
    border-radius: 12px;
    padding: 20px;
    transition: all 0.3s ease;
}

.notification-item.unread {
    background: linear-gradient(135deg, #f0f7ff 0%, #e3f2fd 100%);
    border-color: #2d5f8d;
    box-shadow: 0 2px 8px rgba(45, 95, 141, 0.15);
}

.notification-item:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.notification-header {
    display: flex;
    gap: 16px;
    align-items: flex-start;
}

.notification-icon {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #e9ecef;
}

.notification-icon svg {
    width: 24px;
    height: 24px;
}

.icon-enrollment {
    background: #d4edda;
    color: #28a745;
}

.icon-enrollment svg {
    stroke: #28a745;
}

.icon-update {
    background: #d1ecf1;
    color: #0c5460;
}

.icon-update svg {
    stroke: #0c5460;
}

.icon-cancelled {
    background: #f8d7da;
    color: #dc3545;
}

.icon-cancelled svg {
    stroke: #dc3545;
}

.icon-rescheduled {
    background: #fff3cd;
    color: #856404;
}

.icon-rescheduled svg {
    stroke: #856404;
}

.icon-urgent {
    background: #fff3cd;
    color: #dc3545;
}

.icon-urgent svg {
    stroke: #dc3545;
}

.icon-default {
    background: #e9ecef;
    color: #495057;
}

.icon-default svg {
    stroke: #495057;
}

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-title {
    font-size: 17px;
    font-weight: 600;
    color: #212529;
    margin-bottom: 8px;
    line-height: 1.4;
}

.notification-item.unread .notification-title {
    color: #013365;
}

.notification-message {
    font-size: 15px;
    color: #495057;
    line-height: 1.6;
    margin-bottom: 10px;
    word-wrap: break-word;
}

.notification-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.notification-time {
    font-size: 13px;
    color: #6c757d;
    font-style: italic;
}

.notification-type-badge {
    display: inline-block;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    background: #e9ecef;
    color: #495057;
    border-radius: 12px;
    letter-spacing: 0.5px;
}

.notification-actions {
    display: flex;
    gap: 8px;
    flex-shrink: 0;
    align-items: flex-start;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #013365;
    color: white;
}

.btn-primary:hover {
    background: #012a52;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(1, 51, 101, 0.3);
}

.btn-sm {
    padding: 8px 12px;
    font-size: 13px;
}

.btn-view {
    background: #28a745;
    color: white;
}

.btn-view:hover {
    background: #218838;
}

.btn-info {
    background: #17a2b8;
    color: white;
}

.btn-info:hover {
    background: #138496;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn-delete:hover {
    background: #c82333;
}

.pagination {
    margin-top: 24px;
    display: flex;
    justify-content: center;
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .notification-header {
        flex-direction: column;
        gap: 12px;
    }
    
    .notification-icon {
        width: 36px;
        height: 36px;
    }

    .notification-icon svg {
        width: 20px;
        height: 20px;
    }

    .notification-actions {
        width: 100%;
        justify-content: flex-end;
    }

    .content-panel {
        padding: 16px;
    }
}
</style>
@endsection
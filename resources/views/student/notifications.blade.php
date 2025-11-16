@extends('student.layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="page-header">
    <h1 class="page-title">Notifications</h1>
    <div class="page-actions">
        @if($unreadCount > 0)
        <form action="{{ route('student.notifications.markAllRead') }}" method="POST" style="display: inline;">
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
        <div class="notification-item {{ $notification->is_read ? 'read' : 'unread' }}">
            <div class="notification-header">
                <div class="notification-icon">
                    @if($notification->type === 'session_reminder')
                        📅
                    @elseif($notification->type === 'session_cancelled')
                        ❌
                    @elseif($notification->type === 'session_modified')
                        ✏️
                    @elseif($notification->type === 'announcement')
                        📢
                    @else
                        🔔
                    @endif
                </div>
                <div class="notification-content">
                    <div class="notification-title">{{ $notification->title }}</div>
                    <div class="notification-message">{{ $notification->message }}</div>
                    <div class="notification-time">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</div>
                </div>
                <div class="notification-actions">
                    @if(!$notification->is_read)
                    <form action="{{ route('student.notifications.markRead', $notification->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-view" title="Mark as read">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </button>
                    </form>
                    @endif
                    <form action="{{ route('student.notifications.delete', $notification->id) }}" method="POST" style="display: inline;">
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
        <div class="empty-state-icon">🔔</div>
        <h3>No Notifications</h3>
        <p>You don't have any notifications yet.</p>
    </div>
    @endif
</div>

<style>
.empty-state {
    text-align: center;
    padding: 40px;
    background-color: #f8f9fa;       /* light gray background */
    border: 1px solid #01336584;       /* subtle border */
    border-radius: 8px;              /* rounded corners */
    box-shadow: 0 8px 8px rgba(0, 0, 0, 0.1); /* soft shadow */
    max-width: 400px;
    margin: 20px auto;               /* center horizontally */
}

.notification-item {
    background: white;
    border: 2px solid #dee2e6;
    border-radius: 12px;
    padding: 16px;
    transition: all 0.3s ease;
}

.notification-item.unread {
    background: #f0f7ff;
    border-color: #2d5f8d;
}

.notification-item:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.notification-header {
    display: flex;
    gap: 16px;
    align-items: flex-start;
}

.notification-icon {
    font-size: 28px;
    flex-shrink: 0;
}

.notification-content {
    flex: 1;
}

.notification-title {
    font-size: 16px;
    font-weight: 600;
    color: #212529;
    margin-bottom: 6px;
}

.notification-message {
    font-size: 14px;
    color: #495057;
    line-height: 1.5;
    margin-bottom: 8px;
}

.notification-time {
    font-size: 12px;
    color: #6c757d;
}

.notification-actions {
    display: flex;
    gap: 8px;
    flex-shrink: 0;
}

.btn-delete {
    background: #dc3545;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-delete:hover {
    background: #c82333;
}

@media (max-width: 768px) {
    .notification-header {
        flex-direction: column;
    }
    
    .notification-actions {
        width: 100%;
        justify-content: flex-end;
    }
}
</style>
@endsection
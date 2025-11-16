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
            <button type="submit" class="btn btn-primary">Mark All as Read</button>
        </form>
        @endif
    </div>
</div>

<div class="content-panel">
    @if($unreadCount > 0)
    <div style="background: #e3f2fd; border-left: 4px solid #2d5f8d; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
        <strong>You have {{ $unreadCount }} unread notification{{ $unreadCount > 1 ? 's' : '' }}</strong>
    </div>
    @endif

    @if($notifications->count() > 0)
        @foreach($notifications as $notification)
        <div class="notification-card {{ $notification->is_read ? 'read' : 'unread' }}">
            <div class="notification-header">
                <div style="flex: 1;">
                    <div class="notification-icon">
                        @if($notification->type === 'student_enrollment')
                            👤
                        @elseif($notification->type === 'session_update')
                            📝
                        @elseif($notification->type === 'session_cancellation')
                            ❌
                        @elseif($notification->type === 'session_reschedule')
                            📅
                        @else
                            🔔
                        @endif
                    </div>
                    <div>
                        <div class="notification-title">{{ $notification->title }}</div>
                        <div class="notification-time">{{ $notification->time_ago }}</div>
                    </div>
                </div>
                <div class="notification-actions">
                    @if(!$notification->is_read)
                    <form action="{{ route('tutor.notifications.markRead', $notification->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary">Mark as Read</button>
                    </form>
                    @endif
                    <form action="{{ route('tutor.notifications.delete', $notification->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-delete" onclick="return confirm('Delete this notification?')">Delete</button>
                    </form>
                </div>
            </div>
            <div class="notification-message">
                {{ $notification->message }}
            </div>
        </div>
        @endforeach

        <div class="pagination">
            {{ $notifications->links() }}
        </div>
    @else
    <div class="empty-state">
        <div class="empty-icon">🔔</div>
        <h3>No Notifications</h3>
        <p>You don't have any notifications at the moment.</p>
    </div>
    @endif
</div>

<style>
.notification-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
    border-left: 4px solid #dee2e6;
    transition: all 0.3s ease;
}

.notification-card.unread {
    background: #f0f7ff;
    border-left-color: #2d5f8d;
}

.notification-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
    gap: 16px;
}

.notification-header > div:first-child {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.notification-icon {
    font-size: 24px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 50%;
    flex-shrink: 0;
}

.notification-card.unread .notification-icon {
    background: #e3f2fd;
}

.notification-title {
    font-size: 16px;
    font-weight: 600;
    color: #212529;
    margin-bottom: 4px;
}

.notification-time {
    font-size: 13px;
    color: #6c757d;
}

.notification-message {
    font-size: 14px;
    color: #495057;
    line-height: 1.6;
    padding-left: 52px;
}

.notification-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .notification-header {
        flex-direction: column;
    }
    
    .notification-actions {
        width: 100%;
    }
    
    .notification-actions button {
        flex: 1;
    }
    
    .notification-message {
        padding-left: 0;
        margin-top: 12px;
    }
}
</style>
@endsection
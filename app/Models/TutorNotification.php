<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorNotification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'tutor_id',
        'type',
        'title',
        'message',
        'related_id',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the tutor that owns the notification
     */
    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }

    /**
     * Get related session if applicable
     */
    public function session()
    {
        return $this->belongsTo(Session::class, 'related_id');
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    /**
     * Scope: Unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Get time ago format
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Static method to notify specific tutors
     * 
     * @param array|int $tutorIds Single tutor ID or array of tutor IDs
     * @param string $type Notification type
     * @param string $title Notification title
     * @param string $message Notification message
     * @param int|null $relatedId Related entity ID (session, announcement, etc.)
     * @return int Number of notifications created
     */
    public static function notifyTutors($tutorIds, $type, $title, $message, $relatedId = null)
    {
        // Ensure $tutorIds is an array
        if (!is_array($tutorIds)) {
            $tutorIds = [$tutorIds];
        }

        // Remove duplicates
        $tutorIds = array_unique($tutorIds);

        $notifications = [];
        $timestamp = now();

        foreach ($tutorIds as $tutorId) {
            $notifications[] = [
                'tutor_id' => $tutorId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'related_id' => $relatedId,
                'is_read' => false,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        if (!empty($notifications)) {
            self::insert($notifications);
            return count($notifications);
        }

        return 0;
    }

    /**
     * Notify tutor about new student enrollment
     */
    public static function notifyNewEnrollment($tutorId, $session, $student)
    {
        $sessionDate = $session->session_date->format('M d, Y');
        $sessionTime = substr($session->session_time, 0, 5);

        return self::notifyTutors(
            $tutorId,
            'student_enrollment',
            'New Student Enrollment',
            "Student {$student->first_name} {$student->last_name} ({$student->sr_code}) has enrolled in your session '{$session->subject} ({$session->session_code})' scheduled for {$sessionDate} at {$sessionTime}.",
            $session->id
        );
    }

    /**
     * Notify tutor about urgent announcement
     */
    public static function notifyUrgentAnnouncement($tutorId, $announcement)
    {
        $message = strlen($announcement->content) > 200 
            ? substr($announcement->content, 0, 200) . '...' 
            : $announcement->content;

        return self::notifyTutors(
            $tutorId,
            'urgent_announcement',
            'URGENT: ' . $announcement->title,
            $message,
            $announcement->id
        );
    }

    /**
     * Notify tutor about session modification
     */
    public static function notifySessionModified($tutorId, $session, $changes = null)
    {
        $sessionDate = $session->session_date->format('M d, Y');
        $sessionTime = substr($session->session_time, 0, 5);

        $message = "Your session '{$session->subject} ({$session->session_code})' scheduled for {$sessionDate} at {$sessionTime} has been modified by an administrator.";
        
        if ($changes) {
            $message .= " Changes: {$changes}";
        }

        return self::notifyTutors(
            $tutorId,
            'session_update',
            'Session Modified',
            $message,
            $session->id
        );
    }

    /**
     * Notify tutor about session cancellation
     */
    public static function notifySessionCancelled($tutorId, $session)
    {
        $sessionDate = $session->session_date->format('M d, Y');
        $sessionTime = substr($session->session_time, 0, 5);

        return self::notifyTutors(
            $tutorId,
            'session_cancellation',
            'Session Cancelled',
            "Your session '{$session->subject} ({$session->session_code})' scheduled for {$sessionDate} at {$sessionTime} has been cancelled by an administrator. All enrolled students have been notified.",
            $session->id
        );
    }

    /**
     * Notify tutor about session reschedule
     */
    public static function notifySessionRescheduled($tutorId, $session, $oldDate, $oldTime)
    {
        $newDate = $session->session_date->format('M d, Y');
        $newTime = substr($session->session_time, 0, 5);
        $oldDateFormatted = \Carbon\Carbon::parse($oldDate)->format('M d, Y');
        $oldTimeFormatted = substr($oldTime, 0, 5);

        return self::notifyTutors(
            $tutorId,
            'session_reschedule',
            'Session Rescheduled',
            "Your session '{$session->subject} ({$session->session_code})' has been rescheduled from {$oldDateFormatted} at {$oldTimeFormatted} to {$newDate} at {$newTime}. All enrolled students have been notified.",
            $session->id
        );
    }
}
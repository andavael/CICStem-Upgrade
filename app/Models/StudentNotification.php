<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentNotification extends Model
{
    use HasFactory;

    protected $table = 'student_notifications';

    protected $fillable = [
        'student_id',
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
     * Get the student that owns the notification
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
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
     * Create notification for multiple students
     */
    public static function notifyStudents($studentIds, $type, $title, $message, $relatedId = null)
    {
        $notifications = [];
        foreach ($studentIds as $studentId) {
            $notifications[] = [
                'student_id' => $studentId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'related_id' => $relatedId,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($notifications)) {
            self::insert($notifications);
        }
    }

    /**
     * Create notification for a single student
     */
    public static function notifyStudent($studentId, $type, $title, $message, $relatedId = null)
    {
        return self::create([
            'student_id' => $studentId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'related_id' => $relatedId,
        ]);
    }
}
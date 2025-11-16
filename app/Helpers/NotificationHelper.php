<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class NotificationHelper
{
    /**
     * Create notification for student
     */
    public static function createStudentNotification($studentId, $type, $title, $message, $relatedId = null)
    {
        DB::table('student_notifications')->insert([
            'student_id' => $studentId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'related_id' => $relatedId,
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    
    /**
     * Notify students about session update
     */
    public static function notifySessionUpdate($sessionId, $title, $message)
    {
        $studentIds = DB::table('session_enrollments')
            ->where('session_id', $sessionId)
            ->pluck('student_id');
            
        foreach ($studentIds as $studentId) {
            self::createStudentNotification($studentId, 'session_modified', $title, $message, $sessionId);
        }
    }
    
    /**
     * Notify students about session cancellation
     */
    public static function notifySessionCancellation($sessionId, $subject)
    {
        $studentIds = DB::table('session_enrollments')
            ->where('session_id', $sessionId)
            ->pluck('student_id');
            
        foreach ($studentIds as $studentId) {
            self::createStudentNotification(
                $studentId,
                'session_cancelled',
                'Session Cancelled',
                "The session '{$subject}' has been cancelled by the administrator.",
                $sessionId
            );
        }
    }
    
    /**
     * Send session reminder (call this via scheduled task)
     */
    public static function sendSessionReminders()
    {
        $tomorrow = now()->addDay()->toDateString();
        
        $sessions = DB::table('tutor_sessions')
            ->where('session_date', $tomorrow)
            ->where('status', 'Scheduled')
            ->get();
            
        foreach ($sessions as $session) {
            $enrolledStudents = DB::table('session_enrollments')
                ->where('session_id', $session->id)
                ->pluck('student_id');
                
            foreach ($enrolledStudents as $studentId) {
                self::createStudentNotification(
                    $studentId,
                    'session_reminder',
                    'Session Reminder',
                    "Reminder: You have a session '{$session->subject}' tomorrow at {$session->session_time}.",
                    $session->id
                );
            }
        }
    }
}
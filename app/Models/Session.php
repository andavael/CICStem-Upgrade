<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $table = 'tutor_sessions';


    protected $fillable = [
        'session_code',
        'subject',
        'session_date',
        'session_time',
        'tutor_id',
        'capacity',
        'year_level',
        'google_meet_link',
        'description',
        'status',
    ];

    protected $casts = [
        'session_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the tutor for this session
     */
    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }

    /**
     * Get enrolled students for this session
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'session_enrollments', 'session_id', 'student_id')
                    ->withPivot('enrolled_at', 'attendance_status')
                    ->withTimestamps();
    }

    /**
     * Check if session is full
     */
    public function isFull()
    {
        return $this->students()->count() >= $this->capacity;
    }

    /**
     * Get available slots
     */
    public function getAvailableSlotsAttribute()
    {
        return $this->capacity - $this->students()->count();
    }

    /**
     * Check if session is upcoming
     */
    public function isUpcoming()
    {
        return $this->session_date >= now()->toDateString();
    }
}
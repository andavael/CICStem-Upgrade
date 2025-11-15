<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Tutor extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'tutors';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'sr_code',
        'email',
        'password',
        'year_level',
        'course_program',
        'tutor_level_preference',
        'gwa',
        'resume_path',
        'status',
        'is_approved',
        'terms_accepted',
        'terms_accepted_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'gwa' => 'decimal:2',
        'is_approved' => 'boolean',
        'terms_accepted' => 'boolean',
        'terms_accepted_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the guard for this model
     */
    protected $guard = 'tutor';

    /**
     * Get full name
     */
    public function getFullNameAttribute()
    {
        $name = $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        $name .= ' ' . $this->last_name;
        return $name;
    }

    /**
     * Check if tutor is active
     */
    public function isActive()
    {
        return $this->status === 'Active' && $this->is_approved;
    }

    /**
     * Subjects relationship
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'tutor_subjects', 'tutor_id', 'subject_id')
                    ->withPivot('proficiency_level')
                    ->withTimestamps();
    }

    /**
     * Sessions relationship
     */
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
}
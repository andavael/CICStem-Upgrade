<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'students';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'sr_code',
        'email',
        'password',
        'year_level',
        'course_program',
        'status',
        'terms_accepted',
        'terms_accepted_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'terms_accepted' => 'boolean',
        'terms_accepted_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the guard for this model
     */
    protected $guard = 'student';

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
     * Check if student is active
     */
    public function isActive()
    {
        return $this->status === 'Active';
    }
}
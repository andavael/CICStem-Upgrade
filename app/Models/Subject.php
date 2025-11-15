<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $table = 'subjects';

    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    /**
     * Get tutors teaching this subject
     */
    public function tutors()
    {
        return $this->belongsToMany(Tutor::class, 'tutor_subjects', 'subject_id', 'tutor_id')
                    ->withPivot('proficiency_level')
                    ->withTimestamps();
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $table = 'announcements';

    protected $fillable = [
        'title',
        'content',
        'category',
        'target_audience',
        'priority',
        'archived_at',
        'created_by_tutor_id', // Track which tutor created it
    ];

    protected $casts = [
        'archived_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the tutor who created this announcement
     */
    public function createdByTutor()
    {
        return $this->belongsTo(Tutor::class, 'created_by_tutor_id');
    }

    /**
     * Check if announcement is archived
     */
    public function isArchived()
    {
        return !is_null($this->archived_at);
    }

    /**
     * Scope: Active announcements
     */
    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    /**
     * Scope: Archived announcements
     */
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }
}